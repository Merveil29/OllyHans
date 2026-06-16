# TEST RAPIDE DES PAGES D'AUTHENTIFICATION

## ✅ Serveur lancé
Le serveur de développement tourne sur: **http://localhost:5173**

## 📝 URLs à tester

### 1. Page d'accueil
```
http://localhost:5173/
```
**Attendu**:
- Header avec logo TOPIDEAL
- Navigation (Accueil, Produits, À propos, Contact)
- Boutons "Connexion" et "S'inscrire" (si non connecté)
- Hero section avec titre "Bienvenue sur TOPIDEAL"
- 3 cartes features (Qualité, Livraison, Paiement)
- Footer complet

---

### 2. Page d'inscription
```
http://localhost:5173/register
```
**Attendu**:
- Logo cliquable (retour accueil)
- Titre "Créez votre compte"
- 8 champs de formulaire:
  1. Nom
  2. Prénom
  3. Email
  4. Login
  5. Téléphone
  6. Adresse
  7. Mot de passe (avec icône œil pour afficher/masquer)
  8. Confirmer mot de passe
- Bouton "S'inscrire" (bleu primary)
- Lien "Déjà inscrit ? Connectez-vous"
- Responsive: 2 colonnes pour Nom/Prénom sur desktop

**Actions à tester**:
1. Entrer un email déjà utilisé → Vérifier le message d'erreur
2. Entrer un login déjà utilisé → Vérifier le message d'erreur
3. Cliquer sur l'œil → Mot de passe visible/caché
4. Soumettre avec mots de passe différents → Erreur
5. Soumettre formulaire valide → Redirection vers /verify-otp?email=xxx

---

### 3. Page de vérification OTP
```
http://localhost:5173/verify-otp?email=test@example.com
```
**Attendu**:
- Logo cliquable
- Titre "Vérifiez votre adresse email"
- Affichage de l'email
- 6 champs pour le code OTP (1 chiffre par champ)
- Timer "10:00" qui décrémente
- Message "Code expiré" après 10 minutes
- Bouton "Renvoyer le code" (si expiré)
- Bouton "Vérifier" (bleu primary)
- Lien "Retour à l'accueil"

**Actions à tester**:
1. Entrer un chiffre → Auto-focus sur le champ suivant
2. Appuyer sur Backspace → Retour au champ précédent
3. Copier-coller "123456" → Remplissage automatique des 6 champs
4. Attendre 10 minutes → Timer expire, bouton "Renvoyer"
5. Entrer code valide (depuis email) → Auto-login + redirection vers /

---

### 4. Page de connexion
```
http://localhost:5173/login
```
**Attendu**:
- Logo cliquable
- Titre "Bon retour !"
- 2 champs:
  1. Email ou nom d'utilisateur
  2. Mot de passe (avec icône œil)
- Case à cocher "Se souvenir de moi"
- Lien "Mot de passe oublié ?"
- Bouton "Se connecter" (bleu primary)
- Séparateur "Nouveau sur TOPIDEAL ?"
- Bouton "Créer un compte" (bordure bleu)
- Lien "Retour à l'accueil"

**Actions à tester**:
1. Entrer email/login invalide → Erreur "Email/Login ou mot de passe incorrect"
2. Entrer mot de passe incorrect → Erreur 401
3. Cliquer sur "Mot de passe oublié" → Redirection vers /forgot-password
4. Cliquer sur "Créer un compte" → Redirection vers /register
5. Login valide → Redirection vers / (ou query param ?redirect=/path)

---

### 5. Page mot de passe oublié
```
http://localhost:5173/forgot-password
```
**Attendu**:
- Logo cliquable
- Titre "Mot de passe oublié ?"
- Sous-titre "Pas de souci, nous vous aiderons..."
- Champ email
- Bouton "Envoyer le lien de réinitialisation" (bleu primary)
- Séparateur "Ou"
- Bouton "Retour à la connexion"
- Lien "Retour à l'accueil"

**Actions à tester**:
1. Entrer email → Cliquer "Envoyer"
2. Vérifier icône de succès + message "Email envoyé !"
3. Cliquer "Renvoyer l'email" → Nouveau envoi
4. Cliquer "Retour à la connexion" → Redirection vers /login

---

### 6. Page 404
```
http://localhost:5173/page-inexistante
```
**Attendu**:
- Icône SVG triste
- Titre "404"
- Message "Page non trouvée"
- Bouton "Retour à l'accueil" (bleu primary)
- Bouton "Retour" (blanc avec bordure)
- Logo en bas (opacité 50%)

