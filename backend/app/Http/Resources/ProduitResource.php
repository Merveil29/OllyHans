<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProduitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_produits'          => $this->id_produits,
            'nom_produits'         => $this->nom_produits,
            'prix_produits'        => $this->prix_produits,
            'description_produits' => $this->description_produits,
            'images'               => [
                'main' => $this->image_produits,
                'img1' => $this->image_produits1,
                'img2' => $this->image_produits2,
            ],
            'categorie' => $this->whenLoaded('categorie', fn() => [
                'id_categorie'  => $this->categorie->id_categorie,
                'nom_categorie' => $this->categorie->nom_categorie,
            ]),
            'dateSaisie'  => $this->dateSaisie,
            'state'       => $this->whenLoaded('state', fn() => [
                'id_state' => $this->state->id_state,
                'title'    => $this->state->title,
            ]),
            'client'      => $this->whenLoaded('client', fn() => [
                'id_client' => $this->client->id_client,
                'nom'       => $this->client->client_nom . ' ' . $this->client->client_prenom,
                'email'     => $this->client->client_email,
                'telephone' => $this->client->client_tel,
            ]),
            'validated_by' => $this->whenLoaded('user', fn() => $this->user ? [
                'id_user' => $this->user->id_user,
                'nom'     => $this->user->user_nom . ' ' . $this->user->user_prenom,
            ] : null),
            'vues_count' => $this->when(isset($this->vues_count), $this->vues_count),
            'can_edit'   => $this->when(
                $request->user() && !($request->user() instanceof \App\Models\User),
                fn() => $this->id_state !== 2
            ),
        ];
    }
}
