{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', ' - ' . $title)

@section('content_header')
@stop

@section('content')
  <section class="content">
    <router-view></router-view>            
  </section>
@stop

@section('css')
    <link rel="stylesheet" href="/css/sapv.css">
@stop


@section('js')
    <script>
        var userlogged = {
                        id: {!! Auth::user()->id !!}, 
                        nome: "{!! Auth::user()->nome !!}",
                        permissoes: [
                            @foreach (Auth::user()->permissoes() as $permissao)
                                "{!! $permissao->idpermissao !!}",    
                            @endforeach
                            ],
                        };
        var base_url = "{!! url('/') !!}";    
    </script>
    @if(config('app.env')=='production')
    <script src="{{ asset('vendor/vuejs/vue.min.js') }}"></script>
    @else
    <script src="{{ asset('vendor/vuejs/vue.js') }}"></script>
    @endif
    <script src="{{ asset('js/app.js') }}"></script>
@stop