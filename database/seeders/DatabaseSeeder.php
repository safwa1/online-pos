<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(CustomerSeeder::class);
//        $this->call(StoreSeeder::class);
    }
}
