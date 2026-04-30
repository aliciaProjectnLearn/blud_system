<?php

namespace App\Http\Requests\AdminKantin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenyewaanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal_mulai_sewa' => 'required|date',
            'tanggal_selesai_sewa' => 'required|date|after_or_equal:tanggal_mulai_sewa',
            'harga_sewa_tahunan' => 'nullable|numeric|min:0',
            'status_sewa' => 'required|in:pending,disetujui,ditolak,aktif,selesai,dibatalkan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tanggal_mulai_sewa.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai_sewa.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai_sewa.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'status_sewa.in' => 'Status tidak valid.',
        ];
    }
}
