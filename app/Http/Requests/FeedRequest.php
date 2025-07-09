<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
class FeedRequest extends FormRequest
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
        $tiposPermitidos = ['text', 'photo'];
        return [
            'type' => ['required', 'string', Rule::in($tiposPermitidos)],
            'body' => ['required_if:type,text', 'string'],
            'photo' => ['required_if:type,photo', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'O campo "tipo" é obrigatório.',
            'type.string' => 'O campo "tipo" deve ser uma string.',
            'type.in' => 'O tipo selecionado é inválido. Os tipos permitidos são: text ou photo.',

            'body.required_if' => 'O campo "texto" é obrigatório quando o tipo for "text".',
            'body.string' => 'O campo "texto" deve ser uma string.',

            'photo.required_if' => 'O campo "foto" é obrigatório quando o tipo for "photo".',
            'photo.image' => 'O arquivo enviado deve ser uma imagem.',
            'photo.mimes' => 'A imagem deve estar no formato JPEG, PNG ou JPG.',
            'photo.max' => 'A imagem não pode ter mais que 2MB.',
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
