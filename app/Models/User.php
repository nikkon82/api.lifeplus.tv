<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'last_name',
        'gender',
        'avatar',
        'photo_money',
        'photo_auto',
        'country',
        'city',
		'who_is',
        'phone',
        'phone_whatsapp',
        'phone_viber',
        'telegram',
        'lr_number',
        'instagram',
        'fb_messenger',
        'vkontakte',
        'odnoklassniki',
        'about_me',
        'about_me_viz',
        'about_me_biz',
        'biz_video_title',
        'biz_video_link',
        'biz_test_dop',
        'dop_viz',
        'viz_design',
        'promo_test',
        'about_chat',
        'leedbonus'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmailNotification());
    }
}
