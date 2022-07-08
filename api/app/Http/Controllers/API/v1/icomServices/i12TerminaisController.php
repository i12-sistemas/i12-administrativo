<?php

namespace App\Http\Controllers\api\v1\icomServices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\i12Terminal;
use App\models\i12TerminalCNPJ;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\RetApiController;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class i12TerminaisController extends Controller
{

  public function update(Request $request)  {
    $ret = new RetApiController;
    try {
      $empresa= session('empresa');
      if (!$empresa) throw new Exception('Nenhuma empresa identificada');

      $rules = [
        'lista' => ['string', 'min:1', 'required'],
      ];
      $messages = [
          'size' => 'O campo :attribute, deverá ter :max caracteres.',
          'integer' => 'O conteudo do campo :attribute deverá ser um número inteiro.',
          'unique' => 'O conteudo do campo :attribute já foi cadastrado.',
          'required' => 'O conteudo do campo :attribute é obrigatório.',
      ];

      $validator = Validator::make($request->all(), $rules, $messages);
      if ($validator->fails()) {
          $msgs = [];
          $errors = $validator->errors();
          foreach ($errors->all() as $message) {
              $msgs[] = $message;
          }
          $ret->data = $msgs;
          throw new Exception(join("; ", $msgs));
      }

      $lista = json_decode($request->lista);
      if (!$lista) throw new Exception('Nenhum lista de terminal informada');
      if (count($lista)<=0) throw new Exception('Nenhum lista de terminal informada');

      try{
        DB::beginTransaction();

        foreach ($lista as $terminal) {
          $row = i12Terminal::find($terminal->serialnumber);
          if (!$row) {
            $row = new i12Terminal();
            $row->serialnumber = $terminal->serialnumber;
          }
          if (isset($terminal->mb_manufacter)) $row->mb_manufacter = $terminal->mb_manufacter;
          if (isset($terminal->mb_product)) $row->mb_product = $terminal->mb_product;
          if (isset($terminal->mb_version)) $row->mb_version = $terminal->mb_version;
          if (isset($terminal->mb_serialnumber)) $row->mb_serialnumber = $terminal->mb_serialnumber;

          if (isset($terminal->so_name)) $row->so_name = $terminal->so_name;
          if (isset($terminal->so_computador)) $row->so_computador = $terminal->so_computador;
          if (isset($terminal->so_homedrive)) $row->so_homedrive = $terminal->so_homedrive;
          if (isset($terminal->so_homepath)) $row->so_homepath = $terminal->so_homepath;
          if (isset($terminal->so_logonserver)) $row->so_logonserver = $terminal->so_logonserver;

          if (isset($terminal->proc_familiy)) $row->proc_familiy = $terminal->proc_familiy;
          if (isset($terminal->proc_version)) $row->proc_version = $terminal->proc_version;
          if (isset($terminal->proc_speedcurrentmhz)) $row->proc_speedcurrentmhz = $terminal->proc_speedcurrentmhz;
          if (isset($terminal->proc_coreenabled)) $row->proc_coreenabled = $terminal->proc_coreenabled;
          if (isset($terminal->proc_threadscount)) $row->proc_threadscount = $terminal->proc_threadscount;
          if (isset($terminal->proc_socket)) $row->proc_socket = $terminal->proc_socket;
          if (isset($terminal->proc_manufacturer)) $row->proc_manufacturer = $terminal->proc_manufacturer;

          if (isset($terminal->mem_sizemb)) $row->mem_sizemb = $terminal->mem_sizemb;
          if (isset($terminal->mem_speedmhz)) $row->mem_speedmhz = $terminal->mem_speedmhz;
          if (isset($terminal->mem_formfactor)) $row->mem_formfactor = $terminal->mem_formfactor;
          if (isset($terminal->mem_type)) $row->mem_type = $terminal->mem_type;
          
          if (isset($terminal->created_at)) $row->cliente_created_at = $terminal->created_at;
          if (isset($terminal->updated_at)) $row->cliente_updated_at = $terminal->updated_at;

          $row->ativo = (isset($terminal->ativo)) ? ($terminal->ativo ? 1 : 0) : 1 ;
     
          $row->save();


          $terminalcnpj = i12TerminalCNPJ::where('cnpj', '=', $empresa->cnpj)->where('serialnumber', '=', $row->serialnumber)->first();
          if (!$terminalcnpj) {
            $terminalcnpj = new i12TerminalCNPJ();
            $terminalcnpj->cnpj = $empresa->cnpj;
            $terminalcnpj->serialnumber = $terminal->serialnumber;
          }
          $terminalcnpj->save();

        }

        
        
        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
      }          

      $ret->ok=True;
    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

  }  

}
