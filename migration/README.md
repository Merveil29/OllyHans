# Guide de Migration — TopIdealSpace
## Ancien PHP → Laravel + Cloudinary

---

## Prérequis

| Élément | Valeur |
|---|---|
| Ancien projet (médias) | `./topideal/` (présent en local) |
| Dump SQL | `dwsaqreq_topidealspace(1).sql` |
| Nouveau DB | `topspaceideal` (MySQL local) |
| Cloudinary cloud | `votre-cloud` |
| Backend Laravel | `./backend/` |

---

## Structure du dossier `migration/`

```
migration/
├── step1_import_db.sh              ← Import données de l'ancien DB
├── step2_cloudinary_migration.php  ← Upload médias → Cloudinary + update DB
├── step3_verify.sh                 ← Vérification + rollback
├── README.md                       ← Ce guide
│
│   (générés automatiquement)
├── migration_db.log                ← Log de l'étape 1
├── migration_cloudinary.log        ← Log de l'étape 2
├── verification.log                ← Log de l'étape 3
└── backup_before_cloudinary.sql    ← Backup URLs avant migration (rollback)
```

---

## ÉTAPE 0 — Préparation

```bash
# 1. Se placer à la racine du projet
cd /chemin/vers/le/projet

# 2. S'assurer que Laravel a ses migrations fraîches
cd backend && php artisan migrate:fresh && cd ..

# 3. Rendre les scripts exécutables
chmod +x migration/step1_import_db.sh migration/step3_verify.sh
```

---

## ÉTAPE 1 — Import des données DB

Ce script extrait les `INSERT` de l'ancien dump et les charge dans le nouveau DB Laravel.

```bash
cd /chemin/vers/le/projet
./migration/step1_import_db.sh
```

**Ce qui est importé :**

| Table | Contenu |
|---|---|
| `state` | Référentiel statuts produits (en attente, publié, refusé) |
| `statetickets` | Référentiel statuts tickets |
| `users` | Administrateurs |
| `categorie` | Catégories avec images |
| `sous_categorie` | Sous-catégories |
| `clients` | Utilisateurs clients |
| `sponsor` | Sponsors avec images |
| `produits` | Annonces avec 3 colonnes images |
| `ticket` | Tickets support |
| `demande` | Messages de tickets |
| `posts` | Articles blog |
| `reponseblog` | Commentaires blog |
| `vues` | Statistiques de vues |

**Ce qui n'est PAS importé (tables nouvelles, vides):**
- `transactions`, `newsletter_subscribers`, `personal_access_tokens`, `withdrawals`, `password_reset_tokens`, `sessions`, `infos_cv`, etc.

**Log :** `migration/migration_db.log`

---

## ÉTAPE 2 — Migration des médias vers Cloudinary

Ce script lit chaque chemin d'image en DB, télécharge le fichier depuis `topideal/` et l'upload sur Cloudinary. Ensuite il met à jour la DB avec l'URL Cloudinary.

```bash
cd /chemin/vers/le/projet
php migration/step2_cloudinary_migration.php
```

> **Test avant exécution :** Ouvrez le script et passez `DRY_RUN` à `true` pour une simulation sans modification.

**Colonnes migrées :**

| Table | Colonnes | Dossier Cloudinary |
|---|---|---|
| `produits` | `image_produits`, `image_produits1`, `image_produits2` | `topideal/produits/` |
| `clients` | `image_client` | `topideal/clients/` |
| `sponsor` | `image_sponsor` | `topideal/sponsors/` |
| `categorie` | `image_categorie` | `topideal/categories/` |
| `posts` | `image1`, `image2` | `topideal/blog/` |
| `users` | `image_user` | `topideal/admins/` |

**Normalisation des chemins :**

| Chemin DB | Résolution |
|---|---|
| `members/125/xxx.jpg` | `topideal/members/125/xxx.jpg` |
| `/members/administrateur/1/sponsor/xxx.jpeg` | `topideal/members/administrateur/1/sponsor/xxx.jpeg` |
| `../uploads/ima1.png` | `topideal/uploads/ima1.png` |
| `categorie/xxx.png` | `topideal/categorie/xxx.png` |
| `avatar/xxx.png` | `topideal/avatar/xxx.png` |
| `https://res.cloudinary.com/…` | — Ignoré (déjà migré) |

**Log :** `migration/migration_cloudinary.log`

**Backup rollback :** `migration/backup_before_cloudinary.sql` (créé automatiquement avant toute modification)

---

## ÉTAPE 3 — Vérification

```bash
./migration/step3_verify.sh
```

Résultat attendu :
```
produits    image_produits        Total:284  Cloudinary:284  Local:0   ✅
produits    image_produits1       Total:254  Cloudinary:254  Local:0   ✅
...
```

**Log :** `migration/verification.log`

---

## Rollback (si problème)

```bash
./migration/step3_verify.sh --rollback
```

Cela restaure toutes les URLs originales (chemins locaux) depuis `backup_before_cloudinary.sql`.

---

## Points d'attention

### Mots de passe clients

Les mots de passe dans l'ancien projet sont **MD5** (PHP natif `md5()`). Le nouveau backend Laravel utilise **bcrypt**. 

Conséquence : les anciens clients ne peuvent pas se connecter directement.

Solutions :
1. **Recommandé** : Forcer un reset de mot de passe à la première connexion
2. **Technique** : Créer un middleware `LegacyPasswordUpgrade` qui vérifie MD5 puis re-hash en bcrypt

### AUTO_INCREMENT

Après import, MySQL auto-increment est basé sur les IDs existants. Cela est géré automatiquement.

### Fichiers manquants

Certains chemins en DB peuvent pointer vers des fichiers supprimés (entre 2021 et aujourd'hui). Ces entrées seront loguées comme `⚠️ manquants` mais n'empêchent pas la migration.

---

## Déploiement PlanetHoster (après migration)

1. Build frontend :
   ```bash
   cd frontend
   VITE_API_BASE_URL=https://votredomaine.com/api/v1 npm run build
   ```

2. Uploader `frontend/dist/` + `backend/` sur PlanetHoster via FTP/Git

3. `.env` production sur PlanetHoster :
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://votredomaine.com
   
   DB_CONNECTION=mysql
   DB_HOST=votre-host-planethoster
   DB_DATABASE=votre-db-planethoster
   DB_USERNAME=votre-user
   DB_PASSWORD=votre-password
   
   CLOUDINARY_CLOUD_NAME=votre-cloud
   CLOUDINARY_API_KEY=votre-key
   CLOUDINARY_API_SECRET=votre-secret
   
   FEDAPAY_MODE=live
   FEDAPAY_PUBLIC_KEY=pk_live_...
   FEDAPAY_SECRET_KEY=sk_live_...
   ```

4. Sur le serveur :
   ```bash
   php artisan migrate
   php artisan config:cache
   php artisan route:cache
   php artisan storage:link
   ```

5. Apache `.htaccess` (racine) pour servir Vue + Laravel sur un seul domaine :
   ```apache
   # frontend/dist/ → domaine racine
   # backend/public/ → /api/*
   RewriteEngine On
   RewriteRule ^api/(.*)$ backend/public/index.php [L,QSA]
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteRule ^(.*)$ index.html [L]
   ```
