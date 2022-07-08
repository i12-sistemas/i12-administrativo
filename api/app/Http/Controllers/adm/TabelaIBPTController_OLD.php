<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Clientes;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\RetApiController;

class TabelaIBPTController extends Controller
{
  protected $dir = '/arquivos/sistemas-down/icom/';
  
  public function index(Request $request)  {
    
  }

  public function add(Request $request)  {
    $year = date("Y"); 
    $estados = array(
      'AC' => 'Acre',
      'AL' => 'Alagoas',
      'AP' => 'Amapá',
      'AM' => 'Amazonas',
      'BA' => 'Bahia',
      'CE' => 'Ceará',
      'DF' => 'Distrito Federal',
      'ES' => 'Espirito Santo',
      'GO' => 'Goiás',
      'MA' => 'Maranhão',
      'MS' => 'Mato Grosso do Sul',
      'MT' => 'Mato Grosso',
      'MG' => 'Minas Gerais',
      'PA' => 'Pará',
      'PB' => 'Paraíba',
      'PR' => 'Paraná',
      'PE' => 'Pernambuco',
      'PI' => 'Piauí',
      'RJ' => 'Rio de Janeiro',
      'RN' => 'Rio Grande do Norte',
      'RS' => 'Rio Grande do Sul',
      'RO' => 'Rondônia',
      'RR' => 'Roraima',
      'SC' => 'Santa Catarina',
      'SP' => 'São Paulo',
      'SE' => 'Sergipe',
      'TO' => 'Tocantins',
    );
  }

  public function upload(Request $request)  {
    try {

      $semestre = isset($request->semestre) ? intVal($request->semestre) : 1;
      if (($semestre < 1) || ($semestre>2)) throw new Exception('Semestre inválido');
      $ano = isset($request->ano) ? intVal($request->ano) : intVal( date("Y"));
      $estado = isset($request->estado) ? $request->estado : 'SP';
      $arquivo = $request->hasfile('arquivo') ? $request->file('arquivo') : null;
      if (!$arquivo) throw new Exception('Nenhum arquivo informado');
      $localdir = $this->dir;
      $name = Str::replaceArray('?', [$semestre, $ano, $estado], 'tabelaibpt_sem_?_ano_?_?.csv');
      Storage::disk('public')->put($localdir . '\\' . $name, file_get_contents($arquivo) );
      // return redirect()->route('adm.tabelaibpt')->with('success', $name . ' arquivo carregado com sucesso.');
    } catch (Exception $e) {
      return back()->withErrors($e->getMessage());
    }
  }  


  public function delete($filename)  {
    try {
      if (!$filename) throw new Exception('Arquivo não informado');
      if ($filename == '') throw new Exception('Arquivo não informado');

      $arquivo = $this->dir . '\\' . $filename . '.csv';
      $disk = Storage::disk('public');

      if (!$disk->exists($arquivo)) throw new Exception('Arquivo não encontrado no diretório');
      $disk->delete($arquivo);
      return back()->with('success', 'Arquivo removido com sucesso.');
    } catch (Exception $e) {
      return back()->withErrors($e->getMessage());
    }
  }
}
