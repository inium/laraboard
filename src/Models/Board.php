<?php
/**
 * 게시판 정보 모델
 * 
 * @author inlee <einable@gmail.com>
 */

namespace Inium\Laraboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inium\Laraboard\Core\Pagination\PaginationPageResolverTrait;
use Inium\Laraboard\Core\Relations\BoardRelationsTrait;

class Board extends Model
{
    use SoftDeletes, BoardRelationsTrait, PaginationPageResolverTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = null;

    /**
     * 게시글 검색 유형
     *
     * @var array
     */
    private static $searchTypes = [
        'subcon' => '제목 + 내용',
        'subject' => '제목',
        'content' => '내용',
        'tag' => '태그',
        'comment' => '댓글'
    ];

    /**
     * 기본 검색 유형. getSearchTypes()의 key 입력.
     *
     * @var string
     */
    private static $defaultSearchType = 'subcon';


    /**
     * Constructor
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('laraboard.board.table_name.board');
        parent::__construct($attributes);
    }

    /**
     * 게시판 영문 이름으로 게시판 정보를 가져온다.
     *
     * @param string $boardName     게시판 영문이름
     * @param array $columns    검색대상 필드
     * @return mixed
     */
    public static function findByName(string $boardName)
    {
        // return static::with('posts')->where('name', $boardName)->first();
        return static::where('name', $boardName)->first();
    }

    /**
     * 게시판의 공지글들을 가져온다.
     * - 공지글은 최신순으로 정렬
     *
     * @return mixed
     */
    public function getNotices()
    {
        $notices = $this->posts()
                        ->with('user')
                        ->withCount('comments')
                        ->where('notice', 1)
                        ->latest()
                        ->get();

        return $notices;
    }

    /**
     * 페이지 단위 게시글 목록을 가져온다.
     *
     * @param integer $page     페이지 번호
     * @return mixed
     */
    public function getPosts(int $page = 1)
    {
        $this->setPageNum($page);

        $list = $this->posts()
                     ->with('user')
                     ->withCount('comments')
                     ->where('notice', 0)
                     ->latest()
                     ->paginate($this->post_rows_per_page);

        return $list;
    }

    /**
     * 게시글을 가져온다.
     *
     * @param integer $postId               게시글 ID
     * @param boolean $incrementViewCount   조회수 1 증가 여부.
     * @return mixed
     */
    public function getPost(int $postId, bool $incrementViewCount = true)
    {
        $post = $this->posts()
                     ->with('user')
                     ->withCount('comments')
                     ->find($postId);

        // 조회수 1 증가
        if ($incrementViewCount) {
            $post->view_count++;
            $post->timestamps = false;  // 조회수 증가시 updated_at 추가 안함
            $post->save();

            $post->timestamps = true;   // update_at 사용 복구
        }

        return $post;
    }


    // public function search()
    // {

    // }

    /**
     * 검색 유형 정보를 반환한다.
     *
     * @return array
     */
    public static function getSearchTypes(): array
    {
        return static::$searchTypes;
    }

    /**
     * 기본 검색 유형 key를 반환한다.
     *
     * @return string
     */
    public static function getDefaultSearchType(): string
    {
        $searchTypes = static::getSearchTypes();
        return $searchTypes[static::$defaultSearchType];
    }

    /**
     * 검색 유형 key가 맞는지 확인하여 그 결과를 반환한다.
     *
     * @param string $type      검색 유형 key.
     * @return boolean
     */
    public static function isValidSearchType(string $type): bool
    {
        $searchTypes = static::getSearchTypes();
        return array_key_exists($type, $searchTypes);
    }
}
