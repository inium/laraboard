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
        // 댓글 테이블
        Schema::create("lb_board_post_comments", function (Blueprint $table) {
            $table->id();
            $table
                ->string("ip_address")
                ->nullable()
                ->comment("작성자 IP Address");
            $table
                ->text("user_agent")
                ->nullable()
                ->comment("작성자 User Agent");
            $table
                ->enum("device_type", [
                    "desktop", // PC
                    "tablet", // 태블릿
                    "mobile", // 모바일
                    "others", // 그 외
                ])
                ->nullable()
                ->comment("작성자 Device Type");
            $table
                ->string("os_name")
                ->nullable()
                ->comment("작성자 OS 이름");
            $table
                ->string("os_ver")
                ->nullable()
                ->comment("작성자 OS 버전");
            $table
                ->string("browser_name")
                ->nullable()
                ->comment("작성자 Browser 이름");
            $table
                ->string("browser_ver")
                ->nullable()
                ->comment("작성자 Browser 버전");
            $table->text("content")->comment("댓글 본문");
            $table
                ->text("stripped_content")
                ->comment("검색용 tag제외 댓글 본문");
            $table->integer("point")->comment("댓글 부여 포인트");
            $table
                ->unsignedBigInteger("parent_comment_id")
                ->nullable()
                ->comment("부모 댓글 ID");
            $table->unsignedBigInteger("board_id")->comment("게시판 ID");
            $table->unsignedBigInteger("post_id")->comment("게시글 ID");
            $table->unsignedBigInteger("wrote_user_id")->comment("작성자 ID");
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key 설정
            $table
                ->foreign("parent_comment_id")
                ->references("id")
                ->on("lb_board_post_comments");
            $table
                ->foreign("post_id")
                ->references("id")
                ->on("lb_board_posts");
            $table
                ->foreign("board_id")
                ->references("id")
                ->on("lb_board");
            $table
                ->foreign("wrote_user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("lb_board_post_comments");
    }
};
