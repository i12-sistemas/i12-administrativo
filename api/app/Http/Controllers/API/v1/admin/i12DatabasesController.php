<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\i12Databases;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\RetApiController;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class i12DatabasesController extends Controller
{
  public function getValueVariable ($variable, $variables) {
    $col = array_column($variables, 'Variable_name');
    $found_key = array_search($variable, $col);
    if ($found_key) {
      return $variables[$found_key]->Value;
    } else {
      return null;
    }
  }


  public function index(Request $request)  {
    $ret = new RetApiController;
    try {
      $usuario= session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário loagdo');

      $ambiente = isset($request->ambiente) ? strval($request->ambiente) : '1';
      if ($ambiente !== '2') $ambiente = '2';
      $perpage = isset($request->perpage) ? $request->perpage : 25;
      $dataset = i12Databases::select(
                        DB::raw('i12_databases.*'), 
                        DB::raw('count(distinct db.database) as database_count'),
                        DB::raw('count(distinct db.cnpj) as cnpj_count'),
                    )
                  ->leftJoin('i12_databases as db', 'i12_databases.serialnumber', '=', 'db.serialnumber')   
                  ->where('i12_databases.ambiente', '=', $ambiente)
                  ->groupBy('i12_databases.serialnumber')
                  ->orderBy('i12_databases.created_at', 'DESC')
                  ->paginate($perpage);
      $dados = [];
      foreach ($dataset as $key => $file) {
        $dados[] = $file->exportServer(true);
      }

      $ret->collection=$dataset;
      $ret->data=$dados;
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

    
  }

}
