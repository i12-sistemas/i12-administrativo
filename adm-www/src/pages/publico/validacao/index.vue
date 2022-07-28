<template>
<div>
  <q-page class="">
    <div class="row doc-header justify-center items-center" >
      <div class="col-xs-12 col-sm-6 col-lg-4 " :class="$q.platform.is.mobile ? '' : 'q-pa-lg'" >
        <div class="col-12" >
          <div class="col-12" v-if="error && !loading">
            <q-banner class="bg-red text-white" rounded>
              <div class="text-h6"><q-icon name="link_off" class="text-white q-mr-md" size="40px" /> Link quebrado</div>
              <div class="q-ma-lg">{{error}}</div>
            </q-banner>
          </div>
          <div class="col-12" v-if="loading">
             <q-card class="q-pa-none q-my-md" flat bordered v-if="error ? error !== '' : false" >
              <q-card-section class="text-center q-py-md">
                <q-spinner color="primary" size="3rem" :thickness="5" />
                <div class="full-width text-center text-primary q-mt-lg ">consultando link, aguarde!</div>
              </q-card-section>
            </q-card>
          </div>
          <div class="col-12" v-if="!error && !loading">
            <q-card flat bordered>
              <q-card-section>
                <div class="text-left text-h6 text-weight-bold">Link de validação de contato</div>
              </q-card-section>
              <q-separator  />
              <q-card-section class="text-center" v-if="!retcheck">
                <div class="full-width row items-center">
                  <div class="col-xs-12 col-md-8 text-left text-h6">
                    <div v-if="tipo === 'email'">{{ $helpers.emailhide(chave)}}</div>
                    <div v-if="tipo === 'whatsapp'">{{$helpers.emailphone(chave)}}</div>
                  </div>
                  <div class="col-xs-12 col-md-4 text-center" >
                    <div style="max-height: 10em; aspect-ratio: 1/1;margin: 0 auto;">
                      <lottieinternal arquivo="whatsapp" v-if="tipo === 'whatsapp'"  />
                      <lottieinternal arquivo="email" v-if="tipo === 'email'"  />
                    </div>
                  </div>
                </div>
              </q-card-section>
              <q-separator v-if="!retcheck" />
               <q-card-section class="text-center" v-if="!error && !loading && retcheck">
                <div class="full-width row items-center">
                  <div class="col-xs-12 col-md-8 text-left text-h6">
                    <div>{{retcheck.msg}}</div>
                  </div>
                  <div class="col-xs-12 col-md-4 text-center" >
                    <div  v-if="!retcheck.ok">
                      <q-icon name="error" size="6em" color="red" />
                    </div>
                    <div v-if="retcheck.ok">
                      <q-icon name="check_circle" size="6em" color="green" />
                    </div>
                  </div>
                </div>
              </q-card-section>
              <q-card-section class="text-center q-py-md" v-if="!retcheck">
                <div class="row justify-center items-center" >
                  <div class="col-12">
                    <q-input v-model="code" outlined type="phone" label="Código de validação" maxlength="8" ref="txtchave" input-class="text-h5"  />
                  </div>
                </div>
              </q-card-section>
              <q-card-section class="text-center q-py-md" v-if="!retcheck && (recaptchasitekey ? recaptchasitekey !== '' : false)">
                <div class="full-width row justify-center items-center">
                  <div v-if="recaptchaloading">carregando...</div>
                  <vue-recaptcha :sitekey="recaptchasitekey" @verify="verify" :loadRecaptchaScript="true" @render="rendered" ></vue-recaptcha>
                </div>
              </q-card-section>
              <q-card-actions vertical align="center">
                <q-btn :color="allowConsulta ? 'primary' : 'grey-6'" :unelevated="allowConsulta" :outline="!allowConsulta" @click="actVerificar"
                  v-if="allowConsulta && !retcheck"
                  label="verificar" class="full-width q-py-sm" icon="verified_user" />
                <q-btn color="red" outline @click="inicializa" v-if="retcheck ? !retcheck.ok : false"
                  label="Tentar novamente" class="full-width q-py-sm" icon="arrow_back" />
              </q-card-actions>
            </q-card>
          </div>
        </div>
      </div>
    </div>
    </q-page>
</div>
</template>

