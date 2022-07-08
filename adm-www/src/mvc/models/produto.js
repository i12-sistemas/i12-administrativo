import Usuario from 'src/mvc/models/usuario.js'
import Vue from 'vue'

class Produto {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.onu = null
    this.nome = ''
    this.nome_old = ''
    this.classerisco = ''
    this.riscosubs = ''
    this.riscosubs2 = ''
    this.numrisco = ''
    this.grupoemb = ''
    this.provespec = ''
    this.qtdeltdav = ''
    this.qtdeltdae = ''
    this.embibcinst = ''
    this.embibcprov = ''
    this.tanqueinst = ''
    this.tanqueprov = ''
    this.polimeriza = ''
    this.epi = ''
    this.kit = ''
    this.guia = null
    this.reage_agua = false
    this.ativo = true
    this.created_at = null
    this.updated_at = null
    this.created_usuario = new Usuario()
    this.updated_usuario = new Usuario()
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.id) self.id = item.id
    if (item.onu) self.onu = item.onu
    if (item.nome) self.nome = item.nome
    self.nome_old = item.nome
    if (item.classerisco) self.classerisco = item.classerisco
    if (item.riscosubs) self.riscosubs = item.riscosubs
    if (item.riscosubs2) self.riscosubs2 = item.riscosubs2
    if (item.numrisco) self.numrisco = item.numrisco
    if (item.grupoemb) self.grupoemb = item.grupoemb
    if (item.provespec) self.provespec = item.provespec
    if (item.qtdeltdav) self.qtdeltdav = item.qtdeltdav
    if (item.qtdeltdae) self.qtdeltdae = item.qtdeltdae
    if (item.embibcinst) self.embibcinst = item.embibcinst
    if (item.embibcprov) self.embibcprov = item.embibcprov
    if (item.tanqueinst) self.tanqueinst = item.tanqueinst
    if (item.tanqueprov) self.tanqueprov = item.tanqueprov
    if (item.polimeriza) self.polimeriza = item.polimeriza
    if (item.epi) self.epi = item.epi
    if (item.kit) self.kit = item.kit
    if (item.guia) self.guia = item.guia
    self.reage_agua = Vue.prototype.$helpers.toBool(item.reage_agua)
    self.ativo = Vue.prototype.$helpers.toBool(item.ativo)
    if (item.created_at) self.created_at = item.created_at
    if (item.updated_at) self.updated_at = item.updated_at
    if (item.updated_usuario) await self.updated_usuario.cloneFrom(item.updated_usuario)
    if (item.updated_usuario) await self.updated_usuario.cloneFrom(item.updated_usuario)
  }

  async find (pID) {
    var self = this
    self.limpardados()
    let ret = await Vue.prototype.$axios.get('v1/produto/' + pID).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async save () {
    var self = this
    try {
      var permite = Vue.prototype.$helpers.permite('cadastros.produtos.save')
      if (!permite.ok) throw new Error(permite.msg)
      if (!self.nome) throw new Error('Nome do produto não foi informado')
      if (self.nome.length < 2) throw new Error('Nome do produto deve ter no mínimo 2 caracteres')
      self.onu = parseInt(self.onu)
      if (!(self.onu > 0)) throw new Error('Número ONU inválida')
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    let params = {
      id: self.id ? (self.id > 0 ? self.id : null) : null,
      nome: self.nome,
      onu: self.onu,
      ativo: self.ativo,
      reage_agua: self.reage_agua,
      classerisco: self.classerisco,
      riscosubs: self.riscosubs,
      riscosubs2: self.riscosubs2
    }
    let ret = await Vue.prototype.$axios.post('v1/produto', params).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          if (data.data) self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async deleteWithQuestion (app) {
    var self = this
    try {
      var permite = Vue.prototype.$helpers.permite('cadastros.produtos.delete')
      if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        title: 'Excluir produto',
        message: 'Para excluir o produto "' + self.nome + '" digite o código ' + self.id + '?',
        prompt: {
          model: '',
          type: 'text' // optional
        },
        cancel: true
      }).onOk(async data => {
        if (parseInt(data) === parseInt(self.id)) {
          var ret = await self.delete()
          resolve(ret)
        } else {
          resolve({ ok: false, msg: 'Informação inválida', warning: true })
        }
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async delete () {
    var self = this

    let ret = await Vue.prototype.$axios.delete('v1/produto/' + self.id).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          self.limpardados()
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}

export default Produto
