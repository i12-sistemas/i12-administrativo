<template>
<div>
  <q-page class="">
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header" >
      <div class="col-sm-12" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'" >
        <div class="col-12" >
          <div class="col-12" v-if="error">
            <q-banner class="bg-negative text-white">{{error}}</q-banner>
          </div>
          <div class="row full-width q-px-md q-gutter-y-md" :class="$q.platform.is.mobile ? '' : 'q-col-gutter-x-md' ">
            <div class="col-12">
              <q-card class="bg-grey-2 text-grey-10" bordered flat>
                <q-card-section >
                  <div class="row q-gutter-y-lg">
                    <div class="col-xs-6 col-md-3 text-center">
                      <div><q-icon name="calculate" size="3em" /></div>
                      <div class="text-h4 text-weight-bold">{{$helpers.bytesToHumanFileSizeString(rows.total.size)}}</div>
                      <div class="text-h6">armazenados</div>
                    </div>
                    <div class="col-xs-6 col-md-3 text-center">
                      <div><q-icon name="text_snippet" size="3em" /></div>
                      <div class="text-h4 text-weight-bold">{{$helpers.formatRS(rows.total.qtde,'',0)}}</div>
                      <div class="text-h6">arquivos</div>
                    </div>
                    <div class="col-xs-6 col-md-3 text-center">
                      <div><q-icon name="people_alt" size="3em" /></div>
                      <div class="text-h4 text-weight-bold">{{$helpers.formatRS(rows.total.qtdeclientes,'',0)}}</div>
                      <div class="text-h6">clientes</div>
                    </div>
                    <div class="col-xs-6 col-md-3 text-center">
                      <div><q-icon name="functions" size="3em" /></div>
                      <div class="text-h4 text-weight-bold">{{$helpers.bytesToHumanFileSizeString(rows.total.mediacliente)}}</div>
                      <div class="text-h6">por cliente</div>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
            <div class="col-xs-12 col-md-4">
              <q-card class="bg-blue text-white full-height" flat>
                <q-card-section>
                  <div class="text-h6">Nível pago</div>
                  <div class="text-subtitle2">
                    <q-linear-progress :value="rows.nivelpago.mediageral/100" rounded color="white" track-color="blue-5"  size="20px" class="q-mt-sm" >
                      <div class="absolute-full flex flex-center">
                      <q-badge color="blue-5" text-color="white" :label="rows.nivelpago.mediageral" />
                      </div>
                    </q-linear-progress>
                  </div>
                  <div class="posicaoprogresso">
                    {{$helpers.bytesToHumanFileSizeString(rows.nivelpago.total)}}
                  </div>
                </q-card-section>
                <q-card-section class="q-pt-none">
                  <div class="row">
                    <div class="col-xs-6 text-center">
                      <div><q-icon name="text_snippet" size="2em" /></div>
                      <div class="text-h6">{{$helpers.formatRS(rows.nivelpago.qtdearquivos,'',0)}}</div>
                      <div class="text-caption">arquivos</div>
                    </div>
                    <div class="col-xs-6 text-center">
                      <div><q-icon name="people_alt" size="2em" /></div>
                      <div class="text-h6">{{$helpers.formatRS(rows.nivelpago.qtdeclientes,'',0)}}</div>
                      <div class="text-caption">clientes</div>
                    </div>
                  </div>
                  <q-separator spaced dark  />
                  <div class="row">
                    <div class="col-12 text-center">{{$helpers.bytesToHumanFileSizeString(rows.nivelpago.mediaarquivoscliente)}} por cliente</div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
            <div class="col-xs-12 col-md-4">
              <q-card class="bg-light-blue-10 text-white full-height" flat>
                <q-card-section>
                  <div class="text-h6">Gratuito</div>
                  <div class="text-subtitle2">
                    <q-linear-progress :value="rows.nivelgratuito.mediageral/100" rounded color="white" track-color="light-blue-9"  size="20px" class="q-mt-sm" >
                      <div class="absolute-full flex flex-center">
                      <q-badge color="light-blue-9" text-color="white" :label="rows.nivelgratuito.mediageral" />
                      </div>
                    </q-linear-progress>
                  </div>
                  <div class="posicaoprogresso">
                    {{$helpers.bytesToHumanFileSizeString(rows.nivelgratuito.total)}}
                  </div>
                </q-card-section>
                <q-card-section class="q-pt-none">
                  <div class="row">
                    <div class="col-xs-6 text-center">
                      <div><q-icon name="text_snippet" size="2em" /></div>
                      <div class="text-h6">{{$helpers.formatRS(rows.nivelgratuito.qtdearquivos,'',0)}}</div>
                      <div class="text-caption">arquivos</div>
                    </div>
                    <div class="col-xs-6 text-center">
                      <div><q-icon name="people_alt" size="2em" /></div>
                      <div class="text-h6">{{$helpers.formatRS(rows.nivelgratuito.qtdeclientes,'',0)}}</div>
                      <div class="text-caption">clientes</div>
                    </div>
                  </div>
                  <q-separator spaced dark  />
                  <div class="row">
                    <div class="col-12 text-center">{{$helpers.bytesToHumanFileSizeString(rows.nivelgratuito.mediaarquivoscliente)}} por cliente</div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
            <div class="col-xs-12 col-md-4 ">
              <q-card class="bg-red-8 text-white full-height" flat>
                <q-card-section>
                  <div class="text-h6">Inativos</div>
                  <div class="text-subtitle2">
                    <q-linear-progress :value="rows.inativos.mediageral/100" rounded color="white" track-color="red-5"  size="20px" class="q-mt-sm" >
                      <div class="absolute-full flex flex-center">
                      <q-badge color="red-5" text-color="white" :label="rows.inativos.mediageral" />
                      </div>
                    </q-linear-progress>
                  </div>
                  <div class="posicaoprogresso">
                    {{$helpers.bytesToHumanFileSizeString(rows.inativos.total)}}
                  </div>
                </q-card-section>
                <q-card-section class="q-pt-none">
                  <div class="row">
                    <div class="col-xs-6 text-center">
                      <div><q-icon name="text_snippet" size="2em" /></div>
                      <div class="text-h6">{{$helpers.formatRS(rows.inativos.qtdearquivos,'',0)}}</div>
                      <div class="text-caption">arquivos</div>
                    </div>
                    <div class="col-xs-6 text-center">
                      <div><q-icon name="people_alt" size="2em" /></div>
                      <div class="text-h6">{{$helpers.formatRS(rows.inativos.qtdeclientes,'',0)}}</div>
                      <div class="text-caption">clientes</div>
                    </div>
                  </div>
                  <q-separator spaced dark  />
                  <div class="row">
                    <div class="col-12 text-center">{{$helpers.datetimeRelativeToday(rows.inativos.minlastmodified)}} <q-icon name="sync_alt" class="q-mx-xs" size="sm" /> {{$helpers.datetimeRelativeToday(rows.inativos.maxlastmodified)}}</div>
                    <q-tooltip :delay="700">
                      <div>Backup mais antigo: {{$helpers.datetimeToBR(rows.inativos.minlastmodified)}}</div>
                      <div>Backup mais atual: {{$helpers.datetimeToBR(rows.inativos.maxlastmodified)}}</div>
                    </q-tooltip>
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </div>
          <q-card class="bg-white q-mt-md" flat  v-if="rows" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
            <q-card-section class="q-pa-none">
              <div style="max-width: 100vw">
              </div>
            </q-card-section>
          </q-card>
          <q-page-sticky position="top" expand>
            <q-toolbar class="bg-primary text-white">
              <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
              <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
              <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
              <q-toolbar-title>Backups</q-toolbar-title>
              <q-btn  icon="sync" :label="$q.platform.is.mobile ? '' : 'Atualizar'" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
              <q-btn  icon="search" :label="$q.platform.is.mobile ? '' : 'Consultar'" :round="$q.platform.is.mobile" flat :to="{ name: 'backup.listagem'}" />
            </q-toolbar>
          </q-page-sticky>
        </div>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
