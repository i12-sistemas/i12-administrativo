class Cliente {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.doc = null
    this.nome = ''
    this.fantasia = ''
    this.ativo = true
    delete this.saldovencido
    delete this.saldovencidodesde
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') this.id = item.id
    if (typeof item.doc !== 'undefined') this.doc = item.doc
    if (typeof item.nome !== 'undefined') this.nome = item.nome
    if (typeof item.ativo !== 'undefined') this.ativo = item.ativo
    if (typeof item.saldovencido !== 'undefined') this.saldovencido = item.saldovencido
    if (typeof item.saldovencidodesde !== 'undefined') this.saldovencidodesde = item.saldovencidodesde
  }

  // async find (pID) {
  //   var self = this
  //   self.limpardados()
  //   let ret = await Vue.prototype.$axios.get('v1/cliente/' + pID).then(async response => {
  //     let data = response.data
  //     var ret = { ok: false, msg: '' }
  //     if (data) {
  //       ret.msg = data.msg ? data.msg : ''
  //       if (data.ok) {
  //         ret.ok = true
  //         await self.cloneFrom(data.data)
  //       }
  //     }
  //     return ret
  //   }).catch(error => {
  //     return Vue.prototype.$helpers.errorReturn(error)
  //   })
  //   return ret
  // }
}

export default Cliente
