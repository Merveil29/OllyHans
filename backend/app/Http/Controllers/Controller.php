<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "TOPIDEALSPACE API",
    description: "API REST de la marketplace TOPIDEALSPACE. Authentification via Laravel Sanctum (Bearer Token). Connectez-vous via **POST /auth/login**, copiez le token retourné, puis cliquez sur **Authorize** en haut à droite.",
    contact: new OA\Contact(email: "contact@topidealspace.com")
)]
#[OA\Server(url: "http://localhost:8000/api/v1", description: "Serveur local (dev)")]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "Token",
    description: "Token Sanctum. Obtenez-le via POST /auth/login puis cliquez sur Authorize."
)]
#[OA\Tag(name: "Authentification", description: "Inscription, connexion, OTP, réinitialisation mot de passe")]
#[OA\Tag(name: "Profil", description: "Profil de l'utilisateur connecté")]
#[OA\Tag(name: "Produits Publics", description: "Consultation publique des produits")]
#[OA\Tag(name: "Produits Client", description: "CRUD produits du client connecté")]
#[OA\Tag(name: "Newsletter", description: "Abonnement / désabonnement newsletter")]
#[OA\Tag(name: "Assistant IA", description: "Chat IA assisté (SSE streaming)")]
#[OA\Tag(name: "Admin Dashboard", description: "Tableau de bord admin")]
#[OA\Tag(name: "Admin Produits", description: "Gestion des annonces / produits")]
#[OA\Tag(name: "Admin Clients", description: "Gestion des clients")]
#[OA\Tag(name: "Admin Catégories", description: "Gestion des catégories")]
#[OA\Tag(name: "Admin Sous-Catégories", description: "Gestion des sous-catégories")]
#[OA\Tag(name: "Admin Utilisateurs", description: "Gestion des administrateurs")]
#[OA\Tag(name: "Admin Profil", description: "Profil administrateur")]
#[OA\Tag(name: "Admin Notifications", description: "Notifications agrégées (produits en attente)")]
#[OA\Tag(name: "Admin Newsletter", description: "Gestion abonnés newsletter")]

// ============================================================================
// SCHEMAS
// ============================================================================

#[OA\Schema(
    schema: "Post",
    type: "object",
    properties: [
        new OA\Property(property: "idposts", type: "integer", example: 1),
        new OA\Property(property: "titre", type: "string", example: "Mon article"),
        new OA\Property(property: "contenu", type: "string", example: "Contenu de l'article..."),
        new OA\Property(property: "categorie", type: "string", example: "Actualités"),
        new OA\Property(property: "dateposts", type: "string", format: "date", example: "2024-01-15"),
        new OA\Property(property: "nbvue", type: "integer", example: 150),
        new OA\Property(property: "image1", type: "string", nullable: true, example: "https://cloudinary.com/image1.jpg"),
        new OA\Property(property: "image2", type: "string", nullable: true, example: "https://cloudinary.com/image2.jpg"),
    ]
)]

#[OA\Schema(
    schema: "BlogPost",
    type: "object",
    description: "Alias de Post pour compatibilité",
    properties: [
        new OA\Property(property: "idposts", type: "integer", example: 1),
        new OA\Property(property: "titre", type: "string", example: "Mon article"),
        new OA\Property(property: "contenu", type: "string", example: "Contenu de l'article..."),
        new OA\Property(property: "categorie", type: "string", example: "Actualités"),
        new OA\Property(property: "dateposts", type: "string", format: "date", example: "2024-01-15"),
        new OA\Property(property: "nbvue", type: "integer", example: 150),
        new OA\Property(property: "image1", type: "string", nullable: true),
        new OA\Property(property: "image2", type: "string", nullable: true),
        new OA\Property(property: "reponses", type: "array", items: new OA\Items(ref: "#/components/schemas/ReponseBlog"), description: "Commentaires associés"),
    ]
)]

