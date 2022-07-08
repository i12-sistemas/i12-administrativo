import VeiculoTipo from 'src/mvc/models/veiculotipo.js'
import Cidade from 'src/mvc/models/cidade.js'
import Usuario from 'src/mvc/models/usuario.js'
import Vue from 'vue'

class Veiculo {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    delete this.ultimokmacerto
    this.id = null
    this.descricao = ''
    this.placa = ''
    this.placa_old = ''
    this.proprietario = 'T'
    this.tara = 0
    this.lotacao = 0
    this.pbt = 0
    this.pbtc = 0
    this.mediaconsumo = 0
    this.ativo = true
    this.manutencao = false
    this.created_at = null
    this.updated_at = null
    this.alertamanut = null
    this.ultimokm = null
    this.ultimokmdhcheck = null
    this.cidade = new Cidade()
    this.tipo = new VeiculoTipo()
    this.created_usuario = null
    this.updated_usuario = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.id) self.id = item.id
    if (item.descricao) self.descricao = item.descricao
    if (item.placa) self.placa = item.placa
    if (item.tara) self.tara = parseFloat(item.tara)
    if (item.lotacao) self.lotacao = parseFloat(item.lotacao)
    if (item.pbt) self.pbt = parseFloat(item.pbt)
    if (item.pbtc) self.pbtc = parseFloat(item.pbtc)
    if (typeof item.mediaconsumo !== 'undefined') self.mediaconsumo = parseFloat(item.mediaconsumo)
    if (item.ultimokm) self.ultimokm = parseInt(item.ultimokm)
    if (item.ultimokmdhcheck) self.ultimokmdhcheck = item.ultimokmdhcheck
    self.placa_old = item.placa
    self.ativo = Vue.prototype.$helpers.toBool(item.ativo)
    self.manutencao = Vue.prototype.$helpers.toBool(item.manutencao)
    if (item.alertamanut) self.alertamanut = item.alertamanut
    if (item.proprietario) self.proprietario = item.proprietario
    if (item.created_at) self.created_at = item.created_at
    if (item.updated_at) self.updated_at = item.updated_at
    if (item.cidade) await self.cidade.cloneFrom(item.cidade)
    if (item.tipo) await self.tipo.cloneFrom(item.tipo)
    if (typeof item.updated_usuario !== 'undefined') self.updated_usuario = new Usuario(item.updated_usuario)
    if (typeof item.created_usuario !== 'undefined') self.created_usuario = new Usuario(item.created_usuario)
  }
}

export default Veiculo
