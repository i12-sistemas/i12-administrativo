<template>
<div>
  <q-page class="" >
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header full-width" >
      <div class="col-xs-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 " :class="$q.screen.lt.sm ? '' : 'q-pa-lg'" >
        <!-- error -->
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''" v-if="error && !posting">
          <q-card-section v-if="error">
            <q-banner class="bg-red text-white" rounded>
              {{error}}
              <template v-slot:action>
                <q-btn flat color="white" label="ok" @click="error = null" />
              </template>
            </q-banner>
          </q-card-section>
        </q-card>
        <!-- error -->
        <!-- info -->
        <q-card class="bg-white q-pb-md " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section>
            <div class="row q-col-gutter-sm">
              <div class="col-xs-12">
                <div>Descreve sua necessidade de atendimento</div>
                <q-input  outlined v-model="atendimento.problemareclamado" class="full-width q-my-sm" placeholder="Mensagem" type="textarea"
                  :disable="posting" ref="txtmensagem"
                  autogrow input-style="min-height: 150px" counter hint="Obrigatório no mínimo 10 caracteres" />
              </div>
              <div class="col-xs-12 ">
                <div class="q-mt-md">Como você entende a necessidade da sua solicitação de atendimento</div>
                <q-select v-model="atendimento.prioridade"  outlined emit-value stack-label map-options class="q-my-sm" :dense="!$q.platform.is.mobile"
                  :disable="posting"
                  hint="A definição de prioridade poderá ser alterada após a análise técnica"
                  :options="[
                    {value: 1, label: 'Normal', color: 'green', hint: 'Novas solicitações ou erros que não afetam diretamente o processo da empresa' },
                    { value: 2, label: 'Média', color: 'yellow-10', hint: 'Quando há erros ou necessidade que funções secundárias. Não paralisa o processo de uso'},
                    { value: 3, label: 'Alta', color: 'red-10', hint: 'Quando a situação está totalmente paralisada ou alguma função primária está afetando processo de uso' }
                    ]" >
                  <template v-slot:selected-item="scope">
                    <div class="text-no-wrap ellipsis">
                    <q-icon name="circle" size="16px" class="q-mr-sm" :color="scope.opt.color" v-if="scope.opt.color" />
                    {{scope.opt.label}}
                    <q-icon name="arrow_right" size="20px"  color="grey-5" />
                    {{scope.opt.hint}}

                    </div>
                  </template>
                  <template v-slot:option="scope">
                    <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator" >
                      <q-item-section avatar>
                        <q-icon name="circle" size="12px" class="q-mr-sm" :color="scope.opt.color" v-if="scope.opt.color" />
                      </q-item-section>
                      <q-item-section >
                        <q-item-label >{{scope.opt.label}}</q-item-label>
                        <q-item-label caption>{{scope.opt.hint}}</q-item-label>
                      </q-item-section>
                      <q-tooltip :delay="500">
                        {{scope.opt.hint}}
                      </q-tooltip>
                    </q-item>
                  </template>
                </q-select>
              </div>
            </div>
          </q-card-section>
          <q-card-section  >
            <q-banner class="bg-light-blue-1 text-primary full-width" rounded >
              <q-icon name="attach_file" size="20px"  class="q-mr-md" />Após inserir a solicitação, será permitido a inclusão de anexos ou mais informações!
            </q-banner>
          </q-card-section>
          <q-card-section class="row q-gutter-x-md" v-if="!$q.screen.lt.sm">
            <q-btn color="primary" icon="check" unelevated label="Incluir atendimento" @click="actSave" :class="$q.screen.lt.sm ? 'full-width' : ''"
              :loading="posting" :disable="posting" />
            <q-btn color="grey-4" text-color="grey-9" icon="arrow_back" unelevated label="Voltar" @click="$router.go(-1)" />
          </q-card-section>
        </q-card>
        <q-page-sticky position="top" expand>
          <q-toolbar class="bg-primary text-white">
            <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
            <q-toolbar-title>
              Novo atendimento
            </q-toolbar-title>
            <q-btn stretch  label="Incluir"  flat @click="actSave" :loading="posting" :disable="posting" dense/>
          </q-toolbar>
        </q-page-sticky>
        <q-page-sticky position="bottom" expand v-if="$q.screen.lt.sm">
          <q-toolbar class="bg-primary text-white">
            <q-btn color="primary" icon="check" unelevated label="Incluir atendimento" @click="actSave" :class="$q.screen.lt.sm ? 'full-width q-pa-md' : ''"
              stretch :loading="posting" :disable="posting"/>
          </q-toolbar>
        </q-page-sticky>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
// eslint-disable-next-line no-unused-vars
import autolink from 'src/boot/helpers/autolink.js'
import Atendimento from 'src/mvc/models/atendimento.js'
export default {
  name: 'atendimentos.listagem',
  components: {
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
  data () {
    var atendimento = new Atendimento()
    return {
      atendimento,
      posting: false,
      error: null
    }
  },
  async activated () {
    var app = this
    app.$nextTick(() => {
      app.$refs.txtmensagem.$el.focus()
    })
  },
  async mounted () {
    var app = this
    app.atendimento.limpardados()
    app.atendimento.prioridade = 1
  },
  methods: {
    async actSave () {
      var app = this
      app.error = null
      if (app.posting) return
      var dialog = app.$helpers.dialogProgress(app, 'Registrando, aguarde!')
      try {
        app.posting = true
        app.error = null
        var ret = await app.atendimento.saveAdd()
        if (!ret.ok) {
          app.error = ret.msg
          app.$q.notify({
            message: 'Atendimento não foi registrado',
            color: 'red',
            timeout: 3000,
            position: 'center',
            caption: app.error,
            actions: [
              { label: 'OK', color: 'white', handler: () => { app.error = null } }
            ]
          })
        } else {
          app.atendimento.limpardados()
          app.$eventbus.$emit('usuariodash', false)
          app.$q.notify({
            message: 'Atendimento registrado com sucesso!',
            color: 'green',
            timeout: 2000,
            position: 'center',
            icon: 'check'
          })
          app.$nextTick(() => {
            app.error = null
            app.loading = true
            app.$router.push({ name: 'atendimentos.atendimento', params: { id: ret.id } })
            app.loading = false
          })
        }
      } catch (error) {
        app.posting = false
        app.error = error.message
      } finally {
        app.posting = false
        dialog.hide()
      }
    }
  }
}
</script>
