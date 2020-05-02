<?php

namespace App\Laraboard;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
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
        $this->table = config('laraboard.board.table_name.privilege');
        parent::__construct($attributes);
    }

    /**
     * 사용자 권한에 해당하는 게시판 사용자들을 가져오기 위한 관계 정의
     *
     * @return void
     */
    public function users()
    {
        return $this->hasMany('App\Laraboard\User');
    }
}
