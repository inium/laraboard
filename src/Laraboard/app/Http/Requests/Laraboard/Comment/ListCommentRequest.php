<?php

namespace App\Http\Requests\Laraboard\Comment;

use Inium\Laraboard\Support\Requests\LaraboardFormRequest;

class ListCommentRequest extends LaraboardFormRequest
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
            "parent" => "sometimes|nullable|numeric|min:1", // 부모 댓글
            "page" => "numeric|min:1", // 페이지 번호
            "query" => "sometimes|string|nullable", // 검색어
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
            "parent" => null,
            "page" => 1,
            "query" => null,
        ];
    }
}
