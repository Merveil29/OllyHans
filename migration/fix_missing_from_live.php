<?php
/**
 * Récupère les images manquantes depuis le serveur live (topidealspace.com)
 * Upload → Cloudinary → met à jour le DB
 */

require '/chemin/vers/backend/vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

// ─── CONFIG ──────────────────────────────────────────────────────────────────
define('LIVE_BASE_URL', 'https://topidealspace.com');
define('TEMP_DIR',      sys_get_temp_dir() . '/topideal_missing/');

$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=votre-db;charset=utf8mb4',
    'root', 'votre-password',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$cloudinary = new Cloudinary(
    Configuration::instance([
        'cloud' => [
            'cloud_name' => 'votre-cloud',
            'api_key'    => 'votre-key',
            'api_secret' => 'votre-secret',
        ],
        'url' => ['secure' => true],
    ])
);

@mkdir(TEMP_DIR, 0755, true);
// ─────────────────────────────────────────────────────────────────────────────

$stats = ['downloaded' => 0, 'uploaded' => 0, 'failed' => 0];

/**
 * Télécharge un fichier depuis le serveur live dans /tmp
 */
function downloadFromLive(string $remotePath): ?string
{
    // Normalise le chemin (supprimer /leading slash, ../uploads)
    $path = ltrim($remotePath, '/');
    $path = preg_replace('#^\.\./uploads/#', 'uploads/', $path);

    $url      = LIVE_BASE_URL . '/' . $path;
    $filename = basename($path);
    $tempFile = TEMP_DIR . $filename;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0',
    ]);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($code !== 200 || empty($data)) {
        echo "  ❌ Download FAILED ($code) : $url" . ($err ? " [$err]" : '') . PHP_EOL;
        return null;
    }

    file_put_contents($tempFile, $data);
    echo "  ✅ Downloaded ($code) : $url → $tempFile" . PHP_EOL;
    return $tempFile;
}

/**
 * Upload le fichier temp sur Cloudinary, retourne la secure_url
 */
function uploadToCloud($cloudinary, string $localFile, string $folder): ?string
{
    global $stats;
    $filename = pathinfo($localFile, PATHINFO_FILENAME);
    $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);

    try {
        $result = $cloudinary->uploadApi()->upload($localFile, [
            'folder'        => 'topideal/' . $folder,
            'public_id'     => $filename,
            'overwrite'     => true,
            'resource_type' => 'image',
        ]);
        echo "  ☁️  Cloudinary: " . $result['secure_url'] . PHP_EOL;
        @unlink($localFile);
        $stats['uploaded']++;
        return $result['secure_url'];
    } catch (Exception $e) {
        echo "  ❌ Cloudinary FAILED: " . $e->getMessage() . PHP_EOL;
        $stats['failed']++;
        return null;
    }
}

/**
 * Traite une image : download → upload → retourne l'URL
 */
function migrateOne($cloudinary, string $dbPath, string $folder): ?string
{
    global $stats;

    if (empty($dbPath) || str_starts_with($dbPath, 'https://res.cloudinary.com/')) {
        return null; // déjà migré
    }

    $localFile = downloadFromLive($dbPath);
    if ($localFile === null) {
        $stats['failed']++;
        return null;
    }

    $stats['downloaded']++;
    return uploadToCloud($cloudinary, $localFile, $folder);
}

// ═══════════════════════════════════════════════════════════════════════════
// PRODUITS (6 produits × 3 colonnes)
// ═══════════════════════════════════════════════════════════════════════════

echo PHP_EOL . "═══ PRODUITS ═══" . PHP_EOL;

$rows = $pdo->query("
    SELECT id_produits, image_produits, image_produits1, image_produits2
    FROM produits
    WHERE image_produits NOT LIKE 'https://res.cloudinary.com/%'
      AND image_produits IS NOT NULL AND image_produits != ''
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $id = $row['id_produits'];
    echo PHP_EOL . "→ Produit #$id" . PHP_EOL;

    $updates = [];

    foreach (['image_produits' => 'produits', 'image_produits1' => 'produits', 'image_produits2' => 'produits'] as $col => $folder) {
        $url = migrateOne($cloudinary, $row[$col], $folder);
        if ($url) {
            $updates[$col] = $url;
        }
    }

    if (!empty($updates)) {
        $sets = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($updates)));
        $stmt = $pdo->prepare("UPDATE produits SET $sets WHERE id_produits = ?");
        $stmt->execute([...array_values($updates), $id]);
        echo "  ✔️  DB mis à jour pour produit #$id" . PHP_EOL;
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// CLIENTS (profils manquants)
// ═══════════════════════════════════════════════════════════════════════════

echo PHP_EOL . "═══ CLIENTS ═══" . PHP_EOL;

$rows = $pdo->query("
    SELECT id_client, image_client
    FROM clients
    WHERE image_client NOT LIKE 'https://res.cloudinary.com/%'
      AND image_client IS NOT NULL AND image_client != ''
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $id  = $row['id_client'];
    echo PHP_EOL . "→ Client #$id" . PHP_EOL;

    $url = migrateOne($cloudinary, $row['image_client'], 'clients');
    if ($url) {
        $pdo->prepare("UPDATE clients SET image_client = ? WHERE id_client = ?")
            ->execute([$url, $id]);
        echo "  ✔️  DB mis à jour pour client #$id" . PHP_EOL;
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// SPONSORS
// ═══════════════════════════════════════════════════════════════════════════

echo PHP_EOL . "═══ SPONSORS ═══" . PHP_EOL;

$rows = $pdo->query("
    SELECT id_sponsor, image_sponsor
    FROM sponsor
    WHERE image_sponsor NOT LIKE 'https://res.cloudinary.com/%'
      AND image_sponsor IS NOT NULL AND image_sponsor != ''
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $id  = $row['id_sponsor'];
    echo PHP_EOL . "→ Sponsor #$id" . PHP_EOL;

    $url = migrateOne($cloudinary, $row['image_sponsor'], 'sponsors');
    if ($url) {
        $pdo->prepare("UPDATE sponsor SET image_sponsor = ? WHERE id_sponsor = ?")
            ->execute([$url, $id]);
        echo "  ✔️  DB mis à jour pour sponsor #$id" . PHP_EOL;
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// RÉSUMÉ
// ═══════════════════════════════════════════════════════════════════════════

echo PHP_EOL . "═══════════════════════════════" . PHP_EOL;
echo "✅ Téléchargés  : " . $stats['downloaded'] . PHP_EOL;
echo "☁️  Sur Cloudinary: " . $stats['uploaded'] . PHP_EOL;
echo "❌ Échecs       : " . $stats['failed'] . PHP_EOL;
echo "═══════════════════════════════" . PHP_EOL;

// Nettoyage temp
@rmdir(TEMP_DIR);
