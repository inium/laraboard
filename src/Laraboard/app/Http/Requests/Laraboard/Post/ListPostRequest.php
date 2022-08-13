<?php

namespace App\Http\Requests\Laraboard\Post;

use Inium\Laraboard\Support\Requests\LaraboardFormRequest;

class ListPostRequest extends LaraboardFormRequest
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
            "notice" => "sometimes|boolean", // 공지사항
            "page" => "numeric|min:1", // 게시글 페이지 번호
            "query" => "sometimes|string|nullable", // 게시글 검색어
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
            "page" => 1,
            "query" => null,
        ];
    }
}
