<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-70'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <q-icon name="shared" />
        <span class="ellipsis text-subtitle2">{{ title }}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-icon name="shared" />
        <q-toolbar-title>{{title }}</q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section class="no-padding scroll" style="max-height: 90vh">
        <div v-if="!loading">
            <q-card flat>
              <q-card-section >
                <div class="fit row wrap q-gutter-xs">
                  <div class="col-12">
                    <selectcontato outlined label="Para" nullabled :dense="!$q.platform.is.mobile" v-model="model.to" :sugestao="listasugestao" ref="txtto" />
                  </div>
                  <div class="col-12" v-if="showcc">
                    <selectcontato outlined label="Cópia para" nullabled :dense="!$q.platform.is.mobile" v-model="model.cc" :sugestao="listasugestao" ref="txtcc" />
                  </div>
                  <div class="col-12">
                    <q-input outlined v-model="model.assunto" label="Assunto" stack-label maxlength="250" :dense="!$q.platform.is.mobile" >
                      <template v-slot:after>
                        <q-btn flat label="CC" rounded @click="showcc = true" />
                      </template>
                    </q-input>
                  </div>
                  <div class="col-12">
                    <q-input outlined v-model="model.mensagem" label="Mensagem" stack-label type="textarea" class="full-height" />
                  </div>
                </div>
              </q-card-section>
              <q-card-section v-if="error ? error !== '' : false">
                <div class="q-my-md q-pa-md text-white bg-red rounded-borders">{{error}}</div>
              </q-card-section>
              <q-card-actions class="q-px-md">
                  <q-btn color="primary" unelevated icon="send" label="Enviar" @click="onOKClick" />
                  <q-btn color="primary" flat icon="clear" label="fechar" @click="actClose" />
              </q-card-actions>
            </q-card>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import selectcontato from 'src/components/cnp-select-emailcontato.vue'
export default {
  name: 'cpn.share.email.dialog',
  components: {
    selectcontato
  },
  props: {
    title: {
      type: String,
      default: function () {
        return 'E-mail'
      }
    },
    assunto: {
      type: String,
      default: function () {
        return 'E-mail'
      }
    },
    sugestao: {
      type: Array,
      default: function () {
        return []
      }
    },
    to: {
      type: Array,
      default: function () {
        return []
      }
    },
    cc: {
      type: Object,
      default: function () {
        return null
      }
    },
    message: {
      type: String,
      default: function () {
        return null
      }
    }
  },
  data () {
    return {
      loading: true,
      showcc: false,
      error: null,
      listasugestao: [],
      model: {
        to: [],
        cc: [],
        mensagem: '',
        assunto: ''
      }
    }
  },
  async mounted () {
    var app = this
    if (app.assunto) app.model.assunto = app.assunto
    // if (app.message) app.model.message = app.message
    if (app.sugestao) {
      for (let index = 0; index < app.sugestao.length; index++) {
        const element = app.sugestao[index]
        app.listasugestao.push(element)
      }
    }
    if (app.to) {
      for (let index = 0; index < app.to.length; index++) {
        const element = app.to[index]
        app.model.to.push(element)
      }
    }
    // if (app.cc) {
    //   for (let index = 0; index < app.cc.length; index++) {
    //     const element = app.cc[index]
    //     app.model.cc.push(element)
    //   }
    // }
    // app.loading = false
  },
  methods: {
    actClose () {
      this.onClose(false)
    },
    onClose (TeveAlteracao) {
      this.$emit('ok', null)
      this.hide()
    },
    async actUpdated (dataset) {
      // var app = this
      // app.dataset = dataset
    },

    // following method is REQUIRED
    // (don't change its name --> "show")
    show () {
      var app = this
      app.$refs.dialog.show()
      setTimeout(() => {
        app.loading = false
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
      var app = this
      try {
        if (!app.model) throw new Error('Nenhum e-mail informado')
        if (!app.model.to) throw new Error('Nenhum e-mail informado')
        if (app.model.to.length <= 0) throw new Error('Nenhum e-mail informado')

        if (!app.model.assunto) throw new Error('Assunto não informado')
        app.model.assunto = app.model.assunto.trim()
        if (app.model.assunto === '') throw new Error('Assunto não informado')
      } catch (error) {
        this.error = error.message
        setTimeout(() => {
          app.error = null
        }, 3000)
        return false
      }
      // on OK, it is REQUIRED to
      // emit "ok" event (with optional payload)
      // before hiding the QDialog
      this.$emit('ok', app.model)
      // or with payload: this.$emit('ok', { ... })

      // then hiding dialog
      this.hide()
    }

  }
}
</script>
