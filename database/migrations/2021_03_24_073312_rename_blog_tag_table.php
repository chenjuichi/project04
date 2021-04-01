<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBlogTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        //Schema::rename($currentTableName, $newTableName);
        $currentTableName = 'blog_tag';
        $newTableName = 'blogs_tags';
        Schema::rename($currentTableName, $newTableName);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
