@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="form-group row">
            <div class="col-md-6">
              <form method="get" id="formdiretorio" name="formdiretorio" action="{{ route('adm.etiquetas') }}" >
                <select class="form-control" name="diretorio" onchange="Consultar()">
                  <option value="">Diretório raiz</option>
                  @foreach ($diretorios as $diretorio)
                    <option value="{{$diretorio['dir']}}" {{$diretorio['dir'] == $diretorioatual ? 'selected' : ''}}>
                      {{$diretorio['cliente'] ? $diretorio['cliente']->nome : ''}} {{$diretorio['dir']}} - {{$diretorio['qtde'] . ' etiqueta' . ($diretorio['qtde'] > 1 ? 's' : '')}}
                    </option>
                  @endforeach
                </select>
              </form>
            </div>
            <div class="col-md-6">
              <a  class="btn btn-primary" href="{{ route('adm.etiquetas.add') }}">Nova etiqueta</a>
              <button onclick="send()" class="btn btn-danger" >Remover selecionados</button>
            </div>
          </div>
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
          @if(isset($etiquetas))
            <form method="POST" id="formsel" name="formsel" action="{{ route('adm.etiquetas.delete.multi') }}" >
              @csrf 
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th scope="col"><input type="checkbox" name="select-all" id="select-all" ></th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Última alteração</th>
                    <th scope="col">Ações</th>
                  </tr>
                </thead>
                <tbody>
            @foreach ($etiquetas as $item)
                <tr>
                  <td><input type="checkbox" class="selecionado"  id="selecionado" name="selecionado[]" value="{{$item->md5file}}"></td>
                  <td><a href="{{$item->urldownload}}" target="_blank" title="Download da etiqueta">{{$item->nome}}</a></td>
                  <td>{{$item->updated_at->format('d/m/Y H:i:s')}}</td>
                  <td><div class="text-right"><a  class="btn btn-danger btn-sm" href="{{ route('adm.etiquetas.delete', ['md5' => $item->md5file ]) }}">Remover</a></div></td>
                </tr>
            @endforeach
                </tbody>
              </table>
            </form>
            <div class="form-group row">
              <div class="col-md-12">
                <div class="text-center">
                {{ $etiquetas->links() }}
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