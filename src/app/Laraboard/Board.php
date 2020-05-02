<?php

namespace App\Laraboard;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
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
        $this->table = config('laraboard.board.table_name.board');
        parent::__construct($attributes);
    }

    /**
     * 게시판 생성 사용자 정보를 가져오기 위한 관계 정의
     */
    public function user()
    {
        return $this->belongsTo('App\Laraboard\User');
    }

    /**
     * 게시판 게시글 정보를 가져오기 위한 관계 정의
     */
    public function posts()
    {
        return $this->hasMany('App\Laraboard\Post');
    }

    /**
     * 게시판 게시글의 댓글 정보를 가져오기 위한 관계 정의
     */
    public function comments()
    {
        return $this->hasMany('App\Laraboard\Comment');
    }

    /**
     * 최소 게시글 목록 읽기 권한 정보를 가져오기 위한 관계 정의
     */
    public function minListReadPrivilege()
    {
        return $this->belongsTo('App\Laraboard\Privilege',
                                'min_list_read_privilege_id');
    }

    /**
     * 최소 게시글 읽기 권한 정보를 가져오기 위한 관계 정의
     */
    public function minPostReadPrivilege()
    {
        return $this->belongsTo('App\Laraboard\Privilege',
                                'min_post_read_privilege_id');
    }

    /**
     * 최소 게시글 쓰기, 수정, 삭제 권한 정보를 가져오기 위한 관계 정의
     */
    public function minPostWritePrivilege()
    {
        return $this->belongsTo('App\Laraboard\Privilege',
                                'min_post_write_privilege_id');
    }

    /**
     * 최소 댓글 읽기 권한 정보를 가져오기 위한 관계 정의
     */
    public function minCommentReadPrivilege()
    {
        return $this->belongsTo('App\Laraboard\Privilege',
                                'min_comment_read_privilege_id');
    }

    /**
     * 최소 댓글 쓰기, 수정, 삭제 권한 정보를 가져오기 위한 관계 정의
     */
    public function minCommentWritePrivilege()
    {
        return $this->belongsTo('App\Laraboard\Privilege',
                                'min_comment_write_privilege_id');
    }
}
