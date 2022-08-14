<?php

namespace App\Models\Laraboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Laraboard\Post;
use App\Models\Laraboard\Comment;
use Illuminate\support\Str;

class Board extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "lb_boards";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name",
        "name_ko",
        "description",
        "post_points",
        "comment_points",
        "posts_per_page",
        "comments_per_page",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ["deleted_at"];

    /**
     * 게시판 게시글의 정보를 가져오기 위한 관계 정의
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, "board_id");
    }

    /**
     * 게시판 게시글의 댓글 정보를 가져오기 위한 관계 정의
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, "board_id");
    }
}
