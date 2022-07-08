<template>
  <div>
    <div class="q-pa-none q-ma-none">
      <q-uploader flat
        :factory="factoryFn" @uploaded="uploaded" @failed="uploaded" @finish="actFinish"
        :form-fields="[{name: 'my-field', value: 'my-value'}]"
        :headers="header"
        field-name="arquivo"
        multiple
        ref="uploader"
        auto-expand
        class="full-width full-height"
        color="primary"
      >
      <template v-slot:header="scope">
    <q-card-section class="bg-primary text-white">
        <div class="row">
          <q-btn v-if="scope.uploadedFiles.length > 0" icon="done_all" @click="scope.removeUploadedFiles" round dense flat >
            <q-tooltip>Remove Uploaded Files</q-tooltip>
          </q-btn>
          <q-spinner v-if="scope.isUploading" class="q-uploader__spinner" />
          <div class="col">
            <div class="q-uploader__title ellipsis ellipsis-line-1">{{label}}</div>
            <div class="q-uploader__subtitle">{{ scope.uploadSizeLabel }} / {{ scope.uploadProgressLabel }}</div>
          </div>
          <q-btn v-if="scope.queuedFiles.length > 0" icon="clear_all" :label="$q.platform.is.mobile ? '' : 'limpar' " @click="scope.removeQueuedFiles" dense flat >
            <q-tooltip>Remover tudo</q-tooltip>
          </q-btn>
          <q-separator spaced inset vertical color="white"  v-if="scope.queuedFiles.length > 0 && !$q.platform.is.mobile" />
          <q-btn v-if="scope.canAddFiles" type="a" icon="add" :label="$q.platform.is.mobile ? '' : 'arquivos'" :dense="!$q.platform.is.mobile" :stretch="$q.platform.is.mobile" flat>
            <q-uploader-add-trigger />
            <q-tooltip>Adicionar arquivos</q-tooltip>
          </q-btn>
          <q-separator spaced inset vertical color="white" v-if="scope.canUpload && !$q.platform.is.mobile"  />
          <q-btn v-if="scope.canUpload" icon="cloud_upload" label="Enviar" stack  @click="scope.upload" :dense="!$q.platform.is.mobile" :stretch="$q.platform.is.mobile" flat >
            <q-tooltip>Enviar arquivos</q-tooltip>
          </q-btn>

          <q-btn v-if="scope.isUploading" icon="clear" @click="scope.abort" :round="!$q.platform.is.mobile" :dense="!$q.platform.is.mobile" :stretch="$q.platform.is.mobile" flat >
            <q-tooltip>Cancelar upload</q-tooltip>
          </q-btn>
          <q-separator spaced inset vertical color="white" v-if="!$q.platform.is.mobile" />
          <q-btn v-if="!scope.isUploading" icon="clear" @click="actClose" :round="!$q.platform.is.mobile" :dense="!$q.platform.is.mobile" :stretch="$q.platform.is.mobile" flat >
            <q-tooltip>Sair</q-tooltip>
          </q-btn>
        </div>
    </q-card-section>

      </template>
      <template v-slot:list="scope">
        <div v-if="scope.files ? scope.files.length == 0 : true" class="full-width row no-wrap justify-center items-center content-center">
          <div  class="justify-center items-center content-center text-center " style="overflow: auto;">
            <div class="text-subtitle2 q-pa-md" >Nenhum arquivo carregado!</div>
            <div class="text-subtitle2 q-pa-md">Clique em adicionar arquivo ou arraste e solte seus arquivos aqui!</div>
            <div class="q-pa-md"><q-icon name="arrow_forward" size="80px" color="grey" /><q-icon name="cloud_upload" size="80px" color="grey" /></div>
          </div>
        </div>
        <div style="height: 100%">
        <q-list separator>
          <q-item v-for="(file, key) in scope.files" :key="file.name" :class="$helpers.isOdd(key) ? 'bg-grey-1' : 'bg-white'">
            <q-item-section v-if="file.__img" thumbnail  >
              <img :src="file.__img.src" style="height: 100%; width: 100%; max-width: 60px; max-height: 80px">
            </q-item-section>
            <q-item-section>
              <q-item-label class="full-width ellipsis">
                {{ file.name }}
              </q-item-label>
              <q-item-label class="text-caption">
                Status:
                <span class="q-pl-md  text-red text-weight-bold" v-if="file.__status == 'failed'">Falha ao enviar</span>
                <span class="q-pl-md text-green" v-if="file.__status == 'uploaded'">Enviado!</span>
                <span class="q-pl-md text-grey" v-if="file.__status == 'idle'">Aguardando envio</span>
                <span class="q-pl-md text-primary" v-if="file.__status == 'uploading'">enviando...</span>
              </q-item-label>
              <q-item-label caption>
                {{ file.__sizeLabel }} / {{ file.__progressLabel }}
              </q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn  text-color="red" unelevated dense round icon="delete" @click="scope.removeFile(file)" />
            </q-item-section>
          </q-item>

        </q-list>
        </div>
      </template>
      </q-uploader>
    </div>
  </div>
</template>

<script>
export default {
  props: [
    'label', 'value', 'outlined', 'dense', 'clearable', 'starturlcomplete', 'starturlappend'
  ],
  data: () => ({
    header: [
      { name: 'Access-Control-Allow-Origin', value: '*' },
      { name: 'Access-Control-Allow-Headers', value: 'Authorization' },
      { name: 'Access-Control-Allow-Methods', value: 'GET, POST, OPTIONS, PUT, PATCH, DELETE' }
    ],
    url: '',
    alterado: false,
    qtdeErros: 0,
    dFormatada: null
  }),
  mounted () {
    var app = this
    app.url = app.$configini.API_URL
    if (app.starturlappend) app.url = app.url + app.starturlappend
    if (app.starturlcomplete) app.url = app.starturlcomplete
    app.header.push({ name: 'token', value: app.$store.state.usuario.token })
    app.header.push({ name: 'tokenempresa', value: app.$store.state.usuario.tokenempresa })
    app.header.push({ name: 'cnpj', value: app.$store.state.usuario.empresa ? app.$store.state.usuario.empresa.cnpj : null })
    app.header.push({ name: 'username', value: app.$store.state.usuario.usuario.login })
  },
  methods: {
    actClose () {
      this.$emit('close', this.alterado)
    },
    actFinish () {
      if (this.qtdeErros === 0) this.actClose()
    },
    async factoryFn (files) {
      var app = this
      app.qtdeErros = 0
      return {
        url: app.url,
        method: 'POST'
      }
    },
    uploaded (info) {
      const ret = JSON.parse(info.xhr.response)
      if (!ret.ok) {
        this.qtdeErros = this.qtdeErros + 1
        ret.msg = ret.msg + ' - Arquivo: ' + info.files[0].name
        this.$helpers.showDialog(ret, true)
      } else {
        this.alterado = true
      }
    }
  }
}
</script>

<style scoped>

.q-uploader {
  max-height: 100%;
  height: 100%;
}
</style>
