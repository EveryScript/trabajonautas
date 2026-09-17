<?php

namespace App\Console\Commands;

use App\Services\ProfessionCatalogSynchronizer;
use Database\Seeders\AreaSeeder;
use Database\Seeders\ProfesionAliasSeeder;
use Database\Seeders\ProfesionSeeder;
use Illuminate\Console\Command;
use Throwable;

class SyncProfessionCatalog extends Command
{
    protected $signature = 'trabajonautas:sync-profession-catalog
                            {--dry-run : Calcula los cambios sin modificar la base de datos}
                            {--user= : UUID de un usuario ADMIN activo que será propietario de registros nuevos}';

    protected $description = 'Sincroniza de forma idempotente áreas, profesiones, relaciones y aliases.';

    public function handle(ProfessionCatalogSynchronizer $synchronizer): int
    {
        $dryRun = (bool) $this->option('dry-run');

        try {
            $result = $synchronizer->synchronize(
                areas: AreaSeeder::catalog(),
                professions: ProfesionSeeder::catalog(),
                aliases: ProfesionAliasSeeder::catalog(),
                userId: $this->option('user') ?: null,
                dryRun: $dryRun,
            );
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info($dryRun
            ? 'Modo dry-run: no se modificó la base de datos.'
            : 'Catálogo sincronizado correctamente.');
        $this->table(
            ['Elemento', 'Creados', 'Actualizados'],
            [
                ['Áreas', $result['areas_created'], $result['areas_updated']],
                ['Profesiones', $result['professions_created'], $result['professions_updated']],
                ['Relaciones área-profesión', $result['relations_created'], 0],
                ['Aliases', $result['aliases_created'], $result['aliases_updated']],
            ],
        );

        foreach ($result['warnings'] as $warning) {
            $this->warn($warning);
        }

        foreach ($result['errors'] as $error) {
            $this->error($error);
        }

        return $result['errors'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
