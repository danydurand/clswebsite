<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forms = [
            // Example 1: Form for bank transfer deposit
            [
                'id' => 1,
                'name' => 'manual_bank_deposit',
                'act' => 'deposit',
                'form_data' => [
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Transaction Number',
                        'name' => 'transaction_number',
                        'placeholder' => 'Enter transaction number'
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Source Bank Name',
                        'name' => 'bank_name',
                        'placeholder' => 'Enter bank name'
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Source Account Number',
                        'name' => 'account_number',
                        'placeholder' => 'Enter account number'
                    ],
                    [
                        'type' => 'textarea',
                        'required' => false,
                        'label' => 'Additional Notes',
                        'name' => 'notes',
                        'placeholder' => 'Any additional information'
                    ]
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Example 2: Simple form for cash deposit
            [
                'id' => 2,
                'name' => 'manual_cash_deposit',
                'act' => 'deposit',
                'form_data' => [
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Reference Number',
                        'name' => 'reference_number',
                        'placeholder' => 'Enter reference number'
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Branch where deposit was made',
                        'name' => 'branch_name',
                        'placeholder' => 'Enter branch name'
                    ]
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Example 3: Form for cryptocurrency deposit
            [
                'id' => 3,
                'name' => 'manual_crypto_deposit',
                'act' => 'deposit',
                'form_data' => [
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Transaction Hash (TxID)',
                        'name' => 'transaction_hash',
                        'placeholder' => 'Enter transaction hash'
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Wallet Address (Sender)',
                        'name' => 'sender_wallet',
                        'placeholder' => 'Enter your wallet address'
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Network used',
                        'name' => 'network',
                        'placeholder' => 'e.g., BTC, ETH, USDT-TRC20'
                    ]
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Example 4: Form for manual withdrawals
            [
                'id' => 4,
                'name' => 'manual_withdraw',
                'act' => 'withdraw',
                'form_data' => [
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Account Holder Name',
                        'name' => 'account_holder_name',
                        'placeholder' => 'Enter account holder name'
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Bank Name',
                        'name' => 'bank_name',
                        'placeholder' => 'Enter bank name'
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Account Number',
                        'name' => 'account_number',
                        'placeholder' => 'Enter account number'
                    ],
                    [
                        'type' => 'text',
                        'required' => false,
                        'label' => 'SWIFT/BIC Code (for international)',
                        'name' => 'swift_code',
                        'placeholder' => 'Enter SWIFT/BIC code'
                    ]
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($forms as $form) {
            Form::create($form);
        }
    }
}
