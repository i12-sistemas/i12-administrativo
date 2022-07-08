// import Vue from 'vue'
// import moment from 'moment'

class Configuracao {
  constructor (pItem, pDefault) {
    this._oldvalue = null
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
    this.defaultvalue = pDefault
  }

  async limpardados () {
    this.id = null
    this.tipo = null
    this._valor = null
  }

  get oldvalue () {
    return this._oldvalue
  }

  get valor () {
    return this._valor
  }

  valorAsDouble (decimal = 2) {
    return this._valor.toLocaleString('pt-br', { style: 'decimal', currency: 'BRL', useGrouping: false, minimumFractionDigits: decimal })
  }

  set valor (val) {
    switch (this.tipo) {
      case 'double':
        this._valor = val ? parseFloat(val) : 0
        break
      case 'integer':
        this._valor = val ? parseInt(val) : 0
        break
      case 'time':
        // self._valor = item.valor ? moment(item.valor, 'HH:mm') : self.defaultvalue
        this._valor = val
        break
      default:
        this._valor = val
        break
    }
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    self.id = item.id
    self.tipo = item.tipo
    self.valor = item.valor ? item.valor : self.defaultvalue
    self._oldvalue = self.valor
  }

  // async find (pID) {
  //   var self = this
  //   self.limpardados()
  //   let ret = await Vue.prototype.$axios.get('v1/cliente/' + pID).then(response => {
  //     let data = response.data
  //     var ret = { ok: false, msg: '' }
  //     if (data) {
  //       ret.msg = data.msg ? data.msg : ''
  //       if (data.ok) {
  //         ret.ok = true
  //         self.cloneFrom(data.data)
  //       }
  //     }
  //     return ret
  //   }).catch(error => {
  //     return Vue.prototype.$helpers.errorReturn(error)
  //   })
  //   return ret
  // }

  // async save () {
  //   var self = this
  //   try {
  //     var permissao = Vue.prototype.$helpers.permite('cadastros.clientes.save')
  //     if (!permissao.ok) return permissao

  //     if (!self.cnpj) throw new Error('Informe o CNPJ')
  //     if (!Vue.prototype.$helpers.validarCNPJCPF(self.cnpj)) throw new Error('CNPJ inválido')

  //     if (!self.razaosocial) throw new Error('Informe a Razão Social')
  //     if (self.razaosocial.trim() === '') throw new Error('Informe a Razão Social')
  //     if (!self.fantasia) throw new Error('Informe a Fantasia')
  //     if (self.fantasia.trim() === '') throw new Error('Informe a Fantasia')
  //     if (self.fantasia_followup.trim() === '') self.fantasia_followup = self.fantasia.trim()

  //     var cidadeid = self.endereco.cidade.id > 0 ? self.endereco.cidade.id : null
  //     if (!cidadeid) throw new Error('Obrigatório informar a cidade')
  //     if (!(cidadeid > 0)) throw new Error('Obrigatório informar a cidade')
  //   } catch (error) {
  //     return { ok: false, msg: error.message }
  //   }
  //   let params = {
  //     id: self.id ? (self.id > 0 ? self.id : null) : null,
  //     cnpj: self.cnpj,
  //     razaosocial: self.razaosocial,
  //     fantasia: self.fantasia,
  //     fantasia_followup: self.fantasia_followup,
  //     fone1: self.fone1,
  //     fone2: self.fone2,
  //     ativo: self.ativo,
  //     obs: self.obs,
  //     logradouro: self.endereco.logradouro,
  //     endereco: self.endereco.endereco,
  //     numero: self.endereco.numero,
  //     bairro: self.endereco.bairro,
  //     cep: self.endereco.cep,
  //     complemento: self.endereco.complemento,
  //     cidadeid: cidadeid
  //   }

  //   params.segqui_hr1_i = self.segqui_hr1_i
  //   params.segqui_hr1_f = self.segqui_hr1_f
  //   params.segqui_hr2_i = self.segqui_hr2_i
  //   params.segqui_hr2_f = self.segqui_hr2_f

  //   if (self.sex_hr1_i) params.sex_hr1_i = self.sex_hr1_i
  //   if (self.sex_hr1_f) params.sex_hr1_f = self.sex_hr1_f
  //   if (self.sex_hr2_i) params.sex_hr2_i = self.sex_hr2_i
  //   if (self.sex_hr2_f) params.sex_hr2_f = self.sex_hr2_f

  //   if (self.portaria_hr1_i) params.portaria_hr1_i = self.portaria_hr1_i
  //   if (self.portaria_hr1_f) params.portaria_hr1_f = self.portaria_hr1_f
  //   if (self.portaria_hr2_i) params.portaria_hr2_i = self.portaria_hr2_i
  //   if (self.portaria_hr2_f) params.portaria_hr2_f = self.portaria_hr2_f

  //   var emailsAction = null
  //   if (self.emails) {
  //     if (self.emails.length > 0) {
  //       emailsAction = []
  //       for (let index = 0; index < self.emails.length; index++) {
  //         const email = self.emails[index]
  //         var element = { email: email, action: '' }

  //         if (email.id > 0) {
  //           if (email.updated) element.action = 'update'
  //           if (email.deleted) element.action = 'delete'
  //         } else {
  //           element.action = 'insert'
  //         }
  //         if (element.action !== '') emailsAction.push(element)
  //       }
  //       if (emailsAction.length === 0) emailsAction = null
  //     }
  //   }

  //   if (self.cnpjmemo) params.cnpjmemo = self.cnpjmemo
  //   if (emailsAction) params.emails = emailsAction

  //   let ret = await Vue.prototype.$axios.post('v1/cliente', params).then(response => {
  //     let data = response.data
  //     var ret = { ok: false, msg: '' }
  //     if (data) {
  //       ret.msg = data.msg ? data.msg : ''
  //       if (data.ok) {
  //         ret.ok = true
  //         if (data.data) self.cloneFrom(data.data)
  //       }
  //     }
  //     return ret
  //   }).catch(error => {
  //     return Vue.prototype.$helpers.errorReturn(error)
  //   })
  //   return ret
  // }
}

export default Configuracao
