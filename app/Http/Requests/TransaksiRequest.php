<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiRequest extends FormRequest
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
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_masuk',
            'status' => 'nullable|in:pending,proses,selesai,diambil',
            'catatan' => 'nullable|string|max:1000',
            'layanan' => 'required|array|min:1',
            'layanan.*.layanan_id' => 'required|exists:layanan,id',
            'layanan.*.jumlah' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'pelanggan_id.required' => 'Pelanggan harus dipilih',
            'pelanggan_id.exists' => 'Pelanggan tidak valid',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal masuk',
            'status.in' => 'Status harus salah satu dari: pending, proses, selesai, diambil',
            'catatan.max' => 'Catatan maksimal 1000 karakter',
            'layanan.required' => 'Layanan harus dipilih',
            'layanan.array' => 'Format layanan tidak valid',
            'layanan.min' => 'Minimal satu layanan harus dipilih',
            'layanan.*.layanan_id.required' => 'ID layanan harus diisi',
            'layanan.*.layanan_id.exists' => 'Layanan tidak valid',
            'layanan.*.jumlah.required' => 'Jumlah layanan harus diisi',
            'layanan.*.jumlah.numeric' => 'Jumlah layanan harus berupa angka',
            'layanan.*.jumlah.min' => 'Jumlah layanan minimal 0.01',
        ];
    }
}
