<template>
  <apexchart height="100%" width="100%" v-if="!loading" type="donut" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
import Vue from 'vue'
export default {
  name: 'ApexSimplePieChart',
  props: [ 'series', 'labels', 'colors' ],
  data () {
    return {
      loading: true,
      chartOptions: {
        chart: {
          type: 'donut'
        },
        colors: [],
        labels: [], // exemplo ['Em acompanhamento', 'Em aberto']
        tooltip: {
          y: {
            formatter: function (value) {
              return Vue.prototype.$helpers.formatRS(value, false, 3) + ' %'
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
    app.loading = false
  }
}
</script>
