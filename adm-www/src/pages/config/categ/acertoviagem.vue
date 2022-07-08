<template>
  <div>
    <q-card class="q-ma-sm" flat  >
      <q-card-section>
        <div class="fit row wrap q-col-gutter-md q-pb-md">

          <div class="col-12" v-if="loading">
            <q-card class="" flat >
              <q-card-section class="no-padding">
                <div class="col-12" >
                  <q-card class="" flat >
                    <q-card-section  class="full-width text-center">
                      <q-spinner color="blue" size="3rem" :thickness="5" />
                      <div class="text-blue q-pa-md">Carregando...</div>
                    </q-card-section>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>
          </div>

          <!-- dados de base -->
          <div class="col-12" v-if="!loading">
            <q-card class="" flat >
              <q-card-section class="no-padding">
                <div class="col-12" >
                  <q-card class="" flat bordered >
                    <q-card-section >
                      <div class="fit row wrap q-col-gutter-sm">

                        <div class="col-md-4 col-xs-12 self-center">
                          Horário limite para cálculo
                        </div>
                        <div class="col-md-2 col-xs-12">
                          <q-input v-model="configuracoes.itens.acerto_cafe_horario.valor" mask="time" label="Café" stack-label
                            :rules="[ val => (val && /^([0-1]?\d|2[0-3]):[0-5]\d$/.test(val)) || !val ]" outlined :dense="!$q.platform.is.mobile">
                            <template v-slot:append>
                              <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy transition-show="scale" transition-hide="scale">
                                  <q-time v-model="configuracoes.itens.acerto_cafe_horario.valor" />
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                        </div>
                        <div class="col-md-2 col-xs-12">
                          <q-input v-model="configuracoes.itens.acerto_almoco_horario.valor" mask="time" label="Almoço" stack-label
                            :rules="[ val => (val && /^([0-1]?\d|2[0-3]):[0-5]\d$/.test(val)) || !val ]" outlined :dense="!$q.platform.is.mobile">
                            <template v-slot:append>
                              <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy transition-show="scale" transition-hide="scale">
                                  <q-time v-model="configuracoes.itens.acerto_almoco_horario.valor" />
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                        </div>
                        <div class="col-md-2 col-xs-12">
                          <q-input v-model="configuracoes.itens.acerto_jantar_horario.valor" mask="time" label="Jantar" stack-label
                            :rules="[ val => (val && /^([0-1]?\d|2[0-3]):[0-5]\d$/.test(val)) || !val ]" outlined :dense="!$q.platform.is.mobile">
                            <template v-slot:append>
                              <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy transition-show="scale" transition-hide="scale">
                                  <q-time v-model="configuracoes.itens.acerto_jantar_horario.valor" />
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                        </div>
                        <div class="col-md-2 col-xs-12">
                          <q-input v-model="configuracoes.itens.acerto_pernoite_horario.valor" mask="time" label="Pernoite" stack-label
                            :rules="[ val => (val && /^([0-1]?\d|2[0-3]):[0-5]\d$/.test(val)) || !val ]" outlined :dense="!$q.platform.is.mobile">
                            <template v-slot:append>
                              <q-icon name="access_time" class="cursor-pointer">
                                <q-popup-proxy transition-show="scale" transition-hide="scale">
                                  <q-time v-model="configuracoes.itens.acerto_pernoite_horario.valor" />
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                        </div>

                        <div class="col-md-4 col-xs-12 self-center">
                          Valores de base
                        </div>
                        <div class="col-md-2 col-xs-12">
                          <q-input outlined v-model="acerto_cafe_vlr" label="Café" stack-label :dense="!$q.platform.is.mobile"
                            mask="#,##" fill-mask="0" reverse-fill-mask input-class="text-left" prefix="R$" />
                        </div>
                        <div class="col-md-2 col-xs-12">
                          <q-input outlined v-model="acerto_almoco_vlr" label="Almoço" stack-label :dense="!$q.platform.is.mobile"
                            mask="#,##" fill-mask="0" reverse-fill-mask input-class="text-left" prefix="R$" />
                        </div>
                        <div class="col-md-2 col-xs-12">
                          <q-input outlined v-model="acerto_jantar_vlr" label="Jantar" stack-label :dense="!$q.platform.is.mobile"
                            mask="#,##" fill-mask="0" reverse-fill-mask input-class="text-left" prefix="R$" />
                        </div>
                        <div class="col-md-2 col-xs-12">
                          <q-input outlined v-model="acerto_pernoite_vlr" label="Pernoite" stack-label :dense="!$q.platform.is.mobile"
                            mask="#,##" fill-mask="0" reverse-fill-mask input-class="text-left" prefix="R$" :disable="loading" />
                        </div>
                      </div>
                    </q-card-section>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- dados de base -->

          <!-- Quadro de informações para impressão do "Relatório de Viagem" -->
          <div class="col-12" v-if="!loading">
            <q-card class="" flat >
              <q-card-section class="no-padding">
                <q-toolbar class="bg-grey-3 text-black" si>
                  <q-toolbar-title class="text-subtitle2">
                    Quadro de informações para impressão do "Relatório de Viagem"
                  </q-toolbar-title>
                </q-toolbar>
                <div class="col-12" >
                  <q-card class="" flat bordered >
                    <q-card-section >
                      <q-input v-model="configuracoes.itens.acerto_info_relviagem.valor" type="textarea" outlined label="informações para impressão do Relatório de Viagem" autogrow :dense="!$q.platform.is.mobile"
                       hint="Evite muitas linhas para não ocorrer quebra de página na impressão"/>
                    </q-card-section>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>
          </div>
          <!-- Quadro de informações para impressão do "Relatório de Viagem" -->

          <div class="col-12" v-if="!loading">
            <q-card class="" bordered flat >
              <q-toolbar class="bg-grey-3 text-black" >
                <q-toolbar-title class="text-subtitle2">
                  Impresão de Ficha de Liberação do Mototrista
                </q-toolbar-title>
              </q-toolbar>
              <q-card-section >
                  <div class="row wrap q-col-gutter-sm q-mb-xs">
                    <div class="col-md-4 col-xs-12 self-center">
                      Linhas para períodos
                    </div>
                    <div class="col-md-2 col-xs-12">
                      <q-input v-model="acerto_quadro_periodos_qtdelinha" label="Quantidade" type="number" min="0" max="99" stack-label outlined :dense="!$q.platform.is.mobile" />
                    </div>
                    <div class="col-md-6 col-xs-12 self-center text-caption text-grey">
                      <div>Limite 99 linhas</div>
                      <div>Se informado 0 (zero), o quadro será removido</div>
                    </div>
                  </div>
                  <div class="row wrap q-col-gutter-sm q-mb-xs">
                    <div class="col-md-4 col-xs-12 self-center">
                      Linhas para abastecimento
                    </div>
                    <div class="col-md-2 col-xs-12">
                      <q-input v-model="acerto_quadro_abast_qtdelinha" label="Quantidade" type="number" min="0" max="99" stack-label outlined :dense="!$q.platform.is.mobile" />
                    </div>
                    <div class="col-md-6 col-xs-12 self-center text-caption text-grey">
                      <div>Limite 99 linhas</div>
                      <div>Se informado 0 (zero), o quadro será removido</div>
                    </div>
                  </div>
                  <div class="row wrap q-col-gutter-sm q-mb-xs">
                    <div class="col-md-4 col-xs-12 self-center">
                      Linhas para manutenção
                    </div>
                    <div class="col-md-2 col-xs-12">
                      <q-input v-model="acerto_quadro_manutencao_qtdelinha" label="Quantidade" type="number" min="0" max="99" stack-label outlined :dense="!$q.platform.is.mobile" />
                    </div>
                    <div class="col-md-6 col-xs-12 self-center text-caption text-grey">
                      <div>Limite 99 linhas</div>
                      <div>Se informado 0 (zero), o quadro será removido</div>
                    </div>
                  </div>
              </q-card-section>
              <q-card-section >

                <!-- checklist -->
                <div class="col-12" v-if="!loading">
                  <q-card class="" bordered flat >
                    <q-toolbar class="bg-grey-3 text-black" si>
                      <q-toolbar-title class="text-subtitle2">
                        Checklist de liberação de veículo
                      </q-toolbar-title>
                      <q-btn flat :round="$q.platform.is.mobile" dense icon="add" class="q-mr-xs"
                        :label="$q.platform.is.mobile ? '' : 'Adicionar'" @click="actChecklistEdit('insert', null)" />
                    </q-toolbar>
                    <q-card-section class="no-padding" v-if="acerto_checklist ? acerto_checklist.length > 0 : false">
                      <q-table flat dense :data="acerto_checklist"
                        :columns="[
                                  { name: 'action', style: 'width: 90px', align: 'left', label: '*', field: 'id' },
                                  { name: 'item', align: 'left', label: 'Item', field: 'item' }
                                  ]"
                        row-key="id" :rows-per-page-options="[0]" hide-bottom >
                        <template v-slot:body-cell-item="props">
                          <q-td :props="props" @click.native="actChecklistEdit('update', props)" class="cursor-pointer">
                            {{props.row.item}}
                          </q-td>
                        </template>
                        <template v-slot:body-cell-action="props" >
                          <q-td :props="props">
                            <q-btn color="black" icon="edit" @click="actChecklistEdit('update', props)" round flat size="sm" />
                            <q-btn color="red" icon="delete" @click="actChecklistDelete(props)" round flat size="sm" />
                          </q-td>
                        </template>
                      </q-table>
                    </q-card-section>
                  </q-card>
                </div>
                <!-- checklist -->
              </q-card-section>
            </q-card>
          </div>

          <div class="col-12" v-if="!loading">
            <q-card class="" flat >
              <q-card-section class="no-padding">
                <div class="col-12" >
                  <q-card class="bg-grey-3 text-red-10" flat >
                    <q-card-section>
                      <div class="fit row wrap">
                        <div class="col-12 text-caption">
                          <div>As alterações feita aqui não refletem nos acertos cadastrados anteriormente.</div>
                          <div>Se necessário edite o acerto e marque para recalcular.</div>
                        </div>
                      </div>
                    </q-card-section>
                  </q-card>
                </div>
              </q-card-section>
            </q-card>
          </div>

        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import Configuracoes from 'src/mvc/collections/configuracoes.js'
