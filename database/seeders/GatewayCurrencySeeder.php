<?php

namespace Database\Seeders;

use App\Models\GatewayCurrency;
use Illuminate\Database\Seeder;

class GatewayCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gatewayCurrencies = [
            [
                'id' => 1,
                'gateway_id' => 1,
                'name' => 'PayPal - USD',
                'currency' => 'USD',
                'symbol' => '$',
                'min_amount' => 50.00,
                'max_amount' => 5000.00,
                'percent_charge' => 2.50,
                'fixed_charge' => 0.50,
                'rate' => 1.00,
                'gateway_parameter' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'gateway_id' => 3,
                'name' => 'Stripe - USD',
                'currency' => 'USD',
                'symbol' => '$',
                'min_amount' => 50.00,
                'max_amount' => 5000.00,
                'percent_charge' => 2.90,
                'fixed_charge' => 0.30,
                'rate' => 1.00,
                'gateway_parameter' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'gateway_id' => 1001,
                'name' => 'USD',
                'currency' => 'USD',
                'symbol' => '$',
                'min_amount' => 50.00,
                'max_amount' => 5000.00,
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'rate' => 1.00,
                'gateway_parameter' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'gateway_id' => 1002,
                'name' => 'USD',
                'currency' => 'USD',
                'symbol' => '$',
                'min_amount' => 50.00,
                'max_amount' => 5000.00,
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'rate' => 1.00,
                'gateway_parameter' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'gateway_id' => 1003,
                'name' => 'USDT',
                'currency' => 'USDT',
                'symbol' => '₮',
                'min_amount' => 50.00,
                'max_amount' => 5000.00,
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'rate' => 1.00,
                'gateway_parameter' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'gateway_id' => 1004,
                'name' => 'USD',
                'currency' => 'USD',
                'symbol' => '$',
                'min_amount' => 50.00,
                'max_amount' => 5000.00,
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'rate' => 1.00,
                'gateway_parameter' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($gatewayCurrencies as $gatewayCurrency) {
            GatewayCurrency::create($gatewayCurrency);
        }
    }
}
