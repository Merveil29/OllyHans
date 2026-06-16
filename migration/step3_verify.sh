#!/bin/bash
# ════════════════════════════════════════════════════════════════════════════
# ÉTAPE 3 — VÉRIFICATION & ROLLBACK
#
# Ce script vérifie que la migration est complète :
#   - Compte les URLs Cloudinary dans chaque table
#   - Repère les URLs locales restantes (non migrées)
#   - Offre un rollback en cas de problème
#
# UTILISATION :
#   ./step3_verify.sh            # vérification seulement
#   ./step3_verify.sh --rollback # restaure les anciennes URLs depuis le backup
# ════════════════════════════════════════════════════════════════════════════

set -e

DB_NAME="votre-db"
DB_USER="root"
DB_PASS="votre-password"
DB_HOST="127.0.0.1"
DB_PORT="3306"

BACKUP_SQL="$(dirname "$0")/backup_before_cloudinary.sql"
LOG_FILE="$(dirname "$0")/verification.log"

MYSQL="mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} -p${DB_PASS} ${DB_NAME}"

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee "$LOG_FILE"
echo "  VÉRIFICATION MIGRATION — $(date)" | tee -a "$LOG_FILE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$LOG_FILE"

# ─── ROLLBACK ───────────────────────────────────────────────────────────────
if [ "$1" == "--rollback" ]; then
    echo "" | tee -a "$LOG_FILE"
    echo "⚠️  ROLLBACK DEMANDÉ" | tee -a "$LOG_FILE"

    if [ ! -f "$BACKUP_SQL" ]; then
        echo "❌ Fichier backup introuvable : $BACKUP_SQL" | tee -a "$LOG_FILE"
        exit 1
    fi

    echo "🔄 Restauration des anciennes URLs depuis $BACKUP_SQL..." | tee -a "$LOG_FILE"
    $MYSQL < "$BACKUP_SQL" 2>&1 | tee -a "$LOG_FILE"
    echo "✅ Rollback terminé. Les URLs originales ont été restaurées." | tee -a "$LOG_FILE"
    exit 0
fi

# ─── VÉRIFICATION ──────────────────────────────────────────────────────────

echo "" | tee -a "$LOG_FILE"
echo "📊 ÉTAT DES IMAGES PAR TABLE :" | tee -a "$LOG_FILE"
echo "" | tee -a "$LOG_FILE"

# Fonction pour vérifier une table/colonne
check_column() {
    local table="$1"
    local col="$2"
    local pk="$3"

    TOTAL=$($MYSQL -se "SELECT COUNT(*) FROM \`${table}\` WHERE \`${col}\` IS NOT NULL AND \`${col}\` != ''" 2>/dev/null)
    CLOUDINARY=$($MYSQL -se "SELECT COUNT(*) FROM \`${table}\` WHERE \`${col}\` LIKE 'https://res.cloudinary.com/%'" 2>/dev/null)
    LOCAL=$((TOTAL - CLOUDINARY))

    STATUS="✅"
    [ "$LOCAL" -gt 0 ] && STATUS="⚠️ "

    printf "  %-12s %-22s  Total:%-5s  Cloudinary:%-5s  Local:%-5s  %s\n" \
        "$table" "$col" "$TOTAL" "$CLOUDINARY" "$LOCAL" "$STATUS" | tee -a "$LOG_FILE"

    # Si des URLs locales restent, les lister
    if [ "$LOCAL" -gt 0 ]; then
        echo "     → URLs locales restantes :" | tee -a "$LOG_FILE"
        $MYSQL -se "SELECT \`${pk}\`, \`${col}\` FROM \`${table}\` WHERE \`${col}\` IS NOT NULL AND \`${col}\` != '' AND \`${col}\` NOT LIKE 'https://res.cloudinary.com/%' LIMIT 5" 2>/dev/null | \
            while read row; do echo "       $row"; done | tee -a "$LOG_FILE"
    fi
}

printf "  %-12s %-22s  %s\n" "TABLE" "COLONNE" "STATUT" | tee -a "$LOG_FILE"
printf "  %s\n" "$(printf '─%.0s' {1..70})" | tee -a "$LOG_FILE"

check_column "produits"  "image_produits"  "id_produits"
check_column "produits"  "image_produits1" "id_produits"
check_column "produits"  "image_produits2" "id_produits"
check_column "clients"   "image_client"    "id_client"
check_column "sponsor"   "image_sponsor"   "id_sponsor"
check_column "categorie" "image_categorie" "id_categorie"
check_column "posts"     "image1"          "idposts"
check_column "posts"     "image2"          "idposts"
check_column "users"     "image_user"      "id_user"

echo "" | tee -a "$LOG_FILE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$LOG_FILE"

# ─── VÉRIFICATION DES DONNÉES GÉNÉRALES ────────────────────────────────────
echo "" | tee -a "$LOG_FILE"
echo "📋 COMPTES DE DONNÉES GLOBALES :" | tee -a "$LOG_FILE"
for TABLE in state statetickets users categorie sous_categorie clients sponsor produits ticket demande posts reponseblog vues; do
    COUNT=$($MYSQL -se "SELECT COUNT(*) FROM \`$TABLE\`" 2>/dev/null || echo "?")
    printf "   %-18s : %s enregistrements\n" "$TABLE" "$COUNT" | tee -a "$LOG_FILE"
done

echo "" | tee -a "$LOG_FILE"
echo "ℹ️  En cas de problème : ./step3_verify.sh --rollback" | tee -a "$LOG_FILE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$LOG_FILE"
