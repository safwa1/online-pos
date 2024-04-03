<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currencies')->insert([
            [
                'code' => 'YER',
                'name' => 'ريال يمني',
                'country' => 'اليمن',
                'symbol' => '﷼',
                'decimal_places' => 2,
                'exchange_rate' => 1.0000,
                'smallest_unit_rate' => 0.01,
                'smallest_unit_name' => 'فلس',
                'is_default' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'SAR',
                'name' => 'ريال سعودي',
                'country' => 'السعودية',
                'symbol' => '﷼',
                'decimal_places' => 2,
                'exchange_rate' => 140.00,
                'smallest_unit_rate' => 0.01,
                'smallest_unit_name' => 'هللة',
                'is_default' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'USD',
                'name' => 'دولار أمريكي',
                'country' => 'الولايات المتحدة الأمريكية',
                'symbol' => '$',
                'decimal_places' => 2,
                'exchange_rate' => 531.00,
                'smallest_unit_rate' => 0.01,
                'smallest_unit_name' => 'سنت',
                'is_default' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
