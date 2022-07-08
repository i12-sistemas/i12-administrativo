import moment from "moment";
import VueMomentJS from "vue-momentjs";
import jsmd5 from "js-md5";

export default {
  components: {
    moment,
    VueMomentJS
  },
  install: Vue => {
    String.prototype.trunc =
    function( n, useWordBoundary ){
        if (this.length <= n) { return this; }
        var subString = this.substr(0, n-1);
        return (useWordBoundary 
           ? subString.substr(0, subString.lastIndexOf(' ')) 
           : subString) + "...";
     };    
    Vue.prototype.$helpers = {
      echo(p1) {
        return p1;
      },
      md5(s) {
        return jsmd5(s);
      },
      base64enc(s) {
        return window.btoa(s);
      },
      base64dec(s) {
        return window.atob(s);
      },
      async sendPost(app, url, content) {
        return await app.axios
          .post(url, content)
          .then(function(resp) {
            var ret = resp.data;
            if (ret.ok) {
              var id = ret.id ? ret.id : null;
              return { ok: true, rows: ret.rows, id: id };
            } else {
              return { ok: false, msg: ret.msg };
            }
          })
          .catch(function(resp) {
            return { ok: false, msg: resp };
          });
      },
      MinToHourTxt(Minutes, middleChar = "h", EndChar = "m") {
        var hours = Math.trunc(Minutes / 60);
        var shr = "";
        if (hours <= 0) {
          shr = "";
        } else {
          shr = hours + middleChar;
        }
        var minutes = (Minutes % 60).toFixed(0);
        var s = minutes + "";
        var n = s.length;
        if (n == 1) {
          s = "0" + s;
        } else if (n == 0) {
          s = "00" + s;
        }
        return shr + s + EndChar;
      },
      getPerc(vrlatual, vlrtotal, multiplicoCem = true, QtdeCasaDecimal = 2) {
        if (vrlatual == 0 || vlrtotal == 0) {
          return 0;
          exit;
        }

        var perc = vrlatual / vlrtotal;
        if (multiplicoCem) {
          perc = perc * 100;
        }
        perc = perc.toFixed(QtdeCasaDecimal);
        return perc;
      },
      dateToBR(date) {
        return moment(date, "YYYY-MM-DD").format("DD/MM/YYYY");
      },
      paginationInfo(currentPage, pagesize, paginationtotal) {
        var info = "";
        if (paginationtotal == 0) {
          info = "Nenhum registro";
        } else {
          if (paginationtotal < pagesize) {
            info = "Mostrando " + paginationtotal + " registro(s)";
          } else {
            info =
              "Página " +
              currentPage +
              " - Mostrando " +
              pagesize +
              " de " +
              paginationtotal +
              " registro(s)";
          }
        }
        return info;
      },
      datetimeToBR(date, OmiteDataSeHoje = false, OmiteSegundo = false) {
        moment.locale("pt-br");
        if (date == "" || date == undefined) return "-";
        var dh = moment(date, "YYYY-MM-DD HH:mm:ss");
        if (dh < moment("01/01/1900", "YYYY-MM-DD")) return "-";
        var mask = "DD/MM/YYYY - HH:mm:ss";
        if (OmiteDataSeHoje) {
          var today = dh.format("DD/MM/YYYY") == moment().format("DD/MM/YYYY");
          if (today) {
            mask = OmiteSegundo ? "HH:mm" : "HH:mm:ss";
          }
        } else {
          mask = "DD/MM/YYYY - HH:mm" + (OmiteSegundo ? "" : ":ss");
        }

        return moment(date, "YYYY-MM-DD - HH:mm:ss").format(mask);
      },
      datetimeRelativeToday(datetime) {
        moment.locale("pt-br");
        var dh = moment(datetime, "YYYY-MM-DD HH:mm:ss");
        return dh.fromNow();
      },
      formatRS(valor, ShowCifrao = true, QtdeCasaDecimal = 2) {
        var v = valor.toFixed(2);
        var numero = v.split(".");
        numero[0] = numero[0].split(/(?=(?:...)*$)/).join(".");
        var s = numero.join(",");

        // let v = 0;
        // if (!(valor == "undefined")) {
        //   v = valor.toFixed(QtdeCasaDecimal);
        // }
        return (ShowCifrao ? "R$ " : "") + (v !== 0 ? s : "-");
      },
      linebreakToBr(s) {
        // var texto = s.replace("\n", "<br/>");
        var texto = s.split("\n").join("<br />");
        return texto;
      },
      padLeftZero(num, size) {
        var s = num + "";
        while (s.length < size) s = "0" + s;
        return s;
      },
      checkpermissao(minhaspermissoes, permissao) {
        if (!minhaspermissoes) return false;
        var permite = false;
        for (var i = 0, len = minhaspermissoes.length; i < len; i++) {
          if (minhaspermissoes[i] == permissao) permite = true;
        }
        return permite;
      },
      nl2br(str, is_xhtml) {
        if (typeof str === "undefined" || str === null) {
          return "";
        }
        var breakTag =
          is_xhtml || typeof is_xhtml === "undefined" ? "<br />" : "<br>";
        return (str + "").replace(
          /([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,
          "$1" + breakTag + "$2"
        );
      },
      clearMask(str) {
        let s = str;
        s = s.replace(/[.,-/_/ /(/)/[\]]/g, "");
        return s;
      },
      validaCPF(cpf) {
        var numeros, digitos, soma, i, resultado, digitos_iguais;
        digitos_iguais = 1;
        if (cpf.length < 11) return false;
        for (i = 0; i < cpf.length - 1; i++)
          if (cpf.charAt(i) != cpf.charAt(i + 1)) {
            digitos_iguais = 0;
            break;
          }
        if (!digitos_iguais) {
          numeros = cpf.substring(0, 9);
          digitos = cpf.substring(9);
          soma = 0;
          for (i = 10; i > 1; i--) soma += numeros.charAt(10 - i) * i;
          resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
          if (resultado != digitos.charAt(0)) return false;
          numeros = cpf.substring(0, 10);
          soma = 0;
          for (i = 11; i > 1; i--) soma += numeros.charAt(11 - i) * i;
          resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
          if (resultado != digitos.charAt(1)) return false;
          return true;
        } else return false;
      },
      bytesToHumanFileSizeString(fileSizeInBytes) {
        var i = -1;
        var byteUnits = [" kB", " MB", " GB", " TB", "PB", "EB", "ZB", "YB"];
        do {
          fileSizeInBytes = fileSizeInBytes / 1024;
          i++;
        } while (fileSizeInBytes > 1024);

        return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
      },
      validaEmail(email) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        //var address = document.getElementById[email].value;
        return reg.test(email);
      },
      arrayObjectIndexOf(myArray, searchTerm, property) {
        for (var i = 0, len = myArray.length; i < len; i++) {
          if (myArray[i][property] === searchTerm) return i;
        }
        return -1;
      },
      jsonEqual(a, b) {
        console.log(JSON.stringify(a));
        console.log(JSON.stringify(b));
        return JSON.stringify(a) === JSON.stringify(b);
      }
    };
  }
};