<script>
import axios from 'axios'
import VueRecaptcha from 'vue-recaptcha'
export default {
  name: 'publico.validacao.contato',
  components: {
    VueRecaptcha
  },
  data () {
    return {
      tipo: null,
      chave: null,
      token: null,
      code: null,
      title: 'Validação de contato (e-mail/WhatsApp)',
      recaptchaloading: true,
      recaptchatoken: null,
      recaptchasitekey: null,
      loading: false,
      error: null,
      retcheck: null
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
  async mounted () {
    var app = this
    app.loading = true
    app.recaptchasitekey = app.$configini.RECAPTCHA_KEY ? app.$configini.RECAPTCHA_KEY : null
    if (app.recaptchasitekey ? app.recaptchasitekey === '' : true) app.recaptchasitekey = null
    await app.inicializa()
  },
  computed: {
    allowConsulta: function () {
      var app = this
      var b = true
      try {
        if (app.loading) throw new Error('Em loading')
        if (app.error) throw new Error('com erro')
        // if (app.recaptchasitekey ? app.recaptchasitekey !== '' : false) {
        //   if (!app.recaptchatoken) throw new Error('Sem recaptcha nota')
        // }
        if (app.tipo ? ['email', 'whatsapp'].indexOf(app.tipo) < 0 : true) throw new Error('Tipo de validação incorreta')
        if (app.token ? app.token === '' : true) throw new Error('Token inválido')
        if (app.chave ? app.chave === '' : true) throw new Error('Chave de validação incorreta')
        if (app.code ? app.code.length < 4 : true) throw new Error('code de validação incorreta')
      } catch (error) {
        b = false
      }
      return b
    }
  },
  methods: {
    async inicializa () {
      var app = this
      try {
        app.loading = true
        app.retcheck = null
        app.error = null
        app.tipo = app.$route.params.tipo
        app.chave = (app.$route.query.chave ? app.$route.query.chave !== '' : false) ? atob(app.$route.query.chave) : null
        app.token = (app.$route.query.token ? app.$route.query.token !== '' : false) ? app.$route.query.token : null
        app.code = (app.$route.query.code ? app.$route.query.code !== '' : false) ? app.$route.query.code : null
        if (app.tipo ? ['email', 'whatsapp'].indexOf(app.tipo) < 0 : true) throw new Error('Tipo de validação incorreta')
        if (app.token ? app.token === '' : true) throw new Error('Token inválido')
        if (app.chave ? app.chave === '' : true) throw new Error('Chave de validação incorreta')
      } catch (error) {
        app.error = error.message
      } finally {
        app.loading = false
      }
    },
    rendered (id) {
      this.recaptchaloading = false
    },
    async verify (response) {
      this.recaptchatoken = response
    },
    actClear () {
      var app = this
      app.loading = true
      app.dataresumo = null
      app.eventos = null
      app.dadosconsulta.chave = ''
      app.dadosconsulta.numero = ''
      app.dadosconsulta.cnpjemitente = ''
      app.dadosconsulta.cnpjdestinatario = ''
      app.loading = false
      app.$nextTick(() => {
        if (app.dadosconsulta.modo === 'chave') {
          app.$refs.txtchave.focus()
        } else {
          app.$refs.txtnumero.focus()
        }
      })
      var query = { t: new Date().getTime() }
      try {
        app.$router.replace({ name: app.$route.name, query: query, replace: true, append: false })
      } catch (error) {
        console.error(error)
      }
    },
    async actVerificar () {
      var app = this
      try {
        if (app.loading) throw new Error('Em loading')
        // if (!app.recaptchatoken) throw new Error('Sem recaptcha nota')
        if (!app.allowConsulta) throw new Error('Dados inválidos')

        app.retcheck = await app.sendvalidacao()
        console.log(app.retcheck)
        if (app.retcheck.ok) {
        } else {
          app.$q.notify({
            color: 'red',
            icon: 'error',
            position: 'center',
            multiLine: true,
            timeout: 3000,
            message: app.retcheck.msg
          })
        }
      } catch (error) {
        app.retcheck = { ok: false, msg: error.message }
        app.$helpers.showDialog({ ok: false, msg: error.message }, true)
      }
    },
    async sendvalidacao () {
      var app = this
      var params = {
        token: app.token,
        code: app.code
      }
      if (app.tipo === 'email') params['email'] = app.chave
      if (app.tipo === 'whatsapp') params['whatsapp'] = app.chave
      if (app.recaptchasitekey ? app.recaptchasitekey !== '' : false) {
        params['g-recaptcha-response'] = app.recaptchatoken
      }

      var req = axios.create({
        baseURL: app.$configini.API_URL,
        withCredentials: false,
        headers: {
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Headers': 'Authorization',
          'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
          'Content-Type': 'application/json;charset=UTF-8'
        }
      })
      let ret = await req.post('v1/public/meiocomunicacao/validar', params).then(response => {
        let data = response.data
        var ret = { ok: false, msg: '' }
        if (data) {
          ret.msg = data.msg ? data.msg : ''
          ret.ok = data.ok
        }
        return ret
      }).catch(error => {
        return { ok: false, msg: error.message }
      })
      return ret
    }
  }
}
</script>
