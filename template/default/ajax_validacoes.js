
/**
 * Verifica se existe outro cliente com o documento citado.
 * No caso da altera��o verifica se o docto pertence ao id_cliente que est� sendo alterado.
 * Retorna true apenas se o documento n�o pertencer ao cliente informado.
 */

function pesquisaClientes(texto_pesquisa,tipo_pesquisa) {
	var url = "clientes.php?op=pesquisa&texto_pesquisa=" + encodeURIComponent(texto_pesquisa) + "&tipo_pesquisa=" + tipo_pesquisa + "&a=pesquisa&retorno=XML";
	return ajaxXML(url);
}

function existeDOCTO(docto,id_cliente) {
	var tp="DOCTOS";
	var response = pesquisaClientes(docto,tp);

	if( response.getElementsByTagName("item").length == 0 ) {
	  /**
	   * O documento indicado n�o esta cadastrado no sistema
	   */
		return false;
	}

	/**
	 * Registro encontrado, verificar se o mesmo n�o pertence a outro cliente
	 */

	itens = response.getElementsByTagName("item");
	for (i=0;i<response.getElementsByTagName("item").length;i++){
		bd_id_cliente = itens[i].getElementsByTagName("id_cliente")[0].firstChild.nodeValue;
		if( bd_id_cliente != id_cliente ) {
			return true;
		}
	}

	return false;
}

/**
 * Verifica se existe um determinado username
 */
function existeUsuario(username,dominio,tipo_conta) {
	var tp="CONTA";
	var texto_pesquisa = username + "@" + dominio;
	var response = pesquisaClientes(texto_pesquisa,tp);

	if( response.getElementsByTagName("item").length == 0 ) {
		/**
		 * N�o foi encontrado nenhum usu�rio que satisfa�a a condi��o de pesquisa.
		 */
		 return false;
	}

	/**
	 * Se achou a conta verifica se o tipo da conta que est� sendo cadastrada � o mesmo do tipo informado.
	 */
	itens = response.getElementsByTagName("item");

	// Varre a lista de clientes
	for (i=0;i<response.getElementsByTagName("item").length;i++){
		contas     = itens[i].getElementsByTagName("conta");
		// Varre a lista de contas
		if( contas.length > 0 ) {
			for(x=0;x<contas.length;x++) {
				// Verifica o tipo da conta
				bd_tipo_conta  = contas[x].getElementsByTagName("tipo_conta")[0].firstChild.nodeValue;

				if( bd_tipo_conta == tipo_conta ) {
					// Match...
					return true;
				}
			}
		}
	}

	return false;

}

/**
 * Verifica de o endere�o de rede/ip atribuido a um cliente n�o est� sendo utilizado por outro
 * Caso o id_conta seja o mesmo enviado n�o considera como encontrado.
 */

function enderecoUtilizado(endereco,id_conta) {
	var tp="CONTA";
	var response = pesquisaClientes(endereco,tp);

	if( response.getElementsByTagName("item").length == 0 ) {
		/**
		 * Endereco solicitado n�o atribu�do a nenhum outro usu�rio
		 */
		return false;
	}


	/**
	 * Se achou a conta verifica se o endereco que est� sendo cadastrado n�o � referente � mesma conta em quest�o.
	 */
	itens = response.getElementsByTagName("item");

	// Varre a lista de clientes
	for (i=0;i<response.getElementsByTagName("item").length;i++){
		contas     = itens[i].getElementsByTagName("conta");
		// Varre a lista de contas
		if( contas.length > 0 ) {
			for(x=0;x<contas.length;x++) {
				// Verifica o id da conta
				bd_id_conta  = contas[x].getElementsByTagName("id_conta")[0].firstChild.nodeValue;

				if( bd_id_conta != id_conta ) {
					// Match...
					return true;
				}
			}
		}
	}

	return false;

}

/**
 * Verifica se o endereco pertence ao NAS especificado
 * Retorna "" ou o endereco de ip/rede
 */
function enderecoPertenceAoNas(endereco,id_nas) {

	var url="ajax.php?op=endereco_nas&id_nas="+encodeURIComponent(id_nas)+"&endereco="+encodeURIComponent(endereco);
	response = ajaxXML(url);

	if( response.getElementsByTagName("item").length == 0 ) {
		/**
		 * O endereco n�o est� cadastrado no sistema ou n�o pertence ao nas especificado.
		 */
		return "";
	}

	itens = response.getElementsByTagName("item");

	// Varre a lista de enderecos
	for (i=0;i<response.getElementsByTagName("item").length;i++){
				bd_endereco = itens[i].getElementsByTagName("endereco")[0].firstChild.nodeValue;
				return bd_endereco;
	}

	return "";

}
