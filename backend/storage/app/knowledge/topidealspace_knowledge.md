# Base de Connaissances Officielle — TOPIDEALSPACE
## Document de référence pour l'assistant IA

> Mis à jour le 26/02/2026 — Extrait directement du code source

---

## 1. Présentation générale

**TOPIDEALSPACE** est une marketplace béninoise permettant aux particuliers et professionnels de :
- Publier et vendre des **produits** (annonces)
- Créer des espaces de **sponsoring** (publicités visibles sur la page d'accueil)
- Lire et commenter des **articles de blog**
- Ouvrir des **tickets de support**
- Recharger un **portefeuille de jetons** (monnaie interne) via Mobile Money

**Devise** : XOF (Franc CFA Ouest-Africain)
**Paiement** : FedaPay (MTN Mobile Money, Moov Money, etc.)
**Hébergement images** : Cloudinary

---

## 2. Inscription (User Flow complet)

**URL** : `/register`

### Étapes

1. L'utilisateur remplit le formulaire :
   - **Nom** (obligatoire)
   - **Prénom** (obligatoire)
   - **Email** (obligatoire, doit être un domaine valide : gmail.com, yahoo.fr, hotmail.com, etc.)
   - **Login / Pseudo** (obligatoire, unique, servira pour se connecter)
   - **Téléphone** (obligatoire)
   - **Mot de passe** (obligatoire, confirmé)

2. Après soumission, un **code OTP à 6 chiffres** est envoyé par email.

3. L'utilisateur est redirigé vers `/verify-otp` pour entrer le code.

4. Si le code est correct, le compte est activé et l'utilisateur est connecté automatiquement.

### Erreurs fréquentes inscription
- "Email déjà utilisé" → L'adresse est déjà associée à un compte. Aller sur `/login` ou `/forgot-password`.
- "Login déjà pris" → Choisir un autre pseudo/identifiant.
- "Email invalide" → Utiliser une adresse email réelle (pas de domaines fictifs).
- OTP non reçu → Vérifier le dossier spam. Attendre 2 minutes puis utiliser le bouton "Renvoyer".

---

## 3. Connexion (User Flow complet)

**URL** : `/login`

### Étapes pour les clients
1. Entrer l'identifiant (**email OU login**) + mot de passe.
2. Connexion directe si les informations sont correctes.
3. L'utilisateur est redirigé vers son dashboard ou la page précédente.

### Étapes pour les administrateurs
1. Entrer les identifiants admin.
2. Un **OTP email** est envoyé.
3. Entrer l'OTP sur `/verify-login-otp`.
4. Accès au panneau d'administration `/admin`.

### Erreurs fréquentes connexion
- "Identifiants incorrects" → Email/login ou mot de passe erroné.
- "Compte non vérifié" → L'email n'a pas été confirmé. Relancer la vérification.

---

## 4. Mot de passe oublié

**URL** : `/forgot-password`

1. Entrer l'adresse email enregistrée.
2. Un **OTP de réinitialisation** est envoyé par email.
3. Valider l'OTP sur `/reset-password`.
4. Entrer le nouveau mot de passe (avec confirmation).

### Erreurs fréquentes MDP
- "Email non trouvé" → L'adresse n'est associée à aucun compte. Vérifier l'orthographe.
- OTP expiré → Les OTP ont une durée de vie limitée. Recommencer depuis `/forgot-password`.

---

## 5. Profil utilisateur

**URL** : `/profile` (authentification requise)

### Ce qu'on peut modifier
- Prénom, nom, email, numéro de téléphone
- **Photo de profil** (upload vers Cloudinary — formats JPG, PNG, WEBP)
- Mot de passe (nécessite l'ancien mot de passe)

### Informations affichées
- Solde de **jetons clients** (`client_jettons`)
- Solde de **jetons sponsors** (`client_jettons_sponsor`)
- Login/pseudo

---

## 6. Tableau de bord client

**URL** : `/dashboard` (authentification requise)

Centre de contrôle personnel avec accès rapide à :
- Mes produits, mes sponsors, mes tickets de support
- Solde de jetons (clients + sponsors)
- Recharger mes jetons
- Historique des transactions

---

## 7. Système de jetons (crédits internes)

### Types de jetons
| Type | Usage | Colonne BDD |
|------|-------|-------------|
| Jetons clients | Publier un produit (1 jeton par publication) | `client_jettons` |
| Jetons sponsors | Publier un espace publicitaire | `client_jettons_sponsor` |

### Forfaits disponibles (prix réels)

#### Forfaits Jetons Clients
| Pack | Jetons | Prix |
|------|--------|------|
| Access | 5 jetons | 1 000 F CFA |
| Premium | 13 jetons | 2 000 F CFA |

#### Forfait Sponsor
| Pack | Jetons sponsor | Bonus | Prix |
|------|---------------|-------|------|
| Sponsor | 5 jetons sponsor | +13 jetons clients offerts | 3 500 F CFA |

#### Forfait Gratuit
- 1 crédit gratuit offert (0 F CFA - Forfait "Libre")

### Comment recharger ses jetons
**URL** : `/credit/recharge` (authentification requise)

1. Choisir un forfait.
2. Cliquer "Acheter" → redirection vers FedaPay.
3. Payer via MTN Mobile Money ou Moov Money.
4. Confirmation automatique → jetons crédités instantanément.
5. En cas de doute, vérifier dans `/dashboard`.

---

## 8. Produits

### Publier un produit
**URL** : `/publish/produit` (authentification + 1 jeton client requis)

#### Formulaire requis
| Champ | Obligatoire | Exemple |
|-------|-------------|--------|
| Nom du produit | ✅ | "iPhone 14 Pro Max" |
| Prix (FCFA) | ✅ | 850000 |
| Catégorie / Sous-catégorie | ✅ | Choisir dans la liste |
| Description | ✅ | Détails détaillés |
| Images | Recommandé | Upload Cloudinary |

#### Processus après soumission
1. Le produit est créé avec l'état **"en attente"**.
2. **1 jeton client** est déduit automatiquement.
3. L'admin valide → visible sur `/products`.
4. Délai habituel : **24 à 48 heures**.

#### Erreur "jetons insuffisants"
→ Aller sur `/credit/recharge` pour recharger avant de publier.

### Mes produits
**URL** : `/suivi/produits` (authentification requise)

### États d'un produit
| État | Signification | Couleur |
|------|---------------|---------|
| En attente | Soumis, en examen par un admin | Orange |
| Validé | Visible publiquement sur `/products` | Vert |
| Rejeté | Refusé, la raison est affichée | Rouge |

### Modifier un produit
- Aller dans `/suivi/produits` → cliquer → "Modifier".
- **Attention** : un produit validé repassera "en attente" après modification.
- La modification ne déduit **pas** de jetons supplémentaires.

### Voir les produits publics
- Liste : `/products` (filtrage par catégorie disponible)
- Détail : `/products/:id`

---

## 9. Sponsors (Espaces publicitaires)

### Publier un sponsor
**URL** : `/publish/sponsor` (authentification + jetons sponsor requis)

#### Formulaire
| Champ | Obligatoire |
|-------|-------------|
| Titre du sponsor | ✅ |
| Description | ✅ |
| Lien URL de destination | ✅ |
| Image / Bannière | ✅ |

### Mes sponsors
**URL** : `/suivi/sponsors` (authentification requise)

### États d'un sponsor
| État | Signification |
|------|---------------|
| En attente | En examen |
| Validé | Visible sur la page d'accueil (carrousel) |
| Rejeté | Refusé avec raison |

---

## 10. Tickets de support

### Créer un ticket
**URLs** : `/tickets/create` ou `/suivi/tickets/create` (authentification requise)

- Décrire le problème (catégorie, objet, description détaillée).

### Suivi des tickets
**URL** : `/suivi/tickets` (authentification requise)

### États des tickets
| État | Signification |
|------|---------------|
| Ouvert | En attente de traitement |
| En cours | Pris en charge par l'équipe |
| Résolu | Solution apportée |
| Fermé | Clôturé |

### Détail d'un ticket
**URL** : `/suivi/tickets/:id` — Conversation avec l'équipe support.

---

## 11. Blog

**URL** : `/blog`

- Articles publiés par l'équipe TOPIDEALSPACE, filtrés par catégorie.
- Articles populaires et récents mis en avant.
- Détail d'un article : `/blog/:id`
- **Commenter** : réservé aux utilisateurs connectés.
- Publication d'articles réservée aux administrateurs uniquement.

---

## 12. Paiement FedaPay — Flow complet

### Méthodes acceptées
- MTN Mobile Money (Bénin)
- Moov Money (Bénin)

### Flow de paiement
1. L'utilisateur choisit un forfait sur `/credit/recharge`.
2. Le backend initie une transaction FedaPay.
3. L'utilisateur est redirigé vers la page FedaPay.
4. Il choisit son opérateur (MTN/Moov) et confirme sur son téléphone.
5. FedaPay envoie un webhook au backend.
6. Le backend crédite automatiquement les jetons.
7. L'utilisateur est redirigé vers `/payment/callback` avec statut.

### Problèmes paiement courants
| Problème | Solution |
|---------|----------|
| Jetons non reçus après paiement | Attendre 15 min. Si toujours absent, ouvrir un ticket avec référence de transaction et capture SMS |
| Erreur "Transaction échouée" | Solde Mobile Money insuffisant ou refus de confirmation |
| Redirection FedaPay bloquée | Désactiver le bloqueur de publicités |

---

## 13. Toutes les pages — Navigation complète

### Pages publiques (sans connexion)
| Page | URL | Description |
|------|-----|-------------|
| Accueil | `/` | Sponsors, catégories, produits récents |
| Produits | `/products` | Catalogue des produits validés |
| Détail produit | `/products/:id` | Fiche complète d'un produit |
| Blog | `/blog` | Articles de la plateforme |
| Article blog | `/blog/:id` | Lecture d'un article + commentaires |
| À propos | `/about` | Présentation de TOPIDEALSPACE |
| Aide IA | `/help` | Assistant virtuel IA (vous êtes ici) |
| Connexion | `/login` | Se connecter |
| Inscription | `/register` | Créer un compte |
| Mot de passe oublié | `/forgot-password` | Récupérer son accès |
| Réinitialiser MDP | `/reset-password` | Nouveau mot de passe |

### Pages client (connexion requise)
| Page | URL | Description |
|------|-----|-------------|
| Tableau de bord | `/dashboard` | Centre de contrôle |
| Profil | `/profile` | Gérer ses informations |
| Mes produits | `/suivi/produits` | Voir/gérer ses annonces |
| Publier produit | `/publish/produit` | Créer une nouvelle annonce |
| Mes sponsors | `/suivi/sponsors` | Gérer ses espaces publicitaires |
| Créer sponsor | `/publish/sponsor` | Nouveau sponsor |
| Mes tickets | `/suivi/tickets` | Support client |
| Créer ticket | `/suivi/tickets/create` | Nouveau ticket |
| Recharger jetons | `/credit/recharge` | Acheter des crédits |
| Retour paiement | `/payment/callback` | Page de confirmation FedaPay |

### Pages admin (admins uniquement)
| Page | URL | Description |
|------|-----|-------------|
| Dashboard | `/admin/dashboard` | Statistiques globales |
| Produits | `/admin/products` | Valider/rejeter les produits |
| Sponsors | `/admin/sponsors` | Gérer les espaces publicitaires |
| Catégories | `/admin/categories` | Gérer les catégories |
| Clients | `/admin/clients` | Gérer les comptes clients |
| Blog | `/admin/blog` | Publier des articles |
| Tickets | `/admin/tickets` | Traiter le support |

---

## 14. FAQ — Questions fréquentes

**Q: Je n'arrive pas à me connecter**
→ Vérifier l'identifiant (email OU login) et le mot de passe. Utiliser `/forgot-password` si le mot de passe est oublié.

**Q: Je n'ai pas reçu l'OTP**
→ Vérifier le dossier spam. Attendre 2-3 minutes. Utiliser "Renvoyer l'OTP". Si toujours absent après 5 minutes, contacter le support.

**Q: Mon produit est "en attente" depuis longtemps**
→ Délai habituel : 24-48h. Si cela dépasse 72h (jours ouvrables), ouvrir un ticket.

**Q: Mon produit a été rejeté**
→ La raison est visible dans `/suivi/produits`. Corriger les problèmes et republier (coûte 1 jeton supplémentaire).

**Q: Combien coûte la publication d'un produit ?**
→ 1 jeton client. Pack Access : 5 jetons = 1 000 F CFA sur `/credit/recharge`.

**Q: Le paiement a été accepté mais les jetons ne sont pas arrivés**
→ Attendre 5 à 15 minutes. Si après 20 minutes rien, contacter le support avec : référence de transaction, montant, capture SMS de confirmation.

**Q: Quelles méthodes de paiement sont acceptées ?**
→ Mobile Money : MTN Bénin, Moov Bénin via FedaPay.

**Q: Comment changer mon mot de passe ?**
→ Connecté : `/profile` → onglet "Mot de passe". Déconnecté : `/forgot-password`.

**Q: Comment supprimer mon compte ?**
→ Ouvrir un ticket de support depuis `/tickets/create`.

**Q: Puis-je publier des articles sur le blog ?**
→ Non, la publication est réservée aux administrateurs. Vous pouvez uniquement commenter.

**Q: Mon sponsor n'apparaît pas sur la page d'accueil**
→ Il est probablement en attente de validation. Vérifier le statut sur `/suivi/sponsors`.

**Q: J'ai oublié mon login/pseudo**
→ Utiliser l'email pour se connecter. Le login est visible dans `/profile`.

---

## 15. Erreurs techniques courantes

| Message d'erreur | Cause | Solution |
|-----------------|-------|----------|
| "Vous n'avez pas assez de jetons" | Solde insuffisant | Recharger sur `/credit/recharge` |
| "Email invalide" | Email mal saisi | Utiliser un vrai domaine email |
| "Identifiants incorrects" | Email/login ou MDP erroné | Vérifier, ou utiliser `/forgot-password` |
| "Trop de tentatives" | Rate limiting | Attendre 1 minute |
| "Session expirée" | Token expiré | Se reconnecter sur `/login` |
| Error 500 | Erreur serveur | Attendre quelques minutes. Si persistant, ouvrir un ticket |

---

## 16. Informations techniques

- **Frontend** : Vue.js 3 + Vite + Tailwind CSS (SPA)
- **Backend** : Laravel 12 (PHP 8.2+) — API REST
- **Base de données** : MySQL
- **Authentification** : Laravel Sanctum (tokens Bearer) + OTP email
- **Images** : Cloudinary (CDN mondial)
- **Paiement** : FedaPay en XOF
- **Cache** : Redis / Base de données

---

## 17. Ce que l'assistant NE peut PAS faire

- Accéder aux données personnelles des utilisateurs
- Effectuer des paiements ou transactions
- Modifier le compte d'un utilisateur
- Accéder aux tickets ou messages privés
- Effectuer des actions sur la plateforme à la place de l'utilisateur

Pour toute action nécessitant un accès administratif, contacter le support via `/tickets/create`.

---

*Document de référence pour l'assistant IA TOPIDEALSPACE — mis à jour le 26/02/2026*
