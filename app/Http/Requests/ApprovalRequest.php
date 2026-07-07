<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => 'required|in:approved,rejected',
            'notes' => 'required_if:decision,rejected|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Pilih keputusan approve atau reject.',
            'notes.required_if' => 'Catatan wajib diisi saat menolak pengajuan.',
        ];
    }
}
