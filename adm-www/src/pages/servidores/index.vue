<template>
<div>
  <q-page class="">
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header" >
      <div class="col-sm-12 full-width" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'" >
        <div class="col-12" v-if="error">
          <q-banner class="bg-negative text-white">{{error}}</q-banner>
        </div>
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section>
            <div class="row q-col-gutter-sm">
                <div class="col-xs-12 col-sm-8 col-lg-6">
                  <q-select v-model="filterambiente" label="Ambiente" outlined emit-value stack-label map-options
                    :dense="!$q.platform.is.mobile"
                    :options="[ { value: '1', label: '1 - Produção' }, { value: '2', label: '2 - Dev/Homologação' } ]" />
                </div>
                <div class="col-xs-12 col-sm-12 col-lg-6">
                    <q-btn unelevated label="Consultar" color="primary" icon="search"  @click="refreshData(null)" :loading="loading" class="full-width full-height"  />
                </div>
            </div>
          </q-card-section>
        </q-card>
        <q-card class="bg-white q-mt-md" flat  v-if="rows ? rows.length > 0 : false" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-separator v-if="$q.platform.is.mobile"  />
          <q-card-section class="q-pa-none">
            <div style="max-width: 100vw">
              <q-table :data="rows" :columns="columns" :dense="!$q.platform.is.mobile"  flat
                :loading="loading" color="primary" id="sticky-table"
                :pagination.sync="dataset.pagination"
                row-key="serialnumber"
                 :rows-per-page-options="$qtable.rowsperpageoptions"
                @request="refreshData"
                :filter="filter"
                >
                <template v-slot:header="props">
                  <q-tr :props="props">
                    <q-th auto-width />
                    <q-th
                      v-for="col in props.cols"
                      :key="col.name"
                      :props="props"
                    >
                      {{ col.label }}
                    </q-th>
                  </q-tr>
                </template>
                <template v-slot:body="props">
                  <q-tr :props="props">
                    <q-td auto-width>
                      <q-btn size="sm" color="grey-7" unelevated dense @click="props.expand = !props.expand" :icon="props.expand ? 'remove' : 'add'" />
                    </q-td>
                    <q-td
                      v-for="col in props.cols"
                      :key="col.name"
                      :props="props"
                    >
                    <div v-if="col.name === 'updated_at'" style="max-width: 150px" class="cursor-pointer" @click="actEditDialog(props.pageIndex)" >
                      <span class="q-mr-xs">{{ $helpers.datetimeRelativeToday(col.value) }}</span>
                      <q-tooltip>
                        <div>{{ $helpers.datetimeRelativeToday(col.value) }}</div>
                        <div>em {{ $helpers.datetimeToBR(col.value) }}</div>
                      </q-tooltip>
                    </div>
                    <div v-else>
                      {{ col.value }}
                    </div>
                    </q-td>
                  </q-tr>
                  <q-tr v-show="props.expand" :props="props">
                    <q-td colspan="100%" class="bg-grey-3">
                      <div>
                        <q-card class="q-mb-sm" v-for="(database, dbidx) in props.row.databases" :key="'database' + dbidx" flat bordered>
                          <q-card-section class="q-py-xs bg-grey-2">
                            <div class="text-h6">{{database.database}} <span class="q-ml-md text-subtitle2">Versão: {{database.version_icomdb}}</span></div>
                          </q-card-section>
                          <q-card-section>
                            <q-list bordered>
                              <q-item clickable v-ripple v-for="(cliente, cidx) in database.clientes" :key="'cliente' + cidx">
                                <q-item-section avatar>
                                  <q-icon
                                    :color="cliente.cnpjsecundario ? 'grey-9' : 'primary'"
                                    :name="cliente.cnpjsecundario ? 'adjust' : 'task_alt'"
                                    />
                                </q-item-section>
                                <q-item-section class="ellipsis">{{cliente.nome}}</q-item-section>
                                <q-item-section>{{$helpers.mascaraCpfCnpj(cliente.cnpj)}}</q-item-section>
                                <q-item-section avatar>
                                  <q-chip >
                                  <q-avatar
                                      :icon="cliente.cnpjoperacional ? 'important_devices' : 'desktop_access_disabled'"
                                      :color="cliente.cnpjoperacional ? 'green' : 'purple'"
                                      text-color="white" />
                                  {{cliente.cnpjoperacional ? 'Operacional' : 'Somente Fiscal'}}
                                  </q-chip>
                                </q-item-section>
                              </q-item>
                            </q-list>
                          </q-card-section>
                        </q-card>
                      </div>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
            </div>
          </q-card-section>
        </q-card>
        <q-page-sticky position="top" expand>
          <q-toolbar class="bg-primary text-white">
            <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
            <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
            <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
            <q-toolbar-title>Servidores</q-toolbar-title>
            <q-btn  icon="sync" :label="$q.platform.is.mobile ? '' : 'Atualizar'" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
          </q-toolbar>
        </q-page-sticky>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
