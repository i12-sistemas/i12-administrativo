<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    public $timestamps = false;
    protected $table = 'permissao';
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function getdescricaoAttribute($value)
    {
        return utf8_encode($value);
    }
}
