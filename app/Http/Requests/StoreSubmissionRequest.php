<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'submission_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|min:10',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Nilai pengajuan harus lebih dari 0',
            'description.min' => 'Deskripsi minimal 10 karakter',
            'attachments.*.mimes' => 'Lampiran harus berupa PDF/JPG/JPEG/PNG',
            'attachments.*.max' => 'Ukuran lampiran maksimal 5MB',
        ];
    }
}