import Servidores from 'src/mvc/collections/servidores.js'
export default {
  name: 'servidores.index',
  components: {
  },
  data () {
    var dataset = new Servidores()
    return {
      dataset,
      rows: [],
      filterambiente: '1',
      filter: '',
      columns: [
        { name: 'hostname', align: 'left', label: 'Hostname', field: 'hostname', filterconfig: { value: null } },
        { name: 'version', align: 'left', style: 'min-width: 120px', label: 'Versão MySQL', field: 'version', filterconfig: { tipo: 'tablefiltersituacao', value: ['0', '1'], label: '', info: '' } },
        { name: 'serialnumber', align: 'left', label: 'Serial', field: 'serialnumber', filterconfig: { value: null } },
        { name: 'updated_at', align: 'left', label: 'Atualizado em', field: 'updated_at' }
      ],
      loading: false,
      posting: false,
      error: null
    }
  },
  async mounted () {
    var app = this
    if (app.$q.platform.is.mobile) {
      app.columns.splice(2, 1)
    }
    if (app.$route.query) app.queryRead(app.$route.query)
    await app.refreshData(null)
  },

  methods: {
    async actRastrear (pRow) {
      var chave = null
      var cnpj = null

      if (pRow.notascount ? pRow.notascount >= 1 : false) {
        chave = pRow.notas[0].chave
      } else {
        if (pRow.nfe ? pRow.nfe.nNF > 0 : false) {
          chave = pRow.nfe.chave
        }
      }

      if (pRow.clientedestino ? pRow.clientedestino.cnpj !== '' : false) {
        cnpj = pRow.clientedestino.cnpj
      }
      if ((chave ? chave !== '' : false) && (cnpj ? cnpj !== '' : false)) {
        this.$router.push({ name: 'rastrear.entrega', query: { chave: chave, cnpj: cnpj } })
      }
    },
    async coletasconsultaupdated (status) {
      var app = this
      app.customfilter = parseInt(status)
      await this.refreshData(null)
    },
    async queryRead (pQuery) {
      var app = this
      if (pQuery) {
        if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)
        if (pQuery.ambiente) {
          app.filterambiente = parseInt(pQuery.ambiente)
          if (app.filterambiente !== 2) app.filterambiente = 1
          app.filterambiente = app.filterambiente.toString()
        }

        if (pQuery.customcnpj) app.customcnpj = pQuery.customcnpj
        if (pQuery.customnumero) app.customnumero = pQuery.customnumero
        if (pQuery.customchave) app.customchave = pQuery.customchave

        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.filterconfig) {
            if ((element.name === 'ambiente') && (pQuery.numero)) {
              element.filterconfig.value = app.$route.query.numero
            }

            if (element.name === 'situacao' && pQuery.situacao) {
              element.filterconfig.value = pQuery.situacao.toString().split(',')
            }
            if (element.name === 'clientedestino' && pQuery.clientedestino) {
              element.filterconfig.value = pQuery.clientedestino.toString().split(',')
            }
            if (element.name === 'clienteorigem' && pQuery.clienteorigem) {
              element.filterconfig.value = pQuery.clienteorigem.toString().split(',')
            }

            if (element.name === 'clienteorigem' && pQuery.clienteorigemstr) element.filterconfig.value2 = pQuery.clienteorigemstr
            if (element.name === 'regiao' && pQuery.regiaostr) element.filterconfig.value2 = pQuery.regiaostr
            if (element.name === 'enderecocoleta' && pQuery.enderecocoletastr) element.filterconfig.value2 = pQuery.enderecocoletastr
            if (element.name === 'clientedestino' && pQuery.clientedestinostr) element.filterconfig.value2 = pQuery.clientedestinostr
            if (element.name === 'cidadedestino' && pQuery.cidadedestinostr) element.filterconfig.value2 = pQuery.cidadedestinostr

            if (element.name === 'regiao' && pQuery.regiao) {
              element.filterconfig.value = pQuery.regiao.toString().split(',')
            }
            if (element.name === 'enderecocoleta' && pQuery.cidades) {
              element.filterconfig.value = pQuery.cidades.toString().split(',')
            }
            if (element.name === 'cidadedestino' && pQuery.cidadedestino) {
              element.filterconfig.value = pQuery.cidadedestino.toString().split(',')
            }
            if (element.name === 'origem' && pQuery.origem) {
              element.filterconfig.value = pQuery.origem.toString().split(',')
            }
            if (element.name === 'dhcoleta') {
              if (pQuery.dhcoletai || pQuery.dhcoletaf) {
                element.filterconfig.value = { dti: pQuery.dhcoletai, dtf: pQuery.dhcoletaf }
              } else {
                element.filterconfig.value = null
              }
            }

            if (element.name === 'motorista') {
              if ((pQuery.semmotorista === 'S') || (pQuery.motoristas)) {
                element.filterconfig.value = {
                  ids: pQuery.motoristas ? pQuery.motoristas.toString().split(',') : null,
                  semmotorista: (pQuery.semmotorista ? pQuery.semmotorista : null)
                }
              }
            }

            if (element.name === 'peso') {
              if (pQuery.pesoi || pQuery.pesof) {
                element.filterconfig.value = { valuei: pQuery.pesoi, valuef: pQuery.pesof }
              } else {
                element.filterconfig.value = null
              }
            }

            if (element.name === 'dhbaixa') {
              if (pQuery.dhbaixai || pQuery.dhbaixaf) {
                element.filterconfig.value = { dti: pQuery.dhbaixai, dtf: pQuery.dhbaixaf }
              } else {
                element.filterconfig.value = null
              }
            }
            if (element.name === 'categ') {
              element.filterconfig.value = []
              if (pQuery.produtosperigosos) {
                element.filterconfig.value.push({ field: 'produtosperigosos', desc: 'Produtos perigosos', value: (pQuery.produtosperigosos === '1' ? 'S' : 'N'), sim: 'Com produtos perigosos', nao: 'Sem produtos perigosos' })
              }
              if (pQuery.veiculoexclusico) {
                element.filterconfig.value.push({ field: 'veiculoexclusico', desc: 'Veículo exclusivo', value: (pQuery.veiculoexclusico === '1' ? 'S' : 'N'), sim: 'Com veículo exclusivo', nao: 'Sem veículo exclusivo' })
              }
              if (pQuery.cargaurgente) {
                element.filterconfig.value.push({ field: 'cargaurgente', desc: 'Carga urgente', value: (pQuery.cargaurgente === '1' ? 'S' : 'N'), sim: 'Com carga urgente', nao: 'Sem carga urgente' })
              }
            }
          }
        }
      }
    },
    async actEditDialog (rowIndex) {
      var app = this
      var row = app.rows[rowIndex]
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) {
        app.refreshData(null)
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    async actLoadMore () {
      var app = this
      app.loading = true
      var ret = await app.dataset.loadmore()
      if (ret.ok) {
        app.rows = app.dataset.itens
      }
      app.loading = false
    },
    async refreshData (props) {
      var app = this
      try {
        app.loading = true
        app.error = null

        app.error = null
        app.dataset.readPropsTable(props)
        app.dataset.ambiente = app.filterambiente

        app.dataset.orderby = null
        if (app.orderbylist) {
          var c = Object.keys(app.orderbylist).length
          if (c > 0) app.dataset.orderby = app.orderbylist
        }

        var ret = await app.dataset.fetch()
        if (ret.ok) {
          app.rows = app.dataset.itens
          // atualiza url
          // if (app.$route.query.t) delete app.$route.query.t
          var query = {}
          if (app.dataset.filter !== null && app.dataset.filter !== '') query.find = app.dataset.filter
          if (app.dataset.ambiente !== null) query.ambiente = app.dataset.ambiente

          if (app.dataset.customcnpj ? app.dataset.customcnpj !== '' : false) query.customcnpj = app.dataset.customcnpj
          if (app.dataset.customnumero !== null) query.customnumero = app.dataset.customnumero
          if (app.dataset.customchave !== null) query.customchave = app.dataset.customchave

          // if (app.dataset.pesoi !== null) query.pesoi = app.dataset.pesoi
          // if (app.dataset.pesof !== null) query.pesof = app.dataset.pesof
          if (app.dataset.pagination.page !== null && app.dataset.pagination.page > 1) query.page = app.dataset.pagination.page

          query.t = new Date().getTime()
          try {
            app.$router.replace({ name: app.$route.name, query: query, replace: true, append: false })
          } catch (error) {
            console.error(error)
          }
        } else {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      } catch (error) {
        app.loading = false
        app.error = error.message
      }
      app.loading = false
    }
  }
}
</script>
