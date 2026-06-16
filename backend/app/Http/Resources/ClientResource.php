<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_client'               => $this->id_client,
            'client_nom'              => $this->client_nom,
            'client_prenom'           => $this->client_prenom,
            'client_email'            => $this->client_email,
            'client_login'            => $this->client_login,
            'client_tel'              => $this->client_tel,
            'client_adresse'          => $this->client_adresse,
            'image_client'            => $this->image_client,
            'client_email_status'     => $this->client_email_status,
            'etape'                   => $this->etape,
            'client_jettons'          => $this->client_jettons,
            'produits_count'          => $this->when(isset($this->produits_count), $this->produits_count),
        ];
    }
}
