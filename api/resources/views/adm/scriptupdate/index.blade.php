@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="form-group row">
            <div class="col-md-6">
              <span>Último <strong>{{$ultimo}}</strong>, primeiro {{$primeiro}} - Total: {{$qtdetotal}}</span>
              @if($diferenca != 0 )
              <span class="text-danger"> - Faltando de arquivos: {{$diferenca}}</span>
              @endif

              {{-- <form method="get" id="formdiretorio" name="formdiretorio" action="{{ route('adm.etiquetas') }}" >
                <select class="form-control" name="diretorio" onchange="Consultar()">
                  <option value="">Diretório raiz</option>
                  @foreach ($diretorios as $diretorio)
                    <option value="{{$diretorio['dir']}}" {{$diretorio['dir'] == $diretorioatual ? 'selected' : ''}}>
                      {{$diretorio['cliente'] ? $diretorio['cliente']->nome : ''}} {{$diretorio['dir']}} - {{$diretorio['qtde'] . ' etiqueta' . ($diretorio['qtde'] > 1 ? 's' : '')}}
                    </option>
                  @endforeach
                </select>
              </form> --}}
            </div>
            <div class="col-md-6">
              <a  class="btn btn-primary" href="{{ route('adm.scripts.add') }}">Novo script</a>
              <button onclick="send()" class="btn btn-danger" >Remover selecionados</button>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-4">
              <h6>Listagem de scripts de update do iCom</h6>
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
          @if(isset($lista))
            <form method="POST" id="formsel" name="formsel" action="{{ route('adm.scripts.delete.multi') }}" >
              @csrf 
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th scope="col"><input type="checkbox" name="select-all" id="select-all" ></th>
                    <th scope="col">Nº Versão</th>
                    <th scope="col">Status</th>
                    <th scope="col">Nome do arquivo</th>
                    <th scope="col">Última alteração</th>
                    <th scope="col">Ações</th>
                  </tr>
                </thead>
                <tbody>
            @foreach ($lista as $itemdalista)
                @if($itemdalista['status'] === false)
                <tr class="table-danger">
                  <td scope="col">-</td>
                  <td>{{$itemdalista['numero']}}</td>
                  <td>Indisponível</td>
                  <td colspan="4">Quebra de sequência</td>
                </tr>
                @else
                <?php $item = $itemdalista['arquivo'] ?>
                <tr>
                  <td><input type="checkbox" class="selecionado"  id="selecionado" name="selecionado[]" value="{{$item->md5file}}"></td>
                  <td><a href="{{$item->urldownload}}" target="_blank" title="Download da etiqueta">{{$item->nordem}}</a></td>
                  <td><a href="{{$item->urldownload}}" target="_blank" title="Download da etiqueta">{{$item->statusdesc}}</a></td>
                  <td><a href="{{$item->urldownload}}" target="_blank" title="Download da etiqueta">{{$item->filename}}</a></td>
                  <td>
                    <div class="small"><i class="fa fa-plus"></i>{{$item->created_at->format('d/m/Y - H:i:s')}}</div>
                    <div class="small"><i class="fa fa-edit"></i>{{$item->updated_at->format('d/m/Y - H:i:s')}}</div>
                  <td><div class="text-right"><a  class="btn btn-danger btn-sm" href="{{ route('adm.scripts.delete', ['md5' => $item->md5file ]) }}">Remover</a></div></td>
                </tr>
                @endif
            @endforeach
                </tbody>
              </table>
            </form>
            <div class="form-group row">
              <div class="col-md-12">
                <div class="text-center">
                {{ $arquivos->links() }}
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