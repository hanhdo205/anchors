<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGetanchorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('getanchor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('getrank_id');
			$table->text('anchor_text');
            $table->string('anchor_type');
			$table->text('anchor_url');
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
        Schema::dropIfExists('getanchor');
    }
}
