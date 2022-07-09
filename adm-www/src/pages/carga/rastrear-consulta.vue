<template>
<div>
  <q-page class="">
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-12" v-if="error">
        <q-banner class="bg-negative text-white">{{error}}</q-banner>
      </div>
      <div class="col-sm-12 full-width" v-if="!error">
        <!-- BUSCA -->
        <q-card class="bg-white" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile" flat>
          <q-card-section class="full-width text-center justify-center items-center q-ma-lg" v-if="consultando">
            <div style="max-width: 15rem; margin: 0 auto;" >
              <lottie-animation  :animationData="lottiessearchdoc" :speed="1" :loop="true" autoPlay />
            </div>
          </q-card-section>
          <q-card-section >
            <q-option-group v-model="consultatipo" type="radio"
              :options="[{ label: 'Por chave da NF-e', value: 'chave' }, { label: 'Número NF-e', value: 'numero' }, { label: 'Número da coleta', value: 'coleta' }]"
              inline :disable="consultando"/>
          </q-card-section>
          <q-card-section v-if="(consultatipo === 'chave')" class="q-py-xs" >
            <q-input outlined v-model="consultachave" @input="onInputChave" label="Chave da nota*" stack-label maxlength="44" counter
                  ref="txtchave" input-class="text-weight-bold"
                  :disable="consultando"
                  :error-message="consultanferet.msg"
                  :error="(consultanferet ? (!consultanferet.ok && (consultanferet.msg !== '')) : false) && (consultachave ? consultachave.length > 0 : false)"
                >
                  <template v-slot:append>
                    <q-icon name="check_circle" color="positive" size="md" v-if="(consultanferet ? consultanferet.ok : false) && (consultachave ? consultachave.length > 0 : false)" />
                  </template>
                </q-input>
          </q-card-section>
          <q-card-section v-if="(consultatipo === 'numero') || (consultatipo === 'coleta')" class="q-py-xs" >
            <q-input outlined v-model="consultanumero" :label="consultatipo === 'numero' ? 'Número da nota fiscal' : 'Número da coleta'" stack-label type="number" :disable="consultando"
              min="1" mask="9999999999" input-class="text-weight-bold"  />
          </q-card-section>
          <q-card-section>
              <q-btn label="Consultar" unelevated color="primary" @click="actConsultar" :disable="!allowconsulta" :loading="consultando"
                :class="$q.platform.is.mobile ? 'full-width q-py-md' : ''"/>
              <q-btn flat color="primary" icon="clear" v-if="!$q.platform.is.mobile" @click="actClear" :label="$q.platform.is.mobile ? '' : 'Limpar'" class="q-ml-md"/>
          </q-card-section>
        </q-card>
        <!-- BUSCA -->
        <!-- timeline -->
        <div v-if="(rows ? rows.length > 0 : false) && !loading" class="q-mt-md" :class="$q.platform.is.mobile ? 'q-px-sm' : ''">
          <div class="text-h6 q-ml-xs q-mb-sm">
            Notas encontradas
          </div>
          <q-card v-for="(nota, knota) in rows" :key="'nota' + knota" class="bg-white q-mb-md cursor-pointer" flat bordered clickable v-ripple
            @click="actRastrear(nota.notachave)">
            <q-card-section>
              <div class="row text-body" >
                <div class="col-7 text-weight-bold" style="font-size: 1rem">
                  NF-e: {{$helpers.formatMilhar(nota.notanumero)}} - Série: {{$helpers.padLeftZero(nota.notaserie, 3)}}
                </div>
                <div class="col-5 text-right text-body">
                  Coleta: {{ nota.idcoleta ? $helpers.formatMilhar(nota.idcoleta) : '?'}}
                </div>
                <div class="col-12 text-caption">
                  {{nota.notachave}}
                </div>
              </div>
            </q-card-section>
            <q-separator />
            <q-card-section class="q-py-xs">
              <div class="text-caption">Emitente</div>
              <div class="row">
                <div class="col-12 text-weight-bold">
                  {{nota.remetentenome}}
                </div>
                <div class="col-12">
                  CNPJ: {{$helpers.mascaraDocCPFCNPJ(nota.remetentecnpj)}}
                </div>
              </div>
            </q-card-section>
            <q-card-section>
              <div class="text-caption">Destinatário</div>
              <div class="row">
                <div class="col-12 text-weight-bold">
                  {{nota.destinatarionome}}
                </div>
                <div class="col-12">
                  CNPJ: {{$helpers.mascaraDocCPFCNPJ(nota.destinatariocnpj)}}
                </div>
              </div>
            </q-card-section>
            <q-card-actions >
              <q-btn unelevated label="Rastrear" icon="travel_explore" class="full-width" color="accent" />
            </q-card-actions>
          </q-card>

        </div>
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
import ColetasNotas from 'src/mvc/collections/coletasnotas.js'
import Usuario from 'src/mvc/models/usuario.js'
import NFe from 'src/mvc/models/nfe.js'
export default {
  name: 'usuario.meuperfil',
  components: {
  },
  data () {
    var lottiessearchdoc = require('src/assets/lotties/searchdoc.json')
    var usuario = new Usuario()
    var dataset = new ColetasNotas()
    var consultanfe = new NFe()
    return {
      lottiessearchdoc,
      usuario,
      cnpjstart: null,
      rows: null,
      consultatipo: 'chave',
      consultanumero: '',
      consultachave: '',
      consultanfe,
      error: null,
      consultanferet: { ok: false },
      dataset,
      loading: false,
      consultando: false
    }
  },
  activated () {
  },
  deactivated () {
  },
  async mounted () {
    await this.init()
    if (this.allowconsulta) this.actConsultar()
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
    },
    allowconsulta: function () {
      var app = this
      try {
        if (['numero', 'coleta'].indexOf(app.consultatipo) >= 0) {
          if (app.consultanumero ? app.consultanumero === '' : true) throw new Error('Número vazia')
          if (!(parseInt(app.consultanumero) > 0)) throw new Error('Número inválido')
        }
        if (app.consultatipo === 'chave') {
          if (app.consultachave ? app.consultachave === '' : true) throw new Error('Chave vazia')
          if (!app.consultanferet.ok) throw new Error(app.consultanferet.msg)
        }
        return true
      } catch (error) {
        return false
      }
    }
  },
  methods: {
    async actRastrear (pChave) {
      this.$router.push({ name: 'rastrear', params: { chave: pChave } })
    },
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
        app.consultaret = { ok: false }

        if (!app.usuariologado) throw new Error('Nenhum usuário logado')
        if (!app.usuariologado.permitecoletasver) throw new Error('Sem permissão de acesso')
        // consultatipo: 'chave',
        // consultanumero: '',
        // consultachave: '',
        if (app.$route.query.cnpj ? app.$route.query.cnpj !== '' : false) app.cnpjstart = app.$route.query.cnpj
        if (app.$route.query.chave ? app.$route.query.chave !== '' : false) {
          app.consultatipo = 'chave'
          app.consultachave = app.$route.query.chave
          app.onInputChave(app.consultachave)
        }
        // if (app.$route.query.modo) app.dadosconsulta.modo = app.$route.query.modo
        // if ((app.dadosconsulta.modo !== 'chave') && (app.dadosconsulta.modo !== 'dados')) app.dadosconsulta.modo = 'chave'
        // if (app.$route.query.cnpjemitente) app.dadosconsulta.cnpjemitente = app.$route.query.cnpjemitente
        // if (app.$route.query.cnpjdestinatario) app.dadosconsulta.cnpjdestinatario = app.$route.query.cnpjdestinatario

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
      }
    },
    async actClear () {
      var app = this
      app.rows = null
      app.dataset.limpardados()
      app.consultanumero = null
      app.consultachave = null
    },
    async onInputChave (val) {
      var app = this
      app.consultanfe.setChave(val)
      app.consultanferet = await app.consultanfe.isValid()
    },
    async actConsultar () {
      var app = this
      app.consultando = true
      var dialog = app.$helpers.dialogProgress(app, 'Consultando notas, aguarde!', 'Rastreamento de carga')
      try {
        if (['numero', 'coleta'].indexOf(app.consultatipo) >= 0) {
          if (app.consultanumero ? app.consultanumero === '' : true) throw new Error('Número vazia')
          if (!(parseInt(app.consultanumero) > 0)) throw new Error('Número inválido')
        }
        if (app.consultatipo === 'chave') {
          if (app.consultachave ? app.consultachave === '' : true) throw new Error('Chave vazia')
          if (!app.consultanferet.ok) throw new Error(app.consultanferet.msg)
        }

        app.dataset.readPropsTable()
        if (app.$q.platform.is.mobile) app.dataset.pagination.rowsPerPage = 50
        delete app.dataset.notanumero
        delete app.dataset.idcoleta
        delete app.dataset.chave

        if (app.consultatipo === 'chave') app.dataset.chave = app.consultachave
        if (app.consultatipo === 'numero') app.dataset.notanumero = app.consultanumero
        if (app.consultatipo === 'coleta') app.dataset.idcoleta = app.consultanumero

        app.dataset.orderby = null
        if (app.orderbylist) {
          var c = Object.keys(app.orderbylist).length
          if (c > 0) app.dataset.orderby = app.orderbylist
        }

        var ret = await app.dataset.fetch()
        if (ret.ok) {
          app.rows = app.dataset.itens
        } else {
          app.msgError = ret.msg
          app.consultaret = { ok: false, msg: ret.msg }
        }
      } catch (error) {
        console.error(error)
        app.$helpers.showDialog({ ok: false, msg: error.message }, true)
        app.consultaret = { ok: false, msg: error.message }
      } finally {
        dialog.hide()
        app.consultando = false
      }
    }
  }
}
</script>

<style lang="stylus">
</style>
