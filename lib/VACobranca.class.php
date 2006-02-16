<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );
// Criacao da classe VACobranca para uso no sistema de cobrança.
class VACobranca extends VirtexAdmin {

	public function VACobranca() {
		parent::VirtexAdmin();
	
	
	}
	
// metodo para pegar as propriedadas enviadas via menu na interface.
	public function processa($op=null) {	
	if($op == "bloqueados"){	
		$this->arquivoTemplate = "cobr_bloqueados.html";
	} else if ($op == "cadastrar"){
	// INICIO DE PRODUTOS
	
				$erros = array();
	
				$acao = @$_REQUEST["acao"];
				$id_produto = @$_REQUEST["id_produto"];
	
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
				      $sSQL .= "   id_produto, nome, descricao, tipo, ";
				      $sSQL .= "   valor, disponivel ";
				      $sSQL .= "FROM prtb_produto ";
				      $sSQL .= "WHERE id_produto = $id_produto ";
	
	
					
				      $reg = $this->bd->obtemUnicoRegistro($sSQL);
				      
				      
				      
				      $acao = "alt";
				      
				      
				      
				      
				   } else {
				      $acao = "cad";
				   }
				}
				
				if ($acao == "cad"){
				   $msg_final = "Produto cadastrado com sucesso!";
				   $titulo = "Cadastrar";
				   
				   
				}else{
				   $msg_final = "Produto alterado com sucesso!";
				   $titulo = "Alterar";
				   }
				
	
				$this->tpl->atribui("op",$op);
				$this->tpl->atribui("acao",$acao);
				$this->tpl->atribui("id_produto",$id_produto);
	
	
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
				         $id_produto = $this->bd->proximoID("prsq_id_produto");//?
	
				         // Cadastro
				         $sSQL  = "INSERT INTO ";
				         $sSQL .= "   prtb_produto( ";
					 $sSQL .= "      id_produto, nome, descricao, tipo, valor, disponivel )";
				         $sSQL .= "   VALUES (";
					 $sSQL .= "     '" . $this->bd->escape($id_produto) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["descricao"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["valor"]) . "', ";
					 $sSQL .= "     '" . $this->bd->escape(@$_REQUEST["disponivel"]) . "', ";
				         $sSQL .= "     )";
	
	
				      } else {
				         // Alteração
				         $sSQL  = "UPDATE ";
				         $sSQL .= "   prtb_produto ";
				         $sSQL .= "SET ";
				         $sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
				         $sSQL .= "   descricao = '" . $this->bd->escape(@$_REQUEST["descricao"]) . "', ";
	       			         $sSQL .= "   tipo = '" . $this->bd->escape(@$_REQUEST["tipo"]) . "', ";
	       			         $sSQL .= "   valor = '" . $this->bd->escape(@$_REQUEST["valor"]) . "', ";
	       			         $sSQL .= "   disponivel = '" . $this->bd->escape(@$_REQUEST["disponivel"]) . "', ";
				         $sSQL .= "WHERE ";
				         $sSQL .= "   id_produto = '" . $this->bd->escape(@$_REQUEST["id_produto"]) . "' ";  // se id_produto for =  ao passado.
				         
	    		         
				      }
	
				      $this->bd->consulta($sSQL);  //mostra mensagem de erro
				      
				      if( $this->bd->obtemErro() != MDATABASE_OK ) {
				         echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
				         echo "QUERY: " . $sSQL . "<br>\n";
				      }
	
	
				      // Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
				      $this->tpl->atribui("mensagem",$msg_final); //pega o conteúdo de msg_final e envia para mensagem que é uma val do smart.
				      $this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=listagem");
				      $this->tpl->atribui("target","_top");
	
				       //  if (count($checa)){
				       //  $this->arquivoTemplate="clientes_cadastro.html";
				       // }
	
	
				      $this->arquivoTemplate="msgredirect.html"; //faz exibir o msgredirect.html que tem vai receber a mensagem de erro ou sucesso.
	
				      // cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
				      return;
				   }
	
				}
	
				// Atribui a variável de erro no template.
				$this->tpl->atribui("erros",$erros);
				
				// Atribui as listas
	
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
			        
			        $this->tpl->atribui("titulo",$titulo);// para que no clientes_cadastro.html a variavel do smart titulo consiga pegar o que foi definido no $titulo.
			        
	
				// Seta as variáveis do template.
				$this->arquivoTemplate = "produtos.html";
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	//FINAL DE PRODUTOS	
		
		
	} else if ($op == "lista"){
	// COMEÇO DE LISTA DE PRODUTOS
	//FUNCIONANDO!!!!!!!
		$erros = array();
		$filtro = @$_REQUEST['filtro'];
		$disp = @$_REQUEST['disp'];
		
		$sSQL  = "SELECT ";
		$sSQL .= "   id_produto, nome, descricao, tipo, ";
		$sSQL .= "   valor, disponivel ";
		$sSQL .= "FROM prtb_produto ";
		
		$where = "";
		
		$clausulas = array();
			if ($filtro){
				$clausulas[] = "tipo = '". $this->bd->escape($_REQUEST["filtro"]) ."' ";
			}
			
			if ($disp){
			
				$clausulas[] = "disponivel = '". $this->bd->escape($_REQUEST["disp"]) ."' ";
			
				}else{
			
					$clausulas[] = "disponivel = 't'";
			
			}
			
		if (count($clausulas)){
		
			$where = "WHERE ". implode(' AND ', $clausulas);
			
			$sSQL .= $where;
		
		}
		
		
		

		$produtos = $this->bd->obtemRegistros($sSQL);
		
		if( $this->bd->obtemErro() != MDATABASE_OK ) {
		echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
		echo "QUERY: " . $sSQL . "<br>\n";
		}

		$this->tpl->atribui("disp",$disp);
		$this->tpl->atribui("op",$op);
		$this->tpl->atribui("filtro",$filtro);
		$this->tpl->atribui("id_produto",@$reg["id_produto"]);
		$this->tpl->atribui("nome",@$reg["nome"]);
		$this->tpl->atribui("descricao",@$reg["descricao"]);
		$this->tpl->atribui("tipo",@$reg["tipo"]);
		$this->tpl->atribui("valor",@$reg["valor"]);
		$this->tpl->atribui("disponivel",@$reg["disponivel"]);
		
		$this->tpl->atribui("lista_produtos", $produtos);


		
		$this->arquivoTemplate = "cobranca_produtos_lista.html";
	
	
	
	
	// FINAL DE LISTA DE PRODUTOS
	} else if ($op == "desbloqOk"){
		$this->arquivoTemplate = "desbloqueado_ok.html";
	} else if ($op == "cadprobl"){
		$this->arquivoTemplate = "cadastro_produto_bl.html";
	} else if ($op == "cadprodisc"){
		$this->arquivoTemplate = "cadastro_produto_disc.html";
	} else if ($op == "cadprohosp"){
		$this->arquivoTemplate = "cadastro_produto_hosp.html";
	} else if ($op == "altprodbl"){
		$this->arquivoTemplate = "cobranca_altera_bl.html";
	} else if ($op == "altproddisc"){
		$this->arquivoTemplate = "cobranca_altera_disc.html";
	} else if ($op == "altprodhosp"){
		$this->arquivoTemplate = "cobranca_altera_hosp.html";
		}	
}


}



?>