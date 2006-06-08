
function abrePesquisa() {
	var pC = document.getElementById("caixa_pesquisa");
	pC.className = "box_aberta";
	var pR = document.getElementById("resultado_pesquisa");
	pR.className = "box_aberta";

	/**
	 * Ajusta a Posição em relação ao DIV anterior
	 */

	var pB = document.getElementById("box_pesquisa");
	var top = pB.offsetTop + 98;
	var left = pB.offsetLeft + pB.offsetWidth;

	pC.style.top = top;
	pC.style.left = left - 15;

}

function fechaPesquisa() {
	var pC = document.getElementById("caixa_pesquisa");
	pC.className = "box_fechada";
	var pR = document.getElementById("resultado_pesquisa");
	pR.className = "box_fechada";
}

function scrollPesquisa(str) {
	var pR = document.getElementById("resultado_pesquisa");
	pR.doScroll(str);
}

function scrollDownPesquisa() {
	scrollPesquisa("scrollbarPageDown");
}
function scrollUpPesquisa() {
	scrollPesquisa("scrollbarPageUp");
}


function pesquisaUsuario() {
	var frm = document.form_pesquisa;
	var pW = document.getElementById("resultado_pesquisa");

	lista = frm.getElementsByTagName("input");
  tp="";
	for(i=0;i<lista.length;i++) {
		if( lista[i].type == "radio" ) {
			if( lista[i].name == "tipo_pesquisa" ) {
				if( lista[i].checked ) {
					tp = lista[i].value;
				}
			}
		}
	}

	if( tp == "" || frm.texto_pesquisa == "" ) {
	   window.alert("Parametros de pesquisa incompletos");
	   return false;
	}


	abrePesquisa();
	pW.innerHTML = "<p id='pesquisando'>Pesquisando</p>";

	url = "clientes.php?op=pesquisa&texto_pesquisa=" + encodeURIComponent(frm.texto_pesquisa.value) + "&tipo_pesquisa=" + tp + "&a=pesquisa&retorno=XML";
	response = ajaxXML(url);


	/**
	 * Joga o resultado no div apropriado.
	 */

	if( response.getElementsByTagName("item").length == 0 ) {
	   pW.innerHTML = "<p id='msgErro'>Nenhum registro encontrado</p>";
	} else {
		/**
		 * Cria a tabela de resultado
		 */

		itens = response.getElementsByTagName("item");
		ihtml = "<table id='tabela_resposta'>\n";
		for (i=0;i<response.getElementsByTagName("item").length;i++){

			id_cliente = itens[i].getElementsByTagName("id_cliente")[0].firstChild.nodeValue;
			nome_razao = itens[i].getElementsByTagName("nome_razao")[0].firstChild.nodeValue;
			lnk = "clientes.php?op=cadastro&id_cliente=" + id_cliente;
			contas     = itens[i].getElementsByTagName("conta");
			
			clklnk = 'javascript:clickLink("'+lnk+'","CONTEUDO");';

			ihtml += "<tr id='pesquisa_info_cliente'>";
			ihtml += "<td class='p_id_cliente'><a href='"+clklnk+"' target='_self'>"+id_cliente+"</a></td>\n";
			ihtml += "<td class='p_nome_razao'><a href='"+clklnk+"' target='_self'>"+nome_razao+"</a></td>\n";
			ihtml += "<td class='p_link'><a href='"+clklnk+"' target='_self'><img src=\"template/default/images/gif_alterar.gif\" width=\"16\" height=\"16\" border=\"0\"></a></td>\n";
			ihtml += "</tr>";

			if( contas.length > 0 ) {
			   for(x=0;x<contas.length;x++) {
							username    = contas[x].getElementsByTagName("username")[0].firstChild.nodeValue;
							dominio     = contas[x].getElementsByTagName("dominio")[0].firstChild.nodeValue;
							tipo_conta  = contas[x].getElementsByTagName("tipo_conta")[0].firstChild.nodeValue;
							clnk        = "clientes.php?op=conta&pg=ficha&id_cliente=" + id_cliente + "&username="+username+"&dominio="+dominio+"&tipo_conta="+tipo_conta;
							
							cclklnk = 'javascript:clickLink("'+lnk+'","CONTEUDO");';
							


							ihtml += "<tr id='pesquisa_info_contas'>";

							ihtml += "<td colspan=3 class='p_conta'> &nbsp; &nbsp; - <a href='"+cclnk+"' target='_self'>";
							ihtml += username;

							if( tipo_conta == "E" ) {
								 ihtml += "@" + dominio;
							}

							ihtml += "</a> [" + tipo_conta + "]";
							ihtml += "</td>\n";

							ihtml += "</tr>";
			   }

			}



		}
		ihtml += "</table>";
		pW.innerHTML = ihtml;

		return false;

	}

}


function clickLink(url,target) {
   window.open(url,target)
   fechaPesquisa();
   /**
   var t = document.getElementById(target);
   v.focus();
   v.setActive();
   */
}

