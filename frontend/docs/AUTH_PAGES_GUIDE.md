# Frontend TOPIDEAL - Guide des Pages d'Authentification

## Vue d'ensemble

Les pages d'authentification ont été créées avec Vue 3, Tailwind CSS et Pinia, en respectant le design system défini.

## 🎨 Design System

- **Couleur primaire**: Bleu (#3b82f6 - primary-600)
- **Couleur secondaire**: Orange (#f97316 - secondary-600)
- **Polices**: Inter/Poppins
- **Responsive**: Mobile-first, optimisé pour tous les écrans

## 📁 Structure des fichiers

```
frontend/
├── src/
│   ├── views/
│   │   ├── Home.vue                    # Page d'accueil
│   │   ├── NotFound.vue                # Page 404
│   │   └── auth/
│   │       ├── Register.vue            # Inscription
│   │       ├── VerifyOtp.vue           # Vérification OTP
│   │       ├── Login.vue               # Connexion
│   │       └── ForgotPassword.vue      # Mot de passe oublié
│   ├── stores/
│   │   └── auth.js                     # Store Pinia pour auth
│   ├── api/
│   │   └── modules/
│   │       └── auth.js                 # API auth
│   └── router/
│       └── index.js                    # Configuration des routes
```

## 🔐 Flux d'authentification

### 1. Inscription (Register.vue)
- **Route**: `/register`
- **Champs**:
  - Nom et Prénom (2 colonnes sur desktop)
  - Email (unique)
  - Login (unique, validation en temps réel)
  - Téléphone
  - Adresse
  - Mot de passe (avec toggle de visibilité)
  - Confirmation mot de passe
- **Fonctionnalités**:
  - Validation en temps réel (email, login)
  - Affichage des erreurs API
  - Redirection vers `/verify-otp?email=xxx` après succès
  - Bouton de connexion si déjà inscrit

### 2. Vérification OTP (VerifyOtp.vue)
- **Route**: `/verify-otp?email=xxx`
- **Fonctionnalités**:
  - 6 inputs pour le code OTP
  - Auto-focus et auto-advance entre les champs
  - Support du copier-coller (6 chiffres)
  - Timer de 10 minutes (600s)
  - Bouton "Renvoyer le code" après expiration
  - Auto-login après vérification réussie
  - Redirection vers `/` après succès

### 3. Connexion (Login.vue)
- **Route**: `/login`
- **Champs**:
  - Email ou Login (identifier)
  - Mot de passe (avec toggle de visibilité)
  - Case "Se souvenir de moi"
- **Fonctionnalités**:
  - Lien "Mot de passe oublié"
  - Lien "Créer un compte"
  - Redirection avec query param `?redirect=/path`
  - Messages d'erreur spécifiques (401, 422)

### 4. Mot de passe oublié (ForgotPassword.vue)
- **Route**: `/forgot-password`
- **Champs**:
  - Email
- **Fonctionnalités**:
  - Envoi d'email de réinitialisation
  - Confirmation visuelle après envoi
  - Option de renvoi
  - Retour à la connexion

## 🛣️ Routes et Guards

### Routes publiques
- `/` - Accueil
- `/register` - Inscription (guest only)
- `/verify-otp` - Vérification OTP (guest only)
- `/login` - Connexion (guest only)
- `/forgot-password` - Mot de passe oublié (guest only)

### Navigation Guards
- **requiresAuth**: Redirige vers `/login` si non authentifié
- **requiresGuest**: Redirige vers `/` si déjà authentifié

## 🎯 API Endpoints

```javascript
// Inscription (envoie OTP par email)
POST /api/v1/register
Body: { nom, prenom, email, login, telephone, adresse, password, password_confirmation }
Response: { success: true, message: "..." }

// Vérification OTP (crée le compte)
POST /api/v1/verify-otp
Body: { email, otp }
Response: { data: { token, client: {...} } }

// Connexion
POST /api/v1/login
Body: { identifier, password, remember }
Response: { data: { token, user: {...} } }

// Vérifier email unique
GET /api/v1/check-email?email=xxx
Response: { available: true/false }

// Vérifier login unique
GET /api/v1/check-login?login=xxx
Response: { available: true/false }

// Déconnexion
POST /api/v1/logout
Headers: { Authorization: "Bearer token" }
```

## 💾 Store Pinia (auth.js)

### State
```javascript
{
  user: null,              // Données utilisateur
  token: null,             // Token JWT
  isAuthenticated: false,  // Statut connexion
  isAdmin: false,          // Rôle admin
  loading: false           // État chargement
}
```

### Actions principales
- `login(credentials)` - Connexion
- `register(data)` - Inscription
- `verifyOtp(email, otp)` - Vérification OTP
- `logout()` - Déconnexion
- `checkEmail(email)` - Vérifier email
- `checkLogin(login)` - Vérifier login
- `initializeAuth()` - Initialiser depuis localStorage

### Getters
- `fullName` - Nom complet utilisateur
- `tokenBalance` - Solde jetons
- `sponsorTokens` - Jetons sponsor

## 🎨 Composants visuels

### Boutons
```html
<!-- Bouton primaire -->
<button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-3 rounded-md">
  Connexion
</button>

<!-- Bouton secondaire -->
<button class="border border-primary-600 text-primary-600 hover:bg-primary-50 px-4 py-3 rounded-md">
  S'inscrire
</button>
```

### Inputs
```html
<input 
  class="block w-full px-3 py-2.5 border border-gray-300 rounded-md 
         focus:ring-primary-500 focus:border-primary-500" 
  type="text"
/>
```

### Alertes
```html
<!-- Succès -->
<div class="bg-success-50 border-l-4 border-success-500 p-4 rounded">
  <p class="text-sm text-success-700">Message de succès</p>
</div>

<!-- Erreur -->
<div class="bg-error-50 border-l-4 border-error-500 p-4 rounded">
  <p class="text-sm text-error-700">Message d'erreur</p>
</div>
```

## 📱 Responsive Design

### Breakpoints Tailwind
- `sm`: 640px
- `md`: 768px
- `lg`: 1024px
- `xl`: 1280px

### Exemples
```html
<!-- 1 colonne sur mobile, 2 sur desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

<!-- Texte responsive -->
<h1 class="text-3xl sm:text-4xl md:text-5xl">

<!-- Padding responsive -->
<div class="px-4 sm:px-6 lg:px-8">
```

## 🚀 Lancer le projet

```bash
# Installation
cd frontend
npm install

# Développement
npm run dev

# Build production
npm run build

# Preview production
npm run preview
```

## 🔧 Configuration

### Variables d'environnement (.env)
```env
VITE_API_BASE_URL=http://localhost:8000/api/v1
```

### Tailwind Configuration
Les couleurs du design system sont configurées dans `tailwind.config.js`:
```javascript
colors: {
  primary: colors.blue,
  secondary: colors.orange,
  success: colors.green,
  error: colors.red,
}
```

## ✅ Checklist de test

- [ ] Register: Formulaire complet, validation, erreurs
- [ ] Register: Responsive sur mobile/tablette/desktop
- [ ] VerifyOtp: Input 6 chiffres, auto-focus, paste
- [ ] VerifyOtp: Timer 10min, resend après expiration
- [ ] Login: Email ou login, toggle password
- [ ] Login: Redirection avec query param
- [ ] Navigation guards: Redirect auth/guest
- [ ] localStorage: Token et user persistés
- [ ] Déconnexion: Clear state et redirect
- [ ] 404: Page personnalisée

## 🐛 Debug

### Vérifier l'état de l'auth
```javascript
// Dans Vue DevTools
const authStore = useAuthStore()
console.log(authStore.isAuthenticated)
console.log(authStore.user)
console.log(authStore.token)
```

### Vérifier localStorage
```javascript
console.log(localStorage.getItem('token'))
console.log(localStorage.getItem('user'))
```

### Tester les guards
```javascript
// Dans router/index.js, ajouter des logs
router.beforeEach((to, from, next) => {
  console.log('Navigation vers:', to.name)
  console.log('Auth:', authStore.isAuthenticated)
  // ...
})
```

## 📚 Ressources

- [Vue 3 Documentation](https://vuejs.org/)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Vue Router](https://router.vuejs.org/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Design System TOPIDEAL](../DOCUMENTATION/05_DESIGN_SYSTEM_UX_UI.md)

## 🤝 Contribution

Pour ajouter de nouvelles pages d'authentification:

1. Créer le composant dans `src/views/auth/`
2. Ajouter la route dans `router/index.js`
3. Configurer les guards (`requiresAuth` ou `requiresGuest`)
4. Utiliser le store `useAuthStore()` pour l'état
5. Suivre le design system (couleurs, typographie, spacing)
6. Tester la responsivité sur tous les écrans

---

**Auteur**: TOPIDEAL Development Team  
**Date**: 2024  
**Version**: 1.0.0
