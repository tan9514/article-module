<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateArticleCatTable extends Migration
{
    public $tableName = "article_cat";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->tableName)) $this->create();
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
            $table->bigInteger("cat_pid")->nullable(false)->default(0)->comment("父ID,0为顶级")->index("cat_pid_index");
            $table->string('name', 100)->nullable(false)->comment("文章分类名称")->unique("name_unique");
            $table->string('sub_name', 100)->nullable(false)->default("")->comment("文章分类副名称")->index("sub_name_unique");
            $table->tinyInteger("is_recommend")->nullable(false)->default(0)->comment("是否推荐：0=否，1=是");
            $table->tinyInteger("status")->nullable(false)->default(1)->comment("状态：0=关闭，1=开启");
            $table->longText("ulevels")->nullable(false)->comment("浏览权限：以会员等级为准，如果为空就是全部用户可以，反正就是包含的会员等级");
            $table->string('cat_pic')->nullable(false)->default("")->comment("文章分类缩略图");
            $table->string("cat_outer_chain")->nullable(false)->default("")->comment("外链地址");
            $table->string("cat_seo")->nullable(false)->default("")->comment("SEO标题");
            $table->longText("cat_keys")->nullable(false)->comment("关键词,多个以中文逗号分割");
            $table->longText("cat_describe")->nullable(false)->comment("描述");
            $table->longText("cat_content")->nullable(false)->comment("内容编辑器");
            $table->unsignedTinyInteger("sort")->nullable(false)->default(100)->comment("文章分类排序: 升序");
            $table->timestamps();
        });
        $prefix = DB::getConfig('prefix');
        $qu = "ALTER TABLE " . $prefix . $this->tableName . " comment '文章分类表'";
        DB::statement($qu);
    }
}
