<template>
<div>
  <q-page class="">
    <div v-if="!$q.platform.is.mobile">
      <breadcrumbs v-model="breadcrumbslist"
      :showactive="{ label: (!loading ? (adding ? 'Novo cadastro' : (dataset ? dataset.descricao_old +  ' [id: ' + dataset.id + ']' : '?')) : 'Consultando') }"
      />
    </div>
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-sm-12 full-width" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pt-md'">
          <div v-if="!loading">
            <formedit :adding="adding" :idstart="idstart" @updated="actUpdated" />
          </div>
      </div>
    </div>
  </q-page>
</div>
</template>

<script>
import breadcrumbs from 'src/components/breadcrumbs'
import formedit from './edit-cpn.vue'

export default {
  name: 'perfilacesso.edit',
  components: {
    breadcrumbs,
    formedit
  },
  data () {
    return {
      loading: true,
      idstart: null,
      adding: false,
      dataset: false,
      breadcrumbslist: [
        { to: { name: 'cadastros' }, label: 'Cadastro' },
        { to: { name: 'cadastro.perfilacesso' }, label: 'Perfil de Acesso' }
      ]
    }
  },
  async mounted () {
    await this.init()
  },
  methods: {
    async actUpdated (dataset) {
      var app = this
      app.dataset = dataset
      if ((app.adding) && (app.dataset.id > 0)) {
        app.adding = false
        app.idstart = app.dataset.id
        app.loading = false
      }
    },
    async init () {
      var app = this
      app.adding = (this.$route.name === 'cadastro.perfilacesso.add')
      if (app.adding) {
        app.idstart = null
        app.loading = true
      } else {
        app.loading = true
        if (app.$route.params.id) {
          app.idstart = parseInt(app.$route.params.id)
        }
      }
      app.loading = false
    }
  }
}
</script>
