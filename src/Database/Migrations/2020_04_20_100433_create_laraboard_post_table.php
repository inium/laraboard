<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaraboardPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $boardTableName = $this->getBoardTableName();
        $userTableName = $this->getUserTableName();

        Schema::create($this->getTableName(),
            function (Blueprint $table) use ($boardTableName,
                                             $userTableName) {
                $table->id();
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
                $table->boolean('notice')->comment('공지글 여부');
                $table->string('subject')->comment('게시글 제목');
                $table->text('content')->comment('게시글 본문');
                $table->text('content_pure')
                      ->comment('tag제외 게시글 본문. 검색용.');
                $table->text('attachment_json')
                      ->nullable()
                      ->comment('JSON 형태의 첨부파일');
                $table->text('tag_json')
                      ->nullable()
                      ->comment('JSON 형태의 게시글 태그');
                $table->integer('view_count')->comment('조회수');
                $table->integer('point')->comment('게시글 부여 포인트');
                $table->unsignedBigInteger('board_id')->comment('게시판 ID');
                $table->unsignedBigInteger('wrote_user_id')
                      ->comment('작성자 ID');
                $table->timestamps();
                $table->softDeletes();

                // Foreign Key 설정
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
        return config('laraboard.board.table_name.post');
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
     * 게시판 사용자 정보 저장 테이블을 반환한다.
     *
     * @return string
     */
    private function getUserTableName()
    {
        return config('laraboard.board.table_name.user');
    }
}
