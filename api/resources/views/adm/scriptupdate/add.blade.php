@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="form-group row">
            <div class="col-md-12">
              <h6>Cadastro de script</h6>
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
          <form method="POST" action="{{ route('adm.scripts.upload') }}" enctype="multipart/form-data">
            @csrf 
            <div class="form-group">
              <label for="exampleFormControlFile1">Selecione arquivos .SQL para upload</label>
              <input type="file" class="form-control-file" name="filenames[]" multiple accept=".sql">
              <small id="emailHelp" class="form-text text-muted">Somente extensão .sql</small>
            </div>
            <div class="form-group">
              <label for="txtstatus">Status</label>
              <select class="form-control" name="status">
                <option value="1" selected>Desenvolvimento</option>
                <option value="2">Produção</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button
              >
          </form>

        </div>
    </div>
</div>
@endsection
