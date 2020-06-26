<?php

/**
 * 게시글 목록, 검색 페이지
 *
 * -------------------------------------------------------------------------
 * GET [/{$prefix}]/board/{boardName}?query=lorem&page=1
 * 
 * Route params
 * @param Request $request  Request
 * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
 * @param string boardName  게시판 영문 이름.
 * 
 * Query params (Optional)
 * @param string query      검색어.
 * @param int    page       페이지 번호. 기본 1.
 * -------------------------------------------------------------------------
 */
Route::get('/board/{boardName}', 'PostListSearchController@view')
        ->name('board.postListSearch.view');

/**
 * 게시글 보기 페이지
 *
 * -----------------------------------------------------------------------------
 * GET [/{$prefix}]/board/{boardName}/{id}?page=1&query=lorem
 * 
 * Route params
 * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
 * @param string boardName  게시판 영문 이름.
 * @param integer id        게시글 ID.
 * 
 * Query params (Optional)
 * @param int    page       페이지 번호. 기본 1.
 * @param string query      검색어.
 * -----------------------------------------------------------------------------
 */
Route::get('/board/{boardName}/{id}', 'PostController@view')
        ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
        ->name('board.post.view');

/**
 * 게시글 쓰기 페이지
 * 
 * -----------------------------------------------------------------------------
 * GET [/{$prefix}]/board/{boardName}/write
 * 
 * Route params
 * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
 * @param string boardName  게시판 영문 이름.
 * -----------------------------------------------------------------------------
 */
Route::get('/board/{boardName}/write', 'PostWriteController@view')
        ->name('board.post.write.view');

/**
 * 게시글 저장
 * 
 * -----------------------------------------------------------------------------
 * POST [/{$prefix}]/board/{boardName}/write
 * 
 * subject=lorem
 * content=<p>dolor</p>
 * 
 * Route params
 * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
 * @param string boardName  게시판 영문 이름.
 * 
 * Post params
 * @param string subject 게시글 제목
 * @param string content 게시글 내용 (HTML)
 * -----------------------------------------------------------------------------
 */
Route::post('/board/{boardName}/write', 'PostWriteController@submit')
        ->name('board.post.write.submit');

/**
 * 게시글 수정 페이지
 * 
 * -----------------------------------------------------------------------------
 * GET [/{$prefix}]/board/{boardName}/modify/{id}
 * 
 * Route params
 * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
 * @param string boardName  게시판 영문 이름.
 * @param integer id        게시글 ID
 * -----------------------------------------------------------------------------
 */
Route::get('/board/{boardName}/modify/{id}', 'PostModifyController@view')
        ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
        ->name('board.post.modify.view');

/**
 * 게시글 수정
 * 
 * -----------------------------------------------------------------------------
 * PUT [/{$prefix}]/board/{boardName}/modify/{id}
 * 
 * subject=lorem
 * content=<p>ipsum</p>
 * 
 * Route params
 * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
 * @param string boardName  게시판 영문 이름.
 * @param integer id        게시글 ID
 * 
 * Put params
 * @param string subject 게시글 제목
 * @param string content 게시글 내용 (HTML)
 * -----------------------------------------------------------------------------
 */
Route::put('/board/{boardName}/modify/{id}', 'PostModifyController@put')
        ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
        ->name('board.post.modify.put');

/**
 * 게시글 삭제
 * 
 * -----------------------------------------------------------------------------
 * DELETE [/{$prefix}]/board/{boardName}/{id}
 * 
 * Route params
 * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
 * @param string boardName  게시판 영문 이름.
 * @param integer id        게시글 ID.
 * -----------------------------------------------------------------------------
 */
Route::delete('/board/{boardName}/{id}', 'PostController@delete')
        ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
        ->name('board.post.delete');




// 댓글 추가

// 댓글 수정

// 댓글 삭제

// Route::get('/boardtest',
        // 'Inium\Laraboard\Http\Controllers\TestController@index');

// Route::get('/')

/**
 * GET /board/{boardName}?page=1&st=baseball&sc=content
 * GET /board/{boardName}/{postId}?page=1&st=baseball&sc=content
 * 
 * GET /board/{boardName}/write
 * GET /board/{boardName}/modify/{postId}
 * 
 * POST /board/{boardName}
 * PUT /board/{boardName}/{postId}
 * DELETE /board/{boardName}/{postId}
 */




