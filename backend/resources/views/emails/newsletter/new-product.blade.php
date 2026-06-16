<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Produit</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 0; background: #f4f4f4; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .header h1 { margin: 0 0 5px; font-size: 24px; }
        .header p { margin: 0; opacity: 0.9; font-size: 14px; }
        .content { background: #fff; padding: 30px; border: 1px solid #e0e0e0; }
        .product-card { background: #f9f9f9; border-radius: 12px; overflow: hidden; margin: 20px 0; border: 1px solid #eee; }
        .product-image { width: 100%; max-height: 300px; object-fit: cover; display: block; }
        .product-info { padding: 20px; }
        .product-info h2 { margin: 0 0 8px; color: #333; font-size: 20px; }
        .product-price { font-size: 22px; font-weight: bold; color: #667eea; margin: 10px 0; }
        .product-desc { color: #666; font-size: 14px; line-height: 1.5; }
        .button { display: inline-block; background: #667eea; color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; margin: 15px 0; }
        .button:hover { background: #5568d3; }
        .cta { text-align: center; margin: 25px 0; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; background: #f0f0f0; border-radius: 0 0 10px 10px; }
        .footer a { color: #667eea; text-decoration: underline; }
        .divider { height: 1px; background: #eee; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛍️ Nouveau Produit</h1>
        <p>TOPIDEALSPACE</p>
    </div>

    <div class="content">
        @php
            $hour = now()->setTimezone('Africa/Porto-Novo')->hour;
            $civil = ($hour >= 6 && $hour < 18) ? 'Bonjour' : 'Bonsoir';
            $salutation = $civil . ($subscriberNom ? ', ' . $subscriberNom : '') . ' !';
        @endphp
        <p>{{ $salutation }}</p>
        <p>Un nouveau produit vient d'être ajouté sur <strong>TOPIDEALSPACE</strong> et pourrait vous intéresser !</p>

        <div class="product-card">
            @if($produit->image_produits)
                <img src="{{ $produit->image_produits }}" alt="{{ $produit->nom_produits }}" class="product-image">
            @endif
            <div class="product-info">
                <h2>{{ $produit->nom_produits }}</h2>
                <div class="product-price">{{ number_format($produit->prix_produits, 0, ',', ' ') }} FCFA</div>
                @if($produit->description_produits)
                    <p class="product-desc">{{ Str::limit(strip_tags($produit->description_produits), 200) }}</p>
                @endif
            </div>
        </div>

        <div class="cta">
            <a href="{{ $productUrl }}" class="button">Voir le produit →</a>
        </div>

        <div class="divider"></div>
        <p style="color: #999; font-size: 13px;">Vous recevez cet email car vous êtes abonné à la newsletter TOPIDEALSPACE.</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} TOPIDEALSPACE. Tous droits réservés.</p>
        <p><a href="{{ $unsubscribeUrl }}">Se désabonner de la newsletter</a></p>
    </div>
</body>
</html>
