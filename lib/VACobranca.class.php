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
	// INICIO DE PRODUTOS CADASTRO
		
		$erros = array();
		$acao = @$_REQUEST["acao"];
		$prod = @$_REQUEST['prod'];
		$enviando = false;
		$reg = array();


	if (!$id_prod){
		$acao = "cad";
		$titulo = "Cadastro";
		}else{
		$acao = "alt";
		$titulo = "Alteração";
		}
		
		
		
		if (!$prod){
		
			$tipo = "cobranca_produtos_nada.html";	
			$this->tpl->atribui("tipo",$tipo );
			$this->arquivoTemplate = "cobranca_produtos_novo.html";
		
		}else if ($prod == "bl"){
		
		        
		        $tipo = "cobranca_produtos_bandalarga.html";
		        
		        $this->tpl->atribui("nome",@$_REQUEST['nome']);
		        $this->tpl->atribui("descricao",@$_REQUEST['descricao']);
			$this->tpl->atribui("tipo",@$_REQUEST['tipo']);
			$this->tpl->atribui("valor",@$_REQUEST['valor']);
			$this->tpl->atribui("disponivel",@$_REQUEST['disponivel']);
			$this->tpl->atribui("banda_upload_kbps",@$_REQUEST['banda_upload_kbps']);
			$this->tpl->atribui("banda_download_kbps",@$_REQUEST['banda_download_kbps']);
			$this->tpl->atribui("franquia_trafego_mensal_gb",@$_REQUEST['franquia_trafego_mensal_gb']);
			$this->tpl->atribui("valor_trafego_adicional_gb",@$_REQUEST['valor_trafego_adicional_gb']);
			$this->tpl->atribui("numero_contas",@$_REQUEST['numero_contas']);
		        $this->tpl->atribui("titulo","Cadastro" );
		        $this->tpl->atribui("tipo",$tipo );
		        
		        
		        $id_produto = $this->bd->proximoID("prsq_id_produto");//?
		        
				if ($acao = "cad"){
				
					$enviando = true;
				        $id_produto = $this->bd->proximoID("prsq_id_produto");//?
					
					$sSQL  = "INSERT INTO ";
					$sSQL .= "prtb_produto ";
					$sSQL .= "(id_produto, nome, descricao, tipo, valor, disponivel, ";
					$sSQL .= "num_emails, quota_por_conta, vl_email_adicional, permitir_outros_dominios, ";
					$sSQL .= "emails_anexados, numero_contas)";
					$sSQL .= "VALUES (";
					$sSQL .= " '" . $this->bd->escape($id_produto) . "', ";
					$sSQL .= " '" . $this->bd->escape(@$_REQUEST['nome']) . "', ";
					$sSQL .= " '" . $this->bd->escape(@$_REQUEST['descricao']) . "', ";
					$sSQL .= " '" . $this->bd->escape(@$_REQUEST['tipo']) . "', ";
					$sSQL .= " '" . $this->bd->escape(@$_REQUEST['valor']) . "', ";
					$sSQL .= " '" . $this->bd->escape(@$_REQUEST['disponivel']) . "' ";
					//-----------------------------------------------! P A R E I  A Q U I !-----------------------------------
					
					
					
					$sSQL .= " )";
					
					$tSQL  = "INSERT INTO ";
					$tSQL .= "prtb_produto_bandalarga ";
					
				
				
				
				
				}
			
			
			
				
			
			
			
			$this->arquivoTemplate = "cobranca_produtos_novo.html";
		
		
		}else if ($prod == "d"){
		
			$tipo = "cobranca_produtos_discado.html";
			$this->tpl->atribui("titulo","Cadastro" );
			$this->tpl->atribui("tipo",$tipo );
			$this->arquivoTemplate = "cobranca_produtos_novo.html";
		
		
		}else if ($prod == "h"){
		
			$tipo = "cobranca_produtos_hospedagem.html";	
			$this->tpl->atribui("titulo","Cadastro" );
			$this->tpl->atribui("tipo",$tipo );
			$this->arquivoTemplate = "cobranca_produtos_novo.html";
		}
		
		
	
		
		
		
		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
	// FINAL DE PRODUTOS CADASTRO	
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