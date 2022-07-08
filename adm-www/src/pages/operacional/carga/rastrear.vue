<template>
<div>
  <q-page class="">
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-12" v-if="error">
        <q-card class="bg-red text-white q-ma-md" :bordered="!$q.platform.is.mobile"  flat>
          <q-card-section class="full-width text-center justify-center items-center q-ma-lg">
            <div>
              <q-icon name="error" color="white" size="4rem" />
            </div>
          </q-card-section >
          <q-card-section class="text-body text-center">
            <div >
              {{error}}
            </div>
          </q-card-section>
          <q-separator dark />
          <q-card-actions>
            <q-btn flat icon="arrow_back" @click="$router.go(-1)" label="Voltar" class="full-width"/>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-12 full-width" v-if="!error">
        <!-- BUSCA -->
        <q-card class="bg-white" v-if="!data.nota" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile" flat>
          <q-card-section class="full-width text-center justify-center items-center q-ma-lg" v-if="consultando">
            <div style="max-width: 15rem; margin: 0 auto;" >
              <lottie-animation  :animationData="lottiessearchdoc" :speed="1" :loop="true" autoPlay />
            </div>
          </q-card-section>
        </q-card>
        <!-- BUSCA -->
        <!-- timeline -->
        <q-card class="bg-white" flat :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile" v-if="(data.nota) && !loading">
          <q-card-section>
            <div class="row text-h6 q-col-gutter-sm">
              <div class="col-7">
                <q-field label="Número" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{$helpers.formatRS(data.nota.numero, '', 0)}}</div>
                  </template>
                </q-field>
              </div>
              <div class="col-5">
                <q-field label="Série" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{$helpers.padLeftZero(data.nota.serie, 3)}}</div>
                  </template>
                </q-field>
              </div>
              <div class="col-12">
                <q-field label="Chave de acesso" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-caption" tabindex="0">{{data.nota.chave}}</div>
                  </template>
                </q-field>
              </div>
              <div class="col-12">
                <q-field label="Remetente" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline " tabindex="0">
                      <div class="text-weight-bold">{{data.nota.remetentenome}}</div>
                      <div class="text-caption">{{$helpers.mascaraCpfCnpj(data.nota.remetentecnpj)}}</div>
                      <div>{{data.nota.remetentecidadeeuf}}</div>
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-12">
                <q-field label="Destinatário" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline " tabindex="0">
                      <div class="text-weight-bold">{{data.nota.destinatarionome}}</div>
                      <div class="text-caption">{{$helpers.mascaraCpfCnpj(data.nota.destinatariocnpj)}}</div>
                      <div>{{data.nota.destinatariocidadeeuf}}</div>
                    </div>
                  </template>
                </q-field>
              </div>
            </div>
          </q-card-section>
          <q-card-section v-if="data" class="q-pl-lg">
            <q-timeline color="white" layout="dense" cl>
              <q-timeline-entry v-for="(item, k) in data" :key="k" :title="item.title"
                :color="statusToConfig(item.status).color" :icon="statusToConfig(item.status).icon" v-show="k !== 'nota'" >
                <template v-slot:subtitle>
                  <div v-if="item.dh" class="text-black">
                    <div>{{$helpers.dateToBR(item.dh)}}</div>
                    <div class="text-caption">{{$helpers.datetimeRelativeToday(item.dh)}}</div>
                    <q-tooltip :delay="500">
                      {{$helpers.datetimeToBR(item.dh)}}
                    </q-tooltip>
                  </div>
                </template>
                <div>
                  <div v-if="item.eventos ? item.eventos.length > 0 : false">
                    <div v-for="(evento, kproc) in item.eventos" :key="'proc' + kproc">
                      <div class="row">
                        <div class="col-xs-12 col-md-8 text-subtitle2"><q-icon name="fiber_manual_record" size="10px" :color="item.color"   />{{evento.title}}</div>
                        <div class="q-pl-md col-xs-12 col-md-4 text-caption" :class="$q.platform.is.mobile ? '' : 'text-right'" v-if="evento.dh">
                          <div>{{$helpers.datetimeToBR(evento.dh)}}</div>
                          <q-tooltip :delay="500">
                            {{$helpers.datetimeRelativeToday(evento.dh)}}
                          </q-tooltip>
                        </div>
                        <div class="q-pl-md" v-if="evento.text ? evento.text !== '' : false" v-html="evento.text" >{{evento.text}}</div>
                      </div>
                      <q-separator spaced v-if="kproc < (item.eventos.length - 1)" />
                    </div>
                  </div>
                </div>
              </q-timeline-entry>
            </q-timeline>
          </q-card-section>
          <q-card-section>
              <div class="full-width text-right text-caption">
                Consulta realizada em {{$helpers.datetimeToBR(datadaconsulta)}}
              </div>
          </q-card-section>
        </q-card>
        <!-- timeline -->
      </div>
      <q-page-sticky position="top" expand>
        <q-toolbar class="bg-white text-primary shadow-up-21">
          <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
          <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
          <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
          <q-toolbar-title>Rastreamento de carga</q-toolbar-title>
          <q-btn flat rounded icon="clear" @click="actClear" :label="$q.platform.is.mobile ? '' : 'Limpar'"/>
        </q-toolbar>
      </q-page-sticky>
    </div>
  </q-page>
</div>
</template>

