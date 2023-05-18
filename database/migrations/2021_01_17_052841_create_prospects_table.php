<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(1);
            $table->string('name')->nullable();
            $table->string('city')->nullable();
            $table->string('branch')->nullable();
            $table->string('target_a')->nullable();
            $table->string('income_want')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->string('req')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_whatsapp')->nullable();
            $table->string('phone_viber')->nullable();
            $table->string('telegram')->nullable();
            $table->string('email')->nullable();
            $table->string('action_bot')->nullable();
            $table->string('test_results')->nullable();
            $table->string('bizt_results')->nullable();
            $table->string('instrument')->nullable();
            $table->string('result')->nullable();
            $table->string('step')->default('Новый');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospects');
    }
}
