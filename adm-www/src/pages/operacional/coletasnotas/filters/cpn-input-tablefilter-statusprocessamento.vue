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
              <q-checkbox v-model="model" :val="option.value" :color="option.color"  >
                  <template v-slot:default>
                    <q-icon :name="option.icon" v-if="option.icon" class="q-mr-md" size="20px"  :color="(model.indexOf(option.value) >= 0) ? option.color : 'grey'" /><span :class="(model.indexOf(option.value) >= 0) ? 'text-' + option.color : ''">{{option.label}}</span>
                  </template>
              </q-checkbox>
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
      model: [],
      options: [
        { color: 'green', value: '1', label: 'Nota Ok', icon: 'check_circle' },
        { color: 'blue', value: '2', label: 'Pendente XML', icon: 'hourglass_empty' },
        { color: 'orange', value: '3', label: 'Pendente processar XML', icon: 'hourglass_bottom' },
        { color: 'red', value: '4', label: 'Erro no download', icon: 'info' },
        { color: 'blue-5', value: '5', label: 'Ignorado', icon: 'info' }
      ]
    }
  },
  mounted () {
    var app = this
    if (app.config.value) app.model = app.config.value
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
