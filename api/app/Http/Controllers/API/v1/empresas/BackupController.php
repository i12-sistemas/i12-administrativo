<?php

namespace App\Http\Controllers\API\v1\empresas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RetornoApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\models\i12clibackuplog;
use App\models\Clientes;

class BackupController extends Controller
{
    
    public function listcliente(Request $request) {
        $ret = new RetornoApiController;
        try{
            $ativo =intval(isset($request->ativo) ? $request->ativo : '1');
            // $dhlocal = isset($request->dhlocal) ? $request->dhlocal : '';
            // if($dhlocal=='') throw new Exception('Data e hora do backup invalida.');
            // try{
            //     if (!Carbon::createFromFormat('Y-m-d H:i:s', $dhlocal)) {
            //         throw new Exception('Data e hora do backup inválida. ' . $dhlocal);
            //     }
            // } catch (Exception $e) {
            //     throw new Exception('Data e hora do backup inválida. ' . $dhlocal);
            // }
            // $filename = isset($request->filename) ? $request->filename : '';
            // $temposeg = intval(isset($request->temposeg) ? $request->temposeg : '0');
            // $sizebyte = floatval(isset($request->sizebyte) ? $request->sizebyte : '0');
            // $detalhe = isset($request->detalhe) ? $request->detalhe : '';
            // $servercloud = isset($request->servercloud) ? $request->servercloud : '';
            // $versaoapp = isset($request->versaoapp) ? $request->versaoapp : '';
            // $versaoappbkp = isset($request->versaoappbkp) ? $request->versaoappbkp : '';
            // $versaobd = isset($request->versaobd) ? $request->versaobd : '';
            // $statuslocal = isset($request->statuslocal) ? $request->statuslocal : '0';
            // $statusonline = isset($request->statusonline) ? $request->statusonline : '0';
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }

        try{
            $clientes = Clientes::whereIn('i12controlabkp', [1, 2])
                        ->whereRaw('(not( coalesce(cpf,"")="" AND coalesce(cnpj,"")="" ))')
                        ->whereRaw('if(?>=0, ativo=?, true)', [$ativo, $ativo])
                        ->get();
            $dados =[];
            foreach ($clientes as $cliente) {
                $dados[] = [    'codcliente' => $cliente->codcliente, 
                                'nome' => $cliente->nome, 
                                'fantasia' => $cliente->fantasia, 
                                'ativo' => $cliente->ativo, 
                                'i12controlabkp' => $cliente->i12controlabkp, 
                                'cnpj' => ($cliente->pessoa=="F" ? '000'.$cliente->cpf : $cliente->cnpj),
                                
                                'ultimobkp_dhlocal' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->dhlocal->format('Y-m-d H:i:s'):null),
                                'ultimobkp_sizebyte' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->sizebyte : 0),
                                'ultimobkp_temposeg' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->temposeg : 0),

                                'ultimobkp_filename' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->filename : ''),
                                'ultimobkp_detalhe' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->detalhe : ''),
                                'ultimobkp_servercloud' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->servercloud : ''),
                                'ultimobkp_versaoapp' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->versaoapp : ''),
                                'ultimobkp_versaoappbkp' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->versaoappbkp : ''),
                                'ultimobkp_versaobd' => ($cliente->ultimo_backup ? $cliente->ultimo_backup->versaobd : ''),
                         ];
            }


            $ret->rows = $dados;
            $ret->ok = true;
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
        }

        return $ret->toJson();
    }       
    
    
    
    public function logadd(Request $request) {
        $ret = new RetornoApiController;
        try{
            
            $cnpj = isset($request->cnpj) ? $request->cnpj : '';
            $dhlocal = isset($request->dhlocal) ? $request->dhlocal : '';
            if($dhlocal=='') throw new Exception('Data e hora do backup invalida.');
            try{
                if (!Carbon::createFromFormat('Y-m-d H:i:s', $dhlocal)) {
                    throw new Exception('Data e hora do backup inválida. ' . $dhlocal);
                }
            } catch (Exception $e) {
                throw new Exception('Data e hora do backup inválida. ' . $dhlocal);
            }
            $filename = isset($request->filename) ? $request->filename : '';
            $temposeg = intval(isset($request->temposeg) ? $request->temposeg : '0');
            $sizebyte = floatval(isset($request->sizebyte) ? $request->sizebyte : '0');
            $detalhe = isset($request->detalhe) ? $request->detalhe : '';
            $servercloud = isset($request->servercloud) ? $request->servercloud : '';
            $versaoapp = isset($request->versaoapp) ? $request->versaoapp : '';
            $versaoappbkp = isset($request->versaoappbkp) ? $request->versaoappbkp : '';
            $versaobd = isset($request->versaobd) ? $request->versaobd : '';
            $statuslocal = isset($request->statuslocal) ? $request->statuslocal : '0';
            $statusonline = isset($request->statusonline) ? $request->statusonline : '0';
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }

        try{
            DB::beginTransaction();
            $log = new i12clibackuplog;
            $log->dhlocal = $dhlocal;
            $log->filename = $filename;
            $log->sizebyte = $sizebyte;
            $log->temposeg = $temposeg;
            $log->dhlocal = $dhlocal;
            $log->cnpj = $cnpj;
            $log->dhcad = Carbon::now();
            $log->detalhe = $detalhe;
            $log->servercloud = $servercloud;
            $log->versaoapp = $versaoapp;
            $log->versaoappbkp = $versaoappbkp;
            $log->versaobd = $versaobd;
            $log->statuslocal = $statuslocal;
            $log->statusonline = $statusonline;
            $saved = $log->save();
            if (!$saved)
                throw new Exception('Falha ao salvar log!'); 
            
            
            DB::commit();
            $ret->ok = true;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->msg = $e->getMessage();
        }

        return $ret->toJson();
    }       
    
    
}

