<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ClienteReadProjeto extends Model
{
    protected $dates = [
        'dhread',
        'dhreadlast'
    ];
    public $timestamps = false;
    private $location;
    protected $table = 'clientereadprojeto';
    protected $primaryKey = 'id';
    protected $appends = ['secondsread'];


    
    public function getsecondsreadAttribute()
    {
        return $this->dhread->diffInSeconds($this->dhreadlast);
    }    
    


}
