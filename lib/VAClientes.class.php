<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAClientes extends VirtexAdmin {

	public function VAClientes() {
		parent::VirtexAdmin();


	}


	protected function validaFormulario() {
	   $erros = array();
	   return $erros;
	}


	public function processa($op=null) {

		if ($op == "cadastro"){

			$erros = array();

			$acao = @$_REQUEST["acao"];
			$id_cliente = @$_REQUEST["id_cliente"];
			$cpf_cnpj = @$_REQUEST["cpf_cnpj"];
			//$msg_final = @$_REQUEST["msg_final"];

			$enviando = false;
			
			
			$reg = array();


			if( $acao ) {
			   // Se ele recebeu o campo ação é pq veio de um submit
			   $enviando = true;
			} else {
				// Se não recebe o campo ação e tem id_cliente é alteração, caso contrário é cadastro.
				if( $id_cliente ) {
					// SELECT
					$sSQL  = "SELECT ";
					$sSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
					$sSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
					$sSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
					$sSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
					$sSQL .= "   ativo,obs ";
					$sSQL .= "FROM ";
					$sSQL .= "   cltb_cliente ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_cliente = '$id_cliente' ";
					
					$reg = $this->bd->obtemUnicoRegistro($sSQL);

					$acao = "alt";
					$titulo = "Alterar";

				} else {
					$acao = "cad";
					$titulo = "Cadastrar";
				}
			}
			
			if( $enviando ) {

				if( $cpf_cnpj ){

					$tSQL  = "SELECT ";
					$tSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
					$tSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
					$tSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
					$tSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
					$tSQL .= "   ativo,obs ";
					$tSQL .= "FROM ";
					$tSQL .= "   cltb_cliente ";
					$tSQL .= "WHERE ";
					$tSQL .= "   cpf_cnpj = '$cpf_cnpj' ";
					
					if( $acao == "alt" ) {
					   $tSQL .= "   AND id_cliente != '". $id_cliente ."' ";
					}

					$checa = $this->bd->obtemUnicoRegistro($tSQL);
					if($checa){
					
					$erros[] = "CPF/CNPJ cadastrado para outro cliente.";
					
					// Atribui os campos
					$this->tpl->atribui("id_cliente",@$_REQUEST["id_cliente"]);
					$this->tpl->atribui("data_cadastro",@$_REQUEST["data_cadastro"]);
					$this->tpl->atribui("nome_razao",@$_REQUEST["nome_razao"]);// pega a info do db e atribui ao campo correspon do form
					$this->tpl->atribui("tipo_pessoa",@$_REQUEST["tipo_pessoa"]);
					$this->tpl->atribui("rg_inscr",@$_REQUEST["rg_inscr"]);
					$this->tpl->atribui("expedicao",@$_REQUEST["expedicao"]);
					$this->tpl->atribui("cpf_cnpj",@$_REQUEST["cpf_cnpj"]);
					$this->tpl->atribui("email",@$_REQUEST["email"]);
					$this->tpl->atribui("endereco",@$_REQUEST["endereco"]);
					$this->tpl->atribui("complemento",@$_REQUEST["complemento"]);
					$this->tpl->atribui("id_city",@$_REQUEST["id_cidade"]);
					$this->tpl->atribui("cidade",@$_REQUEST["cidade"]);
					$this->tpl->atribui("estado",@$_REQUEST["estado"]);
					$this->tpl->atribui("cep",@$_REQUEST["cep"]);
					$this->tpl->atribui("bairro",@$_REQUEST["bairro"]);
					$this->tpl->atribui("fone_comercial",@$_REQUEST["fone_comercial"]);
					$this->tpl->atribui("fone_residencial",@$_REQUEST["fone_residencial"]);
					$this->tpl->atribui("fone_celular",@$_REQUEST["fone_celular"]);			
					$this->tpl->atribui("contato",@$_REQUEST["contato"]);			
					$this->tpl->atribui("banco",@$_REQUEST["banco"]);
					$this->tpl->atribui("conta_corrente",@$_REQUEST["conta_corrente"]);			
					$this->tpl->atribui("agencia",@$_REQUEST["agencia"]);			
					$this->tpl->atribui("dia_pagamento",@$_REQUEST["dia_pagamento"]);			
					$this->tpl->atribui("ativo",@$_REQUEST["ativo"]);			
		        		$this->tpl->atribui("obs",@$_REQUEST["obs"]);
					
					}
					
				}
				
				if( !count($erros) ) {
				   // Grava no banco.
					if( $acao == "cad" ) {
				   		// CADASTRO
				   		
						$msg_final = "Cliente Cadastrado com sucesso!";
				   		
						$id_cliente = $this->bd->proximoID("clsq_id_cliente");

					
						$sSQL  = "INSERT INTO ";
						$sSQL .= "   cltb_cliente( ";
						$sSQL .= "      id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
						$sSQL .= "      rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
						$sSQL .= "      cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
						$sSQL .= "      fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
						$sSQL .= "      ativo,obs ) ";
						$sSQL .= "   VALUES (";
						$sSQL .= "     '" . $this->bd->escape($id_cliente) . "', ";
						$sSQL .= "     now(), ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome_razao"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo_pessoa"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["rg_inscr"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["expedicao"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["cpf_cnpj"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["email"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["endereco"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["complemento"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["id_cidade"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["cidade"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["estado"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["cep"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["bairro"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["fone_comercial"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["fone_residencial"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["fone_celular"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["contato"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["banco"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["conta_corrente"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["agencia"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["dia_pagamento"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["ativo"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["obs"]) . "' ";
						$sSQL .= "     )";
					
					
					} else {
					   // ALTERACAO
						$msg_final = "Cliente Alterado com sucesso!";


						$sSQL  = "UPDATE ";
						$sSQL .= "   cltb_cliente ";
						$sSQL .= "SET ";
						$sSQL .= "   nome_razao = '" . $this->bd->escape(@$_REQUEST["nome_razao"]) . "', ";
						$sSQL .= "   tipo_pessoa = '" . $this->bd->escape(@$_REQUEST["tipo_pessoa"]) . "', ";
						$sSQL .= "   rg_inscr = '" . $this->bd->escape(@$_REQUEST["rg_inscr"]) . "', ";
						$sSQL .= "   expedicao = '" . $this->bd->escape(@$_REQUEST["expedicao"]) . "', ";
						$sSQL .= "   cpf_cnpj = '" . $this->bd->escape(@$_REQUEST["cpf_cnpj"]) . "', ";
						$sSQL .= "   email = '" . $this->bd->escape(@$_REQUEST["email"]) . "', ";
						$sSQL .= "   endereco = '" . $this->bd->escape(@$_REQUEST["endereco"]) . "', ";
						$sSQL .= "   complemento = '" . $this->bd->escape(@$_REQUEST["complemento"]) . "', ";
						$sSQL .= "   id_cidade = '" . $this->bd->escape(@$_REQUEST["id_cidade"]) . "', ";
						$sSQL .= "   cidade = '" . $this->bd->escape(@$_REQUEST["cidade"]) . "', ";
						$sSQL .= "   estado = '" . $this->bd->escape(@$_REQUEST["estado"]) . "', ";
						$sSQL .= "   cep = '" . $this->bd->escape(@$_REQUEST["cep"]) . "', ";
						$sSQL .= "   bairro = '" . $this->bd->escape(@$_REQUEST["bairro"]) . "', ";
						$sSQL .= "   fone_comercial = '" . $this->bd->escape(@$_REQUEST["fone_comercial"]) . "', ";
						$sSQL .= "   fone_residencial = '" . $this->bd->escape(@$_REQUEST["fone_residencial"]) . "', ";
						$sSQL .= "   fone_celular = '" . $this->bd->escape(@$_REQUEST["fone_celular"]) . "', ";
						$sSQL .= "   contato = '" . $this->bd->escape(@$_REQUEST["contato"]) . "', ";
						$sSQL .= "   banco = '" . $this->bd->escape(@$_REQUEST["banco"]) . "', ";
						$sSQL .= "   conta_corrente = '" . $this->bd->escape(@$_REQUEST["conta_corrente"]) . "', ";
						$sSQL .= "   agencia = '" . $this->bd->escape(@$_REQUEST["agencia"]) . "', ";
						$sSQL .= "   dia_pagamento = '" . $this->bd->escape(@$_REQUEST["dia_pagamento"]) . "', ";
						$sSQL .= "   ativo = '" . $this->bd->escape(@$_REQUEST["ativo"]) . "', ";
						$sSQL .= "   obs = '" . $this->bd->escape(@$_REQUEST["obs"]) . "' ";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_cliente = '" . $this->bd->escape(@$_REQUEST["id_cliente"]) . "' ";  // se idcliente for =  ao passado.


					}
					
					$this->bd->consulta($sSQL);  

					if( $this->bd->obtemErro() != MDATABASE_OK ) {
						echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
						echo "QUERY: " . $sSQL . "<br>\n";
					
					}


					// Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
					$this->tpl->atribui("mensagem",$msg_final); 
					$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=listagem");
					$this->tpl->atribui("target","_top");

					$this->arquivoTemplate = "msgredirect.html";
					
					
					// cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
					return;
				}else{
				
				//$erros[] = "CPF/CNPJ cadastrado para outro cliente.";
				
				}
				
			}
			
			
			// Pegar lista de cidades que o provedor opera.
			
				$eSQL  = "SELECT ";
				$eSQL .= "   id_cidade, uf, cidade, disponivel ";
				$eSQL .= "FROM cftb_cidade ";
				$eSQL .= "WHERE disponivel = 't' ";
				$eSQL .= "ORDER BY cidade ASC";

				$cidades_disponiveis = $this->bd->obtemRegistros($eSQL);
				
				$this->tpl->atribui("cidades_disponiveis",$cidades_disponiveis);
			
				
				
				//echo $erros;
			if ($acao == "cad"){
				$titulo = "Cadastrar";
			}else if ($acao == "alt"){
				$titulo = "Alterar";
			}
			
			
			
			
			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("mensagem",$erros);
			$this->tpl->atribui("acao",$acao);
			$this->tpl->atribui("op",$op);
			
			// Atribui as listas
			//global $_LS_ESTADOS;
			//$this->tpl->atribui("lista_estados",$_LS_ESTADOS);
			
			global $_LS_TP_PESSOA;
			$this->tpl->atribui("lista_tp_pessoa",$_LS_TP_PESSOA); //lista_tp_pessoa recebe os dados do array LS_TP_PESSOA(status.defs.php) para mostrar do dropdown.
			
			global $_LS_ST_CLIENTE;
			$this->tpl->atribui("lista_ativo",$_LS_ST_CLIENTE);

			global $_LS_DIA_PGTO;
			$this->tpl->atribui("lista_dia_pagamento",$_LS_DIA_PGTO);

			// Atribui os campos
		        $this->tpl->atribui("id_cliente",@$reg["id_cliente"]);
		        $this->tpl->atribui("data_cadastro",@$reg["data_cadastro"]);
		        $this->tpl->atribui("nome_razao",@$reg["nome_razao"]);// pega a info do db e atribui ao campo correspon do form
		        $this->tpl->atribui("tipo_pessoa",@$reg["tipo_pessoa"]);
		        $this->tpl->atribui("rg_inscr",@$reg["rg_inscr"]);
		        $this->tpl->atribui("expedicao",@$reg["expedicao"]);
		        $this->tpl->atribui("cpf_cnpj",@$reg["cpf_cnpj"]);
		        $this->tpl->atribui("email",@$reg["email"]);
		        $this->tpl->atribui("endereco",@$reg["endereco"]);
		        $this->tpl->atribui("complemento",@$reg["complemento"]);
		        $this->tpl->atribui("id_city",@$reg["id_cidade"]);
		        $this->tpl->atribui("cidade",@$reg["cidade"]);
		        $this->tpl->atribui("estado",@$reg["estado"]);
		        $this->tpl->atribui("cep",@$reg["cep"]);
		        $this->tpl->atribui("bairro",@$reg["bairro"]);
		        $this->tpl->atribui("fone_comercial",@$reg["fone_comercial"]);
		        $this->tpl->atribui("fone_residencial",@$reg["fone_residencial"]);
		        $this->tpl->atribui("fone_celular",@$reg["fone_celular"]);			
		        $this->tpl->atribui("contato",@$reg["contato"]);			
		        $this->tpl->atribui("banco",@$reg["banco"]);
		        $this->tpl->atribui("conta_corrente",@$reg["conta_corrente"]);			
		        $this->tpl->atribui("agencia",@$reg["agencia"]);			
		        $this->tpl->atribui("dia_pagamento",@$reg["dia_pagamento"]);			
		        $this->tpl->atribui("ativo",@$reg["ativo"]);			
		        $this->tpl->atribui("obs",@$reg["obs"]);
		        
		        $this->tpl->atribui("titulo",@$titulo);// para que no clientes_cadastro.html a variavel do smart titulo consiga pegar o que foi definido no $titulo.
		        
					

			
			
			// Seta as variáveis do template.
			$this->arquivoTemplate = "clientes_cadastro.html";
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			

		} else if ( $op == "pesquisa" ){
		
		
			$erros = array();
			$cond = @$_REQUEST['cond'];
			$campo_pesquisa = @$_REQUEST['campo_pesquisa'];
			
			if( !$campo_pesquisa ){
			 	// Faz alguma coisa
			 	
			 	$aSQL  = "SELECT ";
				$aSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
				$aSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
				$aSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
				$aSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
				$aSQL .= "   ativo,obs ";
				$aSQL .= "FROM cltb_cliente ";
				$aSQL .= "ORDER BY id_cliente DESC LIMIT (10)";
							
				$listagem_clientes = $this->bd->obtemRegistros($aSQL);
				$this->tpl->atribui("listagem_clientes", $listagem_clientes);
			 	
			 	
			 	
			 	
			  	if( $cond ) {
			  		$erros[] = "Você se esqueceu de preencher os parâmetros da pesquisa.";
			  	} else {
			  		$cond = "nome";
			  	}
			  
			} else {

				$sSQL  = "SELECT ";
				$sSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
				$sSQL .= "   rg_inscr, expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
				$sSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
				$sSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
				$sSQL .= "   ativo,obs ";
				$sSQL .= "FROM cltb_cliente ";
				//$sSQL .= "WHERE $campo = '$campo_pesquisa' ";
				$sSQL .= "WHERE ";

				switch($cond) {

				   case 'nome':
				      $sSQL .= "   nome_razao ilike '%$campo_pesquisa%' ";
				      break;
				   case 'CPF':
				      $sSQL .= "   cpf_cnpj = '" . $this->bd->escape(@$_REQUEST["campo_pesquisa"]) . "' ";
				      break;
				   case 'cod':
				      $sSQL .= "   id_cliente = '" . $this->bd->escape(@$_REQUEST["campo_pesquisa"]) . "' ";
				      break;
				}
		
				$clientes = $this->bd->obtemRegistros($sSQL);
		
				if( $this->bd->obtemErro() ) {
				   echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
				   echo "SQL: $sSQL <br>\n";
				}
		
				$this->tpl->atribui("lista_clientes", $clientes);
				
				if( !count($clientes) ) {
				   $erros[] = "A pesquisa não retornou resultados";
				}
				
				
		
			} //fecha o else do mostra				
			
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("cond",$cond);
			$this->tpl->atribui("campo_pesquisa",$campo_pesquisa);
			
			$this->arquivoTemplate="clientes_pesquisa.html";
				

				
				
				
				
		} else if ($op == "eliminar"){
			$this->arquivoTemplate = "eliminar_cliente.html";
		} else if ($op =="clientemod"){
			$this->arquivoTemplate = "ficha_cliente.html";
		} else if ($op == "clientedisc"){
			$this->arquivoTemplate = "cliente_discado.html";
		} else if ($op == "clienteAdisc"){
			$this->arquivoTemplate = "cliente_discado_altera.html";
		} else if ($op == "clienteAmail"){
			$this->arquivoTemplate = "cliente_email_altera.html";
		} else if ($op == "clienteAhosp"){
			$this->arquivoTemplate = "cliente_hospedagem_altera.html";
		} else if ($op == "clientehosp"){
			$this->arquivoTemplate = "cliente_hospedagem.html";
		} else if ($op == "clientebl"){
			$this->arquivoTemplate = "cliente_bandalarga.html";
		} else if ($op == "clienteAbl"){
			$this->arquivoTemplate = "cliente_bandalarga_altera.html";
		} else if ($op == "clienteCob"){
			$this->arquivoTemplate = "cliente_cobranca.html";
		} else if ($op == "clienteCobHist"){
			$this->arquivoTemplate = "cliente_cobranca_historico.html";
		} else if ($op == "clienteCobContr"){
			$this->arquivoTemplate = "cliente_cobranca_contratos.html";
		} else if ($op == "clienteAcob"){
			$this->arquivoTemplate = "cliente_cobranca_alteracob.html";
		} else if ($op == "clienteAcontr"){
			$this->arquivoTemplate = "cliente_cobranca_alteracontr.html";
		} else if ($op == "clienteContrExibe"){
			$this->arquivoTemplate = "cliente_contrato.html";
		} else if ($op == "clienteContr"){
			$this->arquivoTemplate = "cliente_novo_contrato.html";
		} else if ($op =="clnada"){
			$this->arquivoTemplate = "cliente_cobranca_nada.html";
		} else if ($op == "clBl"){
			$this->arquivoTemplate = "cliente_cobranca_bl.html";
		}else if ($op == "clDisc"){
			$this->arquivoTemplate = "cliente_cobranca_disc.html";
		} else if ($op == "clHosp"){
			$this->arquivoTemplate = "cliente_cobranca_hosp.html";
		}  else if ($op =="clEliminado"){
			$this->arquivoTemplate = "cliente_eliminadoOk.html";
		} else if ($op =="confirmacadcli"){
			$this->arquivoTemplate = "confirma_cadastro_pops.html";
		}else if ($op =="confirmaaltcli"){
			$this->arquivoTemplate = "confirma_alteracao_pops.html";
		}
	}
	
	
	
	

				
				
}
			














?>
