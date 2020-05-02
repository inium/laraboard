<?php
/**
 * 게시판 사용자 권한 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace App\Laraboard;

use Illuminate\Database\Eloquent\Model;
use Inium\Laraboard\Component\PrivilegeRelations;

class Privilege extends Model
{
    use PrivilegeRelations;

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
}
