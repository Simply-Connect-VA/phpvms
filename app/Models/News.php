<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * @property int       id
 * @property int|mixed user_id
 * @property string    subject
 * @property string    body
 * @property User      user
 */
class News extends Model
{
    public $table = 'news';

    protected $fillable = [
        'user_id',
        'subject',
        'body',
    ];

    public static $rules = [
        'subject' => 'required',
        'body'    => 'required',
    ];

    /**
     * FOREIGN KEYS
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
