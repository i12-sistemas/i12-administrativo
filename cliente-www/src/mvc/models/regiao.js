import Vue from 'vue'
import Cidade from 'src/mvc/models/cidade.js'

class Regiao {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.regiao = ''
    this.regiao_old = ''
    this.cidadescount = 0
    this.cidadespivot = null
  }

  async fechtCidades (force = false) {
    var self = this
    self.cidadespivot = null
    let ret = await Vue.prototype.$axios.get('v1/regiao/' + self.id + '/cidades').then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          if (data.data) {
            if (data.data.length > 0) {
              self.cidadespivot = []
              for (let index = 0; index < data.data.length; index++) {
                const lcidadespivot = data.data[index]
                let lItem = new Cidade()
                await lItem.cloneFrom(lcidadespivot)
                if (lItem.id > 0) {
                  self.cidadespivot.push({ pivot: (lcidadespivot.pivot ? lcidadespivot.pivot : null), cidade: lItem, id: lItem.id })
                }
              }
            }
          }
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') self.id = item.id
    if (typeof item.regiao !== 'undefined') self.regiao = item.regiao.toUpperCase()
    self.regiao_old = self.regiao
    if (typeof item.cidadescount !== 'undefined') self.cidadescount = item.cidadescount
  }
}

export default Regiao
