<template>
<div>
   <q-form @submit="actSave" v-if="!loading" style="max-width: 300px" >
      <div class="fit row wrap">
        <div class="col-12 q-mb-sm" v-if="config.info ? config.info !== '' : false">
          <div class="text-subtitle2">{{config.info}}</div>
        </div>
        <div class="col-12 q-mb-sm">
          <q-btn-group spread flat>
            <q-btn label="Hoje" dense  @click="actChangeDate(momento().format('YYYY/MM/DD'), momento().format('YYYY/MM/DD'))"/>
            <q-btn label="Semana" dense  @click="actChangeDate(momento().startOf('week').format('YYYY/MM/DD'), momento().endOf('week').format('YYYY/MM/DD'))"/>
            <q-btn label="Mês" dense   @click="actChangeDate(momento().startOf('month').format('YYYY/MM/DD'), momento().endOf('month').format('YYYY/MM/DD'))"/>
            <q-btn label="Ano" dense   @click="actChangeDate(momento().startOf('year').format('YYYY/MM/DD'), momento().endOf('year').format('YYYY/MM/DD'))"/>
          </q-btn-group>
        </div>
        <div class="col-12 q-mb-sm">
          <inputdata outlined v-model="modeldti" label="Data inicial" stacklabel :dense="!$q.platform.is.mobile"  />
        </div>
        <div class="col-12 q-mb-sm">
          <inputdata outlined v-model="modeldtf" label="Data final" stacklabel :dense="!$q.platform.is.mobile"  />
        </div>
        <div class="col-12">
          <q-separator spaced  />
          <q-btn label="Confirmar" type="submit" color="primary" unelevated/>
          <q-btn label="Limpar" color="primary" class="q-ml-sm" unelevated @click="actClear"/>
          <q-btn label="Fechar" color="primary" flat class="q-ml-sm" @click="actClose"  />
        </div>
      </div>
  </q-form>
</div>
</template>

<script>
import inputdata from 'src/components/cpn-input-data'
import moment from 'moment'
export default {
  components: {
    inputdata
  },
  props: {
    config: {
      type: Object,
      default: function () {
        return {
          value: [],
          label: ''
        }
      }
    }
  },
  data () {
    return {
      loading: true,
      modeldti: null,
      modeldtf: null
    }
  },
  mounted () {
    var app = this
    if (app.config.value) {
      var element = app.config.value
      if (element.dti) app.modeldti = element.dti
      if (element.dtf) app.modeldtf = element.dtf
    }
    app.loading = false
    document.onkeydown = function (e) {
      if (e.key === 'Escape') {
        app.actClose()
      }
    }
  },
  watch: {
  },
  methods: {
    momento () {
      return moment()
    },
    async actChangeDate (pIni, pFim) {
      this.modeldti = pIni
      this.modeldtf = pFim
      await this.actSave()
      this.$emit('close')
    },
    actClose () {
      this.$emit('close')
    },
    actClear () {
      this.$emit('update', null)
    },
    async actSave (e) {
      var app = this
      var val = {
        dti: app.modeldti,
        dtf: app.modeldtf
      }
      this.$emit('update', val)
    }
  }
}
</script>
