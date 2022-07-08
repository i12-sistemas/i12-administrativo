<template>
<div>
  <div class="no-padding" style="width: 400px;">
    <div class="fit row wrap q-col-gutter-sm q-pa-sm">
      <div class="col-12" v-if="!loading">
        <div>
          <q-option-group v-model="status" type="radio" color="primary" :options="optionsstatus" size="sm" inline @input="actSendInput" />
        </div>
        <div v-if="status === '2'">
          <selectfollowuperros outlined label="Erro" nullabled :tipo="tipo"  v-model="erro" class="full-width" @input="actSendInput"  />
        </div>
        <div v-if="retornoerro ? retornoerro.ok : false" class="text-caption text-red">
          {{retornoerro.msg}}
        </div>

      </div>
    </div>
      </div>
</div>
</template>

<script>
import selectfollowuperros from 'src/components/cnp-select-followup-erros'
export default {
  components: {
    selectfollowuperros
  },
  props: [ 'value', 'tipo' ],
  data () {
    return {
      retornoerro: { ok: false, msg: '' },
      loading: true,
      status: '0',
      erro: null,
      optionsstatus: [
        {
          label: 'Sem status',
          value: '0',
          color: 'grey'
        },
        {
          label: 'OK',
          value: '1',
          color: 'green'
        },
        {
          label: 'Com erro',
          value: '2',
          color: 'red'
        }
      ]
    }
  },
  computed: {
    hasError () {
      return this.retornoerro.ok
    }
  },
  async mounted () {
    var app = this
    if (app.value) {
      if (typeof app.status !== 'undefined') app.status = app.value.status
      if (typeof app.erro !== 'undefined') app.erro = app.value.erro
    }
    app.checkError()
    app.loading = false
  },
  methods: {
    checkError () {
      var app = this
      try {
        if (typeof app.status === 'undefined') throw new Error('Selecione um status')
        if (!((app.status === '0') || (app.status === '1') || (app.status === '2'))) throw new Error('Selecione um status')
        if (app.status === '2') {
          if (typeof app.erro === 'undefined') throw new Error('Para o ')
          if (!app.erro) throw new Error('Obrigatório informar o erro do status')
          if (!app.erro.id) throw new Error('Obrigatório informar o erro do status')
          if (!(app.erro.id > 0)) throw new Error('Obrigatório informar o erro do status')
        } else {
          app.erro = null
        }
        app.retornoerro.ok = false
        app.retornoerro.msg = ''
        return false
      } catch (error) {
        app.retornoerro.ok = true
        app.retornoerro.msg = error.message
        return true
      }
    },
    async actSendInput (e) {
      var app = this
      try {
        await app.checkError()
        // if (temerro) return
        this.$emit('input', { status: app.status, erro: app.erro })
      } catch (error) {
        return false
      }
    }
  }
}
</script>
