<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-50'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">Novo usuário do painel do cliente</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>
          Novo usuário do painel do cliente
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section>
        <div class="row full-width q-col-gutter-sm">
          <div class="col-12 q-mb-sm" v-if="cliente ? cliente.id > 0 : false">
            <div class="text-caption">Usuário será associado ao cliente:</div>
            <div class="text-weight-bold">{{cliente.razaosocial_old}}</div>
          </div>
          <div class="col-12">
            <q-input outlined v-model="dataset.email" type="email" maxlength="255" label="* E-mail" :dense="!$q.platform.is.mobile" stack-label counter
              ref="txtemail" @keydown.enter="actValidarEmail">
              <template v-slot:append>
                <q-btn color="black" flat  icon="search" label="Validar" @click="actValidarEmail" :loading="validando" v-if="!emailchecado.ok || emailchecado.email !== dataset.email"/>
              </template>
            </q-input>
            <div v-if="emailchecado.msg ? emailchecado.msg !== '' : false" class="rounded-borders q-pa-md"
              :class="emailchecado.ok ? 'bg-green-1 text-green' : 'bg-red-1 text-red'"
            >
              {{emailchecado.msg}}
              <div class="q-my-sm" v-if="!emailchecado.ok && (emailchecado.usuario ? emailchecado.usuario.id > 0 : false)">
                <div>Deseja associar o cliente <b>{{cliente.razaosocial_old}}</b> com o usuário <b>{{emailchecado.usuario.nome}}</b>?</div>
                <q-btn unelevated class="q-mt-sm" color="primary" label="Sim, quero associar agora!" no-caps @click="actAssociarAgora" />
              </div>
            </div>
          </div>
          <div class="col-12">
            <q-input outlined v-model="dataset.nome" type="text" maxlength="60" counter label="* Nome" :dense="!$q.platform.is.mobile" stack-label ref="txtnome"
              @keydown.enter="$refs.txtcelular.$el.focus()"
             />
          </div>
          <div class="col-12">
            <q-input outlined v-model="dataset.celular" type="phone" maxlength="11" hint="Informe somente número com o DDD - Campo opcional"
              stack-label label="Celular" :dense="!$q.platform.is.mobile" ref="txtcelular" @keydown.enter="actSave" />
          </div>
          <div class="col-12 q-mt-md">
            <q-btn unelevated label="Salvar" color="primary" class="q-mr-sm" @click="actSave" :disable="!permiteSave" />
            <q-btn flat label="Sair" icon="clear" @click="actClose"/>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import ClienteUsuario from 'src/mvc/models/clienteusuario.js'
export default {
  name: 'cliente.usuarios.gerenciar.dialog',
  components: {
  },
  props: {
    cliente: {
      type: Object,
      default: function () {
        return null
      }
    },
    usuario: {
      type: Object,
      default: function () {
        return null
      }
    }
  },
  data () {
    var dataset = new ClienteUsuario()
    return {
      loading: true,
      validando: false,
      emailchecado: { ok: false, msg: null, email: null, usuario: null },
      dataset
    }
  },
  async mounted () {
  },
  computed: {
    permiteSave: function () {
      var app = this
      try {
        var allow = false
        if (app.loading) throw new Error('loading.')
        if (!app.emailchecado.ok) throw new Error('E-mail não foi checado.')
        if (app.emailchecado.email !== app.dataset.email) throw new Error('E-mail diferente do checado.')
        if (app.dataset.nome ? app.dataset.nome === '' : true) throw new Error('Nome inválido')
        allow = true
      } catch (error) {
        allow = false
      }
      return allow
    }
  },
  methods: {
    actClose () {
      this.onClose(false)
    },
    onClose () {
      this.$emit('ok', { ok: false })
      this.hide()
    },
    async actSave () {
      var app = this
      if (!app.permiteSave) return
      app.loading = true
      if (app.cliente ? app.cliente.id > 0 : false) app.dataset.clienteid = app.cliente.id
      var ret = await app.dataset.save()
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        app.onOKClick()
      }
      app.loading = false
    },
    async actValidarEmail () {
      var app = this
      try {
        app.validando = true
        var email = app.dataset.email ? app.dataset.email.trim() : ''
        if (!app.$helpers.validaEmail(email)) throw new Error('E-mail inválido. [' + email + ']')

        app.emailchecado.usuario = new ClienteUsuario()
        await app.emailchecado.usuario.find(email)
        // if (!ret.ok) throw new Error(ret.msg)
        if (app.emailchecado.usuario ? app.emailchecado.usuario.id > 0 : false) throw new Error('Já existe um usuário cadastro com e-mail ' + email)
      } catch (error) {
        app.emailchecado.ok = false
        app.emailchecado.msg = error.message
        app.emailchecado.email = null
        app.validando = false
        app.$refs.txtemail.$el.focus()
        return
      }
      app.emailchecado.ok = true
      app.emailchecado.msg = 'Nenhum usuário cadastrado com ' + app.dataset.email
      app.emailchecado.email = app.dataset.email
      app.$refs.txtnome.$el.focus()
      app.validando = false
    },
    async actAssociarAgora () {
      var app = this
      try {
        app.loading = true
        var permite = app.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
        if (!permite.ok) throw new Error(permite.msg)

        if (app.emailchecado.usuario ? !(app.emailchecado.usuario.id > 0) : true) throw new Error('Nenhum usuário informado')
        if (app.cliente ? !(app.cliente.id > 0) : true) throw new Error('Nenhum cliente selecionado')
      } catch (error) {
        app.loading = false
        var a = app.$helpers.showDialog({ ok: false, msg: error.message })
        await a.then(function () {})
        return false
      }
      var lUsuario = new ClienteUsuario()
      lUsuario.id = app.emailchecado.usuario.id
      var ret = await lUsuario.clienteAdd(app.cliente.id)
      if (!ret.ok) {
        var aRet = app.$helpers.showDialog(ret)
        await aRet.then(function () {})
      } else {
        app.onOKClick()
      }
      app.loading = false
    },
    async refreshData () {
      var app = this
      app.loading = true
      app.dataset.limpardados()
      app.dataset.ativo = true
      app.loading = false
    },
    // following method is REQUIRED
    // (don't change its name --> "show")
    show () {
      var app = this
      this.$refs.dialog.show()
      setTimeout(() => {
        app.refreshData()
        setTimeout(() => {
          app.$refs.txtemail.$el.focus()
        }, 100)
      }, 500)
    },

    // following method is REQUIRED
    // (don't change its name --> "hide")
    hide () {
      this.$refs.dialog.hide()
    },

    onDialogHide () {
      // required to be emitted
      // when QDialog emits "hide" event
      this.$emit('hide')
    },

    onOKClick () {
      this.$emit('ok', { ok: true, dataset: this.dataset })
      this.hide()
    }

  }
}
</script>
