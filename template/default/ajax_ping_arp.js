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

		ajax.open("POST", "configuracao.php?op=ajax_ping", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			if(ajax.readyState == 1) {
			document.getElementById("ping").className = "box_aberta" ; 
			 document.getElementById("ping").innerHTML= "processando...";


	        }
	        if(ajax.readyState == 2) {
			document.getElementById("ping").className = "box_aberta" ;
			
						document.getElementById("ping").innerHTML= "processando........";

			
			
	        }
	        if(ajax.readyState == 3) {
	        document.getElementById("ping").className = "box_aberta" ;
									
						document.getElementById("ping").innerHTML= "processando........";
						
	        }
	        
			//após ser processado
			if(ajax.readyState == 4 ) {
				if(ajax.responseTEXT) {
				
					document.getElementById("ping").className = "box_aberta" ;
					
					document.getElementById("ping").innerHTML=(ajax.responseTEXT);
					
					
				} else {
			       
				}
			}

		}
		
		var params = "ip="+frmConta.end_ip.value + '&id_nas=' + frmConta.nas_orig.value;
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

		ajax.open("POST", "configuracao.php?op=ajax_arp", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			if(ajax.readyState == 1) {
			document.getElementById("ping").className = "box_aberta" ; 
			 document.getElementById("ping").innerHTML= "processando...";


	        }
	        if(ajax.readyState == 2) {
			document.getElementById("ping").className = "box_aberta" ;
			
						document.getElementById("ping").innerHTML= "processando........";

			
			
	        }
	        if(ajax.readyState == 3) {
	        document.getElementById("ping").className = "box_aberta" ;
									
						document.getElementById("ping").innerHTML= "processando........";
						
	        }
	        
			//após ser processado
			if(ajax.readyState == 4 ) {
				if(ajax.responseTEXT) {
				
					document.getElementById("ping").className = "box_aberta" ;
					
					document.getElementById("ping").innerHTML=(ajax.responseTEXT);
					
					
				} else {
			       
				}
			}

		}
		
		var params = "ip="+frmConta.end_ip.value + '&id_nas=' + frmConta.nas_orig.value;
		ajax.send(params);


	}

}

