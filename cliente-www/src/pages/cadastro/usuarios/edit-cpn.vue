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
          <q-btn label="Voltar para consultas"  outline  :to="{ name: 'cadastro.clientes' }" />
      </div>
    </q-card-section>
    <q-card-section horizontal v-if="!loading && (adding || (!adding && dataset.id > 0)) && (mode == 'integrado')" >
        <div class="text-h6 q-py-sm q-px-md">Usuário</div>
        <q-space />
        <q-btn color="negative" icon="delete_forever" flat :label="$q.platform.is.mobile ? '' : 'Excluir'" stretch @click="actDelete" v-if="!loading && !adding" />
    </q-card-section>
    <q-separator class="q-pa-0 q-ma-0" v-if="!loading && (adding || (!adding && dataset.id > 0)) && (mode == 'integrado')" />
    <q-card-section v-if="!loading && (adding || (!adding && dataset.id > 0))" :class="$q.platform.is.mobile ? 'no-padding' : ''">
      <q-form @submit="actSave"   >
        <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
          <div class="col-12">
            <q-input outlined v-model="dataset.nome" label="Nome" stack-label maxlength="60" type="text" :dense="!$q.platform.is.mobile" :readonly="edicaobloqueada" />
          </div>
          <div class="col-12">
            <q-input outlined v-model="dataset.login" label="Login de acesso" stack-label maxlength="255" :dense="!$q.platform.is.mobile" :readonly="edicaobloqueada" />
          </div>
          <div class="col-12">
            <q-input outlined v-model="dataset.email" label="E-mail" stack-label maxlength="255" type="mail" :dense="!$q.platform.is.mobile" :readonly="edicaobloqueada" />
          </div>
          <div class="col-12">
            unidade
            <q-input outlined v-model="dataset.defaulturl" label="Url padrão - redireciona ao logar" stack-label maxlength="5000" :dense="!$q.platform.is.mobile" :readonly="edicaobloqueada" />
          </div>
          <div class="col-12">
            <q-checkbox v-model="dataset.ativo" label="Ativo" dense  />
          </div>
        </div>
        <div class="full-width">
          <q-card class="my-card full-width" flat bordered>
            <q-card-section>
              <div class="fit row wrap full-width q-col-gutter-sm">
                <div class="col-12 q-mt-sm" v-if="dataset.unidades ? dataset.unidades.length > 0 :false">
                  <q-table :data="dataset.unidades" :columns="columnsundiades" :loading="loading" :rows-per-page-options="[0]" row-key="id" flat bordered dense >
                    <template v-slot:body-cell-action="props">
                      <q-td :props="props">
                        <q-btn flat round dense :size="$q.platform.is.mobile ? '12px' : '10px'" :icon="props.row.deleted ? 'undo' : 'clear'" @click="actUnidadeDeleteRestore(props.row)"/>
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
                    <template v-slot:body-cell-unidade="props">
                      <q-td :props="props">
                        <div>
                          {{ props.row.unidade.fantasia }}
                          <q-chip v-if="!props.row.unidade.ativo" class="q-ml-md" color="grey-3" text-color="red" label="Inativo" icon="block" size="12px" />
                          <q-chip v-if="props.row.deleted" class="q-ml-md" color="red" text-color="white" label="Marcado p/ remover" icon="clear" size="10px"
                            outline @click="actUnidadeDeleteRestore(props.row)" clickable >
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
                <div class="col-12 q-mt-sm text-center text-caption" v-else>
                  Nenhuma unidade definida!
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <q-separator spaced  />
        <div >
          <q-btn label="Salvar" type="submit" color="primary" :disable="loading || saving" :loading="saving"/>
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
import Usuario from 'src/mvc/models/usuario.js'
import logusuario from 'src/components/cpn-logusuario'
export default {
  name: 'usuario.edit.cpn',
  components: {
    logusuario
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
    var dataset = new Usuario()
    return {
      showadd: false,
      saving: false,
      unidadeadd: null,
      dataset,
      breadcrumbslist: [
        { to: { name: 'cadastros' }, label: 'Cadastro' },
        { to: { name: 'cadastro.usuarios' }, label: 'Usuários' }
      ],
      columnsundiades: [
        { name: 'action', style: 'width: 30px', align: 'left', label: '', field: 'id', sortable: false },
        { name: 'unidade', align: 'left', label: 'Unidade', field: 'unidade', sortable: true },
        { name: 'createdat', style: 'width: 220px', align: 'left', label: 'Adicionado por', field: 'created_at', sortable: true },
        { name: 'id', style: 'width: 30px', align: 'center', label: 'ID', field: 'id', sortable: true }
      ],
      loading: false,
      deleting: false,
      posting: false,
      msgError: ''
    }
  },
  async mounted () {
    await this.init()
  },
  computed: {
    edicaobloqueada () {
      return (this.dataset ? !this.dataset.ativo : true)
    }
  },
  methods: {
    actUnidadeDeleteRestore (pRow) {
      var app = this
      app.loading = true
      var idx = -1
      for (let index = 0; index < app.dataset.unidades.length; index++) {
        const element = app.dataset.unidades[index]
        if (element.unidade.id === pRow.unidade.id) {
          idx = index
          break
        }
      }

      if (idx >= 0) {
        var lItem = app.dataset.unidades[idx]
        app.loading = true
        if (lItem.deleted) {
          lItem.deleted = false
        } else {
          lItem.deleted = true
          if (lItem.new) app.dataset.unidades.splice(idx, 1)
        }
      }

      app.dataset._unidadesedit = true
      app.$nextTick(() => {
        app.loading = false
      })
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
        }
        if (app.adding) {
          if (app.mode === 'integrado') {
            app.$nextTick(() => {
              app.loading = true
              app.$router.push({ name: 'cadastro.usuario.edit', params: { id: app.dataset.id } })
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
            message: 'Usuário excluído',
            color: 'positive',
            caption: value.msg,
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
          app.$nextTick(() => {
            app.$router.push({ name: 'cadastro.usuarios' })
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
