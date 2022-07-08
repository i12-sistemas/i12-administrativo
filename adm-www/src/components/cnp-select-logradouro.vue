<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="model" input-debounce="0"
        label="Logradouro" stack-label :clearable="clearable"
        ref="txtselect" class="full-width"
        :options="options" use-input hide-selected fill-input
        clear-icon="clear"
        map-options
         @filter="filterFnAutoselect"
        @filter-abort="abortFilterFn"
        :readonly="readonly"
      >
    </q-select>
</div>
</template>

<script>
import Logradouros from 'src/assets/utils/logradouros.js'
export default {
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'modeshow', 'readonly'
  ],
  data () {
    let logradouros = new Logradouros()
    return {
      logradouros,
      model: null,
      options: null,
      showing: false,
      loading: false
    }
  },
  async mounted () {
    var app = this
    if (app.value) {
      app.model = app.value
    }
  },
  watch: {
    model: async function (val) {
      var app = this
      app.$emit('input', val)
      app.$emit('updated')
    },
    value: async function (val) {
      var app = this
      app.model = val
    }
  },
  methods: {
    filterFnAutoselect (val, update, abort) {
      var app = this
      // call abort() at any time if you can't retrieve data somehow
      setTimeout(() => {
        update(() => {
          if (val === '') {
            this.options = app.logradouros.lista
          } else {
            const needle = val.toLowerCase()
            this.options = app.logradouros.lista.filter(function (element) {
              return (element.toLowerCase().indexOf(needle) > -1)
            })
          }
        },
        // next function is available in Quasar v1.7.4+;
        // "ref" is the Vue reference to the QSelect
        ref => {
          if (val !== '' && ref.options.length > 0 && ref.optionIndex === -1) {
            ref.moveOptionSelection(1, true) // focus the first selectable option and do not update the input-value
            ref.toggleOption(ref.options[ref.optionIndex], true) // toggle the focused option
          }
        })
      }, 300)
    },
    abortFilterFn () {
    },
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtselect.$el.focus()
      })
    }
  }
}
</script>
