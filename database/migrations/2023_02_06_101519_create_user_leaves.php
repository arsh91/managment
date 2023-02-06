<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLeaves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->constrained();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->string('type')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

        });
    }
    // Schema::create('user_attendance', function (Blueprint $table) {
    //     $table->id();
    //     $table->integer('user_id')->constrained();
    //     $table->date('date')->nullable();
    //     $table->time('in_time')->nullable();
    //     $table->time('out_time')->nullable();
    //     $table->string('notes')->nullable();
    //     $table->t

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_leaves');
    }
}