<script>
import ColetaNota from 'src/mvc/models/coletanota.js'
import Usuario from 'src/mvc/models/usuario.js'
import NFe from 'src/mvc/models/nfe.js'
import moment from 'moment'
export default {
  name: 'usuario.meuperfil',
  components: {
  },
  data () {
    var lottiessearchdoc = require('src/assets/lotties/searchdoc.json')
    var usuario = new Usuario()
    var dataset = new ColetaNota()
    var consultanfe = new NFe()
    return {
      lottiessearchdoc,
      usuario,
      datadaconsulta: null,
      data: {
        nota: null,
        coletagem: {
          eventos: null,
          title: 'Coletagem',
          tooltip: 'Carga  em trânsito para o centro de distribuição',
          status: '0' // 0-Sem info, 1-concluido, 2-em progresso
        },
        preparo: {
          eventos: null,
          title: 'Preparo / Logística',
          tooltip: 'Carga  em trânsito para o centro de distribuição',
          status: '0' // 0-Sem info, 1-concluido, 2-em progresso
        },
        ementrega: {
          title: 'Em entrega',
          tooltip: 'Carga em rota de entrega',
          eventos: null,
          status: '0' // 0-Sem info, 1-concluido, 2-em progresso
        },
        finalizado: {
          id: 'entregaencerrada',
          title: 'Encerrado',
          eventos: null,
          tooltip: 'Carga  em trânsito para o centro de distribuição',
          status: '0' // 0-Sem info, 1-concluido, 2-em progresso
        }
      },
      consultachave: '',
      consultanfe,
      error: null,
      dataset,
      loading: false,
      consultando: false
    }
  },
  async activated () {
    await this.init()
    this.actConsultar()
  },
  deactivated () {
    // called when removed from the DOM into the cache
    // and also when unmounted
  },
  async mounted () {
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.user) {
          u = app.$store.state.usuario.user
        }
      }
      return u
    }
  },
  methods: {
    statusToConfig (value) {
      var config = {
        icon: 'radio_button_unchecked',
        color: 'grey-5',
        textcolor: 'white'
      }
      switch (value) {
        case '1':
          config.icon = 'task_alt'
          config.color = 'positive'
          break
        case '2':
          config.icon = 'swipe_right_alt'
          config.color = 'orange'
          break
        case '3':
          config.icon = 'error'
          config.color = 'red'
          break
      }
      return config
    },
    async init () {
      var app = this
      try {
        app.loading = true
        app.error = null
        if (!app.usuariologado) throw new Error('Nenhum usuário logado')
        if (!app.usuariologado.permitecoletasver) throw new Error('Sem permissão de acesso')
        app.consultachave = null
        if (app.$route.params.chave ? app.$route.params.chave !== '' : false) app.consultachave = app.$route.params.chave
        app.usuario.limpardados()
        app.dataset.limpardados()
        await app.refreshData()
      } catch (error) {
        app.loading = false
        app.error = error.message
      }
      app.loading = false
    },
    async refreshData () {
      var app = this
      var ret = await app.usuario.meuUsuario()
      if (!ret.ok) {
        app.loading = false
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      } else {
        if ((!app.empresaselecionada) && (app.usuario.clientes ? app.usuario.clientes.length > 0 : false)) {
          if (app.cnpjstart ? app.cnpjstart !== '' : false) {
            for (let index = 0; index < app.usuario.clientes.length; index++) {
              const cli = app.usuario.clientes[index]
              if (cli.cnpj === app.cnpjstart) {
                app.empresaselecionada = cli
                break
              }
            }
          }
        }
        if ((!app.empresaselecionada) && (app.usuario.clientes ? app.usuario.clientes.length === 1 : false)) {
          app.empresaselecionada = app.usuario.clientes[0]
        }
      }
    },
    async actClear () {
      var app = this
      app.data.nota = null
      app.consultanumero = null
      app.consultachave = null
    },
    async actConsultar () {
      var app = this
      app.data.nota = null
      app.consultando = true
      try {
        if (app.consultachave ? app.consultachave === '' : true) throw new Error('Chave vazia')
        app.consultanfe.setChave(app.consultachave)
        var ret = await app.consultanfe.isValid()
        if (!ret.ok) throw new Error(ret.msg)

        ret = await app.dataset.findrastreio(app.consultachave)
        if (ret.ok) {
          if (ret.data.coletagem) {
            app.data.coletagem.dh = ret.data.coletagem.dh
            app.data.coletagem.status = ret.data.coletagem.status
            app.data.coletagem.eventos = ret.data.coletagem.eventos
          }
          if (ret.data.preparo) {
            app.data.preparo.dh = ret.data.preparo.dh
            app.data.preparo.status = ret.data.preparo.status
            app.data.preparo.eventos = ret.data.preparo.eventos
          }
          if (ret.data.ementrega) {
            app.data.ementrega.dh = ret.data.ementrega.dh
            app.data.ementrega.status = ret.data.ementrega.status
            app.data.ementrega.eventos = ret.data.ementrega.eventos
          }
          if (ret.data.finalizado) {
            app.data.finalizado.dh = ret.data.finalizado.dh
            app.data.finalizado.status = ret.data.finalizado.status
            app.data.finalizado.eventos = ret.data.finalizado.eventos
          }
          app.datadaconsulta = moment()
          if (ret.data.nota) app.data.nota = ret.data.nota
        } else {
          app.error = ret.msg
        }
      } catch (error) {
        app.error = error.message
      } finally {
        app.consultando = false
      }
    }
  }
}
</script>

<style lang="stylus">
</style>
