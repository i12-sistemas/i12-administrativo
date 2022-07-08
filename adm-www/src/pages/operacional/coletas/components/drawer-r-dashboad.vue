<template>
<div>
  <q-list v-for="(itens, key) in menuitens" :key="key"   >
    <q-item :dense="!$q.platform.is.mobile" clickable class="text-black" v-ripple >
      <q-item-section avatar v-if="itens.icon">
        <q-icon :name="itens.icon"  size="20px"/>
      </q-item-section>
      <q-item-section header class="text-weight-bold text-uppercase q-pt-0 text-primary" >{{itens.categoria}}</q-item-section>
      <q-item-section side v-if="itens.badge || itens.id">
        <q-badge :color="itens.badgecolor ? itens.badgecolor : 'grey-5'" :text-color="itens.badgetextcolor ? itens.badgetextcolor : 'white'"
          :label="itens.classid ? dashboard1[itens.classid][itens.id] : (itens.badge ? itens.badge : '0')" />
      </q-item-section>
    </q-item>
    <div v-for="(subitens, skey) in itens.itens" :key="skey">
      <q-item :dense="!$q.platform.is.mobile" clickable class="text-black" v-if="!subitens.separator" @click="actConsultaColetas(subitens.query)" v-ripple >
        <q-item-section avatar v-if="subitens.icon">
          <q-icon :name="subitens.icon"  size="20px"/>
        </q-item-section>
        <q-item-section>{{subitens.text}}</q-item-section>
        <q-item-section side v-if="subitens.badge || subitens.id">
          <q-badge
            :color="dashboard1[subitens.classid][subitens.id] > 0 ? (subitens.badgecolor ? subitens.badgecolor : 'grey-5') : 'grey-1'"
            :text-color="dashboard1[subitens.classid][subitens.id] > 0 ? (subitens.badgetextcolor ? subitens.badgetextcolor : 'white') : 'grey-5'"
            :label="subitens.classid ? dashboard1[subitens.classid][subitens.id] : (subitens.badge ? subitens.badge : '0')" />
        </q-item-section>
        <q-tooltip v-if="subitens.tooltip">{{subitens.tooltip}}</q-tooltip>
      </q-item>
      <q-separator spaced v-if="subitens.separator" inset />
    </div>
    <q-separator class="q-mt-md q-mb-lg" />
  </q-list>
  <div class="text-right q-pa-sm">
    <span class="text-caption" v-if="dashboard1.updated">{{ infoupdated }}</span>
    <q-btn color="grey-10" icon="sync" flat dense @click="refreshData" :loading="loading" />
    <q-tooltip v-if="dashboard1.updated">Última atualização em {{ this.$helpers.datetimeToBR(this.dashboard1.updated) }}

    </q-tooltip>
  </div>
</div>
</template>

<script>
import ColetasDashboard from 'src/mvc/collections/coletasdashboard.js'
import moment from 'moment'

