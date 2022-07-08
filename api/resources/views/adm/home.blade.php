@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <a  class="btn btn-primary" href="{{ route('adm.etiquetas') }}">Etiquetas</a>
          <a  class="btn btn-primary" href="{{ route('adm.tabelaibpt') }}">Tabela IBPT</a>
          <a  class="btn btn-primary" href="{{ route('adm.clientes.licencas') }}">Licenças</a>
          <a  class="btn btn-primary" href="{{ route('adm.scripts.listagem') }}">Scripts de atualização</a>
          <a  class="btn btn-primary" href="{{ route('adm.logs.index') }}">Logs de Erros</a>
          <a  class="btn btn-primary" href="{{ route('adm.logs.clientes') }}">Logs de Erros por clientes</a>
          <a  class="btn btn-primary" href="{{ route('adm.backup.dash') }}">Backup</a>
    </div>
</div>
@endsection
