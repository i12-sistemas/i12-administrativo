<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" bordered>
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">{{(title ? title !== '' : false) ? title : 'Período'}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>
          {{(title ? title !== '' : false) ? title : 'Período'}}
        </q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>
      <q-card-section>
      <div class="col-12 q-mb-sm">
          <q-btn-group spread flat>
            <q-btn label="Hoje" dense  @click="actChangeDate(momento().format('YYYY/MM/DD'), momento().format('YYYY/MM/DD'))"/>
            <q-btn label="Semana" dense  @click="actChangeDate(momento().startOf('week').format('YYYY/MM/DD'), momento().endOf('week').format('YYYY/MM/DD'))"/>
            <q-btn label="Mês" dense   @click="actChangeDate(momento().startOf('month').format('YYYY/MM/DD'), momento().endOf('month').format('YYYY/MM/DD'))"/>
            <q-btn label="Ano" dense   @click="actChangeDate(momento().startOf('year').format('YYYY/MM/DD'), momento().endOf('year').format('YYYY/MM/DD'))"/>
          </q-btn-group>
        </div>
      </q-card-section>
      <q-card-section>
        <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
          <div class="col-12">
            <inputdata outlined v-model="valuei" label="Data inicial" stacklabel :dense="!$q.platform.is.mobile"  />
          </div>
          <div class="col-12">
            <inputdata outlined v-model="valuef" label="Data final" stacklabel :dense="!$q.platform.is.mobile"  />
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
import moment from 'moment'
import inputdata from 'src/components/cpn-input-data'
export default {
  name: 'dialoag.filter.datetime.interval',
  components: {
    inputdata
  },
  props: {
    dados: {
      type: Object,
      default: function () {
        return { valuei: null, valuef: null }
      }
    }
  },
  data () {
    return {
      loading: true,
      title: 'Período',
      valuei: 0,
      valuef: 0
    }
  },
  async mounted () {
    var app = this
    if (app.dados) {
      if (app.dados.valuei) app.valuei = app.dados.valuei
      if (app.dados.valuef) app.valuef = app.dados.valuef
      if (app.dados.title) app.title = app.dados.title
    }
  },
  methods: {
    momento () {
      return moment()
    },
    async actChangeDate (pIni, pFim) {
      this.valuei = pIni
      this.valuef = pFim
      this.onOKClick()
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
    onOKClick () {
      try {
        var app = this
        var dados = {
          valuei: app.valuei,
          valuef: app.valuef
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
