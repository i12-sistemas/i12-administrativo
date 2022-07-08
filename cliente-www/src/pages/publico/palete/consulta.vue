<template>
<div>
  <q-page class="">
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-sm-12 full-width" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pt-md'">
         <!-- loading -->
        <q-card class="my-card" v-if="!temErro && loading" flat :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-card-section class="full-width text-center text-grey-9">
            <div class="text-h6">Consultando, aguarde!</div>
          </q-card-section>
          <q-card-section class="full-width text-center justify-center items-center">
            <div style="max-width: 80%; margin: 0 auto;" >
              <q-spinner
                color="primary"
                size="5rem"
                :thickness="5"
              />
            </div>
          </q-card-section>
        </q-card>
        <!-- loading-->
        <q-card class="my-card" v-if="temErro" flat :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-card-section class="full-width text-center text-red">
            <div class="text-h6">{{error}}</div>
          </q-card-section>
          <q-card-section class="full-width text-center justify-center items-center">
            <div style="max-width: 7rem; aspect-ratio: 1/1;margin: 0 auto;" >
              <lottie-animation  :animationData="errorjson" :speed="1" :loop="false" autoPlay />
            </div>
          </q-card-section>
          <q-card-actions vertical align="center">
            <q-btn color="red" unelevated @click="init"
              label="Tentar novamente" class="q-px-md" size="lg"
              :class="$q.platform.is.mobile ? 'full-width' : ''"
              />
          </q-card-actions>
        </q-card>
        <!-- tem erro -->
        <!-- consulta -->
        <q-card class="q-pa-none q-my-md" flat :bordered="!$q.platform.is.mobile" v-if="!loading && !temErro && !palete"
          :class="$q.platform.is.mobile ? 'q-ma-none': ''" :square="$q.platform.is.mobile"
          >
          <q-card-section class="text-center" v-if="!loading">
            <div class="full-width row items-center">
              <div class="col-12 text-h6">
                Para consultar o palete número <b>{{ean}}</b><br>marque "Não sou robô" e clique em consultar
              </div>
            </div>
          </q-card-section>
          <q-card-section class="text-center">
            <div style="max-width: 60%; margin: 0 auto;" :style="!$q.platform.is.mobile ? 'max-width: 30%' : ''" >
            <lottie-animation  :animationData="cargacheck"
                :speed="1" :loop="true" autoPlay
                />
            </div>
          </q-card-section>
          <q-card-section class="text-center q-py-md" v-if="!loading">
            <div class="full-width row justify-center items-center">
              <div v-if="recaptchaloading">carregando...</div>
              <vue-recaptcha :sitekey="$configini.RECAPTCHA_KEY" @verify="verify" :loadRecaptchaScript="true" @render="rendered" ></vue-recaptcha>
            </div>
          </q-card-section>
          <q-separator spaced v-if="!$q.platform.is.mobile" />
          <q-card-actions vertical align="center">
            <q-btn :color="allowConsulta ? 'primary' : 'grey-6'" :unelevated="allowConsulta" :outline="!allowConsulta" @click="actConsulta"
              label="Consultar" class="q-px-md" size="lg" icon="search" :disabled="!allowConsulta"
              :class="$q.platform.is.mobile ? 'full-width' : ''"
              />
          </q-card-actions>
        </q-card>
        <!-- consulta -->
        <!-- palete -->
        <q-card class="my-card q-mb-lg" v-if="palete && !loading && !temErro && !$q.platform.is.mobile" flat bordered>
          <q-toolbar class="bg-grey-3 text-black">
            <q-icon name="widgets" size="24px" color="black" />
            <q-toolbar-title>
              Consulta de Palete
            </q-toolbar-title>
            <span  class="text-right text-caption" style="align: 'rigth'">consulta realizada em<br>{{$helpers.datetimeToBR(hrconsulta)}}</span>
          </q-toolbar>
          <q-card-section >
            <div class="row text-h6 q-col-gutter-md">
              <div class="col-xs-12 col-md-4">
                <q-field label="Código EAN" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{palete.ean13}}</div>
                  </template>
                </q-field>
              </div>
               <div class="col-xs-12 col-md-8">
                 <q-field label="Unidade" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{palete.unidade.fantasia}}</div>
                  </template>
                </q-field>
              </div>
              <div class="col-12">
                <q-field label="Descrição" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{palete.descricao}}</div>
                  </template>
                </q-field>
              </div>
            </div>
          </q-card-section>
          <q-toolbar class="bg-grey-1 text-black">
            <q-toolbar-title class="q-ml-md">
              Notas Fiscais
            </q-toolbar-title>
            <span class="q-mr-lg rounded-borders bg-grey-4 q-pa-xs">{{palete.notas ? palete.notas.length : 0 }} nota(s)</span>
            <span class="rounded-borders bg-grey-3 q-pa-xs">{{palete.volqtde}} volume(s)</span>
          </q-toolbar>
          <q-card-section class="text-caption" >
            <div>
                <q-table :data="palete.notas" :columns="columns" dense :loading="loading" color="primary" flat bordered
                  id="sticky-table" hide-bottom
                  row-key="id"
                  :rows-per-page-options="[0]"
                  >
                <template v-slot:body-cell-remetentenome="props">
                  <q-td :props="props"  style="max-width: 200px" >
                    <div style="max-width: 200px">
                      <div class="ellipsis text-bold full-width" >{{ props.row.remetentenome}}</div>
                      <div class="text-caption" v-if="props.row.remetentecnpj !== ''">CNPJ: {{ props.row.remetentecnpj}}</div>
                    </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-destinatarionome="props">
                  <q-td :props="props" style="max-width: 200px"  >
                    <div style="max-width: 200px">
                      <div class="ellipsis text-bold full-width" >{{ props.row.destinatarionome}}</div>
                      <div class="text-caption" v-if="props.row.destinatariocnpj !== ''">CNPJ: {{ props.row.destinatariocnpj}}</div>
                    </div>
                  </q-td>
                </template>
                <template v-slot:body-cell-volumes="props">
                  <q-td :props="props" style="max-width: 200px"   >
                    <div style="max-width: 200px">
                      <div class="ellipsis text-bold full-width rounded-borders"
                        :class="props.row.voltotal === (props.row.volumes ? props.row.volumes.length : 0) ? 'bg-white' : 'bg-red text-white'" >
                        {{ props.row.volumes ? props.row.volumes.length : 'Nenhum'}}
                        <q-tooltip :delay="700">
                          <div v-if="props.row.voltotal !== (props.row.volumes ? props.row.volumes.length : 0)">
                            <div>A NF-e contêm {{props.row.voltotal}} volume(s)
                                 porém somente os volumes de número {{props.row.volumes.join(', ')}} estão presentes neste palete</div>
                          </div>
                          <div v-else>
                            <div>A NF-e contêm {{props.row.volumes.length}} volume(s)</div>
                          </div>

                        </q-tooltip>
                      </div>
                    </div>
                  </q-td>
                </template>
                </q-table>
            </div>
          </q-card-section>
        </q-card>
        <q-card class="my-card q-mb-lg" v-if="palete && !loading && !temErro && $q.platform.is.mobile" flat square>
          <q-toolbar class="bg-grey-3 text-black">
            <q-icon name="widgets" size="24px" color="black" />
            <q-toolbar-title>
              Consulta de Palete
            </q-toolbar-title>
            <span  class="text-right text-caption" style="align: 'rigth'">consulta realizada em<br>{{$helpers.datetimeToBR(hrconsulta)}}</span>
          </q-toolbar>
          <q-card-section >
            <div class="row text-h6 q-col-gutter-md">
              <div class="col-xs-12 col-md-4">
                <q-field label="Código EAN" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{palete.ean13}}</div>
                  </template>
                </q-field>
              </div>
               <div class="col-xs-12 col-md-8">
                 <q-field label="Unidade" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{palete.unidade.fantasia}}</div>
                  </template>
                </q-field>
              </div>
              <div class="col-12">
                <q-field label="Descrição" stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{palete.descricao}}</div>
                  </template>
                </q-field>
              </div>
              <div class="col-6">
                <q-field stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{palete.notas ? palete.notas.length : 0 }} nota(s)</div>
                  </template>
                </q-field>
              </div>
              <div class="col-6">
                <q-field stack-label outlined >
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-h6" tabindex="0">{{palete.volqtde}} volume(s)</div>
                  </template>
                </q-field>
              </div>
            </div>
          </q-card-section>
          <q-card-section class="q-pa-none" >
            <q-list>
              <q-item clickable v-ripple v-for="(row, k) in palete.notas" :key="'row' + k">
                <q-item-section>
                  <q-card bordered flat >
                    <q-card-section class="bg-grey-2 q-py-xs">
                      <div class="row">
                        <div class="col-8 text-h6">NF-e: {{$helpers.padLeftZero(row.numero, 7)}}</div>
                        <div class="col-4 text-h6 text-right">Série: {{$helpers.padLeftZero(row.serie, 3)}}</div>
                      </div>
                    </q-card-section>
                    <q-separator  />
                    <q-card-section>
                      <div class="row text-body">
                        <div class="col-4 text-caption">Remetente</div>
                        <div class="col-8 text-right" v-if="row.remetentecnpj !== ''">CNPJ: {{ row.remetentecnpj}}</div>
                        <div class="col-12 text-bold" >{{ row.remetentenome}}</div>
                      </div>
                    </q-card-section>
                    <q-separator  />
                    <q-card-section>
                      <div class="row text-body">
                        <div class="col-4 text-caption">Destinatário</div>
                        <div class="col-8 text-right" v-if="row.destinatariocnpj !== ''">CNPJ: {{ row.destinatariocnpj}}</div>
                        <div class="col-12 text-bold" >{{ row.destinatarionome}}</div>
                      </div>
                    </q-card-section>
                    <q-separator  />
                    <q-card-section>
                      <div class="full-width text-center text-red" v-if="row.voltotal !== (row.volumes ? row.volumes.length : 0)">
                        <div>Este palete contêm {{ row.volumes ? row.volumes.length : 'Nenhum'}} volume(s)</div>
                        <div>porém a NF-e contêm {{row.voltotal}} volume(s)</div>
                      </div>
                      <div v-else class="full-width text-center ">
                        Contêm {{ row.volumes ? row.volumes.length : 'Nenhum'}} volume(s)
                      </div>
                    </q-card-section>
                  </q-card>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
        <!-- palete -->
      </div>
    </div>
  </q-page>
