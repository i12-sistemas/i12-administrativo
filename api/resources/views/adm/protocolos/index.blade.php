@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop

@section('content-header')
<section class="content-header">
    <h1>Protocolos</h1>
    <ol class="breadcrumb">
        <li> <a href="{{route('admin.home')}}" >home</a></li>
        <li class="active">Listagem</li>
    </ol>
    </section>
@stop

@section('content')
<section class="content">
    <router-view></router-view>

</section>
@stop