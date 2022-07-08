<template>
<div>
  <q-page class="">
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-sm-12 full-width" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pt-md'">
        <!-- <q-card class="my-card" bordered flat>
          <q-card-section horizontal v-if="dataset">
            <q-card-section class="col-6">
              <div class="text-h6">Olá, {{dataset.nome_old}}</div>
            </q-card-section>
            <q-card-section class="col-6 text-right">
              <div class="text-caption">Informações referente a</div>
              <div class="text-weight-bold" v-if="dataset.clientescount > 0">
                <div v-if="dataset.clientescount === 1">
                  {{dataset.clientes[0].fantasia !== '' ? dataset.clientes[0].fantasia : dataset.clientes[0].razaosocial }}
                </div>
                <div v-else>
                  {{dataset.clientescount}} empresas
                </div>
                <q-tooltip :delay="500">
                  <div v-for="(cli, k) in dataset.clientes" :key="'cli' + k">
                    <q-icon name="circle" class="q-mr-md" size="8px" />{{ cli.cnpj ? $helpers.mascaraDocCPFCNPJ(cli.cnpj) + ' :: ': ''}} {{cli.razaosocial !== '' ? cli.razaosocial : cli.fantasia }}
                  </div>
                </q-tooltip>
              </div>
              <div class="text-weight-bold" v-if="dataset.clientescount <= 0">Nenhuma empresa identificada</div>
            </q-card-section>
          </q-card-section>
          <q-separator spaced  />
          <q-card-section v-if="usuariologado ? usuariologado.permitecoletasver : false">
            <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md" v-if="optionsmenu">
              <div class="col-9">
                Coletas
              </div>
              <div class="col-3 text-right">
                <q-btn color="grey-9" icon="sync" @click="actDashboard" dense flat :loading="loading" label="Atualizar" no-caps no-wrap />
              </div>
              <div class="col-md-4 col-xs-12" v-for="(n, index) in 3" :key="'col' + index">
                <q-btn :icon="optionsmenu[n-1].icon" stack unelevated :color="optionsmenu[n-1].color" size="lg" class="full-width"
                  :to="{ name: 'coletas.consulta', query: { customfilter: optionsmenu[n-1].value }}"
                  :outline="optionsmenu[n-1].badge ? optionsmenu[n-1].badge === 0 : true"
                  >
                  <span v-html="optionsmenu[n-1].title" />
                  <q-badge color="red" text-color="white" :label="optionsmenu[n-1].badge ? optionsmenu[n-1].badge : ''"
                    floating rounded class="text-h6" v-if="optionsmenu[n-1].badge ? optionsmenu[n-1].badge > 0 : false" />
                  <q-tooltip :delay="700">
                    <span v-html="optionsmenu[n-1].tooltip" />
                  </q-tooltip>
                 </q-btn>
              </div>
              <div class="col-12">
                Entregas
              </div>
              <div class="col-md-4 col-xs-12" v-for="(n, index) in 3" :key="'entrega' + index">
                <q-btn :icon="optionsmenu[n+2].icon" stack unelevated :color="optionsmenu[n+2].color" size="lg" class="full-width"
                  :to="{ name: 'coletas.consulta', query: { customfilter: optionsmenu[n+2].value }}"
                  :outline="optionsmenu[n+2].badge ? optionsmenu[n+2].badge === 0 : true"
                  >
                  <span v-html="optionsmenu[n+2].title" />
                  <q-badge color="red" text-color="white" :label="optionsmenu[n+2].badge ? optionsmenu[n+2].badge : ''" floating rounded class="text-h6" v-if="optionsmenu[n+2].badge ? optionsmenu[n+2].badge > 0 : false" />
                  <q-tooltip :delay="700">
                    <span v-html="optionsmenu[n+2].tooltip" />
                  </q-tooltip>
                 </q-btn>
              </div>
            </div>
          </q-card-section>
          <q-separator spaced v-if="false" />
          <q-card-section class="q-ma-none q-pa-none" v-if="false" >
            <q-table :data="datalastmov" :columns="columns" dense :loading="loading" id="tableultimasmovimentacoes" flat
              row-key="id" :rows-per-page-options="[0]" title="Úlimas movimentações" >
              <template v-slot:body-cell-data="props">
              <q-td :props="props" >
                <div class="cursor-pointer" >
                  {{ $helpers.datetimeRelativeToday(props.row.data) }}
                  <q-tooltip>
                    <div>em {{ $helpers.datetimeToBR(props.row.data) }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            </q-table>
          </q-card-section>
        </q-card> -->
      </div>
      <q-page-sticky position="top" expand>
        <q-toolbar class="bg-white text-primary shadow-up-21">
          <!-- <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
          <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
          <q-toolbar-title>Status Geral</q-toolbar-title> -->
        </q-toolbar>
      </q-page-sticky>
    </div>
  </q-page>
</div>
</template>

<script>
export default {
  name: 'home',
  components: {
  },
  data () {
    return {
      dataset: null,
      optionsmenu: null,
      rows: [],
      filter: '',
      datalastmov: [
        { id: 1, data: '2021-10-13 10:23:50', detalhe: 'Carga carregada em SAO PAULO para SERTAOZINHO' },
        { id: 2, data: '2021-10-01 11:23:00', detalhe: 'Carga carregada em SAO PAULO para SERTAOZINHO' },
        { id: 3, data: '2021-09-01 10:23:50', detalhe: 'Carga carregada em SAO PAULO para SERTAOZINHO' },
        { id: 4, data: '2021-07-01 10:23:50', detalhe: 'Carga carregada em SAO PAULO para SERTAOZINHO' }
      ],
      columns: [
        { name: 'data', style: 'width: 120px', field: 'data', required: true, label: 'Data', align: 'left', sortable: false },
        { name: 'detalhe', align: 'left', label: 'Detalhe', field: 'detalhe', sortable: false }
      ],
      showpedido: false,
      tab: 'dados',
      slide: 0,
      loading: true,
      posting: false,
      msgError: '',
      showfavoritos: true
    }
  },
  created: function () {
    // this.$eventbus.$on('homedashboardupdated', this.actRefreshInfo)
  },
  beforeDestroy: function () {
    // this.$eventbus.$off('homedashboardupdated', this.actRefreshInfo)
  },
  async mounted () {
    // this.optionsmenu = this.$store.state.homedashboard.options
    // this.actRefreshInfo()
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.usuario) {
          u = app.$store.state.usuario.usuario
        }
      }
      return u
    }
  },
  methods: {
    async actDashboard () {
      var app = this
      app.loading = true
      var ret = await app.$store.dispatch('homedashboard/refresh')
      app.loading = false
      if (!ret.ok) {
        app.loading = false
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
    },
    async actRefreshInfo () {
      var app = this
      if (app.$store.state.homedashboard.options) app.optionsmenu = app.$store.state.homedashboard.options
      if (app.$store.state.homedashboard.user) app.dataset = app.$store.state.homedashboard.user
      app.loading = false
    },
    async actSave () {
      var app = this
      app.loading = true
      var celularalterado = ((app.dataset.usernametype === 'celular') && (app.dataset.celular_old !== app.dataset.celular))
      var ret = await app.dataset.saveMeuUsuario()
      if (!ret.ok) {
        app.loading = false
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      } else {
        app.$q.notify({
          color: 'positive',
          icon: 'check_circle',
          timeout: 2500,
          message: 'Informações do usuário foram salvas!'
        })
        if (celularalterado) {
          app.$store.dispatch('usuario/logout')
          app.$q.notify({
            color: 'orange',
            icon: 'person',
            timeout: 2500,
            message: 'Celular alterado, necessário fazer novo login!'
          })
        } else {
          await app.$store.dispatch('usuario/getlocalstorage')
        }
      }
      app.loading = false
    }
  }
}
</script>

<style lang="stylus">
</style>
