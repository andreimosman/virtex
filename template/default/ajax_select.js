	   function Dados(valor) {
      //verifica se o browser tem suporte a ajax
	  try {
         ajax = new ActiveXObject("Microsoft.XMLHTTP");
      } 
      catch(e) {
         try {
            ajax = new ActiveXObject("Msxml2.XMLHTTP");
         }
	     catch(ex) {
            try {
               ajax = new XMLHttpRequest();
            }
	        catch(exc) {
               alert("Esse browser não tem recursos para uso do Ajax");
               ajax = null;
            }
         }
      }
	  //se tiver suporte ajax
	  if(ajax) {
	     //deixa apenas o elemento 1 no option, os outros são excluídos
		 document.formulario.id_pop_ap.options.length = 1;
	     
		 idOpcao  = document.getElementById("opcoes");
		 
	     ajax.open("POST", "configuracao.php?op=ajax", true);
		 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		 
		 ajax.onreadystatechange = function() {
			//enquanto estiver processando...emite a msg de carregando
				if(ajax.readyState == 1) {
				idOpcao.innerHTML = "..";
				idOpcao.innerHTML = "......";
				idOpcao.innerHTML = ".............";

			}
			//após ser processado - chama função processXML que vai varrer os dados
            if(ajax.readyState == 4 ) {
			   if(ajax.responseXML) {
			      processXML(ajax.responseXML);
			   }
			   else {
			       //caso não seja um arquivo XML emite a mensagem abaixo
				   idOpcao.innerHTML = "--Primeiro selecione o tipo--";
			   }
            }
         }
		 //passa o código do tipo escolhido
	     var params = "tipo="+valor;
         ajax.send(params);
      }
   }
   
function ProcessLoop(){
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

		ajax.open("POST", "configuracao.php?op=ajax_loop", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			if(ajax.readyState == 1) {
			   //idOpcao.innerHTML = "..";
			   //idOpcao.innerHTML = "......";
			   //idOpcao.innerHTML = ".............";

	        }
			//após ser processado - chama função processXML que vai varrer os dados
			if(ajax.readyState == 4 ) {
				if(ajax.responseTEXT) {
					//processXML(ajax.responseXML);
					window.alert(ajax.responseTEXT);
					formulario.id_pop_ap.value = "";
				} else {
			       //caso não seja um arquivo XML emite a mensagem abaixo
				   //idOpcao.innerHTML = "--Primeiro selecione o tipo--";
				}
			}

		}

		var params = "id_pop="+formulario.id_pop.value + '&id_pop_ap=' + formulario.id_pop_ap.value;
		ajax.send(params);

	}

}   


   function processXML(obj){
      //pega a tag id_pop_ap
      var dataArray   = obj.getElementsByTagName("pop");
      
	  //total de elementos contidos na tag id_pop_ap
	  if(dataArray.length > 0) {
	     //percorre o arquivo XML paara extrair os dados
         for(var i = 0 ; i < dataArray.length ; i++) {
            var item = dataArray[i];
			//contéudo dos campos no arquivo XML
			var codigo	=  item.getElementsByTagName("id_pop")[0].firstChild.nodeValue;
			var nome	=  item.getElementsByTagName("nm_nome")[0].firstChild.nodeValue;
			
	        idOpcao.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			
		if ( codigo != formulario.id_pop.value){	
			
			//cria um novo option dinamicamente  
			var novo = document.createElement("option");
			    //atribui um ID a esse elemento
			    novo.setAttribute("id", "opcoes");
				//atribui um valor
			    novo.value = codigo;
				//atribui um texto
			    novo.text  = nome;
				//finalmente adiciona o novo elemento
				document.formulario.id_pop_ap.options.add(novo);
			}
		 }
	  }
	  else {	
	    //caso o XML volte vazio, printa a mensagem abaixo
		idOpcao.innerHTML = " Não há opções para esse tipo ";
	  }
	  		formulario.id_pop_ap.value = formulario.id_pop_ap_selected.value;
	  		/////////window.location= "javascript: ProcessLoop(formulario.id_pop_ap_selected.value);";
	  		
   }
