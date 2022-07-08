@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="form-group row">
            <div class="col-md-12">
              <h6>Cadastro de nova etiquetas</h6>
            </div>
          </div>
          @if ($errors->any())
            <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
            </div>
          @endif
          @if (session()->has('success'))
          <div class="form-group row">
            <div class="col-md-12">
              <div class="alert alert-success" role="alert">
                <strong>{{session('success')}}</strong>
              </div>
            </div>
          </div>
          @endif
          <form method="POST" action="{{ route('adm.etiquetas.upload') }}" enctype="multipart/form-data">
            @csrf 
            <div class="form-group">
              <label for="exampleFormControlFile1">Selecione as etiquetas para upload</label>
              <input type="file" class="form-control-file" name="filenames[]" multiple accept=".eti">
              <small id="emailHelp" class="form-text text-muted">Somente extensão .eti</small>
            </div>
            <div class="form-group">
              <label for="txtdiretorio">Diretório</label>
              <select class="form-control" name="diretorio">
                <option value="">Diretório raiz</option>
                @foreach ($clientes as $cli)
                <option value="{{$cli->pessoa == 'F' ? '000' . $cli->cpf : $cli->cnpj }}">{{$cli->nome . ' | ' . $cli->fantasia }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button
              >
          </form>

        </div>
    </div>
</div>
@endsection