**Actions à tester**:
1. Cliquer "Retour à l'accueil" → /
2. Cliquer "Retour" → Page précédente (history.back())

---

## 🎯 Tests de navigation guards

### Test 1: Redirection si non authentifié
```
1. Se déconnecter (si connecté)
2. Tenter d'accéder à /profile (route protégée, à créer)
3. Attendu: Redirection vers /login?redirect=/profile
```

### Test 2: Redirection si déjà authentifié
```
1. Se connecter
2. Tenter d'accéder à /login
3. Attendu: Redirection vers /
```

---

## 📱 Tests responsive

### Mobile (< 768px)
- [ ] Header: Menu hamburger (à implémenter)
- [ ] Register: Champs en 1 colonne
- [ ] Login: Champs en 1 colonne
- [ ] Home: Features en 1 colonne
- [ ] Footer: En 1 colonne

### Tablette (768px - 1024px)
- [ ] Register: Nom/Prénom en 2 colonnes
- [ ] Home: Features en 2 colonnes
- [ ] Footer: En 2 colonnes

### Desktop (> 1024px)
- [ ] Register: Nom/Prénom en 2 colonnes
- [ ] Home: Features en 3 colonnes
- [ ] Footer: En 4 colonnes
- [ ] Padding augmenté (px-8)

---

## 🔍 Tests de validation

### Register
- [ ] Email vide → "Ce champ est requis"
- [ ] Email invalide → "Email invalide"
- [ ] Email déjà utilisé → "Cet email est déjà utilisé"
- [ ] Login déjà utilisé → "Ce login est déjà pris"
- [ ] Mots de passe différents → "Les mots de passe ne correspondent pas"
- [ ] Mot de passe trop court → "Le mot de passe doit contenir au moins X caractères"

### Login
- [ ] Email/login vide → "Ce champ est requis"
- [ ] Mot de passe vide → "Ce champ est requis"
- [ ] Credentials invalides → "Email/Login ou mot de passe incorrect"

### VerifyOtp
- [ ] Code incomplet → Bouton "Vérifier" désactivé
- [ ] Code invalide → "Code OTP invalide"
- [ ] Code expiré → "Code expiré, veuillez en demander un nouveau"

---

## 🎨 Tests de design system

### Couleurs
- [ ] Boutons primaires: bg-primary-600 (#3b82f6)
- [ ] Boutons hover: bg-primary-700
- [ ] Liens: text-primary-600
- [ ] Accents: text-secondary-600 (#f97316)
- [ ] Succès: bg-success-50, border-success-500
- [ ] Erreur: bg-error-50, border-error-500

### Typographie
- [ ] Titres: font-bold, text-3xl
- [ ] Corps: text-sm ou text-base
- [ ] Labels: font-medium, text-gray-700

### Composants
- [ ] Inputs: border-gray-300, rounded-md
- [ ] Inputs focus: ring-primary-500, border-primary-500
- [ ] Cards: bg-white, rounded-lg, shadow-xl
- [ ] Boutons: rounded-md, shadow-sm, transition-colors

---

## 🚀 Commandes utiles

### Lancer les serveurs
```bash
# Frontend
cd frontend
npm run dev

# Backend
cd backend
php artisan serve
```

### Vérifier les erreurs
```bash
# Frontend
cd frontend
npm run lint

# Backend
cd backend
php artisan test
```

### Build production
```bash
cd frontend
npm run build
npm run preview
```

---

## 📊 Checklist finale

### Fonctionnalités
- [ ] Inscription fonctionnelle
- [ ] OTP envoyé par email
- [ ] Vérification OTP fonctionnelle
- [ ] Auto-login après OTP
- [ ] Connexion fonctionnelle
- [ ] Déconnexion fonctionnelle
- [ ] Navigation guards opérationnels
- [ ] localStorage persisté

### Design
- [ ] Toutes les pages responsive
- [ ] Couleurs du design system respectées
- [ ] Logos affichés correctement
- [ ] Animations smooth
- [ ] Loading states présents
- [ ] Messages erreur/succès clairs

### Performance
- [ ] Temps de chargement < 2s
- [ ] Images optimisées
- [ ] Lazy loading activé
- [ ] Pas de memory leaks

---

**Bon test ! 🎉**
