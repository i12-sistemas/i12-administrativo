<template>
<div>
  <q-card :bordered="mode == 'integrado'" flat :class="$q.platform.is.mobile ? 'q-pa-0' : ''">
    <q-card-section v-if="loading || saving" style="padding: 0">
        <q-linear-progress indeterminate />
        <div class="text-primary text-center q-pa-md" v-if="loading">Carregando...</div>
    </q-card-section>
    <q-card-section  horizontal v-if="!loading && (!adding && !dataset.id)" >
      <div class="full-width text-center q-pa-lg">
          <div class="text-h6 q-pa-md">Nenhum registro encontrado!</div>
          <q-btn label="Voltar para consultas"  outline  :to="{ name: 'cadastro.perfilacesso' }" />
      </div>
    </q-card-section>
    <q-card-section horizontal v-if="!loading && (adding || (!adding && dataset.id > 0)) && (mode == 'integrado')" >
        <q-tabs v-model="tab" inline-label class="bg-grey-2 text-primary" align="left" >
          <q-tab name="perfil" icon="fas fa-shield-alt" label="Perfil" />
          <q-tab name="permissoes" icon="list" label="Permissões" v-if="permiteconfigusuariosliberarpermissao ? permiteconfigusuariosliberarpermissao.ok : false" />
          <q-tab name="usuarios" :icon="loadingusuarios ? '' : 'fas fa-user-shield'" label="Usuários" v-if="permiteconfigusuariosliberarpermissao ? permiteconfigusuariosliberarpermissao.ok : false" >
            <q-spinner color="accent" :thickness="5" size="20px" v-if="loadingusuarios" class="q-ml-md" />
          </q-tab>
        </q-tabs>
        <q-space />
        <q-btn color="primary" flat @click="actSave" icon="done" unelevated :label="$q.platform.is.mobile ? '' : 'Salvar'" stretch :disable="loading || saving" :loading="saving" v-if="permiteconfigperfilacessosave ? permiteconfigperfilacessosave.ok : false"/>
        <q-separator spaced inset vertical  />
        <q-btn color="negative" icon="delete_forever" flat :label="$q.platform.is.mobile ? '' : 'Excluir'" stretch @click="actDelete" v-if="!loading && !adding && (permiteconfigperfilacessodelete ? permiteconfigperfilacessodelete.ok : false)" />
    </q-card-section>
    <q-separator class="q-pa-0 q-ma-0" v-if="!loading && (adding || (!adding && dataset.id > 0)) && (mode == 'integrado')" />
    <q-card-section v-if="!loading && (adding || (!adding && dataset.id > 0))" :class="$q.platform.is.mobile ? 'no-padding' : ''">
      <q-form @submit="actSave"   >
        <q-tab-panels v-model="tab" animated @before-transition="onbeforetransition">
          <q-tab-panel name="perfil">
            <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
              <div class="col-12">
                <q-input outlined v-model="dataset.descricao" label="Descrição" stack-label maxlength="100" type="text" :dense="!$q.platform.is.mobile" :readonly="edicaobloqueada" :disable="permiteconfigperfilacessosave ? !permiteconfigperfilacessosave.ok : true" />
              </div>
              <div class="col-12">
                <q-checkbox v-model="dataset.ativo" label="Ativo" dense :disable="permiteconfigperfilacessosave ? !permiteconfigperfilacessosave.ok : true" />
              </div>
            </div>
          </q-tab-panel>
          <q-tab-panel name="usuarios" v-if="permiteconfigusuariosliberarpermissao ? permiteconfigusuariosliberarpermissao.ok : false">
            <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
              <div class="col-md-6 col-sm-12" >
                <selectusuario outlined label="Adicionar usuário" nullabled :dense="!$q.platform.is.mobile" v-model="usuarioadd" :readonly="loadingusuarios || !dataset._usuariosfechted" />
              </div>
              <div class="col-md-6 col-sm-12" >
                <q-btn icon="add" label="adicionar" :loading="loadingusuarios" @click="actUsuarioAdd" class="q-mr-sm" color="primary" flat
                  :disable="loadingusuarios || !dataset._usuariosfechted || (usuarioadd ? !(usuarioadd.id > 0) : false) " />
                <q-btn color="primary" icon="sync" flat :loading="loadingusuarios" @click="onRefreshUsuarios" class="q-mr-sm"   />
              </div>
              <div class="col-12">
                <q-table :data="dataset.usuarios" :columns="columnsusuarios" dense flat
                :loading="loadingusuarios"
                :rows-per-page-options="[0]"
                row-key="id"
                >
                  <template v-slot:body-cell-action="props">
                    <q-td :props="props">
                      <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '10px'" :icon="props.row.deleted ? 'undo' : 'clear'" @click="actUsuarioDeleteRestore(props.row)" v-if="permiteconfigusuariosliberarpermissao ? permiteconfigusuariosliberarpermissao.ok : false"/>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-createdat="props">
                    <q-td :props="props">
                      <div v-if="props.row.pivot_created_usuario && !props.row.new">
                        {{ props.row.pivot_created_usuario.nome }} - {{ $helpers.datetimeRelativeToday(props.row.pivot_created_at) }}
                        <q-tooltip>
                          <div>Adicionado por  {{ props.row.pivot_created_usuario.nome }}</div>
                          <div>em  {{ $helpers.datetimeToBR(props.row.pivot_created_at) }}</div>
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
                  <template v-slot:body-cell-nome="props">
                    <q-td :props="props">
                      <div>
                        {{ props.row.nome }}
                        <q-chip v-if="!props.row.ativo" class="q-ml-md" color="grey-3" text-color="red" label="Inativo" icon="block" size="12px" />
                        <q-chip v-if="props.row.deleted && (permiteconfigusuariosliberarpermissao ? permiteconfigusuariosliberarpermissao.ok : false)" class="q-ml-md" color="red" text-color="white" label="Marcado p/ remover" icon="clear" size="10px"
                          outline @click="actUsuarioDeleteRestore(props.row)" clickable >
                          <q-tooltip>
                            <div>Registro marcado para excluir após salvar</div>
                            <div>Clique aqui para desmarcar</div>
                          </q-tooltip>
                        </q-chip>
                      </div>
                    </q-td>
                  </template>
              </q-table>
              </div>
            </div>
          </q-tab-panel>
          <q-tab-panel name="permissoes" v-if="permiteconfigusuariosliberarpermissao ? permiteconfigusuariosliberarpermissao.ok : false">
            <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
              <div class="col-md-8 col-sm-12">
                <q-input ref="txtfilterpermissao" outlined v-model="filterpermissao" label="Filtrar permissão" stack-label :dense="!$q.platform.is.mobile"  debounce="300" >
                  <template v-slot:append>
                    <q-icon v-if="filterpermissao !== ''" name="clear" class="cursor-pointer" @click="actResetFilterPermissao" />
                  </template>
                </q-input>
              </div>
              <div class="col-md-4 col-sm-12">
              </div>
              <div class="col-12" v-if="permissoes.itens && !loading">
                {{filterpermissao}}
                <q-tree :nodes="permissoes.itens" node-key="id"
                :dense="!$q.platform.is.mobile"
                  default-expand-all
                  :disabled="permiteconfigusuariosliberarpermissao ? !permiteconfigusuariosliberarpermissao.ok : true"
                  :ticked.sync="dataset.permissoes" tick-strategy="leaf"
                  :filter="filterpermissao" :filter-method="onFilterPermissao"
                >
                  <template v-slot:default-header="prop">
                    <div class="row items-center">
                      {{ prop.node.label }}
                      <q-tooltip :delay="600">
                        <div v-if="prop.node.body">{{ prop.node.body }}</div>
                        <div>{{ prop.node.id }}</div>
                      </q-tooltip>
                    </div>
                  </template>
                </q-tree>
              </div>
            </div>
          </q-tab-panel>
        </q-tab-panels>
        <q-separator spaced  />
        <div >
          <q-btn label="Salvar" type="submit" color="primary" :disable="loading || saving" :loading="saving" v-if="permiteconfigperfilacessosave ? permiteconfigperfilacessosave.ok : false"/>
          <q-btn label="Voltar" color="primary" flat class="q-ml-sm" @click="$router.go(-1)" v-if="mode == 'integrado'" />
          <q-btn label="Fechar" color="primary" flat class="q-ml-sm" @click="actClose" v-if="mode == 'dialog'" />
        </div>
        <logusuario v-if="!loading && !adding" :createdat="dataset.created_at" :createdusuario="dataset.created_usuario"
          :updatedat="dataset.updated_at" :updatedusuario="dataset.updated_usuario" />
      </q-form>
    </q-card-section>
  </q-card>
