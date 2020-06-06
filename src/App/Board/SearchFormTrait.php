<?php

namespace Inium\Laraboard\App\Board;

use Illuminate\Support\Facades\Auth;
use Inium\Laraboard\App\Board;

trait SearchFormTrait
{
    /**
     * 검색 Form 정보를 반환한다 (로그인 한 사용자에게만 제공).
     *
     * @return array|null
     */
    private function searchForm(string $boardName): ?array
    {
        $searchForm = null;
        if (Auth::check()) {
            $searchForm = [
                'types' => $this->getSearchTypes(),
                'action' => route('laraboard.list.view', [
                    'boardName' => $boardName
                ])
            ];
        }

        return $searchForm;
    }

    /**
     * 검색 유형을 반환한다.
     *
     * @return array
     */
    private function getSearchTypes(): array
    {
        return Board::searchTypes();
    }
}
