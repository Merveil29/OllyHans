<?php

namespace App\Http\Requests\Produit;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_produits'         => 'required|string|max:255',
            'prix_produits'        => 'required|numeric|min:0|max:999999999999',
            'description_produits' => 'required|string',
            'id_categorie'         => 'required|integer|exists:categorie,id_categorie',
            'image_produits'       => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'image_produits1'      => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'image_produits2'      => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_produits.required'         => 'Le nom du produit est obligatoire.',
            'prix_produits.required'        => 'Le prix est obligatoire.',
            'prix_produits.numeric'         => 'Le prix doit être un nombre.',
            'description_produits.required' => 'La description est obligatoire.',
            'id_categorie.required'         => 'La catégorie est obligatoire.',
            'id_categorie.exists'           => 'La catégorie sélectionnée est invalide.',
            'image_produits.required'       => 'L\'image principale est obligatoire.',
            'image_produits.image'          => 'Le fichier doit être une image.',
            'image_produits.max'            => 'L\'image ne doit pas dépasser 5 Mo.',
        ];
    }
}
