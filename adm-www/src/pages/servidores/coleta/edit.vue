  <template>
<div>
  <q-page class="">
    <div v-if="!$q.platform.is.mobile">
      <breadcrumbs v-model="breadcrumbslist"
      :showactive="{ label: (!loading ? (idstart ? 'Coleta #' + idstart : '?') : 'Consultando') }"
      />
    </div>
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-sm-12 full-width" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pt-md'">
        <div v-if="!loading">
        <formedit :adding="false" :idstart="idstart"  @updated="actUpdated" />
        </div>
      </div>
    </div>
  </q-page>
</div>
</template>

<script>
import breadcrumbs from 'src/components/breadcrumbs'
import formedit from './edit-cnp'
export default {
  name: 'coleta.edit',
  components: {
    breadcrumbs,
    formedit
  },
  data () {
    return {
      idstart: null,
      showadd: false,
      dataset: null,
      expanded: { remetente: false, destinatario: false, enderecocoleta: true, geral: true, produtos: false },
      saving: false,
      breadcrumbslist: [
        { to: { name: 'operacional.coletas.consulta' }, label: 'Coletas' }
      ],
      loading: true
    }
  },
  async mounted () {
    var app = this
    await app.init()
  },
  methods: {
    async actUpdated (dataset) {
      var app = this
      app.dataset = dataset
      if (app.dataset.id > 0) {
        app.idstart = app.dataset.id
        app.loading = false
      }
    },
    async init () {
      var app = this
      app.loading = true
      if (app.$route.params.id) {
        app.idstart = parseInt(app.$route.params.id)
      }
      app.loading = false
    }
  }
}
</script>
