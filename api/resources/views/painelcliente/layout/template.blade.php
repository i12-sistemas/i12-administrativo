<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>i12 Sistema - Painel do cliente - @yield('title')</title>
  <meta name="description" content="i12 Sistema - Painel do cliente">
  <meta name="author" content="i12 Sistema e sua equipe">
  <META NAME="robots" CONTENT="noindex,nofollow">

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/Ionicons/css/ionicons.min.css') }}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/jvectormap/jquery-jvectormap.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/dist/css/AdminLTE.min.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/dist/css/skins/_all-skins.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/istu.css') }}">

  <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/favicon/apple-icon-57x57.png') }}">
  <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/favicon/apple-icon-60x60.png') }}">
  <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/favicon/apple-icon-72x72.png') }}">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/favicon/apple-icon-76x76.png') }}">
  <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/favicon/apple-icon-114x114.png') }}">
  <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/favicon/apple-icon-120x120.png') }}">
  <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/favicon/apple-icon-144x144.png') }}">
  <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/favicon/apple-icon-152x152.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-icon-180x180.png') }}">
  <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('assets/favicon/android-icon-192x192.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicon/favicon-96x96.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="{{ asset('assets/favicon/ms-icon-144x144.png') }}">
  <meta name="theme-color" content="#ffffff">


  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-119245133-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-119245133-1');
  </script>
  
  
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="user" content="{{Auth::guard('painelcliente')->User()}}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-black layout-top-nav">
<div id="appusercontent">
<div class="wrapper">

  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <a href="{{route('painelcliente.home')}}" class="navbar-brand">i<b>12</b></a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-address-book" aria-hidden="true"></i> Chamados <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#"><i class="fa fa-mobile-phone" aria-hidden="true"></i> Abrir novo chamado</a></li>
                <li class="divider"></li>
                <li><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i> Em abertos</a></li>
                <li><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i> Encerrados</a></li>
              </ul>              
              
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-address-book" aria-hidden="true"></i> Faturas <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i> A vencer</a></li>
                <li><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i> Vencidos</a></li>
                <li><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i> Pagas</a></li>
              </ul>              
              
            </li>
            <li><a href="{{route('painelcliente.extrato')}}"><i class="fa fa-file-text" aria-hidden="true"></i> Licenca de uso</a></li>
            <li><a href="{{route('painelcliente.extrato')}}"><i class="fa fa-file-text" aria-hidden="true"></i> Contrato vigente</a></li>
            
            <li><a href="{{route('painelcliente.logout')}}"><i class="fa fa-power-off" aria-hidden="true"></i> Sair</a></li>
          </ul>        
            @yield('menu-top')
        </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            {{-- protocolos --}}
            <li class="dropdown messages-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-sticky-note"></i>
                    {{-- se tiver protocolos sem leitura --}}
                    
                      <span class="label label-danger">{{Auth::guard('painelcliente')->User()->chamadoaberto->count() }}</span>
                    
              </a>
              <ul class="dropdown-menu">
                  <li class="header">
                      {{ 'Você tem ' . Auth::guard('painelcliente')->User()->chamadoaberto->count() . ' chamado(s) em aberto'}} 
                      
                  </li>
                  <li>
                    <ul class="menu">
                        @if(Auth::guard('painelcliente')->User()->chamadoaberto->count()>0)
                          @foreach (Auth::guard('painelcliente')->User()->chamadoaberto()->orderBy('dtabertura', 'desc')->get() as $chamado )
                          <li><!-- start notification -->
                            <a href="{{route('painelcliente.protocolos.show', [$chamado->id])}}" class="text-red">
                            <i class="fa fa-sticky-note text-red"></i> {{$chamado->problemareclamado}}
                            </a>
                          </li> 
                          @endforeach
                        @endif
                    </ul>
                  <li class="footer"><a href="{{route('painelcliente.protocolos')}}">Ver todos os protocolos</a></li>                                      
                  </li>
              </ul>
            </li>
            {{-- protocolos --}}
            

            {{-- saldo --}}
            {{-- <li class="dropdown tasks-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-ticket"></i>
                @if(Auth::guard('painelcliente')->User()->saldopassagem<0)
                    <span class="label label-danger">{{Auth::guard('painelcliente')->User()->saldopassagem * (-1) }}</span>
                @elseif(Auth::guard('painelcliente')->User()->saldopassagem>0)
                    <span class="label label-primary">{{Auth::guard('painelcliente')->User()->saldopassagem}}</span>
                @endif
              </a>
              <ul class="dropdown-menu">
                <li class="header">Meu saldo em {{Auth::guard('painelcliente')->User()->saldodhupdate->format('d/m/y h:i')}}</li>
                <li>
                  <!-- Inner menu: contains the tasks -->
                  <ul class="menu">
                    <li><!-- Task item -->
                        <a href="{{route('painelcliente.extrato')}}">
                            <!-- Task title and progress text -->
                            @if(Auth::guard('painelcliente')->User()->saldopassagem<0)
                                <span class="text-red text-bold">{{Auth::guard('painelcliente')->User()->saldopassagem * (-1) }} ticket em débito</span>
                                <i class="fa fa-meh-o fa-2x text-red pull-right"></i>
                            @elseif(Auth::guard('painelcliente')->User()->saldopassagem>0)
                                <span class="text-blue text-bold">{{Auth::guard('painelcliente')->User()->saldopassagem}} ticket</span>
                                <i class="fa fa-smile-o fa-2x text-blue pull-right"></i>
                            @endif
                                                    
                        </a>
                    </li>
                    <!-- end task item -->
                  </ul>
                </li>
                <li class="footer">
                  <a href="{{route('painelcliente.extrato')}}">ver meu extrato recente</a>
                </li>
              </ul>
            </li> --}}
            {{-- saldo --}}

            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                {{-- <img src="{{ Auth::guard('painelcliente')->User()->fotourlsmall }}" class="user-image" alt="User Image"> --}}
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs">{{Auth::guard('painelcliente')->User()->firstname}}</span>
              </a>
              <ul class="dropdown-menu">
                <!-- The user image in the menu -->
                <li class="user-header">
                  <img src="{{ Auth::guard('painelcliente')->User()->fotourl }}" class="img-circle" alt="User Image">

                  <p>
                    {{Auth::guard('painelcliente')->User()->nome}}
                    <small>{{Auth::guard('painelcliente')->User()->curso}} | {{Auth::guard('painelcliente')->User()->instituicaoensino}}</small>
                  </p>
                </li>
                                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="{{ route('painelcliente.profile') }}" class="btn btn-default btn-flat"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Meus dados</a>
                  </div>
                  <div class="pull-right">
                    <a href="{{ route('painelcliente.logout') }}" class="btn btn-default btn-flat"><i class="fa fa-power-off" aria-hidden="true"></i> Sair</a>
                  </div>
                </li>
              </ul>
            </li>             
            @yield('menu-top-right')
          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container">
      <!-- Content Header (Page header) -->
      @yield('content-header')
      <!-- Main content -->
      @yield('content')

      <!-- /.content -->
    </div>
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="container">
      <div class="pull-right hidden-xs">
        i12 - Painel do cliente <b>Version</b> 2.4.0
      </div>
      <strong>Copyright &copy; 2008-{{ date("Y") }} <a href="http://www.idoze.com.br" target="_self">i12 Sistemas</a></strong> 
    </div>
    <!-- /.container -->
  </footer>
</div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/dist/js/adminlte.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap  -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('vendor/AdminLTE-2.4.3/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/chart.js/Chart.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{--  <script src="{{ asset('vendor/AdminLTE-2.4.3/dist/js/pages/dashboard2.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/dist/js/demo.js') }}"></script>  --}}

<script>
    var userlogged = {
                    id: {!! Auth::guard('painelcliente')->user()->id !!},
                    permissoes: []
                    };
    var base_url = "{!! url('/painelcliente') !!}";    
</script>
@if(config('app.env')=='production')
<script src="{{ asset('vendor/vuejs/vue.min.js') }}"></script>
@else
<script src="{{ asset('vendor/vuejs/vue.js') }}"></script>
@endif
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
