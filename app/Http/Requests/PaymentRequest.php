<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => 'required|in:paid,rejected',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Pilih keputusan pembayaran.',
        ];
    }
}
