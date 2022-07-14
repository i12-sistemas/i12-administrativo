import Vue from 'vue'
import { BackupNivel } from './enums/backuptype'
class BackupCliente {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.md5 = null
    this.filename = null
    this.size = 0
    this.lastmodified = null
    this.lastcheck = null
    this.ultimolastmodified = null
    this.totalsize = 0
    this.qtdearquivos = 0
    this.bucket = 0
    this.cliente = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') self.id = item.id
    if (typeof item.md5 !== 'undefined') self.md5 = item.md5
    if (typeof item.filename !== 'undefined') self.filename = item.filename
    if (typeof item.size !== 'undefined') self.size = item.size
    if (typeof item.lastmodified !== 'undefined') self.lastmodified = item.lastmodified
    if (typeof item.lastcheck !== 'undefined') self.lastcheck = item.lastcheck
    if (typeof item.ultimolastmodified !== 'undefined') self.ultimolastmodified = item.ultimolastmodified
    if (typeof item.totalsize !== 'undefined') self.totalsize = item.totalsize
    if (typeof item.qtdearquivos !== 'undefined') self.qtdearquivos = item.qtdearquivos
    if (typeof item.bucket !== 'undefined') self.bucket = item.bucket
    if (typeof item.cliente !== 'undefined') {
      self.cliente = item.cliente
      self.cliente.controlabkp = new BackupNivel(item.cliente.controlabkp)
      self.cliente.ativo = Vue.prototype.$helpers.toBool(item.cliente.ativo)
    }
  }
}

export default BackupCliente
