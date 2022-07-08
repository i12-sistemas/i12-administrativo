<template>
  <q-layout view="hHh lpR lff" class="bg-grey-1" >
    <q-header class="bg-primary q-mb-md gradientToolbar">
      <div class="full-width row justify-center " :class="$q.platform.is.mobile ? '' : 'full-height items-center'">
        <div class="column col-xs-12 col-sm-11 col-lg-8 col-xl-8 bg-white">
          <div class="row full-width">
            <q-toolbar :class="$q.platform.is.mobile ? 'bg-primary text-white' : 'text-black'">
              <q-toolbar-title shrink class="row items-center no-wrap cursor-pointer" >
                <img src="~assets/Logo-i12-horizontal150x50.png" style="max-height: 40px;"  v-if="$q.platform.is.mobile">
                <img src="~assets/Logo-i12-horizontal150x50.png" style="max-height: 30px;"  v-if="!$q.platform.is.mobile">
              </q-toolbar-title>
              <q-separator spaced inset vertical v-if="!$q.platform.is.mobile" />
              <q-toolbar-title v-if="!$q.platform.is.mobile">
                Coletas sem arquivo XML (NF-e)
              </q-toolbar-title>
              <q-space />
            </q-toolbar>
            <q-toolbar class="bg-deep-orange-8 text-white" v-if="$q.platform.is.mobile">
              <q-toolbar-title shrink class="text-center text-subtitle2 full-width">
                Coletas sem arquivo XML (NF-e)
              </q-toolbar-title>
              <q-space />
            </q-toolbar>
          </div>
        </div>
      </div>
    </q-header>
    <q-page-container class="bg-primary">
      <q-page class="">
      <div class="full-width row justify-center " :class="$q.platform.is.mobile ? '' : 'full-height items-center'">
        <div class="column col-xs-12 col-sm-11 col-lg-8 col-xl-8 bg-white" style="border-radius: 0 0 12px 12px;">
          <div class="row full-width ">
            <q-card class="full-width q-pa-none" flat bordered >
              <q-card-section class="q-pa-none" v-if="!loading && !error && xmls.consultado">
                <q-toolbar class="bg-grey-3 text-grey-9" >
                  <q-toolbar-title class="text-body2">
                    <div class="text-weight-bold">{{xmls.cliente.razaosocial}}</div>
                    <div>CNPJ: {{ $helpers.mascaraDocCPFCNPJ(xmls.cliente.cnpj) }}</div>
                  </q-toolbar-title>
                  <q-space  />
                  <q-toolbar-title class="text-body2 text-right">
                    <div class="text-weight-bold">{{xmls.notas ? xmls.notas.length-countSucesso + ' nota(s)' : ''}}</div>
                  </q-toolbar-title>
                </q-toolbar>
              </q-card-section>
              <q-card-section class="q-pa-xl text-center" v-if="loading">
                <q-spinner color="primary" size="3rem" :thickness="5" />
              </q-card-section>
              <q-card-section class="q-pa-lg" v-if="!loading && error">
                <div class="bg-red-1 text-red-10 rounded-borders text-center q-pa-md col-">
                  <div><q-icon name="error" size="60px" class="q-ma-md" /></div>
                  <div class="text-h4">Ops!</div>
                  <div class="text-h6">{{error}}</div>
                </div>
              </q-card-section>
              <q-card-section class="q-pa-lg " v-if="!loading && !error && (xmls.notas ? xmls.notas.length === 0 : true) && xmls.consultado">
                <div class="bg-green-1 text-green rounded-borders text-center q-pa-md col-">
                  <div><q-icon name="check_circle" size="60px" class="q-ma-md" /></div>
                  <div class="text-h4">Tudo certo!</div>
                  <div class="text-h6">Nenhuma nota pendente!!</div>
                </div>
              </q-card-section>
              <q-card-section class="q-pa-none" v-if="!loading && !error && (xmls.notas ? xmls.notas.length > 0 : false)">
                <q-list separator>
                  <q-item v-for="nota in xmls.notas" :key="nota.notachave" class="q-my-sm" >
                    <input :ref="'file_' + nota.notachave" type="file" accept="text/xml" @change="loadFile($event, nota)"  hidden/>
                    <q-item-section>
                      <q-item-label class="text-h6">NF-e: {{ nota.notanumero }} - Série: {{ $helpers.padLeftZero(nota.notaserie, 3) }}</q-item-label>
                      <q-item-label lines="1">Chave: {{ $helpers.mascaraChaveNFe(nota.notachave) }}</q-item-label>
                      <q-item-label caption>Postado {{ $helpers.datetimeRelativeToday(nota.dhultimo) }}</q-item-label>
                      <q-item-label lines="1" class="text-blue-10 text-weight-bold" v-if="nota.file">
                        <div class="q-pa-md q-mt-md rounded-borders bg-grey-3">
                          <div>Arquivo: {{ nota.file.name}}</div>
                          <div>Tamanho: {{ $helpers.bytesToHumanFileSizeString(nota.file.size) }}</div>
                        </div>
                      </q-item-label>
                      <q-item-label lines="1" class="text-blue-10 text-weight-bold" v-if="nota.filesending || nota.fileret">
                        <div v-if="nota.filesending" class="a-mt-sm">
                          <div><q-linear-progress indeterminate rounded color="blue-10" track-color="white" class="q-mt-sm" size="12px" /></div>
                          <div class="q-pa-sm text-center full-width">Enviando arquivo...</div>
                        </div>
                        <div v-if="nota.fileret" class="q-mt-sm">
                          <q-banner class="bg-positive text-white rounded-borders" v-if="nota.fileret.ok">
                            <q-icon name="check_circle" size="20px" class="q-mr-sm" /> Arquivo enviado com sucesso!
                          </q-banner>
                          <q-banner class="bg-negative text-white rounded-borders" v-if="!nota.fileret.ok">
                            <div><q-icon name="error" size="20px" class="q-mr-sm" /> Falha ao enviar arquivo!</div>
                            <q-separator spaced />
                            <div class="text-caption">{{nota.fileret.msg}}</div>
                            <template v-slot:action>
                              <q-btn flat color="white" label="Tentar novamente" @click="actInputFile(nota)" />
                              <q-btn flat color="white" label="Reiniciar" @click="actRestart(nota)" />
                            </template>
                            <q-tooltip :delay="500">{{nota.fileret.msg}}</q-tooltip>
                          </q-banner>
                        </div>
                      </q-item-label>
                      <q-item-label lines="1" class="q-mt-sm" v-if="!nota.file && !nota.fileret">
                        <q-btn-group class="full-width" outline >
                          <q-btn outline color="grey-9" icon="file_upload" label="Enviar arquivo" no-caps class="full-width" @click="actInputFile(nota)" />
                          <q-separator vertical />
                          <q-btn outline icon="file_copy" @click="$helpers.copytoclipboard(nota.notachave)" />
                        </q-btn-group>
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-card-section>
              <q-card-section class="text-center q-py-md" v-if="!xmls.consultado && !loading">
                <div class="full-width row justify-center items-center">
                  <div v-if="recaptchaloading">carregando...</div>
                  <vue-recaptcha :sitekey="$configini.RECAPTCHA_KEY" @verify="verify" :loadRecaptchaScript="true" @render="rendered" ></vue-recaptcha>
                </div>
              </q-card-section>
            </q-card>

          </div>
        </div>
      </div>

      </q-page>
    </q-page-container>
    <q-footer  >
      <div class="bg-grey-2 text-grey-8 text-caption q-pa-xs text-center">
        <div><span class="text-weight-bold">{{appPackage.productName}} :: {{appPackage.description}}</span> | Versão {{appPackage.version}} release {{$helpers.datetimeToBR(appPackage.releasedatetime)}}</div>
        <div>© Copyright {{year ? year : ''}} :: <a href="http://www.i12.com.br" target="_blank">i12.com.br</a></div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script>
