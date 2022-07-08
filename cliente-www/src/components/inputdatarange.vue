<template>
<div>
  <div class="row q-col-gutter-md items-center full-width">
    <div class="col" style="min-width: 200px">
      <inputdata v-model="i" :label="'Data inicial'"  />
    </div>
    <div class="col" style="min-width: 200px">
      <inputdata v-model="f" :label="'Data final'"  />
    </div>
    <div class="col-2 " v-if="(f || i)">
      <q-btn color="black" icon="clear" flat dense round @click="actClear" />
    </div>
  </div>
</div>
</template>

<script>
import inputdata from 'src/components/inputdata'
import { date } from 'quasar'

export default {
  components: {
    inputdata
  },
  props: [
    'value'
  ],
  data: () => ({
    readOnly: false,
    i: null,
    f: null,
    model: { i: null, f: null }
  }),
  mounted () {
    if (this.value) {
      if (this.value.i) this.i = this.value.i
      if (this.value.f) this.f = this.value.f
    }
  },
  watch: {
    i: function (val) {
      this.model.i = val
      this.$emit('input', this.model)
    },
    f: function (val) {
      this.model.f = val
      this.$emit('input', this.model)
    },
    value: function (val) {
      if (val) {
        this.i = val.i
        this.f = val.f
      }
    }
  },
  methods: {
    actClear () {
      this.i = null
      this.f = null
    },
    hoje () {
      let now = new Date()
      this.i = date.formatDate(now, 'YYYY/MM/DD')
      this.f = date.formatDate(now, 'YYYY/MM/DD')
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
