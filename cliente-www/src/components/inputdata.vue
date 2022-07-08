<template>
<div>
{{readonly}}
<q-input v-model="dFormatada" ref="txtdata" :readonly="readonly" :label="label" @change="onChange" maxlength="10"
  :outlined="outlined" :dense="dense"
  >
  <template v-slot:append v-if="!readonly">
    <q-btn icon='date_range' dense flat round>
      <q-popup-proxy ref='qDateProxy' @show='readOnly = true' @hide='readonly = false'>
        <q-date v-model='d' @input="$refs.qDateProxy.hide()" today-btn/>
      </q-popup-proxy>
    </q-btn>
    <q-btn icon='clear' dense flat round v-if="clearable" @click="actClear" />
  </template>
</q-input>
</div>
</template>

<script>
import moment from 'moment'
export default {
  props: [
    'label', 'value', 'outlined', 'dense', 'clearable', 'readonly'
  ],
  data: () => ({
    d: null,
    dFormatada: null
  }),
  mounted () {
    if (this.value) this.d = this.value
  },
  watch: {
    value: function (val) {
      this.d = val
      this.dFormatada = this.$helpers.dateToBR(this.d)
    },
    d: function (val) {
      // this.value = val
      this.dFormatada = this.$helpers.dateToBR(val)
      this.$emit('input', val)
    }
  },
  methods: {
    actClear () {
      this.dFormatada = null
      this.$emit('clear')
    },
    onChange (e) {
      moment.locale('pt-br')
      var str = e.target.value
      var dh = moment(str, 'DD/MM/YYYY')
      if (dh.isValid()) {
        this.d = dh.format('YYYY/MM/DD')
      } else {
        this.d = ''
      }
    },
    verplaatsFocus () {
      document.activeElement.blur()
      this.$refs.qDateProxy.show()
    },
    formatteerDatum (x) {
      return this.$helpers.dateToBR(x)
    }
  }
}
</script>
