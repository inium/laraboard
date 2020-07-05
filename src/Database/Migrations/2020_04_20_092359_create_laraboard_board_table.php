<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaraboardBoardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roleTableName = $this->getRoleTableName();
        $userTableName = $this->getUserTableName();

        // 게시판 정보 저장 테이블
        Schema::create($this->getTableName(),
            function (Blueprint $table) use ($roleTableName,
                                             $userTableName) {
                $table->id();
                $table->string('name')->unique()->comment('영문 이름');
                $table->string('name_ko')->unique()->comment('한글 이름');
                $table->text('description')->nullable()->comment('게시판 설명');

                $table->integer('post_point')->comment('게시글 작성 포인트');
                $table->integer('comment_point')->comment('댓글 작성 포인트');

                $table->integer('post_rows_per_page')
                      ->comment('게시글 페이징 수');
                $table->integer('comment_rows_per_page')
                      ->comment('댓글 페이징 수');

                $table->unsignedBigInteger('min_post_read_role_id')
                      ->nullable()
                      ->comment('게시글 읽기 사용자 권한 ID');
                $table->unsignedBigInteger('min_post_write_role_id')
                      ->comment('게시글 쓰기, 수정, 삭제 사용자 최소 권한 ID');
                $table->unsignedBigInteger('min_comment_read_role_id')
                      ->nullable()
                      ->comment('댓글 읽기 사용자 최소 권한 ID');
                $table->unsignedBigInteger('min_comment_write_role_id')
                      ->comment('댓글 쓰기, 수정, 삭제 사용자 최소 권한 ID');

                $table->unsignedBigInteger('create_user_id')
                      ->comment('게시판 생성한 게시판 사용자의 ID');

                $table->timestamps();
                $table->softDeletes();

                // Foreign Key 생성
                $table->foreign('min_post_read_role_id')
                      ->references('id')
                      ->on($roleTableName);
                $table->foreign('min_post_write_role_id')
                      ->references('id')
                      ->on($roleTableName);
                $table->foreign('min_comment_read_role_id')
                      ->references('id')
                      ->on($roleTableName);
                $table->foreign('min_comment_write_role_id')
                      ->references('id')
                      ->on($roleTableName);

                $table->foreign('create_user_id')
                      ->references('id')
                      ->on($userTableName);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->getTableName());
    }

    /**
     * 테이블 이름을 반환한다.
     *
     * @return string
     */
    private function getTableName()
    {
        return config('laraboard.board.table_name.board');
    }

    /**
     * 게시판 권한 테이블 이름을 반환한다.
     *
     * @return string
     */
    private function getRoleTableName()
    {
        return config('laraboard.board.table_name.role');
    }

    /**
     * Board 사용자 테이블 이름을 반환한다.
     *
     * @return string
     */
    private function getUserTableName()
    {
        return config('laraboard.board.table_name.user');
    }
}
