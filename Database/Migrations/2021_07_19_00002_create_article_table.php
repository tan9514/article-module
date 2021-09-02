<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateArticleTable extends Migration
{
    public $tableName = "article";

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

            $table->id();
            $table->string("title")->nullable(false)->comment("文章主标题")->index("title_index");
            $table->string('sub_title')->nullable(false)->default("")->comment("文章副标题")->index("sub_title_index");
            $table->string('pic')->nullable(false)->default("")->comment("文章缩略图");
            $table->longText("content")->nullable(false)->comment("文章内容");
            $table->string('source')->nullable(false)->default("")->comment("来源");
            $table->string('author')->nullable(false)->default("")->comment("作者")->index("author_index");
            $table->string("outer_chain")->nullable(false)->default("")->comment("外链地址");
            $table->longText("keys")->nullable(false)->comment("关键词,多个以中文逗号分割");
            $table->longText("describe")->nullable(false)->comment("摘要");
            $table->unsignedInteger("read")->nullable(false)->default("0")->comment("阅读量");
            $table->unsignedInteger("virtual_read")->nullable(false)->default("0")->comment("虚拟阅读量");
            $table->unsignedInteger("agree")->nullable(false)->default("0")->comment("点赞量");
            $table->unsignedInteger("virtual_agree")->nullable(false)->default("0")->comment("虚拟点赞量");
            $table->unsignedInteger("favorite")->nullable(false)->default("0")->comment("收藏量");
            $table->unsignedInteger("virtual_favorite")->nullable(false)->default("0")->comment("虚拟收藏量");
            $table->unsignedTinyInteger("sort")->nullable(false)->default(100)->comment("排序: 升序");
            $table->tinyInteger("status")->nullable(false)->default(1)->comment("状态：0=关闭，1=开启");
            $table->timestamps();
            $table->softDeletes();
        });
        $prefix = DB::getConfig('prefix');
        $qu = "ALTER TABLE " . $prefix . $this->tableName . " comment '文章表'";
        DB::statement($qu);
    }
}
