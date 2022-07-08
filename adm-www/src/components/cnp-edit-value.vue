<template>
<div>
  <q-field :outlined="outlined" v-model="model" :label="label" :hint="hint" :dense="dense"
      lazy-rules
      :rules="[
          val => !!val && (limitemin >= model_en) && (limitemax <= model_en) || 'Valor inválido - Mín.: ' + $helpers.formatRS(limitemin, moneyFormatForComponent.prefix, moneyFormatForComponent.precision) + (limitemax===Number.MAX_VALUE ? '' : ' até ' + $helpers.formatRS(limitemax, moneyFormatForComponent.prefix, moneyFormatForComponent.precision))
        ]"
 >
    <template v-slot:control="{ id, floatingLabel, value, emitValue }">
      <money :id="id" class="q-field__input" :class="left ? 'text-left' : 'text-right'" :value="value" @input="emitValue" v-bind="moneyFormatForComponent" v-show="floatingLabel"  />
    </template>
  </q-field>
</div>
</template>

<script>
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'hint', 'max', 'min', 'left'
  ],
  data () {
    return {
      limitemax: 999999999999, // Number.MAX_VALUE,
      limitemin: 0,
      model: 0,
      model_en: 0,
      moneyFormatForComponent: {
        decimal: ',',
        thousands: '.',
        prefix: 'R$ ',
        suffix: '',
        precision: 2,
        masked: false
      },
      options: null,
      showing: false,
      updating: false
    }
  },
  async mounted () {
    var app = this
    if (app.max) app.limitemax = parseFloat(app.max)
    if (app.min) app.limitemin = parseFloat(app.min)
    if (app.value) {
      app.model = app.value
    }
  },
  watch: {
    model: async function (val) {
      var app = this
      if (app.updating) return
      app.updating = true
      var modelstr = val ? val.toString() : ''
      modelstr = app.$helpers.replaceAll(modelstr, app.moneyFormatForComponent.prefix, '').trim()
      modelstr = app.$helpers.replaceAll(modelstr, app.moneyFormatForComponent.suffix, '').trim()
      modelstr = app.$helpers.replaceAll(modelstr, app.moneyFormatForComponent.thousands, '').trim()
      modelstr = app.$helpers.replaceAll(modelstr, app.moneyFormatForComponent.decimal, '.').trim()
      app.model_en = parseFloat(modelstr, 2)
      app.$emit('input', app.model_en)
      app.$emit('updated')
      app.updating = false
    },
    value: async function (val) {
      var app = this
      if (app.updating) return
      app.updating = true
      app.model_en = val
      var modelstr = app.$helpers.formatRS(val, app.moneyFormatForComponent.prefix, app.moneyFormatForComponent.precision)
      app.model = modelstr
      app.updating = false
    },
    prefix: async function (val) {
      var app = this
      app.moneyFormatForComponent.prefix = val
    }
  },
  methods: {
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselect.$el.focus()
      })
    }
  }
}
</script>
