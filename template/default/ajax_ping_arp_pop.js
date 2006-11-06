function Ping(){
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

		ajax.open("POST", "configuracao.php?op=ajax_ping_pop", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			if(ajax.readyState == 1) {
			
				document.getElementById("container").className = "box_aberta" ; 
				document.getElementById("container").innerHTML= "processando...";


	        }
	        if(ajax.readyState == 2) {
				
				document.getElementById("container").className = "box_aberta" ;
				document.getElementById("container").innerHTML= "processando....";

			
			
	        }
	        if(ajax.readyState == 3) {

				document.getElementById("container").className = "box_aberta" ;
				document.getElementById("container").innerHTML= "processando.....";
						
	        }
	        
			//após ser processado
			if(ajax.readyState == 4 ) {
				if(ajax.responseTEXT) {
				
				document.getElementById("container").className = "box_aberta" ;
				document.getElementById("container").innerHTML=(ajax.responseTEXT);
				

					
				} else {
			       
				}
			}

		}
		
		var params = "ip="+formulario.ip.value + '&host=' + formulario.infoserver.value;
		ajax.send(params);


	}

}



function Arp(){
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

		ajax.open("POST", "configuracao.php?op=ajax_arp_pop", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			if(ajax.readyState == 1) {
			
				document.getElementById("container").className = "box_aberta" ; 
				document.getElementById("container").innerHTML= "processando...";

	        }
	        if(ajax.readyState == 2) {
			
			
				document.getElementById("container").className = "box_aberta" ;
				document.getElementById("container").innerHTML= "processando....";

			
			
	        }
	        if(ajax.readyState == 3) {
			
				document.getElementById("container").className = "box_aberta" ;
				document.getElementById("container").innerHTML= "processando........";

	        }
	        
			//após ser processado
			if(ajax.readyState == 4 ) {
				if(ajax.responseTEXT) {
				
					document.getElementById("container").className = "box_aberta" ;
					document.getElementById("container").innerHTML=(ajax.responseTEXT);
					
					
				} else {
			       
				}
			}

		}
		
		var params = "ip="+formulario.ip.value + '&host=' + formulario.infoserver.value;
		ajax.send(params);


	}

}

function Fecha(){


		document.getElementById("container").className = "box_fechada" ;
	


}


