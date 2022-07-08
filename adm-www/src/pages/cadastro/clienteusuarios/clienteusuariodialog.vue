<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-70'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">Gerenciar usuários do cliente :: {{ (!loading ? (cliente ? cliente.razaosocial_old +  ' [id: ' + cliente.id + ']' : '?') : 'Consultando')}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>
          Gerenciar usuários do cliente :: {{ (!loading ? (cliente ? cliente.razaosocial_old +  ' [id: ' + cliente.id + ']' : '?') : 'Consultando')}}
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-toolbar class="bg-primary text-white" v-if="permiteusuariosconsultar ? permiteusuariosconsultar.ok : false">
        <q-btn flat rounded icon="add" label="Adicionar" @click="actAddUsuario" :disable="loading" v-if="permiteusuariosgerenciar ? permiteusuariosgerenciar.ok : false"/>
        <q-btn flat round icon="sync" @click="refreshData" :loading="loading" />
      </q-toolbar>
      <q-card-section>
        <div v-if="!loading && cliente" class="no-padding scroll" style="max-height: 80vh">
          <q-table :data="rows" :columns="columns" :loading="loading" :rows-per-page-options="[0]" row-key="id" flat bordered dense >
            <template v-slot:body-cell-action="props">
              <q-td :props="props">
                <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '10px'"
                  icon="edit" color="grey-9" @click="actEditUsuario(props.row)" />
              </q-td>
            </template>
            <template v-slot:body-cell-ultimoacesso="props">
              <q-td :props="props">
                <div v-if="props.row.ultimoacesso">{{ $helpers.datetimeRelativeToday(props.row.ultimoacesso) }}
                  <q-tooltip>
                    <div>Último acesso em  {{ $helpers.datetimeToBR(props.row.ultimoacesso) }}</div>
                  </q-tooltip>
                </div>
                <div v-else class="text-blue">
                  Nunca acessou!
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-createdat="props">
              <q-td :props="props">
                <div v-if="props.row.created_usuario && !props.row.new">
                  {{ props.row.created_usuario.nome }} - {{ $helpers.datetimeRelativeToday(props.row.created_at) }}
                  <q-tooltip>
                    <div>Adicionado por  {{ props.row.created_usuario.nome }}</div>
                    <div>em  {{ $helpers.datetimeToBR(props.row.created_at) }}</div>
                  </q-tooltip>
                </div>
                <div v-if="props.row.new" class="text-blue">
                  <q-icon name="add" color="blue" size="14px" /> Novo registro
                  <q-tooltip>
                    <div>Registro será inserido ao salvar perfil</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-ativo="props">
              <q-td :props="props">
                <span :class="props.row.ativo ? '' : 'text-red text-weight-bold'">{{props.row.ativo ? 'Ativo' : 'Inativo'}}</span>
              </q-td>
            </template>
        </q-table>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import ClienteUsuarios from 'src/mvc/collections/clienteusuarios.js'
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
    }
  },
  data () {
    var dataset = new ClienteUsuarios()
    return {
      loading: true,
      permiteusuariosconsultar: null,
      permiteusuariosgerenciar: null,
      dataset,
      rows: [],
      columns: [
        { name: 'action', style: 'width: 30px', align: 'left', label: '', field: 'id', sortable: false },
        { name: 'nome', align: 'left', label: 'Nome', field: 'nome', sortable: true },
        { name: 'ativo', align: 'center', label: 'Status', field: 'ativo', sortable: true },
        { name: 'email', align: 'left', label: 'E-mail', field: 'email', sortable: true },
        { name: 'celular', align: 'left', label: 'Celular', field: 'celular', sortable: true },
        { name: 'clientecount', align: 'center', label: 'Clientes', field: 'clientecount', sortable: true },
        { name: 'ultimoacesso', style: 'width: 220px', align: 'left', label: 'Último acesso', field: 'ultimoacesso', sortable: true },
        { name: 'createdat', style: 'width: 220px', align: 'left', label: 'Adicionado por', field: 'created_at', sortable: true },
        { name: 'id', style: 'width: 30px', align: 'center', label: 'ID', field: 'id', sortable: true }
      ]
    }
  },
  async mounted () {
  },
  methods: {
    actClose () {
      this.onClose(false)
    },
    onClose (TeveAlteracao) {
      this.$emit('ok', { ok: TeveAlteracao, dataset: TeveAlteracao ? this.localdataset : null })
      this.hide()
    },
    async actAddUsuario () {
      var app = this
      var usuario = new ClienteUsuario()
      var ret = await usuario.ShowDialogAdd(app, app.cliente)
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        app.refreshData()
      }
    },
    async actEditUsuario (pRow) {
      var app = this
      var ret = await pRow.ShowDialogEdit(app)
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        app.refreshData()
      }
    },
    async refreshData () {
      var app = this
      if (app.permiteusuariosconsultar ? !app.permiteusuariosconsultar.ok : true) {
        app.loading = false
        app.$helpers.showDialog({ ok: false, msg: app.permiteusuariosconsultar.msg })
        return
      }
      app.loading = true
      app.dataset.showall = 1
      var ret = await app.dataset.fetchPorCliente(app.cliente.id)
      if (ret.ok) {
        app.rows = app.dataset.itens
      } else {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      }
      app.loading = false
    },
    // following method is REQUIRED
    // (don't change its name --> "show")
    show () {
      var app = this
      this.permiteusuariosconsultar = this.$helpers.permite('cadastros.clientes.usuarios.consultar')
      this.permiteusuariosgerenciar = this.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      this.$refs.dialog.show()
      setTimeout(() => {
        app.refreshData()
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
      this.$emit('ok', { ok: true, dataset: this.localdataset })
      this.hide()
    }

  }
}
</script>
