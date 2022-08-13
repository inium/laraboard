<?php

namespace App\Http\Requests\Laraboard\Post;

use Inium\Laraboard\Support\Requests\LaraboardFormRequest;

class UpdatePostRequest extends LaraboardFormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "notice" => "sometimes|boolean", // 공지사항 여부
            "subject" => "required|string|max:255", // 게시판 영문명 (Unique)
            "content" => "required|string", // 게시판 한글명 (Unique)
        ];
    }

    /**
     * Set default values of parameter
     * - If the form request value is not defined.
     *
     * @return array|null
     */
    public function defaults(): ?array
    {
        return [
            "notice" => 0,
        ];
    }
}
