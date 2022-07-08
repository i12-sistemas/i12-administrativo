import Vue from 'vue'
import moment from 'moment'
import DialogView from 'src/pages/comercial/nfe/viewdialog.vue'
class NFe {
  constructor (pChave) {
    this.limpardados()
    if (pChave) {
      if (pChave !== '') {
        this.setChave(pChave)
      }
    }
  }

  async limpardados () {
    this.chave = ''
    this.cUF = null
    this.AAMM = null
    this.mesAno = null
    this.CNPJ = null
    this.mod = null
    this.serie = null
    this.nNF = null
    this.tpEmis = ''
    this.cNF = ''
    this.cDV = ''
    this.danfe = null
    this.valido = false
    this.error = null
  }

  async isValid () {
    var v = { ok: false, msg: '' }
    this.valido = false
    try {
      if (!(this.chave.length === 44)) throw new Error('Chave deve conter 44 números')
      if (!this.nNF) throw new Error('Número da nota não foi identificado')
      if (!(this.nNF > 0)) throw new Error('Número da nota inválido')

      var dv = await this.getMod11(this.chave)
      if (parseInt(this.cDV) !== dv) throw new Error('Digíto verificador inválido. Verifique o número da chave.')

      if (!this.CNPJ) throw new Error('CNPJ da chave não foi identificado')
      if (String(this.CNPJ).length !== 14) throw new Error('CNPJ ' + this.CNPJ + ' inválido')
      if (!Vue.prototype.$helpers.validarCNPJCPF(this.CNPJ)) throw new Error('CNPJ ' + this.CNPJ + ' inválido')

      var mes = moment(this.mesAno, 'MM/YYYY')
      if (!mes.isValid()) throw new Error('Mês/Ano de emissão inválido')

      this.valido = true
      this.error = null
      v.ok = true
    } catch (error) {
      v.ok = false
      v.msg = error.message
      this.error = error.message
    }
    return v
  }

  setMesAno () {
    this.mesAno = null
    try {
      if (!this.AAMM) throw new Error('-')
      let a = this.AAMM
      let ano = parseInt(a.substring(0, 2))
      ano = 2000 + ano
      a = a.substring(2, 4) + '/' + ano
      this.mesAno = a
    } catch (error) {
      this.mesAno = null
    }
  }

  async setChave (pChave) {
    this.limpardados()
    if (pChave) {
      this.chave = pChave
    } else {
      this.chave = ''
    }
    var ch = this.chave
    // cUF - Código da UF do emitente do Documento Fisca 0, 2
    // AAMM - Ano e Mês de emissão da NF-e;
    // CNPJ - CNPJ do emitente;
    // mod - Modelo do Documento Fiscal;
    // serie - Série do Documento Fiscal;
    // nNF - Número do Documento Fiscal;
    // tpEmis – forma de emissão da NF-e;
    // cNF - Código Numérico que compõe a Chave de Acesso;
    // cDV - Dígito Verificador da Chave de Acesso.
    var nota = [
      { tag: 'cUF', value: '', size: 2 },
      { tag: 'AAMM', value: '', size: 4 },
      { tag: 'CNPJ', value: '', size: 14 },
      { tag: 'mod', value: '', size: 2 },
      { tag: 'serie', value: '', size: 3 },
      { tag: 'nNF', value: '', size: 9 },
      { tag: 'tpEmis', value: '', size: 1 },
      { tag: 'cNF', value: '', size: 8 },
      { tag: 'cDV', value: '', size: 1 }
    ]
    // 35190808957311000155550000003068981672367898
    // 35 . 1908 . 08957311000155 . 55 . 000 . 000306898 . 1 . 67236789 . 8
    // var l = ch.length
    var p = 0
    for (let index = 0; index < nota.length; index++) {
      const element = nota[index]
      element.value = ch.substring(p, p + element.size)
      switch (element.tag) {
        case 'cUF':
          this.cUF = element.value
          break
        case 'AAMM':
          this.AAMM = element.value
          this.setMesAno()
          break
        case 'CNPJ':
          this.CNPJ = element.value
          break
        case 'mod':
          this.mod = element.value
          break
        case 'serie':
          this.serie = element.value
          break
        case 'nNF':
          try {
            this.nNF = parseInt(element.value)
          } catch (error) {
            this.nNF = null
            console.error(error)
          }
          break
        case 'tpEmis':
          this.tpEmis = element.value
          break
        case 'cNF':
          this.cNF = element.value
          break
        case 'cDV':
          this.cDV = element.value
          break
      }
      p = p + element.size
    }

    this.isValid()
    return nota
  }

  async getMod11 (chaveSemDigito) {
    var chave = chaveSemDigito.substring(0, 43)
    var variavel = 2
    var total = 0
    var dv = 0
    for (var i = chave.length - 1; i >= 0; i--) {
      var n = parseInt(chave.charAt(i))
      n = n * variavel
      variavel++
      if (variavel > 9) variavel = 2
      total += n
    }
    // Porque o total é modulado por onze após as somas...
    total = total % 11
    if (total === 0 || total === 1) {
      dv = 0
    } else {
      dv = 11 - total
    }
    return dv
  }

  async getpdf (pForceChave = null) {
    var self = this
    try {
      var permite = Vue.prototype.$helpers.permite('comercial.nfe.consultaporchave')
      if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    var ch = this.chave
    if (pForceChave) ch = pForceChave
    let params = {}
    self.danfe = null
    let ret = await Vue.prototype.$axios.get('v1/nfe/export/pdf/' + ch, { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        ret.ok = data.ok ? data.ok : false
        if (data.data) {
          self.danfe = data.data
          ret.data = data.data
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async dialogShow (app, pForceChave = null) {
    var self = this
    try {
      var permite = Vue.prototype.$helpers.permite('comercial.nfe.consultaporchave')
      if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    var ch = self.chave
    if (pForceChave) ch = pForceChave
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: DialogView,
        chave: ch,
        cancel: true
      }).onOk(async retOk => {
        var ret = { ok: retOk }
        resolve(ret)
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }
}

export default NFe