import Vue from 'vue'
export default {
  name: 'config.categ.acertoviagem',
  components: {
  },
  data () {
    var configuracoes = new Configuracoes()
    return {
      configuracoes,
      loading: true,
      acerto_cafe_vlr: '0',
      acerto_almoco_vlr: '0',
      acerto_jantar_vlr: '0',
      acerto_pernoite_vlr: '0',
      acerto_checklist: [],
      acerto_quadro_periodos_qtdelinha: 0,
      acerto_quadro_abast_qtdelinha: 0,
      acerto_quadro_manutencao_qtdelinha: 0
    }
  },
  mounted () {
    var app = this
    app.setLoading(true)
    // app.configuracoes.additem (id, tipo, defaultvalue)
    app.configuracoes.additem('acerto_cafe_horario', 'time', '00:00')
    app.configuracoes.additem('acerto_almoco_horario', 'time', '00:00')
    app.configuracoes.additem('acerto_jantar_horario', 'time', '00:00')
    app.configuracoes.additem('acerto_pernoite_horario', 'time', '00:00')

    app.configuracoes.additem('acerto_cafe_vlr', 'double', 0)
    app.configuracoes.additem('acerto_almoco_vlr', 'double', 0)
    app.configuracoes.additem('acerto_jantar_vlr', 'double', 0)
    app.configuracoes.additem('acerto_pernoite_vlr', 'double', 0)

    app.configuracoes.additem('acerto_quadro_abast_qtdelinha', 'integer', 4)
    app.configuracoes.additem('acerto_quadro_periodos_qtdelinha', 'integer', 4)
    app.configuracoes.additem('acerto_quadro_manutencao_qtdelinha', 'integer', 4)

    app.configuracoes.additem('acerto_checklist', 'json', [])

    app.configuracoes.additem('acerto_info_relviagem', 'mediumtext', '')

    app.refreshdata()
  },
  methods: {
    async actChecklistDelete (pProps) {
      var app = this
      if (!pProps) return
      app.$q.dialog({
        title: 'Remover item checklist',
        message: pProps.row.item,
        cancel: true
      }).onOk(() => {
        app.acerto_checklist.splice(pProps.rowIndex, 1)
      }).onCancel(() => {

      })
      // var ret = app.dataset.rotaDelete(app, pProps.rowIndex)
      // await ret.then(function (value) {
      //   if (value) {
      //     if (!value.ok) {
      //       if (value.msg) {
      //         if (value.msg !== '') app.$helpers.showDialog(value)
      //       }
      //     }
      //   }
      //   app.deleting = false
      // })
    },
    async actChecklistEdit (pAcao, pProps) {
      var app = this
      var sug = ''

      if (pAcao !== 'insert') sug = pProps.row.item
      // } else {
      //   ret = app.dataset.rotaAddOrEdit(app, pProps.rowIndex, pProps.row)
      // }

      app.$q.dialog({
        title: pAcao === 'insert' ? 'Novo item' : 'Alterar item',
        prompt: {
          model: sug,
          type: 'text' // optional
        },
        cancel: true
      }).onOk(async data => {
        var itemstr = data.trim()
        if (pAcao === 'insert') {
          app.acerto_checklist.push({ id: app.acerto_checklist.length, item: itemstr })
        } else {
          app.acerto_checklist[pProps.rowIndex].item = itemstr
        }
      })
    },
    async savedata () {
      var app = this
      app.setLoading(true)
      app.$emit('saving', true)
      app.configuracoes.itens.acerto_cafe_vlr.valor = Vue.prototype.$helpers.strToCurr(app.acerto_cafe_vlr)
      app.configuracoes.itens.acerto_almoco_vlr.valor = Vue.prototype.$helpers.strToCurr(app.acerto_almoco_vlr)
      app.configuracoes.itens.acerto_jantar_vlr.valor = Vue.prototype.$helpers.strToCurr(app.acerto_jantar_vlr)
      app.configuracoes.itens.acerto_pernoite_vlr.valor = Vue.prototype.$helpers.strToCurr(app.acerto_pernoite_vlr)

      app.configuracoes.itens.acerto_quadro_periodos_qtdelinha.valor = app.acerto_quadro_periodos_qtdelinha
      app.configuracoes.itens.acerto_quadro_abast_qtdelinha.valor = app.acerto_quadro_abast_qtdelinha
      app.configuracoes.itens.acerto_quadro_manutencao_qtdelinha.valor = app.acerto_quadro_manutencao_qtdelinha

      app.configuracoes.itens.acerto_checklist.valor = []
      for (let index = 0; index < app.acerto_checklist.length; index++) {
        const element = app.acerto_checklist[index]
        app.configuracoes.itens.acerto_checklist.valor.push(element.item)
      }

      var ret = await app.configuracoes.save()
      app.$emit('saved', ret)
      if (ret.ok) {
        app.refreshdata()
      } else {
        app.setLoading(false)
      }
      app.$emit('saving', false)
    },
    setLoading (val) {
      this.loading = val
      this.$emit('loading', this.loading)
    },
    async refreshdata () {
      var app = this
      app.setLoading(true)
      var ret = await app.configuracoes.fetch()
      if (ret.ok) {
        app.acerto_cafe_vlr = app.configuracoes.itens.acerto_cafe_vlr.valorAsDouble(2)
        app.acerto_almoco_vlr = app.configuracoes.itens.acerto_almoco_vlr.valorAsDouble(2)
        app.acerto_jantar_vlr = app.configuracoes.itens.acerto_jantar_vlr.valorAsDouble(2)
        app.acerto_pernoite_vlr = app.configuracoes.itens.acerto_pernoite_vlr.valorAsDouble(2)

        app.acerto_quadro_periodos_qtdelinha = app.configuracoes.itens.acerto_quadro_periodos_qtdelinha.valor
        app.acerto_quadro_abast_qtdelinha = app.configuracoes.itens.acerto_quadro_abast_qtdelinha.valor
        app.acerto_quadro_manutencao_qtdelinha = app.configuracoes.itens.acerto_quadro_manutencao_qtdelinha.valor

        app.acerto_checklist = []
        for (let index = 0; index < app.configuracoes.itens.acerto_checklist.valor.length; index++) {
          const element = app.configuracoes.itens.acerto_checklist.valor[index]
          app.acerto_checklist.push({ id: index, item: element })
        }
      }
      app.setLoading(false)
    }
  }

}
</script>
