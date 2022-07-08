@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')
@stop


@section('content-header')
    <section class="content-header">
    <h1>Protocolo</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Mensagens</li>
    </ol>
    </section>

@stop

@section('content')
      <section class="content">

        <div class="row">
        <div class="col-md-12">
          <router-view numprotocolo="{{$protocolo}}" v-on:toolbarchanged="toolbarchanged"></router-view>
      </div>
      </div>


      </section>
@stop