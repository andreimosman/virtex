function Processa(){
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

		ajax.open("POST", "cobranca.php?op=tabela_compra&acao=listar", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			//após ser processado
			if(ajax.readyState == 4 ) {
				if(ajax.responseTEXT) {
				
				document.getElementById("tabela_permanente").className = "box_aberta" ;
				document.getElementById("tabela_permanente").innerHTML=(ajax.responseTEXT);
				

					
				} else {
			       
				}
			}

		}
		
		var params = "id_produto="+formulario.nome.value;
		ajax.send(params);


	}

}
function Processa_lista(){
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

		ajax.open("POST", "cobranca.php?op=tabela_compra&acao=adicionar_lista", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		ajax.onreadystatechange = function() {
            //enquanto estiver processando...emite a msg de carregando
			//após ser processado
			if(ajax.readyState == 4 ) {
				if(ajax.responseTEXT) {
				
					document.getElementById("tabela").className = "box_aberta" ;
					document.getElementById("tabela_botao").className = "box_aberta" ;
					var vezes = document.getElementById("vezes").value;
					var total = document.getElementById("total").value;
					var conteudo = ajax.responseTEXT.split('-');
															
						var table = document.createElement('table');
						var tr = document.createElement('tr');
						var td = document.createElement('td');
						var td2 = document.createElement('td');
						var td3 = document.createElement('td');
						var td4 = document.createElement('td');
						var td5 = document.createElement('td');
						var texto = document.createTextNode();
						
							td.appendChild(texto);
							td.innerHTML = conteudo[0]
							td2.innerHTML = conteudo[1]
							td3.innerHTML = conteudo[2]
							td4.innerHTML = conteudo[3]
							td5.innerHTML = conteudo[4]
							
							td.className = "td";
							td2.className = "td";
							td3.className = "td";
							td4.className = "td";
							td5.className = "td";
							
							tr.appendChild(td);
							tr.appendChild(td2);
							tr.appendChild(td3);
							tr.appendChild(td4);
							tr.appendChild(td5);
							
							table.appendChild(table);

					document.getElementById('tabela').firstChild.appendChild(tr);	
					document.getElementById("vezes").value = parseInt(vezes)+1;
					total_calc = parseFloat(total)+parseFloat(conteudo[4]);
					var totalGeral = total_calc.toFixed(2); 
					document.getElementById("total").value = totalGeral;
					document.getElementById("total_calc").value = totalGeral;
					
				} else {
			       
				}
			}

		}
		
		var params = "nome="+form_tabela.nome.value + '&quant=' + form_tabela.quant.value + '&valor=' + form_tabela.valor.value+ '&id_produto=' + form_tabela.id_produto.value+ '&vezes=' + tabela_compra.vezes.value;
		ajax.send(params);


	}

}