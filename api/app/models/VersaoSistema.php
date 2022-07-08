<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class VersaoSistema extends Model
{
    protected $table = null;

    public static function all($columns = array('*')) {
        // make all fillable
        Model::unguard();
        // start with empy array


        $versao = \DB::select('CALL versaosistema2();');
        return $versao[0];
    }

}
