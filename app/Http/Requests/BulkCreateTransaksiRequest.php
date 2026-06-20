<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkCreateTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaksis' => ['required', 'array', 'min:1'],
            'transaksis.*.pelanggan_id' => ['required', 'exists:pelanggan,id'],
            'transaksis.*.tanggal_masuk' => ['required', 'date'],
            'transaksis.*.tanggal_selesai' => ['nullable', 'date', 'after_or_equal:transaksis.*.tanggal_masuk'],
            'transaksis.*.catatan' => ['nullable', 'string', 'max:1000'],
            'transaksis.*.layanan' => ['required', 'array', 'min:1'],
            'transaksis.*.layanan.*.layanan_id' => ['required', 'exists:layanan,id'],
            'transaksis.*.layanan.*.jumlah' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaksis.required' => 'Minimal harus ada satu transaksi',
            'transaksis.array' => 'Format data transaksi tidak valid',
            'transaksis.min' => 'Minimal harus ada satu transaksi',
            'transaksis.*.pelanggan_id.required' => 'Pelanggan harus dipilih untuk setiap transaksi',
            'transaksis.*.pelanggan_id.exists' => 'Pelanggan tidak valid',
            'transaksis.*.tanggal_masuk.required' => 'Tanggal masuk harus diisi untuk setiap transaksi',
            'transaksis.*.tanggal_masuk.date' => 'Format tanggal masuk tidak valid',
            'transaksis.*.tanggal_selesai.date' => 'Format tanggal selesai tidak valid',
            'transaksis.*.tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal masuk',
            'transaksis.*.catatan.max' => 'Catatan maksimal 1000 karakter',
            'transaksis.*.layanan.required' => 'Layanan harus dipilih untuk setiap transaksi',
            'transaksis.*.layanan.array' => 'Format layanan tidak valid',
            'transaksis.*.layanan.min' => 'Minimal satu layanan harus dipilih per transaksi',
            'transaksis.*.layanan.*.layanan_id.required' => 'ID layanan harus diisi',
            'transaksis.*.layanan.*.layanan_id.exists' => 'Layanan tidak valid',
            'transaksis.*.layanan.*.jumlah.required' => 'Jumlah layanan harus diisi',
            'transaksis.*.layanan.*.jumlah.numeric' => 'Jumlah layanan harus berupa angka',
            'transaksis.*.layanan.*.jumlah.min' => 'Jumlah layanan minimal 0.01',
        ];
    }
}
