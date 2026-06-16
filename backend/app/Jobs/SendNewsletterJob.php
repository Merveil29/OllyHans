<?php

namespace App\Jobs;

use App\Mail\NewsletterNewProduct;
use App\Models\NewsletterSubscriber;
use App\Models\Produit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $type,
        protected Produit $produit
    ) {}

    public function handle(): void
    {
        try {
            $subscribers = NewsletterSubscriber::wantsProducts()->get();

            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)
                    ->queue(new NewsletterNewProduct(
                        produit: $this->produit,
                        unsubscribeToken: $subscriber->unsubscribe_token,
                        subscriberNom: $subscriber->nom,
                    ));
            }

            Log::info('Newsletter envoyée pour le produit', [
                'produit_id' => $this->produit->id_produits,
                'subscribers_count' => $subscribers->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi newsletter: ' . $e->getMessage());
        }
    }
}
