<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{config('app.name')}} - @yield('title')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/Ionicons/css/ionicons.min.css') }}">

  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/bower_components/jvectormap/jquery-jvectormap.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/dist/css/AdminLTE.min.css') }}">


    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE-2.4.3/dist/css/skins/_all-skins.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/icom.css') }}">

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
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <meta name="user" content="{{ Auth::guard('admin')->check() ? Auth::guard('admin')->User() : ''}}">
  

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
<div id="app">
    
    @foreach ($errors->all() as $error)
    <div class="text-center has-feedback has-error" style="margin: 50px 0 0 9px;font-size: x-large;">
        <span class="text-danger">
            <strong>{{ $error }}</strong>
        </span>
    </div>

    @endforeach
    <app></app>
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

<!-- date-range-picker -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>


<!-- Sparkline -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap  -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('vendor/AdminLTE-2.4.3/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('vendor/AdminLTE-2.4.3/bower_components/chart.js/Chart.js') }}"></script>

<script>
    var base_url = "{!! url('/admin') !!}";
    var url = "{!! url('/') !!}";
    var origem = "{!! Auth::guard('admin')->getSession()->has('origem') ? 'icom' : '' !!}"
</script>

@if(config('app.env')=='production')
<script src="{{ asset('vendor/vuejs/vue.min.js') }}"></script>
@else
<script src="{{ asset('vendor/vuejs/vue.js') }}"></script>
@endif

<script src="{{ asset('js/app.js') }}"></script>

@yield('js')

</body>
</html>
