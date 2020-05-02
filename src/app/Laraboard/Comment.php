<?php

namespace App\Laraboard;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
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
        return $this->belongsTo('App\Laraboard\Board');
    }

    /**
     * 댓글 작성 사용자 정보를 가져오기 위한 관계 정의
     */
    public function user()
    {
        return $this->belongsTo('App\Laraboard\User');
    }

    /**
     * 댓글의 게시글 정보를 가져오기 위한 관계 정의
     */
    public function post()
    {
        return $this->belongsTo('App\Laraboard\Post');
    }

    /**
     * 부모 댓글 정보 가져오기 위한 관계 정의
     */
    public function parent()
    {
        return $this->belongsTo('App\Laraboard\Comment');
    }

    /**
     * 자식 댓글 정보 가져오기 위한 관계 정의
     */
    public function children()
    {
        return $this->hasMany('App\Laraboard\Comment');
    }
}
