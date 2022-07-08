<template>
<div>
  <q-select :dense="dense" :outlined="outlined" v-model="modelselect" input-debounce="300"
        :label="label" stack-label
        use-input
        clearable
        input-class="text-uppercase"
        ref="txtselect" class="full-width no-wrap"
        :options="options" map-options
        @filter="refreshData"
        @clear="actOnFocus"
        :loading="loading || loadingdata"
        :readonly="readonly"
        @dblclick="actOnFocus"
      >
        <template v-slot:selected-item="scope">
            <div  v-if="scope.opt ? scope.opt.itemid !== '' : false" class="ellipsis text-uppercase no-wrap"  >
              {{scope.opt.itemid }}
              <q-tooltip>
                <div>{{scope.opt.itemid}}</div>
              </q-tooltip>
            </div>
        </template>
        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" v-on="scope.itemEvents" dense class="border-bottom-separator full-width" >
            <q-item-section>
              <q-item-label>{{scope.opt.itemid}}</q-item-label>
            </q-item-section>
          </q-item>
        </template>
        <template v-slot:no-option>
          <q-item>
            <q-item-section class="text-grey">
              Sem resultados
            </q-item-section>
          </q-item>
        </template>
  </q-select>
</div>
</template>

<script>
import FollowUps from 'src/mvc/collections/followups.js'
export default {
  name: 'cnp.followup.filter.itemid',
  components: {
  },
  props: [
    'value', 'dense', 'outlined', 'label', 'clearable', 'nullabled', 'loading', 'readonly'
  ],
  data () {
    var dataset = new FollowUps()
    return {
      dataset,
      modelselect: null,
      modelold: null,
      options: null,
      showing: false,
      loadingdata: false
    }
  },
  async mounted () {
    this.init()
  },
  computed: {
    selecao: function () {
      var app = this
      if (!app.options) return false
      var sel = null
      for (let index = 0; index < app.options.length; index++) {
        const element = app.options[index]
        if (app.registroselecionado === element.itemid) {
          sel = element.dados
          break
        }
      }
      return sel
    }
  },
  watch: {
    modelselect: async function (val) {
      var app = this
      if (app.readonly) return
      app.$emit('input', val)
      app.$emit('updated')
    },
    'value.id': async function (val) {
      var app = this
      app.init()
    }
    // 'value.cidade': async function (val) {
    //   var app = this
    //   app.modelselect.cidade = val
    // },
    // 'value.estado': async function (val) {
    //   var app = this
    //   app.modelselect.estado = val
    // },
    // 'value.uf': async function (val) {
    //   var app = this
    //   app.modelselect.uf = val
    // }
  },
  methods: {
    async init () {
      var app = this
      if (app.value) {
        try {
          app.modelselect = app.value
          // if (!app.modelselect.id) throw new Error('Nenhum cliente informado 1')
          // if (!(app.modelselect.id > 0)) throw new Error('Nenhum cliente informado 2')
        } catch (error) {
          app.modelselect = null
        }
      }
    },
    actOnFocus () {
      var app = this
      app.modelselect = null
      this.$nextTick(() => {
        app.$refs.txtselect.$el.focus()
      })
    },
    async refreshData (val, update) {
      var app = this
      app.loadingdata = true
      var ret = await app.dataset.fetchlistitemid(val)
      if (ret.ok) {
        update(() => {
          this.options = []
          for (let index = 0; index < ret.data.length; index++) {
            const element = ret.data[index]
            app.options.push(element)
          }
        })
      }
      app.loadingdata = false
    }
  }
}
</script>
<style>
.border-bottom-separator {
  border-bottom: 1px solid #e4e4e4;
  padding-top: 3px;
  padding-bottom: 3px;
}
</style>
