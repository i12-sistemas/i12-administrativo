@extends('layouts.admin.template')

@section('title', isset($title) ? $title : '')

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop

@section('content-header')
@stop

@section('content')
<section class="content">
<router-view></router-view>
</section>
@stop

@section('js')
<script>
</script>
@stop