<?php

namespace App\Http\Requests;

use App\Rules\SouthAfricanId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewUserPostRequest extends FormRequest
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
        $emailUnique = Rule::unique('clients', 'email');
        $mobileUnique = Rule::unique('clients', 'mobile');

        return [
            'name' => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', $emailUnique],
            'mobile' => ['required', 'string', 'max:30', $mobileUnique],
            'birth_date' => ['nullable', 'date'],
            'language_id' => ['nullable', Rule::exists('languages', 'id')],
            'identity_type_id' => ['nullable', Rule::exists('identity_types', 'id')],
            'id_number' => ['nullable', 'string', new SouthAfricanId()],
            'interests' => ['sometimes', 'array'],
            'interests.*' => [Rule::exists('interests', 'id')],
        ];
    }
}
