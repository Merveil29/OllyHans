<?php
/**
 * ════════════════════════════════════════════════════════════════════════════
 * ÉTAPE 2 — MIGRATION DES MÉDIAS LOCAUX VERS CLOUDINARY
 *
 * Ce script :
 *   1. Lit les chemins d'images dans le nouveau DB (déjà peuplé par step1)
 *   2. Pour chaque image : normalise le chemin, vérifie que le fichier existe
 *   3. Upload sur Cloudinary
 *   4. Met à jour la colonne DB avec l'URL Cloudinary https://...
 *   5. Enregistre tout dans migration_cloudinary.log
 *
 * PRÉREQUIS :
 *   - Étape 1 (step1_import_db.sh) terminée avec succès
 *   - Ancien projet accessible localement dans OLD_PROJECT_PATH
 *
 * UTILISATION :
 *   cd /chemin/vers/le/projet/migration
 *   php step2_cloudinary_migration.php
 *
 * Le script est IDEMPOTENT : il ignore les URLs déjà en format Cloudinary.
 * ════════════════════════════════════════════════════════════════════════════
 */

// ─── CONFIGURATION ──────────────────────────────────────────────────────────
define('OLD_PROJECT_PATH', '/chemin/vers/ancien-projet');
define('BACKEND_VENDOR',   '/chemin/vers/backend/vendor/autoload.php');

define('CLOUDINARY_CLOUD',  'votre-cloud');
define('CLOUDINARY_KEY',    'votre-key');
define('CLOUDINARY_SECRET', 'votre-secret');
define('CLOUDINARY_FOLDER', 'topideal');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'votre-db');
define('DB_USER', 'root');
define('DB_PASS', 'votre-password');

define('LOG_FILE',     __DIR__ . '/migration_cloudinary.log');
define('BACKUP_SQL',   __DIR__ . '/backup_before_cloudinary.sql');
define('DRY_RUN',      false);  // ← true = simulation sans modifier la DB
// ────────────────────────────────────────────────────────────────────────────

require BACKEND_VENDOR;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

// ═══════════════════════════════════════════════════════════════════════════
// Helpers
// ═══════════════════════════════════════════════════════════════════════════

$logHandle = fopen(LOG_FILE, 'a');

function log_msg(string $msg, string $level = 'INFO'): void
{
    global $logHandle;
    $line = '[' . date('Y-m-d H:i:s') . "] [$level] $msg";
    echo $line . PHP_EOL;
    fwrite($logHandle, $line . PHP_EOL);
}

function log_separator(): void
{
    log_msg(str_repeat('─', 60));
}

/**
 * Normalise un chemin d'image stocké en DB vers un chemin absolu sur disque.
 *
 * Cas gérés :
 *   members/125/xxx.jpg                            → OLD_PROJECT_PATH/members/125/xxx.jpg
 *   /members/administrateur/1/sponsor/xxx.jpeg     → OLD_PROJECT_PATH/members/...  (supprime /)
 *   ../uploads/ima1.png                            → OLD_PROJECT_PATH/uploads/ima1.png
 *   categorie/xxx.png                              → OLD_PROJECT_PATH/categorie/xxx.png
 *   avatar/xxx.png                                 → OLD_PROJECT_PATH/avatar/xxx.png
 *   dashimmo/avatar.png                            → OLD_PROJECT_PATH/dashimmo/avatar.png
 *   https://res.cloudinary.com/…                   → null (déjà migré)
 */
function resolveLocalPath(?string $dbPath): ?string
{
    if (empty($dbPath)) {
        return null;
    }

    // Déjà une URL Cloudinary → pas besoin de migrer
    if (str_starts_with($dbPath, 'https://res.cloudinary.com/') || str_starts_with($dbPath, 'http://res.cloudinary.com/')) {
        return null;
    }

    // Supprimer le slash initial (/members/... → members/...)
    $path = ltrim($dbPath, '/');

    // ../uploads/xxx → uploads/xxx
    $path = preg_replace('#^\.\./uploads/#', 'uploads/', $path);

    // Chemin absolu
    return OLD_PROJECT_PATH . '/' . $path;
}

/**
 * Génère un public_id Cloudinary propre (sans extension, sans caractères spéciaux)
 */
function buildPublicId(string $folder, string $filename): string
{
    // Supprimer l'extension
    $name = pathinfo($filename, PATHINFO_FILENAME);
    // Nettoyer les caractères
    $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
    return CLOUDINARY_FOLDER . '/' . $folder . '/' . $name;
}

// ═══════════════════════════════════════════════════════════════════════════
// Connexion DB
// ═══════════════════════════════════════════════════════════════════════════

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo "❌ Connexion DB échouée : " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// ═══════════════════════════════════════════════════════════════════════════
// Connexion Cloudinary
// ═══════════════════════════════════════════════════════════════════════════

