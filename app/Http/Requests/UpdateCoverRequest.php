<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCoverRequest extends FormRequest
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
            'cover' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'cover.required' => 'O campo cover é obrigatório',
            'cover.image' => 'O arquivo deve ser uma imagem.',
            'cover.mimes' => 'A imagem deve estar no formato: jpeg, png, jpg.',
            'cover.max' => 'A imagem não pode ter mais que 2MB.',
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
