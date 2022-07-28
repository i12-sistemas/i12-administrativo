<template>
<div>
  <q-page class="" >
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header full-width" >
      <div class="col-xs-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 " :class="$q.screen.lt.sm ? '' : 'q-pa-lg'" >
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section v-if="error">
            <q-banner class="bg-negative text-white">{{error}}</q-banner>
          </q-card-section>
          <q-card-section>
            <div class="row q-col-gutter-sm">
              <div class="col-xs-12 col-md-8">
                <q-input outlined debounce="700" @input="refreshData(null, true)" v-model="filter" placeholder="Informe qualquer Informação para pesquisar" label="Procurar" clearable stack-label
                  :dense="!$q.platform.is.mobile">
                  <template v-slot:append>
                    <q-btn flat color="primary" icon="search"  @click="refreshData(null, true)" :loading="loading" round dense/>
                  </template>
                </q-input>
              </div>
              <div class="col-xs-12 col-md-4">
                <q-select v-model="filtersituacao" label="Situação" outlined emit-value stack-label map-options class="q-mr-xs" :dense="!$q.platform.is.mobile"
                  @input="refreshData(null, true)"
                  :options="optionssituacao" >
                  <template v-slot:selected-item="scope">
                    <q-icon name="fiber_manual_record" size="12px" class="q-mr-sm" :color="scope.opt.color" v-if="scope.opt.color" />
                    {{scope.opt.label}}
                  </template>
                  <template v-slot:option="scope">
                    <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
                      <q-item-section avatar>
                        <q-icon name="fiber_manual_record" size="12px" class="q-mr-sm" :color="scope.opt.color" v-if="scope.opt.color" />
                      </q-item-section>
                      <q-item-section >
                        <q-item-label >{{scope.opt.label}}</q-item-label>
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>
              </div>
            </div>
          </q-card-section>
        </q-card>
        <q-card class="bg-white q-mt-md" flat  v-if="rows ? rows.length > 0 : false" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-separator v-if="$q.platform.is.mobile"  />
          <q-card-section class="q-pa-none">
            <!-- celular -->
            <div style="max-width: 100vw" >
              <q-list >
                <q-infinite-scroll @load="onLoadInfinite" :offset="250" :disable="!dataset.temmaisregistros">
                  <q-item v-for="(row, index) in rows" :key="index" clickable v-ripple :class="$helpers.isOdd(index) ? '' : 'bg-grey-2'"
                      @click="actShow(row)">
                    <q-item-section>
                      <q-item-label v-if="row.dtabertura" caption>Aberto em {{$helpers.datetimeToBR(row.dtabertura, true, true)}}</q-item-label>
                      <q-item-label v-if="row.encerrado" class="text-green"><q-icon name="check_circle" color="green" size="18px" /> Encerrado em {{$helpers.datetimeToBR(row.dtfechamento, true, true)}}</q-item-label>
                      <q-item-label lines="3">{{row.problemareclamado}}</q-item-label>
                      <q-item-label lines="1" v-if="row.categoria" caption>
                        <span v-if="row.categoria">{{row.categoria.categoria}}</span>
                        <q-icon name="arrow_right" />
                        <span v-if="row.classificacao">{{row.classificacao.classificacao}}</span>
                      </q-item-label>
                      <q-item-label lines="1" caption v-if="row.fase"   >
                        {{row.fase.descricaocliente}}
                      </q-item-label>
                      <q-item-label lines="1" v-if="row.prazoprevisao && !row.encerrado" >
                        <div class="text-body rounded-borders q-px-sm q-py-xs bg-accent text-white">
                          Previsão <q-icon name="arrow_right" size="20px" /> {{$helpers.datetimeToBR(row.prazoprevisao, true, true)}}
                        </div>
                      </q-item-label>
                    </q-item-section>
                     <q-item-section side top>
                      <q-item-label class="text-weight-bold" lines="1">
                        # {{$helpers.formatRS(row.id,'',0)}}
                        </q-item-label>
                        <q-item-label lines="1"  v-if="row.ultimainteracao ? row.ultimainteracao.datahora : false">
                          {{$helpers.datetimeRelativeToday(row.ultimainteracao.datahora)}}
                        </q-item-label>
                        <q-item-label lines="1" v-if="row.ultimainteracao ? row.ultimainteracao.contatoacao : false">
                          {{$helpers.firstName(row.ultimainteracao.contatoacao.nome)}}
                          <q-icon name="support_agent" size="18px" v-if="row.ultimainteracao.origem === 'O'" />
                        </q-item-label>
                        <q-item-label v-if="!row.lido">
                          <q-badge color="deep-orange" text-color="white" label="não lido"  />
                        </q-item-label>
                        <q-item-label v-if="row.pendentecliente">
                          <q-badge color="purple-3" text-color="black" v-if="row.pendentecliente" class="q-py-xs" >
                            <div class="text-center">Aguardando<br>resposta</div>
                          </q-badge>

                        </q-item-label>
                      </q-item-section>
                  </q-item>
                  <q-separator  />
                  <q-item class="bg-grey-2 text-grey-7">
                    <q-item-section >
                      <q-item-label>
                        <div class="full-width text-center">Exibindo {{dataset.infopagination}} atendimentos(s)</div>
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
          </q-card-section>
        </q-card>
        <q-page-sticky position="top" expand>
          <q-toolbar class="bg-primary text-white">
            <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
            <q-toolbar-title>Meus atendimentos</q-toolbar-title>
            <q-btn flat icon="add" :label="$q.platform.is.mobile ?  '' : 'Novo atendimento'" :to="{ name: 'atendimentos.novo' }"/>
            <q-btn flat round icon="sync" @click="refreshData(null, true)"/>
          </q-toolbar>
        </q-page-sticky>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
