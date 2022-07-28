<template>
<div>
  <q-page class="" >
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header full-width" style="padding-bottom: 70px" >
      <div class="col-xs-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 " :class="$q.screen.lt.sm ? '' : 'q-pa-lg'" >
        <!-- loading -->
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''" v-if="loading">
          <q-card-section>
            <data-loading message="Consultando atendimento" />
          </q-card-section>
        </q-card>
        <!-- loading -->
        <!-- error -->
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''" v-if="error && !loading">
          <q-card-section v-if="error">
            <q-banner class="bg-red text-white" rounded>{{error}}</q-banner>
          </q-card-section>
        </q-card>
        <!-- error -->
        <!-- info -->
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section v-if="!loading && (atendimento ? atendimento.id > 0 : false)">
            <div class="row q-col-gutter-sm">
              <div class="col-xs-4 col-md-2">
                <q-field label="Número" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline" tabindex="0">
                      {{$helpers.formatRS(atendimento.id,'',0)}}
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-xs-4 col-md-2">
                <q-field label="Código de acesso" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline" tabindex="0">
                      {{atendimento.codigoacesso}}
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-xs-4 col-md-2">
                <q-field label="Situação" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline" tabindex="0">
                      <q-icon name="fiber_manual_record" size="18px" color="light-blue-3" v-if="!atendimento.encerrado" />
                      <q-icon name="check_circle" size="18px" color="green" v-if="atendimento.encerrado" />
                      {{atendimento.situacao}}
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-xs-12 col-md-3">
                <q-field label="Data de abertura" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-no-wrap ellipsis" tabindex="0">
                      {{$helpers.datetimeToBR(atendimento.dtabertura, false, true)}} - {{$helpers.datetimeRelativeToday(atendimento.dtabertura)}}
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-xs-7 col-md-3">
                <q-field label="Fase" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-no-wrap ellipsis" tabindex="0">
                      {{atendimento.fase ? atendimento.fase.descricaocliente : '*Sem classificação'}}
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-12" v-if="atendimento.pendentecliente">
                <q-banner class="bg-purple-3 text-black" rounded>
                  <p>Este atendimento está aguardando sua resposta. Leia as mensagens e responda pra darmos continuidade no atendimento!</p>
                  <p class="text-caption">* Após 15 dias sem resposta o atendimento será automaticamente encerrado!</p>
                </q-banner>
              </div>
              <div class="col-xs-12 col-md-7">
                <q-field label="Cliente" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-no-wrap ellipsis" tabindex="0">
                      <span v-if="atendimento.cliente ? atendimento.cliente.id > 0 : false">
                        {{atendimento.cliente.nome + ' - ' + $helpers.emailphone($helpers.mascaraDocCPFCNPJ(atendimento.cliente.doc), 5, 5)}}
                      </span>
                      <span v-else>Não identificado</span>
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-xs-12 col-md-5">
                <q-field label="Contato responsável" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-no-wrap ellipsis" tabindex="0">
                      <span v-if="atendimento.contato ? atendimento.contato.id > 0 : false">
                        {{atendimento.contato.nome }}
                      </span>
                      <span v-else>Não identificado</span>
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-12" v-if="atendimento.prazoprevisao && !atendimento.encerrado">
                <q-field label="* Previsão de conclusão" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline text-accent text-weight-bold" tabindex="0">
                     {{$helpers.datetimeToBR(atendimento.prazoprevisao, false, true)}} - {{$helpers.datetimeRelativeToday(atendimento.prazoprevisao)}}
                     <span class="text-caption text-grey-6">* Esta data pode sofrer alteração</span>
                    </div>
                  </template>
                </q-field>
              </div>
              <div class="col-xs-12">
                <q-field label="Descrição inicial de atendimento" stack-label outlined readonly>
                  <template v-slot:control>
                    <div class="self-center full-width no-outline" tabindex="0" >
                      {{atendimento.problemareclamado }}
                    </div>
                  </template>
                </q-field>
              </div>
            </div>
          </q-card-section>
          <!-- info -->
        </q-card>
        <!-- info -->
        <!-- chat -->
        <q-card class="q-mt-sm" flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''" v-if="!loading && (atendimento ? atendimento.id > 0 : false)">
          <q-card-section class="q-pa-none" >
            <div class="row justify-center q-mt-md q-mb-xl q-px-md">
              <div style="width: 100%;">
                <q-chat-message v-for="(chat, k) in atendimento.interacoes" :key="'chat' + k"
                  :sent="chat.origem === 'C'"
                  text-html
                  :bg-color="chat.origem === 'C' ? 'blue-1' : 'grey-4'"
                  :text-color="chat.origem === 'C' ? 'black' : 'black'"
                  :stamp="$helpers.datetimeRelativeToday(chat.datahora)"
                >
                <div>
                  <div v-html="makeAutoLink2(chat.texto, 'chat')" style="white-space:pre-wrap;"/>
                  <div v-for="(anexo, kanexo) in chat.anexos" :key="'anexo' + kanexo"
                    class="q-my-sm row q-pl-sm linkanexo" style="border-left: 3px solid rgb(0 0 0 / 80%)">
                    <div class="col-12"><q-icon name="insert_link" size="28px"  /> {{anexo.descricao}}</div>
                    <div class="col-12" v-html="makeAutoLink2(anexo.url, 'chatundeline text-caption')" />
                  </div>
                </div>
                <template v-slot:name  v-if="(usuariologado ? usuariologado.id : -2) != (chat.contatoacao ? chat.contatoacao.id : -1) ">
                  <span class="text-caption">
                    {{(chat.origem === 'O' ? 'Atendente: ' : '') + (chat.contatoacao ? $helpers.firstName(chat.contatoacao.nome) : '')}}
                  </span>
                </template>
                <template v-slot:avatar v-if="(usuariologado ? usuariologado.id : -2) != (chat.contatoacao ? chat.contatoacao.id : -1) ">
                  <q-avatar size="44px" font-size="25px" color="blue-grey-1" class="q-mx-sm"
                    text-color="primary" v-if="chat.contatoacao.fotoicon ? chat.contatoacao.fotoicon !== '' : false" >
                    <img :src="chat.contatoacao.fotoicon">
                  </q-avatar>
                  <q-avatar size="44px" font-size="25px" color="blue-grey-1" class="q-mx-sm"
                    text-color="primary" v-if="chat.contatoacao.fotoicon ? chat.contatoacao.fotoicon === '' : true" >
                    {{chat.contatoacao ? chat.contatoacao.nome.charAt(0) : ''}}
                  </q-avatar>
                </template>
                </q-chat-message>
              </div>
            </div>
          </q-card-section>
          <q-toolbar class="bg-primary text-white" v-if="!loading && (atendimento ? (atendimento.id > 0) && !atendimento.encerrado : false) && !$q.screen.lt.sm">
            <q-input dark dense standout v-model="text" class="full-width q-mx-md q-my-sm" placeholder="Mensagem" type="textarea"
              :disable="posting || loading" autogrow input-style="max-height: 150px" ref="txtmsg">
                <template v-slot:append>
                  <q-icon v-if="text === ''" name="edit" />
                  <q-icon v-else name="clear" class="cursor-pointer" @click="text = ''" />
                </template>
              </q-input>
            <q-btn  icon="send" class="q-ml-sm" round flat @click="actInteracaoAdd" v-if="text ? text !== '' : false" :loading="posting" :disable="posting || loading"/>
          </q-toolbar>
        </q-card>
        <!-- chat -->
        <q-page-sticky position="top" expand>
          <q-toolbar class="bg-primary text-white">
            <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
            <q-toolbar-title>
              Atendimento {{'#' + $helpers.formatRS(numero, '', 0)}}
            </q-toolbar-title>
            <q-btn  icon="sync" class="q-ml-sm" round flat @click="refreshData(null, false)" :loading="loading" />
            <q-btn  icon="more_vert" round flat @click="refreshData(null, false)"  v-if="(atendimento ? atendimento.id > 0 : false)" />
          </q-toolbar>
        </q-page-sticky>
        <q-page-sticky position="bottom" expand v-if="!loading && (atendimento ? (atendimento.id > 0) && !atendimento.encerrado : false) && $q.screen.lt.sm">
          <q-toolbar class="bg-primary text-white">
            <!-- <q-btn  icon="attach_file" round  flat /> -->
             <q-input dark dense standout v-model="text" class="full-width q-mx-md q-my-sm" placeholder="Mensagem" type="textarea"
              :disable="posting || loading" autogrow input-style="max-height: 150px" ref="txtmsg">
                <template v-slot:append>
                  <q-icon v-if="text === ''" name="edit" />
                  <q-icon v-else name="clear" class="cursor-pointer" @click="text = ''" />
                </template>
              </q-input>
            <q-btn  icon="send" class="q-ml-sm" round flat @click="actInteracaoAdd" v-if="text ? text !== '' : false" :loading="posting" :disable="posting || loading"/>
          </q-toolbar>
        </q-page-sticky>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
