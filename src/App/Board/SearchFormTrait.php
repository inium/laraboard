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
                'action' => route('laraboard.postList.view', [
                    'boardName' => $boardName
                ])
            ];
        }

        return $searchForm;
    }

    /**
     * 기본 검색 유형을 반환한다.
     *
     * @return string
     */
    private function getDefaultSearchType(): string
    {
        return Board::defaultSearchType();
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

    /**
     * 사용자가 입력한 검색 유형이 유효한지 반환한다.
     *
     * @param string $type  검색 유형
     * @return boolean
     */
    private function validSearchType(string $type): bool
    {
        return Board::validSearchType($type) ? true : false;
    }
}
