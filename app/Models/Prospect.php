<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'city',
        'phone',
        'phone_whatsapp',
        'phone_viber',
        'telegram',
        'instrument',
        'result',
        'step',
        'comment',
        'action_bot',
        'test_results',
        'bizt_results',
        'branch',
        'target_a',
        'income_want',
        'gender',
        'age',
        'req'
    ];

    protected $dates = ['created_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:H:i d.m.Y'
    ];
}
