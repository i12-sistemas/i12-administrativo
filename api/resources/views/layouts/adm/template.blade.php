<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- favicon.ico. Place these in the root directory. -->
    <link rel="shortcut icon" href="http://ww.i12.com.br/favicon.png">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Add icon library -->
    <script defer src="{{asset('vendor/fontawesome-free-5.13.0-web/js/all.js')}}"></script>

    <!-- Styles -->
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    {{-- <link href="{{ asset('css/default.css') }}" rel="stylesheet"> --}}

    <!-- INÍCIO META TAGS -->
    <meta name="description" content="i12 Sistemas">
    <meta name="keywords" content="i12 Sistemas">
    <meta name="abstract" content="i12 Sistemas">
    <meta name="author" content="Weber de Paula">
    <meta name="url" content="http://www.i12.com.br/" />
    <!-- FIM META TAGS -->

    <!-- INÍCIO CANONICAL -->
    <link rel="canonical" href="http://www.i12.com.br/" />
    <!-- FIM CANONICAL -->
  
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('adm.home') }}">
                  <img class="logo mb3" src="{{ asset('assets\images\Logo-Horizontal-350px.png') }}" alt="i12 Sistemas" style="max-height: 30px">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @if(Auth::guard('adm')->check())
                          <li class="nav-item">
                            <a class="nav-link" href="#">
                                {{ Auth::guard('adm')->user()->nome }} <span class="caret"></span>
                            </a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="{{ route('adm.logout') }}"
                              onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                Sair
                            </a>

                                <form id="logout-form" action="{{ route('adm.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                          </li>
                        @else
                          <li class="nav-item">
                            <a class="nav-link" href="{{ route('adm.login') }}">{{ __('Login') }}</a>
                          </li>
                            
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script>
      function goBack() {
        window.history.back();
      }
      </script>  
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    @yield('js')

</body>
</html>
