<template>
<div>
  <q-page class="">
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header " >
      <div class="col-sm-12" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'" >
        <div class="col-12" v-if="error">
          <q-banner class="bg-negative text-white">{{error}}</q-banner>
        </div>
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section>
            <div class="row q-col-gutter-sm">
              <div class="col-md-6 col-xs-12">
                <q-input outlined debounce="700" v-model="filter" placeholder="Informe qualquer Informação para pesquisar" label="Procurar" clearable :dense="!$q.platform.is.mobile"
                  hint="Ex.: CNPJ, nome do cliente e etc...">
                  <template v-slot:prepend>
                    <q-icon name="search" />
                  </template>
                </q-input>
              </div>
              <div class="col-xs-12 col-md-3">
                <q-select v-model="filterstatus" label="Status" outlined emit-value stack-label map-options class="q-mr-xs" :dense="!$q.platform.is.mobile"
                  :options="[ { value: '2', label: 'Todos' }, { value: '1', label: 'Ativos' }, { value: '0', label: 'Inativo' } ]" />
              </div>
              <div class="col-12">
                <div class="q-gutter-sm">
                  <q-checkbox v-for="(option, k) in opcoesNivel.options" :key="'nivel' + k" v-model="filternivel"
                    :val="option.value" :label="option.desc" :color="option.color" />
                </div>
              </div>
            </div>
          </q-card-section>
          <q-card-section>
            <q-btn unelevated label="Consultar" color="primary" icon="search"  @click="refreshData(null)" :loading="loading"  />
          </q-card-section>
        </q-card>
        <q-card class="bg-white q-mt-md" flat  v-if="rows ? rows.length > 0 : false" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-separator v-if="$q.platform.is.mobile"  />
          <q-card-section class="q-pa-none">
            <div style="max-width: 100vw">
              <q-table :data="rows" :columns="columns" :dense="!$q.platform.is.mobile"  flat
                :loading="loading" color="primary" id="sticky-table"
                :pagination.sync="dataset.pagination"
                row-key="id"
                 :rows-per-page-options="$qtable.rowsperpageoptions"
                @request="refreshData"
                :filter="filter"
                >
                  <template v-slot:body-cell-action="props">
                    <q-td :props="props">
                      <q-btn flat round :dense="!$q.platform.is.mobile" :size="$q.platform.is.mobile ? '12px' : '8px'" icon="edit"
                        :to="{ name: 'backup.cliente.detalhe', params: { doc: props.row.cliente.doc } }">
                        <q-tooltip>Ver backups do cliente</q-tooltip>
                      </q-btn>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-cliente="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" :class="!props.row.cliente.ativo ? 'text-red' : ''" >
                        <div class="ellipsis">
                            <span class="bg-red-1 text-red rounded-borders q-pa-xs text-caption" v-if="!props.row.cliente.ativo">Inativo</span>
                            <span>
                              {{(props.row.cliente.fantasia ? props.row.cliente.fantasia !== '' : false) ? props.row.cliente.fantasia : props.row.cliente.nome }}
                            </span>
                            <span class="q-ml-md text-caption text-grey-7" v-if="(props.row.cliente.fantasia ? props.row.cliente.fantasia !== '' : false)">{{$helpers.strEllipsis(props.row.cliente.nome, 30)}}</span>
                        </div>
                      </div>
                      <q-tooltip :delay="700">
                        <div>{{props.row.cliente.fantasia}}</div>
                        <div>{{props.row.cliente.nome}}</div>
                        <div>Documento: {{props.row.cliente.doc}}</div>
                        <div>Id: {{props.row.cliente.id}}</div>
                        <div >{{props.row.cliente.cidade  + ' - ' + props.row.cliente.uf}}</div>
                      </q-tooltip>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-cidade="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" >
                        <div class="ellipsis" v-if="props.row.cliente.cidade">{{props.row.cliente.cidade  + ' - ' + props.row.cliente.uf}}</div>
                      </div>
                      <q-tooltip :delay="700">
                        <div >{{props.row.cliente.cidade  + ' - ' + props.row.cliente.uf}}</div>
                      </q-tooltip>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-totalsize="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" >
                        <div class="">{{$helpers.bytesToHumanFileSizeString(props.row.totalsize)}}</div>
                        <q-tooltip :delay="700">
                          <div>{{$helpers.bytesToHumanFileSizeString(props.row.totalsize)}}</div>
                          <div >{{$helpers.formatRS(props.row.qtdearquivos, '', 0)}} arquivos</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-controlabkp="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" >
                        <div v-if="props.row.cliente ?props.row.cliente.controlabkp : false">
                          <q-avatar size="24px" font-size="20px"
                            :text-color="props.row.cliente.controlabkp.color ? props.row.cliente.controlabkp.color : 'black'" color="grey-3"
                            :icon="props.row.cliente.controlabkp.icon" />
                          <q-tooltip :delay="700">
                            {{props.row.cliente.controlabkp.memo}}
                          </q-tooltip>
                        </div>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-qtdearquivos="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" >
                        <div class="text-caption text-grey-7">{{$helpers.formatRS(props.row.qtdearquivos, '', 0)}}</div>
                        <q-tooltip :delay="700">
                          <div>{{$helpers.bytesToHumanFileSizeString(props.row.totalsize)}}</div>
                          <div >{{$helpers.formatRS(props.row.qtdearquivos, '', 0)}} arquivos</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-ultimolastmodified="props">
                    <q-td :props="props" >
                      <div >
                        <div v-if="props.row.ultimolastmodified">
                          <div>{{$helpers.datetimeRelativeToday(props.row.ultimolastmodified)}}</div>
                          <q-tooltip>
                            <div v-if="props.row.ultimolastmodified">Último backup em {{ $helpers.datetimeToBR(props.row.ultimolastmodified, false, true) }}</div>
                          </q-tooltip>
                        </div>
                      </div>
                    </q-td>
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
            <q-toolbar-title>Consulta de backup</q-toolbar-title>
            <q-btn  icon="search" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
          </q-toolbar>
        </q-page-sticky>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