import Atendimentos from 'src/mvc/collections/atendimentos.js'
export default {
  name: 'atendimentos.listagem',
  components: {
  },
  data () {
    var dataset = new Atendimentos()
    return {
      dataset,
      rows: [],
      filtersituacao: 'emabertos',
      filter: '',
      optionssituacao: [
        { value: 'emabertos', label: 'Em aberto', color: 'light-blue-3' },
        { value: 'encerrada', label: 'Encerrados', color: 'green' },
        { value: 'naolidos', label: 'Não lidos', color: 'deep-orange-4' },
        { value: 'pendenteresposta', label: 'Aguardando resposta', color: 'purple-3' },
        { value: 'todos', label: 'Todos', color: 'grey-7' }
      ],
      columns: [
        { name: 'id', align: 'left', style: 'max-width: 100px', label: 'Número', field: 'id', filterconfig: { value: null } },
        { name: 'dtabertura', align: 'center', style: 'max-width: 120px', label: 'Abertura', field: 'dtabertura' },
        { name: 'problemareclamado', align: 'left', style: 'max-width: 250px', label: 'Descrição inicial', field: 'problemareclamado', filterconfig: { value: null } },
        { name: 'classificacao', align: 'left', label: 'Categoria/Classificação', field: 'classificacao', style: 'max-width: 90px' },
        { name: 'fase', align: 'left', label: 'Fase', field: 'fase', style: 'max-width: 90px' },
        { name: 'ultimainteracao', align: 'right', style: 'max-width: 80px', label: 'Última interação', field: 'ultimainteracao', filterconfig: { value: null } }
      ],
      loading: false,
      posting: false,
      error: null
    }
  },
  async activated () {
    var app = this
    if (app.$route.query) await app.queryRead(app.$route.query)
    app.refreshData(null, true)
  },
  async mounted () {
    var app = this
    if (app.$route.query) await app.queryRead(app.$route.query)
    await app.refreshData(null, true)
  },
  methods: {
    async queryRead (pQuery) {
      var app = this
      app.loading = true
      try {
        if (pQuery) {
          if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)
          if (pQuery.situacao) {
            app.filtersituacao = pQuery.situacao
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
    async actShow (row) {
      var app = this
      app.$nextTick(() => {
        app.$router.push({ name: 'atendimentos.atendimento', params: { id: row.id } })
      })
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
        app.dataset.situacao = app.filtersituacao

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
          if (app.dataset.situacao !== null) query.situacao = app.dataset.situacao
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