$cloudinary = new Cloudinary(
    Configuration::instance([
        'cloud' => [
            'cloud_name' => CLOUDINARY_CLOUD,
            'api_key'    => CLOUDINARY_KEY,
            'api_secret' => CLOUDINARY_SECRET,
        ],
        'url' => ['secure' => true],
    ])
);
$uploadApi = $cloudinary->uploadApi();

// ═══════════════════════════════════════════════════════════════════════════
// Sauvegarde des URLs actuelles (avant modification)
// ═══════════════════════════════════════════════════════════════════════════

function backupCurrentUrls(PDO $pdo): void
{
    log_msg("Sauvegarde des URLs actuelles dans backup_before_cloudinary.sql...");

    $tables = [
        'produits'  => ['image_produits', 'image_produits1', 'image_produits2'],
        'clients'   => ['image_client'],
        'sponsor'   => ['image_sponsor'],
        'categorie' => ['image_categorie'],
        'posts'     => ['image1', 'image2'],
        'users'     => ['image_user'],
    ];

    $sql = "-- Backup URLs avant migration Cloudinary — " . date('Y-m-d H:i:s') . PHP_EOL;
    $sql .= "-- Pour rollback : importer ce fichier dans " . DB_NAME . PHP_EOL . PHP_EOL;

    foreach ($tables as $table => $columns) {
        $colList = implode(', ', array_map(fn($c) => "`$c`", $columns));
        $pkMap = [
            'produits'  => 'id_produits',
            'clients'   => 'id_client',
            'sponsor'   => 'id_sponsor',
            'categorie' => 'id_categorie',
            'posts'     => 'idposts',
            'users'     => 'id_user',
        ];
        $pkCol = $pkMap[$table];
        $rows = $pdo->query("SELECT `{$pkCol}`, {$colList} FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $pk = $row[$pkCol];
            $sets = [];
            foreach ($columns as $col) {
                if (!empty($row[$col])) {
                    $escaped = addslashes($row[$col]);
                    $sets[] = "`{$col}` = '{$escaped}'";
                }
            }
            if (!empty($sets)) {
                $sql .= "UPDATE `{$table}` SET " . implode(', ', $sets) . " WHERE `{$pkCol}` = {$pk};" . PHP_EOL;
            }
        }
    }

    file_put_contents(BACKUP_SQL, $sql);
    log_msg("✅ Backup créé : " . BACKUP_SQL);
}

// ═══════════════════════════════════════════════════════════════════════════
// Fonction principale d'upload
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Upload une image sur Cloudinary et retourne la secure_url.
 * Retourne null en cas d'échec.
 */
