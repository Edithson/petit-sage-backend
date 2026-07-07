<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Controllers\ResponseHelper;

class UpdateQuestionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'intitule' => 'required|array',
            'intitule.contentType' => 'required|string|in:text,media',
            'intitule.mediaSourceType' => 'nullable|string|in:url,file',
            'intitule.text' => 'nullable|string',
            'intitule.mediaDescription' => 'nullable|string',
            'intitule.mediaUrl' => 'nullable|string',
            'intitule.mediaFile' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:20480',
            'theme' => 'required|integer|exists:thematiques,id',
            'partie_id' => 'required|integer|exists:parties,id',
            'difficulte' => 'required|integer|in:1,2,3',
            'typeReponse' => 'required|string|in:unique,multiple',
            'explication' => 'nullable|string',
            'indice' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.isCorrect' => 'required|boolean',
            'options.*.contentType' => 'required|string|in:text,media',
            'options.*.text' => 'nullable|string',
            'options.*.mediaSourceType' => 'nullable|string|in:url,file',
            'options.*.mediaUrl' => 'nullable|string',
            'options.*.mediaDescription' => 'nullable|string',
        ];

        // Validation dynamique pour l'intitulé
        if ($this->input('intitule.contentType') === 'text') {
            $rules['intitule.text'] = 'required|string';
        } elseif ($this->input('intitule.contentType') === 'media') {
            $rules['intitule.mediaDescription'] = 'required|string';
            if ($this->input('intitule.mediaSourceType') === 'url') {
                $rules['intitule.mediaUrl'] = 'required|string';
            } elseif ($this->input('intitule.mediaSourceType') === 'file') {
                // Rendre le fichier optionnel si l'on ne veut pas le remplacer
                $rules['intitule.mediaFile'] = 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:20480';
            }
        }

        // Validation dynamique pour les options
        if (is_array($this->input('options'))) {
            foreach ($this->input('options') as $key => $option) {
                if ($option['contentType'] === 'text') {
                    $rules["options.{$key}.text"] = 'required|string';
                } elseif ($option['contentType'] === 'media') {
                    if ($option['mediaSourceType'] === 'url') {
                        $rules["options.{$key}.mediaUrl"] = 'required|string';
                    } elseif ($option['mediaSourceType'] === 'file') {
                        $rules["options.{$key}.mediaFile"] = 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:10240';
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Custom response on failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ResponseHelper::errorResponse(
                'Erreurs de validation : ' . $validator->errors()->first(),
                422,
                ['errors' => $validator->errors()]
            )
        );
    }
}
