@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <form method="get" id="formdiretorio" name="formconsulta" action="{{ route('adm.clientes.licencas') }}" >
          <div class="form-group row">
            <div class="col-md-3">
              <div class="form-group row" >
                <input type="text" class="form-control" value="{{$q}}" onchange="Consultar()" name="q" placeholder="Digite aqui para consultar">
              </div>
            </div>
            <div class="col-md-3">
              <label for="ckvencidos"> Vencidos</label>
              <input type="checkbox" name="vencidos" id="ckvencidos" onchange="Consultar()" {{request()->vencidos ? 'checked' : ''}} >
            </div>
            <div class="col-md-6">
              <a  class="btn btn-secondary" href="{{ route('adm.clientes.licencas') }}">Todos</a>
              <button class="btn btn-primary" >Gerar</button>
            </div>
          </div>
        </form>
          <div class="form-group row">
            <div class="col-md-4">
              <h6>Listagem de etiquetas</h6>
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
          @if($errors->any())
            <div class="form-group row">
              <div class="col-md-12">
                <div class="alert alert-danger" role="alert">
                  <strong>{{$errors->first()}}</strong>
                </div>
              </div>
            </div>
          @endif
          @if(isset($clientes))
            <form method="POST" id="formsel" name="formsel" action="{{ route('adm.etiquetas.delete.multi') }}" >
              @csrf 
              <table class="table table-sm table-striped">
                <thead>
                  <tr>
                    <th scope="col"><input type="checkbox" name="select-all" id="select-all" ></th>
                    <th scope="col">Código</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Emissão</th>
                    <th scope="col">Vencimento</th>
                    <th scope="col">Bloqueio total</th>
                    <th scope="col">Fatura<br>Próx. vencto</th>
                  </tr>
                </thead>
                <tbody>
            @foreach ($clientes as $cliente)
                <tr >
                  <td><input type="checkbox" class="selecionado"  id="selecionado" name="selecionado[]" value="{{$cliente->codcliente}}"></td>
                  <td>{{$cliente->codcliente}}</td>
                  <td>{{$cliente->nome}}</td>
                  <td><div>{{ $cliente->licencaatual ? $cliente->licencaatual->dhemissao->format('d/m/Y') : '-'}}</div></td>
                  <td class="{{  $cliente->licencaatual ? ($cliente->licencaatual->expirado ? 'table-danger' : '') : ''}}">
                      <div>{{  $cliente->licencaatual ? $cliente->licencaatual->datavencimento->format('d/m/Y') : ''}}</div>
                  </td>
                  <td class="{{  $cliente->licencaatual ? ($cliente->licencaatual->bloqueado ? 'table-danger' : '') : ''}}">
                      <div>{{ $cliente->licencaatual ? $cliente->licencaatual->databloqueiototal->format('d/m/Y') : '-'}}</div>
                  </td>
                  <td>
                    <?php
                      $fatura = $cliente->faturasemaberto ? $cliente->faturasemaberto()->orderBy('vencimento')->first() : null;
                    ?>
                    <div>{{ $fatura ? $fatura->vencimento->format('d/m/Y') : '-'}}</div>
                </td>
                  
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