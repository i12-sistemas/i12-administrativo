{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', ' - ' . $title)

@section('content_header')

<section class="content-header">
    <h1>
        {{ $title or 'Listagem de cliente'}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('empresas.consultar') }}"><i class="fa fa-dashboard"></i> Home</a></li>
    </ol>
  </section>


@stop

@section('content')
  
  <div id="map" class="map"></div>


@stop

@section('css')
    <link rel="stylesheet" href="/css/sapv.css">
@stop

@section('js')
<script>
    // This example displays a map with the language and region set
    // to Japan. These settings are specified in the HTML script element
    // when loading the Google Maps JavaScript API.
    // Setting the language shows the map in the language of your choice.
    // Setting the region biases the geocoding results to that region.
    function initMap() {
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 4,
       center: {lat: {!! $empresas[0]->mapslat !!}, lng: {!! $empresas[0]->mapslng !!} }
      });

      // Create an array of alphabetical characters used to label the markers.
      var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

      // Add some markers to the map.
      // Note: The code uses the JavaScript Array.prototype.map() method to
      // create an array of markers based on a given "locations" array.
      // The map() method here has nothing to do with the Google Maps API.
      var markers = locations.map(function(location, i) {
        return new google.maps.Marker({
          position: location,
          label: labels[i % labels.length]
        });
      });                
      // Add a marker clusterer to manage the markers.
      var markerCluster = new MarkerClusterer(map, markers,
          {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
      }

      var locations = [
                @foreach($empresas as $empresa)
                      {lat: {!! $empresa->mapslat !!}, lng: {!! $empresa->mapslng !!} },
                @endforeach
                ];      
      
  </script>
  <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6kMHvafcIeCSVK7sLCpE5iyfipCd4-3M&callback=initMap&language=pt-br" async defer>
  </script>
@stop

