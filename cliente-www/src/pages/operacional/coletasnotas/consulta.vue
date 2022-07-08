<template>
<div>
  <q-page class="">
    <div class="row doc-header" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'">
      <div class="col-sm-12" >
        <q-card class="bg-white" flat :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-sm-12 col-md-6">
                <div class="col-12 ">
                  <div class="ellipsis">Informe qualquer Informação para pesquisar</div>
                  <div class="text-caption  text-grey-7 ellipsis">Ex.: CNPJ, número, remetente, destinatário, coleta...</div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-6">
                  <q-input outlined debounce="700" v-model="filter" placeholder="Procurar" label="Procurar" clearable>
                    <template v-slot:prepend>
                      <q-icon name="search" />
                    </template>
                  </q-input>
                </div>
              </div>
              <div class="col-sm-12 col-md-6">
                <div class="col-12 ">
                  <div class="ellipsis">Você também pode restrigir a consulta pela data de coleta</div>
                  <div class="text-caption text-grey-7 ellipsis">Essa opção é opcional</div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-6">
                  <q-field label="Período de coleta" stack-label outlined  >
                    <template v-slot:prepend>
                      <q-btn color="primary" icon="today" round dense  @click="actShowFilter(null, 2)" flat />
                    </template>
                    <template v-slot:control>
                      <div class="self-center no-outline cursor-pointer" tabindex="0" @click="actShowFilter(null, 2)">
                        <span v-if="columns[2].filterconfig.value ? (columns[2].filterconfig.value.dti) || (columns[2].filterconfig.value.dtf) : false">
                          de {{columns[2].filterconfig.value.dti ? $helpers.dateToBR(columns[2].filterconfig.value.dti) : 'qualquer data'}}
                          até  {{columns[2].filterconfig.value.dtf ?  $helpers.dateToBR(columns[2].filterconfig.value.dtf) : 'qualquer data'}}
                        </span>
                        <span v-else>
                            Nenhum período informado
                        </span>
                      </div>
                    </template>
                  </q-field>
                </div>
              </div>
            </div>
          </q-card-section>
          <q-card-section>
            <div class="row">
            <q-btn unelevated label="Consultar" color="primary" icon="search"  @click="refreshData(null)" :loading="loading"  />
            </div>
          </q-card-section>
        </q-card>
       <q-card class="bg-white q-mt-md" flat  v-if="rows ? rows.length > 0 : false" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-separator  />
          <q-card-section class="q-pa-none">
             <div style="max-width: 100vw">
             <q-table :data="rows" :columns="columns" :dense="!$q.platform.is.mobile" :loading="loading" flat
              :pagination.sync="dataset.pagination" row-key="id" :rows-per-page-options="$qtable.rowsperpageoptions"
              @request="refreshData" :filter="filter"
              >
                <!-- colunas -->
                <template v-slot:body-cell-remetentenome="props">
                  <q-td :props="props" @click="actRastrear(props.row)" class="cursor-pointer" >
                    <div >
                      <div style="max-width: 200px" class="ellipsis">{{props.row.remetentenome}}</div>
                      <div style="max-width: 200px" class="ellipsis text-caption">{{$helpers.mascaraDocCPFCNPJ(props.row.remetentecnpj)}}</div>
                      <q-tooltip :delay="700">
                        <div>{{props.row.remetentenome}}</div>
                        <div>CNPJ: {{$helpers.mascaraDocCPFCNPJ(props.row.remetentecnpj)}}</div>
                      </q-tooltip>
                    </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-destinatarionome="props">
                  <q-td :props="props" @click="actRastrear(props.row)" class="cursor-pointer" >
                    <div>
                      <div style="max-width: 200px" class="ellipsis">{{props.row.destinatarionome}}</div>
                      <div style="max-width: 200px" class="ellipsis text-caption">{{$helpers.mascaraDocCPFCNPJ(props.row.destinatariocnpj)}}</div>
                      <q-tooltip :delay="700">
                        <div>{{props.row.destinatarionome}}</div>
                        <div>CNPJ: {{$helpers.mascaraDocCPFCNPJ(props.row.destinatariocnpj)}}</div>
                      </q-tooltip>
                    </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-dhlocal_data="props">
                  <q-td :props="props" @click="actRastrear(props.row)" class="cursor-pointer" >
                    <div v-if="!props.row.dhlocal_data" class="text-caption ellipsis">
                      Nenhum
                    </div>
                    <div v-if="props.row.dhlocal_data" class="ellipsis">
                      {{ $helpers.datetimeToBR(props.row.dhlocal_data, false, true) }}
                      <q-tooltip :delay="700">
                        <div>Data e hora da coleta no aplicativo pelo motorista</div>
                        <div>{{$helpers.datetimeToBR(props.row.dhlocal_data, false, false)}}</div>
                      </q-tooltip>
                    </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-idcoleta="props">
                <q-td :props="props" @click="actRastrear(props.row)" class="cursor-pointer" >
                  <div v-if="props.row.idcoleta ? !(props.row.idcoleta > 0) : true">
                    ?
                  </div>
                  <div v-else class="ellipsis">
                    <span v-if="props.row.idcoleta ? props.row.idcoleta > 0 : false">{{$helpers.formatMilhar(props.row.idcoleta)}}</span>
                    <q-icon name="info" color="red" size="20px" v-if="props.row.coleta ? (props.row.coleta.cnpjorigem !== props.row.remetentecnpj) : false" class="q-mr-xs">
                      <q-tooltip :delay="700">
                        O CNPJ ({{$helpers.mascaraDocCPFCNPJ(props.row.remetentecnpj)}}) emissor da NF-e é diferente do CNPJ {{$helpers.mascaraDocCPFCNPJ(props.row.coleta.cnpjorigem)}} emissor da coleta
                      </q-tooltip>
                    </q-icon>
                    <q-icon name="info" color="red" size="20px" v-if="props.row.coleta ? (props.row.coleta.cnpjdestino !== props.row.destinatariocnpj) : false" class="q-mr-xs">
                      <q-tooltip :delay="700">
                        O CNPJ ({{$helpers.mascaraDocCPFCNPJ(props.row.destinatariocnpj)}}) do destinatário da NF-e é diferente do CNPJ {{$helpers.mascaraDocCPFCNPJ(props.row.coleta.cnpjdestino)}} do destinatário da coleta
                      </q-tooltip>
                    </q-icon>
                    <q-icon name="grading" size="20px" color="blue" v-if="props.row.coletaavulsa" >
                      <q-tooltip :delay="700">
                        <div>Originado de uma coleta avulsa</div>
                        <div v-if="props.row.idcoletaavulsa ? props.row.idcoletaavulsa > 0 : false">Coleta avulsa associada a coleta #{{props.row.idcoletaavulsa}}</div>
                        <div v-if="(props.row.idcoleta ? !(props.row.idcoleta > 0) : true)">Nenhum coleta gerada</div>
                      </q-tooltip>
                    </q-icon>
                  </div>
                </q-td>
                </template>
                <template v-slot:body-cell-entregastatus="props">
                  <q-td :props="props"  @click="actRastrear(props.row)" class="cursor-pointer" >
                    <div class="ellipsis text-white rounded-borders q-px-xs text-weight-bold" :class="'bg-' + props.row.entregastatus.color">
                      {{props.row.entregastatus.group}}
                      <q-tooltip :delay="500">
                        <div>Status: {{props.row.entregastatus.description}}</div>
                        <div>Carga de entraga: {{props.row.cargaentradaid ? props.row.cargaentradaid : 'Sem carga de entrada'}}</div>
                        <div>Última atualização: {{ props.row.entregaupdated_at ? $helpers.datetimeToBR(props.row.entregaupdated_at, false, true) : '-'}}</div>
                      </q-tooltip>
                    </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-unidadeatual="props">
                  <q-td :props="props" @click="actRastrear(props.row)" class="cursor-pointer" >
                    <div style="max-width: 120px" class="ellipsis cursor-pointer" v-if="props.row.unidadeatual ? props.row.unidadeatual.id > 0 : false" >
                      {{props.row.unidadeatual.fantasia}}
                      <q-tooltip :delay="500">
                        <div class="text-subtitle2 text-weight-bold">{{props.row.unidadeatual.fantasia}}</div>
                        <div>{{props.row.unidadeatual.razaosocial}}</div>
                        <div>{{ props.row.unidadeatual.cnpj ? $helpers.mascaraDocCPFCNPJ(props.row.unidadeatual.cnpj) : '-'}}</div>
                      </q-tooltip>
                    </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-status="props">
                  <q-td :props="props" @click="actRastrear(props.row)" class="cursor-pointer" >
                    <div style="max-width: 150px" class="ellipsis">
                      <q-icon :name="props.row.status.icon" :color="props.row.status.color" size="18px" />
                      {{props.row.status.code }}
                      {{props.row.status ? props.row.status.msgshort : '?'}}
                      <q-tooltip :delay="700">
                        <div v-if="props.row.status">
                          <div>{{props.row.status.msg}}</div>
                          <div v-if="props.row.status.code === 4" class="text-red">
                            {{ props.row.baixanfemsg === '' ? 'Erro ao processar XML' : props.row.baixanfemsg }}
                          </div>
                        </div>
                      </q-tooltip>
                    </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-notanumero="props">
                  <q-td :props="props" @click="actRastrear(props.row)" class="cursor-pointer" >
                    <div class="cursor-pointer" style="width: 70px;">
                      {{props.row.notanumero}}
                      <q-tooltip :delay="700">
                        <div class="q-mb-md">Clique aqui para abrir o PDF da chave</div>

                        <div v-if="props.row.xmlprocessado">
                          <div v-if="props.row.notadh">Data emissão: {{props.row.notadh}}</div>
                          <div v-if="props.row.notavalor">Valor: {{props.row.notavalor}}</div>
                          <div v-if="props.row.notachave">Chave: {{props.row.notachave}}</div>
                        </div>
                        <div v-else>
                            Nota ainda não foi processada
                        </div>
                      </q-tooltip>
                    </div>
                  </q-td>
                </template>
                <!-- colunas -->
            </q-table>
             </div>
          </q-card-section>
        </q-card>
      </div>
      <q-page-sticky position="top" expand>
        <q-toolbar class="bg-white text-primary shadow-up-21">
          <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
          <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
          <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
          <q-toolbar-title>Notas Fiscais Coletadas</q-toolbar-title>
          <q-btn  icon="sync" :label="$q.platform.is.mobile ? '' : 'Atualizar'" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
        </q-toolbar>
      </q-page-sticky>
    </div>
  </q-page>
  <!-- filtros -->
  <q-dialog v-model="showfilter" persistent >
    <q-card>
      <q-card-actions>
         <tablefiltersituacaocoletaavulsa v-if="showfilteridx >= 0 ? columns[showfilteridx].filterconfig.tipo == 'tablefiltersituacaocoletaavulsa' : false"
          :config="showfilteridx >= 0 ? columns[showfilteridx].filterconfig : null" @close="showfilter = false" @update="actFilterUpdated" />
       <tablefilterstatusprocessamento v-if="showfilteridx >= 0 ? columns[showfilteridx].filterconfig.tipo == 'tablefilterstatusprocessamento' : false"
          :config="showfilteridx >= 0 ? columns[showfilteridx].filterconfig : null" @close="showfilter = false" @update="actFilterUpdated" />
        <tablefilterperiodo v-if="showfilteridx >= 0 ? columns[showfilteridx].filterconfig.tipo == 'tablefilterperiodo' : false"
          :config="showfilteridx >= 0 ? columns[showfilteridx].filterconfig : null" @close="showfilter = false" @update="actFilterUpdated" />
      </q-card-actions>
    </q-card>
  </q-dialog>
  <!-- filtros -->

