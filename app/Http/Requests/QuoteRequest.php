<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string'],
            'qty' => ['required', 'integer', 'min:1'],
        ];
    }
}
