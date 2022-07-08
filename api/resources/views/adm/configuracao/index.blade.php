@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop

@section('content-header')
<section class="content-header">
    <h1>Configuração</h1>
    <ol class="breadcrumb">
        <li> <a href="{{route('admin.home')}}" >home</a></li>
        <li class="active">Configuração <b> {{ $area or '' }}</b></li>
    </ol>
    </section>
@stop

@section('content')
<section class="content">
<router-view area="{{ $area or ''}}"></router-view>
</section>
@stop


@section('js')
<!-- CK Editor -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/ckeditor/ckeditor.js') }}"></script>
@stop

