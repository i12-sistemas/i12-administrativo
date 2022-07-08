<?php

namespace App\Http\Controllers\painelcliente\icom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\RetornoApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\models\i12Etiquetas;

class EtiquetasController extends Controller
{

  protected $dir = '';
  protected $storagedisk = 's3';

  public function __construct () {
    $this->dir = env('STORAGE_DIR_ETIQUETA','');
    $this->storagedisk = ENV('STORAGE_MODO', 's3');
  }

  public function index(Request $request)  {
    $ret = new RetornoApiController;
    try {
      $localdir = $this->dir;
      $diretorio = isset($request->diretorio) ? $request->diretorio : '';
      if ($diretorio != '') $localdir = $localdir . "/" . $diretorio;
      
      $etiquetas = i12Etiquetas::where('ativo', '=', '1')
                    ->where('directory', '=', $localdir)
                    ->orderBy('nome')
                    ->get();
      $lista = [];
       foreach ($etiquetas as $etiqueta) {
        $lista[] = [
          'url'       =>  $etiqueta->urldownload,
          'name'      =>  $etiqueta->nome,
          'md5'       =>  $etiqueta->md5file,
          'updated_at' =>  $etiqueta->updated_at->format('Y-m-d H:i:s'),
        ];
      }
      $ret->ok = true;
      $ret->rows = $lista;
    } catch (Exception $e) {
        $ret->msg = $e->getMessage();
    }
    return $ret->toJson();

  }

}
