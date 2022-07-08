<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Http\Controllers\RetApiController;
use App\models\Empresa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TesteController extends Controller
{
 
  public function teste(Request $request)  {
    $ret = new RetApiController;
    try {
      
      $empresa = Empresa::where('ativo','=', 1)->where('padrao', '=', 1)->first();
      if (!$empresa) throw new Exception('Empresa não encontrada');
        
      
      $ret->data = [ 'empresa' => $empresa->apelido, 'datahora' => Carbon::now()->format('Y-m-d H:i:s')];
      $ret->ok = true;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

    
  }
}