export default {
  name: 'drawer.r.menu.dashboard',
  components: {
  },
  data () {
    let dashboard1 = new ColetasDashboard()
    return {
      dashboard1,
      intervalClock: 0,
      infoupdated: null,
      loading: true,
      menuitens: [
        {
          categoria: 'Coletas em aberto',
          classid: 'emaberto',
          id: 'total',
          badge: '99',
          badgecolor: 'primary',
          badgetextcolor: 'white',
          itens: [
            { classid: 'emaberto', id: 'revisaoorcamento', text: 'Revisar dados', badge: '99', badgecolor: 'indigo', badgetextcolor: 'white', icon: 'warning', query: { situacao: '0', origem: '2', t: new Date().getTime() } },
            { classid: 'emaberto', id: 'naoliberado', text: 'Não liberados', badge: '99', badgecolor: 'red', badgetextcolor: 'white', icon: 'warning', query: { situacao: '0,1' } },
            { classid: 'emaberto', id: 'semmotorista', text: 'Sem motorista', badgecolor: 'orange', badgetextcolor: 'white', icon: 'airline_seat_recline_normal', query: { situacao: '0,1', semmotorista: '1' } },
            { classid: 'emaberto', id: 'cargaurgente', text: 'Urgentes', badge: '99', badgecolor: 'red-10', badgetextcolor: 'white', icon: 'flash_on', query: { situacao: '0,1', cargaurgente: '1' } },
            { separator: true },
            { classid: 'emaberto', id: 'atrasado', text: 'Em atraso', badge: '99', badgecolor: 'red-2', badgetextcolor: 'black', query: { situacao: '0,1', dhcoletaf: moment().subtract(2, 'days').format('YYYY/MM/DD') } },
            { classid: 'emaberto', id: 'ontem', text: 'Ontem', badge: '99', badgecolor: 'yellow-6', badgetextcolor: 'black', query: { situacao: '0,1', dhcoletai: moment().subtract(1, 'days').format('YYYY/MM/DD'), dhcoletaf: moment().subtract(1, 'days').format('YYYY/MM/DD') } },
            { classid: 'emaberto', id: 'hoje', text: 'Hoje', badge: '99', badgecolor: 'green-5', badgetextcolor: 'white', query: { situacao: '0,1', dhcoletai: moment().format('YYYY/MM/DD'), dhcoletaf: moment().format('YYYY/MM/DD') } },
            { classid: 'emaberto', id: 'futuro', text: 'Futuramente', badge: '99', badgecolor: 'blue-5', badgetextcolor: 'white', query: { situacao: '0,1', dhcoletai: moment().add(1, 'days').format('YYYY/MM/DD') } }
          ]
        },
        {
          categoria: 'Encerradas recentemente',
          itens: [
            { classid: 'encerrados', id: 'hoje', text: 'Hoje', badgecolor: 'green-5', query: { situacao: '2', dhbaixai: moment().format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') } },
            { classid: 'encerrados', id: 'semana', text: 'Esta semana', badgecolor: 'green-5', query: { situacao: '2', dhbaixai: moment().startOf('week').format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') }, tooltip: moment().startOf('week').format('YYYY/MM/DD') },
            { classid: 'encerrados', id: 'mes', text: 'Este mês', badgecolor: 'green-5', query: { situacao: '2', dhbaixai: moment().startOf('month').format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') } }
          ]
        },
        {
          categoria: 'Cancelados recentemente',
          itens: [
            { classid: 'cancelados', id: 'hoje', text: 'Hoje', badgecolor: 'red-5', query: { situacao: '3', dhbaixai: moment().format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') } },
            { classid: 'cancelados', id: 'semana', text: 'Esta semana', badgecolor: 'red-5', query: { situacao: '3', dhbaixai: moment().startOf('week').format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') }, tooltip: moment().startOf('week').format('YYYY/MM/DD') },
            { classid: 'cancelados', id: 'mes', text: 'Este mês', badgecolor: 'red-5', query: { situacao: '3', dhbaixai: moment().startOf('month').format('YYYY/MM/DD'), dhbaixaf: moment().format('YYYY/MM/DD') } }
          ]
        }
      ]
    }
  },
  created: function () {
    // this.$eventbus.$on('coleta_consulta_update', this.onColetaConsultaUpdate)
  },
  beforeDestroy: function () {
    clearInterval(this.intervalClock)
    // this.$eventbus.$off('updatemenulateral', this.onUpdateMenuLateral)
  },
  async mounted () {
    // var app = this
    this.refreshData()
    this.intervalClock = setInterval(this.updateClock, 1000)
  },

  methods: {
    actConsultaColetas (params) {
      if (this.$q.platform.is.mobile) this.leftDrawerOpen = false
      if (this.$route.name === 'operacional.coletas.consulta') {
        this.$eventbus.$emit('coleta_consulta_update', params)
      } else {
        this.$router.push({ name: 'operacional.coletas.consulta', query: params })
      }
    },
    updateClock () {
      this.infoupdated = this.$helpers.datetimeRelativeToday(this.dashboard1.updated)
    },
    async refreshData () {
      var app = this
      app.loading = true
      app.msgError = ''
      var ret = await app.dashboard1.fetch()
      if (ret.ok) {
      } else {
        app.loading = false
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
      app.loading = false
    }
  }
}
</script>
