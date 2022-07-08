import Vue from 'vue'
// import Etiqueta from 'src/mvc/models/etiqueta.js'

class CargaEntregaItemAgrupado {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.cargaentradaitem = null
    this.coletaid = null
    this.etiquetascompletas = false
    this.etiquetas = null
    this.etiquetastotal = 0
    this.etiquetaserros = 0
    this.cte = null
    this.destinatario = null
    this.nfeentrada = null
    this.baixastatus = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.cargaentradaitem !== 'undefined') self.cargaentradaitem = item.cargaentradaitem
    if (typeof item.baixastatus !== 'undefined') self.baixastatus = item.baixastatus
    if (typeof item.coletaid !== 'undefined') self.coletaid = item.coletaid
    if (typeof item.etiquetascompletas !== 'undefined') self.etiquetascompletas = Vue.prototype.$helpers.toBool(item.etiquetascompletas)
    if (typeof item.cte !== 'undefined') self.cte = item.cte
    if (typeof item.destinatario !== 'undefined') self.destinatario = item.destinatario
    if (typeof item.etiquetas !== 'undefined') self.etiquetas = item.etiquetas
    if (typeof item.etiquetastotal !== 'undefined') self.etiquetastotal = item.etiquetastotal
    if (typeof item.etiquetaserros !== 'undefined') self.etiquetaserros = item.etiquetaserros
    if (typeof item.nfeentrada !== 'undefined') self.nfeentrada = item.nfeentrada
  }

  // somente insert
  // pModo pode ser: ean ou volume
  // quando ean, pData informe array de ean13
  // quando volume, pData informe uma object de carga { cargaentradaitem: 2222, vol: 1 }
  async save (pCargaId, pModo, pData) {
    var self = this
    try {
      if (!pCargaId) throw new Error('Número da carga não foi informado')
      var params = {
        modo: pModo
      }
      var eans = null
      if (pModo === 'ean') {
        eans = []
        if (pData ? pData.length > 0 : false) {
          for (let index = 0; index < pData.length; index++) {
            eans.push(pData[index])
          }
        }
        if (eans.length <= 0) throw new Error('Nenhum código de barra informado')
        params['ean13'] = eans
      } else if (pModo === 'volume') {
        params['cargaentradaitem'] = self.cargaentradaitem
        params['vol'] = pData
      }
      // if (self.etiqueta ? (self.etiqueta.ean13 ? self.etiqueta.ean13 !== '' : false) : false) eans.push(self.etiqueta.ean13)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    let ret = await Vue.prototype.$axios.post('v1/cargaentrega/carga/' + pCargaId + '/itens', params).then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.data) ret.data = data.data
        if (data.ok) {
          ret.ok = true
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  // somente insert
  // async saveedit (pIds) {
  //   var self = this
  //   try {
  //     if (!self.cargaentregaid) throw new Error('Número da carga não foi informado')
  //     if (!self.id) throw new Error('Item não foi encontrada')
  //     if (!(self.id > 0)) throw new Error('Item não foi encontrada')
  //   } catch (error) {
  //     return { ok: false, msg: error.message, warning: false }
  //   }
  //   let params = {
  //     ids: pIds,
  //     ctechave: self.ctechave
  //   }

  //   let ret = await Vue.prototype.$axios.post('v1/cargaentrega/carga/' + self.cargaentregaid + '/itens/update', params).then(async response => {
  //     let data = response.data
  //     var ret = { ok: false, msg: '' }
  //     if (data) {
  //       ret.msg = data.msg ? data.msg : ''
  //       if (data.data) ret.data = data.data
  //       if (data.ok) {
  //         ret.ok = true
  //       }
  //     }
  //     return ret
  //   }).catch(error => {
  //     return Vue.prototype.$helpers.errorReturn(error)
  //   })
  //   return ret
  // }

  async delete (pCargaEntregaId, pVolumes = null) {
    var self = this
    try {
      // var ids = []
      // if (self.id ? (self.id > 0) : false) ids.push(self.id)
      // if (pOutrosIDS ? pOutrosIDS.length > 0 : false) {
      //   for (let index = 0; index < pOutrosIDS.length; index++) {
      //     ids.push(pOutrosIDS[index])
      //   }
      // }

      // if (ids.length === 0) throw new Error('Nenhum item informado')
      if (!pCargaEntregaId) throw new Error('Número da carga não foi informado')
      if (!self.cargaentradaitem) throw new Error('Número do item da carga não foi informado')
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    var params = {
      cargaentradaitem: self.cargaentradaitem
    }
    if (pVolumes ? pVolumes.length > 0 : false) {
      params['volumes'] = pVolumes.join(',')
    }
    let ret = await Vue.prototype.$axios.delete('v1/cargaentrega/carga/' + pCargaEntregaId + '/itens', { params: params }).then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.data = data.data ? data.data : null
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) ret.ok = true
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  // async etiquetasgerar () {
  //   var self = this
  //   try {
  //     if (!(self.id)) throw new Error('Número da carga não foi informado')
  //     if (!(self.id > 0)) throw new Error('Número da carga não foi informado')
  //     if (!self.cargaentregaid) throw new Error('Número da carga não foi informado')
  //     if (!(self.cargaentregaid > 0)) throw new Error('Número da carga não foi informado')
  //   } catch (error) {
  //     return { ok: false, msg: error.message, warning: false }
  //   }
  //   let ret = await Vue.prototype.$axios.post('v1/cargaentrega/carga/' + self.cargaentregaid + '/itens/id/' + self.id + '/etiquetas/gerar').then(async response => {
  //     let data = response.data
  //     var ret = { ok: false, msg: '' }
  //     if (data) {
  //       ret.msg = data.msg ? data.msg : ''
  //       if (data.ok) {
  //         ret.ok = true
  //         if (data.data) self.etiquetas = data.data
  //       }
  //     }
  //     return ret
  //   }).catch(error => {
  //     return Vue.prototype.$helpers.errorReturn(error)
  //   })
  //   return ret
  // }
}

export default CargaEntregaItemAgrupado
