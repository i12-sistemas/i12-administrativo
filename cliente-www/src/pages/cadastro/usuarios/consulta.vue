<template>
<div>
  <q-page class="">
    <div v-if="!$q.platform.is.mobile">
      <breadcrumbs v-model="breadcrumbslist" :showactive="{ label: 'Usuários' }"/>
    </div>
    <div class="row q-ma-lg" >
      <div class="col-sm-12">
        <q-table :data="rows" :columns="columns" dense
          :loading="loading"
           :pagination.sync="dataset.pagination"
           row-key="id"
           @request="refreshData"
           :filter="filter"
           title="Usuários"
           >
           <template v-slot:top-right>
             <q-btn color="black" outline  label="adicionar" :to="{ name: 'cadastro.usuario.add' }" class="q-mr-sm" />
            <q-input outlined dense debounce="300" v-model="filter" placeholder="Procurar">
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </template>
            <template v-slot:body-cell-action="props">
              <q-td :props="props">
                <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '8px'" icon="edit" :to="{ name: 'cadastro.usuario.edit', params: { id: props.row.id } }" />
                <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '8px'" icon="menu" class="q-ml-sm" >
                  <q-menu>
                    <q-list dense>
                      <q-item tag="label" dense @click="actDelete(props.row)">
                        <q-item-section avatar>
                          <q-icon name="delete_forever" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Remover {{ props.row.nome }}</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-menu>
                </q-btn>
              </q-td>
            </template>
            <template v-slot:body-cell-nome="props">
              <q-td :props="props">
                <div>
                  {{ props.row.nome }}
                  <q-badge v-if="!props.row.ativo" class="q-ml-md" color="grey-3" text-color="red" label="Inativo" />
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-unidadeprincipal="props">
              <q-td :props="props">
                <div v-if="props.row.unidadeprincipal ? props.row.unidadeprincipal.id > 0 : false" class="text-caption ellipsis" style="max-width: 200px">
                  <span v-if="props.row.unidadeprincipal.endereco" class="text-weight-bold q-mr-xs">{{ props.row.unidadeprincipal.endereco.cidade.cidade }}</span>
                  <span class="q-mr-xs text-grey-7">({{ props.row.unidadeprincipal.id }})</span>
                  <span>{{ props.row.unidadeprincipal.fantasia }}</span>
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
import Usuarios from 'src/mvc/collections/usuarios.js'
import breadcrumbs from 'src/components/breadcrumbs'
export default {
  name: 'cadastro.usuarios',
  components: {
    breadcrumbs
  },
  data () {
    var dataset = new Usuarios()
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
        { name: 'nome', align: 'left', label: 'Nome', field: 'nome', sortable: true },
        { name: 'email', align: 'left', label: 'E-mail', field: 'email', sortable: true },
        { name: 'unidadeprincipal', align: 'left', label: 'Unidade Principal', field: 'unidadeprincipal' },
        { name: 'login', style: 'max-width: 120px', align: 'left', label: 'Login', field: 'login' }
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
            message: 'Usuário excluído',
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
