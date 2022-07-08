<?php

namespace App\Http\Controllers\API\V1\empresas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\RetornoApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\models\i12ScriptUpdate;

class ScriptsUpdateController extends Controller
{
  public function list(Request $request) {

    $ret = new RetornoApiController;
    try{
        $cnpj = isset($request->cnpj) ? $request->cnpj : '';

        $versaoinicial = isset($request->versaoinicial) ? intVal($request->versaoinicial) : 0;
        $versaofinal = isset($request->versaofinal) ? intVal($request->versaofinal) : 0;
        $status = isset($request->status) ? intVal($request->status) : 1; //padrao dev

        $arquivos = i12ScriptUpdate::whereRaw('if(?=2, status=2, status in (1,2))', [$status])
                        ->where(function($query) use ($versaoinicial, $versaofinal) {
                          if (($versaoinicial > 0) && ($versaofinal > 0)) {
                            $query->whereBetween('nordem', [$versaoinicial, $versaofinal]);
                          } else if (($versaoinicial > 0) && !($versaofinal > 0)) {
                            $query->where('nordem', '>=', $versaoinicial);
                          } else if (!($versaoinicial > 0) && ($versaofinal > 0)) {
                            $query->where('nordem', '<=', $versaofinal);
                          };
                        })
                        ->orderBy('nordem')
                        ->get();
      
        //checa se existe quebra de sequencia
        $nfirst = -1;
        $nlast = -1;
        if (count($arquivos) > 0) {
          $nlast  = $arquivos[count($arquivos)-1]->nordem;
          $nfirst = $arquivos[0]->nordem;
          for ($i=$nlast; $i >= $nfirst; $i--) { 
            $status =  $arquivos->search(function ($item, $key) use ($i) {
              return $item->nordem == $i;
            });
            if ($status === false) throw new Exception('Existe quebra de sequencia no script ' . $i);
          }
        }
        //checa se existe quebra de sequencia

        $dados = [];
        foreach ($arquivos as $arquivo) {
          $dados[] = [
            'md5file'         =>  $arquivo->md5file,
            'urldownload'     =>  $arquivo->urldownload,
            'nordem'          =>  $arquivo->nordem,
            'status'          =>  $arquivo->status,
            'updatedat'       =>  $arquivo->updated_at->format('Y-m-d'),
          ];
        };
        
        $ret->ok = true;
        $ret->rows = $dados;
    } catch (Exception $e) {
        $ret->msg = $e->getMessage();
    }

    return $ret->toJson();
  }


  public function allowed(Request $request) {

    $ret = new RetornoApiController;
    try{
        $cnpj = isset($request->cnpj) ? $request->cnpj : '';

        $versaoatual = isset($request->versaoatual) ? intVal($request->versaoatual) : 0;
        $status = isset($request->status) ? intVal($request->status) : 1; //padrao dev

        $arquivos = i12ScriptUpdate::whereRaw('if(?=2, status=2, status in (1,2))', [$status])
                        ->where('nordem', '>', $versaoatual)
                        ->orderBy('nordem')
                        ->get();

        //checa se existe quebra de sequencia
        $nfirst = -1;
        $nlast = -1;
        if (count($arquivos) > 0) {
          $nlast  = $arquivos[count($arquivos)-1]->nordem;
          $nfirst = $arquivos[0]->nordem;
          for ($i=$nlast; $i >= $nfirst; $i--) { 
            $status =  $arquivos->search(function ($item, $key) use ($i) {
              return $item->nordem == $i;
            });
            if ($status === false) throw new Exception('Existe quebra de sequencia no script ' . $i);
          }
        }
        //checa se existe quebra de sequencia  
                              
        $dados = [];
        $ultimo = 0;
        foreach ($arquivos as $arquivo) {
          $dados[] = [
            'nordem'      =>  $arquivo->nordem
          ];
          $ultimo = $arquivo->nordem;
        };

        $ret->ok = true;
        $ret->id = $ultimo;
        $ret->rows = $dados;
    } catch (Exception $e) {
        $ret->msg = $e->getMessage();
    }

    return $ret->toJson();
  }


  
}
