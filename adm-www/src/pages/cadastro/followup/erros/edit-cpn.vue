<template>
<div>
  <q-card :bordered="mode == 'integrado'" flat :class="$q.platform.is.mobile ? 'no-padding' : ''">
    <q-card-section v-if="loading" style="padding: 0">
        <q-linear-progress indeterminate />
        <div class="text-primary text-center q-pa-md">Carregando...</div>
    </q-card-section>
    <q-card-section  horizontal v-if="!loading && (!adding && !dataset.id)" >
      <div class="full-width text-center q-pa-lg">
          <div class="text-h6 q-pa-md">Nenhum registro encontrado!</div>
          <q-btn label="Voltar para consultas"  outline  :to="{ name: 'cadastro.motoristas' }" />
      </div>
    </q-card-section>
    <q-card-section horizontal v-if="!loading && (adding || (!adding && dataset.id > 0)) && (mode == 'integrado')" >
        <div class="text-h6 q-py-sm q-px-md">Categoria de Erro</div>
        <q-space />
        <q-btn color="negative" icon="delete_forever" flat :label="$q.platform.is.mobile ? '' : 'Excluir'" stretch @click="actDelete" v-if="!loading && !adding && (permitedelete ? permitedelete.ok : false)" />
    </q-card-section>
    <q-separator class="q-pa-0 q-ma-0" v-if="!loading && (adding || (!adding && dataset.id > 0)) && (mode == 'integrado')" />
    <q-card-section v-if="!loading && (adding || (!adding && dataset.id > 0))" :class="$q.platform.is.mobile ? 'no-padding' : ''">
      <q-form @submit="actSave" autofocus  >
        <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
          <div class="col-md-12 col-xs-12">
            <q-input outlined v-model="dataset.descricao" label="Descrição *" stack-label maxlength="150" counter autofocus
              :dense="!$q.platform.is.mobile" :disable="!dataset.ativo" :readonly="edicaobloqueada"
              lazy-rules :rules="[ val => val && (val.length <= 150)|| 'Obrigatório informar o nome. Máximo 150 caracteres']"
            />
          </div>
          <div class="col-md-6 col-xs-12">
            <q-select outlined v-model="dataset.tipo" :options="optionstipo" label="Tipo de erro" :readonly="edicaobloqueada"
              emit-value map-options stack-label :dense="!$q.platform.is.mobile" :disable="!dataset.ativo"/>
          </div>
          <div class="col-md-6 col-xs-12">
            <q-checkbox v-model="dataset.ativo" label="Ativo" dense :disable="edicaobloqueada" />
          </div>
        </div>
        <q-separator spaced  />
        <div v-if="mode == 'integrado'" >
          <q-btn label="Salvar" type="submit" color="primary" :disable="edicaobloqueada" />
          <q-btn label="Voltar" color="primary" flat class="q-ml-sm" @click="$router.go(-1)" v-if="mode == 'integrado'" />
          <q-btn label="Fechar" color="primary" flat class="q-ml-sm" @click="actClose" v-if="mode == 'dialog'" />
        </div>
        <div v-if="mode == 'dialog'" >
          <q-btn label="Salvar" type="submit" color="primary" :disable="edicaobloqueada" :class="$q.platform.is.mobile ? 'full-width' : ''" :size="$q.platform.is.mobile ? 'lg' : 'md'" />
          <q-btn label="Fechar" color="primary" flat  @click="actClose" :class="$q.platform.is.mobile ? 'full-width q-mt-md' : 'q-ml-sm'" :size="$q.platform.is.mobile ? 'lg' : 'md'" />
        </div>
        <logusuario v-if="!loading && !adding" :createdat="dataset.created_at" :createdusuario="dataset.created_usuario"
          :updatedat="dataset.updated_at" :updatedusuario="dataset.updated_usuario" />
      </q-form>
    </q-card-section>
  </q-card>
</div>
</template>

<script>
import FollowupErro from 'src/mvc/models/followuperro.js'
import logusuario from 'src/components/cpn-logusuario'

export default {
  name: 'followup.cadastro.erros.edit.cnp',
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
    var dataset = new FollowupErro()
    return {
      permitesave: null,
      permitedelete: null,
      showadd: false,
      saving: false,
      optionstipo: [
        { value: 'agenda', label: 'Erro de agendamento' },
        { value: 'coleta', label: 'Erro de coleta' },
        { value: 'dtpromessa', label: 'Erro de data da promessa' }
      ],
      showsearch: false,
      dataset,
      breadcrumbslist: [
        { to: { name: 'followup.consulta' }, label: 'Follow Up' },
        { to: { name: 'followup.cadastro.erros' }, label: 'Categorias de erros' }
      ],
      showchangepwd: false,
      loading: false,
      deleting: false,
      posting: false,
      msgError: ''
    }
  },
  computed: {
    edicaobloqueada () {
      return (this.permitesave ? !this.permitesave.ok : true)
    }
  },
  async mounted () {
    this.permitesave = this.$helpers.permite('followup.consulta')
    this.permitedelete = this.$helpers.permite('followup.consulta')
    await this.init()
  },

  methods: {
    actChangePwdShow () {
      this.showchangepwd = !this.showchangepwd
      if (this.showchangepwd) {
        this.$nextTick(() => {
          this.dataset.newpwd = null
          this.$refs.txtsenha.$el.focus()
        })
      } else {
        this.dataset.newpwd = null
      }
    },
    async init () {
      var app = this
      // app.adding = (this.$route.name === 'cadastro.clientes.add')
      if (app.adding) {
        app.idstart = null
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
        app.dataset.senhainformada = false
        if (app.showchangepwd) app.dataset.senhainformada = true
        // var params = await app.dataset.parametros()
        // if (!params) throw new Error('Nenhuma alteração')
      } catch (error) {
        app.$helpers.showDialog({ ok: false, msg: error.message })
        app.saving = false
        return
      }
      var ret = await app.dataset.save()
      if (ret.ok) {
        app.showchangepwd = false
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
              app.$router.push({ name: 'followup.cadastro.erros.edit', params: { id: app.dataset.id } })
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
    actClose () {
      this.$emit('close', false)
    },
    async actDelete () {
      var app = this
      app.deleting = true
      var ret = app.dataset.deleteWithQuestion(app)
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
          app.$nextTick(() => {
            app.$router.push({ name: 'followup.cadastro.erros' })
          })
        } else {
          if (value.msg) {
            if (value.msg !== '') app.$helpers.showDialog(value)
          }
        }
        app.deleting = false
      })
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
