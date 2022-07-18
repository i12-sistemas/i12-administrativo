<template>
<div>
  <q-page class="">
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header" >
      <div class="col-sm-12" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'" >
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section v-if="error">
            <q-banner class="bg-negative text-white">{{error}}</q-banner>
          </q-card-section>
          <q-card-section>
            <div class="row q-col-gutter-sm">
              <div class="col-xs-12 col-md-4">
                <q-input outlined debounce="700" @input="refreshData(null, true)" v-model="filter" placeholder="Informe qualquer Informação para pesquisar" label="Procurar" clearable stack-label
                  :dense="!$q.platform.is.mobile"
                  hint="Ex.: CNPJ, nome do cliente e etc...">
                  <template v-slot:append>
                    <q-btn flat color="primary" icon="search"  @click="refreshData(null, false)" :loading="loading" round dense/>
                  </template>
                </q-input>
              </div>
              <div class="col-xs-12 col-md-4">
                <q-select v-model="filterstatus" label="Status" outlined emit-value stack-label map-options class="q-mr-xs" :dense="!$q.platform.is.mobile"
                  @input="refreshData(null)"
                  :options="[ { value: 'todos', label: 'Todos' }, { value: 'expirado', label: 'Expirados' }, { value: 'bloqueado', label: 'Bloqueados' }, { value: 'naoaplicado', label: 'Não aplicados' }, { value: 'semlicenca', label: 'Sem licença' } ]" />
              </div>
            </div>
          </q-card-section>
        </q-card>
        <q-card class="bg-white q-mt-md" flat  v-if="rows ? rows.length > 0 : false" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-separator v-if="$q.platform.is.mobile"  />
          <q-card-section class="q-pa-none">
            <!-- celular -->
            <div style="max-width: 100vw" v-if="$q.platform.is.mobile">
              <q-list >
                <q-infinite-scroll @load="onLoadInfinite" :offset="250" :disable="!dataset.temmaisregistros">
                  <q-item v-for="(row, index) in rows" :key="index" clickable v-ripple :class="$helpers.isOdd(index) ? '' : 'bg-grey-2'"
                      @click="actAddLicencaDialog(row)">
                    <q-item-section>
                      <q-item-label class="text-subtitle2" lines="1">{{row.cliente.nome}}</q-item-label>
                      <q-item-label v-if="row.datavencimento">
                        {{ (row.expirado ? 'Expirado ' : 'Expira') + ' ' + $helpers.datetimeRelativeToday(row.datavencimento) + ' - ' +$helpers.dateToBR(row.datavencimento)}}
                      </q-item-label>
                      <q-item-label v-if="row.databloqueio">
                        {{ (row.bloqueado ? 'Bloqueado ' : 'Bloqueio') + ' ' + $helpers.datetimeRelativeToday(row.databloqueio) + ' - ' +$helpers.dateToBR(row.databloqueio)}}
                      </q-item-label>
                      <q-item-label v-if="row.datavencimento">
                        <div v-if="row.dataativacao" class="text-caption">
                          Ativado: {{$helpers.datetimeToBR(row.dataativacao)}} - {{ $helpers.datetimeRelativeToday(row.dataativacao) }}
                        </div>
                        <div v-else>Nunca foi ativado</div>
                      </q-item-label>
                      <q-item-label v-if="!row.datavencimento">
                        Sem licença gerada!
                      </q-item-label>

                      <q-item-label v-if="(row.cliente ? row.cliente.saldovencido : false)" class="text-red text-weight-bold">
                        <div>Cliente com débito em atraso</div>
                        <div>{{ $helpers.formatRS(row.cliente.saldovencido, 'R$', 2) }} <span v-if="row.cliente.saldovencidodesde">desde {{$helpers.dateToBR(row.cliente.saldovencidodesde)}}</span></div>
                      </q-item-label>

                                     <!-- <template v-slot:body-cell-saldovencido="props">
                    <q-td :props="props"  @click="actAddLicencaDialog(props.row)" class="cursor-pointer" :class="(props.row.cliente ? props.row.cliente.saldovencido : false) ? 'bg-red-1 text-red' : ''">
                      <div class="cursor-pointer text-weight-bold" v-if="props.row.cliente ? props.row.cliente.saldovencido : false"   >
                          {{ $helpers.formatRS(props.row.cliente.saldovencido, '', 2) }}
                          <q-tooltip :delay="700">
                            <div>Pendência financeira</div>
                            <div>Débito em atraso: {{$helpers.formatRS(props.row.cliente.saldovencido, 'R$ ', 2)}}</div>
                            <div v-if="props.row.cliente.saldovencidodesde">desde {{$helpers.dateToBR(props.row.cliente.saldovencidodesde)}}</div>
                          </q-tooltip>
                      </div>
                      <div v-else class="text-grey-4">0
                        <q-tooltip :delay="700">Nenhuma pendência financeira</q-tooltip>
                      </div>
                    </q-td>
                  </template> -->
                    </q-item-section>
                    <q-item-section side class="text-center">
                      <q-item-label class="full-width text-center ">
                        <q-avatar size="3em" font-size="0.5em" color="red" text-color="white" v-if="row.bloqueado || !row.datavencimento">
                          {{ row.databloqueio ? $helpers.diffDate(row.databloqueio, '', 'days')+1 : '?'}}
                        </q-avatar>
                        <q-avatar size="3em" font-size="0.5em" color="yellow-10" text-color="white" v-if="row.datavencimento && row.expirado && !row.bloqueado">
                          {{$helpers.diffDate('', row.databloqueio, 'days')-1}}
                        </q-avatar>
                        <q-avatar size="3em" font-size="0.5em" color="green" text-color="white" v-if="!row.expirado && !row.bloqueado && row.datavencimento">
                          {{$helpers.diffDate('', row.datavencimento, 'days')+1}}
                        </q-avatar>
                      </q-item-label>
                      <q-item-label style="min-width: 90px">{{row.datavencimento ? row.status : 'Sem licença'}}</q-item-label>
                    </q-item-section>
                  </q-item>
                  <q-separator  />
                  <q-item class="bg-grey-2 text-grey-7">
                    <q-item-section >
                      <q-item-label>
                        <div class="full-width text-center">Exibindo {{dataset.infopagination}} cliente(s)</div>
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                  <template v-slot:loading>
                    <div class="row justify-center q-my-md">
                      <div class="full-width text-center">
                        <q-spinner-dots color="primary" size="40px" />
                      </div>
                    </div>
                  </template>
                </q-infinite-scroll>
              </q-list>
            </div>
            <!-- celular -->
            <!-- pc -->
            <div style="max-width: 100vw" v-else>
              <q-table :data="rows" :columns="columns" :dense="!$q.platform.is.mobile"  flat
                :loading="loading" color="primary" id="sticky-table"
                :pagination.sync="dataset.pagination"
                row-key="id"
                 :rows-per-page-options="$qtable.rowsperpageoptions"
                @request="refreshData"
                >
                  <template v-slot:body-cell-cliente="props">
                    <q-td :props="props"  @click="actAddLicencaDialog(props.row)" class="cursor-pointer">
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
                      </q-tooltip>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-saldovencido="props">
                    <q-td :props="props"  @click="actAddLicencaDialog(props.row)" class="cursor-pointer" :class="(props.row.cliente ? props.row.cliente.saldovencido : false) ? 'bg-red-1 text-red' : ''">
                      <div class="cursor-pointer text-weight-bold" v-if="props.row.cliente ? props.row.cliente.saldovencido : false"   >
                          {{ $helpers.formatRS(props.row.cliente.saldovencido, '', 2) }}
                          <q-tooltip :delay="700">
                            <div>Pendência financeira</div>
                            <div>Débito em atraso: {{$helpers.formatRS(props.row.cliente.saldovencido, 'R$ ', 2)}}</div>
                            <div v-if="props.row.cliente.saldovencidodesde">desde {{$helpers.dateToBR(props.row.cliente.saldovencidodesde)}}</div>
                          </q-tooltip>
                      </div>
                      <div v-else class="text-grey-4">0
                        <q-tooltip :delay="700">Nenhuma pendência financeira</q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-dataativacao="props">
                    <q-td :props="props" >
                      <div v-if="props.row.dataativacao">
                        <div class="">{{$helpers.datetimeRelativeToday(props.row.dataativacao)}}</div>
                        <q-tooltip :delay="700">
                          <div>{{$helpers.datetimeToBR(props.row.dataativacao)}}</div>
                        </q-tooltip>
                      </div>
                      <div v-else>
                        -
                        <q-tooltip :delay="700">
                          {{props.row.databloqueio ? 'Licença ainda não foi ativada' : 'Nenhuma licença gerada'}}
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-datavencimento="props">
                    <q-td :props="props" :class="props.row.expirado ? 'bg-red-1 text-red text-weight-bold' : ''" >
                      <div v-if="props.row.datavencimento">
                        <div>{{$helpers.dateToBR(props.row.datavencimento)}}</div>
                      </div>
                      <div v-else >
                        Sem licença
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-databloqueio="props">
                    <q-td :props="props" :class="props.row.bloqueado ? 'bg-red-1 text-red text-weight-bold' : ''" >
                      <div v-if="props.row.databloqueio">
                        <div>{{$helpers.dateToBR(props.row.databloqueio)}}</div>
                      </div>
                      <div v-else >
                        Sem licença
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-diasrestantes="props">
                    <q-td :props="props" :class="props.row.bloqueado ? 'bg-red-1 text-red text-weight-bold' : ''" >
                      <div v-if="props.row.databloqueio">
                        <div>{{props.row.diasrestantes}} dia(s)</div>
                      </div>
                      <div v-else >
                        Sem licença
                      </div>
                    </q-td>
                  </template>
              </q-table>
            </div>
            <!-- pc -->
          </q-card-section>
        </q-card>
        <q-page-sticky position="top" expand>
          <q-toolbar class="bg-primary text-white">
            <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
            <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
            <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
            <q-toolbar-title>Licenças</q-toolbar-title>
            <q-btn  icon="search" :label="$q.platform.is.mobile ? '' : 'Consultar'" :round="$q.platform.is.mobile" flat @click="refreshData(null, false)" :loading="loading" />
          </q-toolbar>
        </q-page-sticky>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
