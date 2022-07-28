import moment from 'moment'
import jsmd5 from 'js-md5'
import bcrypt from 'bcryptjs'
import PlacaMercosul from './placa-mercosul-brasil'
import dialoginputfloat from 'src/components/cpn-input-dialog-float.vue'
import dialogfilterfloatinterval from 'src/components/cpn-filter-float-interval.vue'
import dialogfilterdatetimeinterval from 'src/components/cpn-filter-datetime-interval.vue'
import dialogtableshowcolumn from 'src/components/tableshowcolumn.vue'
import { QSpinnerOval } from 'quasar'

/* eslint-disable no-useless-escape */
export default async ({ Vue, store }) => {
  Vue.prototype.$helpers = {
    echo (p1) {
      return p1
    },
    ifnull (pVal, pDefault) {
      if (pVal) {
        return pVal
      } else {
        return pDefault
      }
    },
    async saveTableColunasLocalStorage (app, localstorageid, data) {
      let usuariologado = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.user) {
          usuariologado = app.$store.state.usuario.user
        }
      }
      var local = 'tablecols_' + localstorageid + '_' + (usuariologado ? usuariologado.id : '')
      localStorage.setItem(local, JSON.stringify(data))
      return true
    },
    async recoveryTableColunasLocalStorage (app, localstorageid) {
      let usuariologado = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.user) {
          usuariologado = app.$store.state.usuario.user
        }
      }
      var local = 'tablecols_' + localstorageid + '_' + (usuariologado ? usuariologado.id : '')
      return localStorage.getItem(local) ? JSON.parse(localStorage.getItem(local)) : null
    },
    async dialogConfigTableColunas (app, visibleColumns, columns, title = null) {
      return new Promise((resolve) => {
        app.$q.dialog({
          parent: app,
          component: dialogtableshowcolumn,
          colvisible: visibleColumns,
          columns: columns,
          title: title,
          cancel: true
        }).onOk(async data => {
          resolve({ ok: true, data: data })
        }).onCancel(() => {
          resolve({ ok: false })
        })
      })
    },
    async dialogFilterFloatInterval (app, dados) {
      return new Promise((resolve) => {
        app.$q.dialog({
          parent: app,
          component: dialogfilterfloatinterval,
          dados: dados,
          cancel: true
        }).onOk(async retOk => {
          resolve({ ok: true, dados: retOk })
        }).onCancel(() => {
          resolve(null)
        })
      })
    },
    async dialogInputFloat (app, dados) {
      return new Promise((resolve) => {
        app.$q.dialog({
          parent: app,
          component: dialoginputfloat,
          dados: dados,
          cancel: true
        }).onOk(async value => {
          resolve({ ok: true, value: value })
        }).onCancel(() => {
          resolve({ ok: false })
        })
      })
    },
    async dialogFilterDateTimeInterval (app, dados) {
      return new Promise((resolve) => {
        app.$q.dialog({
          parent: app,
          component: dialogfilterdatetimeinterval,
          dados: dados,
          cancel: true
        }).onOk(async retOk => {
          resolve({ ok: true, dados: retOk })
        }).onCancel(() => {
          resolve(null)
        })
      })
    },
    dialogProgress (app, pMsg = '', pTitle = '') {
      return app.$q.dialog({
        title: pTitle,
        message: pMsg,
        class: 'bg-grey-4 text-black',
        progress: {
          spinner: QSpinnerOval,
          color: 'black'
        },
        persistent: true, // we want the user to not be able to close it
        ok: false // we want the user to not be able to close it
      })
    },
    toBool (val) {
      return (val === true) || (val === 1) || (val === '1') || (val === 'true') || (val === 'True') || (val === 'TRUE')
    },
    getUserConfigLocal (flag, valuedefault) {
      var config = localStorage.getItem('cnfuser_' + flag) ? JSON.parse(localStorage.getItem('cnfuser_' + flag)) : { value: valuedefault }
      if (!config) return valuedefault
      if (!config.value) return valuedefault
      if (!config.type) return config.value
      switch (config.type) {
        case 'string':
          return config.value.toString()
        case 'int':
          return parseInt(config.value)
        case 'bool':
          return this.toBool(config.value)
        case 'object':
          return config.value ? JSON.parse(config.value) : null
        default:
          return config.value
      }
    },
    setUserConfigLocal (flag, type, value) {
      var config = {
        type: type,
        value: value
      }
      if (type === 'object') config.value = JSON.stringify(value)
      localStorage.setItem('cnfuser_' + flag, JSON.stringify(config))
    },
    mascaraCpfCnpj (valor) {
      if (valor === null) return ''
      if (valor.length <= 11) {
        return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, '\$1.\$2.\$3\-\$4')
      } else {
        return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, '\$1.\$2.\$3\/\$4\-\$5')
      }
    },
    copytoclipboard (pStr) {
      // Create a <textarea> element
      const el = document.createElement('textarea')
      // Set its value to the string that you want copied
      el.value = pStr
      // Make it readonly to be tamper-proof
      el.setAttribute('readonly', '')
      el.style.position = 'absolute'
      // Move outside the screen to make it invisible
      el.style.left = '-9999px'
      // Append the <textarea> element to the HTML document
      document.body.appendChild(el)
      // Check if there is any content selected previously
      // Store selection if found
      // Mark as false to know no selection existed before
      const selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false
      // Select the <textarea> content
      el.select()
      // Copy - only works as a result of a user action (e.g. click events)
      document.execCommand('copy')
      // Remove the <textarea> element
      document.body.removeChild(el)
      // If a selection existed before copying
      if (selected) {
        // Unselect everything on the HTML document
        document.getSelection().removeAllRanges()
        // Restore the original selection
        document.getSelection().addRange(selected)
      }
      Vue.prototype.$q.notify({
        color: 'blue',
        icon: 'content_copy',
        timeout: 1500,
        message: 'Texto copiado para sua área de transferência',
        caption: 'Texto copiado :: ' + pStr
      })
    },
    errorReturn (pError, pValidacao = false) {
      try {
        if (!pError) throw new Error('Erro desconhecido (error null)')
        var msg = ''
        var code = -1
        var title = 'Ops! Algo não deu certo'
        var nCateg = 0
        if (pError.response) {
          code = pError.response.status ? pError.response.status.toString() : ''
          nCateg = parseInt(code.substring(0, 1))
          msg = (pError.response.data.message ? pError.response.data.message : (pError.response.data.msg ? pError.response.data.msg : '')) + ' - Code: ' + code
          if (nCateg === 4) title = 'Algo não deu certo aqui no app'
          if (nCateg === 5) title = 'Algo não deu certo no servidor'
          if (nCateg === 3) title = 'Algum redicionamento ocorreu'
        } else {
          msg = pError.message
        }
        var r = { ok: false, msg: msg, error: { code: code, error: pError, title: title, msg: msg, codeinit: nCateg } }
        if (pValidacao) r.warning = true
        return r
      } catch (error) {
        return { ok: false, msg: error.message, error: { code: -1, error: pError, title: 'Ops! Algo não deu certo', msg: error.message } }
      }
    },
    isNotEmpty (value) {
      return value !== undefined && value !== null && value !== ''
    },
    colorByCNPJ (pCNPJ) {
      const colors = ['blue-10', 'cyan-7', 'purple-10', 'pink-7', 'deep-purple-7', 'green-9', 'amber-10', 'brown-10', 'blue-grey-10', 'yellow-13']
      var s = pCNPJ.toString().charAt(0)
      var n = parseInt(s)
      var color = colors[n]
      return color
    },
    colorArray (idx, hex = true) {
      const color = ['blue-10', 'cyan-7', 'purple-10', 'pink-7', 'deep-purple-7', 'green-9', 'amber-10', 'brown-10', 'blue-grey-10', 'yellow-13']
      const colorhex = ['#ff6384', '#00acc1', '#ff9f40', '#ff6384', '#4bc0c0', '#ffcd56', '#ff6f00', '#4bc0c0', '#36a2eb', '#ffea00']
      return (hex ? colorhex[idx] : color[idx])
    },
    lastChar (pStr) {
      const s = pStr.toString()
      const l = s.charAt(s.length - 1)
      return l
    },
    showTelefone (pTel, pNome) {
      var app = Vue.prototype
      if (!pTel) return
      if (pTel === '') return
      app.$q.bottomSheet({
        dark: false,
        message: 'Tel.: ' + app.$helpers.mascaratel(pTel) + ((pNome ? pNome !== '' : false) ? ' :: ' + pNome : ''),
        actions: [
          {
            label: 'Discar',
            icon: 'local_phone',
            id: 'phone'
          },
          {
            label: 'WhatsApp ',
            icon: 'fab fa-whatsapp',
            id: 'whatsapp'
          },
          {
            label: 'SMS',
            icon: 'fas fa-sms',
            id: 'sms'
          }
        ]
      }).onOk(action => {
        if (action.id === 'phone') {
          app.$helpers.discartel(pTel)
        }
        if (action.id === 'whatsapp') {
          app.$helpers.whatsapp(pTel)
        }
        if (action.id === 'sms') {
          app.$helpers.sendsms(pTel)
        }
      })
    },
    async showDialog (ret, forceAsError) {
      var title = ''
      var bgcolor = 'white'
      var color = 'black'
      var msg = ret.msg ? ret.msg : ''
      if (ret.error) {
        if (ret.error.codeinit === 5) {
          bgcolor = 'red-9'
          color = 'white'
          title = ':-( Ops'
        } else if (ret.error.codeinit === 4) {
          bgcolor = 'yellow-9'
          color = 'white'
          title = ':-( Ops'
        } else {
          bgcolor = 'black'
          color = 'white'
          title = ':-( Ops'
        }
        if (ret.error) {
          msg = '<p class="text-body">' + ret.error.title + ', se o problema persistir faça contato com o suporte.</p>' +
                '<p class="text-caption">Msg técnica: ' + ret.error.msg + '</p>'
        }
      }
      if (ret.warning) {
        bgcolor = 'blue-grey'
        color = 'white'
        title = ''
        msg = ret.msg
      }
      if (ret.permissaonegada) {
        bgcolor = 'grey-10'
        color = 'white'
        title = 'Acesso negado!'
        msg = ret.msg
      }

      if ((!ret.error) && (!ret.ok) && (forceAsError) && (!ret.permissaonegada)) {
        bgcolor = 'red-9'
        color = 'white'
        title = ':-( Ops'
        msg = '<p class="text-body">' + ret.msg + '</p>' +
              '<p class="text-caption">Se o problema persistir faça contato com o suporte.</p>'
      }
      if (msg ? msg !== '' : false) {
        return new Promise((resolve) => {
          Vue.prototype.$q.dialog({
            title: title,
            message: msg,
            html: true,
            cancel: false,
            persistent: true,
            color: color,
            class: 'bg-' + bgcolor + ' text-' + color
          }).onOk(async data => {
            resolve({ ok: true })
          }).onCancel(() => {
            resolve({ ok: true })
          })
        })
      } else {
        return false
      }
    },
    randomIntFromInterval (min, max) { // min and max included
      return Math.floor(Math.random() * (max - min + 1) + min)
    },
    generateUUID (hideMask) {
      var d = new Date().getTime()
      // Time in microseconds since page-load or 0 if unsupported
      var d2 = (performance && performance.now && (performance.now() * 1000)) || 0
      var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16
        if (d > 0) {
          // eslint-disable-next-line no-redeclare
          var r = (d + r) % 16 | 0
          d = Math.floor(d / 16)
        } else {
          // eslint-disable-next-line no-redeclare
          var r = (d2 + r) % 16 | 0
          d2 = Math.floor(d2 / 16)
        }
        return (c === 'x' ? r : (r & 0x7 | 0x8)).toString(16)
      })
      if (hideMask) uuid = uuid.replace('-', '')
      return uuid
    },
    emailhide (email, showcharcount = 3) {
      let hide = email.split('@')[0].length - showcharcount
      var r = new RegExp('.{' + hide + '}@', 'g')
      return email.replace(r, '***@')
    },
    emailphone (phone, countIni = 2, countEnd = 4) {
      if (typeof phone === 'undefined') return ''
      if (phone ? phone === '' : true) return ''
      var init = phone.substring(0, countIni)
      var ind = phone.length - countEnd
      var end = phone.substring(ind)
      const char = '*'
      const telNewNum = init + char.repeat(ind - countIni) + end
      return telNewNum
    },
    replaceAll (str, needle, replacement) {
      if ((str === '') || (needle === '')) return str
      var i = 0
      while ((i = str.indexOf(needle, i)) !== -1) {
        str = str.replace(needle, replacement)
      }
      return str
    },
    async bcrypt (password, salt) {
      var lsalt = bcrypt.genSaltSync(salt)
      var hash = bcrypt.hashSync(password, lsalt)
      return hash
    },
    async bcryptcompare (pwdText, pwdhashed) {
      var r = await bcrypt.compare(pwdText, pwdhashed)
      return r
    },
    href (link, target = '_blank') {
      window.open(link, target)
    },
    sendmail (mail) {
      window.open('mailto:' + mail, '_blank')
    },
    whatsapp (number) {
      window.open('https://api.whatsapp.com/send?phone=55' + number, '_blank')
    },
    discartel (number) {
      window.open('tel:' + number, '_blank')
    },
    sendsms (number, msg) {
      window.open('sms:' + number + (msg === '' ? '' : '?body=' + msg), '_blank')
    },
    isOdd (index) {
      if (index % 2 === 0) {
        return true
      }
    },
    mapslink (endereco) {
      let link = 'http://maps.google.com/maps?q=' + encodeURIComponent(endereco)
      window.open(link, '_blank')
    },
    mapsroutelink (origin, destination) {
      let link = 'https://www.google.com/maps/dir/?api=1&travelmode=driving' +
               ((origin !== '') ? '&origin=' + encodeURIComponent(origin) : '') +
               ((destination !== '') ? '&destination=' + encodeURIComponent(destination) : '')
      window.open(link, '_blank')
    },
    mascaraCEP (valor) {
      if (valor === null) return ''
      return valor.replace(/(\d{2})(\d{3})(\d{3})/g, '\$1.\$2\-\$3')
    },
    mascaraChaveNFe (valor) {
      if (valor === null) return ''
      return valor.replace(/(\d{4})(\d{4})(\d{4})(\d{4})(\d{4})(\d{4})(\d{4})(\d{4})(\d{4})(\d{4})(\d{4})/g, '\$1 \$2 \$3 \$4 \$5 \$6 \$7 \$8 \$9 \$10 \$11 ')
    },
    paginationInfo (currentPage, pagesize, paginationtotal) {
      var info = ''
      if (paginationtotal === 0) {
        info = 'Nenhum registro'
      } else {
        if (paginationtotal < pagesize) {
          info = 'Mostrando ' + paginationtotal + ' registro(s)'
        } else {
          info =
            'Página ' +
            currentPage +
            ' - Mostrando ' +
            pagesize +
            ' de ' +
            paginationtotal +
            ' registro(s)'
        }
      }
      return info
    },
    md5 (s) {
      return jsmd5(s)
    },
    getAge (born) {
      var now = new Date()
      var nborn = new Date(born)
      var birthday = new Date(now.getFullYear(), nborn.getMonth(), nborn.getDate())
      if (now >= birthday) {
        return now.getFullYear() - nborn.getFullYear()
      } else {
        return now.getFullYear() - nborn.getFullYear() - 1
      }
    },
    MinToHourTxt (Minutes, middleChar = 'h', EndChar = 'm') {
      var hours = Math.trunc(Minutes / 60)
      var shr = ''
      if (hours <= 0) {
        shr = ''
      } else {
        shr = hours + middleChar
      }
      var minutes = (Minutes % 60).toFixed(0)
      var s = minutes + ''
      var n = s.length
      if (n === 1) {
        s = '0' + s
      } else if (n === 0) {
        s = '00' + s
      }
      return shr + s + EndChar
    },
    convertCorDelphiToHtml (pcor, pdefault = 'white') {
      if (!pcor) return pdefault
      var cor = pcor
      var n = cor.indexOf('cl')
      if (n === 0) cor = cor.substr(2, cor.length)
      n = cor.indexOf('$')
      if (n === 0) cor = '#' + cor.substr(2, cor.length)
      if (cor !== '') return cor
      return pdefault
    },
    HourToHHMM (horaStr, middleChar = 'h', EndChar = 'm') {
      var r = '-'
      try {
        var hora = moment(horaStr, 'HH:mm:ss')
        r = hora.format('HH') + middleChar + hora.format('mm') + EndChar
      } catch (error) {
        console.error(error)
        r = '-'
      }
      return r
    },
    minToHour (n, separator = ':', siglaHour = '', siglaMin = '') {
      var num = n
      var hours = (num / 60)
      var rhours = Math.floor(hours)
      var minutes = (hours - rhours) * 60
      var rminutes = Math.round(minutes)
      return rhours + siglaHour + separator + rminutes + siglaMin
    },
    getPerc (vrlatual, vlrtotal, multiplicoCem = true, QtdeCasaDecimal = 2, forcelimit100 = false) {
      if (vrlatual === 0 || vlrtotal === 0) {
        return 0
      }

      var perc = vrlatual / vlrtotal
      if (multiplicoCem) {
        perc = perc * 100
      }
      perc = perc.toFixed(QtdeCasaDecimal)
      if ((forcelimit100) && (perc > 100)) perc = 100
      return perc
    },
    DateToData (data) {
      try {
        var d = this.strToMoment(data, 'DD/MM/YYYY')
        if (!d.isValid()) throw new Error('Data inválida')
        return d.format('YYYY-MM-DD')
      } catch (error) {
        return null
      }
    },
    dateCompare (pD1, pOperator, pD2) {
      try {
        var d1 = new Date()
        var d2 = new Date()
        if (pD1) d1 = new Date(pD1)
        if (pD2) d2 = new Date(pD2)
        if (pOperator === '<') {
          return d1 < d2
        } else if (pOperator === '<=') {
          return d1 <= d2
        } else if (pOperator === '>') {
          return d1 > d2
        } else if (pOperator === '>=') {
          return d1 >= d2
        } else if (pOperator === '==') {
          return d1 === d2
        } else if (pOperator === '!==') {
          return d1 !== d2
        }
      } catch (error) {
        return false
      }
    },
    strDateToFormated (data, maskin, maskout) {
      try {
        var d = this.strToMoment(data, maskin)
        if (!d.isValid()) throw new Error('Data inválida')
        return d.format(maskout)
      } catch (error) {
        return null
      }
    },
    dateToBR (date) {
      var d = this.strToMoment(date)
      if (d.isValid()) {
        return d.format('DD/MM/YYYY')
      } else {
        return null
      }
    },
    formatMilhar (valor) {
      return this.formatRS(valor, false, 0, false)
    },
    secToTime (pSeconds, HideHourIfZero = true) {
      const sec = parseInt(pSeconds, 10) // convert value to number if it's string
      let hours = Math.floor(sec / 3600) // get hours
      let minutes = Math.floor((sec - (hours * 3600)) / 60) // get minutes
      let seconds = sec - (hours * 3600) - (minutes * 60) //  get seconds
      // add 0 if value < 10
      if (hours < 10) hours = '0' + hours
      if (minutes < 10) minutes = '0' + minutes
      if (seconds < 10) seconds = '0' + seconds
      return ((!(hours > 0) && HideHourIfZero) ? '' : hours + 'hr ') +
             (((hours > 0) && !(minutes > 0)) ? '' : minutes + 'min ') +
             ((hours > 0) || !(seconds > 0) ? '' : seconds + 'seg')
    },
    timeToBR (date, mask) {
      return moment(date, 'YYYY-MM-DD HH:mm:ss').format(mask)
    },
    datetimeFormat (date, mask) {
      moment.locale('pt-br')
      if (date === '' || date === undefined) return '-'
      var dh = this.strToMoment(date)
      if (dh < moment('01/01/1900', 'YYYY-MM-DD')) return '-'
      return dh.format(mask)
    },
    isToday (pDate) {
      var dh = null
      if (moment.isMoment(pDate)) {
        dh = pDate
      } else {
        moment.locale('pt-br')
        if (pDate === '' || pDate === undefined) return
        dh = this.strToMoment(pDate)
        if (!dh.isValid()) return
      }
      return (dh.format('DD/MM/YYYY') === moment().format('DD/MM/YYYY'))
    },
    datetimeToBR (date, OmiteDataSeHoje = false, OmiteSegundo = false) {
      var dh = null
      if (moment.isMoment(date)) {
        dh = date
      } else {
        moment.locale('pt-br')
        if (date === '' || date === undefined) return
        dh = this.strToMoment(date)
        if (!dh.isValid()) return
      }
      var mask = 'DD/MM/YYYY - HH:mm:ss'
      if (OmiteSegundo) mask = 'DD/MM/YYYY - HH:mm'
      if (OmiteDataSeHoje) {
        var today = dh.format('DD/MM/YYYY') === moment().format('DD/MM/YYYY')
        if (today) mask = OmiteSegundo ? 'HH:mm' : 'HH:mm:ss'
      } else {
        mask = 'DD/MM/YYYY - HH:mm' + (OmiteSegundo ? '' : ':ss')
      }
      return dh.format(mask)
    },
    datetimeRelativeToday (datetime) {
      moment.locale('pt-br')
      if (datetime) {
        var dh = this.strToMoment(datetime)
        return dh.fromNow()
      } else {
        return '-'
      }
    },
    durationHumanize (pNumber, pUnit) {
      moment.locale('pt-br')
      var pDuration = moment.duration(pNumber, pUnit)
      if (!pDuration) return
      var s = ''
      var seconds = pDuration.seconds()
      var minutes = pDuration.asMinutes()
      var hours = pDuration.asHours()
      var days = pDuration.asDays()
      if (seconds > 0) {
        s = seconds + ' seg.'
      }

      if (minutes > 0) {
        s = minutes + ' min' + (seconds > 0) ? ' ' + seconds + ' seg.' : ''
      }

      if (hours > 0) {
        s = hours + ' h ' + minutes + ' min'
      }

      if (days > 0) {
        s = days + 'd. ' + hours + 'h ' + minutes + ' min'
      }
      return s
    },
    strToCurr (pStr, pDecimal = ',') {
      var s = pStr
      var tipo = typeof pStr
      if (tipo === 'number') {
        return pStr
      }

      if ((pStr.indexOf(',') === -1) && (pStr.indexOf('.') >= 0)) {
        pDecimal = ''
        tipo = ''
      }

      if (tipo === 'string') {
        if (pDecimal === ',') {
          s = this.replaceAll(s, '.', '')
          s = this.replaceAll(s, ',', '.')
        } else {
          s = this.replaceAll(s, ',', '')
          s = this.replaceAll(s, '.', ',')
        }
      }
      var v = parseFloat(s)
      return v
    },
    formatRS (valor, ShowCifrao = true, QtdeCasaDecimal = 2, omitirPontoDecimal = false) {
      var v = 0
      if (typeof valor === 'undefined') return ''
      if (typeof valor === 'object') {
        if (!valor) return ''
      }

      if (typeof valor === 'string') {
        v = parseFloat(valor)
      } else {
        v = valor
      }
      v = v.toFixed(QtdeCasaDecimal)
      var numero = v.split('.')
      numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.')
      var s = numero.join(',')

      if (omitirPontoDecimal) {
        s = v.toString()
        s = s.replace('.', ',')
        return s
      }

      return (ShowCifrao ? 'R$ ' : '') + (v !== 0 ? s : '-')
    },
    formatBigValue (valor, QtdeCasaDecimal = 0) {
      var v = 0
      if (typeof valor === 'undefined') return ''
      if (typeof valor === 'object') {
        if (!valor) return ''
      }

      if (typeof valor === 'string') {
        v = parseFloat(valor)
      } else {
        v = valor
      }
      var n = 0
      var s = ''
      v = v.toFixed(QtdeCasaDecimal)
      if (v > 1000) {
        n = v / 1000
        n = n.toFixed(QtdeCasaDecimal)
        s = n + ' K'
      }
      if (v > 1000000) {
        n = v / 1000000
        n = n.toFixed(QtdeCasaDecimal)
        s = n + ' M'
      }
      if (v > 1000000000) {
        n = v / 1000000000
        n = n.toFixed(QtdeCasaDecimal)
        s = n + ' B'
      }
      return s
    },
    padLeftZero (num, size) {
      var s = num + ''
      while (s.length < size) s = '0' + s
      return s
    },
    permite (permissao) {
      try {
        if (!store) throw new Error('Store inválido')
        if (!store.state.usuario) throw new Error('Usuário logado não foi reconhecimento')
        if (!store.state.usuario.user) throw new Error('Nenhum usuário logado')
        if (!(store.state.usuario.user.id > 0)) throw new Error('Nenhum usuário logado')
        if (!store.state.usuario.user.permissoes) throw new Error('Usuário sem nenhuma permissão cadastrada. [' + permissao + ']')
        if (store.state.usuario.user.permissoes.length <= 0) throw new Error('Usuário sem nenhuma permissão cadastrada. [' + permissao + ']')
        var idx = store.state.usuario.user.permissoes.indexOf(permissao)
        if (!(idx >= 0)) throw new Error('Usuário sem permissão.  [' + permissao + ']')
        return { ok: true }
      } catch (error) {
        return { ok: false, msg: error.message, permissaonegada: true }
      }
    },
    forceFileDownload (url) {
      var self = this
      const link = document.createElement('a')
      link.href = url
      var name = url.split('/').pop()
      link.setAttribute('download', name)
      document.body.appendChild(link)
      link.click()

      Vue.prototype.$q.notify({
        color: 'green',
        icon: 'fas fa-file-download',
        position: 'bottom-left',
        multiLine: true,
        timeout: 5000,
        message: 'Download iniciado...',
        caption: 'Confira seu download na área de download. Caso não tenha iniciado clique em "Tentar novamente"',
        actions: [
          {
            label: 'Tentar novamente',
            color: 'white',
            handler: () => {
              self.href(url)
            }
          }
        ]
      })
    },
    checkpermissao (minhaspermissoes, permissao) {
      let idx = minhaspermissoes.indexOf(permissao)
      return idx >= 0
    },
    nl2br (str, isxhtml) {
      if (typeof str === 'undefined' || str === null) {
        return ''
      }
      var breakTag = isxhtml || typeof isxhtml === 'undefined' ? '<br />' : '<br>'
      return (str + '').replace(
        /([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,
        '$1' + breakTag + '$2'
      )
    },
    clearMask (str) {
      let s = str
      s = s.replace(/[.,-/_/ /(/)/[\]]/g, '')
      return s
    },
    mascaraCpf (valor) {
      return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, '\$1.\$2.\$3\-\$4')
    },
    mascaraCnpj (valor) {
      return valor.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, '\$1.\$2.\$3\/\$4\-\$5')
    },
    async showPrint (href) {
      var link = href
      var myWindow = window.open(link, '', 'width=' + (Screen.width - 150) + 'px,height=' + (Screen.height - 40) + 'px,resizable=0,scrollbars=0,status=0,toolbar=0')
      myWindow.focus()
    },
    mascaraDocCPFCNPJ (valor) {
      let d = valor || ''
      d = this.clearMask(d)
      if (d.length === 14) {
        return this.mascaraCnpj(d)
      } else {
        return this.mascaraCpf(d)
      }
    },
    mascaratel (telefone) {
      let v = this.clearMask(telefone)
      let q = v.length
      if (q === 10) {
        v = v.replace(/^(\d{2})(\d)/g, '($1) $2') // Coloca parênteses em volta dos dois primeiros dígitos
        v = v.replace(/(\d)(\d{4})$/, '$1-$2') // Coloca hífen entre o quarto e o quinto dígitos
      } else if (q === 11) {
        v = v.replace(/^(\d{2})(\d)/g, '($1) $2') // Coloca parênteses em volta dos dois primeiros dígitos
        v = v.replace(/(\d)(\d{4})$/, '$1-$2') // Coloca hífen entre o quarto e o quinto dígitos
      } else if (q === 9) {
        // v = v.replace(/^(\d{2})(\d)/g, '($1) $2') // Coloca parênteses em volta dos dois primeiros dígitos
        v = v.replace(/(\d)(\d{4})$/, '$1-$2') // Coloca hífen entre o quarto e o quinto dígitos
      } else if (q === 8) {
        // v = v.replace(/^(\d{2})(\d)/g, '($1) $2') // Coloca parênteses em volta dos dois primeiros dígitos
        v = v.replace(/(\d)(\d{4})$/, '$1-$2') // Coloca hífen entre o quarto e o quinto dígitos
      } else {
        v = v.replace(/(\d)(\d{4})$/, '$1-$2') // Coloca hífen entre o quarto e o quinto dígitos
      }

      return v
    },
    strEllipsis (str, maxChar) {
      let s = str || ''
      if (s.length > maxChar) {
        return s.substring(0, maxChar) + '...'
      } else {
        return s
      }
    },
    strToMoment (pStrDatetime) {
      moment.locale('pt-br')
      var dh = moment()
      if (pStrDatetime.toString().indexOf('T') >= 0) {
        dh = moment(pStrDatetime)
      } else {
        var tembarra = (pStrDatetime.toString().indexOf('/') >= 0)
        dh = moment(pStrDatetime, (tembarra ? 'YYYY/MM/DD' : 'YYYY-MM-DD') + ' HH:mm:ss')
      }
      return dh
    },
    async validarCNPJCPF (doc) {
      let d = doc || ''
      var v = false
      if (d.length === 14) {
        v = this.validarCNPJ(d)
      } else {
        v = this.validaCPF(d)
      }
      return v
    },
    diffTime (pdh1, pdh2, unidadeTempo) {
      var dh1 = pdh1 === '' ? moment() : moment(pdh1)
      var dh2 = pdh2 === '' ? moment() : moment(pdh2)
      var diff = dh2.diff(dh1, unidadeTempo)
      return diff
    },
    diffDate (pdh1, pdh2, unidadeTempo) {
      var dh1 = pdh1 === '' ? moment(moment().format('YYYY-MM-DD')) : moment(moment(pdh1).format('YYYY-MM-DD'))
      var dh2 = pdh2 === '' ? moment(moment().format('YYYY-MM-DD')) : moment(moment(pdh2).format('YYYY-MM-DD'))
      var diff = dh2.diff(dh1, unidadeTempo)
      return diff
    },
    validarCNPJ (cnpj) {
      cnpj = cnpj.replace(/[^\d]+/g, '')
      if (cnpj === '') return false

      if (cnpj.length !== 14) { return false }

      // Elimina CNPJs invalidos conhecidos
      if (cnpj === '00000000000000' || cnpj === '11111111111111' || cnpj === '22222222222222' ||
            cnpj === '33333333333333' || cnpj === '44444444444444' || cnpj === '55555555555555' ||
            cnpj === '66666666666666' || cnpj === '77777777777777' || cnpj === '88888888888888' ||
            cnpj === '99999999999999') { return false }

      // Valida DVs
      let tamanho = cnpj.length - 2
      let numeros = cnpj.substring(0, tamanho)
      let digitos = cnpj.substring(tamanho)
      let soma = 0
      let pos = tamanho - 7
      let i
      for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--
        if (pos < 2) { pos = 9 }
      }
      let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
      if (parseInt(resultado) !== parseInt(digitos.charAt(0))) { return false }

      tamanho = tamanho + 1
      numeros = cnpj.substring(0, tamanho)
      soma = 0
      pos = tamanho - 7
      for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--
        if (pos < 2) { pos = 9 }
      }
      resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
      if (parseInt(resultado) !== parseInt(digitos.charAt(1))) { return false }

      return true
    },
    validaCPF (cpf) {
      cpf = cpf.replace(/[^\d]+/g, '')
      if (cpf === '') return false
      // Elimina CPFs invalidos conhecidos
      if (cpf.length !== 11 || cpf === '00000000000' || cpf === '11111111111' || cpf === '22222222222' || cpf === '33333333333' || cpf === '44444444444' || cpf === '55555555555' || cpf === '66666666666' || cpf === '77777777777' || cpf === '88888888888' || cpf === '99999999999') return false
      // Valida 1o digito
      var add = 0
      for (let i = 0; i < 9; i++) {
        add += parseInt(cpf.charAt(i)) * (10 - i)
      }

      var rev = 11 - (add % 11)
      if (rev === 10 || rev === 11) rev = 0
      if (rev !== parseInt(cpf.charAt(9))) return false
      // Valida 2o digito
      add = 0
      for (let i = 0; i < 10; i++) {
        add += parseInt(cpf.charAt(i)) * (11 - i)
      }
      rev = 11 - (add % 11)
      if (rev === 10 || rev === 11) rev = 0
      if (rev !== parseInt(cpf.charAt(10))) return false
      return true
    },
    getExtension (filename) {
      return filename.split('.').pop()
    },
    bytesToHumanFileSizeString (fileSizeInBytes) {
      var i = -1
      if (typeof fileSizeInBytes === 'undefined') return '0 Kb'
      if (fileSizeInBytes ? fileSizeInBytes <= 0 : true) return '0 Kb'
      var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB']
      do {
        fileSizeInBytes = fileSizeInBytes / 1024
        i++
      } while (fileSizeInBytes > 1024)

      return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i]
    },
    validaEmail (email) {
      var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/
      // var address = document.getElementById[email].value;
      return reg.test(email)
    },
    arrayObjectIndexOf (myArray, searchTerm, property) {
      for (var i = 0, len = myArray.length; i < len; i++) {
        if (myArray[i][property] === searchTerm) return i
      }
      return -1
    },
    joinElement (myArray, elemento, CharJoin) {
      var a = []
      myArray.forEach(element => {
        a.push(element[elemento])
      })
      return a.join(CharJoin)
    },
    replaceAt (str, index, replacement) {
      var s = str
      return (
        s.substr(0, index) +
        replacement +
        s.substr(index + replacement.length)
      )
    },
    jsonEqual (a, b) {
      return JSON.stringify(a) === JSON.stringify(b)
    },
    placaMask (pPlacaStr) {
      var placa = new PlacaMercosul()
      return placa.mask(pPlacaStr)
    },
    async  asyncForEach (array, callback) {
      for (let index = 0; index < array.length; index++) {
        await callback(array[index], index, array)
      }
    }
  }
}