</div>
</template>

<script>
import Permissoes from 'src/mvc/collections/permissoes.js'
import PerfilAcesso from 'src/mvc/models/perfilacesso.js'
import Usuario from 'src/mvc/models/usuario.js'
import logusuario from 'src/components/cpn-logusuario'
import selectusuario from 'src/components/cnp-select-usuario.vue'

export default {
  name: 'perfilacesso.edit.cpn',
  components: {
    logusuario,
    selectusuario
  },
  props: {
    mode: {
      type: String,
      default: function () {
        return 'integrado' // ou dialog
      }
    },
    adding: {
      type: Boolean,
      default: function () {
        return true
      }
    },
    idstart: {
      type: Number,
      default: function () {
        return null
      }
    }
  },
  data () {
    var usuarioadd = new Usuario()
    var permissoes = new Permissoes()
    var dataset = new PerfilAcesso()
    return {
      showadd: false,
      saving: false,
      dataset,
      usuarioadd,
      tab: 'perfil',
      filterpermissao: '',
      permissoes,
      columnsusuarios: [
        { name: 'action', style: 'width: 30px', align: 'left', label: '', field: 'id', sortable: false },
        { name: 'id', style: 'width: 30px', align: 'center', label: 'ID', field: 'id', sortable: true },
        { name: 'nome', align: 'left', label: 'Nome', field: 'nome', sortable: true },
        { name: 'createdat', style: 'width: 220px', align: 'left', label: 'Adicionado por', field: 'created_at', sortable: true }
      ],
      breadcrumbslist: [
        { to: { name: 'cadastros' }, label: 'Cadastro' },
        { to: { name: 'cadastro.perfilacesso' }, label: 'Perfis de Acesso' }
      ],
      loading: false,
      loadingusuarios: false,
      deleting: false,
      posting: false,
      msgError: '',
      permiteconfigperfilacessosave: null,
      permiteconfigperfilacessodelete: null,
      permiteconfigusuariosliberarpermissao: null
    }
  },
  async mounted () {
    this.permiteconfigperfilacessosave = this.$helpers.permite('config.perfilacesso.save')
    this.permiteconfigperfilacessodelete = this.$helpers.permite('config.perfilacesso.delete')
    this.permiteconfigusuariosliberarpermissao = this.$helpers.permite('config.usuarios.liberarpermissao')
    await this.init()
  },
  computed: {
    edicaobloqueada () {
      if (this.permiteconfigperfilacessosave) {
        if (!this.permiteconfigperfilacessosave.ok) return true
      }
      return (this.dataset ? !this.dataset.ativo : true)
    }
  },
  methods: {
    actUsuarioDeleteRestore (pRow) {
      var app = this
      app.loadingusuarios = true
      var idx = -1
      for (let index = 0; index < app.dataset.usuarios.length; index++) {
        const element = app.dataset.usuarios[index]
        if (element.id === pRow.id) {
          idx = index
          break
        }
      }

      if (idx >= 0) {
        var lItem = app.dataset.usuarios[idx]
        app.loadingitens = true
        if (lItem.deleted) {
          lItem.deleted = false
        } else {
          lItem.deleted = true
          if (lItem.new) app.dataset.usuarios.splice(idx, 1)
        }
      }

      app.dataset._usuariosedit = true
      app.$nextTick(() => {
        app.loadingusuarios = false
      })
    },
    actUsuarioAdd () {
      var app = this
      if (!app.usuarioadd) return
      if (!(app.usuarioadd.id > 0)) return
      if (!app.dataset.usuarios) app.dataset.usuarios = []
      var idx = -1
      for (let index = 0; index < app.dataset.usuarios.length; index++) {
        const element = app.dataset.usuarios[index]
        if (app.usuarioadd.id === element.id) {
          idx = index
          break
        }
      }
      if (idx < 0) {
        var lUsuario = new Usuario(app.usuarioadd)
        lUsuario.new = true
        app.dataset.usuarios.push(lUsuario)
        app.dataset._usuariosedit = true
      } else {
        app.$q.notify({
          message: 'Usuário já está na listagem!',
          color: 'accent',
          actions: [
            { label: 'OK', color: 'white', handler: () => { /* ... */ } }
          ]
        })
      }
      app.usuarioadd.limpardados()
    },
    onbeforetransition (newVal, oldVal) {
      var app = this
      if (newVal === 'usuarios') {
        if (!app.dataset._usuariosfechted) app.onRefreshUsuarios()
      }
    },
    onRefreshUsuarios () {
      var app = this
      app.loadingusuarios = true
      var ret = app.dataset.fetchUsuarios()
      ret.then(function (value) {
        app.loadingusuarios = false
        if ((!value.ok) && (value.msg)) {
          if (value.msg !== '') app.$helpers.showDialog(value)
        }
      })
    },
    onFilterPermissao (node, filter) {
      const filt = filter.toLowerCase()
      var i = (node.label && node.label.toLowerCase().indexOf(filt) > -1) || (node.body && node.body.toLowerCase().indexOf(filt) > -1) || (node.id && node.id.toLowerCase().indexOf(filt) > -1)
      return i
    },
    actResetFilterPermissao () {
      this.filterpermissao = ''
      this.$refs.txtfilterpermissao.focus()
    },
    async init () {
      var app = this
      // app.adding = (this.$route.name === 'cadastro.clientes.add')
      if (app.adding) {
        app.loading = true
        app.dataset.limpardados()
      } else {
        app.loading = true
        await app.refreshData()
      }
      await app.refreshDataPermissoes()
      app.loading = false
      app.$emit('updated', app.dataset)
    },
    async actSave () {
      var app = this
      try {
        app.saving = true
        // var params = await app.dataset.parametros()
        // if (!params) throw new Error('Nenhuma alteração')
      } catch (error) {
        app.$helpers.showDialog({ ok: false, msg: error.message })
        app.saving = false
        return
      }
      var ret = await app.dataset.save()
      if (ret.ok) {
        app.$q.notify({
          message: 'Cadastro salvo!',
          color: 'positive',
          actions: [
            { label: 'OK', color: 'white', handler: () => { /* ... */ } }
          ]
        })
        if (app.mode === 'dialog') {
          app.$emit('updated', app.dataset)
          app.$emit('close')
        } else {
          if (app.tab === 'usuarios') app.onRefreshUsuarios()
        }
        if (app.adding) {
          if (app.mode === 'integrado') {
            app.$nextTick(() => {
              app.loading = true
              app.$router.push({ name: 'cadastro.perfilacesso.edit', params: { id: app.dataset.id } })
              app.$emit('updated', app.dataset)
              app.loading = false
            })
          }
        }
      } else {
        app.$helpers.showDialog(ret, true)
      }
      app.saving = false
    },
    async actDelete () {
      var app = this
      app.deleting = true
      var ret = app.dataset.deleteWithQuestion(app)
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
          app.$nextTick(() => {
            app.$router.push({ name: 'cadastro.perfilacesso' })
          })
        } else {
          if (value.msg) {
            if (value.msg !== '') app.$helpers.showDialog(value)
          }
        }
        app.deleting = false
      })
    },
    actClose () {
      this.$emit('close', false)
    },
    async refreshDataPermissoes () {
      var app = this
      var ret = await app.permissoes.fetchAll()
      if (!ret.ok) {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
    },
    async refreshData () {
      var app = this
      app.loading = true
      app.msgError = ''
      var ret = await app.dataset.find(app.idstart)
      if (ret.ok) {
      } else {
        app.loading = false
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
      app.loading = false
    }
  }
}
</script>
