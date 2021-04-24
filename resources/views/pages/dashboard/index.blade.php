@extends('adminlte::page')

@section('title', 'Dashboard')
@section('plugins.Chartjs', true)

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    {{-- <h1>Dashboard</h1> --}}
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/app.js') }}"></script>

<script>
  $(function () {

    // Categories
    var lineChartCanvas = $('#lineChart').get(0).getContext('2d')

    var lineChartData = {
      labels  : @json(array_keys($orders)),
      datasets: [
        {
          label               : 'Digital Goods',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : @json(array_values($orders))
        },
      ]
    }

    var lineChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      }
    }

    new Chart(lineChartCanvas, {
      type: 'line',
      data: lineChartData,
      options: lineChartOptions
    })

    var lineChartCanvas = $('#lineChart').get(0).getContext('2d')
    var lineChartOptions = $.extend(true, {}, lineChartOptions)
    var lineChartData = $.extend(true, {}, lineChartData)
    lineChartOptions.datasetFill = false

    var lineChart = new Chart(lineChartCanvas, {
      type: 'line',
      data: lineChartData,
      options: lineChartOptions
    })


    // Subscribers Chart

  })
    </script>
@stop
