<template>
<q-input :value="computedDatum" :readonly="readOnly"  :label="label">
  <template v-slot:append>
    <q-btn icon='today' @click="hoje" dense flat round />
    <q-btn icon='date_range' dense flat round>
      <q-popup-proxy ref='qDateProxy' @show='readOnly = true' @hide='readOnly = false'>
        <q-date v-model='displayValue' @input="$refs.qDateProxy.hide()" today-btn/>
      </q-popup-proxy>
    </q-btn>
  </template>
</q-input>
</template>

<script>
import { date } from 'quasar'

export default {
  props: [
    'label',
    'value'
  ],
  data: () => ({
    readOnly: false
  }),
  mounted () {
  },
  computed: {
    computedDatum: {
      get: function () {
        return this.formatteerDatum(this.displayValue)
      },
      set: function () {
        this.displayValue = null
      }
    },
    displayValue: {
      get: function () {
        return date.formatDate(this.value, 'YYYY/MM/DD')
      },
      set: function (modifiedValue) {
        this.$emit('input', modifiedValue)
      }
    }
  },
  methods: {
    hoje () {
      let now = new Date()
      this.$emit('input', date.formatDate(now, 'YYYY/MM/DD'))
    },
    verplaatsFocus () {
      document.activeElement.blur()
      this.$refs.qDateProxy.show()
    },
    formatteerDatum (x) {
      return date.formatDate(x, 'DD/MM/YYYY')
    }
  }
}
</script>
