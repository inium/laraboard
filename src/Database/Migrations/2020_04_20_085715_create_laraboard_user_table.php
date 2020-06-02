<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaraboardUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roleTableName = $this->getBoardRoleTableName();
        $authUserTableName = $this->getAuthUserTableName();
        $nicknameUnique = $this->getNicknameUnique();

        // 사용자 게시판 권한 정보 저장 테이블
        Schema::create($this->getTableName(),
            function (Blueprint $table) use ($roleTableName,
                                             $authUserTableName,
                                             $nicknameUnique) {
                $table->id();
                $table->unsignedBigInteger('user_id')
                      ->unique()
                      ->comment('사용자 ID');
                $table->unsignedBigInteger('board_user_role_id')
                      ->comment('게시판 사용자 권한 ID');

                // 닉네임 중복 방지 여부
                if ($nicknameUnique) {
                    $table->string('nickname')->unique()->comment('닉네임');
                }
                else {
                    $table->string('nickname')->comment('닉네임');
                }
                $table->text('introduce')->nullable()->comment('자기소개');
                $table->text('thumbnail_path')
                      ->nullable()
                      ->comment('썸네일 저장 경로');
                $table->timestamps();
                $table->softDeletes();

                // Set foreign key
                $table->foreign('user_id')
                      ->references('id')
                      ->on($authUserTableName);

                $table->foreign('board_user_role_id')
                      ->references('id')
                      ->on($roleTableName);
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
        return config('laraboard.board.table_name.user');
    }

    /**
     * 게시판 권한 테이블 이름을 반환한다.
     *
     * @return string
     */
    private function getBoardRoleTableName()
    {
        return config('laraboard.board.table_name.role');
    }

    /**
     * Laravel Auth의 사용자 테이블 이름을 반환한다.
     * - Default는 users
     *
     * @return string
     */
    private function getAuthUserTableName()
    {
        $authUser = config('auth.providers.users.model');

        $class = new ReflectionClass($authUser);
        $obj = $class->newInstance();
        $method = $class->getMethod('getTable');

        return $method->invoke($obj);
    }

    /**
     * 게시판에서 사용할 사용자 닉네임이 unique인지 아닌지 여부를 반환한다.
     *
     * @return boolean
     */
    private function getNicknameUnique()
    {
        return config('laraboard.board.nickname_unique', 'users');
    }
}
