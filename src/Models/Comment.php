<?php
/**
 * 게시판 댓글 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = null;

    /**
     * Constructor
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('laraboard.board.table_name.comment');
        parent::__construct($attributes);
    }

    /**
     * 댓글 소속 게시판 정보를 가져오기 위한 관계 정의
     */
    public function board()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Board', 'board_id');
    }

    /**
     * 댓글 작성 사용자 정보를 가져오기 위한 관계 정의
     */
    public function user()
    {
        return $this->belongsTo('Inium\Laraboard\Models\User', 'wrote_user_id');
    }

    /**
     * 댓글의 게시글 정보를 가져오기 위한 관계 정의
     */
    public function post()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Post', 'post_id');
    }

    /**
     * 부모 댓글 정보 가져오기 위한 관계 정의
     */
    public function parent()
    {
        return $this->belongsTo('Inium\Laraboard\Models\Comment',
                                'parent_comment_id');
    }

    /**
     * 자식 댓글 정보 가져오기 위한 관계 정의
     */
    public function children()
    {
        return $this->hasMany('Inium\Laraboard\Models\Comment',
                              'parent_comment_id',
                              'id');
    }
}
