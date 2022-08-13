<?php

namespace App\Http\Requests\Laraboard\Comment;

use App\Http\Requests\Laraboard\LaraboardFormRequest;

class StoreCommentRequest extends LaraboardFormRequest
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
            "content" => "required|string", // 댓글 본문
            "parent_comment_id" => "sometimes|numeric|min:1", // 부모 댓글
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
            "parent_comment_id" => null,
        ];
    }
}