</div>
</template>

<script>
// import axios from 'axios'
import VueRecaptcha from 'vue-recaptcha'
import Palete from 'src/mvc/models/palete.js'
import Vue from 'vue'
import moment from 'moment'
export default {
  name: 'publico.palete.consulta',
  components: { VueRecaptcha },
  data () {
    var cargacheck = require('src/assets/lotties/cargacheck.json')
    var errorjson = require('src/assets/lotties/error.json')
    var dataset = new Palete()
    return {
      cargacheck,
      errorjson,
      hrconsulta: null,
      ean: null,
      senha: null,
      hash: null,
      palete: null,
      dataset,
      recaptchaloading: true,
      recaptchatoken: null,
      loading: false,
      error: null,
      columns: [
        { name: 'numero', align: 'center', style: 'width: 80px', label: 'Número', field: 'numero', format: val => `${Vue.prototype.$helpers.padLeftZero(val, 7)}` },
        { name: 'serie', align: 'center', style: 'width: 30px', label: 'Série', field: 'serie', format: val => `${Vue.prototype.$helpers.padLeftZero(val, 3)}` },
        { name: 'volumes', align: 'center', style: 'width: 30px', label: 'Volumes', field: 'volumes' },
        { name: 'remetentenome', align: 'left', label: 'Remetente', field: 'remetentenome' },
        { name: 'destinatarionome', align: 'left', label: 'Destinatário', field: 'destinatarionome' }
      ]
    }
  },
  meta () {
    return {
      // this accesses the "title" property in your Vue "data";
      // whenever "title" prop changes, your meta will automatically update
      title: 'Conecta Transportes - Consulta de Palete'
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
  async mounted () {
    var app = this
    await app.init()
  },
  computed: {
    temErro: function () {
      return this.error ? this.error !== '' : false
    },
    allowConsulta: function () {
      var app = this
      var b = true
      try {
        if (app.loading) throw new Error('Em loading')
        if (!app.recaptchatoken) throw new Error('Sem recaptcha nota')
        if (app.ean ? app.ean.length !== 13 : true) throw new Error('Número do palete inválido')
        if (app.senha ? app.senha.length < 5 : true) throw new Error('Senha de acesso do palete inválida')
        if (app.hash ? app.hash.length < 5 : true) throw new Error('Hash de acesso do palete inválido')
      } catch (error) {
        b = false
      }
      return b
    }
  },
  methods: {
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
    async actConsulta () {
      var app = this
      try {
        if (app.loading) throw new Error('Em loading')
        if (!app.recaptchatoken) throw new Error('Marque a opção "Não sou robô"')
        if (!app.allowConsulta) throw new Error('Dados incompletos para consulta')
        app.loading = true
        var params = {
          ean: app.ean,
          senha: app.senha,
          hash: app.hash
        }
        var ret = await app.dataset.findPublico(params, app.recaptchatoken)
        if (ret.ok) {
          app.palete = ret.data
          app.hrconsulta = moment()
        } else {
          app.error = ret.msg
        }
      } catch (error) {
        console.error(error)
        app.$helpers.showDialog({ ok: false, msg: error.message }, true)
      } finally {
        app.loading = false
      }
    },
    async init () {
      var app = this
      app.loading = true
      try {
        app.error = null
        app.palete = null
        app.ean = (app.$route.query.ean) ? app.$route.query.ean : null
        app.senha = (app.$route.query.senha) ? app.$route.query.senha : null
        app.hash = (app.$route.query.hash) ? app.$route.query.hash : null

        if (app.ean ? app.ean.length !== 13 : true) throw new Error('Número do palete inválido')
        if (app.senha ? app.senha.length < 5 : true) throw new Error('Senha de acesso do palete inválida')
        if (app.hash ? app.hash.length < 5 : true) throw new Error('Hash de acesso do palete inválido')

        var check = await app.$helpers.bcryptcompare(app.ean + 'palete' + app.senha, app.hash)
        if (!check) throw new Error('Link quebrado. Informações de número e senha não confere')

        app.recaptchaLoad()
      } catch (error) {
        app.error = error.message
      } finally {
        app.loading = false
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
