<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VACobranca extends VirtexAdmin {

	public function VACobranca() {
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
			$id_produto = @$_REQUEST["id_produto"];
			$prod = @$_REQUEST['prod'];

			$enviando = false;
			
			
			$reg = array();


			if( $acao ) {
			   // Se ele recebeu o campo ação é pq veio de um submit
			   $enviando = true;
			} else {
			   // Se não recebe o campo ação e tem id_produto é alteração, caso contrário é cadastro.
			   if( $id_produto ) {
			   
			   	if ($prod == "bl"){
			   
			      // SELECT
			      $sSQL  = "SELECT ";
			      $sSQL .= "   p.id_produto, p.nome, p.descricao, p.tipo, ";
			      $sSQL .= "   p.valor, p.disponivel, p.num_emails, p.quota_por_conta, p.vl_email_adicional, ";
			      $sSQL .= "   p.permitir_outros_dominios, p.email_anexado, p.numero_contas, pbl.banda_upload_kbps, pbl.banda_download_kbps, pbl.franquia_trafego_mensal_gb, ";
			      $sSQL .= "   pbl.valor_trafego_adicional_gb ";
			      $sSQL .= "FROM prtb_produto p , prtb_produto_bandalarga pbl ";
			      $sSQL .= "WHERE p.id_produto = pbl.id_produto ";
			      $sSQL .= "AND p.id_produto = $id_produto ";
			      
			      //$sSQL = "SELECT * FROM prtb_produto INNER JOIN prtb_produto_bandalarga ON (prtb_produto.id_produto = $id_produto AND prtb_produto_bandalarga.id_produto = $id_produto)";
			      
			      
			      } else if ($prod == "d"){
			      
			      $sSQL  = "SELECT ";
			      $sSQL .= "   p.id_produto, p.nome, p.descricao, p.tipo, ";
			      $sSQL .= "   p.valor, p.disponivel, p.num_emails, p.quota_por_conta, p.vl_email_adicional, ";
			      $sSQL .= "   p.permitir_outros_dominios, p.email_anexado, p.numero_contas, pd.franquia_horas, pd.permitir_duplicidade, pd.valor_hora_adicional ";
			      $sSQL .= "FROM prtb_produto p , prtb_produto_discado pd ";
			      $sSQL .= "WHERE p.id_produto = pd.id_produto ";
			      $sSQL .= "AND p.id_produto = $id_produto ";

			      //$sSQL = "SELECT * FROM prtb_produto INNER JOIN prtb_produto_discado ON (prtb_produto.id_produto = $id_produto AND prtb_produto_discado.id_produto = $id_produto)";
			      
			      } else if ($prod == "h"){
			      
			      $sSQL  = "SELECT ";
			      $sSQL .= "   p.id_produto, p.nome, p.descricao, p.tipo, ";
			      $sSQL .= "   p.valor, p.disponivel, p.num_emails, p.quota_por_conta, p.vl_email_adicional, ";
			      $sSQL .= "   p.permitir_outros_dominios, p.email_anexado, p.numero_contas, ph.dominio, ph.franquia_em_mb, ph.valor_mb_adicional ";
			      $sSQL .= "FROM prtb_produto p , prtb_produto_hospedagem ph ";
			      $sSQL .= "WHERE p.id_produto = ph.id_produto ";
			      $sSQL .= "WHERE p.id_produto = $id_produto ";

			      //$sSQL = "SELECT * FROM prtb_produto INNER JOIN prtb_produto_hospedagem ON (prtb_produto.id_produto = $id_produto AND prtb_produto_hospedagem.id_produto = $id_produto)";
			      
			      }
			      
			      

				
			      $reg = $this->bd->obtemUnicoRegistro($sSQL);
			      
			      
			      
			      $acao = "alt";
			      
			      
			      
			      
			   } else {
			      $acao = "cad";
			   }
			}
			
			if ($acao == "cad"){
			   $msg_final = "Produto cadastrado com sucesso!";
			   $titulo = "Cadastro";
			   			   
			}else{
			   $msg_final = "Produto alterado com sucesso!";
			   $titulo = "Alteração";
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
			         	//INICIO DO CADASTRO DE PRODUTOS
			         
			         	//INSERÇÃO NA TABELA prtb_produto
				 	$sSQL  = "INSERT INTO ";
				 	$sSQL .= "prtb_produto ";
				 	$sSQL .= "(id_produto, nome, descricao, tipo, valor, disponivel, ";
				 	$sSQL .= "num_emails, quota_por_conta, vl_email_adicional, permitir_outros_dominios, ";
				 	$sSQL .= "email_anexado, numero_contas)";
				 	$sSQL .= "VALUES (";
				 	$sSQL .= " '" . $this->bd->escape($id_produto) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['nome']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['descricao']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['tipo']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['valor']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['disponivel']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['num_emails']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['quota_por_conta']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['vl_email_adicional']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['permitir_outros_dominios']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['email_anexado']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['numero_contas']) ."' ";
				 	$sSQL .= " )";

					if ($prod == "bl"){
								         
								         		
						// INSERÇÃO NA TABELA prtb_produto_bandalarga
						$tSQL  = "INSERT INTO ";
						$tSQL .= "prtb_produto_bandalarga ";
						$tSQL .= "(id_produto, banda_upload_kbps, banda_download_kbps, ";
						$tSQL .= "franquia_trafego_mensal_gb, valor_trafego_adicional_gb)";
						$tSQL .= "VALUES (";
						$tSQL .= " '" . $this->bd->escape($id_produto) . "', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['banda_upload_kbps']) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['banda_download_kbps']) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['franquia_trafego_mensal_gb']) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['valor_trafego_adicional_gb']) ."' ";
						$tSQL .= " )";
					
								         		
						//$template = "cobranca_produtos_bandalarga.html";
						
								         
					}else if ($prod == "d"){
								         		
						//INSERÇÃO NA TABELA prtb_produto_discado
						$tSQL  = "INSERT INTO ";
						$tSQL .= "prtb_produto_discado ";
						$tSQL .= "(id_produto, franquia_horas, permitir_duplicidade, ";
						$tSQL .= "valor_hora_adicional) ";
						$tSQL .= "VALUES (";
						$tSQL .= " '" . $this->bd->escape($id_produto) . "', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['franquia_horas']) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['permitir_duplicidade']) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['valor_hora_adicional']) ."' ";
						$tSQL .= " )";
											
						//$template = "cobranca_produtos_discado.html";
					
								         	
					}else if ($prod == "h"){
										
						//INSERÇÃO NA TABELA prtb_produto_hospedagem
						$tSQL  = "INSERT INTO ";
						$tSQL .= "prtb_produto_hospedagem ";
						$tSQL .= "(id_produto, dominio, franquia_em_mb, ";
						$tSQL .= "valor_mb_adicional) ";
						$tSQL .= "VALUES (";
						$tSQL .= " '" . $this->bd->escape($id_produto) . "', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['dominio']) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['franquia_em_mb']) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['valor_mb_adicional']) ."' ";
						$tSQL .= " )";
											
						//$template = "cobranca_produtos_hospedagem.html";
								         	
								         	
			         	}


			         

				// FINAL DO CADASTRO DE PRODUTOS
			      } else {
			        // INICIO DO UPDATE DE PRODUTOS
			         // Alteração


					//UPDATE DA TABELA prtb_produto
					$sSQL  = "UPDATE ";
					$sSQL .= "   prtb_produto ";
					$sSQL .= "SET ";
					$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
					$sSQL .= "   descricao = '" . $this->bd->escape(@$_REQUEST["descricao"]) . "', ";
					$sSQL .= "   tipo = '" . $this->bd->escape(@$_REQUEST["tipo"]) . "', ";
					$sSQL .= "   valor = '" . $this->bd->escape(@$_REQUEST["valor"]) . "', ";
					$sSQL .= "   disponivel = '" . $this->bd->escape(@$_REQUEST["disponivel"]) . "', ";
					$sSQL .= "   num_emails = '" . $this->bd->escape(@$_REQUEST["num_emails"]) . "', ";
					$sSQL .= "   quota_por_conta = '" . $this->bd->escape(@$_REQUEST["quota_por_conta"]) . "', ";
					$sSQL .= "   vl_email_adicional = '" . $this->bd->escape(@$_REQUEST["vl_email_adicional"]) . "', ";
					$sSQL .= "   permitir_outros_dominios = '" . $this->bd->escape(@$_REQUEST["permitir_outros_dominios"]) . "', ";
					$sSQL .= "   email_anexado = '" . $this->bd->escape(@$_REQUEST["email_anexado"]) . "', ";
					$sSQL .= "   numero_contas = '" . $this->bd->escape(@$_REQUEST["numero_contas"]) . "' ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_produto = '" . $this->bd->escape(@$_REQUEST["id_produto"]) . "' ";
										
										
									
								         
					if($prod=="bl"){
								         	
						//UPDATE DA TABELA prtb_produto_bandalarga
						$tSQL  = "UPDATE ";
						$tSQL .= "   prtb_produto_bandalarga ";
						$tSQL .= "SET ";
						$tSQL .= "   banda_upload_kbps = '" . $this->bd->escape(@$_REQUEST["banda_upload_kbps"]) . "', ";
						$tSQL .= "   banda_download_kbps = '" . $this->bd->escape(@$_REQUEST["banda_download_kbps"]) . "', ";
						$tSQL .= "   franquia_trafego_mensal_gb = '" . $this->bd->escape(@$_REQUEST["franquia_trafego_mensal_gb"]) . "', ";
						$tSQL .= "   valor_trafego_adicional_gb = '" . $this->bd->escape(@$_REQUEST["valor_trafego_adicional_gb"]) . "' ";
						$tSQL .= "WHERE ";
						$tSQL .= "   id_produto = '" . $this->bd->escape(@$_REQUEST["id_produto"]) . "' ";
										
								         	
					}else if($prod=="d"){
								         	
						//UPDATE DA TABELA prtb_produto_discado
						$tSQL  = "UPDATE ";
						$tSQL .= "   prtb_produto_discado ";
						$tSQL .= "SET ";
						$tSQL .= "   franquia_horas = '" . $this->bd->escape(@$_REQUEST["franquia_horas"]) . "', ";
						$tSQL .= "   permitir_duplicidade = '" . $this->bd->escape(@$_REQUEST["permitir_duplicidade"]) . "', ";
						$tSQL .= "   valor_hora_adicional = '" . $this->bd->escape(@$_REQUEST["valor_hora_adicional"]) . "' ";
						$tSQL .= "WHERE ";
						$tSQL .= "   id_produto = '" . $this->bd->escape(@$_REQUEST["id_produto"]) . "' ";
										
								         	
					}else if($prod=="h"){
								         	
						//UPDATE DA TABELA prtb_produto_hospedagem
						$tSQL  = "UPDATE ";
						$tSQL .= "   prtb_produto_hospedagem ";
						$tSQL .= "SET ";
						$tSQL .= "   dominio = '" . $this->bd->escape(@$_REQUEST["dominio"]) . "', ";
						$tSQL .= "   franquia_em_mb = '" . $this->bd->escape(@$_REQUEST["franquia_em_mb"]) . "', ";
						$tSQL .= "   valor_mb_adicional = '" . $this->bd->escape(@$_REQUEST["valor_mb_adicional"]) . "' ";
						$tSQL .= "WHERE ";
						$tSQL .= "   id_produto = '" . $this->bd->escape(@$_REQUEST["id_produto"]) . "' ";
					
								         	
			         	}
			         
    		         		//FINAL DO UPDATE DE PRODUTOS
			      }

					$this->bd->consulta($sSQL);  //mostra mensagem de erro
					$this->bd->consulta($tSQL);
						      
			      if( $this->bd->obtemErro() != MDATABASE_OK ) {
			         echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
			         echo "QUERY: " . $sSQL . "<br>\n";
			         echo "QUERY2: ". $tSQL . "<br>\n";
			      }


			      // Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
			      $this->tpl->atribui("mensagem",$msg_final); //pega o conteúdo de msg_final e envia para mensagem que é uma val do smart.
			      $this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=listagem");
			      $this->tpl->atribui("target","_top");



			      $this->arquivoTemplate="msgredirect.html"; //faz exibir o msgredirect.html que tem vai receber a mensagem de erro ou sucesso.

			      // cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
			      return;
			   }

			}

			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			
			// tabela prtb_produto
			
			//echo "SQL: ". $sSQL . "<br>\n";
			//echo "resultados: ". $reg ."<br>\n";
			
		        $this->tpl->atribui("id_produto",@$reg["id_produto"]);
		        $this->tpl->atribui("nome",@$reg["nome"]);
		        $this->tpl->atribui("descricao",@$reg["descricao"]);
		        $this->tpl->atribui("tipo",@$reg["tipo"]);
		        $this->tpl->atribui("valor",@$reg["valor"]);
		        $this->tpl->atribui("disponivel",@$reg["disponivel"]);
		        $this->tpl->atribui("num_emails",@$reg["num_emails"]);
		        $this->tpl->atribui("quota_por_conta",@$reg["quota_por_conta"]);
		        $this->tpl->atribui("vl_email_adicional",@$reg["vl_email_adicional"]);
		        $this->tpl->atribui("permitir_outros_dominios",@$reg["permitir_outros_dominios"]);
		        $this->tpl->atribui("email_anexado",@$reg["email_anexado"]);
		        $this->tpl->atribui("numero_contas",@$reg["numero_contas"]);
		        
		        //tabela prtb_produto_bandalarga
		        $this->tpl->atribui("banda_upload_kbps",@$reg["banda_upload_kbps"]);
		        $this->tpl->atribui("banda_download_kbps",@$reg["banda_download_kbps"]);
		        $this->tpl->atribui("franquia_trafego_mensal_gb",@$reg["franquia_trafego_mensal_gb"]);
		        $this->tpl->atribui("valor_trafego_adicional_gb",@$reg["valor_trafego_adicional_gb"]);
		        
		        
		        //tabela prtb_produto_discado
		        $this->tpl->atribui("franquia_horas",@$reg["franquia_horas"]);			
		        $this->tpl->atribui("permitir_duplicidade",@$reg["permitir_duplicidade"]);			
		        $this->tpl->atribui("valor_hora_adicional",@$reg["valor_hora_adicional"]);
		        
		        
		        
		        //tabela prtb_produto_hospedagem
		        $this->tpl->atribui("dominio",@$reg["dominio"]);			
		        $this->tpl->atribui("franquia_em_mb",@$reg["franquia_em_mb"]);			
		        $this->tpl->atribui("valor_mb_adicional",@$reg["valor_mb_adicional"]);			
		        
		        $this->tpl->atribui("titulo",$titulo);
			$this->tpl->atribui("prod",$prod);
	        

			// Seta as variáveis do template.
			$this->arquivoTemplate = "cobranca_produtos_novo.html";


		} else if($op == "lista"){
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

				
		
		}
		
	}

}


?>
