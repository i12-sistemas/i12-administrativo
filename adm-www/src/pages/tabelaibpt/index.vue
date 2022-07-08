<template>
<div>
  <q-page class="">
    <div class="row doc-header" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'">
      <div class="col-12" >
        <div class="col-12" v-if="error">
          <q-banner class="bg-negative text-white">{{error}}</q-banner>
        </div>
        <q-card class="bg-white q-mt-md" flat  v-if="rows ? rows.length > 0 : false" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-separator v-if="$q.platform.is.mobile"  />
          <q-card-section class="q-pa-none">
            <div style="max-width: 100vw">
              <q-table :data="rows" :columns="columns" :dense="!$q.platform.is.mobile"  flat
                :loading="loading" color="primary" id="sticky-table"
                :pagination.sync="dataset.pagination"
                row-key="filename"
                :rows-per-page-options="[0]"
                @request="refreshData"
                >
                  <template v-slot:body-cell-action="props">
                    <q-td :props="props">
                      <q-btn flat round :dense="!$q.platform.is.mobile" :size="$q.platform.is.mobile ? '12px' : '8px'" icon="more_vert" @click="actEditDialog(props.pageIndex)" >
                        <q-tooltip>Abrir coleta</q-tooltip>
                      </q-btn>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-created_at="props">
                    <q-td :props="props" >
                      <div @click="actEditDialog(props.pageIndex)" class="cursor-pointer" >
                        {{$helpers.datetimeRelativeToday(props.row.created_at)}}
                        <q-tooltip>
                          <div>Criado em {{$helpers.datetimeToBR(props.row.created_at)}}</div>
                          <div>por {{ props.row.usuario ? props.row.usuario.nome : '?' }}</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
              </q-table>
            </div>
          </q-card-section>
        </q-card>
        <q-page-sticky position="top" expand>
          <q-toolbar class="bg-white text-primary shadow-up-21">
            <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
            <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
            <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
            <q-toolbar-title>Tabela IBPT</q-toolbar-title>
            <q-btn  icon="work_history" :label="$q.platform.is.mobile ? '' : 'log'" :round="$q.platform.is.mobile" flat :to="{ name: 'tabelaibpt.log'}" />
            <q-btn  icon="add" :label="$q.platform.is.mobile ? '' : 'Adicionar'" :round="$q.platform.is.mobile" flat @click="actAdd"  />
            <q-btn  icon="sync" :label="$q.platform.is.mobile ? '' : 'Atualizar'" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
          </q-toolbar>
        </q-page-sticky>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
import TabelasIbpt from 'src/mvc/collections/tabelasibpt.js'
export default {
  name: 'tabelaibpt.index',
  components: {
  },
  data () {
    var dataset = new TabelasIbpt()
    return {
      dataset,
      rows: [],
      columns: [
        { name: 'action', align: 'left', field: 'uf', sort: false },
        { name: 'uf', align: 'left', label: 'UF', field: 'uf', sort: true },
        { name: 'filename', align: 'left', label: 'Nome do arquivo', field: 'filename', sort: true },
        { name: 'created_at', align: 'left', label: 'Criado em', field: 'created_at', sort: true }
      ],
      loading: false,
      posting: false,
      error: null,
      customcnpj: null,
      customnumero: null,
      customchave: null
    }
  },
  created: function () {
    this.$eventbus.$on('coletasconsultaupdated', this.coletasconsultaupdated)
    // this.$eventbus.$emit('setdrawerr', 'drcoletasdashboad', false)
  },
  beforeDestroy: function () {
    this.$eventbus.$off('coletasconsultaupdated', this.coletasconsultaupdated)
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.usuario) {
          u = app.$store.state.usuario.usuario
        }
      }
      return u
    }
  },
  async mounted () {
    var app = this
    await app.refreshData(null)
  },

  methods: {
    async actAdd () {
      var app = this
      var ret = app.dataset.showAdd(app)
      console.log(ret)
    },
    async actEditDialog (rowIndex) {
      var app = this
      var row = app.rows[rowIndex]
      app.$q.bottomSheet({
        message: 'Tabela: ' + row.uf,
        grid: false,
        actions: [
          {
            label: 'Excluir',
            icon: 'delete_forever',
            color: 'red',
            id: 'delete'
          },
          {
            label: 'Download',
            icon: 'download_for_offline',
            id: 'download'
          }
        ]
      }).onOk(async action => {
        console.log(action)
        if (action.id === 'delete') {
          var retDelete = await row.deleteWithQuestion(app)
          if (retDelete.ok) {
            app.refreshData(null)
          } else {
            app.$helpers.showDialog(retDelete)
          }
        }
        if (action.id === 'download') {
          row.download(app)
        }
      })
    },
    async refreshData (props) {
      var app = this
      try {
        app.loading = true
        app.error = null
        // if (!app.usuario) throw new Error('Nenhum usuário logado')
        // if (!app.usuario.permitecoletasver) throw new Error('Sem permissão de acesso')
        app.error = null
        app.dataset.readPropsTable(props)
        var ret = await app.dataset.fetch()
        console.log(ret)
        if (ret.ok) {
          console.log(app.dataset)
          app.rows = app.dataset.itens
        } else {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      } catch (error) {
        app.loading = false
        app.error = error.message
      }
      app.loading = false
    }
  }
}
</script>
