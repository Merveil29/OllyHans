#!/bin/bash

# Script de test de la page profil
# Usage: ./test_profile.sh <token>

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

BASE_URL="http://127.0.0.1:8000/api/v1"
TOKEN=$1

if [ -z "$TOKEN" ]; then
    echo -e "${RED}❌ Erreur: Token manquant${NC}"
    echo "Usage: ./test_profile.sh <token>"
    echo ""
    echo "Pour obtenir un token:"
    echo "1. Connectez-vous via l'interface ou:"
    echo "   curl -X POST http://127.0.0.1:8000/api/v1/login \\"
    echo "     -H 'Content-Type: application/json' \\"
    echo "     -d '{\"login\":\"votre_login\",\"password\":\"votre_mdp\"}'"
    exit 1
fi

echo -e "${YELLOW}🧪 Tests de la Page Profil${NC}"
echo "======================================"
echo ""

# Test 1: Get Profile
echo -e "${YELLOW}Test 1: Récupération du profil${NC}"
RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "$BASE_URL/profile" \
  -H "Authorization: Bearer $TOKEN")
HTTP_CODE=$(echo "$RESPONSE" | tail -n 1)
BODY=$(echo "$RESPONSE" | head -n -1)

if [ "$HTTP_CODE" -eq 200 ]; then
    echo -e "${GREEN}✅ GET /profile - OK (200)${NC}"
    echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
else
    echo -e "${RED}❌ GET /profile - FAILED ($HTTP_CODE)${NC}"
    echo "$BODY"
fi
echo ""

# Test 2: Update Profile
echo -e "${YELLOW}Test 2: Mise à jour du profil${NC}"
RESPONSE=$(curl -s -w "\n%{http_code}" -X PUT "$BASE_URL/profile" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "TestNom",
    "prenom": "TestPrenom",
    "telephone": "0123456789",
    "adresse": "123 Rue Test"
  }')
HTTP_CODE=$(echo "$RESPONSE" | tail -n 1)
BODY=$(echo "$RESPONSE" | head -n -1)

if [ "$HTTP_CODE" -eq 200 ]; then
    echo -e "${GREEN}✅ PUT /profile - OK (200)${NC}"
    echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
else
    echo -e "${RED}❌ PUT /profile - FAILED ($HTTP_CODE)${NC}"
    echo "$BODY"
fi
echo ""

# Test 3: Update Profile (validation error)
echo -e "${YELLOW}Test 3: Validation des données (téléphone invalide)${NC}"
RESPONSE=$(curl -s -w "\n%{http_code}" -X PUT "$BASE_URL/profile" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "",
    "telephone": "abc"
  }')
HTTP_CODE=$(echo "$RESPONSE" | tail -n 1)
BODY=$(echo "$RESPONSE" | head -n -1)

if [ "$HTTP_CODE" -eq 422 ]; then
    echo -e "${GREEN}✅ Validation fonctionne - OK (422)${NC}"
    echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
else
    echo -e "${YELLOW}⚠️  Validation - Statut inattendu ($HTTP_CODE)${NC}"
    echo "$BODY"
fi
echo ""

# Test 4: Upload Image (nécessite un fichier)
echo -e "${YELLOW}Test 4: Upload d'image (skipped - nécessite un fichier)${NC}"
echo "Pour tester l'upload d'image:"
echo "curl -X POST $BASE_URL/profile/image \\"
echo "  -H 'Authorization: Bearer $TOKEN' \\"
echo "  -F 'image=@/path/to/image.jpg'"
echo ""

# Test 5: Delete Image
echo -e "${YELLOW}Test 5: Suppression de l'image de profil${NC}"
RESPONSE=$(curl -s -w "\n%{http_code}" -X DELETE "$BASE_URL/profile/image" \
  -H "Authorization: Bearer $TOKEN")
HTTP_CODE=$(echo "$RESPONSE" | tail -n 1)
BODY=$(echo "$RESPONSE" | head -n -1)

if [ "$HTTP_CODE" -eq 200 ]; then
    echo -e "${GREEN}✅ DELETE /profile/image - OK (200)${NC}"
    echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
else
    echo -e "${RED}❌ DELETE /profile/image - FAILED ($HTTP_CODE)${NC}"
    echo "$BODY"
fi
echo ""

# Test 6: Change Password
echo -e "${YELLOW}Test 6: Changement de mot de passe (test avec mdp incorrect)${NC}"
RESPONSE=$(curl -s -w "\n%{http_code}" -X PUT "$BASE_URL/profile/password" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "wrong_password",
    "new_password": "NewSecure123!",
    "new_password_confirmation": "NewSecure123!"
  }')
HTTP_CODE=$(echo "$RESPONSE" | tail -n 1)
BODY=$(echo "$RESPONSE" | head -n -1)

if [ "$HTTP_CODE" -eq 422 ]; then
    echo -e "${GREEN}✅ Validation mot de passe actuel - OK (422)${NC}"
    echo "$BODY" | jq '.' 2>/dev/null || echo "$BODY"
else
    echo -e "${YELLOW}⚠️  Change password - Statut inattendu ($HTTP_CODE)${NC}"
    echo "$BODY"
fi
echo ""

# Résumé
echo "======================================"
echo -e "${GREEN}🎉 Tests terminés !${NC}"
echo ""
echo "REMARQUES:"
echo "- Pour tester l'upload d'image, utilisez une vraie image"
echo "- Pour tester le changement de mot de passe avec succès,"
echo "  utilisez le bon mot de passe actuel"
echo "- Assurez-vous que Cloudinary est configuré dans .env"
echo ""
