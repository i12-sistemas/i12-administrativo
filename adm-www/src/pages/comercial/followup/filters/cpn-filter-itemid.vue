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
        <div class="fit row wrap full-width">
          <div class="col-10 q-mb-sm q-mr-sm ">
            <selectitemid outlined label="Descrição dos itens" nullabled :dense="!$q.platform.is.mobile" v-model="model"  />
          </div>
          <div class="col-1">
            <q-btn icon="add" flat outline @click="actAdd" round class="q-ml-sm" />
          </div>
        </div>
      </q-card-section>
      <q-card-section class="scroll" style="max-height: 50vh" >
        <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
          <div class="col-12 q-mb-sm">
            <div v-for="(item, key) in lista" :key="key" class="full-width">
              <q-chip removable @remove="actRemove(key)" class="full-width">{{item.itemid}}
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

import FollowUps from 'src/mvc/collections/followups.js'
import selectitemid from './cnp-select-itemid'
export default {
  name: 'dialog.followup.filter.itemid',
  components: {
    selectitemid
  },
  props: {
    config: {
      type: Object,
      default: function () {
        return {
          multiple: false,
          allowempty: false,
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
      value: null,
      model: null,
      lista: []
    }
  },
  async mounted () {
    var app = this
    if (app.config.title) app.title = app.config.title
    if (app.config.value ? app.config.value.length > 0 : false) {
      var ret = await app.dataset.fetchlistitemid('', app.config.value)
      if (ret.ok) {
        for (let index = 0; index < ret.data.length; index++) {
          const element = ret.data[index]
          app.lista.push(element)
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
      if (app.model) {
        var idx = -1
        for (let index = 0; index < app.lista.length; index++) {
          const element = app.lista[index]
          if ((element.itemid.toString().toLowerCase()) === app.model.itemid.toString().toLowerCase()) {
            idx = index
            break
          }
        }
        if (idx < 0) app.lista.push(this.model)
      }
      this.model = null
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
        var dados = {}
        if ((app.lista.length === 0) && (app.value)) {
          await app.actAdd()
        }
        dados = []
        for (let index = 0; index < app.lista.length; index++) {
          dados.push(app.lista[index].itemid)
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
