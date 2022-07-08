<template>
<div class="row no-wrap q-gutter-xs">
  <div class="col-6">
      <q-input v-model="dFormatada" ref="txtdata" :readonly="readonly" :label="label" @change="onChange" maxlength="10"
      :outlined="outlined" :dense="dense" :stack-label="stacklabel"
      >
      <template v-slot:append v-if="!readonly">
        <q-btn icon='date_range' dense flat round>
          <q-popup-proxy ref='qDateProxy'>
            <q-date v-model='d' @input="$refs.qDateProxy.hide()" today-btn/>
          </q-popup-proxy>
        </q-btn>
        <q-btn icon='clear' dense flat round v-if="clearable" @click="actClear" />
      </template>
      <q-tooltip :delay="700">
        <div v-if="!d">Nenhuma data informada</div>
        <div v-if="d">{{ $helpers.datetimeRelativeToday(d) }}</div>
        <div v-if="d">{{ $helpers.datetimeFormat(d, 'LL') }}</div>
      </q-tooltip>
    </q-input>
  </div>
  <div class="col-5">
    <q-input v-model="t" mask="time" :rules="[ val => (val && /^([0-1]?\d|2[0-3]):[0-5]\d$/.test(val)) || !val ]" outlined :dense="!$q.platform.is.mobile" :label="timelabel" stack-label>
      <template v-slot:append>
        <q-icon name="access_time" class="cursor-pointer">
          <q-popup-proxy transition-show="scale" transition-hide="scale">
            <q-time v-model="t" />
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
  </div>
</div>
</template>

<script>
import moment from 'moment'
export default {
  props: [
    'label', 'value', 'outlined', 'dense', 'clearable', 'stacklabel', 'readonly', 'timelabel'
  ],
  data: () => ({
    d: null,
    dFormatada: null,
    t: null
  }),
  mounted () {
    if (this.value) this.d = moment(this.value, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')
    if (this.value) this.t = moment(this.value, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')
  },
  watch: {
    // value: function (val) {
    //   this.d = val
    //   this.dFormatada = this.$helpers.datetimeToBR(this.d)
    // },
    d: function (val) {
      // this.value = val
      this.dFormatada = this.$helpers.dateToBR(val)
      var r = moment(val + (this.t ? ' ' + this.t : ' 00:00:00'), 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
      this.$emit('input', r)
    },
    t: function (val) {
      var r = moment(this.d + ' ' + val, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
      this.$emit('input', r)
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
