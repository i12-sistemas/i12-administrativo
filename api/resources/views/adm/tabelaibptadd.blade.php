@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="form-group row">
            <div class="col-md-12">
              <h6>Upload de tabela IBPT</h6>
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
          <form method="POST" action="{{ route('adm.tabelaibpt.upload') }}" enctype="multipart/form-data">
            @csrf 
            
            <div class="form-group">
              <label for="exampleFormControlFile1">Selecione as etiquetas para upload</label>
              <input type="file" class="form-control-file" name="arquivo" accept=".csv">
              <small id="emailHelp" class="form-text text-muted">Somente extensão .csv</small>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="txtSemestre">Semeste</label>
                <select class="form-control" name="semestre">
                  <option value="1">1º Semestre</option>
                  <option value="2">2º Semestre</option>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="txtAno">Ano</label>
                <select class="form-control" name="ano">
                  @for ($i = $year; $i < ($year + 10); $i++)
                  <option value="{{$i}}">{{$i}}</option>
                  @endfor
                </select>
              </div>
              <div class="form-group col-md">
                <label for="txtUF">Estado</label>
                <select class="form-control" name="estado">
                  @foreach ($estados as $key => $estado)
                  <option value="{{$key}}">{{$key . ' - ' . $estado}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button
              >
          </form>

        </div>
    </div>
</div>
@endsection