import { BackupNivel } from 'src/mvc/models/enums/backuptype'
import Backups from 'src/mvc/collections/backups.js'
export default {
  name: 'servidores.index',
  components: {
  },
  data () {
    var opcoesNivel = new BackupNivel()
    var dataset = new Backups()
    return {
      dataset,
      opcoesNivel,
      rows: [],
      filternivel: ['8', '1', '2'],
      filterstatus: '1',
      filter: '',
      columns: [
        { name: 'action', align: 'left', label: '*', field: 'id' },
        { name: 'cliente', align: 'left', label: 'Cliente', field: 'cliente', filterconfig: { value: null } },
        { name: 'cidade', align: 'left', label: 'Cidade', field: 'cidade', filterconfig: { value: null } },
        { name: 'ultimolastmodified', align: 'center', label: 'Último backup', field: 'ultimolastmodified' },
        { name: 'controlabkp', align: 'center', label: 'Nível', field: 'controlabkp' },
        { name: 'qtdearquivos', align: 'right', label: 'Qtde', field: 'qtdearquivos', filterconfig: { value: null } },
        { name: 'totalsize', align: 'right', label: 'Arquivos', field: 'totalsize', filterconfig: { value: null } }
      ],
      loading: false,
      posting: false,
      error: null
    }
  },
  async mounted () {
    var app = this
    if (app.$route.query) await app.queryRead(app.$route.query)
    await app.refreshData(null)
  },

  methods: {
    async queryRead (pQuery) {
      var app = this
      if (pQuery) {
        if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)
        if (pQuery.status) {
          app.filterstatus = pQuery.status
        }
        if (pQuery.nivel) {
          app.filternivel = pQuery.nivel.split(',')
        }
        if (pQuery.find) app.filter = pQuery.find
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
        app.dataset.filter = app.filter
        app.dataset.status = app.filterstatus
        app.dataset.nivel = app.filternivel

        app.dataset.orderby = null
        if (app.orderbylist) {
          var c = Object.keys(app.orderbylist).length
          if (c > 0) app.dataset.orderby = app.orderbylist
        }

        var ret = await app.dataset.fetchPorCliente()
        if (ret.ok) {
          app.rows = app.dataset.itens
          // atualiza url
          // if (app.$route.query.t) delete app.$route.query.t
          var query = {}
          if (app.dataset.filter !== null && app.dataset.filter !== '') query.find = app.dataset.filter
          if (app.dataset.status !== null) query.status = app.dataset.status
          if (app.dataset.nivel !== null) query.nivel = app.dataset.nivel.join(',')

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