function uploadToCloudinary($uploadApi, string $filePath, string $folder, string $filename): ?string
{
    $publicId = buildPublicId($folder, $filename);

    try {
        $result = $uploadApi->upload($filePath, [
            'folder'          => CLOUDINARY_FOLDER . '/' . $folder,
            'public_id'       => pathinfo(buildPublicId($folder, $filename), PATHINFO_BASENAME),
            'overwrite'       => false,
            'resource_type'   => 'image',
        ]);
        return $result['secure_url'];
    } catch (\Exception $e) {
        log_msg("Upload échoué pour $filePath : " . $e->getMessage(), 'ERROR');
        return null;
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Compteurs globaux
// ═══════════════════════════════════════════════════════════════════════════

$stats = [
    'total'     => 0,
    'uploaded'  => 0,
    'skipped'   => 0,
    'missing'   => 0,
    'errors'    => 0,
];

/**
 * Traite une table : pour chaque ligne, pour chaque colonne image,
 * télécharge sur Cloudinary et met à jour la DB.
 */
function processTable(
    PDO    $pdo,
    $uploadApi,
    string $table,
    string $pkCol,
    array  $imageColumns,
    string $cloudFolder,
    array  &$stats
): void {
    log_separator();
    log_msg("📁 TABLE: $table (colonnes: " . implode(', ', $imageColumns) . ")");

    $colList = implode(', ', array_map(fn($c) => "`$c`", $imageColumns));
    $rows = $pdo->query("SELECT `{$pkCol}`, {$colList} FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);

    $tableUploaded = 0;
    $tableSkipped  = 0;
    $tableMissing  = 0;
    $tableErrors   = 0;

    foreach ($rows as $row) {
        $pk = $row[$pkCol];

        foreach ($imageColumns as $col) {
            $dbPath = $row[$col] ?? null;
            $stats['total']++;

            if (empty($dbPath)) {
                $stats['skipped']++;
                $tableSkipped++;
                continue;
            }

            // Déjà migré sur Cloudinary ?
            if (str_starts_with($dbPath, 'https://res.cloudinary.com/') || str_starts_with($dbPath, 'http://res.cloudinary.com/')) {
                log_msg("  [$table #$pk | $col] ⏭️  Déjà Cloudinary — skip", 'SKIP');
                $stats['skipped']++;
                $tableSkipped++;
                continue;
            }

            // Résoudre le chemin local
            $localPath = resolveLocalPath($dbPath);

            if ($localPath === null || !file_exists($localPath)) {
                log_msg("  [$table #$pk | $col] ⚠️  Fichier manquant : $dbPath → $localPath", 'WARN');
                $stats['missing']++;
                $tableMissing++;
                continue;
            }

            // Upload sur Cloudinary
            $filename = basename($localPath);
            log_msg("  [$table #$pk | $col] ⬆️  Upload : $dbPath");

            if (DRY_RUN) {
                log_msg("  [$table #$pk | $col] 🔵 [DRY RUN] Simulé — pas de vrai upload", 'DRY');
                $stats['skipped']++;
                $tableSkipped++;
                continue;
            }

            $cloudUrl = uploadToCloudinary($uploadApi, $localPath, $cloudFolder, $filename);

            if ($cloudUrl === null) {
                $stats['errors']++;
                $tableErrors++;
                continue;
            }

            // Mise à jour DB
            try {
                $stmt = $pdo->prepare("UPDATE `{$table}` SET `{$col}` = ? WHERE `{$pkCol}` = ?");
                $stmt->execute([$cloudUrl, $pk]);
                log_msg("  [$table #$pk | $col] ✅ $cloudUrl", 'OK');
                $stats['uploaded']++;
                $tableUploaded++;
            } catch (PDOException $e) {
                log_msg("  [$table #$pk | $col] ❌ Erreur UPDATE DB : " . $e->getMessage(), 'ERROR');
                $stats['errors']++;
                $tableErrors++;
            }
        }
    }

    log_msg("  → Résultat $table : ✅ $tableUploaded uploadés | ⏭️ $tableSkipped ignorés | ⚠️ $tableMissing manquants | ❌ $tableErrors erreurs");
}

// ═══════════════════════════════════════════════════════════════════════════
// MAIN
// ═══════════════════════════════════════════════════════════════════════════

log_separator();
log_msg("🚀 DÉBUT MIGRATION MÉDIAS → CLOUDINARY");
log_msg("   Mode : " . (DRY_RUN ? "DRY RUN (simulation)" : "PRODUCTION (modifie la DB)"));
log_msg("   Cloud: " . CLOUDINARY_CLOUD);
log_msg("   DB   : " . DB_NAME . "@" . DB_HOST);
log_msg("   Src  : " . OLD_PROJECT_PATH);
log_separator();

// 1. Sauvegarder les URLs actuelles avant toute modification
backupCurrentUrls($pdo);

// 2. Traiter chaque table
// ─── PRODUITS ────────────────────────────────────────────────────────────
processTable(
    $pdo, $uploadApi,
    'produits', 'id_produits',
    ['image_produits', 'image_produits1', 'image_produits2'],
    'produits',
    $stats
);

// ─── CLIENTS (avatar/photo profil) ───────────────────────────────────────
processTable(
    $pdo, $uploadApi,
    'clients', 'id_client',
    ['image_client'],
    'clients',
    $stats
);

// ─── SPONSOR ─────────────────────────────────────────────────────────────
processTable(
    $pdo, $uploadApi,
    'sponsor', 'id_sponsor',
    ['image_sponsor'],
    'sponsors',
    $stats
);

// ─── CATÉGORIES ──────────────────────────────────────────────────────────
processTable(
    $pdo, $uploadApi,
    'categorie', 'id_categorie',
    ['image_categorie'],
    'categories',
    $stats
);

// ─── POSTS (blog) ────────────────────────────────────────────────────────
processTable(
    $pdo, $uploadApi,
    'posts', 'idposts',
    ['image1', 'image2'],
    'blog',
    $stats
);

// ─── USERS (admins) ──────────────────────────────────────────────────────
processTable(
    $pdo, $uploadApi,
    'users', 'id_user',
    ['image_user'],
    'admins',
    $stats
);

// ═══════════════════════════════════════════════════════════════════════════
// Résumé final
// ═══════════════════════════════════════════════════════════════════════════

log_separator();
log_msg("📊 RÉSUMÉ FINAL");
log_msg("   Total traité  : " . $stats['total']);
log_msg("   ✅ Uploadés   : " . $stats['uploaded']);
log_msg("   ⏭️  Ignorés   : " . $stats['skipped'] . " (vides ou déjà Cloudinary)");
log_msg("   ⚠️  Manquants  : " . $stats['missing'] . " (fichier absent sur disque)");
log_msg("   ❌ Erreurs    : " . $stats['errors']);
log_separator();

if ($stats['errors'] > 0) {
    log_msg("⚠️  Des erreurs ont eu lieu. Voir le log pour les détails.", 'WARN');
    log_msg("   En cas de problème, restaurez avec : mysql " . DB_NAME . " < " . BACKUP_SQL, 'WARN');
} else {
    log_msg("🎉 Migration Cloudinary terminée sans erreur !");
    log_msg("→ Passez à step3_verify.sh pour valider le résultat.");
}

fclose($logHandle);
