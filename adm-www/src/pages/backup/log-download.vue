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
                row-key="id"
                :rows-per-page-options="$qtable.rowsperpageoptions"
                @request="refreshData"
                >
                  <template v-slot:body-cell-sizebyte="props">
                    <q-td :props="props">
                      {{$helpers.bytesToHumanFileSizeString(props.row.sizebyte)}}
                    </q-td>
                  </template>
                  <template v-slot:body-cell-created_at="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" >
                        {{$helpers.datetimeRelativeToday(props.row.created_at)}}
                        <q-tooltip>
                          <div>Criado em {{$helpers.datetimeToBR(props.row.created_at)}}</div>
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
            <q-toolbar-title>Log de download - Tabela IBPT</q-toolbar-title>
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
import TabelasIbptLogs from 'src/mvc/collections/tabelasibptlogs.js'
export default {
  name: 'tabelaibpt.logs',
  components: {
  },
  data () {
    var dataset = new TabelasIbptLogs()
    return {
      dataset,
      rows: [],
      columns: [
        { name: 'uf', align: 'left', label: 'UF', field: 'uf', sort: true },
        { name: 'ip', align: 'left', label: 'IP', field: 'ip', sort: true },
        { name: 'sizebyte', align: 'right', label: 'Tamanho', field: 'sizebyte', sort: true },
        { name: 'created_at', align: 'left', label: 'Baixado em', field: 'created_at', sort: true },
        { name: 'ambiente', align: 'left', label: 'Ambiente', field: 'ambiente', sort: true },
        { name: 'sistema', align: 'left', label: 'Sistema', field: 'sistema', sort: true },
        { name: 'usuario', align: 'left', label: 'Usuário', field: 'usuario', sort: true },
        { name: 'cnpj', align: 'left', label: 'Cliente/CNPJ', field: 'cnpj', sort: true }
      ],
      loading: false
    }
  },
  async mounted () {
    var app = this
    await app.refreshData(null)
  },
  methods: {
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
        if (ret.ok) {
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
