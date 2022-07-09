<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class i12Databases extends Model
{
  protected $table = 'i12_databases';
  protected $dates = ['created_at', 'updated_at'];

  public function exportDatabase()
  {
    $dados = [
      'database'          =>  $this->database,
      'version_icomdb'    =>  $this->version_icomdb,
    ];
    if ($this->clientes) {
      $clientes = [];
      foreach ($this->clientes as $row) {
        $d  = [
          'cnpj' => $row->cnpj,
          'id' => $row->cliente->codcliente,
          'nome' => $row->cliente->nome,
          'cnpjsecundario' => $row->cnpjsecundario,
          'cnpjoperacional' => $row->cnpjoperacional
        ];
        $clientes[] = $d;
      }
      $dados['clientes'] = $clientes;
    }
    return $dados;
  }  
  public function exportServer($withDatabases = false)
  {
    $dados = [
      'serialnumber'    =>  $this->serialnumber,
      'hostname'    =>  $this->hostname,
      'version'    =>  $this->version,
      'server_uuid'    =>  $this->server_uuid,
      'created_at'       =>  $this->created_at->format('Y-m-d H:i:s'),
      'updated_at'       =>  $this->updated_at->format('Y-m-d H:i:s'),
    ];
    if ($this->database_count) $dados['database_count'] = $this->database_count;
    if ($this->cnpj_count) $dados['cnpj_count'] = $this->cnpj_count;
    if ($withDatabases) {
      $databases = [];
      foreach ($this->databases as $database) {
        $databases[] = $database->exportDatabase();
      }
      $dados['databases'] = $databases;
    }
    return $dados;
  }    


  public function cliente() {
    return $this->hasOne(Clientes::class, 'cnpj', 'cnpj');
  }    
  
  
  
  public function databases() {
    return $this->hasMany(i12Databases::class,'serialnumber', 'serialnumber')
        ->leftJoin('i12_databases as db', function ($join) {
            $join->on('i12_databases.database', '=', 'db.database')
            ->where('db.ambiente','=', $this->ambiente);
        })
        ->where('i12_databases.ambiente','=', $this->ambiente)
        ->groupBy('i12_databases.database');
  }


  public function clientes() {
    $self = $this;
    return $this->hasMany(i12Databases::class,'database', 'database')
        ->select('i12_databases.*')
        ->leftJoin('i12_databases as db', function ($join) use ($self) {
            $join->on('i12_databases.cnpj', '=', 'db.cnpj')
            ->where('db.serialnumber','=', $self->serialnumber)
            ->where('db.database','=', $self->database)
            ->where('db.ambiente','=', $self->ambiente);
        })
        ->with('cliente')
        ->groupBy('i12_databases.cnpj');
  }

}
