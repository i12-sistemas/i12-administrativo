<template>
<div>
    <div class="row q-ma-0" >
      <div class="col-sm-12">
        <q-table :data="rows" :columns="columns" dense flat :loading="loading"
          :rows-per-page-options="$qtable.rowsperpageoptions"
          pagination.sync="dataset.eventos.pagination" row-key="id" @request="refreshData" :filter="filter"
          >
          <template v-slot:body="props">
            <q-tr :props="props">
              <q-td v-for="col in props.cols" :key="col.name" :props="props" >
                <div v-if="col.name == 'tipo'"  style="max-width: 300px" class="q-pr-sm">
                  <q-btn color="grey-9" flat dense round @click="props.expand = !props.expand" :icon="props.expand ? 'expand_less' : 'expand_more'" class="q-mr-md" />
                  <span v-if="props.row.tipo === 'update'"><q-icon name="edit" size="18px"  class="q-mr-md" />Alteração <q-tooltip>Alteração do registro</q-tooltip></span>
                  <span v-else-if="props.row.tipo === 'insert'"><q-icon name="add" size="18px"  class="q-mr-md" />Novo<q-tooltip>Inclusão de novo registro</q-tooltip></span>
                  <span v-else-if="props.row.tipo === 'baixa'" class="text-green"><q-icon name="check_circle" size="18px"  class="q-mr-md" />Baixa<q-tooltip>Baixa da coleta</q-tooltip></span>
                  <span v-else-if="props.row.tipo === 'baixaundo'" class="text-orange"><q-icon name="undo" size="18px"  class="q-mr-md" />Reabertura<q-tooltip>Cancelamento de baixa da coleta</q-tooltip></span>
                  <span v-else-if="props.row.tipo === 'cancel'" class="text-red"><q-icon name="cancel" size="18px"  class="q-mr-md" />Cancelamento<q-tooltip>Cancelamento da coleta</q-tooltip></span>
                  <span v-else-if="props.row.tipo === 'cancelundo'" class="text-blue"><q-icon name="undo" size="18px"  class="q-mr-md" />Cancelamento desfeito<q-tooltip>Cancelamento da coleta desfeito, coleta restaurada!</q-tooltip></span>
                  <span v-else><q-icon name="help_outline" size="15px"  class="q-mr-md" /><q-tooltip>{{props.row.tipo}}</q-tooltip></span>
                  <q-badge class="q-ml-md" color="grey-3" text-color="black" :label="(props.row.data ? Object.keys(props.row.data).length : '')"  />
                </div>
                <div v-else-if="col.name == 'created_at'">
                  <div style="max-width: 300px"  class="q-pr-sm">
                    {{$helpers.datetimeRelativeToday(props.row.created_at)}}
                    <q-tooltip>{{$helpers.datetimeToBR(props.row.created_at)}}</q-tooltip>
                  </div>
                </div>
                <div v-else-if="col.name == 'detalhe'">
                    <div class="ellipsis wrap" style="max-width: 400px">
                      <div v-if="props.row.detalhe != ''">{{ props.row.detalhe }}</div>
                      <div v-if="props.row.data ? ((props.row.data.length > 0) && (props.row.data.length < 3)) : false">
                        <div v-for="(item, key) in props.row.data" :key="key">
                          <div class="text-weight-bold">{{key}}</div>
                          <div class="text-caption">
                            <div class="q-mx-md text-grey"><q-icon name="trending_flat" size="15px"  class="q-mr-md" /> <span>{{item.old}}</span></div>
                            <div class="q-mx-md"><q-icon name="done" size="15px"  class="q-mr-md" /> <span>{{item.new}}</span></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="props.row.data ?  Object.keys(props.row.data).length > 0 : false" class="text-caption">
                      {{ props.row.data ?  Object.keys(props.row.data).length + ' campos alterados': ''}}
                    </div>
                </div>
                <div v-else>
                {{ col.value }}
                </div>
              </q-td>
              <q-td auto-width>
              </q-td>
            </q-tr>
            <q-tr v-show="props.expand" :props="props">
              <q-td colspan="100%">
                <div class="full-width row wrap justify-start items-start content-start border-delimiter-black">
                  <div  class="full-width row wrap justify-start items-start content-start text-weight-bold">
                    <div class="col-3">Campo alterado</div>
                    <div class="col-4">De</div>
                    <div class="col-5">Para</div>
                  </div>
                  <div v-for="(item, key) in props.row.data" :key="key" class="full-width row wrap justify-start items-start content-start text-caption">
                    <div class="col-3">{{key}}</div>
                    <div class="col-4"><span class="text-grey">{{item.old}}</span></div>
                    <div class="col-5"><span class="text-black">{{item.new}}</span></div>
                  </div>
                </div>
              </q-td>
            </q-tr>
          </template>
            <!-- <template v-slot:body-cell-tipo="props">
              <q-td :props="props" >
                <div v-if="props.row.tipo === 'update'"><q-icon name="edit" size="15px"  class="q-mr-md" /><q-tooltip>Alteração</q-tooltip></div>
                <div v-else-if="props.row.tipo === 'insert'"><q-icon name="add" size="15px"  class="q-mr-md" /><q-tooltip>Alteração</q-tooltip></div>
                <div v-else><q-icon name="help_outline" size="15px"  class="q-mr-md" /><q-tooltip>{{props.row.tipo}}</q-tooltip></div>
              </q-td>
            </template>
            <template v-slot:body-cell-created_at="props">
              <q-td :props="props" >
                <div>
                  {{ $helpers.datetimeRelativeToday(props.row.created_at) }}
                  <q-tooltip>{{ $helpers.datetimeToBR(props.row.created_at) }}</q-tooltip>
                </div>
              </q-td>
            </template>
            <template v-slot:body-cell-detalhe="props">
              <q-td :props="props" >

              </q-td>
            </template> -->
        </q-table>
      </div>
    </div>
</div>
</template>

<script>
export default {
  name: 'coletas.eventos.list',
  components: {
  },
  props: [ 'dataset' ],
  data () {
    return {
      rows: [],
      filter: '',
      columns: [
        { name: 'tipo', align: 'left', label: 'Ação', field: 'tipo' },
        { name: 'created_at', align: 'left', label: 'Data', field: 'created_at', sortable: true },
        { name: 'usuario', align: 'left', label: 'Usuário', field: row => row.created_usuario.nome },
        { name: 'detalhe', align: 'left', label: 'Detalhe', field: 'detalhe' }
      ],
      loading: false,
      posting: false,
      msgError: ''
    }
  },
  async mounted () {
    var app = this
    await app.refreshData(null)
  },

  methods: {
    async refreshData (props) {
      var app = this
      app.loading = true
      app.msgError = ''
      app.dataset.eventos.readPropsTable(props)
      var ret = await app.dataset.eventos.fetch(app.dataset.id)
      if (ret.ok) {
        app.rows = app.dataset.eventos.itens
      } else {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
      app.loading = false
    }
  }
}
</script>

<style lang="stylus">
</style>
