<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommentRequest extends FormRequest
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
            'txt' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'txt.required' => 'O campo cover é obrigatório',
            'txt.string' => 'O comentário deve ser do tipo texto.',
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
