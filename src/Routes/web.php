<?php

/**
 * 게시글 목록, 검색
 * -------------------------------------------------------------------------
 * GET [/{$prefix}]/board/{boardName}?page=1&search=lorem&category=subject
 * -------------------------------------------------------------------------
 * Route params
 * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
 * @param string boardName  게시판 영문 이름.
 * 
 * Query params (Optional)
 * @param int    page       페이지 번호. 기본 1.
 * @param string search     검색어.
 * @param string category   검색 유형. 기본 total.
 *                          - total, subject, content, comment 중 1.
 */
Route::get('/board/{boardName}', 'ListController@index')
        ->name('laraboard.list.page');



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

// // 게시글 삭제
// Route::delete('/board/{boardName}/{id}',
//               'App\Http\Controllers\Laraboard\DeleteController@index')
//             ->where('id', '[0-9]+') // 게시글 ID는 숫자로 구성
//             ->name('laraboard.post.delete');

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




