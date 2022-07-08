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
        <div class="text-h6 q-py-sm q-px-md">Cliente</div>
        <q-space />
        <q-btn color="negative" icon="delete_forever" flat :label="$q.platform.is.mobile ? '' : 'Excluir'" stretch @click="actDelete" v-if="!loading && !adding && $helpers.permite('cadastros.clientes.delete').ok" />
      </q-card-section>
      <q-separator class="q-pa-0 q-ma-0" v-if="!loading && (adding || (!adding && dataset.id > 0)) && (mode == 'integrado')" />
      <q-card-section v-if="!loading && (adding || (!adding && dataset.id > 0))" :class="$q.platform.is.mobile ? 'no-padding' : ''">
        <q-form @submit="actSave" autofocus >
          <div class="row full-width q-col-gutter-sm">
            <div class="col-xs-12 col-md-10">
                  <q-input outlined class="self-center full-width no-outline" label="Nome" v-model="dataset.nome" input-class="text-uppercase" stack-label maxlength="250" :dense="!$q.platform.is.mobile" tabindex="0"  value=""/>
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
              <q-input outlined v-model="dataset.celular" input-class="text-uppercase" label="Celular" stack-label maxlength="250" :dense="!$q.platform.is.mobile" />
            </div>
            <div class="col-xs-12 col-md-7">
              <q-input outlined v-model="dataset.email" type="email" input-class="text-uppercase" label="E-mail" stack-label maxlength="250" :dense="!$q.platform.is.mobile" />
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
                      <q-option-group v-model="dataset.statusgeral"  type="radio"
                                      :options="[{ label: 'Liberado', value: true, color: 'blue' }, { label: 'Bloqueado', value: false, color: 'red' }]" inline />
                    </div>
                    <div class="col-xs-12 col-md-4">
                      <div class="text-caption">Permite visualizar coletas e entregas</div>
                      <q-option-group v-model="dataset.permitecoletasver" type="radio"
                                      :options="[{ label: 'Liberado', value: true, color: 'blue' }, { label: 'Bloqueado', value: false, color: 'red' }]" inline />
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
            <div class="col-12 q-mt-md">
              <q-btn label="Salvar" type="submit" unelevated color="primary" :disable="loading || saving" :loading="saving"/>
              <q-btn flat label="Sair" icon="clear" @click="actClose"/>
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>

    <q-dialog v-model="showemailedit" persistent transition-show="rotate" transition-hide="rotate">
      <q-card>
        <q-card-section class="bg-primary text-white q-py-0 q-px-md">
          <div class="row items-center no-wrap">
            <div class="col">
              <div class="text-h6">Dados do Usuário</div>
            </div>
            <div class="col-auto">
              <q-btn color="white" round flat icon="clear" @click="actEmailClose(null)" >
                <q-tooltip>Fechar [ESC]</q-tooltip>
              </q-btn>
            </div>
          </div>
        </q-card-section>
        <q-card-section class="q-pt-md">
          <emailedit outlined :value="emailediting" :dense="!$q.platform.is.mobile" @close="actEmailClose" />
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="showcartaocnpj" full-height :maximized="$q.platform.is.mobile"  >
      <q-card :class="$q.platform.is.mobile ? '' : 'dialogfullheightdesktop'">
        <q-card-section class="bg-primary text-white q-py-0 q-px-md">
          <div class="row items-center no-wrap">
            <div class="col">
              <div class="text-h6">Cartão de CNPJ</div>
            </div>
            <div class="col-auto">
              <q-btn color="white" round flat icon="clear" @click="showcartaocnpj=false" >
                <q-tooltip>Fechar [ESC]</q-tooltip>
              </q-btn>
            </div>
          </div>
        </q-card-section>
        <q-card-section class="q-pt-sm" >
          <div v-html="dataset.cnpjmemo" ></div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import Cliente from 'src/mvc/models/cliente.js'
import emailedit from 'src/components/cpn-email.vue'

export default {
  name: 'cliente.edit.cpn',
  components: {
    emailedit
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
    var dataset = new Cliente()
    return {
      showcartaocnpj: false,
      enderecoupdated: false,
      cnpjloading: false,
      emailediting: null,
      permitecadastrosclientessave: true,
      permiteusuariosconsultar: true,
      showemailedit: false,
      showadd: false,
      saving: false,
      dataset,
      breadcrumbslist: [
        { to: { name: 'cadastros' }, label: 'Cadastro' },
        { to: { name: 'cadastro.clientes' }, label: 'Clientes' }
      ],
      loading: false,
      deleting: false,
      posting: false,
      msgError: ''
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
    async actShowUsuarioAddOrEdit (pCliente) {
      var app = this
      if (!pCliente) return
      if (!(pCliente.id > 0)) return
      var ret = await pCliente.ShowDialogUsuarioGestao(app)
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
    actRemoverEmail (pIdx) {
      var lEmail = this.dataset.emails[pIdx]
      if (lEmail.deleted) {
        lEmail.deleted = false
      } else {
        lEmail.deleted = true
        if (lEmail.inserted) {
          this.dataset.emails.splice(pIdx, 1)
        }
      }
    },
    actClearFUP () {
      var app = this
      if (app.edicaobloqueada) return
      app.dataset.fantasia_followup = null
      app.dataset.followupid = null
    },
    actEmailClose (pModel) {
      if (pModel) {
        // se for update
        if (this.emailediting) {
          pModel.updated = true
          this.emailediting.cloneFrom(pModel)
        } else {
          pModel.inserted = true
          if (!this.dataset.emails) this.dataset.emails = []
          this.dataset.emails.push(pModel)
        }
      }
      this.showemailedit = false
      this.emailediting = null
    },
    actNovoEmail () {
      this.emailediting = null
      this.showemailedit = true
    },
    actEditEmail (pIdx) {
      var lEmail = this.dataset.emails[pIdx]
      if (lEmail.deleted) {
        lEmail.deleted = false
      } else {
        this.showemailedit = true
        this.emailediting = lEmail
      }
    },
    async init () {
      var app = this
      // app.adding = (this.$route.name === 'cadastro.clientes.add')
      if (app.adding) {
        app.idstart = null
        app.loading = true
        app.enderecoupdated = true
        app.dataset.limpardados()
      } else {
        app.loading = true
        app.enderecoupdated = true
        await app.refreshData()
      }
      app.loading = false
      app.enderecoupdated = false
      // app.$emit('updated', app.dataset)
    },
    async teste () {
      alert('a')
    },
    async actSave () {
      var app = this
      try {
        app.saving = true
      } catch (error) {
        if (error.message !== '') app.$helpers.showDialog({ ok: false, msg: error.message })
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
          app.$emit('close', true)
        }
        if (app.adding) {
          if (app.mode === 'integrado') {
            app.$nextTick(() => {
              app.loading = true
              app.$router.push({ name: 'cadastro.clientes.edit', params: { id: app.dataset.id } })
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
            message: 'Cliente excluído',
            color: 'positive',
            caption: value.msg,
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
          app.$nextTick(() => {
            app.$router.push({ name: 'cadastro.clientes' })
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
