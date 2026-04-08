<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:256'],
            'edrpou' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'edrpou' => is_string($this->input('edrpou')) ? trim($this->input('edrpou')) : $this->input('edrpou'),
            'address' => is_string($this->input('address')) ? trim($this->input('address')) : $this->input('address'),
        ]);
    }
}
