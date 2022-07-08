<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class OSLeitura extends Model
{
    public $timestamps = false;
    protected $table = 'osleitura';
    protected $primaryKey = 'id';
    protected $dates = [
        'dataleitura',
    ];  

}
