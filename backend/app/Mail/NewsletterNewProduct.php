<?php

namespace App\Mail;

use App\Models\Produit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterNewProduct extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Produit  $produit,
        public string   $unsubscribeToken,
        public ?string  $subscriberNom = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🛍️ Nouveau produit sur TOPIDEALSPACE : ' . $this->produit->nom_produits,
        );
    }

    public function content(): Content
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

        return new Content(
            view: 'emails.newsletter.new-product',
            with: [
                'produit'          => $this->produit,
                'productUrl'       => $frontendUrl . '/products/' . $this->produit->id_produits,
                'unsubscribeUrl'   => $frontendUrl . '/newsletter/unsubscribe/' . $this->unsubscribeToken,
                'subscriberNom'    => $this->subscriberNom,
            ],
        );
    }
}
