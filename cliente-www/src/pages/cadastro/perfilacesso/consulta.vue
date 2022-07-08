<template>
<div>
  <q-page class="">
    <div v-if="!$q.platform.is.mobile">
      <breadcrumbs v-model="breadcrumbslist" :showactive="{ label: 'Perfil de acesso' }"/>
    </div>
    <div class="row q-ma-lg" >
      <div class="col-sm-12">
        <q-table :data="rows" :columns="columns" dense
          :loading="loading"
           :pagination.sync="dataset.pagination"
           row-key="id"
           @request="refreshData"
           :filter="filter"
           title="Perfil de acesso"
           >
           <template v-slot:top-right>
             <q-btn color="black" outline  label="adicionar" :to="{ name: 'cadastro.perfilacesso.add' }" class="q-mr-sm" />
            <q-input outlined dense debounce="300" v-model="filter" placeholder="Procurar">
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </template>
            <template v-slot:body-cell-action="props">
              <q-td :props="props">
                <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '8px'" icon="edit" :to="{ name: 'cadastro.perfilacesso.edit', params: { id: props.row.id } }" />
                <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '8px'" icon="menu" class="q-ml-sm" >
                  <q-menu>
                    <q-list dense>
                      <q-item tag="label" dense @click="actDelete(props.row)">
                        <q-item-section avatar>
                          <q-icon name="delete_forever" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Remover</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </q-btn>
              </q-td>
            </template>
            <template v-slot:body-cell-descricao="props">
              <q-td :props="props">
                <div>
                  {{ props.row.descricao }}
                  <q-badge v-if="!props.row.ativo" class="q-ml-md" color="grey-3" text-color="red" label="Inativo" />
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
import PerfisAcesso from 'src/mvc/collections/perfisacesso.js'
import breadcrumbs from 'src/components/breadcrumbs'
export default {
  name: 'cadastro.usuarios',
  components: {
    breadcrumbs
  },
  data () {
    var dataset = new PerfisAcesso()
    return {
      dataset,
      rows: [],
      filter: '',
      breadcrumbslist: [
        { to: { name: 'cadastros' }, label: 'Cadastro' }
      ],
      columns: [
        { name: 'action', style: 'width: 30px', align: 'left', label: '', field: 'id', sortable: false },
        { name: 'id', style: 'width: 30px', align: 'center', label: 'ID', field: 'id', sortable: false },
        { name: 'descricao', align: 'left', label: 'Descrição', field: 'descricao', sortable: true },
        { name: 'permissaocount', align: 'center', label: 'Qtde Permissão', field: 'permissaocount', sortable: true },
        { name: 'usuariocount', align: 'center', label: 'Qtde Usuário', field: 'usuariocount' }
      ],
      loading: false,
      posting: false,
      msgError: ''
    }
  },
  async mounted () {
    var app = this
    await app.refreshData(null)
  },

  methods: {
    async actDelete (pRegiao) {
      var app = this
      // app.deleting = true
      var ret = pRegiao.deleteWithQuestion(app)
      await ret.then(function (value) {
        if (value.ok) {
          app.$q.notify({
            message: 'Perfil excluído',
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
