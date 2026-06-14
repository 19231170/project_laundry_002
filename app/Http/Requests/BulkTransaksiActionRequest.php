<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkTransaksiActionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'action' => $this->normalizeEnumValue($this->input('action')),
            'status_transaksi' => $this->normalizeEnumValue($this->input('status_transaksi')),
            'status_pembayaran' => $this->normalizeEnumValue($this->input('status_pembayaran')),
        ]);
    }

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaksi_ids' => ['required', 'array', 'min:1'],
            'transaksi_ids.*' => ['integer', 'exists:transaksi,id'],
            'action' => ['required', 'in:status_transaksi,status_pembayaran'],
            'status_transaksi' => ['nullable', 'required_if:action,status_transaksi', 'in:pending,proses,selesai,diambil'],
            'status_pembayaran' => ['nullable', 'required_if:action,status_pembayaran', 'in:belum_lunas,lunas'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaksi_ids.required' => 'Pilih minimal satu transaksi.',
            'transaksi_ids.array' => 'Format pilihan transaksi tidak valid.',
            'transaksi_ids.min' => 'Pilih minimal satu transaksi.',
            'transaksi_ids.*.exists' => 'Transaksi yang dipilih tidak ditemukan.',
            'action.required' => 'Aksi wajib dipilih.',
            'action.in' => 'Aksi yang dipilih tidak valid.',
            'status_transaksi.required_if' => 'Status transaksi wajib dipilih.',
            'status_transaksi.in' => 'Status transaksi tidak valid.',
            'status_pembayaran.required_if' => 'Status pembayaran wajib dipilih.',
            'status_pembayaran.in' => 'Status pembayaran tidak valid.',
        ];
    }

    private function normalizeEnumValue(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalized = str_replace(' ', '_', strtolower(trim($value)));

        return $normalized === '' ? null : $normalized;
    }
}
