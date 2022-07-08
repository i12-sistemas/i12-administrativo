<template>
  <div>
    <q-card flat class="full-width" >
      <q-card-section class="q-pa-none" v-if="!checado">
        <q-form ref="myForm" @submit.prevent="actCheckCode"  >
        <div class="row flex-center q-pb-md q-px-md full-width" >
          <q-card flat :bordered="false" class="full-width q-pa-none" >
            <q-card-section class="q-pa-none q-pt-sm ">
              <div class="row justify-center content-start text-subtitle1">
                <q-icon name="check_circle" color="positive" size="24px" class="q-pr-sm" />
                Confira o código enviado por e-mail
              </div>
              <div class="row wrap justify-center q-pb-lg content-start text-subtitle1 text-weight-bold">
                {{username}}
              </div>
              <div class="row wrap justify-start content-start text-body">
                <div>Acesse sua caixa de e-mail e siga as instruções para concluir a alteração de senha!</div>
                <div class="text-caption text-left">* Não esqueça de conferir na sua caixa de Spam.</div>
              </div>
            </q-card-section>
            <q-card-section class="text-weight-bold text-center rounded-borders bg-light-green-1 text-green q-my-md q-pa-none q-py-sm">
                O código expira em 1 hora!
            </q-card-section>
            <q-card-section class="q-pa-none">
              <q-input outlined color="primary" bg-color="white" label-color="grey-10" v-model="codenumber" label="Código" stack-label type="number" ref="txtcodenumber" class="codenumber" />
            </q-card-section>
            <q-card-actions align="center">
              <q-btn class="full-width" unelevated color="primary" text-color="white" :loading="sending" type="submit" label="Validar código" />
            </q-card-actions>
            <q-card-actions align="center">
              <q-btn class="full-width" unelevated color="grey-3" text-color="black" :to="{ name: 'login.usuario' }" :disable="sending" label="Login de acesso" />
            </q-card-actions>
            <q-card-section>
              <p class="text-caption text-center">
                Seu IP esta sendo registrado!
              </p>
            </q-card-section>
          </q-card>
        </div>
        </q-form>
      </q-card-section>

      <q-card-section class="q-pa-none" v-if="checado">
        <q-form ref="myFormChecado" @submit.prevent="actChangePwd" >
        <div class="row flex-center q-pb-md q-px-md full-width">
          <q-card flat :bordered="false" class="full-width" >
            <q-card-section class="q-pa-none">
              <div class="row justify-center q-pt-sm content-start text-subtitle1">
                <q-icon name="check_circle" color="positive" size="24px" class="q-pr-sm" />
                Código verificado para a conta
              </div>
              <div class="row wrap justify-center q-pt-lg content-start text-subtitle1 text-weight-bold">
                {{datatoken.username}}
              </div>
              <div class="row wrap justify-center content-start text-body">
                Informe sua nova senha
              </div>
            </q-card-section>
            <q-card-section>
              <q-input outlined color="primary" bg-color="white" label-color="grey-10" v-model="password1" ref="password1" :loading="sending" :disable="sending" label="Senha" stack-label type="password"/>
            </q-card-section>
            <q-card-section>
              <q-input outlined color="primary" bg-color="white" label-color="grey-10" v-model="password2" ref="password2" :loading="sending" :disable="sending" label="Confirmação de senha" stack-label type="password"/>
            </q-card-section>
            <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
              <q-btn class="full-width" unelevated color="primary" text-color="white" :loading="sending" type="submit" label="Alterar senha" />
            </q-card-actions>
            <q-card-actions align="center" class="row wrap justify-between q-px-md content-start">
              <q-btn class="full-width" color="grey-2" text-color="black" icon="arrow_back" unelevated :to="{ name: 'login.usuario' }" label="Login de acesso" :disable="sending"/>
            </q-card-actions>
            <q-card-section>
              <p class="text-caption text-center">
                Seu IP esta sendo registrado!
              </p>
            </q-card-section>
          </q-card>
        </div>
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  data () {
    return {
      email: '',
      username: '',
      codenumber: '',
      checado: false,
      datatoken: null,
      sending: false,
      password1: '',
      password2: ''
    }
  },
  computed: {
  },
  async mounted () {
    var app = this
    if (app.$route.query) {
      if (app.$route.query.username) app.username = app.$route.query.username
      if (app.$route.query.codenumber) app.codenumber = app.$route.query.codenumber
    }
    if ((app.username !== '') && (app.codenumber !== '')) {
      setTimeout(() => {
        app.actCheckCode()
      }, 500)
    }
  },
  methods: {
    async actChangePwd (e) {
      var app = this
      if (app.password1 === '') {
        app.$refs.txtpassword1.$el.focus()
        return
      }
      if (app.password2 === '') {
        app.$refs.password2.$el.focus()
        return
      }
      app.sending = true
      let ret = await app.sendChangePwd()
      if (!ret.ok) {
        app.actShowError('Acesso restrito', ret.msg, 4000)
      } else {
        if (ret.msg !== '') {
          app.$q.notify({
            message: 'Senha alterada!',
            color: 'positive',
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
        }
        app.$router.push({ name: 'login.usuario' })
      }
      app.sending = false
    },
    async sendChangePwd () {
      var app = this
      try {
        if (!app.datatoken) throw new Error('Código não checado')
        if (!app.checado) throw new Error('Código não checado')
        if (app.datatoken.username === '') throw new Error('Usuário inválido')
        if (app.datatoken.codenumber === '') throw new Error('Código não informado')
        if (app.datatoken.token === '') throw new Error('Token não informado')

        if (app.password1 === '') throw new Error('Senha inválida')
        if (app.password2 === '') throw new Error('Confirmação de senha inválida')
        if (!(app.password2 === app.password1)) throw new Error('Senha e confirmação estão diferentes')
      } catch (error) {
        return { ok: false, msg: error.message }
      }
      let params = {
        username: app.datatoken.username,
        codenumber: app.datatoken.codenumber,
        token: app.datatoken.token,
        pwd: app.password1
      }
      var req = axios.create({
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
      var ret = await req.post('v1/painelcliente/login/usuario/resetpwd/changepwd', params).then(response => {
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
      return ret
    },
    async closeApp () {
      // var app = this
      navigator.app.exitApp()
    },
    async actCheckCode (e) {
      var app = this
      if (app.code === '') {
        app.$refs.txtcodenumber.$el.focus()
        return
      }
      app.sending = true
      let ret = await app.sendCheckCode()
      if (!ret.ok) {
        app.actShowError('Acesso restrito', ret.msg, 4000)
      } else {
        app.checado = true
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
        app.codenumber = ''
      })

      const timer = setTimeout(() => {
        dialog.hide()
      }, timeoutclose)
    },
    async sendCheckCode () {
      var app = this
      try {
        if (app.username === '') throw new Error('Usuário inválido')
        if (app.codenumber === '') throw new Error('Código não informado')
      } catch (error) {
        return { ok: false, msg: error.message }
      }
      let params = {
        username: app.username,
        codenumber: app.codenumber
      }
      var req = axios.create({
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
      var ret = await req.post('v1/painelcliente/login/usuario/resetpwd/checkcode', params).then(response => {
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
        app.checado = true
        app.datatoken = ret.data
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

.codenumber {
    font-size:1.5rem;
    line-height:1.5rem;
}
</style>
