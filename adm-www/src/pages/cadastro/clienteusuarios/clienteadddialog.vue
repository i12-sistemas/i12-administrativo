<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-50'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">Associar cliente ao usuário {{ (usuario ? usuario.id > 0 : false) ? usuario.nome : '?'}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>
          aAssociar cliente ao usuário {{ (usuario ? usuario.id > 0 : false) ? usuario.nome : '?'}}
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section>
        <div class="row full-width q-col-gutter-sm">
          <div class="col-12">
            <div class="col-12 q-my-md">
              <my-select-clientes outlined ref="txtcliente" label="Cliente a ser associado" nullabled :dense="!$q.platform.is.mobile"
              v-model="clienteselecionado" />
            </div>
          </div>
          <div class="col-12 q-mt-md">
            <q-btn unelevated label="Associar cliente" color="primary" class="q-mr-sm" @click="actSaveAdd" :disable="clienteselecionado ? !(clienteselecionado.id > 0) : true" />
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
  name: 'cliente.usuarios.gerenciar.cliente.add.dialog',
  components: {
  },
  props: {
    usuario: {
      type: Object,
      default: function () {
        return null
      }
    }
  },
  data () {
    return {
      loading: true,
      clienteselecionado: null
    }
  },
  async mounted () {
  },
  computed: {
  },
  methods: {
    actClose () {
      this.onClose(false)
    },
    onClose () {
      this.$emit('ok', { ok: false })
      this.hide()
    },
    async actSaveAdd () {
      var app = this
      try {
        app.loading = true
        // var permite = app.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
        // if (!permite.ok) throw new Error(permite.msg)
        // if (app.usuario ? !(app.usuario.id > 0) : true) throw new Error('Nenhum usuário informado')
        // if (app.clienteselecionado ? !(app.clienteselecionado.id > 0) : true) throw new Error('Nenhum cliente selecionado')
      } catch (error) {
        app.loading = false
        var a = app.$helpers.showDialog({ ok: false, msg: error.message })
        await a.then(function () {})
        return false
      }
      var lUsuario = new ClienteUsuario()
      lUsuario.id = app.usuario.id
      var ret = await lUsuario.clienteAdd(app.clienteselecionado.id)
      if (!ret.ok) {
        var aRet = app.$helpers.showDialog(ret)
        await aRet.then(function () {})
      } else {
        app.onOKClick()
      }
      app.loading = false
    },
    // following method is REQUIRED
    // (don't change its name --> "show")
    show () {
      var app = this
      this.$refs.dialog.show()
      setTimeout(() => {
        app.$refs.txtcliente.actOnFocus()
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
