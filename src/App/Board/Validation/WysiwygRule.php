<?php

namespace Inium\Laraboard\App\Board\Validation;

use Illuminate\Contracts\Validation\Rule;

class WysiwygRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /**
         * Quill Editor와 같이 아무 내용을 입력하지 않았음에도 불구하고
         * <p><br></p>이 입력된 경우에 대해 글 입력을 하지 않게 하기 위한
         * Validation Rule 추가
         */
        return strlen(strip_tags($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '내용을 입력해주세요.';
    }
}
