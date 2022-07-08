<template>
<div>
   <q-form @submit="actSave"   >
      <div class="fit row wrap">
        <div v-if="config.info ? config.info !== '' : false">
          <div class="text-subtitle2">{{config.info}}</div>
        </div>
        <div class="col-12">
          <div class="row wrap full-width content-center q-ma-md" v-for="(option, key) in options" :key="key">
            <div class="col-12">
              <q-radio v-model="model" :val="option.value"  :label="option.label" :color="option.color"  />
            </div>
          </div>
        </div>
        <div class="col-12">
          <q-separator spaced  />
          <q-btn label="Confirmar" type="submit" color="primary" unelevated/>
          <q-btn label="Ignorar tudo" color="primary" class="q-ml-sm" @click="actClear"  unelevated />
          <q-btn label="Fechar" color="primary" flat class="q-ml-sm" @click="actClose"  />
        </div>
      </div>
</q-form>
</div>
</template>

<script>
export default {
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
      model: null,
      options: [
        { color: 'red', value: 'AP', label: 'Coleta avulsa - pendente gerar coleta' },
        { color: 'blue', value: 'AO', label: 'Coleta avulsa - coleta gerada' },
        { color: 'green', value: 'A', label: 'Somente coleta avulsa' }
      ]
    }
  },
  mounted () {
    var app = this
    if (app.config.value) {
      app.model = app.config.value
    }
    if (app.config.options) {
      app.options = app.config.options
    }
    document.onkeydown = function (e) {
      if (e.key === 'Escape') {
        app.actClose()
      }
    }
  },
  watch: {
  },
  methods: {
    actClose () {
      this.$emit('close')
    },
    actClear () {
      this.$emit('update', null)
    },
    actSave (e) {
      var app = this
      var val = null
      if (app.model) val = app.model
      this.$emit('update', val)
    }
  }
}
</script>
