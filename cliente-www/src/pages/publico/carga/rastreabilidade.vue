<template>
  <q-layout view="hHh lpR lff" class="bg-grey-1" >
    <q-header class="bg-primary q-mb-md gradientToolbar">
      <div class="full-width row justify-center " :class="$q.platform.is.mobile ? '' : 'full-height items-center'">
        <div class="column col-xs-12 col-sm-11 col-lg-8 col-xl-8 bg-white">
          <div class="row full-width">
            <q-toolbar :class="$q.platform.is.mobile ? 'bg-primary text-white' : 'text-black'">
              <q-toolbar-title shrink class="row items-center no-wrap cursor-pointer" >
                <img src="~assets/Logo-i12-horizontal150x50.png" style="max-height: 40px;"  v-if="$q.platform.is.mobile">
                <img src="~assets/Logo-i12-horizontal150x50.png" style="max-height: 30px;"  v-if="!$q.platform.is.mobile">
              </q-toolbar-title>
              <q-separator spaced inset vertical v-if="!$q.platform.is.mobile" />
              <q-toolbar-title v-if="!$q.platform.is.mobile">
                Rastreamento de Carga
              </q-toolbar-title>
              <q-space />
              <q-btn color="primary" icon="youtube_searched_for" label="Nova consulta" @click="actClear"  flat/>
              <q-btn color="primary" icon="home" label="Início" :to="{ name: 'home' }" flat/>
            </q-toolbar>
            <q-toolbar class="bg-deep-orange-8 text-white" v-if="$q.platform.is.mobile">
              <q-toolbar-title shrink class="text-center text-subtitle2 full-width">
                Rastreamento de Carga
              </q-toolbar-title>
              <q-space />
            </q-toolbar>
          </div>
        </div>
      </div>
    </q-header>
    <q-page-container class="bg-primary">
      <q-page class="">
      <div class="full-width row justify-center " :class="$q.platform.is.mobile ? '' : 'full-height items-center'">
        <div class="column col-xs-12 col-sm-11 col-lg-8 col-xl-8 bg-white" style="border-radius: 0 0 12px 12px;">
          <div class="row full-width justify-center items-center">
            <q-card class="q-pa-none q-my-md" flat bordered v-if="!dataresumo" >
              <q-card-section class="text-center q-py-md" v-if="!loading">
                <div class="full-width row items-center">
                  <div class="col-xs-12 col-lg-4  text-left text-body text-grey-7">Rastrear por</div>
                  <div class="col-xs-12 col-lg-8  text-left">
                    <q-option-group v-model="dadosconsulta.modo" :options="optionsconsulta" color="primary" inline />
                  </div>
                </div>
              </q-card-section>
              <q-separator />
              <q-card-section class="text-center q-py-md" v-if="(dadosconsulta.modo ? dadosconsulta.modo !== '' : false) && !loading">
                <div class="row justify-center items-center" v-if="dadosconsulta.modo === 'chave'">
                  <div class="col-12">
                    <q-input v-model="dadosconsulta.chave" type="phone" label="Chave" maxlength="44" ref="txtchave" />
                  </div>
                </div>
                <div class="row justify-center items-center" v-else>
                  <div class="col-12">
                    <q-input v-model="dadosconsulta.numero" type="number" label="Número da nota" maxlength="11" ref="txtnumero" />
                  </div>
                  <div class="col-12">
                    <q-input v-model="dadosconsulta.cnpjemitente" type="tel" label="CNPJ emitente" maxlength="14" debounce="500" ref="txtcnpjemitente"
                      hint="CNPJ obrigatório (Somente números) - 14 dígitos"
                      counter
                      :rules="[
                          val => !!val || '* Obrigatório',
                          val => $helpers.validarCNPJ(val) || 'CNPJ inválido',
                        ]"
                      lazy-rules

                    />
                  </div>
                  <div class="col-12">
                    <q-input v-model="dadosconsulta.cnpjdestinatario" type="tel" label="CNPJ/CPF destinatário" maxlength="14" debounce="500" ref="txtcnpjdestinatario"
                      hint="Documento obrigatório - 11 ou 14 dígitos"
                        counter
                        :rules="[
                            val => !!val || '* Obrigatório',
                            val => $helpers.validarCNPJCPF(val) || 'Documento inválido',
                          ]"
                        lazy-rules
                    />
                  </div>
                </div>
              </q-card-section>
              <q-card-section class="text-center q-py-md" v-if="!loading">
                <div class="full-width row justify-center items-center">
                  <div v-if="recaptchaloading">carregando...</div>
                  <vue-recaptcha :sitekey="$configini.RECAPTCHA_KEY" @verify="verify" :loadRecaptchaScript="true" @render="rendered" ></vue-recaptcha>
                </div>
              </q-card-section>
              <q-card-actions vertical align="center">
                <q-btn :color="allowConsulta ? 'primary' : 'grey-6'" :unelevated="allowConsulta" :outline="!allowConsulta" @click="actConsulta"
                  label="Rastrear carga" class="full-width q-py-md" size="lg" icon="travel_explore" stack :disabled="!allowConsulta" />
                <q-btn flat label="Limpar consulta" icon="clear" @click="actClear" />
              </q-card-actions>
            </q-card>

            <q-card class="my-card q-mb-lg" v-if="(dataresumo || eventos) && !loading" flat bordered>
              <!-- processos -->
              <q-card-section v-if="dataresumo" >
                <div class="row text-h6 q-mb-sm">

                </div>
                <div class="row q-col-gutter-x-xs">
                  <div class="col-2 text-center" v-for="(res, k) in dataresumo" :key="'resumo' + k">
                    <q-card class="my-card full-height" bordered flat >
                      <q-card-section class="q-pa-xs" v-if="loading">
                        <div class="text-body text-weight-bold ellipsis full-width">
                          Consultando...
                        </div>
                      </q-card-section>
                      <q-card-section class="q-pa-xs" v-if="!loading">
                        <div class="text-body text-weight-bold ellipsis full-width">
                          {{res.title}}
                          <q-tooltip :delay="700">{{res.title}}</q-tooltip>
                        </div>
                      </q-card-section>
                      <q-separator />
                      <q-card-section v-if="!loading">
                        <q-avatar size="60px" font-size="52px" color="white" text-color="grey-3" icon="cancel_presentation"
                          v-if="!getStatus(res) && ((res.type === 'orcamento') || (res.type === 'transferencias'))"
                          />
                        <q-avatar v-if="!getStatus(res) && (!((res.type === 'orcamento') || (res.type === 'transferencias')))" size="60px" font-size="52px" color="grey-3" text-color="grey-9" icon="hourglass_empty" />
                        <div v-if="!getStatus(res)" class="q-mt-md">
                          <div v-if="(res.type === 'orcamento') || (res.type === 'transferencias')" class="text-grey">
                            Processo ignorado
                          </div>
                          <div v-else class="text-grey-7">Aguardando</div>
                        </div>
                        <q-avatar v-if="getStatus(res) === 'ok'" size="60px" font-size="52px" color="positive" text-color="white" icon="check" />
                        <q-avatar v-if="getStatus(res) === 'stop'" size="60px" font-size="52px" color="amber" text-color="white" icon="double_arrow" />
                        <q-avatar v-if="getStatus(res) === 'error'" size="60px" font-size="52px" color="red" text-color="white" icon="clear" />
                      </q-card-section>
                      <q-separator v-if="temDados(res) && !loading" />
                      <q-card-section v-if="temDados(res) && !loading" class="text-caption" >
                        <div v-if="res.type === 'orcamento' && temDados(res)">
                          <div>{{$helpers.datetimeToBR(res.data.dhlocal_data, true, false)}}</div>
                          <div class="text-weight-bold">Núm.: {{$helpers.formatMilhar(res.data.id)}}</div>
                        </div>
                        <div v-if="res.type === 'nfe' && temDados(res)">
                          <div>{{$helpers.dateToBR(res.data.dhlocal_data)}}</div>
                          <div>Nº: {{res.data.notanumero}}</div>
                        </div>
                        <div v-if="res.type === 'coleta' && temDados(res)">
                          <div v-if="res.data.created_at">{{$helpers.dateToBR(res.data.created_at)}}</div>
                          <div>Nº: {{res.data.id}}</div>
                        </div>
                        <div v-if="res.type === 'entrada'">
                          <div >{{$helpers.dateToBR(res.data.created_at)}}</div>
                          <div>Nº: {{res.data.id}}</div>
                        </div>
                        <div v-if="res.type === 'entrega'">
                          <div >{{$helpers.dateToBR(res.data.created_at)}}</div>
                          <div>Nº: {{res.data.id}}</div>
                        </div>
                        <div v-if="res.type === 'transferencias'">
                          <div v-if="res.data.length === 1">{{$helpers.dateToBR(res.data[0].created_at)}}</div>
                          <div v-if="res.data.length === 1">Nº: {{res.data[0].id}}</div>
                          <div v-if="res.data.length > 1">Quantidade</div>
                          <div v-if="res.data.length > 1">{{res.data.length + ' itens'}}</div>
                        </div>
                      </q-card-section>
                    </q-card>
                  </div>
                </div>
              </q-card-section>
              <!-- processos -->
              <!-- timeline -->
              <q-separator spaced v-if="eventos"  />
              <q-card-section v-if="eventos">
                <div class="text-h6 q-mb-sm">Linha do tempo</div>
                <div v-if="!loading">
                  <q-timeline layout="comfortable" side="left" color="secondary">
                    <q-timeline-entry v-for="(event, k) in eventos" :key="'event' + k"
                      :title="event.title"
                      side="left"
                      :icon="iconEvent(event).name"
                      :color="iconEvent(event).color"
                    >
                      <template v-slot:subtitle>
                        <div>
                          {{$helpers.datetimeFormat(event.dh, 'ddd') + ', ' +  $helpers.datetimeToBR(event.dh, false, true)}}
                        </div>
                        <div v-if="k === 0">
                          {{$helpers.datetimeRelativeToday(event.dh)}}
                        </div>
                        <div v-if="(k > 0) ? eventos[0].dh < event.dh : false">
                          HÁ
                          <span v-if="$helpers.diffTime(eventos[0].dh, event.dh, 'seconds') > 86400">
                            {{$helpers.diffDate(eventos[0].dh, event.dh, 'days')}} dias
                          </span>
                          <span v-else-if="$helpers.diffTime(eventos[0].dh, event.dh, 'seconds') > 3600">
                            {{$helpers.diffTime(eventos[0].dh, event.dh, 'hours')}} horas
                          </span>
                          <span v-else-if="$helpers.diffTime(eventos[0].dh, event.dh, 'seconds') > 60">
                            {{$helpers.diffTime(eventos[0].dh, event.dh, 'minutes')}} minutos
                          </span>
                          <span v-else>
                            {{$helpers.diffTime(eventos[0].dh, event.dh, 'seconds')}} segundos
                          </span>

                        </div>
                      </template>
                      <div v-if="event.text ? event.text !== '' : false">
                        <span v-html="event.text"></span>
                      </div>
                    </q-timeline-entry>
                  </q-timeline>
                </div>
              </q-card-section>
              <q-card-actions vertical align="center">
                <q-btn color="primary" icon="youtube_searched_for" label="Nova consulta" @click="actClear"  unelevated/>
              </q-card-actions>
              <!-- timeline -->
            </q-card>

          </div>
        </div>
      </div>

      </q-page>
    </q-page-container>
    <q-footer  >
      <div class="bg-grey-2 text-grey-8 text-caption q-pa-xs text-center q-mt-md">
        <div><span class="text-weight-bold">{{appPackage.productName}} :: {{appPackage.description}}</span> | Versão {{appPackage.version}} release {{$helpers.datetimeToBR(appPackage.releasedatetime)}}</div>
        <div>© Copyright {{year ? year : ''}} :: <a href="http://www.i12.com.br" target="_blank">i12.com.br</a></div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script>
