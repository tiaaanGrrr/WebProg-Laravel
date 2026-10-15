<?php

namespace App\Http\Requests;

use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
            'status' => ['nullable', Rule::in(array_keys(Order::STATUS_LABELS))],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $today = CarbonImmutable::now('Asia/Jakarta');
        $this->merge([
            'from' => $this->input('from', $today->startOfMonth()->toDateString()),
            'to' => $this->input('to', $today->toDateString()),
        ]);
    }

    public function messages(): array
    {
        return [
            'from.required' => 'Tanggal mulai wajib diisi.',
            'to.required' => 'Tanggal akhir wajib diisi.',
            'from.date_format' => 'Tanggal mulai tidak valid.',
            'to.date_format' => 'Tanggal akhir tidak valid.',
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
            'status.in' => 'Status pesanan tidak valid.',
        ];
    }

    public function filters(): array
    {
        return $this->safe()->only(['from', 'to', 'status']);
    }
}
