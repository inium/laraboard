<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaraboardCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = $this->getTableName();
        $boardTableName = $this->getBoardTableName();
        $postTableName = $this->getPostTableName();
        $userTableName = $this->getUserTableName();

        Schema::create($tableName,
            function (Blueprint $table) use ($tableName,
                                             $boardTableName,
                                             $postTableName,
                                             $userTableName) {
                $table->id();
                $table->string('ip_address')
                      ->nullable()
                      ->comment('작성자 IP Address');
                $table->string('user_agent')
                    ->nullable()
                    ->comment('작성자 User Agent');
                $table->enum('device_type', [
                    'desktop',  // PC
                    'tablet',   // 태블릿
                    'mobile',   // 모바일
                    'others'    // 그 외
                ])->comment('작성자 Device Type.');
                $table->string('os_name')
                      ->nullable()
                      ->comment('작성자 OS 이름');
                $table->string('os_version')
                      ->nullable()
                      ->comment('작성자 OS 버전');
                $table->string('browser_name')
                      ->nullable()
                      ->comment('작성자 Browser 이름');
                $table->string('browser_version')
                      ->nullable()
                      ->comment('작성자 Browser 버전');
                $table->text('content')->comment('댓글 본문');
                $table->text('content_pure')
                      ->comment('검색용 tag제외 댓글 본문');
                $table->integer('point')->comment('댓글 부여 포인트');
                $table->unsignedBigInteger('parent_comment_id')
                      ->nullable()
                      ->comment('부모 댓글 ID');
                $table->unsignedBigInteger('board_id')->comment('게시판 ID');
                $table->unsignedBigInteger('post_id')->comment('게시글 ID');
                $table->unsignedBigInteger('wrote_user_id')
                      ->comment('작성자 ID');
                $table->timestamps();
                $table->softDeletes();
                
                // Foreign Key 설정
                $table->foreign('parent_comment_id')
                      ->references('id')
                      ->on($tableName);
                $table->foreign('post_id')
                      ->references('id')
                      ->on($postTableName);
                $table->foreign('board_id') 
                      ->references('id')
                      ->on($boardTableName);
                $table->foreign('wrote_user_id')
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
        return config('laraboard.board.table_name.comment');
    }

    /**
     * 게시판 테이블 이름을 반환한다.
     *
     * @return string
     */
    private function getBoardTableName()
    {
        return config('laraboard.board.table_name.board');
    }

    /**
     * 게시글 테이블 이름을 반환한다.
     *
     * @return string
     */
    private function getPostTableName()
    {
        return config('laraboard.board.table_name.post');
    }

    private function getUserTableName()
    {
        return config('laraboard.board.table_name.user');
    }
}
