<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-70'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">Usuário Painel do Cliente :: {{ (!loading ? (dataset ? dataset.nome_old +  ' [id: ' + dataset.id + ']' : '?') : 'Consultando')}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>
          Usuário Painel do Cliente :: {{ (!loading ? (dataset ? dataset.nome_old +  ' [id: ' + dataset.id + ']' : '?') : 'Consultando')}}
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section v-if="loading" style="padding: 0">
        <div class="fit row wrap text-center q-mt-lg q-pa-lg" v-if="loading">
          <div class="col-12">
            <q-spinner color="accent" size="3rem" :thickness="5"/>
          </div>
          <div class="col-12 q-pa-lg text-accent ">
            Consultando dados, aguarde!
          </div>
        </div>
      </q-card-section>
      <q-card-section  horizontal v-if="!loading && (!dataset.id)" >
        <div class="full-width text-center q-pa-lg">
          <div class="text-h6 q-pa-md">Nenhum registro encontrado!</div>
          <q-btn label="Voltar para consultas"  outline  :to="{ name: 'cadastro.clientes' }" />
        </div>
      </q-card-section>
      <q-card-section v-if="!loading && (dataset ? dataset.id > 0 : false)">
        <div class="row full-width q-col-gutter-sm">
          <div class="col-xs-12 col-md-10">
            <q-field label="Nome" outlined stack-label :dense="!$q.platform.is.mobile">
              <template v-slot:control>
                <div class="self-center full-width no-outline" tabindex="0">{{dataset.nome}}</div>
              </template>
            </q-field>
          </div>
          <div class="col-xs-12 col-md-2">
            <q-field label="Status" outlined stack-label :dense="!$q.platform.is.mobile"
                     :label-color="dataset.ativo ? 'black' : 'white'" :bg-color="dataset.ativo ? 'white' : 'red'"  >
              <template v-slot:control>
                <div class="self-center full-width no-outline" :class="dataset.ativo ? 'text-black' : 'text-white'"  tabindex="0">{{dataset.ativo ? 'Ativo' : 'Bloqueado'}}</div>
              </template>
            </q-field>
          </div>
          <div class="col-xs-12 col-md-5">
            <q-field label="Celular" outlined stack-label :dense="!$q.platform.is.mobile">
              <template v-slot:control>
                <div class="self-center full-width no-outline" tabindex="0">{{dataset.celular}}</div>
              </template>
            </q-field>
          </div>
          <div class="col-xs-12 col-md-7">
            <q-field label="E-mail" outlined stack-label :dense="!$q.platform.is.mobile">
              <template v-slot:control>
                <div class="self-center full-width no-outline" tabindex="0">{{dataset.email}}</div>
              </template>
            </q-field>
          </div>
          <div class="col-12">
            <q-card class="my-card" bordered flat>
              <q-card-section class="q-pa-none q-ma-none">
                <q-toolbar class="bg-grey-3 text-black">
                  <q-toolbar-title class="text-body">
                    <div>Permissões</div>
                  </q-toolbar-title>
                </q-toolbar>
              </q-card-section>
              <q-card-section>
                <div class="row full-width q-col-gutter-sm">
                  <div class="col-xs-12 col-md-4">
                    <div class="text-caption">Permite visualizar painel do FUP</div>
                    <q-option-group v-model="dataset.pemitefup"  type="radio"
                                    :options="[{ label: 'Liberado', value: true, color: 'blue' }, { label: 'Bloqueado', value: false, color: 'red' }]" inline />
                  </div>
                  <div class="col-xs-12 col-md-4">
                    <div class="text-caption">Permite visualizar status geral</div>
                    <q-option-group v-model="dataset.permitestatusgeral"  type="radio"
                                    :options="[{ label: 'Liberado', value: true, color: 'blue' }, { label: 'Bloqueado', value: false, color: 'red' }]" inline />
                  </div>
                  <div class="col-xs-12 col-md-4">
                    <div class="text-caption">Permite visualizar coletas e entregas</div>
                    <q-option-group v-model="dataset.permitecoletasver" type="radio"
                                    :options="[{ label: 'Liberado', value: true, color: 'blue' }, { label: 'Bloqueado', value: false, color: 'red' }]" inline />
                  </div>
                </div>
              </q-card-section>
              <q-card-section>
                <q-btn unelevated color="grey-3" text-color="grey-9" icon="security" label="Aplicar permissão" @click="actSavePermissao" />
              </q-card-section>
            </q-card>
          </div>
          <div class="col-12">
            <q-card class="my-card" bordered flat>
              <q-card-section class="q-pa-none q-ma-none">
                <q-toolbar class="bg-grey-3 text-black">
                  <q-toolbar-title class="text-body">
                    <div>Cliente associados</div>
                  </q-toolbar-title>
                  <q-btn flat icon="add" label="Associar cliente" @click="actClienteAdd"/>
                </q-toolbar>
              </q-card-section>
              <q-card-section class="text-center " v-if="dataset.clientes ? dataset.clientes.length === 0 : true">
                <div class="q-pa-lg">Nenhum cliente associado a este usuário</div>
                <div class="q-mt-md">
                  <q-btn unelevated color="grey-3" text-color="grey-9" icon="add" label="Associar cliente" @click="actClienteAdd" size="md"/>
                </div>
              </q-card-section>
              <q-card-section class="q-pa-none q-ma-none" v-if="dataset.clientes ? dataset.clientes.length > 0 : false">
                <q-table flat :data="dataset.clientes" :columns="columns" row-key="id"  :dense="!$q.platform.is.mobile" class="full-width" :rows-per-page-options="[0]">
                  <template v-slot:body-cell-action="props">
                    <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                      <q-btn unelevated color="grey-3" text-color="red" round dense :size="$q.platform.is.mobile ? '12px' : '8px'" icon="delete"  @click="actClienteRemove(props.row)" >
                        <q-tooltip :delay="500">Excluir acesso do usuário para empresa {{props.row.razaosocial}}</q-tooltip>
                      </q-btn>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-razaosocial="props">
                    <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                      <div>
                        <q-icon name="block" color="red" size="16px" v-if="!props.row.ativo" />
                        <div>{{props.row.razaosocial }}</div>
                        <div class="text-caption" v-if="(props.row.razaosocial !== props.row.fantasia) && (props.row.fantasia !== '')">{{props.row.fantasia }}</div>
                      </div>
                      <q-tooltip :delay="700">
                        <div>{{props.row.razaosocial }}</div>
                        <div>{{props.row.fantasia }}</div>
                      </q-tooltip>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-ids="props">
                    <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                      <div>{{ props.row.id }}</div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-fantasia="props">
                    <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                      <div>{{ props.row.fantasia }}</div>
                      <q-tooltip :delay="700">
                        <div>FollowUp: {{props.row.fantasia_followup }}</div>
                      </q-tooltip>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-cnpj="props">
                    <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                      <div>{{ $helpers.mascaraCpfCnpj(props.row.cnpj) }}</div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-cidade="props">
                    <q-td :props="props" :class="props.row.ativo ? '' : 'bg-red-1 text-red'">
                      <div v-if="props.row.endereco.cidade ? props.row.endereco.cidade.id > 0 : false" >
                        {{ props.row.endereco.cidade.cidade + '/' + props.row.endereco.cidade.uf }}
                        <q-tooltip>
                          <div>Cidade: {{ props.row.endereco.cidade.cidade }}</div>
                          <div>Estado: {{ props.row.endereco.cidade.estado }}</div>
                        </q-tooltip>
                      </div>
                      <div v-if="props.row.endereco.cidade ? !(props.row.endereco.cidade.id > 0) : true" class="text-primary">Indefinida</div>
                    </q-td>
                  </template>
                </q-table>
              </q-card-section>
            </q-card>
          </div>
          <div class="col-12 q-mt-md">
            <q-btn unelevated label="Inativar usuário" color="red" class="q-mr-sm" icon="block" v-if="dataset.ativo && dataset.ultimoacesso" @click="actDeleteInativar" />
            <q-btn unelevated label="Reativar usuário" color="blue" icon="check_circle"  v-if="!dataset.ativo" class="q-mr-sm" @click="actReativar" />
            <q-btn unelevated label="Excluir usuário" color="negative" class="q-mr-sm" v-if="dataset.ativo && !dataset.ultimoacesso" icon="delete" @click="actDeleteInativar" />
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
  name: 'cliente.usuarios.gerenciar.edit.dialog',
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
    var dataset = new ClienteUsuario()
    return {
      loading: true,
      dataset,
      columns: [
        { name: 'action', style: 'width: 30px', align: 'left', label: '', field: 'id', sortable: false },
        { name: 'razaosocial', align: 'left', label: 'Razão Social', field: 'razaosocial', sortable: false, filterconfig: { value: null, sorted: true } },
        { name: 'cnpj', style: 'width: 120px', align: 'left', label: 'CNPJ', field: 'cnpj', sortable: false, filterconfig: { value: null, sorted: true } },
        { name: 'cidade', style: 'max-width: 120px', align: 'left', label: 'Cidade', field: 'cidade', sortable: false, filterconfig: { value: null, sorted: true } },
        { name: 'ids', style: 'width: 80px', align: 'center', label: 'id', field: 'id', sortable: false, filterconfig: { value: null, sorted: true, tooltip: 'Digite o número separado por vírgula para pesquisar mais de um. Ex.: 10,30,25,36' } }
      ]
    }
  },
  async mounted () {
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
      app.loading = true
      var ret = await app.dataset.save()
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        app.onOKClick()
      }
      app.loading = false
    },
    async actDeleteInativar () {
      var app = this
      var ret = await app.dataset.deleteWithQuestion(app)
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        await app.refreshData(true)
        if (app.dataset ? !(app.dataset.id > 0) : true) {
          app.onOKClick()
        }
      }
    },
    async actSavePermissao () {
      var app = this
      var ret = await app.dataset.savePermissao(app)
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        await app.refreshData(true)
        if (app.dataset ? !(app.dataset.id > 0) : true) {
          app.onOKClick()
        }
      }
    },
    async actReativar () {
      var app = this
      var ret = await app.dataset.reativarWithQuestion(app)
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        app.refreshData()
      }
    },
    async actClienteAdd () {
      var app = this
      var ret = await app.dataset.ShowDialogClienteAdd(app)
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        app.refreshData()
      }
    },
    async actClienteRemove (pCliente) {
      var app = this
      var ret = await app.dataset.deleteClienteAssociacaoWithQuestion(app, pCliente)
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {})
      } else {
        app.refreshData()
      }
    },
    async refreshData (pHideErro = false) {
      var app = this
      app.loading = true
      var ret = await app.dataset.find(app.usuario.id)
      if (!ret.ok) {
        if (!pHideErro) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {})
        }
      }
      app.loading = false
    },
    // following method is REQUIRED
    // (don't change its name --> "show")
    show () {
      var app = this
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
      this.$emit('ok', { ok: true, dataset: this.dataset })
      this.hide()
    }

  }
}
</script>
