<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" bordered>
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">{{(title ? title !== '' : false) ? title : 'Intervalo de valores'}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>
          {{(title ? title !== '' : false) ? title : 'Intervalo de valores'}}
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section>
        <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
          <div class="col-12">
            <q-input outlined v-model="valuei" label="Valor inicial" stack-label :dense="!$q.platform.is.mobile" ref="txtvaluei" autofocus @keyup.enter="onOKClick"
                :mask="mask ? mask : '#,##'" fill-mask="0" reverse-fill-mask input-class="text-left" :prefix="prefix ? prefix : ''" />
          </div>
          <div class="col-12">
            <q-input outlined v-model="valuef" label="Valor final" stack-label :dense="!$q.platform.is.mobile" @keyup.enter="onOKClick"
                :mask="mask ? mask : '#,##'" fill-mask="0" reverse-fill-mask input-class="text-left" :prefix="prefix ? prefix : ''" />
          </div>
        </div>
      </q-card-section>
      <q-card-actions>
        <q-btn label="Ok" color="primary" unelevated @click="onOKClick" :class="$q.platform.is.mobile ? 'full-width' : ''" :size="$q.platform.is.mobile ? 'lg' : 'md'"/>
        <q-btn label="Limpar tudo" color="primary" flat @click="actClearAll" :class="$q.platform.is.mobile ? 'full-width' : ''" :size="$q.platform.is.mobile ? 'lg' : 'md'"/>
        <q-btn label="Fechar" color="primary" flat  @click="actClose" :class="$q.platform.is.mobile ? 'full-width q-mt-md' : 'q-ml-sm'" :size="$q.platform.is.mobile ? 'lg' : 'md'" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'dialoag.filter.float.interval',
  props: {
    dados: {
      type: Object,
      default: function () {
        return { valuei: 0, valuef: 0, prefix: null, mask: '' }
      }
    }
  },
  data () {
    return {
      loading: true,
      title: 'Intervalo de valores',
      mask: '#,##',
      prefix: '',
      decimal: 2,
      valuei: 0,
      valuef: 0
    }
  },
  async mounted () {
    var app = this
    if (app.dados) {
      if (app.dados.decimal != null) app.decimal = parseInt(app.dados.decimal)
      if (app.dados.valuei) app.valuei = parseFloat(app.dados.valuei).toFixed(app.decimal)
      if (app.dados.valuef) app.valuef = parseFloat(app.dados.valuef).toFixed(app.decimal)
      if (app.dados.mask) app.mask = app.dados.mask
      if (app.dados.title) app.title = app.dados.title
      if (app.dados.prefix) app.prefix = app.dados.prefix
    }
  },
  methods: {
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
    onOKClick () {
      try {
        var app = this
        var vi = parseFloat(app.$helpers.strToCurr(app.valuei)).toFixed(app.decimal)
        var vf = parseFloat(app.$helpers.strToCurr(app.valuef)).toFixed(app.decimal)
        var dados = {
          valuei: vi,
          valuef: vf
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
