<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAConfiguracao extends VirtexAdmin {

	public function VAConfiguracao() {
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
				$id_pop = @$_REQUEST["id_pop"];
				//$id_pop = @$_REQUEST["id_nas"];
				$enviando = false;
				
				
				$reg = array();
	
	
				if( $acao ) {
				   // Se ele recebeu o campo ação é pq veio de um submit
				   $enviando = true;
				} else {
				   // Se não recebe o campo ação e tem id_pop é alteração, caso contrário é cadastro.
				   if( $id_pop ) {
				      // SELECT
				      $sSQL  = "SELECT ";
				      $sSQL .= "   id_pop, nome, info, ";
				      $sSQL .= "   interface, username_conta, username, dominio, tipo_conta ";
				      $sSQL .= "FROM cftb_pop ";
				      $sSQL .= "WHERE id_pop = $id_pop ";
	
	
					
				      $reg = $this->bd->obtemUnicoRegistro($sSQL);
				      
				      
				      
				      $acao = "alt";
				      
				      
				      
				      
				   } else {
				      $acao = "cad";
				   }
				}
				
				if ($acao == "cad"){
				   $msg_final = "Cliente cadastrado com sucesso!";
				   $titulo = "Cadastrar";
				   
				}else{
				   $msg_final = "Cliente alterado com sucesso!";
				   $titulo = "Alterar";
				   }
				
				
				
	
				$this->tpl->atribui("op",$op);
				$this->tpl->atribui("acao",$acao);
				$this->tpl->atribui("id_pop",$id_pop);
	
	
				// O cara clicou no botão enviar (submit).
				if( $enviando ) {
				   // Validar
				   $erros = $this->validaFormulario();
				   
				   if( count($erros) ) {
				      $reg = $_REQUEST;
				      
				   } else {
				      // Gravar no banco.
				      $sSQL = "";
				      if( $acao == "cad" ) {
				         $id_pop = $this->bd->proximoID("popsq_id_pop");
	
				         // Cadastro
				         $sSQL  = "INSERT INTO ";
				         $sSQL .= "   cftb_pop( ";
					 $sSQL .= "      id_pop, nome, info, ";
				     	 $sSQL .= "   interface, username_conta, username, dominio, tipo_conta )";
					 $sSQL .= "   VALUES (";
					 $sSQL .= "     '" . $this->bd->escape($id_pop) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["info"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["interface"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username_conta"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["dominio"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo_conta"]) . "' ";
					 $sSQL .= "     )";
	                           //!!!!!!!!!!!parei aqui 09/02/06
	
				      } else {
				         // Alteração
				         $sSQL  = "UPDATE ";
				         $sSQL .= "   cftb_pop ";
				         $sSQL .= "SET ";
				         $sSQL .= "   nome_razao = '" . $this->bd->escape(@$_REQUEST["nome_razao"]) . "', ";
				         $sSQL .= "   tipo_pessoa = '" . $this->bd->escape(@$_REQUEST["tipo_pessoa"]) . "', ";
	       			         $sSQL .= "   rg_inscr = '" . $this->bd->escape(@$_REQUEST["rg_inscr"]) . "', ";
	       			         $sSQL .= "   expedicao = '" . $this->bd->escape(@$_REQUEST["expedicao"]) . "', ";
	       			         $sSQL .= "   cpf_cnpj = '" . $this->bd->escape(@$_REQUEST["cpf_cnpj"]) . "', ";
	       			         $sSQL .= "   email = '" . $this->bd->escape(@$_REQUEST["email"]) . "', ";
	       			         $sSQL .= "   endereco = '" . $this->bd->escape(@$_REQUEST["endereco"]) . "', ";
	       			         $sSQL .= "   complemento = '" . $this->bd->escape(@$_REQUEST["complemento"]) . "', ";
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
				         $sSQL .= "   id_pop = '" . $this->bd->escape(@$_REQUEST["id_pop"]) . "' ";
				         
	    		         
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
	
				      $this->arquivoTemplate="msgredirect.html";
	
				      // cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
				      return;
				   }
	
				}
	
				// Atribui a variável de erro no template.
				$this->tpl->atribui("erros",$erros);
				
				// Atribui as listas
				global $_LS_ESTADOS;
				$this->tpl->atribui("lista_estados",$_LS_ESTADOS);
				
				global $_LS_TP_PESSOA;
				$this->tpl->atribui("lista_tp_pessoa",$_LS_TP_PESSOA);
				
				global $_LS_ST_CLIENTE;
				$this->tpl->atribui("lista_ativo",$_LS_ST_CLIENTE);
	
				global $_LS_DIA_PGTO;
				$this->tpl->atribui("lista_dia_pagamento",$_LS_DIA_PGTO);
	
				
				
				// Atribui os campos
			        $this->tpl->atribui("id_pop",@$reg["id_pop"]);
			        $this->tpl->atribui("data_cadastro",@$reg["data_cadastro"]);
			        $this->tpl->atribui("nome_razao",@$reg["nome_razao"]);
			        $this->tpl->atribui("tipo_pessoa",@$reg["tipo_pessoa"]);
			        $this->tpl->atribui("rg_inscr",@$reg["rg_inscr"]);
			        $this->tpl->atribui("expedicao",@$reg["expedicao"]);
			        $this->tpl->atribui("cpf_cnpj",@$reg["cpf_cnpj"]);
			        $this->tpl->atribui("email",@$reg["email"]);
			        $this->tpl->atribui("endereco",@$reg["endereco"]);
			        $this->tpl->atribui("complemento",@$reg["complemento"]);
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
			        
			        
	
				// Seta as variáveis do template.
			$this->arquivoTemplate = "clientes_cadastro.html";
	
	
	
	
	
	
	
	if($op == "pop"){	
		$this->arquivoTemplate = "configuracoes_pops.html";
	} else if ($op == "nas"){
		$this->arquivoTemplate = "configuracoes_nas.html";
	} else if ($op == "monitor"){
		$this->arquivoTemplate = "configuracoes_monitoramento.html";
	} else if ($op == "cadpop"){
		$this->arquivoTemplate = "cadastro_pop.html";
	} else if ($op == "altpop"){
		$this->arquivoTemplate = "alteracao_pop.html";
	} else if ($op == "cadnas"){
		$this->arquivoTemplate = "cadastro_nas.html";
	} else if ($op == "altnas"){
		$this->arquivoTemplate = "alteracao_nas.html";
	} else if ($op == "cadredes"){
		$this->arquivoTemplate = "cadastro_nas_redes.html";
	} else if ($op == "altredes"){
		$this->arquivoTemplate = "alteracao_nas_redes.html";
	} else if ($op == "listaredes"){
		$this->arquivoTemplate = "lista_nas_redes.html";
	} else if ($op == "cadpopok"){
		$this->arquivoTemplate = "confirma_cadastro_pops.html";
	} else if ($op == "altpopok"){
		$this->arquivoTemplate = "confirma_alteracao_pops.html";
	}  else if ($op == "cadnasok"){
		$this->arquivoTemplate = "confirma_cadastro_pops.html";
	} else if ($op == "altnasok"){
		$this->arquivoTemplate = "confirma_alteracao_pops.html";
	} else if ($op == "cadredesok"){
		$this->arquivoTemplate = "confirma_cadastro_redes.html";
	} else if ($op == "altredesok"){
		$this->arquivoTemplate = "confirma_alteracao_redes.html";
	} else if ($op == "voltaredes"){
		$this->arquivoTemplate = "lista_nas_redes.html";
	} 
}


}



?>
