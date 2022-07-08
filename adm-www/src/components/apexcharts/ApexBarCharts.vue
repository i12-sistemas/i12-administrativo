<template>
  <apexchart v-if="!loading" type="bar" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
import Vue from 'vue'
export default {
  name: 'ApexBarCharts',
  props: [ 'categories', 'series', 'colors' ],
  data () {
    return {
      loading: true,
      // series: [{
      //   data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
      // }],
      chartOptions: {
        chart: {
          type: 'bar'
        },
        plotOptions: {
          bar: {
            distributed: true,
            horizontal: true,
            // barHeight: '50px'
            dataLabels: {
              position: 'bottom'
            }
          }
        },
        colors: [],
        dataLabels: {
          enabled: true,
          textAnchor: 'start',
          style: {
            colors: ['#000']
          },
          formatter: function (val, opt) {
            return opt.w.globals.labels[opt.dataPointIndex] + ':  ' + val
          },
          offsetX: 0,
          dropShadow: {
            enabled: true
          }
        },
        stroke: {
          width: 1,
          colors: ['#fff']
        },
        xaxis: {
          categories: []
        },
        yaxis: {
          labels: {
            show: false
          }
        },
        tooltip: {
          theme: 'dark',
          y: {
            title: {
              formatter: function (value) {
                return Vue.prototype.$helpers.formatRS(value, false, 3) + ' %'
              }
            }
          }
        },
        legend: {
          show: false,
          labels: {
            colors: '#00'
          }
        }
      }
    }
  },
  async mounted () {
    var app = this
    app.loading = true
    if (app.labels) app.chartOptions.labels = app.labels
    if (app.colors) app.chartOptions.colors = app.colors
    if (app.categories) app.chartOptions.xaxis.categories = app.categories
    app.loading = false
  }
}
</script>
