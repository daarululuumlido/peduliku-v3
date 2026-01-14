<?php

namespace Database\Seeders;

use App\Models\Orang;
use Illuminate\Database\Seeder;

class OrangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = (int) $this->command->ask('Berapa jumlah data orang yang ingin dibuat?', 20000);
        
        $this->command->info("Membuat {$count} data orang...");

        // Use chunks to avoid memory issues
        $chunkSize = 1000;
        $chunks = ceil($count / $chunkSize);

        $bar = $this->command->getOutput()->createProgressBar($chunks);
        $bar->start();

        for ($i = 0; $i < $chunks; $i++) {
            $remaining = $count - ($i * $chunkSize);
            $createCount = min($chunkSize, $remaining);
            
            Orang::factory()->count($createCount)->create();
            
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Selesai! {$count} data orang telah dibuat.");
    }
}
