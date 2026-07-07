<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Controllers\ResponseHelper;

class StoreQuestionRequest extends FormRequest
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
            'difficulte' => 'required|string|in:1,2,3',
            'typeReponse' => 'required|string|in:unique,multiple',
            'explication' => 'nullable|string',
            'indice' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.isCorrect' => 'required|boolean',
            'options.*.contentType' => 'required|string|in:text,media',
            'options.*.text' => 'string|nullable',
            'options.*.mediaSourceType' => 'nullable|string|in:url,file',
            'options.*.mediaUrl' => 'url|nullable',
            'options.*.mediaDescription' => 'string|nullable',
        ];

        // Validation dynamique pour l'intitulé de la question
        if ($this->input('intitule.contentType') === 'text') {
            $rules['intitule.text'] = 'required|string';
        } elseif ($this->input('intitule.contentType') === 'media') {
            $rules['intitule.mediaDescription'] = 'required|string';
            if ($this->input('intitule.mediaSourceType') === 'url') {
                $rules['intitule.mediaUrl'] = 'required';
            } elseif ($this->input('intitule.mediaSourceType') === 'file') {
                $rules['intitule.mediaFile'] = 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:20480';
            }
        }

        // Validation pour les fichiers médias des options (max 10MB par option)
        if (is_array($this->input('options'))) {
            foreach ($this->input('options') as $key => $option) {
                if (isset($option['contentType']) && $option['contentType'] === 'media') {
                    if (isset($option['mediaSourceType']) && $option['mediaSourceType'] === 'file') {
                        $rules["options.{$key}.mediaFile"] = 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:10240';
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
