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
Route::get('/board/{boardName}', 'PostsController@get')
        ->name('laraboard.posts.view');

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
Route::get('/board/{boardName}/{id}', 'PostController@get')
        ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
        ->name('laraboard.post.view');

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
// Route::delete('/board/{boardName}/{id}', 'PostDeleteController@delete')
//         ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
//         ->name('laraboard.post.delete');



// // 게시글 보기 페이지
// Route::get('/board/{boardName}/{id}',
//            'App\Http\Controllers\Laraboard\PostController@index')
//             ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
//             ->name('laraboard.post.page');

// // 게시글 쓰기 페이지
// Route::get('/board/{boardName}/write',
//            'App\Http\Controllers\Laraboard\WriteController@index')
//             ->name('laraboard.write.page');

// // 게시글 수정 페이지
// Route::get('/board/{boardName}/modify',
//            'App\Http\Controllers\Laraboard\ModifyController@index')
//             ->name('laraboard.modify.page');

// // 게시글 쓰기 Post
// Route::post('/board/{boardName}',
//             'App\Http\Controllers\Laraboard\WriteController@post')
//             ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
//             ->name('laraboard.write');

// Route::put('/board/{boardName}/{id}',
//            'App\Http\Controllers\Laraboard\ModifyController@put')
//             ->name('laraboard.modify');

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
 * GET /board/{boardName}/modify
 * 
 * POST /board/{boardName}
 * PUT /board/{boardName}/{postId}
 * DELETE /board/{boardName}/{postId}
 */




