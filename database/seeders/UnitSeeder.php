<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::create([
            'name' => 'حبة',
            'symbol' => 'ح'
        ]);

        Unit::create([
            'name' => 'كرتون',
            'symbol' => 'ك'
        ]);

        Unit::create([
            'name' => 'شدة',
            'symbol' => 'ش'
        ]);

        Unit::create([
            'name' => 'باكت',
            'symbol' => 'ب'
        ]);

        Unit::create([
            'name' => 'درزن',
            'symbol' => 'د'
        ]);

        Unit::create([
            'name' => 'كيس',
            'symbol' => 'ك'
        ]);

        Unit::create([
            'name' => 'كيلو',
            'symbol' => 'كج'
        ]);

        Unit::create([
            'name' => 'جرام',
            'symbol' => 'جم'
        ]);
    }
}
