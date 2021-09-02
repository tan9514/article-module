<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateArticleAttrsTable extends Migration
{
    public $tableName = "article_attrs";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable($this->tableName)) $this->create();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }

    /**
     * 执行创建表
     */
    private function create()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';      // 设置存储引擎
            $table->charset = 'utf8';       // 设置字符集
            $table->collation  = 'utf8_general_ci';       // 设置排序规则

            $table->unsignedBigInteger('article_id')->nullable(false)->comment("文章ID")->index("article_id_index");
            $table->unsignedBigInteger('attr_id')->nullable(false)->comment("文章属性ID")->index("uid_index");
            $table->timestamps();

            // 设置外键
            $table->foreign('article_id', $this->tableName . "_ibfk_1")->references('id')->on('article');
            $table->foreign('attr_id', $this->tableName . "_ibfk_2")->references('id')->on('article_attr');
        });
        $prefix = DB::getConfig('prefix');
        $qu = "ALTER TABLE " . $prefix . $this->tableName . " comment '文章属性关联表'";
        DB::statement($qu);
    }
}