import axios from 'axios'
import VueRecaptcha from 'vue-recaptcha'
import moment from 'moment'
import datapackage from '../../../../package.json'
import XMLPendentes from 'src/mvc/collections/xmlpendentes.js'
export default {
  name: 'publico.coleta.xmlpendente',
  components: { VueRecaptcha },
  data () {
    var xmls = new XMLPendentes()
    return {
      title: 'Conecta Transportes - Envio de arquivo XML',
      xmls,
      token: null,
      recaptchaloading: true,
      recaptchatoken: null,
      loading: false,
      error: null,
      appPackage: datapackage,
      year: 2021,
      testeerro: false

    }
  },
  meta () {
    return {
      // this accesses the "title" property in your Vue "data";
      // whenever "title" prop changes, your meta will automatically update
      title: this.title
    }
    // title: this.title,
    // meta: {
    //   description: { name: 'description', content: 'Page 1' },
    //   keywords: { name: 'keywords', content: 'Quasar website' },
    //   equiv: { 'http-equiv': 'Content-Type', content: 'text/html; charset=UTF-8' },
    //   // note: for Open Graph type metadata you will need to use SSR, to ensure page is rendered by the server
    //   ogTitle: {
    //     name: 'og:title',
    //     // optional; similar to titleTemplate, but allows templating with other meta properties
    //     template (ogTitle) {
    //       return `${ogTitle} - My Website`
    //     }
    //   }
    // }
  },
  mounted () {
    var app = this
    app.year = moment().year()
    if (app.$route.query.token) app.token = app.$route.query.token
    // app.init()
    app.recaptchaLoad()
  },
  computed: {
    countSucesso: function () {
      var app = this
      var n = 0
      try {
        if (app.loading) throw new Error('Em loading')
        if (app.xmls.notas ? app.xmls.notas.length === 0 : false) throw new Error('Nenhum nota')
        for (let index = 0; index < app.xmls.notas.length; index++) {
          const nota = app.xmls.notas[index]
          if (nota.fileret ? nota.fileret.ok : false) n = n + 1
        }
      } catch (error) {
        n = 0
      }
      return n
    }
  },
  methods: {
    rendered (id) {
      this.recaptchaloading = false
    },
    async verify (response) {
      this.recaptchatoken = response
      if (this.recaptchatoken) this.init()
    },
    async recaptchaLoad () {
      // this.recaptcha = await load('6LeVEPgbAAAAAL4O461AuV7piPA85dfWO-EZv-Z5', {
      //   useRecaptchaNet: false,
      //   renderParameters: {
      //     container: 'containerrecaptcha'
      //   }
      // })
      // this.recaptcha.showBadge()
      // this.recaptchatoken = await this.recaptcha.execute('sendxml')
      // this.init()
    },
    actRestart (nota) {
      var app = this
      app.$nextTick(() => {
        app.loading = true
        delete nota.fileret
        delete nota.file
        nota.filesending = false
        app.loading = false
      })
    },
    actInputFile (nota) {
      var app = this
      try {
        if (nota.filesending) throw new Error('Enviando')
        if (nota.fileret ? nota.fileret.ok : false) throw new Error('Arquivo já foi enviando')
      } catch (error) {
        console.error(error)
        return
      }

      var input = app.$refs['file_' + nota.notachave][0]
      input.click()
    },
    loadFile (event, nota) {
      var app = this
      // Reference to the DOM input element
      const { files } = event.target
      // Ensure that you have a file before attempting to read it
      if (files && files[0]) {
        nota.file = files[0]
        app.sendFile(nota)
      }
    },
    sendFile (nota) {
      var app = this
      app.$nextTick(() => {
        app.loading = true
        delete nota.fileret
        nota.filesending = true
        app.loading = false
      })
      var formData = new FormData()
      formData.append('file', nota.file)
      formData.append('notachave', nota.notachave)
      formData.append('token', app.token)
      formData.append('accesscode', app.xmls.accesscode)

      var instance = axios.create({
        baseURL: app.$configini.API_URL,
        withCredentials: false,
        headers: {
          'x-auth-uuid': app.$store.state.usuario.uuid,
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Headers': 'Authorization',
          'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
          'Content-Type': 'application/json;charset=UTF-8'
        }
      })
      instance.defaults.headers.common['Content-Type'] = 'multipart/form-data'

      instance.post('/v1/publico/notas/' + app.xmls.cliente.cnpj + '/xmlpendente/envio', formData).then(response => {
        let data = response.data
        var ret = { ok: false, msg: '' }
        if (data) {
          ret.msg = data.msg ? data.msg : ''
          if (data.ok) ret.ok = true
        }
        app.$nextTick(() => {
          app.loading = true
          delete nota.file
          delete nota.filesending
          nota.fileret = { ok: (data ? data.ok : false), msg: data.msg ? data.msg : '' }
          app.loading = false
          if (nota.fileret.ok) {
            setTimeout(() => {
              if (app.countSucesso === app.xmls.notas.length) app.fimEnvio()
            }, 300)
          }
        })
        return ret
      }).catch(error => {
        app.$nextTick(() => {
          app.loading = true
          delete nota.file
          delete nota.filesending
          nota.fileret = { ok: false, msg: error.message }
          console.error(error)
          app.loading = false
        })
      })
      // setTimeout(() => {
      //   app.$nextTick(() => {
      //     app.loading = true
      //     delete nota.file
      //     delete nota.filesending
      //     app.testeerro = true
      //     nota.fileret = { ok: app.testeerro, msg: (app.testeerro ? '' : 'TESTE DE ERRO') }
      //     app.loading = false
      //   })
      //   setTimeout(() => {
      //     if (app.countSucesso === app.xmls.notas.length) app.fimEnvio()
      //   }, 300)
      // }, 1500)
    },
    async fimEnvio () {
      var app = this
      app.xmls.notas = null
    },
    async init () {
      var app = this
      app.loading = true
      app.error = null
      var ret = await app.xmls.fetch(app.token, app.recaptchatoken)
      if (!ret.ok) {
        app.error = (ret.msg ? ret.msg !== '' : false) ? ret.msg : null
      }
      app.loading = false
    }
  }
}
</script>

<style>
.meuusuariodesktop {
  min-width: 450px
}
.gradientToolbar {
  background-color: #f05b41; /* For browsers that do not support gradients */
  background-image: linear-gradient(to right, #f05b41 , #f05b41); /* Standard syntax (must be last) */
}
.doc-page {
  padding: 16px 46px;
  font-weight: 300;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto
}
.doc-page>div,.doc-page>pre {
  margin-bottom: 22px
 }
@media (max-width: 600px) {
  .doc-page {
    padding: 0px
  }
  .titletoobarcustom {
    min-width: auto;
    padding-left: 16px;
  }
}
</style>
