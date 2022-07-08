<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\models\Sharedprojeto;

class SharedprojetoController extends Controller
{
    public function show($hash)
    {   
        $share = Sharedprojeto::where('hash', $hash)->first();
        $share->leituras;
        $qtde = $share->leituras->count();
        $secondsread = $share->leituras->sum('secondsread');
        $lastread = $share->leituras->max('dhread');
        $ips = $share->leituras->groupBy('ip')->count();

        $r = $share->toArray();
        $r = array_add($r, 'leiturasqtde', $qtde);
        $r = array_add($r, 'leiturasseconds', $secondsread);
        if($lastread){
            $r = array_add($r, 'leituraslast', $lastread->format('Y-m-d H:i:s'));
        }
        if($ips){
            $r = array_add($r, 'leiturasipscount', $ips);
        }
        return $r;
    }      
    
}
