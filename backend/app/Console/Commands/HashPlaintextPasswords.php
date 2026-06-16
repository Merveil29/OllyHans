<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class HashPlaintextPasswords extends Command
{
    protected $signature = 'passwords:hash-plaintext
                            {--dry-run : Afficher les comptes concernés sans modifier la base}';

    protected $description = 'Hache avec bcrypt tous les mots de passe en clair (ou non-bcrypt) dans clients et users';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('[DRY-RUN] Aucune modification ne sera effectuée.');
        }

        $this->line('');
        $this->info('=== Clients ===');
        $this->processModel(
            Client::all(),
            'client_password',
            'id_client',
            $dryRun
        );

        $this->line('');
        $this->info('=== Admins (users) ===');
        $this->processModel(
            User::all(),
            'user_password',
            'id_user',
            $dryRun
        );

        $this->line('');
        $this->info('Terminé.');
        return self::SUCCESS;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $records
     */
    private function processModel($records, string $passwordField, string $idField, bool $dryRun): void
    {
        $total      = 0;
        $migrated   = 0;
        $alreadyBcrypt = 0;
        $skipped    = 0;

        foreach ($records as $record) {
            $total++;
            $hash = $record->$passwordField;

            if (empty($hash)) {
                $skipped++;
                $this->warn("  ID {$record->$idField} : mot de passe vide, ignoré.");
                continue;
            }

            // Déjà bcrypt ?
            $isBcrypt = str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2a$') || str_starts_with($hash, '$2b$');

            if ($isBcrypt) {
                $alreadyBcrypt++;
                continue;
            }

            // Mot de passe en clair ou hash non-bcrypt → re-hacher
            $this->line("  ID {$record->$idField} : hash non-bcrypt détecté (longueur " . strlen($hash) . ")");
            $migrated++;

            if (!$dryRun) {
                $record->$passwordField = Hash::make($hash);
                $record->save();
                // Note : l'utilisateur devra utiliser l'ancien hash comme mot de passe
                // Pour les mots de passe en clair c'est transparent car Hash::make(plaintext)
                // sera vérifié avec Hash::check(plaintext, bcrypt_hash).
            }
        }

        $this->table(
            ['Total', 'Déjà bcrypt', 'Migrés', 'Ignorés'],
            [[$total, $alreadyBcrypt, $migrated, $skipped]]
        );
    }
}
