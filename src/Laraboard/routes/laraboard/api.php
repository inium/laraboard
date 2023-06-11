<?php
/**
 * Laraboard API Routes
 */
Route::group(["prefix" => "v1", "as" => "v1."], function () {
    Route::apiResource(
        "board.post",
        \App\Http\Controllers\Laraboard\PostController::class
    )->parameters([
        "board" => "boardName",
        "post" => "postId",
    ]);

    Route::apiResource(
        "board.post.comment",
        \App\Http\Controllers\Laraboard\CommentController::class
    )->parameters([
        "board" => "boardName",
        "post" => "postId",
        "comment" => "commentId",
    ]);
});