import Backups from 'src/mvc/collections/backups.js'
export default {
  name: 'tabelaibpt.index',
  components: {
  },
  data () {
    var dataset = new Backups()
    return {
      dataset,
      rows: [],
      loading: false,
      posting: false,
      error: null,
      customcnpj: null,
      customnumero: null,
      customchave: null
    }
  },
  created: function () {
    this.$eventbus.$on('coletasconsultaupdated', this.coletasconsultaupdated)
    // this.$eventbus.$emit('setdrawerr', 'drcoletasdashboad', false)
  },
  beforeDestroy: function () {
    this.$eventbus.$off('coletasconsultaupdated', this.coletasconsultaupdated)
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
  async mounted () {
    var app = this
    await app.refreshData(null)
  },

  methods: {
    async actAdd () {
      var app = this
      var ret = app.dataset.showAdd(app)
      if (ret.ok) {
        app.refreshData(null)
      }
    },
    async actEditDialog (rowIndex) {
      var app = this
      var row = app.rows[rowIndex]
      app.$q.bottomSheet({
        message: 'Tabela: ' + row.uf,
        grid: false,
        actions: [
          {
            label: 'Excluir',
            icon: 'delete_forever',
            color: 'red',
            id: 'delete'
          },
          {
            label: 'Download',
            icon: 'download_for_offline',
            id: 'download'
          }
        ]
      }).onOk(async action => {
        if (action.id === 'delete') {
          var retDelete = await row.deleteWithQuestion(app)
          if (retDelete.ok) {
            app.refreshData(null)
          } else {
            app.$helpers.showDialog(retDelete)
          }
        }
        if (action.id === 'download') {
          row.download(app)
        }
      })
    },
    async refreshData () {
      var app = this
      try {
        app.loading = true
        app.error = null
        var ret = await app.dataset.fetchDashbord()
        if (ret.ok) {
          app.rows = ret.data
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
<style scoped>
.posicaoprogresso {
  position: absolute;
  top: 0px;
  right: 0px;
  font-size: 1.8em;
  padding: 13px 10px 0 0;
  font-weight: 900;
}
</style>
