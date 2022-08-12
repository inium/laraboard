<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 게시판 정보 저장 테이블
        Schema::create("lb_boards", function (Blueprint $table) {
            $table->id();
            $table
                ->string("name")
                ->unique()
                ->comment("영문 이름");
            $table
                ->string("name_ko")
                ->unique()
                ->comment("한글 이름");
            $table
                ->text("description")
                ->nullable()
                ->comment("게시판 설명");
            $table->integer("post_point")->comment("게시글 작성 포인트");
            $table->integer("comment_point")->comment("댓글 작성 포인트");
            $table->integer("posts_per_page")->comment("페이지당 게시글 수");
            $table->integer("comments_per_page")->comment("페이지당 댓글 수");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("lb_boards");
    }
};
