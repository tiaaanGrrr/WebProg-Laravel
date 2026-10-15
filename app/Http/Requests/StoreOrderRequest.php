<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'checkout_token' => ['required', 'uuid'],
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[+0-9][0-9 ()-]{7,24}$/'],
            'address' => ['required', 'string', 'min:10', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'phone.regex' => 'Nomor HP harus valid, terdiri dari 8 sampai 25 karakter.',
            'address.min' => 'Alamat minimal 10 karakter.',
        ];
    }

    public function attributes(): array
    {
        return ['customer_name' => 'Nama penerima', 'phone' => 'Nomor HP', 'address' => 'Alamat', 'notes' => 'Catatan'];
    }
}
