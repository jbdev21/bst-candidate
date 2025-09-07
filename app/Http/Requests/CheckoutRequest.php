<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quote_id' => ['required', 'integer'],
            'headers.Idempotency-Key' => ['required', 'string'],
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), [
            'headers' => [
                'Idempotency-Key' => $this->header('Idempotency-Key'),
            ],
        ]);
    }

    public function messages()
    {
        return [
            'headers.Idempotency-Key.required' => 'Idempotency-Key is required',
            'headers.Idempotency-Key.string' => 'Idempotency-Key header should be string',
        ];
    }
}
