<?php

namespace App\Http\Requests\Customers;

use App\Models\Customer;

class UpdateCustomerRequest extends CustomerRequest
{
    public function authorize(): bool
    {
        /** @var Customer $customer */
        $customer = $this->route('customer');

        return $this->user()->can('update', $customer);
    }
}
