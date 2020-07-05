<?php

namespace Inium\Laraboard\App\Board\Request;

use Illuminate\Foundation\Http\FormRequest;
use Inium\Laraboard\App\Board\Validation\WysiwygRule;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'notice' => 'boolean',
            'subject' => 'required',
            'content' => ['required', new WysiwygRule ]
        ];
    }

     /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'subject.required' => '제목을 입력해주세요.',
            'content.required' => '내용을 입력해주세요.'
        ];
    }
}
