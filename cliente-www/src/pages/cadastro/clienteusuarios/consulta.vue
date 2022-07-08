<template>
  <div>
    <q-page class="">
      <div class="row doc-header" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'">
        <div class="col-sm-12">
          <q-table :data="rows" :columns="columns" dense :loading="loading" :rows-per-page-options="$qtable.rowsperpageoptions"
                   :pagination.sync="dataset.pagination" row-key="id" @request="refreshData"
                   :filter="filter" >
            <template v-slot:top-right>
              <q-btn color="black" outline  label="adicionar" @click="actNewDialog" class="q-mr-sm" />
              <q-input outlined dense debounce="300" v-model="filter" placeholder="Procurar" :loading="loading">
                <template v-slot:append>
                  <q-checkbox v-model="dataset.showall" color="red" >
                    <q-tooltip :delay="700">
                      <div class="text-weight-bold">Mostrar todos</div>
                      <div>Se marcado essa opção mostra todos os usuários ativos e inativos</div>
                      <div>Se desmarcado mostra somente usuários ativos</div>
                    </q-tooltip>
                  </q-checkbox>
                  <q-icon name="sync" @click="refreshData(null)" v-if="!loading" />
                </template>
              </q-input>
            </template>
            <!-- body -->
            <template v-slot:body-cell-action="props">
              <q-td :props="props">
                <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '10px'"
                       @click="actEditUsuario(props.row)"
                       icon="edit" color="grey-9"
                />
              </q-td>
            </template>
            <template v-slot:body-cell-permissao="props">
              <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                <q-avatar size="24px" font-size="18px"
                          :color="props.row.pemitefup ? 'blue' : 'red-3'" text-color="white" icon="insights" class="q-mr-sm" >
                  <q-tooltip :delay="500">Permissão de "Ver FUP" :: {{props.row.pemitefup ? 'Liberada' : 'Bloqueado'}} </q-tooltip>
                </q-avatar>
                <q-avatar size="24px" font-size="18px" text-color="white" icon="receipt_long" class="q-mr-sm"
                          :color="props.row.permitecoletasver ? 'blue' : 'red-3'"
                >
                  <q-tooltip :delay="500">Permissão de "Ver Coletas" :: {{props.row.permitecoletasver ? 'Liberada' : 'Bloqueado'}} </q-tooltip>
                </q-avatar>
                <q-avatar size="24px" font-size="18px" text-color="white" icon="dashboard"
                          :color="props.row.permitestatusgeral ? 'blue' : 'red-3'"
                >
                  <q-tooltip :delay="500">Permissão de "Ver Status Geral" :: {{props.row.permitestatusgeral ? 'Liberada' : 'Bloqueado'}} </q-tooltip>
                </q-avatar>
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
                <div v-if="props.row.created_usuario">
                  {{ props.row.created_usuario ? props.row.created_usuario.nome : 'Não identificado' }} - {{ $helpers.datetimeRelativeToday(props.row.created_at) }}
                  <q-tooltip>
                    <div>Adicionado por  {{ props.row.created_usuario ? props.row.created_usuario.nome : 'Não identificado' }}</div>
                    <div>em  {{ $helpers.datetimeToBR(props.row.created_at) }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-ativo="props">
              <q-td :props="props">
                <span :class="props.row.ativo ? '' : 'text-red text-weight-bold'">{{props.row.ativo ? 'Ativo' : 'Inativo'}}</span>
              </q-td>
            </template>
            <!-- body -->
          </q-table>
        </div>
      </div>
      <q-page-sticky position="top" expand>
        <q-toolbar class="bg-white text-primary shadow-up-21">
          <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
          <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
          <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
          <q-toolbar-title>Gestão de Usuários</q-toolbar-title>
          <q-btn  icon="sync" :label="$q.platform.is.mobile ? '' : 'Atualizar'" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
        </q-toolbar>
      </q-page-sticky>
    </q-page>
  </div>
</template>

<script>
import ClienteUsuarios from 'src/mvc/collections/clienteusuarios.js'
import Cliente from 'src/mvc/models/cliente.js'
export default {
  name: 'cadastro.clientes.usuario',
  components: {
  },
  data () {
    var dataset = new ClienteUsuarios()
    return {
      dataset,
      permiteusuariosconsultar: null,
      permiteusuariosgerenciar: null,
      orderbylist: { nome: 'asc' },
      error: null,
      rows: [],
      filter: '',
      columns: [
        { name: 'action', style: 'width: 30px', align: 'left', label: '', field: 'id', sortable: false },
        { name: 'nome', align: 'left', label: 'Nome', field: 'nome', sortable: false },
        { name: 'ativo', align: 'center', label: 'Status', field: 'ativo', sortable: false },
        { name: 'permissao', align: 'center', label: 'Permissões', field: 'permissao', sortable: false },
        { name: 'email', align: 'left', label: 'E-mail', field: 'email', sortable: false, filterconfig: { value: null, sorted: true } },
        { name: 'ultimoacesso', style: 'width: 220px', align: 'left', label: 'Último acesso', field: 'ultimoacesso', sortable: false },
        { name: 'id', style: 'width: 30px', align: 'center', label: 'ID', field: 'id', sortable: false }
      ],
      loading: false,
      posting: false,
      msgError: ''
    }
  },
  async mounted () {
    var app = this
    this.permiteusuariosconsultar = this.$helpers.permite('cadastros.clientes.usuarios.consultar')
    this.permiteusuariosgerenciar = this.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
    if (app.$route.query) await app.queryRead()
    await app.refreshData(null)
  },

  methods: {
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
    async actDelete (pRegiao) {
      var app = this
      // app.deleting = true
      var ret = pRegiao.deleteWithQuestion(app)
      await ret.then(function (value) {
        if (value.ok) {
          app.$q.notify({
            message: 'Cliente excluído',
            color: 'positive',
            caption: value.msg,
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
          app.refreshData(null)
        } else {
          if (value.msg) {
            if (value.msg !== '') app.$helpers.showDialog(value)
          }
        }
      })
    },
    async actEditDialog (rowIndex) {
      var app = this
      var row = app.rows[rowIndex]
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) {
        app.refreshData(null)
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    async actShowUsuarioAddOrEdit (pCliente) {
      var app = this
      var ret = await pCliente.ShowDialogUsuarioGestao(app)
      if (ret.ok) {
        app.refreshData(null)
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    actInputFilter () {
      var app = this
      if (app.loading) return
      app.refreshData(null)
    },
    async actShowFilter (props, pIndex = -1) {
      var app = this
      app.showfilteridx = pIndex
      if (props) {
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.name === props.col.name) {
            app.showfilteridx = index
            break
          }
        }
      }
      this.showfilter = true
    },
    actChangeSortBy (col) {
      var app = this
      if (col) {
        var idx = -1
        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.name === col.name) {
            idx = index
            break
          }
        }
        if (idx >= 0) {
          if (app.columns[idx].filterconfig.sorted) {
            var name = app.columns[idx].name
            var ord = app.orderbylist[name]
            if (ord) {
              ord = (ord === 'asc') ? 'desc' : 'asc'
            } else {
              ord = 'asc'
            }
            app.orderbylist = {}
            app.orderbylist[name] = ord
          }
        }
      }
      app.refreshData(null)
    },
    async queryRead () {
      var app = this
      var pQuery = app.$route.query
      if (pQuery) {
        if (pQuery.pagesize) {
          var pagesize = parseInt(pQuery.pagesize)
          if (app.$qtable.rowsperpageoptions.indexOf(pagesize) >= 0) app.dataset.pagination.rowsPerPage = pagesize
        }
        if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)

        var sortby = null
        if (pQuery.sortby) {
          sortby = JSON.parse(pQuery.sortby)
          if (Object.keys(sortby).length > 0) app.orderbylist = sortby
        }

        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.filterconfig) {
            if (pQuery[element.name]) element.filterconfig.value = pQuery[element.name]
          }
        }
      }
    },
    async actNewDialog (rowIndex) {
      var app = this
      var row = new Cliente()
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) {
        await app.refreshData(null)
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    async refreshData (props) {
      var app = this
      app.loading = true
      app.msgError = ''
      app.dataset.readPropsTable(props)
      app.dataset.params = {}

      for (let index = 0; index < app.columns.length; index++) {
        const element = app.columns[index]
        if (element.filterconfig) {
          if ((element.filterconfig.value !== null) && (element.filterconfig.value !== undefined) && (element.filterconfig.value !== '')) {
            app.dataset.params[element.name] = element.filterconfig.value
          }
        }
      }

      app.dataset.orderby = null
      if (app.orderbylist) {
        var c = Object.keys(app.orderbylist).length
        if (c > 0) app.dataset.orderby = app.orderbylist
      }

      var ret = await app.dataset.fetchPorCliente(-1)
      if (ret.ok) {
        app.rows = app.dataset.itens
        try {
          // var query = await app.dataset.makequery()
          // if (query) app.$router.replace({ name: app.$route.name, query: query, replace: true, append: false })
        } catch (error) {
          console.error(error)
        }
      } else {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      }
      app.loading = false
    }
  }
}
</script>

<style lang="stylus">
</style>
