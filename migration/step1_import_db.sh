#!/bin/bash
# ════════════════════════════════════════════════════════════════════════════
# ÉTAPE 1 — IMPORT DES DONNÉES DE L'ANCIEN DB VERS LE NOUVEAU DB LARAVEL
#
# Stratégie :
#   1. Importer le dump complet dans une DB temporaire (topideal_migration_temp)
#   2. Copier les données vers le nouveau DB (topspaceideal) via INSERT SELECT
#   3. Supprimer la DB temporaire
#
# PRÉREQUIS :
#   - php artisan migrate:fresh  (déjà exécuté)
#   - mysql accessible depuis le terminal
#
# UTILISATION :
#   chmod +x step1_import_db.sh
#   ./step1_import_db.sh
# ════════════════════════════════════════════════════════════════════════════

set -e

# ─── CONFIGURATION ──────────────────────────────────────────────────────────
SQL_DUMP="/chemin/vers/dump.sql"
NEW_DB="topspaceideal"
TEMP_DB="topideal_migration_temp"
DB_USER="root"
DB_PASS="votre-password"
DB_HOST="127.0.0.1"
DB_PORT="3306"

LOG_FILE="$(dirname "$0")/migration_db.log"
# ────────────────────────────────────────────────────────────────────────────

MYSQL="mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} -p${DB_PASS}"

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee "$LOG_FILE"
echo "  MIGRATION DB — $(date)" | tee -a "$LOG_FILE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$LOG_FILE"

# Vérifier le dump
if [ ! -f "$SQL_DUMP" ]; then
    echo "ERREUR : Fichier SQL introuvable : $SQL_DUMP" | tee -a "$LOG_FILE"
    exit 1
fi

# Vérifier la connexion MySQL
$MYSQL -e "SELECT 1" > /dev/null 2>&1 || {
    echo "ERREUR : Impossible de se connecter a MySQL" | tee -a "$LOG_FILE"
    exit 1
}
echo "Connexion MySQL OK" | tee -a "$LOG_FILE"

# ─── ÉTAPE 1A : Créer la DB temporaire et importer le dump ──────────────────
echo "" | tee -a "$LOG_FILE"
echo "Creation de la DB temporaire : $TEMP_DB..." | tee -a "$LOG_FILE"
$MYSQL -e "DROP DATABASE IF EXISTS \`${TEMP_DB}\`; CREATE DATABASE \`${TEMP_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | tee -a "$LOG_FILE"

echo "Import du dump dans $TEMP_DB..." | tee -a "$LOG_FILE"
$MYSQL "$TEMP_DB" < "$SQL_DUMP" 2>&1 | tee -a "$LOG_FILE"

echo "Dump importe dans $TEMP_DB" | tee -a "$LOG_FILE"

# Vérification
echo "" | tee -a "$LOG_FILE"
echo "Tables dans $TEMP_DB :" | tee -a "$LOG_FILE"
$MYSQL "$TEMP_DB" -e "SHOW TABLES;" 2>/dev/null | tee -a "$LOG_FILE"

# ─── ÉTAPE 1B : Copie vers le nouveau DB ────────────────────────────────────
echo "" | tee -a "$LOG_FILE"
echo "Copie des donnees vers $NEW_DB..." | tee -a "$LOG_FILE"

TABLES=(
    "state"
    "statetickets"
    "users"
    "categorie"
    "sous_categorie"
    "clients"
    "sponsor"
    "produits"
    "ticket"
    "demande"
    "posts"
    "reponseblog"
    "vues"
)

$MYSQL "$NEW_DB" -e "SET foreign_key_checks = 0;" 2>/dev/null

for TABLE in "${TABLES[@]}"; do
    echo -n "  -> $TABLE : " | tee -a "$LOG_FILE"

    TABLE_EXISTS=$($MYSQL "$TEMP_DB" -se "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='${TEMP_DB}' AND TABLE_NAME='${TABLE}'" 2>/dev/null)
    if [ "$TABLE_EXISTS" == "0" ]; then
        echo "  Table absente dans le dump" | tee -a "$LOG_FILE"
        continue
    fi

    OLD_COUNT=$($MYSQL "$TEMP_DB" -se "SELECT COUNT(*) FROM \`${TABLE}\`" 2>/dev/null)

    $MYSQL "$NEW_DB" -e "SET foreign_key_checks=0; TRUNCATE TABLE \`${TABLE}\`;" 2>/dev/null

    $MYSQL -e "SET foreign_key_checks=0; INSERT INTO \`${NEW_DB}\`.\`${TABLE}\` SELECT * FROM \`${TEMP_DB}\`.\`${TABLE}\`;" 2>&1 | tee -a "$LOG_FILE" || true

    NEW_COUNT=$($MYSQL "$NEW_DB" -se "SELECT COUNT(*) FROM \`${TABLE}\`" 2>/dev/null)
    echo "OK : $OLD_COUNT -> $NEW_COUNT enregistrements" | tee -a "$LOG_FILE"
done

$MYSQL "$NEW_DB" -e "SET foreign_key_checks = 1;" 2>/dev/null

# ─── ÉTAPE 1C : Nettoyage ────────────────────────────────────────────────────
echo "" | tee -a "$LOG_FILE"
echo "Suppression de la DB temporaire $TEMP_DB..." | tee -a "$LOG_FILE"
$MYSQL -e "DROP DATABASE IF EXISTS \`${TEMP_DB}\`;" 2>&1 | tee -a "$LOG_FILE"

# ─── VÉRIFICATION FINALE ─────────────────────────────────────────────────────
echo "" | tee -a "$LOG_FILE"
echo "Donnees dans $NEW_DB :" | tee -a "$LOG_FILE"
for TABLE in "${TABLES[@]}"; do
    COUNT=$($MYSQL "$NEW_DB" -se "SELECT COUNT(*) FROM \`$TABLE\`" 2>/dev/null || echo "?")
    printf "   %-20s : %s enregistrements\n" "$TABLE" "$COUNT" | tee -a "$LOG_FILE"
done

echo "" | tee -a "$LOG_FILE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$LOG_FILE"
echo "  ETAPE 1 TERMINEE — Passez a step2_cloudinary_migration.php" | tee -a "$LOG_FILE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$LOG_FILE"
