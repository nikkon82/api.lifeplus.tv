<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('role')->default('guest');
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->text('avatar')->nullable();
            $table->text('photo_money')->nullable();
            $table->text('photo_auto')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('gender')->nullable();
			$table->string('who_is')->nullable()->default('Консультант LR Health & Beauty');
            $table->string('phone_whatsapp')->nullable();
            $table->string('phone_viber')->nullable();
            $table->string('telegram')->nullable();
            $table->string('fb_messenger')->nullable();
            $table->string('vkontakte')->nullable();
            $table->string('odnoklassniki')->nullable();
            $table->text('instagram')->nullable();
            $table->text('about_me')->nullable();
            $table->text('about_me_viz')->nullable();
            $table->text('about_me_biz')->nullable();
            $table->text('biz_test_dop')->nullable();
            $table->text('dop_viz')->nullable();
            $table->string('viz_design')->default('default');
            $table->string('biz_video_title')->nullable();
            $table->string('biz_video_link')->nullable();
            $table->text('promo_test')->nullable();
            $table->text('about_chat')->nullable();
            $table->text('leedbonus')->nullable();
            $table->string('lr_number')->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
