<?php

namespace App\Models\Laraboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Laraboard\Board;
use App\Models\Laraboard\Comment;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "lb_board_posts";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "ip_address",
        "user_agent",
        "device_type",
        "os_name",
        "os_ver",
        "browser_name",
        "browser_ver",
        "notice",
        "subject",
        "content",
        "stripped_subject",
        "stripped_content",
        "view_count",
        "points",
        "board_id",
        "wrote_user_id",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        "ip_address",
        "user_agent",
        "stripped_subject",
        "stripped_content",
        "board_id",
        "wrote_user_id",
        "deleted_at",
    ];

    /**
     * 게시판 정보를 가져오기 위한 관계 정의
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function board()
    {
        return $this->belongsTo(Board::class, "board_id");
    }

    /**
     * 게시글의 댓글 정보를 가져오기 위한 관계 정의
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, "post_id");
    }

    /**
     * 게시글 작성한 사용자 정보를 가져오기 위한 관계 정의
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, "wrote_user_id");
    }
}
