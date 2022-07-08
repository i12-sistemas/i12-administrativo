@extends('layouts.adm.template')

@section('content')
<div class="container">
    <div class="row justify-content-left">
        <div class="row">
          <div class="col-md-12">
            @if($s3)
              <div>Arquivo: {{ $s3->filename }}</div>
              <div>Tamanho: {{human_filesize( $s3->size)}}</div>
              <hr />
              <div>Download iniciará 2 segundos,</div>
              <div>caso não inicie <a data-auto-download href="{{$url}}" >clique aqui</a></div>
            @else
              <div>{{$ret->msg}}</div>
            @endif
            </div>
          </div>
        </div>
</div>
@endsection
@section('js')
<script>
  $(function() {
    $('a[data-auto-download]').each(function(){
      var $this = $(this);
      setTimeout(function() {
          window.location = $this.attr('href');
      }, 1300);
    });
  });
</script>
@endsection