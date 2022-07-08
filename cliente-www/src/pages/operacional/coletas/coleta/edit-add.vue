  <template>
<div>
  <q-page class="">
    <div v-if="!$q.platform.is.mobile">
      <breadcrumbs v-model="breadcrumbslist"
      :showactive="{ label: (!loading ? 'Nova coleta' : 'Consultando') }"
      />
    </div>
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-sm-12 full-width" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pt-md'">
        <div v-if="!loading">
        <formedit :adding="true" :idstart="null"  @updated="actUpdated" />
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
  name: 'coleta.add',
  components: {
    breadcrumbs,
    formedit
  },
  data () {
    return {
      breadcrumbslist: [
        { to: { name: 'operacional.coletas.consulta' }, label: 'Coletas' }
      ],
      loading: false
    }
  },
  async mounted () {
  },
  methods: {
    async actUpdated (dataset) {
    }
  }
}
</script>
