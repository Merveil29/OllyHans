<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Produit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductPendingValidation extends Mailable
{
    use Queueable, SerializesModels;

    public Produit $produit;
    public Client $client;

    /**
     * Create a new message instance.
     */
    public function __construct(Produit $produit, Client $client)
    {
        $this->produit = $produit;
        $this->client = $client;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Nouveau produit en attente de validation - TOPIDEALSPACE',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.products.pending-validation',
            with: [
                'produit' => $this->produit,
                'client' => $this->client,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
