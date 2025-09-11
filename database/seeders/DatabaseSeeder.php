<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\InterventionTemplates;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ENV-ben megadott nyelv
        $mainLanguage = env('APP_LANGUAGE', 'en');

        // Nyelvi fájl elérési útja
        $path = database_path("seeders/lang/{$mainLanguage}.php");

        // Ellenőrizd, hogy létezik-e a fájl
        if (file_exists($path)) {
            $data = include $path;

            // Táblák végigiterálása
            foreach ($data as $table => $rows) {
                foreach ($rows as $row) {
                    DB::table($table)->insert($row);
                }
            }
        } else {
            $this->command->info("Nyelvi adatfájl nem található: {$mainLanguage}");
        }
    }
}
