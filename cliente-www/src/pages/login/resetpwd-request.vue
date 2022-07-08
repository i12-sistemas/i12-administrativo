<template>
<q-card flat class="full-width" >
  <q-card-section class="text-center">
    <span class="text-h6"><q-icon name="vpn_key" color="grey" /> Alteração de senha</span>
  </q-card-section>
  <q-card-section v-if="solicitado">
    <div class="row wrap justify-center q-pt-lg content-start">
      <q-icon name="check_circle" color="positive" size="60px" />
    </div>
    <div class="row justify-center q-pa-sm content-start text-body">
      Link enviado ao e-mail informado.
    </div>
    <div class="row wrap justify-center content-start text-body">
      Acesse sua caixa de e-mail e siga as instruções para concluir a alteração de senha!
    </div>
    <div class="row wrap justify-center q-pt-md content-start text-body">
      * Não esquece de conferir na sua caixa de Spam.
    </div>
    <div class="row wrap justify-center content-start text-caption">
      O link expira em 1 hora!
    </div>
  </q-card-section>
  <q-card-section v-if="!solicitado">
    <q-input outlined color="primary" bg-color="white" label-color="grey-10" ref="txtuser" v-model="username" :loading="sending" :disable="sending"
      label="E-mail ou celular" maxlength="255"
      />
  </q-card-section>
  <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
    <q-btn class="full-width" unelevated color="primary" text-color="white" icon="send" label="Solicitar alteração"
      :loading="sending" @click="actRequest"
      :disable="solicitado"
      />
  </q-card-actions>
  <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
    <q-btn class="full-width" unelevated :to="{ name: 'login.usuario' }" label="Voltar" no-caps
      color="grey-2" text-color="black" icon="arrow_back" :disable="sending" />
  </q-card-actions>
  <q-card-section>
    <p class="text-caption text-center">
      Seu IP esta sendo registrado!
    </p>
  </q-card-section>
</q-card>
</template>

<script>
import axios from 'axios'
export default {
  data () {
    return {
      username: '',
      solicitado: false,
      sending: false
    }
  },
  computed: {
  },
  async mounted () {
  },
  methods: {
    async redirectNow () {
      var app = this
      if (app.redirect) {
        app.$router.push({ path: app.redirect })
      } else {
        app.$router.push({ name: 'home' })
      }
    },
    async closeApp () {
      // var app = this
      navigator.app.exitApp()
    },
    async actRequest (e) {
      var app = this
      try {
        if (app.username === '') {
          app.$refs.txtuser.$el.focus()
          return
        }
      } catch (error) {
        if (error.message) {
          app.actShowError('Validação', error.message, 4000)
        } else {
          return
        }
      }
      app.sending = true
      let ret = await app.sendRequest()
      if (!ret.ok) {
        app.actShowError('Acesso restrito', ret.msg, 4000)
      } else {
        if (ret.msg !== '') {
          app.$q.notify({
            message: ret.msg,
            color: 'positive',
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
        }
        app.$router.push({ name: 'login.resetpwd.change', query: { username: app.username, tipouser: app.tipouser } })
      }
      app.sending = false
    },
    actShowError (title, msg, timeoutclose) {
      var app = this
      const dialog = app.$q.dialog({
        title: title,
        message: msg
      }).onDismiss(() => {
        clearTimeout(timer)
        app.email = ''
        app.username = ''
        app.$refs.txtuser.$el.focus()
      })

      const timer = setTimeout(() => {
        dialog.hide()
      }, timeoutclose)
    },
    async sendRequest () {
      var app = this
      try {
        if (app.username === '') {
          this.$refs.txtuser.$el.focus()
          throw new Error('Usuário inválido')
        }
      } catch (error) {
        return { ok: false, msg: error.message }
      }

      var credentials = 'Basic ' + btoa(app.username + ':' + app.tipouser)
      var req = axios.create({
        baseURL: app.$configini.API_URL,
        withCredentials: false,
        headers: {
          'x-auth-uuid': app.$store.state.usuario.uuid,
          'Authorization': credentials,
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Headers': 'Authorization',
          'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
          'Content-Type': 'application/json;charset=UTF-8'
        }
      })
      var ret = await req.post('v1/painelcliente/login/usuario/resetpwd/request').then(response => {
        let data = response.data
        return data
      }
      ).catch(error => {
        let msg = error
        if (error.response) {
          msg = 'Code: ' + error.response.status + ' - ' + error.response.data.message
        } else {
          msg = error.message
        }
        return { ok: false, msg: 'Falha ao consultar servidor online: ' + msg }
      })
      if (ret.ok) {
        app.solicitado = true
      }
      return ret
    }
  }
}
</script>

<style scoped>
.min-width {
  min-width: 450px !important;
}
</style>
