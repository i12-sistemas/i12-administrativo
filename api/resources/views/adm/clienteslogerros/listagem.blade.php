@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <form method="get" id="formdiretorio" name="formconsulta" action="{{ route('adm.logs.clientes') }}" >
          <div class="form-group row">
            <div class="col-md-3">
              <div class="form-group row" >
                <input type="text" class="form-control" value="{{$q}}" onchange="Consultar()" name="q" placeholder="Digite aqui para consultar">
              </div>
            </div>
            <div class="col-md-6">
              <a  class="btn btn-secondary" href="{{ route('adm.logs.clientes') }}">Limpar</a>
              <a  class="btn btn-secondary" href="{{ route('adm.logs.index') }}">Por erro</a>
            </div>
          </div>
        </form>
          <div class="form-group row">
            <div class="col-md-4">
              <h6>Listagem de Clientes</h6>
            </div>
          </div>

          @if (session()->has('success'))
            <div class="form-group row">
              <div class="col-md-12">
                <div class="alert alert-success" role="alert">
                  <strong>{{session('success')}}</strong>
                </div>
              </div>
            </div>
          @endif

          @if(isset($clientes))
            <form method="POST" id="formsel" name="formsel" action="#" >
              @csrf 
              <table class="table table-sm table-striped">
                <thead>
                  <tr>
                    <th scope="col"><input type="checkbox" name="select-all" id="select-all" ></th>
                    <th scope="col">Código</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">CNPJ</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Logs de Erros</th>
                  </tr>
                </thead>
                <tbody>
            @foreach ($clientes as $cliente)
                <tr >
                  <td><input type="checkbox" class="selecionado"  id="selecionado" name="selecionado[]" value="{{$cliente->codcliente}}"></td>
                  <td>{{$cliente->codcliente}}</td>
                  <td>{{$cliente->nome}}</td>
                  <td><div>{{ $cliente->cnpj}}</div></td>
                  <td><div>{{ $cliente->email}}</div></td>
                  <td class="text-center"><div>
                    <a  class="btn btn-secondary btn-sm" href="{{ route('adm.logclientes.logs', $cliente->cnpj) }}"><i class="fa fa-exclamation-triangle"></i> Logs</a>
                  </div></td>

                  {{-- Deixei para só para ter referência de formatação de datas
                  <td class="{{  $cliente->licencaatual ? ($cliente->licencaatual->bloqueado ? 'table-danger' : '') : ''}}">
                      <div>{{ $cliente->licencaatual ? $cliente->licencaatual->databloqueiototal->format('d/m/Y') : '-'}}</div>
                  </td> --}}
                  
                </tr>
            @endforeach
                </tbody>
              </table>
            </form>
            <div class="form-group row">
              <div class="col-md-12">
                <div class="text-center">
                {{ $clientes->links() }}
                </div>
              </div>
            </div>
            
          @endif
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
  function send() {
    document.getElementById("formsel").submit();
  };
  function Consultar() {
    document.getElementById("formdiretorio").submit();
  };
  // Listen for click on toggle checkbox
  $('#select-all').click(function(event) {   
      if(this.checked) {
          // Iterate each checkbox
          $('.selecionado').each(function() {
              this.checked = true;                        
          });
      } else {
          $('.selecionado').each(function() {
              this.checked = false;                       
          });
      }
  });
</script>
@endsection