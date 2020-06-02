<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaraboardRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 사용자 권한 정보 저장 테이블
        Schema::create($this->getTableName(), function (Blueprint $table){
            $table->id();
            $table->string('name')->comment('권한 이름');
            $table->string('description')->nullable()->comment('권한 설명');
            $table->boolean('is_admin')->comment('관리자 여부');
            $table->timestamps();
        });
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
        return config('laraboard.board.table_name.role');
    }
}
