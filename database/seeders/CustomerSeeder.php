<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = Str::uuid();
        Customer::create([
            'id' => $id,
            'user_id' => User::query()->where('role', User::ADMIN)?->first()?->id,
            'name' => 'عميل نقدي',
            'is_active' => true
        ]);
    }
}
