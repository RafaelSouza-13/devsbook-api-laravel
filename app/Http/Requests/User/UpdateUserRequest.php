<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
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
            'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|max:100|unique:users,email,' . $this->user()->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'birthdate' => 'sometimes|date|before:today',
            // Validar a senha atual
            'current_password' => 'required_with:password|string|current_password',
        ];
    }

    public function messages()
    {
        return [
            'birthdate.before' => 'A data de nascimento deve ser anterior à data atual.',
            'name.required' => 'O campo nome é obrigaório',
            'name.string' => 'O campo nome deve ser do tipo texto',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',
            
            'current_password.required_with' => 'Informe sua senha atual para alterar a senha.',
            'current_password.current_password' => 'A senha atual está incorreta.',

            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.string' => 'A senha deve ser do tipo caracter',

            'birthdate.required' => 'A data de nascimento é obrigatória.',
            'birthdate.date' => 'Informe uma data válida.',
            'birthdate.before' => 'A data de nascimento deve ser anterior à data atual.',
        ];
    }

    public function failedValidation(Validator $validator){
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $errors,
        ], 422));
    }
}
