<template>
<div>
  <q-form @submit="actSave"   >
  <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md" >
    <div class="col-12" v-if="action == 'add'">
      <q-input outlined  ref="txtemail" label="E-mail" stack-label type="mail" :dense="dense" v-model="email.email" :readonly="readonly"
        :rules="[
            val => !!val || '* Obrigatório',
            val => $helpers.validaEmail(val) || 'E-mail inválido',
          ]"
        lazy-rules
      />
    </div>
    <div class="col-12" v-if="action == 'update'">
      <div class="text-h6" >{{email.email}}</div>
      <div class="text-caption text-grey" v-if="email.clientescount ? (email.clientescount > 1) : false">Relacionado com outros <strong>{{email.clientescount}} clientes</strong></div>
    </div>
    <div class="col-12">
      <q-input  ref="txtnomecontato" outlined  label="Nome do contato" stack-label :dense="dense" v-model="email.nome" :readonly="readonly" />
    </div>
    <div class="col-12">
      <q-select label="Tags'" outlined v-model="email.tags" use-input stack-label  :dense="dense" :readonly="readonly"
        use-chips
        multiple
        hide-dropdown-icon
        input-debounce="0"
        new-value-mode="add-unique"
      />
    </div>
    <div class="col-12">
      <q-btn label="Salvar" type="submit" color="primary" v-if="!readonly" />
      <q-btn label="Voltar" color="primary" flat class="q-ml-sm" @click="actClose" />
    </div>
  </div>
  </q-form>
</div>
</template>
<script>
import Email from 'src/mvc/models/email.js'
export default {
  name: 'cpn-emailedit',
  components: {
  },
  props: {
    value: {
      type: Object,
      default: function () {
        return { message: 'hello' }
      }
    },
    dense: {
      type: Boolean,
      default: false
    },
    readonly: {
      type: Boolean,
      default: false
    },
    maxnome: { type: Number, default: 100 },
    maxemail: { type: Number, default: 100 }
  },
  data () {
    let email = new Email()
    return {
      email,
      loading: false,
      action: 'update'
    }
  },
  async mounted () {
    var app = this
    app.loading = true
    if (app.value) {
      await app.email.cloneFrom(app.value)
    }
    app.action = (app.email.id ? (app.email.id > 0 ? 'update' : 'add') : 'add')
    document.onkeydown = function (e) {
      if (e.key === 'Escape') {
        app.actClose()
      }
    }
    app.actOnFocus()
    app.loading = false
  },
  methods: {
    actOnFocus () {
      this.$nextTick(() => {
        setTimeout(() => {
          if (this.action === 'add') {
            this.$refs.txtemail.$el.focus()
          } else {
            this.$refs.txtnomecontato.$el.focus()
          }
        }, 20)
      })
    },
    actSave () {
      var app = this
      try {
        if (!app.$helpers.validaEmail(app.email.email)) throw new Error('E-mail inválido!')
      } catch (error) {
        app.$helpers.showDialog({ ok: false, msg: error.message })
        return
      }
      app.$emit('close', app.email)
    },
    actClose () {
      var app = this
      app.$emit('close')
    }
  }
}
</script>
