<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.screen.lt.sm">
    <q-card class="q-dialog-plugin" bordered :style="$q.screen.lt.sm ? '' : 'min-width: 50vw'">
      <q-bar class="bg-primary text-white" v-if="!$q.screen.lt.sm">
        <span class="ellipsis text-subtitle2 q-ml-xs">{{title}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.screen.lt.sm">
        <q-toolbar-title>
          {{title}}
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section v-if="config.allowempty" >
        <div class="fit row wrap">
          <div class="col-12">
            <q-option-group v-model="valueempty" :options="options" inline />
          </div>
        </div>
      </q-card-section>
      <q-card-section v-if="valueempty != 'vazio'" >
        <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
          <div class="col-12 q-mb-sm">
            <q-input v-model="model" type="text" label="Insira o valor" outlined dense autofocus stack-label  v-on:keyup.enter="actAdd"
              :error="(error ? error !== '' : false)" :error-message="(error ? error != '' : false) ? error : ''"
              hint="Insira um valor e clique em + para adicionar à lista">
              <template v-slot:append>
                <q-btn color="black" icon="add" round flat @click="actAdd" dense />
              </template>
            </q-input>
          </div>
          <div class="col-12 q-mb-sm">
            <div v-for="(item, key) in lista" :key="key" class="full-width">
              <q-chip removable @remove="actRemove(key)" class="full-width">{{item}}
              </q-chip>
            </div>
          </div>
        </div>
      </q-card-section>
      <q-card-actions>
        <q-btn label="Ok" color="primary" unelevated @click="onOKClick" :class="$q.screen.lt.sm ? 'full-width' : ''" :size="$q.screen.lt.sm ? 'lg' : 'md'"/>
        <q-btn label="Limpar tudo" color="primary" flat @click="actClearAll" :class="$q.screen.lt.sm ? 'full-width' : ''" :size="$q.screen.lt.sm ? 'lg' : 'md'"/>
        <q-btn label="Fechar" color="primary" flat  @click="actClose" :class="$q.screen.lt.sm ? 'full-width q-mt-md' : 'q-ml-sm'" :size="$q.screen.lt.sm ? 'lg' : 'md'" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>

export default {
  name: 'dialog.followup.filter.statusgeral',
  components: {
  },
  props: {
    config: {
      type: Object,
      default: function () {
        return {
          type: 'integer',
          allowempty: false,
          splitchar: null,
          value: null,
          title: 'Itens'
        }
      }
    }
  },
  data () {
    return {
      model: null,
      loading: true,
      title: 'Itens',
      error: null,
      lista: [],
      valueempty: 'naovazio',
      options: [
        { label: 'Somente com valores', value: 'naovazio' },
        { label: 'Somente sem valores', value: 'vazio' }
      ]
    }
  },
  computed: {
  },
  async mounted () {
    var app = this
    if (app.config.title) app.title = app.config.title

    app.lista = []
    if (app.config.value ? app.config.value.length > 0 : false) {
      for (let index = 0; index < app.config.value.length; index++) {
        var vlr = app.config.value[index]
        if (app.config.allowempty) {
          if ((vlr === 'naovazio') || (vlr === 'vazio')) {
            app.valueempty = vlr
          } else {
            app.lista.push(vlr)
          }
        } else {
          app.lista.push(vlr)
        }
      }
    }
  },
  methods: {
    actClose () {
      // this.$emit('ok', null)
      this.hide()
    },
    actAdd () {
      var app = this
      try {
        if (!app.lista) app.lista = []
        if (!app.model) throw new Error('Nenhum valor informado')
        if (app.model === '') throw new Error('Nenhum valor informado')
        var v = app.model
        if (app.config.type === 'integer') {
          v = parseInt(app.model)
          app.model = v
          if (!(v > 0)) throw new Error('Valor inválido')
        }
        if (app.lista.indexOf(v) >= 0) throw new Error('Já foi inserido anteriormente')
        app.lista.push(v)

        app.model = null
        app.error = null
      } catch (error) {
        app.error = error.message
        setTimeout(() => {
          app.error = null
        }, 4000)
      }
    },
    // following method is REQUIRED
    // (don't change its name --> "show")
    show () {
      var app = this
      this.$refs.dialog.show()
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
    actClearAll () {
      this.$emit('ok', null)
      this.hide()
    },
    async onOKClick () {
      try {
        var app = this
        var dados = []
        if (app.lista) {
          for (let index = 0; index < app.lista.length; index++) {
            dados.push(app.lista[index])
          }
        }
        if (dados.length === 0) dados = null

        if (app.config.allowempty) {
          if (app.valueempty === 'vazio') {
            dados = []
            dados.push(app.valueempty)
          } else {
            if (dados ? dados.length === 0 : true) {
              dados = []
              dados.push(app.valueempty)
            }
          }
        }
      } catch (error) {
        app.error = error.message
        return
      }
      this.$emit('ok', dados)
      // or with payload: this.$emit('ok', { ... })

      // then hiding dialog
      this.hide()
    }

  }
}
</script>