</div>
</template>

<script>
import NFe from 'src/mvc/models/nfe.js'
import ColetasNotas from 'src/mvc/collections/coletasnotas.js'
import tablefiltersituacaocoletaavulsa from './filters/cpn-input-tablefilter-situacaocoletaavulsa.vue'
import tablefilterstatusprocessamento from './filters/cpn-input-tablefilter-statusprocessamento.vue'
import tablefilterperiodo from './filters/cpn-input-tablefilter-periodo'
export default {
  name: 'coletasnotas.consulta',
  components: {
    tablefilterperiodo,
    tablefiltersituacaocoletaavulsa,
    tablefilterstatusprocessamento
  },
  data () {
    var dataset = new ColetasNotas()
    return {
      dataset,
      rows: [],
      filter: '',
      showfilter: false,
      showfilteridx: -1,
      orderbylist: {
        dhlocal_data: 'desc'
      },
      columns: [
        { name: 'entregastatus', align: 'center', label: 'Status', field: 'cargaentradaid', filterconfig: { value: null, sorted: true } },
        { name: 'notanumero', align: 'center', label: 'Número Nota', field: 'notanumero', filterconfig: { value: null, sorted: true } },
        { name: 'dhlocal_data', align: 'center', label: 'Data da coletagem', field: 'dhlocal_data', filterconfig: { tipo: 'tablefilterperiodo', value: null, label: '', info: 'Período para data de coletagem', sorted: true } },
        { name: 'idcoleta', align: 'center', label: 'Coleta', field: 'idcoleta', filterconfig: { value: null, sorted: true } },
        { name: 'remetentenome', align: 'left', label: 'Remetente', field: 'remetentenome', filterconfig: { value: null, sorted: true } },
        { name: 'destinatarionome', align: 'left', label: 'Destinatário', field: 'destinatarionome', filterconfig: { value: null, sorted: true } },
        { name: 'unidadeatual', align: 'center', label: 'Unidade Atual', field: 'unidadeatual', filterconfig: { value: null, sorted: true } }
      ],
      loading: false,
      posting: false,
      msgError: ''
    }
  },
  async mounted () {
    var app = this
    if (app.$route.query) app.queryRead(app.$route.query)
    await app.refreshData(null)
  },

  methods: {
    async actRastrear (pRow) {
      this.$router.push({ name: 'rastrear', params: { chave: pRow.notachave } })
    },
    async showNotaPDF (pChave) {
      var app = this
      var nfe = new NFe()
      var ret = await nfe.dialogShow(app, pChave)
      if (!ret.ok) {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {})
        }
      }
    },
    async queryRead (pQuery) {
      var app = this
      if (pQuery) {
        if (pQuery.pagesize) {
          var pagesize = parseInt(pQuery.pagesize)
          if (app.$qtable.rowsperpageoptions.indexOf(pagesize) >= 0) app.dataset.pagination.rowsPerPage = pagesize
        }
        if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)

        var sortby = null
        if (pQuery.sortby) {
          sortby = JSON.parse(pQuery.sortby)
          if (Object.keys(sortby).length > 0) app.orderbylist = sortby
        }

        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.filterconfig) {
            if ((element.name === 'notanumero') && (pQuery.notanumero)) element.filterconfig.value = app.$route.query.notanumero
            if ((element.name === 'idcoleta') && (pQuery.idcoleta)) element.filterconfig.value = app.$route.query.idcoleta
            if (element.name === 'remetentenome' && pQuery.remetentenome) element.filterconfig.value = pQuery.remetentenome
            if (element.name === 'destinatarionome' && pQuery.destinatarionome) element.filterconfig.value = pQuery.destinatarionome
            if (element.name === 'remetentecnpj' && pQuery.remetentecnpj) element.filterconfig.value = pQuery.remetentecnpj
            if (element.name === 'destinatariocnpj' && pQuery.destinatariocnpj) element.filterconfig.value = pQuery.destinatariocnpj
            if (element.name === 'motorista' && pQuery.motorista) element.filterconfig.value = pQuery.motorista
            if (element.name === 'id' && pQuery.id) element.filterconfig.value = pQuery.id

            if (element.name === 'status' && pQuery.status) element.filterconfig.value = JSON.parse(pQuery.status)
            if (element.name === 'coletaavulsa' && pQuery.coletaavulsa) element.filterconfig.value = pQuery.coletaavulsa
            if (element.name === 'dhlocal_data') {
              if (pQuery.dhlocal_datai || pQuery.dhlocal_dataf) {
                element.filterconfig.value = { dti: pQuery.dhlocal_datai, dtf: pQuery.dhlocal_dataf }
              } else {
                element.filterconfig.value = null
              }
            }
          }
        }
      }
    },
    async actFilterUpdated (val) {
      var app = this
      app.showfilter = false
      app.columns[app.showfilteridx].filterconfig.value = val
      app.showfilteridx = -1
      app.refreshData(null)
    },
    async actClearFilter (props, pIndex = -1) {
      var app = this
      var idx = pIndex
      if (props) {
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.name === props.col.name) {
            idx = index
            break
          }
        }
      }
      app.columns[idx].filterconfig.value = null
      app.showfilter = false
      app.refreshData(null)
    },
    actChangeSortBy (props) {
      var app = this
      if (props) {
        var idx = -1
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.name === props.col.name) {
            idx = index
            break
          }
        }
        if (idx >= 0) {
          if (app.columns[idx].filterconfig.sorted) {
            var name = app.columns[idx].name
            var ord = app.orderbylist[name]
            if (ord) {
              ord = (ord === 'asc') ? 'desc' : 'asc'
            } else {
              ord = 'asc'
            }
            app.orderbylist = {}
            app.orderbylist[name] = ord
          }
        }
      }
      app.refreshData(null)
    },
    actShowFilter (props, pIndex = -1) {
      var app = this
      app.showfilteridx = pIndex
      if (props) {
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.name === props.col.name) {
            app.showfilteridx = index
            break
          }
        }
      }
      this.showfilter = true
    },
    actInputFilter () {
      var app = this
      if (app.loading) return
      app.refreshData(null)
    },
    async refreshData (props) {
      var app = this
      app.loading = true
      var dialog = app.$helpers.dialogProgress(app, 'Aguarde!', 'Consultando notas...')
      try {
        app.msgError = ''
        app.dataset.readPropsTable(props)
        if (app.$q.platform.is.mobile) app.dataset.pagination.rowsPerPage = 50
        app.dataset.notanumero = null
        app.dataset.status = null
        app.dataset.coletaavulsa = null
        app.dataset.idcoleta = null
        app.dataset.dhlocal_datai = null
        app.dataset.dhlocal_dataf = null
        app.dataset.remetentenome = null
        app.dataset.remetentecnpj = null
        app.dataset.destinatarionome = null
        app.dataset.destinatariocnpj = null
        app.dataset.motorista = null
        app.dataset.id = null

        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.filterconfig) {
            if (element.name === 'status') app.dataset.status = element.filterconfig.value
            if (element.name === 'coletaavulsa') app.dataset.coletaavulsa = element.filterconfig.value
            if (element.name === 'notanumero') app.dataset.notanumero = element.filterconfig.value
            if (element.name === 'idcoleta') app.dataset.idcoleta = element.filterconfig.value
            if (element.name === 'remetentenome') app.dataset.remetentenome = element.filterconfig.value
            if (element.name === 'remetentecnpj') app.dataset.remetentecnpj = element.filterconfig.value
            if (element.name === 'destinatarionome') app.dataset.destinatarionome = element.filterconfig.value
            if (element.name === 'destinatariocnpj') app.dataset.destinatariocnpj = element.filterconfig.value
            if (element.name === 'motorista') app.dataset.motorista = element.filterconfig.value
            if (element.name === 'id') app.dataset.id = element.filterconfig.value

            if (element.name === 'dhlocal_data') {
              if (element.filterconfig.value) {
                if (element.filterconfig.value.dti) app.dataset.dhlocal_datai = element.filterconfig.value.dti
                if (element.filterconfig.value.dtf) app.dataset.dhlocal_dataf = element.filterconfig.value.dtf
              }
            }
          }
        }
        app.dataset.orderby = null
        if (app.orderbylist) {
          var c = Object.keys(app.orderbylist).length
          if (c > 0) app.dataset.orderby = app.orderbylist
        }

        var ret = await app.dataset.fetch()
        if (ret.ok) {
          app.rows = app.dataset.itens
          // // atualiza url
          var query = {}
          if (app.dataset.filter !== null && app.dataset.filter !== '') query.find = app.dataset.filter
          if (app.dataset.motorista !== null && app.dataset.motorista !== '') query.motorista = app.dataset.motorista
          if (app.dataset.remetentenome !== null && app.dataset.remetentenome !== '') query.remetentenome = app.dataset.remetentenome
          if (app.dataset.remetentecnpj !== null && app.dataset.remetentecnpj !== '') query.remetentecnpj = app.dataset.remetentecnpj
          if (app.dataset.destinatarionome !== null && app.dataset.destinatarionome !== '') query.destinatarionome = app.dataset.destinatarionome
          if (app.dataset.destinatariocnpj !== null && app.dataset.destinatariocnpj !== '') query.destinatariocnpj = app.dataset.destinatariocnpj
          if (app.dataset.notanumero !== null && (parseInt(app.dataset.notanumero) > 0)) query.notanumero = app.dataset.notanumero
          if (app.dataset.idcoleta !== null && (parseInt(app.dataset.idcoleta) > 0)) query.idcoleta = app.dataset.idcoleta
          if (app.dataset.id !== null && (parseInt(app.dataset.id) > 0)) query.id = app.dataset.id
          if (app.dataset.coletaavulsa !== null) query.coletaavulsa = app.dataset.coletaavulsa
          if (app.dataset.status !== null) query.status = JSON.stringify(app.dataset.status)

          if (app.dataset.dhlocal_datai !== null) query.dhlocal_datai = app.dataset.dhlocal_datai
          if (app.dataset.dhlocal_dataf !== null) query.dhlocal_dataf = app.dataset.dhlocal_dataf
          if (app.dataset.pagination.page !== null && app.dataset.pagination.page > 1) query.page = app.dataset.pagination.page
          if ((app.dataset.pagination.rowsPerPage !== null) && (app.dataset.pagination.rowsPerPage > 0) && (parseInt(app.dataset.pagination.rowsPerPage) !== 20)) query.pagesize = app.dataset.pagination.rowsPerPage
          if (app.dataset.orderby !== null) query.sortby = JSON.stringify(app.dataset.orderby)

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
        app.$helpers.showDialog({ ok: false, msg: error.message }, true)
      } finally {
        dialog.hide()
        app.loading = false
      }
    }
  }
}
</script>