// import axios from 'axios'
import VueRecaptcha from 'vue-recaptcha'
import moment from 'moment'
import datapackage from '../../../../package.json'
import ColetaNota from 'src/mvc/models/coletanota.js'
export default {
  name: 'publico.coleta.rastreamento',
  components: { VueRecaptcha },
  data () {
    var dataset = new ColetaNota()
    return {
      eventos: null,
      dataresumo: null,
      dataset,
      title: 'Conecta Transportes - Rastreamento de Carga',
      dadosconsulta: {
        modo: 'chave',
        chave: '35220467380170000110550550007916321705264891',
        numero: '791693',
        cnpjemitente: '67380170000110',
        cnpjdestinatario: '13537735000362'
      },
      optionsconsulta: [
        {
          label: 'Chave da nota',
          value: 'chave'
        },
        {
          label: 'Dados da nota',
          value: 'dados'
        }
      ],
      token: null,
      recaptchaloading: true,
      recaptchatoken: null,
      loading: false,
      error: null,
      appPackage: datapackage,
      year: 2021,
      testeerro: false

    }
  },
  meta () {
    return {
      // this accesses the "title" property in your Vue "data";
      // whenever "title" prop changes, your meta will automatically update
      title: this.title
    }
    // title: this.title,
    // meta: {
    //   description: { name: 'description', content: 'Page 1' },
    //   keywords: { name: 'keywords', content: 'Quasar website' },
    //   equiv: { 'http-equiv': 'Content-Type', content: 'text/html; charset=UTF-8' },
    //   // note: for Open Graph type metadata you will need to use SSR, to ensure page is rendered by the server
    //   ogTitle: {
    //     name: 'og:title',
    //     // optional; similar to titleTemplate, but allows templating with other meta properties
    //     template (ogTitle) {
    //       return `${ogTitle} - My Website`
    //     }
    //   }
    // }
  },
  mounted () {
    var app = this
    app.year = moment().year()
    if (app.$route.query.modo) app.dadosconsulta.modo = app.$route.query.modo
    if ((app.dadosconsulta.modo !== 'chave') && (app.dadosconsulta.modo !== 'dados')) app.dadosconsulta.modo = 'chave'
    if (app.$route.query.chave) app.dadosconsulta.chave = app.$route.query.chave
    if (app.$route.query.cnpjemitente) app.dadosconsulta.cnpjemitente = app.$route.query.cnpjemitente
    if (app.$route.query.cnpjdestinatario) app.dadosconsulta.cnpjdestinatario = app.$route.query.cnpjdestinatario
    // app.init()
    app.recaptchaLoad()
  },
  computed: {
    allowConsulta: function () {
      var app = this
      var b = true
      try {
        if (app.loading) throw new Error('Em loading')
        if (!app.recaptchatoken) throw new Error('Sem recaptcha nota')
        if (app.dadosconsulta.modo === 'chave') {
          if (!app.dadosconsulta.chave) throw new Error('Sem chave')
          if (app.dadosconsulta.chave.length !== 44) throw new Error('Sem com menos de 44 digitos')
        } else if (app.dadosconsulta.modo === 'dados') {
          if (!app.dadosconsulta.numero) throw new Error('Sem numero')
          if (!(parseInt(app.dadosconsulta.numero) > 0)) throw new Error('Sem numero invalido')
          if (!app.dadosconsulta.cnpjemitente) throw new Error('Sem cnpjemitente')
          if (!app.$helpers.validarCNPJ(app.dadosconsulta.cnpjemitente)) throw new Error('Sem cnpjemitente')
          if (!app.dadosconsulta.cnpjdestinatario) throw new Error('Sem cnpjdestinatario')
          if (!app.$helpers.validarCNPJCPF(app.dadosconsulta.cnpjdestinatario)) throw new Error('Sem cnpjdestinatario')
        } else {
          throw new Error('modo invalida')
        }
      } catch (error) {
        b = false
      }
      return b
    }
  },
  methods: {
    temDados (item) {
      if (this.loading) return false
      switch (item.type) {
        case 'transferencias':
          return (item.data ? item.data.length > 0 : false)

        default:
          return (item.data ? item.data.id > 0 : false)
      }
    },
    getStatus (item) {
      var status = null
      switch (item.type) {
        case 'orcamento':
          if (item.data ? item.data.id > 0 : false) status = 'ok'
          break
        case 'nfe':
          if (item.data ? item.data.id > 0 : false) {
            if (item.data.baixanfestatus === 0) status = 'stop'
            if (item.data.baixanfestatus === 1) status = 'ok'
            if (item.data.baixanfestatus === 2) status = 'error'

            if (!item.data.xmlprocessado) {
              status = 'error'
            }
          }
          break
        case 'coleta':
          if (item.data ? item.data.id > 0 : false) {
            if (item.data.situacao.value === '0') status = 'stop'
            if (item.data.situacao.value === '1') status = 'stop'
            if (item.data.situacao.value === '2') status = 'ok'
            if (item.data.situacao.value === '3') status = 'error'
          }
          break
        case 'entrada':
          if (item.data ? item.data.id > 0 : false) {
            if (item.data.status.value === '1') status = 'stop'
            if (item.data.status.value === '2') status = 'ok'
          }
          break
        case 'entrega':
          if (item.data ? item.data.id > 0 : false) {
            if (['1', '11'].indexOf(item.data.status.value) >= 0) status = 'stop'
            if (['2', '3'].indexOf(item.data.status.value) >= 0) status = 'stop'
            if (['4', '41', '5'].indexOf(item.data.status.value) >= 0) status = 'ok'
          }
          break
        case 'transferencias':
          if (item.data ? item.data.length > 0 : false) {
            for (let index = 0; index < item.data.length; index++) {
              const transfer = item.data[index]
              if (['1', '2', '3'].indexOf(transfer.status.value) >= 0) status = 'stop'
              if (transfer.status.value === '4') status = 'ok'
            }
          }
          break
      }
      return status
    },
    iconEvent (event) {
      var app = this
      var icon = {
        name: 'circle',
        color: 'primary'
      }
      switch (event.type) {
        case 'orcamento':
          icon.name = 'price_check'
          break
        case 'nfe':
          icon.name = 'receipt_long'
          icon.color = 'blue'
          break
        case 'entrada':
          icon.name = 'directions'
          icon.color = 'blue-7'
          break
        case 'transfer':
          icon.name = 'repeat'
          icon.color = 'cyan-8'
          break
        case 'entrega':
          icon.name = 'local_shipping'
          icon.color = 'green'
          break
        case 'coleta':
          var coleta = null
          for (let index = 0; index < app.dataresumo.length; index++) {
            if (app.dataresumo[index].type === 'coleta') {
              coleta = app.dataresumo[index].data
              break
            }
          }
          if (coleta ? coleta.id > 0 : false) {
            icon.name = coleta.origem.icon
            icon.color = coleta.origem.color
          }
          break
      }
      return icon
    },
    rendered (id) {
      this.recaptchaloading = false
    },
    async verify (response) {
      this.recaptchatoken = response
      // if (this.recaptchatoken) this.init()
    },
    async recaptchaLoad () {
      // this.recaptcha = await load('6LeVEPgbAAAAAL4O461AuV7piPA85dfWO-EZv-Z5', {
      //   useRecaptchaNet: false,
      //   renderParameters: {
      //     container: 'containerrecaptcha'
      //   }
      // })
      // this.recaptcha.showBadge()
      // this.recaptchatoken = await this.recaptcha.execute('sendxml')
      // this.init()
    },
    actClear () {
      var app = this
      app.loading = true
      app.dataresumo = null
      app.eventos = null
      app.dadosconsulta.chave = ''
      app.dadosconsulta.numero = ''
      app.dadosconsulta.cnpjemitente = ''
      app.dadosconsulta.cnpjdestinatario = ''
      app.loading = false
      app.$nextTick(() => {
        if (app.dadosconsulta.modo === 'chave') {
          app.$refs.txtchave.focus()
        } else {
          app.$refs.txtnumero.focus()
        }
      })
      var query = { t: new Date().getTime() }
      try {
        app.$router.replace({ name: app.$route.name, query: query, replace: true, append: false })
      } catch (error) {
        console.error(error)
      }
    },
    async actConsulta () {
      var app = this
      try {
        if (app.loading) throw new Error('Em loading')
        if (!app.recaptchatoken) throw new Error('Sem recaptcha nota')
        if (app.dadosconsulta.modo === 'chave') {
          if (!app.dadosconsulta.chave) throw new Error('Sem chave')
          if (app.dadosconsulta.chave.length !== 44) throw new Error('Sem com menos de 44 digitos')
        } else if (app.dadosconsulta.modo === 'dados') {
          if (!app.dadosconsulta.numero) throw new Error('Sem numero')
          if (!(parseInt(app.dadosconsulta.numero) > 0)) throw new Error('Sem numero invalido')
          if (!app.dadosconsulta.cnpjemitente) throw new Error('Sem cnpjemitente')
          if (!app.$helpers.validarCNPJ(app.dadosconsulta.cnpjemitente)) throw new Error('Sem cnpjemitente')
          if (!app.dadosconsulta.cnpjdestinatario) throw new Error('Sem cnpjdestinatario')
          if (!app.$helpers.validarCNPJCPF(app.dadosconsulta.cnpjdestinatario)) throw new Error('Sem cnpjdestinatario')
        }

        var ret = await app.dataset.findrastreioPublico(app.dadosconsulta, app.recaptchatoken)
        if (ret.ok) {
          app.dataresumo = []
          app.dataresumo.push({ title: 'Orçamento', type: 'orcamento', data: ret.data.orcamento ? ret.data.orcamento.id > 0 : null })

          var coleta = null
          if (ret.data.coleta ? ret.data.coleta.id > 0 : false) {
            coleta = ret.data.coleta
          }
          if (ret.data.nfe ? ret.data.nfe.id > 0 : false) {
            if ((ret.data.nfe.coletaavulsa) && (coleta ? coleta.origem.value === '4' : false)) {
              app.dataresumo.push({ title: 'Nota Fiscal', type: 'nfe', data: ret.data.nfe })
              app.dataresumo.push({ title: 'Coleta', type: 'coleta', data: coleta })
            } else {
              app.dataresumo.push({ title: 'Coleta', type: 'coleta', data: coleta })
              app.dataresumo.push({ title: 'Nota Fiscal', type: 'nfe', data: ret.data.nfe })
            }
          }
          app.dataresumo.push({
            title: 'Entrada',
            type: 'entrada',
            data: (ret.data.entrada ? ret.data.entrada.id > 0 : false) ? ret.data.entrada : null
          })
          app.dataresumo.push({
            title: 'Transferências',
            type: 'transferencias',
            data: (ret.data.transferencias ? ret.data.transferencias.length > 0 : false) ? ret.data.transferencias : null
          })
          app.dataresumo.push({
            title: 'Entrega',
            type: 'entrega',
            data: (ret.data.entrega ? ret.data.entrega.id > 0 : false) ? ret.data.entrega : null
          })
          app.eventos = ret.data.eventos
          var query = {
            t: new Date().getTime(),
            modo: app.dadosconsulta.modo
          }
          if (app.dadosconsulta.modo === 'chave') {
            query['chave'] = app.dadosconsulta.chave
          } else if (app.dadosconsulta.modo === 'dados') {
            query['numero'] = app.dadosconsulta.numero
            query['cnpjemitente'] = app.dadosconsulta.cnpjemitente
            query['cnpjdestinatario'] = app.dadosconsulta.cnpjdestinatario
          }
          try {
            app.$router.replace({ name: app.$route.name, query: query, replace: true, append: false })
          } catch (error) {
            console.error(error)
          }
        } else {
          app.$helpers.showDialog(ret)
          app.msgError = ret.msg
        }
      } catch (error) {
        console.error(error)
        app.$helpers.showDialog({ ok: false, msg: error.message }, true)
      }
    }
  }
}
</script>

<style>
.meuusuariodesktop {
  min-width: 450px
}
.gradientToolbar {
  background-color: #f05b41; /* For browsers that do not support gradients */
  background-image: linear-gradient(to right, #f05b41 , #f05b41); /* Standard syntax (must be last) */
}
.doc-page {
  padding: 16px 46px;
  font-weight: 300;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto
}
.doc-page>div,.doc-page>pre {
  margin-bottom: 22px
 }
@media (max-width: 600px) {
  .doc-page {
    padding: 0px
  }
  .titletoobarcustom {
    min-width: auto;
    padding-left: 16px;
  }
}
</style>
