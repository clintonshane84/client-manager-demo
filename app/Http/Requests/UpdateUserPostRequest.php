<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\SouthAfricanId;
use App\Models\User;

class UpdateUserPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function targetUserId(): ?int
    {
        $routeParam = $this->route('user'); // could be User model or id
        if ($routeParam instanceof User) {
            return $routeParam->id;
        }
        return $routeParam ? (int) $routeParam : null;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->targetUserId();
        
        $emailUnique  = Rule::unique('users', 'email')->ignore($userId);
        $mobileUnique = Rule::unique('users', 'mobile')->ignore($userId);
        
        return [
            // Use 'sometimes' so partial updates are allowed.
            'name'             => ['sometimes', 'string', 'max:100'],
            'surname'          => ['sometimes', 'string', 'max:100'],
            
            'email'            => ['sometimes', 'email', 'max:255', $emailUnique],
            'mobile'           => ['sometimes', 'string', 'max:30', $mobileUnique],
            
            'birth_date'       => ['sometimes', 'nullable', 'date'],
            
            'language_id'      => ['sometimes', 'nullable', Rule::exists('languages', 'id')],
            
            // Identity fields (optional; validated if present)
            'identity_type_id' => ['sometimes', 'nullable', Rule::exists('identity_types', 'id')],
            'id_number'        => ['sometimes', 'nullable', 'string', new SouthAfricanId],
            
            // Interests (optional)
            'interests'        => ['sometimes', 'array'],
            'interests.*'      => [Rule::exists('interests', 'id')],
        ];
    }
    
    /**
     * Optional: make incoming types consistent before validation.
     */
    protected function prepareForValidation(): void
    {
        // Example: trim strings
        $this->merge([
            'email'  => $this->filled('email')  ? trim((string)$this->input('email')) : $this->input('email'),
            'mobile' => $this->filled('mobile') ? trim((string)$this->input('mobile')) : $this->input('mobile'),
        ]);
    }
    
    /**
     * Optional: custom messages if you want friendlier errors.
     */
    public function messages(): array
    {
        return [
            'email.unique'  => 'That email address is already in use.',
            'mobile.unique' => 'That mobile number is already in use.',
        ];
    }
}
