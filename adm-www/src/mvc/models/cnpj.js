import Vue from 'vue'
// import Endereco from 'src/mvc/models/endereco.js'

class ConsultaCNPJ {
  constructor () {
    this.limpardados()
  }

  async limpardados () {
    this.tipo = ''
    this.abertura = ''
    this.nome = ''
    this.fantasia = ''
    this.atividade_principal = null
    this.atividades_secundarias = null
    this.natureza_juridica = ''
    this.email = ''
    this.telefone = ''
    this.telefone2 = ''
    this.telefone3 = ''
    this.situacao = ''
    this.data_situacao = null
    this.motivo_situacao = ''
    this.situacao_especial = ''
    this.data_situacao_especial = ''
    this.capital_social = 0
    this.qsa = null
    // this.endereco = new Endereco()
    this.dataraw = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    self.dataraw = item
    if (item.nome) self.nome = item.nome
    if (item.fantasia) self.fantasia = item.fantasia
    if (item.telefone) {
      var tels = item.telefone.trim().split('/')
      if (tels.length >= 1) self.telefone = tels[0].trim()
      if (tels.length >= 2) self.telefone2 = tels[1].trim()
      if (tels.length >= 3) self.telefone3 = tels[2].trim()
    }
    if (item.email) self.email = item.email

    // if (item.logradouro) self.endereco.logradouro = item.logradouro
    var ret = await self.endereco.splitLogradouroEndereco(item.logradouro)
    self.endereco.logradouro = ret.logradouro
    self.endereco.endereco = ret.endereco
    if (item.numero) self.endereco.numero = item.numero
    if (item.cep) self.endereco.cep = Vue.prototype.$helpers.clearMask(item.cep)
    if (item.bairro) self.endereco.bairro = item.bairro
    if (item.complemento) self.endereco.complemento = item.complemento

    if (item.municipio) await self.endereco.cidade.findByCodigoIBGE(null, item.municipio, item.uf)
  }

  async cartaoMemoText () {
    var item = this.dataraw
    var self = this
    if (!item) return

    var html = ''
    if (item.ultima_atualizacao) {
      item.ultima_atualizacao = Vue.prototype.$helpers.strToMoment(item.ultima_atualizacao)
      if (item.ultima_atualizacao) html += '<p>Última atualização: <b>' + item.ultima_atualizacao.format('LLLL') + '</b></p>'
    }

    html += '<p>Número da inscrição: <b>' + Vue.prototype.$helpers.mascaraDocCPFCNPJ(item.cnpj) + '</b>'
    html += '<br>Situação cadastral: <b>' + item.situacao + '</b><br>'
    html += 'Data da situação cadastral: <b>' + item.data_situacao + '</b></p>'

    if (item.abertura) {
      html += '<p>Data de abertura: <b>' + item.abertura + ' - ' + Vue.prototype.$helpers.datetimeRelativeToday(item.abertura) + '</b></p>'
    }

    html += '<p>Nome empresarial: <b>' + item.nome + '</b><br>'
    html += 'Nome fantasia: <b>' + item.fantasia + '</b></p>'

    if (item.porte) html += '<p>Porte: <b>' + item.porte + '</b></p>'

    html += '<p>Código e descrição da atividade econômica principal:'
    html += '<ul>'
    for (let index = 0; index < item.atividade_principal.length; index++) {
      const element = item.atividade_principal[index]
      html += '<li>Código: <b>' + element.code + '</b> - <b>' + element.text + '</b></li>'
    }
    html += '</ul></p>'

    html += '<p>Código e descrição das atividades econômicas secundárias:'
    html += '<ul>'
    for (let index = 0; index < item.atividades_secundarias.length; index++) {
      const element = item.atividades_secundarias[index]
      html += '<li>Código: <b>' + element.code + '</b> - <b>' + element.text + '</b></li>'
    }
    html += '</ul></p>'

    if (item.natureza_juridica) html += '<p>Código e descrição da natureza jurídica: <b>' + item.natureza_juridica + '</b></p>'

    html += '<p>Endereço: <b>' + self.endereco.enderecocompletotext + '</b></p>'

    html += '<p>Telefone: <b>' + item.telefone + '</b></p>'
    html += '<p>E-mail: <b>' + item.email + '</b></p>'

    html += '<p>Capital Social: <b>' + Vue.prototype.$helpers.formatRS(item.capital_social) + '</b></p>'

    html += '<p>QSA - Quadro de sócios administradores:'
    html += '<ul>'
    for (let index = 0; index < item.qsa.length; index++) {
      const element = item.qsa[index]
      html += '<li>Nome: <b>' + element.nome + '</b> - Qualificação: ' + element.qual + '</li>'
    }
    html += '</ul></p>'

    return html
  }

  async consultaReceitaWS (pCNPJ) {
    var self = this
    var cnpj = Vue.prototype.$helpers.clearMask(pCNPJ)
    try {
      if (cnpj.length !== 14) throw new Error('CNPJ deve ter 14 dígitos')
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    let ret = await Vue.prototype.$axios.get('v1/receitaws/' + cnpj).then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          await self.cloneFrom(data.data)
        }
      }
      return ret
      // let data = response.data
      // var ret = { ok: false, msg: '', warning: true }
      // try {

      //   if (!data) throw new Error('Nenhum registro encontrado')
      //   ret.ok = (data.status === 'OK')
      //   if (!ret.ok) ret.warning = true
      //   ret.msg = (data.message ? data.message : '')
      //   await self.cloneFrom(data)
      //   ret.data = data
      // } catch (error) {
      //   ret.msg = error.message
      // }
      // return ret
    }).catch(error => {
      return { ok: false, msg: 'Erro ao consultar CNPJ - ' + error }
    })
    return ret
  }
}

export default ConsultaCNPJ
