<template>
<div>
  <q-page class="">
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header " >
      <div class="col-sm-12" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'" >
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section class="col-12" v-if="error">
            <q-banner class="bg-negative text-white">{{error}}</q-banner>
          </q-card-section>
          <q-card-section v-show="showfilters">
            <div class="row q-col-gutter-sm">
              <div class="col-xs-12">
                <selectbackupmesano outlined :doc="cnpj" v-if="cnpj ? cnpj !== '' : false" v-model="filtermesano" />
              </div>
            </div>
          </q-card-section>
          <q-card-section class="q-gutter-x-sm">
            <q-btn unelevated label="Consultar" color="primary" icon="search"  @click="refreshData(null)" :loading="loading"  />
            <q-btn unelevated :label="$q.platform.is.mobile ? '' : 'Forçar sync'" color="secondary" icon="sync"  @click="actSync" :loading="loading" >
                <q-tooltip :delay="500">
                  Essa função faz o sincronismo entre os arquivos em disco com o banco de dados, atualizando as informações de arquivos.
                </q-tooltip>
            </q-btn>
            <q-btn unelevated :label="$q.platform.is.mobile ? '' : 'Remover'" color="red" icon="delete"  @click="actDeleteSelected"
              :disable="loading || (selected_rows ? selected_rows.length <= 0 : true)" >
              <q-badge color="yellow" text-color="black" :label="selected_rows.length " floating v-if="(selected_rows ? selected_rows.length > 0 : false)" />
            </q-btn>
          </q-card-section>
        </q-card>
        <q-card class="bg-white q-mt-md" flat  v-if="rows ? rows.length > 0 : false" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-separator v-if="$q.platform.is.mobile"  />
          <q-card-section class="q-pa-none">
            <div style="max-width: 100vw">
              <q-table :data="rows" :columns="columns" :dense="!$q.platform.is.mobile"  flat
                :loading="loading" color="primary" id="sticky-table"
                :pagination.sync="dataset.pagination"
                row-key="md5"
                selection="multiple" :selected.sync="selected_rows"
                 :rows-per-page-options="$qtable.rowsperpageoptions"
                @request="refreshData"
                >
                  <template v-slot:body-cell-action="props">
                    <q-td :props="props">
                      <q-btn flat round :dense="!$q.platform.is.mobile" :size="$q.platform.is.mobile ? '12px' : '8px'" icon="file_download"
                        @click="actDownload(props.row)" >
                        <q-tooltip>Download do backup</q-tooltip>
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
                  <template v-slot:body-cell-size="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" >
                        <div class="">{{$helpers.bytesToHumanFileSizeString(props.row.size)}}</div>
                        <q-tooltip :delay="700">
                          <div>{{$helpers.bytesToHumanFileSizeString(props.row.size)}}</div>
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
                  <template v-slot:body-cell-downloadcount="props">
                    <q-td :props="props" >
                      <div v-if="props.row.downloadcount ?props.row.downloadcount > 0  : false">
                        {{props.row.downloadcount}} x
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-bucket="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" >
                        <div class="text-caption text-grey-7">{{props.row.bucket === 1 ? 'EUA' : 'BR'}}</div>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-lastmodified="props">
                    <q-td :props="props" >
                      <div >
                        <div v-if="props.row.lastmodified">
                          <div>{{$helpers.datetimeToBR(props.row.lastmodified, false, true)}}</div>
                          <q-tooltip>
                            <div v-if="props.row.lastmodified">{{ $helpers.datetimeRelativeToday(props.row.lastmodified) }}</div>
                          </q-tooltip>
                        </div>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:bottom-row>
                    <q-tr>
                      <q-td></q-td>
                      <q-td colspan="6" class="text-right">
                        <div>
                          <span v-if="(totalbytesselected > 0) && (selected_rows ? selected_rows.length > 0 :false) " class="rounded-borders bg-yellow text-body q-px-xs q-mr-md">
                            Selecionado: {{$helpers.bytesToHumanFileSizeString(totalbytesselected)}}
                          </span>
                          <span class="text-weight-bold">Total: {{$helpers.bytesToHumanFileSizeString(totalbytes)}}</span>
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
            <q-toolbar-title>Backup - Cliente: {{cnpj}}</q-toolbar-title>
            <q-btn  :icon="showfilters ? 'filter_alt_off' : 'filter_alt'" :round="$q.platform.is.mobile" flat @click="showfilters = !showfilters" />
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
import selectbackupmesano from 'src/components/cnp-select-backupmesano'
export default {
  name: 'servidores.index',
  components: {
    selectbackupmesano
  },
  data () {
    var opcoesNivel = new BackupNivel()
    var dataset = new Backups()
    return {
      dataset,
      opcoesNivel,
      rows: [],
      filternivel: ['8', '1', '2'],
      filtermesano: null,
      cnpj: '',
      showfilters: false,
      selected_rows: [],
      columns: [
        { name: 'action', align: 'left', label: '*', field: 'id' },
        { name: 'name', align: 'left', label: 'Arquivo', field: 'name', filterconfig: { value: null } },
        { name: 'bucket', align: 'left', label: 'Bucket', field: 'bucket', filterconfig: { value: null } },
        { name: 'lastmodified', align: 'center', label: 'Data backup', field: 'lastmodified' },
        { name: 'downloadcount', align: 'center', label: 'Download', field: 'downloadcount', filterconfig: { value: null } },
        { name: 'size', align: 'right', label: 'Tamanho', field: 'size', filterconfig: { value: null } }
      ],
      loading: false,
      posting: false,
      error: null
    }
  },
  async mounted () {

  },
  computed: {
    totalbytes: function () {
      var app = this
      if (!app.rows) return 0
      var total = 0
      for (let index = 0; index < app.rows.length; index++) {
        const element = app.rows[index]
        total = parseInt(total + parseInt(element.size))
      }
      return total
    },
    totalbytesselected: function () {
      var app = this
      if (!app.selected_rows) return 0
      var total = 0
      for (let index = 0; index < app.selected_rows.length; index++) {
        const element = app.selected_rows[index]
        total = parseInt(total + parseInt(element.size))
      }
      return total
    }
  },
  async activated () {
    var app = this
    var force = (app.cnpj ? app.cnpj === '' : true) || (app.cnpj !== app.$route.params.doc)
    app.cnpj = app.$route.params.doc
    if (app.$route.query) await app.queryRead(app.$route.query)
    if (force) await app.refreshData(null)
    // called on initial mount
    // and every time it is re-inserted from the cache
  },
  deactivated () {
    // called when removed from the DOM into the cache
    // and also when unmounted
  },

  methods: {
    async queryRead (pQuery) {
      var app = this
      if (pQuery) {
        if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)
        if (pQuery.mesano) {
          app.filtermesano = pQuery.mesano
        }
        // if (pQuery.nivel) {
        //   app.filternivel = pQuery.nivel.split(',')
        // }
        // if (pQuery.find) app.filter = pQuery.find
      }
    },
    async actSync () {
      var app = this
      var dialog = app.$helpers.dialogProgress(app, 'Aguarde o processo terminar!', 'Sincronizando...')
      try {
        var ret = await app.dataset.syncSend(app.cnpj)
        if (ret.ok) {
          app.$q.notify({
            color: 'positive',
            icon: 'sync',
            timeout: 2500,
            caption: ret.msg,
            message: 'Sincronismo de backup'
          })
          app.refreshData()
        } else {
          app.$helpers.showDialog(ret)
        }
      } catch (error) {
        app.$helpers.showDialog({ ok: false, msg: error.message, warning: true })
      } finally {
        dialog.hide()
      }
    },
    async actDownload (row) {
      var app = this
      app.$q.dialog({
        title: 'Download de backup',
        message: 'Justifique o motivo do download?',
        prompt: {
          model: '',
          isValid: val => val.length > 2, // << here is the magic
          type: 'text' // optional
        },
        cancel: true,
        persistent: true
      }).onOk(async justificativa => {
        if (justificativa ? justificativa.length > 2 : false) {
          var dialog = app.$helpers.dialogProgress(app, 'Em alguns segundos o download iniciará', 'Preparando download...')
          try {
            var ret = await row.download(justificativa)
            if (ret.ok) {
              app.$helpers.forceFileDownload(ret.url)
            } else {
              app.$helpers.showDialog(ret)
            }
          } catch (error) {
            app.$helpers.showDialog({ ok: false, msg: error.message, warning: true })
          } finally {
            dialog.hide()
          }
        } else {
          app.$helpers.showDialog({ ok: false, msg: 'Informe no mínimo 3 e no máximo 100 caracteres' })
        }
      })
    },
    async actDeleteSelected () {
      var app = this
      if (app.selected_rows ? app.selected_rows.length <= 0 : true) return
      var dialog = app.$helpers.dialogProgress(app, 'Excluido arquivo de backup', 'Aguarde...')
      try {
        var dados = []
        if (app.selected_rows ? app.selected_rows.length > 0 : false) {
          for (let index = 0; index < app.selected_rows.length; index++) {
            dados.push(app.selected_rows[index].md5)
          }
        }
        var ret = await app.dataset.deleteSend(dados)
        if (ret.ok) {
          app.$q.notify({
            color: 'positive',
            icon: 'delete',
            timeout: 2500,
            caption: ret.msg,
            message: 'Arquivos de backup'
          })
        } else {
          app.$helpers.showDialog(ret)
        }
      } catch (error) {
        app.$helpers.showDialog({ ok: false, msg: error.message, warning: true })
      } finally {
        dialog.hide()
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
        app.dataset.mesano = app.filtermesano

        app.dataset.orderby = null
        if (app.orderbylist) {
          var c = Object.keys(app.orderbylist).length
          if (c > 0) app.dataset.orderby = app.orderbylist
        }

        var ret = await app.dataset.fetchDetalhe(app.cnpj)
        if (ret.ok) {
          app.selected_rows = []
          app.rows = app.dataset.itens
          // atualiza url
          // if (app.$route.query.t) delete app.$route.query.t
          var query = {}
          // if (app.dataset.filter !== null && app.dataset.filter !== '') query.find = app.dataset.filter
          // if (app.dataset.status !== null) query.status = app.dataset.status
          if (app.dataset.mesano !== null) query.mesano = app.dataset.mesano

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
