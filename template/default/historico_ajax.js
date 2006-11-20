function process_history(){
	//verifica se o browser tem suporte a ajax
	try {
		ajax = new ActiveXObject("Microsoft.XMLHTTP");
	} catch(e) {
		try {
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(ex) {
			try {
				ajax = new XMLHttpRequest();
			} catch(exc) {
				alert("Esse browser não tem recursos para uso do Ajax");
				ajax = null;
			}
		}
	}
	//se tiver suporte ajax
	if(ajax) {

		ajax.open("POST", "clientes.php?op=historico", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
			        
			//após ser processado
			if(ajax.readyState == 4 ) {
				if(ajax.responseTEXT) {
				
					document.getElementById("historico").className = "box_aberta" ;
					document.getElementById("historico").innerHTML=(ajax.responseTEXT);

				} else {
				   
				}
			}

		}
		
		var params = "username="+form1.username.value + '&tipo_conta=' + form1.tipo_conta.value + '&id_cliente_produto=' + form1.idcp.value + '&dominio=' + form1.dominio.value;
		ajax.send(params);


	}

}

function Fecha_hist(){


		document.getElementById("historico").className = "box_fechada" ;
	


}