// eslint-disable-next-line no-unused-vars
import autolink from 'src/boot/helpers/autolink.js'
import Atendimento from 'src/mvc/models/atendimento.js'
export default {
  name: 'atendimentos.listagem',
  components: {
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.usuario) {
          u = app.$store.state.usuario.usuario
        }
      }
      return u
    }
  },
  data () {
    var atendimento = new Atendimento()
    return {
      sendlido: false,
      sendlidoTimeOut: null,
      numero: null,
      atendimento,
      rows: [],
      tab: 'info',
      text: '',
      filter: '',
      loading: false,
      posting: false,
      error: null
    }
  },
  async activated () {
    var app = this
    if (app.$route.params) app.numero = app.$route.params.id
    if (app.$route.query) await app.queryRead(app.$route.query)
    app.sendlido = false
    if (app.sendlidoTimeOut) clearTimeout(app.sendlidoTimeOut)
    app.sendlidoTimeOut = null
    await app.refreshData()
  },
  deactivated () {
    var app = this
    if (app.sendlidoTimeOut) clearTimeout(app.sendlidoTimeOut)
  },
  methods: {
    makeAutoLink2 (pChatMsg, classe = '') {
      var texto = pChatMsg.autoLink({
        target: '_blank',
        class: classe,
        callback: function (url) {
          return /\.(gif|png|jpe?g)$/i.test(url) ? '<a href="' + url + '" class="' + classe + '" target="_blank" title="abrir imagem"><img src="' + url + '" style="max-width: 20em; max-height: 50em"></a>' : null
        }
      })

      return texto
    },
    makeAutoLink (pChatMsg) {
      var itens = []

      itens.push(pChatMsg.texto)

      if (pChatMsg.anexos) {
        for (let index = 0; index < pChatMsg.anexos.length; index++) {
          const anexo = pChatMsg.anexos[index]
          // var anexotext = '<i class="fa fa-link"></i><a href="' + anexo.url + '" target="_blank" title="abrir arquivo">Arquivo:' + anexo.descricao + '</a>'
          var anexotext = '<i class="fa fa-link"></i> ' + anexo.descricao + '<br>' + anexo.url
          itens.push(anexotext)
        }
      }
      var texto = itens.join('<hr aria-orientation="horizontal" class="q-separator q-separator q-separator--horizontal q-separator--dark" style="margin-bottom: 8px; margin-top: 8px;">')
      texto = texto.autoLink({
        target: '_blank',
        class: 'chat',
        callback: function (url) {
          return /\.(gif|png|jpe?g)$/i.test(url) ? '<a href="' + url + '" class="chat" target="_blank" title="abrir imagem"><img src="' + url + '" style="max-width: 20em; max-height: 50em"></a>' : null
        }
      })

      return texto
    },
    async queryRead (pQuery) {
      var app = this
      app.loading = true
      try {
        if (pQuery) {
          if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)
          if (pQuery.status) {
            app.filterstatus = pQuery.status
          }
          if (pQuery.nivel) {
            app.filternivel = pQuery.nivel.split(',')
          }
          if (pQuery.find) app.filter = pQuery.find
        }
      } finally {
        app.loading = false
      }
    },
    async actInteracaoAdd () {
      var app = this
      app.posting = true
      try {
        var ret = await app.atendimento.sendInteracao(app.text)
        if (ret.ok) {
          if (app.atendimento.interacoes ? app.atendimento.interacoes.length === 0 : true) app.atendimento.interacoes = []
          app.atendimento.interacoes.push(ret.data)
          app.text = ''
          this.$nextTick(() => {
            window.scrollTo(0, document.body.scrollHeight)
            this.$refs.txtmsg.$el.focus()
          })
        } else {
          if (ret.msg ? ret.msg !== '' : false) {
            var a = app.$helpers.showDialog(ret)
            await a.then(function () {
            })
          }
        }
      } finally {
        app.posting = false
      }
    },
    async onLoadInfinite (index, done) {
      var app = this
      setTimeout(() => {
        try {
          if (app.loading) throw new Error('inload')
          if (!app.dataset.temmaisregistros) throw new Error('nao tem mais regsityros')
          app.actLoadMore()
          done()
        } catch (error) {
          // console.error(error.message)
          done()
        }
      }, 200)
    },
    async refreshData () {
      var app = this
      if (app.loading) return
      try {
        app.loading = true
        app.error = null
        var ret = await app.atendimento.find(app.numero)
        if (!ret.ok) {
          app.error = ret.msg
        } else {
          if (!app.sendlidoTimeOut && !app.sendlido) {
            app.sendlidoTimeOut = setTimeout(() => {
              app.atendimento.sendLido(true)
            }, 2000)
          }
        }
      } catch (error) {
        app.loading = false
        app.error = error.message
      }
      app.loading = false
    }
  }
}
</script>

<style>
.chat:link, .chat:visited {
    background-color: #ffffff00;
    color: rgb(20, 20, 20);
    padding: 10px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    border-radius: 8px;
    border: 1px solid #ffffff00;
}

.chat:hover, .chat:active {
    background-color: #ffffffba;
    color: #000;
    padding: 10px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    border-radius: 8px;
    border: 1px solid #ffffff96;
}

.chatundeline:link, .chatundeline:visited {
  color: #000;
  text-decoration: none;
}

.chatundeline:hover, .chatundeline:active {
  text-decoration: underline;
}

.linkanexo:hover, .chat:active {
    background-color: #00000021;
}
</style>