import ClientesLicencas from 'src/mvc/collections/clienteslicencas.js'
export default {
  name: 'clientes.licencas.index',
  components: {
  },
  data () {
    var dataset = new ClientesLicencas()
    return {
      dataset,
      rows: [],
      filterstatus: 'expirado',
      filter: '',
      columns: [
        { name: 'cliente', align: 'left', label: 'Cliente', field: 'cliente', filterconfig: { value: null } },
        { name: 'saldovencido', align: 'right', label: 'Financeiro', field: 'saldovencido', filterconfig: { value: null } },
        { name: 'datavencimento', align: 'center', label: 'Expira em', field: 'datavencimento', filterconfig: { value: null } },
        { name: 'databloqueio', align: 'center', label: 'Bloqueia em', field: 'databloqueio' },
        { name: 'diasrestantes', align: 'center', label: 'Dias restantes', field: 'diasrestantes', filterconfig: { value: null } },
        { name: 'status', align: 'center', label: 'status', field: 'status' },
        { name: 'dataativacao', align: 'center', label: 'Aplicado (ativado)', field: 'dataativacao', filterconfig: { value: null } }
      ],
      loading: false,
      posting: false,
      error: null
    }
  },
  async mounted () {
    var app = this
    if (app.$route.query) await app.queryRead(app.$route.query)
    await app.refreshData(null, false)
  },
  methods: {
    async queryRead (pQuery) {
      var app = this
      app.loading = true
      try {
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
      } finally {
        app.loading = false
      }
    },
    async actAddLicencaDialog (row) {
      var app = this
      var ret = await app.dataset.showAddLicenca(app, row.cliente)
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
    async onLoadInfinite (index, done) {
      var app = this
      setTimeout(() => {
        try {
          if (app.loading) throw new Error('inload')
          if (!app.dataset.temmaisregistros) throw new Error('nao tem mais regsityros')
          app.actLoadMore()
          done()
        } catch (error) {
          // console.error(error.message)
          done()
        }
      }, 200)
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
    async refreshData (props, resetPage = true) {
      var app = this
      if (app.loading) return
      try {
        app.loading = true
        app.error = null

        app.error = null
        app.dataset.readPropsTable(props, resetPage)
        app.dataset.filter = app.filter
        app.dataset.status = app.filterstatus

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
          if (app.dataset.status !== null) query.status = app.dataset.status
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
