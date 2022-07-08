export default {
  loggedIn() {
    var user = document
      .querySelector('meta[name="user"]')
      .getAttribute("content");
    if (user !== "") {
      user = JSON.parse(user);
    } else {
      return false;
    }
    return !user.codusuario ? false : user.codusuario > 0;
  },

  loggedInPainelCliente() {
    var logged = false;

    var contato = null;
    var metacontato = document.querySelector('meta[name="contato"]');
    if (metacontato) contato = metacontato.getAttribute("content");
    if (contato !== "") {
      contato = JSON.parse(contato);
    }

    var cliente = null;
    var metacliente = document.querySelector('meta[name="cliente"]');
    if (metacliente) cliente = metacliente.getAttribute("content");
    if (cliente !== "") {
      cliente = JSON.parse(cliente);
    }

    if (contato && cliente) {
      if (contato.id > 0 && cliente.codcliente > 0) logged = true;
    }
    return logged;
  }
};
