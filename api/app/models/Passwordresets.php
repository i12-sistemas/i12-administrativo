<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Passwordresets extends Model
{
    const CREATED_AT = 'createddh';
    const UPDATED_AT = 'createddh';

    protected $table = 'password_resets';
    protected $primaryKey = 'id';
    protected $dates = [
        'createddh',
    ];

}
