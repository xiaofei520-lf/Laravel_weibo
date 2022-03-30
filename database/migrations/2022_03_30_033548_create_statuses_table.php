<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content')->comment('微博的内容');
            //index() 字段加上索引 普通索引
            $table->integer('user_id')->index()->comment('微博发布者的个人 id');
            //为创建时间添加索引
            $table->index(['created_at']);
            /*timestamps 方法会为微博数据表生成一个微博创建时间字段 created_at
            和一个微博更新时间字段 updated_at 微博的创建时间添加索引的目的是，
            后面我们会根据微博的创建时间进行倒序输出，并在页面上进行显示，使新建
            的微博能够排在比较靠前的位置。*/
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
        Schema::dropIfExists('statuses');
    }
};
