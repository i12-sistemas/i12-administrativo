@extends('site.layout.template')
@section('title', '')
@section('content')

<section id="suporte_chat" style="{{ (Request::query('hidetoolbar')=='1') ? 'padding-top: 15px' : ''}}">
  <div class="container">
    <div class="row">
      <div class="main_feature text-center">
        <div class="col-md-12 col-xs-12 col-sm-12 col-md-offset-3">
          <div class="feature_content">
            <div id='tawk_5a6a20d44b401e45400c65e9' class="col-md-12 col-xs-12 col-sm-12"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@stop