#[OA\Schema(
    schema: "Produit",
    type: "object",
    properties: [
        new OA\Property(property: "id_produits", type: "integer", example: 1),
        new OA\Property(property: "nom_produits", type: "string", example: "iPhone 15 Pro"),
        new OA\Property(property: "prix_produits", type: "number", format: "float", example: 599000),
        new OA\Property(property: "description_produits", type: "string", example: "Smartphone haut de gamme..."),
        new OA\Property(property: "image_produits", type: "string", example: "https://cloudinary.com/iphone.jpg"),
        new OA\Property(property: "image_produits1", type: "string", nullable: true),
        new OA\Property(property: "image_produits2", type: "string", nullable: true),
        new OA\Property(property: "id_sous_categorie", type: "integer", example: 5),
        new OA\Property(property: "id_client", type: "integer", example: 1),
        new OA\Property(property: "id_user", type: "integer", nullable: true),
        new OA\Property(property: "id_state", type: "integer", example: 1),
        new OA\Property(property: "dateSaisie", type: "string", format: "date-time"),
    ]
)]

#[OA\Schema(
    schema: "Sponsor",
    type: "object",
    properties: [
        new OA\Property(property: "id_sponsor", type: "integer", example: 1),
        new OA\Property(property: "nom_sponsor", type: "string", example: "Ma Boutique"),
        new OA\Property(property: "image_sponsor", type: "string", example: "https://cloudinary.com/sponsor.jpg"),
        new OA\Property(property: "tel_sponsor", type: "string", example: "+229 97000000"),
        new OA\Property(property: "lien_sponsor", type: "string", example: "https://maboutique.com"),
        new OA\Property(property: "id_user", type: "integer", nullable: true),
        new OA\Property(property: "id_client", type: "integer", example: 1),
        new OA\Property(property: "id_state", type: "integer", example: 1),
    ]
)]

#[OA\Schema(
    schema: "Transaction",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "id_client", type: "integer", example: 1),
        new OA\Property(property: "fedapay_transaction_id", type: "string", example: "txn_123456789"),
        new OA\Property(property: "type", type: "string", enum: ["standard", "sponsor"], example: "standard"),
        new OA\Property(property: "token_quantity", type: "integer", example: 10),
        new OA\Property(property: "amount", type: "number", format: "float", example: 5000),
        new OA\Property(property: "currency", type: "string", example: "XOF"),
        new OA\Property(property: "status", type: "string", enum: ["pending", "approved", "declined", "canceled"], example: "approved"),
        new OA\Property(property: "payment_method", type: "string", nullable: true, example: "mtn"),
        new OA\Property(property: "description", type: "string", nullable: true),
        new OA\Property(property: "paid_at", type: "string", format: "date-time", nullable: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]

#[OA\Schema(
    schema: "Ticket",
    type: "object",
    properties: [
        new OA\Property(property: "id_tickets", type: "integer", example: 1),
        new OA\Property(property: "nom_tickets", type: "string", example: "Problème de connexion"),
        new OA\Property(property: "id_client", type: "integer", example: 1),
        new OA\Property(property: "id_user", type: "integer", nullable: true),
    ]
)]

#[OA\Schema(
    schema: "Categorie",
    type: "object",
    properties: [
        new OA\Property(property: "id_categorie", type: "integer", example: 1),
        new OA\Property(property: "nom_categorie", type: "string", example: "Électronique"),
        new OA\Property(property: "image_categorie", type: "string", nullable: true, example: "https://cloudinary.com/cat.jpg"),
    ]
)]

#[OA\Schema(
    schema: "SousCategorie",
    type: "object",
    properties: [
        new OA\Property(property: "id_sous_categorie", type: "integer", example: 1),
        new OA\Property(property: "libelle_sous_categorie", type: "string", example: "Smartphones"),
        new OA\Property(property: "id_categorie", type: "integer", example: 1),
    ]
)]

#[OA\Schema(
    schema: "Client",
    type: "object",
    properties: [
        new OA\Property(property: "id_client", type: "integer", example: 1),
        new OA\Property(property: "nom_client", type: "string", example: "Jean Dupont"),
        new OA\Property(property: "email_client", type: "string", format: "email", example: "jean@example.com"),
        new OA\Property(property: "tel_client", type: "string", example: "+229 97000000"),
        new OA\Property(property: "adresse_client", type: "string", nullable: true),
        new OA\Property(property: "token_standard", type: "integer", example: 5),
        new OA\Property(property: "token_sponsor", type: "integer", example: 2),
        new OA\Property(property: "photo_client", type: "string", nullable: true),
        new OA\Property(property: "id_state", type: "integer", example: 1),
    ]
)]

