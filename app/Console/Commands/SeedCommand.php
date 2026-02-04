<?php

namespace App\Console\Commands;

use Domain\Travel\Models\TravelFormat;
use Domain\Travel\Models\TravelResort;
use Domain\Travel\Seeders\TravelSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $choice = $this->choice('What entity need to seed?', [
            'schedule',
            'travel',
        ], 'travel');


        match ($choice) {
            'travel' => $this->travel(),
        };

        return self::SUCCESS;
    }

    protected function travel(): void
    {
        $delete = $this->confirm('Delete old?');

        if ($delete) {
            Schema::disableForeignKeyConstraints();
            TravelResort::truncate();
            TravelFormat::truncate();
            Schema::enableForeignKeyConstraints();
        }

        $this->call('db:seed', ['class' => TravelSeeder::class]);
    }
}
