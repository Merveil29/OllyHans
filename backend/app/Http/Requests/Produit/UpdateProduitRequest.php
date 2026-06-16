<?php

namespace App\Http\Requests\Produit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_produits'         => 'sometimes|string|max:255',
            'prix_produits'        => 'sometimes|numeric|min:0|max:999999999999',
            'description_produits' => 'sometimes|string',
            'id_categorie'         => 'sometimes|integer|exists:categorie,id_categorie',
            'image_produits'       => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'image_produits1'      => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'image_produits2'      => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'id_categorie.exists' => 'La catégorie sélectionnée est invalide.',
            'image_produits.image'     => 'Le fichier doit être une image.',
            'image_produits.max'       => 'L\'image ne doit pas dépasser 5 Mo.',
        ];
    }
}
