<template>
<div>
  <q-page class="">
    <div v-if="!$q.platform.is.mobile">
      <breadcrumbs v-model="breadcrumbslist" :showactive="{ label: 'Categoria de erros' }"/>
    </div>
    <div class="row doc-page" >
      <div class="col-sm-12">
        <q-table :data="rows" :columns="columns" dense
          :loading="loading"
           :pagination.sync="dataset.pagination"
           row-key="id"
           @request="refreshData"
           :filter="filter"
           title="Categoria de erros"
           >
           <template v-slot:top-right>
             <q-btn color="black" outline  label="adicionar" @click="actNewDialog" class="q-mr-sm" />
            <q-input outlined dense debounce="300" v-model="filter" placeholder="Procurar" clearable>
              <template v-slot:append>
                <q-checkbox left-label v-model="dataset.showall" dense >
                  <q-tooltip :delay="700">
                    <div class="text-weight-bold q-mb-md">Ignorar registro bloqueado:</div>
                    <div>Marque essa opção para exibir todos os cadastros. Ativos ou inativos.</div>
                    <div>Desmarque essa opção para exibir somente cadastro ativos.</div>
                  </q-tooltip>
                </q-checkbox>
                <q-btn icon="sync" flat round dense @click="refreshData(null)" />
              </template>
            </q-input>
          </template>
            <template v-slot:body-cell-action="props">
              <q-td :props="props"  :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '8px'" icon="edit" @click="actEditDialog(props.pageIndex)"/>
                <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '8px'" icon="delete_forever" @click="actDelete(props.row)" v-if="permitedelete ? permitedelete.ok : false"/>
              </q-td>
            </template>
            <template v-slot:body-cell-descricao="props">
              <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                <div>
                  <q-icon name="block" size="16px" color="red" v-if="!props.row.ativo" class="q-mr-sm" />
                  {{ props.row.descricao }}
                  <q-tooltip :delay="500" v-if="!props.row.ativo">
                    <div>Cadastro inativo</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-tipo="props">
              <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                {{props.row.tipo}}
              </q-td>
            </template>
            <template v-slot:body-cell-updated_at="props">
              <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                <div class="ellipsis" >
                  {{ props.row.updated_at ? $helpers.datetimeToBR(props.row.updated_at, true, true) : '-' }}
                  <q-tooltip>
                    <div v-if="props.row.updated_at">{{ $helpers.datetimeToBR(props.row.updated_at) }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-updated_usuario="props">
              <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                <div class="ellipsis" style="max-width: 130px">
                  {{ props.row.updated_usuario ? props.row.updated_usuario.nome : '-' }}
                  <q-tooltip>
                    <div v-if="props.row.updated_usuario ">Usuário: {{ props.row.updated_usuario.nome }}</div>
                    <div v-if="props.row.updated_at">{{ props.row.updated_at }}</div>
                  </q-tooltip>
                </div>
              </q-td>
            </template>
        </q-table>
      </div>
    </div>
  </q-page>
</div>
</template>

<script>
import FollowupErros from 'src/mvc/collections/followuperros.js'
import FollowupErro from 'src/mvc/models/followuperro.js'
import breadcrumbs from 'src/components/breadcrumbs'
export default {
  name: 'followup.cadastro.erros.consulta',
  components: {
    breadcrumbs
  },
  data () {
    var dataset = new FollowupErros()
    return {
      dataset,
      permitedelete: null,
      rows: [],
      filter: '',
      breadcrumbslist: [
        { to: { name: 'followup.consulta' }, label: 'Follow Up' }
      ],
      columns: [
        { name: 'action', style: 'width: 30px', align: 'left', label: '', field: 'id', sortable: false },
        { name: 'id', style: 'width: 30px', align: 'center', label: 'ID', field: 'id', sortable: false },
        { name: 'descricao', align: 'left', label: 'Descrição', field: 'descricao', sortable: false },
        { name: 'tipo', style: 'max-width: 120px', align: 'left', label: 'Tipo', field: 'tipo', sortable: false },
        { name: 'updated_at', style: 'max-width: 180px', align: 'left', label: 'Última alteração', field: 'updated_at' },
        { name: 'updated_usuario', style: 'max-width: 180px', align: 'left', label: 'Usuário', field: 'updated_usuario' }
      ],
      loading: false,
      posting: false,
      msgError: ''
    }
  },
  async mounted () {
    var app = this
    this.permitedelete = this.$helpers.permite('followup.consulta')
    await app.refreshData(null)
  },

  methods: {
    async actEditDialog (rowIndex) {
      var app = this
      var row = app.rows[rowIndex]
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) app.refreshData(null)
    },
    async actNewDialog () {
      var app = this
      var row = new FollowupErro()
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) app.refreshData(null)
    },
    async actDelete (pRegiao) {
      var app = this
      // app.deleting = true
      var ret = pRegiao.deleteWithQuestion(app)
      await ret.then(function (value) {
        if (value.ok) {
          app.$q.notify({
            message: 'Registro excluído',
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
    async refreshData (props) {
      var app = this
      app.loading = true
      app.msgError = ''
      app.dataset.readPropsTable(props)
      var ret = await app.dataset.fetch()
      if (ret.ok) {
        app.rows = app.dataset.itens
      } else {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
      app.loading = false
    }
  }
}
</script>

<style lang="stylus">
</style>
