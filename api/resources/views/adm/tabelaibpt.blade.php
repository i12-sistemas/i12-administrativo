@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="form-group row">
            <div class="col-md-6">
              <a  class="btn btn-primary" href="{{ route('adm.tabelaibpt.add') }}">Incluir novo arquivo (descontinuado)</a>
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
          <div class="form-group row">
            <div class="col-md-4">
              <h6>Arquivos disponíveis (storage descontinuado)</h6>
            </div>
          </div>
          @if(isset($lista))
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th scope="col">Nome do arquivo</th>
                    <th scope="col"><div class="text-right">Ações</div></th>
                  </tr>
                </thead>
                <tbody>
            @foreach ($lista as $item)
                <tr>
                  <td><a href="{{$item['url']}}" target="_blank" title="Download do arquivo">{{$item['name']}}</a></td>
                  <td><div class="text-right"><a  class="btn btn-danger btn-sm" href="{{ route('adm.tabelaibpt.delete', ['filename' => $item['name'] ]) }}">Remover</a></div></td>
                </tr>
            @endforeach
                </tbody>
              </table>
          @endif
        </div>
    </div>
</div>
@endsection