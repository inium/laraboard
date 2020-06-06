<?php

namespace Inium\Laraboard\App\Database;

use Illuminate\Pagination\Paginator;

trait PaginationPageResolverTrait {

    /**
     * Paginator에 Page 번호를 설정한다.
     * laravel은 page 번호를 자동으로 처리 ($_REQUEST['page'] 이용).
     * page 번호를 명시적으로 사용하려면, 아래 currentPageResolver 사용
     * 
     * @param integer $pageNum  페이지 번호
     * @return void
     * 
     * @see https://laracasts.com/discuss/channels/general-discussion/laravel-5-set-current-page-programatically
     * @see https://stackoverflow.com/questions/31747801
     */
    protected function setPageNum(int $pageNum)
    {
        // Make sure that you call the static method currentPageResolver()
        // before querying
        Paginator::currentPageResolver(function () use ($pageNum) {
            return $pageNum;
        });
    }
}
