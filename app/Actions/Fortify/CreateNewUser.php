<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user with customer data.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            // 'country_id' => ['required', 'integer', 'exists:countries,id'],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique(Customer::class, 'phone'),
            ],
            // 'document_id' => [
            //     'required',
            //     'string',
            //     'max:20',
            //     Rule::unique(Customer::class, 'document_id'),
            // ],
            'birth_date' => [
                'required',
                'date',
                'before:' . now()->subYears(18)->format('Y-m-d'),
            ],
            'terms_accepted' => [
                'required',
                'accepted',
            ],
        ], [
            'birth_date.before' => 'You must be at least 18 years old to register.',
            'terms_accepted.accepted' => 'You must accept the Terms and Conditions to register.',
        ])->validate();

        return DB::transaction(function () use ($input) {
            // Create the User record
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'type' => UserTypeEnum::Customer->value,
                'password' => $input['password'],
            ]);

            // Create the Customer record linked to the User
            $customer = Customer::create([
                'user_id' => $user->id,
                // 'country_id' => $input['country_id'],
                'phone' => $input['phone'],
                // 'document_id' => $input['document_id'],
                'name' => $input['name'],
                'email' => $input['email'],
                'birth_date' => $input['birth_date'],
                'is_reseller' => false,
                'balance' => 0,
            ]);

            // Update User with customer_id reference
            $user->update(['customer_id' => $customer->id]);

            return $user;
        });
    }
}