#[OA\Schema(
    schema: "User",
    type: "object",
    properties: [
        new OA\Property(property: "id_user", type: "integer", example: 1),
        new OA\Property(property: "nom_user", type: "string", example: "Admin User"),
        new OA\Property(property: "email_user", type: "string", format: "email", example: "admin@topidealspace.com"),
        new OA\Property(property: "tel_user", type: "string", example: "+229 97000000"),
        new OA\Property(property: "photo_user", type: "string", nullable: true),
        new OA\Property(property: "role_id", type: "integer", example: 1),
    ]
)]

#[OA\Schema(
    schema: "Comment",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "idposts", type: "integer", example: 1),
        new OA\Property(property: "nom", type: "string", example: "Jean"),
        new OA\Property(property: "email", type: "string", format: "email"),
        new OA\Property(property: "commentaire", type: "string", example: "Super article!"),
        new OA\Property(property: "datecommentaire", type: "string", format: "date-time"),
    ]
)]

#[OA\Schema(
    schema: "PaginatedResponse",
    type: "object",
    properties: [
        new OA\Property(property: "current_page", type: "integer", example: 1),
        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object")),
        new OA\Property(property: "first_page_url", type: "string"),
        new OA\Property(property: "from", type: "integer", example: 1),
        new OA\Property(property: "last_page", type: "integer", example: 10),
        new OA\Property(property: "last_page_url", type: "string"),
        new OA\Property(property: "next_page_url", type: "string", nullable: true),
        new OA\Property(property: "path", type: "string"),
        new OA\Property(property: "per_page", type: "integer", example: 15),
        new OA\Property(property: "prev_page_url", type: "string", nullable: true),
        new OA\Property(property: "to", type: "integer", example: 15),
        new OA\Property(property: "total", type: "integer", example: 150),
    ]
)]

#[OA\Schema(
    schema: "SuccessResponse",
    type: "object",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: true),
        new OA\Property(property: "message", type: "string", example: "Opération réussie"),
        new OA\Property(property: "data", type: "object", nullable: true),
    ]
)]

#[OA\Schema(
    schema: "ErrorResponse",
    type: "object",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: false),
        new OA\Property(property: "message", type: "string", example: "Une erreur est survenue"),
        new OA\Property(property: "errors", type: "object", nullable: true),
    ]
)]

#[OA\Schema(
    schema: "ValidationError",
    type: "object",
    properties: [
        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
        new OA\Property(property: "errors", type: "object", example: ["email" => ["The email field is required."]]),
    ]
)]

#[OA\Schema(
    schema: "Demande",
    type: "object",
    properties: [
        new OA\Property(property: "id_demandes", type: "integer", example: 1),
        new OA\Property(property: "nom_demandes", type: "string", example: "Demande de support"),
        new OA\Property(property: "description_demandes", type: "string", example: "Description détaillée..."),
        new OA\Property(property: "id_tickets", type: "integer", example: 1),
        new OA\Property(property: "id_client", type: "integer", example: 1),
        new OA\Property(property: "id_user", type: "integer", nullable: true),
        new OA\Property(property: "id_statetickets", type: "integer", example: 1),
    ]
)]

#[OA\Schema(
    schema: "State",
    type: "object",
    properties: [
        new OA\Property(property: "id_state", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Actif"),
        new OA\Property(property: "autre", type: "string", nullable: true),
    ]
)]

#[OA\Schema(
    schema: "StateTicket",
    type: "object",
    properties: [
        new OA\Property(property: "id_statetickets", type: "integer", example: 1),
        new OA\Property(property: "nom_statetickets", type: "string", example: "En cours"),
        new OA\Property(property: "autre", type: "string", nullable: true),
    ]
)]

#[OA\Schema(
    schema: "ReponseBlog",
    type: "object",
    properties: [
        new OA\Property(property: "idreply", type: "integer", example: 1),
        new OA\Property(property: "commentaire", type: "string", example: "Super article!"),
        new OA\Property(property: "nomvisiteur", type: "string", example: "Jean"),
        new OA\Property(property: "email", type: "string", format: "email"),
        new OA\Property(property: "ordrereply", type: "integer", example: 1),
        new OA\Property(property: "idposts", type: "integer", example: 1),
        new OA\Property(property: "datereply", type: "string", format: "date-time"),
        new OA\Property(property: "reponsea", type: "integer", nullable: true),
        new OA\Property(property: "nivo", type: "integer", example: 0),
    ]
)]

abstract class Controller
{
    //
}
