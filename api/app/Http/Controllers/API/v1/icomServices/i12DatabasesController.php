<?php

namespace App\Http\Controllers\api\v1\icomServices;

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

  public function update(Request $request)  {
    $ret = new RetApiController;
    try {
      $empresa= session('empresa');
      if (!$empresa) throw new Exception('Nenhuma empresa identificada');

      $rules = [
        'serialnumber' => ['string', 'min:1', 'max:255', 'required'],
        'hostname' => ['string', 'min:1', 'max:255', 'required'],
        'database' => ['string', 'min:1', 'max:255', 'required'],
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

      try {
        $variables = $request->has('variablebase64') ? $request->variablebase64 : null;
        if ($variables) {
          $variables = base64_decode($variables);
          $variables = json_decode($variables);
        }
      } catch (Exception $e) {
        $variables = null;
      }  

      $add = false;
      $row = i12Databases::where('serialnumber', '=', $request->serialnumber)
                        ->where('hostname', '=', $request->hostname)
                        ->where('database', '=', $request->database)
                        ->where('cnpj', '=', $empresa->cnpj)
                        ->first();

      $add = $row ? $row->serialnumber === '' : true;
      
      $outroscnpjs = [];
      if ($request->has('cnpjoperacional')) {
        $cnpjs = trim($request->cnpjoperacional);
        if ($cnpjs !== '') {
          $cnpjs = explode(',', $cnpjs);
          foreach ($cnpjs as $value) {
            $outroscnpjs[] = ['cnpj' => $value, 'operacional' => true ];
          }
        }
      }
      if ($request->has('cnpjfiscal')) {
        $cnpjs = trim($request->cnpjfiscal);
        if ($cnpjs !== '') {
          $cnpjs = explode(',', $cnpjs);
          foreach ($cnpjs as $value) {
            $outroscnpjs[] = ['cnpj' => $value, 'operacional' => false ];
          }
        }
      }    
     
      if ($outroscnpjs ? count($outroscnpjs) === 0 : true) $outroscnpjs = null;


      

      try{
        DB::beginTransaction();

        // atualização do bd principal
        if (!$row) $row = new i12Databases();
        if ($add ) {
          $row->serialnumber = $request->serialnumber;
          $row->database = $request->database;
        }
        $row->cnpj = $empresa->cnpj;
        $row->variables = $variables ? json_encode($variables) : null;
        $row->cnpjsecundario = 0;
        $row->cnpjoperacional = 1;
        $row->hostname = $request->hostname;
        $row->ambiente = $request->has('ambiente') ? ($request->ambiente === '1' ? '1' : '2') : '2';
        if ($variables) {
          $row->version = $this->getValueVariable('version', $variables);
          $row->server_uuid = $this->getValueVariable('server_uuid', $variables);
        }
        if ($request->has('version_icomdb')) $row->version_icomdb = $request->version_icomdb;
        $row->save();
        // atualização do bd principal

        if ($outroscnpjs ? count($outroscnpjs) > 0 : false) {
          foreach ($outroscnpjs as $dado) {
            $add = false;
            $row = i12Databases::where('serialnumber', '=', $request->serialnumber)
                ->where('hostname', '=', $request->hostname)
                ->where('database', '=', $request->database)
                ->where('cnpj', '=', $dado['cnpj'])
                ->first();
      
            $add = $row ? $row->serialnumber === '' : true;


            // atualização do bd secundario
            if (!$row) $row = new i12Databases();
            if ($add ) {
              $row->serialnumber = $request->serialnumber;
              $row->database = $request->database;
            }
            $row->cnpj = $dado['cnpj'];
            $row->variables = $variables ? json_encode($variables) : null;
            $row->cnpjsecundario = 1;
            $row->cnpjoperacional = $dado['operacional'] ? 1 : 0;
            $row->hostname = $request->hostname;
            $row->ambiente = $request->has('ambiente') ? ($request->ambiente === '1' ? '1' : '2') : '2';
            if ($variables) {
              $row->version = $this->getValueVariable('version', $variables);
              $row->server_uuid = $this->getValueVariable('server_uuid', $variables);
            }
            if ($request->has('version_icomdb')) $row->version_icomdb = $request->version_icomdb;
            $row->save();
            // atualização do bd secundario            
          }
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
