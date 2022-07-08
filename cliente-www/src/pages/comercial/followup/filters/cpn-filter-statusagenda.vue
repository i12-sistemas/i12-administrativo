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
      <q-card-section>
         <q-card bordered flat>
          <q-card-section class="bg-grey-3 q-pa-sm">
            <div class="text-subtitle2">Status</div>
          </q-card-section>
          <q-card-section class="q-pa-none q-ma-none" >
              <div class="q-pa-sm">
              <q-option-group v-model="status" type="checkbox" color="primary" :options="optionsstatusagendamento" size="sm" inline />
              </div>
          </q-card-section>
         </q-card>
      </q-card-section>
      <q-card-section>
        <q-card bordered flat>
          <q-card-section class="bg-grey-3 q-pa-sm">
            <div class="text-subtitle2">Seleção de erros</div>
          </q-card-section>
          <q-card-section class="q-pa-none q-ma-none" >
            <div class="row wrap full-width q-pa-sm q-col-gutter-sm">
              <div class="col-9">
                <selectfollowuperros outlined label="Erro" nullabled :tipo="config.tipoerro"  v-model="modelerro" class="full-width" ref="txterro" :disable="!erroallowed"/>
              </div>
              <div class="col-3">
                <q-btn icon="add" label="Incluir"  flat outline @click="actAdd" class="full-width q-pt-xs" :disable="!erroallowed" icon-right no-wrap />
              </div>
            </div>
          </q-card-section>
          <q-card-section class="scroll" style="max-height: 50vh" v-if="status ? status.indexOf('erro') >= 0 : false" >
            <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
              <div class="col-12 q-mb-sm">
                <div v-for="(item, key) in lista" :key="key" class="full-width">
                  <q-chip removable @remove="actRemove(key)" class="full-width">{{item.descricao}}
                  </q-chip>
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
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

import FollowUps from 'src/mvc/collections/followups.js'
import FollowupErros from 'src/mvc/collections/followuperros.js'
import selectfollowuperros from 'src/components/cnp-select-followup-erros'
export default {
  name: 'dialog.followup.filter.itensdescricao',
  components: {
    selectfollowuperros
  },
  props: {
    config: {
      type: Object,
      default: function () {
        return {
          tipoerro: 'agenda',
          value: null,
          title: 'Itens'
        }
      }
    }
  },
  data () {
    var dataset = new FollowUps()
    return {
      dataset,
      loading: true,
      title: 'Itens',
      modelerro: null,
      lista: [],
      status: [],
      optionsstatusagendamento: [
        {
          label: 'Sem status',
          value: 'semstatus',
          color: 'grey'
        },
        {
          label: 'OK',
          value: 'ok',
          color: 'green'
        },
        {
          label: 'Com erro',
          value: 'erro',
          color: 'red'
        }
      ]
    }
  },
  computed: {
    erroallowed () {
      if (this.loading) return false
      if (!this.status) return false
      return (this.status.indexOf('erro') >= 0)
    }
  },
  async mounted () {
    var app = this
    if (app.config.title) app.title = app.config.title
    if (app.config.value ? app.config.value.length > 0 : false) {
      var tem = false

      tem = (app.config.value.indexOf('ok') >= 0)
      if (tem) app.status.push('ok')

      tem = (app.config.value.indexOf('semstatus') >= 0)
      if (tem) app.status.push('semstatus')

      tem = (app.config.value.indexOf('erro') >= 0)
      if (tem) {
        app.status.push('erro')
        var errosids = []
        for (let index = 0; index < app.config.value.length; index++) {
          const n = app.config.value[index]
          if (!(n === 'ok' || n === 'semstatus' || n === 'erro')) {
            errosids.push(parseInt(n))
          }
        }
        if (errosids ? errosids.length > 0 : false) {
          var erros = new FollowupErros()
          erros.ids = errosids
          var ret = await erros.fetch()
          if (ret.ok) {
            for (let index = 0; index < erros.itens.length; index++) {
              const element = erros.itens[index]
              app.lista.push(element)
            }
          }
        }
      }
    }
  },
  methods: {
    actRemove (key) {
      this.lista.splice(key, 1)
    },
    async actAdd () {
      var app = this
      if (app.modelerro) {
        var idx = -1
        for (let index = 0; index < app.lista.length; index++) {
          const element = app.lista[index]
          if (parseInt(element.id) === parseInt(app.modelerro.id)) {
            idx = index
            break
          }
        }
        if (idx < 0) app.lista.push(this.modelerro)
      }
      this.modelerro = null
    },
    actClose () {
      // this.$emit('ok', null)
      this.hide()
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
        if (app.status) {
          for (let index = 0; index < app.status.length; index++) {
            dados.push(app.status[index])
          }
          var tem = (app.status.indexOf('erro') >= 0)
          if (tem) {
            if (app.lista) {
              for (let index = 0; index < app.lista.length; index++) {
                const n = app.lista[index]
                dados.push(n.id)
              }
            }
          }
        }
        if (dados.length === 0) dados = null
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
