/**
 * Number.prototype.format(n, x, s, c)
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

function getFormattedDate(date) {
  var year = date.getFullYear();

  var month = (1 + date.getMonth()).toString();
  month = month.length > 1 ? month : '0' + month;

  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;

  return day + '/' + month + '/' + year;
}
function toDate(dateStr) {
    const [year, month, day] = dateStr.split("-")
    return new Date(year, month - 1, day)
}

$(document).ready(function() {
  var cnpj = 	$('#cnpj').val();
  var token = 	$('#token').val();
  var fatura = 	$('#fatura').val();
  console.log('cnpj' + cnpj);
  console.log('token' + token);
  console.log('fatura' + fatura);
  setTimeout(function(){
      console.log('enviando');
      SyncData(cnpj, fatura, token);
      // window.location = 'http://idoze/boletofacil.php?cnpj=' + cnpj + '&fatura=' + fatura + '&token=' + token ;
  }, 2000);


	SyncData = function (vcnpj, vfatura, vtoken) {
  	var send = {
			cnpj: vcnpj,
      fatura: vfatura,
      token: vtoken
		}

		$.ajax({
			url: 'http://ws.idoze/api/boleto',
			type: 'get',
			dataType: 'json',
			async: true,
      error: function (request, status, error) {
        $('#loading').hide("slow") ;
        msg = '<p><b> OCORREU ALGUM ERRO AO CARREGAR O BOLETO!<br>Atualizar sua página se o problema persistir chame o suporte pelo chat, e-mail ou telefone.</b></p>';
        $('#dadosboleto').html(msg) ;

      },
			success: function (data) {
        console.log(data);
				var ok = data["ok"];
				var msg = data["msg"];
        var boleto = data["rows"];

        console.log(boleto);
				if(ok){
            $('#loading').hide("slow") ;
            var valor = Number(boleto["valor"]);
            var vencimento = boleto["vencimento"];
            console.log(vencimento);
            vencimento = toDate(vencimento);
            msg = msg +
                  '<hr/><div class="nomeempresa">' + boleto["razao"] +  '</div>'  +
                  '<div> ' + boleto["fantasia"] +  '</div><hr/>'  +
                  '<div class="boleto">Valor: <span class="boletovalor">R$ ' + valor.format(2, 3, '.', ',')  +  '</span> '  +
                  ' Vencimento: <span class="boletovalor">' + getFormattedDate(vencimento) +  '</span></div>'  +
                  '<p>Código de barra - linha digitável</p><div class="boleto"><span class="codigobarraboleto">' + boleto["descanexo"] +  '</span></div>'  +
                  '<p>Descritivo: ' + boleto["refaoservico"] +  '</p>' ;
            $('#dadosboleto').html(msg) ;

            window.location = 'http://idoze/boletofacil.php?cnpj=' + vcnpj + '&fatura=' + vfatura + '&token=' + vtoken ;

      //
				}else{
          $('#loading').hide("slow") ;
          $('#dadosboleto').html(msg) ;
				}
			},
			data: send
		});
	};




});
