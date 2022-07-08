import Vue from 'vue'
import Usuario from 'src/mvc/models/usuario.js'

class VeiculoTipo {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.tipo = ''
    this.temreboque = false
    this.ativo = true
    this.created_at = null
    this.updated_at = null
    this.veiculoscount = 0
    this.created_usuario = new Usuario()
    this.updated_usuario = new Usuario()
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.id) self.id = item.id
    if (item.tipo) self.tipo = item.tipo
    if (typeof item.temreboque !== 'undefined') self.temreboque = Vue.prototype.$helpers.toBool(item.temreboque)
    if (item.veiculoscount) self.veiculoscount = item.veiculoscount
    if (typeof item.ativo !== 'undefined') self.ativo = Vue.prototype.$helpers.toBool(item.ativo)
    if (item.created_at) self.created_at = item.created_at
    if (item.updated_at) self.updated_at = item.updated_at
    if (item.created_usuario) await self.created_usuario.cloneFrom(item.created_usuario)
    if (item.updated_usuario) await self.updated_usuario.cloneFrom(item.updated_usuario)
  }
}

export default VeiculoTipo
