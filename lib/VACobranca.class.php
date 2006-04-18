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
		//INICIO DO CADASTRAMENTO E ALTERAÇÃO DE PRODUTO
			
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
			   
			   	if ($prod == "BL"){
			   
			      // SELECT
			      $sSQL  = "SELECT ";
			      $sSQL .= "   p.id_produto, p.nome, p.descricao, p.tipo, ";
			      $sSQL .= "   p.valor, p.disponivel, p.num_emails, p.quota_por_conta, p.vl_email_adicional, ";
			      $sSQL .= "   p.permitir_outros_dominios, p.numero_contas, pbl.banda_upload_kbps, pbl.banda_download_kbps, pbl.franquia_trafego_mensal_gb, ";
			      $sSQL .= "   pbl.valor_trafego_adicional_gb ";
			      $sSQL .= "FROM prtb_produto p , prtb_produto_bandalarga pbl ";
			      $sSQL .= "WHERE p.id_produto = pbl.id_produto ";
			      $sSQL .= "AND p.id_produto = $id_produto ";
			      
			      //$sSQL = "SELECT * FROM prtb_produto INNER JOIN prtb_produto_bandalarga ON (prtb_produto.id_produto = $id_produto AND prtb_produto_bandalarga.id_produto = $id_produto)";
			      
			      
			      } else if ($prod == "D"){
			      
			      $sSQL  = "SELECT ";
			      $sSQL .= "   p.id_produto, p.nome, p.descricao, p.tipo, ";
			      $sSQL .= "   p.valor, p.disponivel, p.num_emails, p.quota_por_conta, p.vl_email_adicional, ";
			      $sSQL .= "   p.permitir_outros_dominios, p.numero_contas, pd.franquia_horas, pd.permitir_duplicidade, pd.valor_hora_adicional ";
			      $sSQL .= "FROM prtb_produto p , prtb_produto_discado pd ";
			      $sSQL .= "WHERE p.id_produto = pd.id_produto ";
			      $sSQL .= "AND p.id_produto = $id_produto ";

			      //$sSQL = "SELECT * FROM prtb_produto INNER JOIN prtb_produto_discado ON (prtb_produto.id_produto = $id_produto AND prtb_produto_discado.id_produto = $id_produto)";
			      
			      } else if ($prod == "H"){
			      
			      $sSQL  = "SELECT ";
			      $sSQL .= "   p.id_produto, p.nome, p.descricao, p.tipo, ";
			      $sSQL .= "   p.valor, p.disponivel, p.num_emails, p.quota_por_conta, p.vl_email_adicional, ";
			      $sSQL .= "   p.permitir_outros_dominios, p.numero_contas, ph.dominio, ph.franquia_em_mb, ph.valor_mb_adicional ";
			      $sSQL .= "FROM prtb_produto p , prtb_produto_hospedagem ph ";
			      $sSQL .= "WHERE p.id_produto = ph.id_produto ";
			      $sSQL .= "AND p.id_produto = $id_produto ";

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
					
					/* Início do tratamento de erros */
			      		
			      		$descricao = trim(@$_REQUEST["descricao"]);
					if(!$descricao) $descricao = "Nao informado";			      
			      		
					$valor = number_format(trim(@$_REQUEST['valor']),2,'.',',');
					if(!$valor) $valor = 0.00;	
					
					$num_emails = (int) trim(@$_REQUEST["num_emails"]);
					if(!$num_emails) $num_emails = 0;
					
					$quota_por_conta = (int) trim(@$_REQUEST['quota_por_conta']);
					if(!$quota_por_conta) $quota_por_conta = 0;
					
					$vl_email_adicional = number_format(trim(@$_REQUEST['vl_email_adicional']),2,'.',',');
					if(!$vl_email_adicional) $vl_email_adicional = 0.00;

					$numero_contas = (int) trim(@$_REQUEST['numero_contas']);
					if(!$numero_contas) $numero_contas = 0;
					
					$prod = strtoupper(@$_REQUEST['prod']);
					
					$franquia_trafego_mensal_gb = (int) trim(@$_REQUEST['franquia_trafego_mensal_gb']);
					if(!$franquia_trafego_mensal_gb) $franquia_trafego_mensal_gb = 0;
					
					$valor_trafego_adicional_gb = number_format(trim(@$_REQUEST['valor_trafego_adicional_gb']),2,'.',',');
					if(!$valor_trafego_adicional_gb) $valor_trafego_adicional_gb = 0.00;
					
					$franquia_horas = (int) trim(@$_REQUEST['franquia_horas']);
					if(!$franquia_horas) $franquia_horas = 0;
					
					$valor_hora_adicional = number_format(trim(@$_REQUEST['valor_hora_adicional']),2,'.',',');
					if(!$valor_hora_adicional) $valor_hora_adicional = 0.00;
					
					$franquia_em_mb = (int) trim(@$_REQUEST['franquia_em_mb']);
					if(!$franquia_em_mb) $franquia_em_mb = 0;					
					
					$valor_mb_adicional = number_format(trim(@$_REQUEST['valor_mb_adicional']),2,'.',',');
					if(!$valor_mb_adicional) $valor_mb_adicional = 0.00;
					
					/* Final do tratamento de erros */
					
			      			
			         	$id_produto = $this->bd->proximoID("prsq_id_produto");//?
			         	//INICIO DO CADASTRO DE PRODUTOS
			         
			         	//INSERÇÃO NA TABELA prtb_produto
				 	$sSQL  = "INSERT INTO ";
				 	$sSQL .= "prtb_produto ";
				 	$sSQL .= "(id_produto, nome, descricao, tipo, valor, disponivel, ";
				 	$sSQL .= "num_emails, quota_por_conta, vl_email_adicional, permitir_outros_dominios, ";
				 	$sSQL .= "numero_contas)";
				 	$sSQL .= "VALUES (";
				 	$sSQL .= " '" . $this->bd->escape($id_produto) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['nome']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape($descricao) . "', ";
				 	$sSQL .= " '" . $this->bd->escape($prod) . "', ";
				 	$sSQL .= " '" . $this->bd->escape($valor) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['disponivel']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape($num_emails) . "', ";
				 	$sSQL .= " '" . $this->bd->escape($quota_por_conta) . "', ";
				 	$sSQL .= " '" . $this->bd->escape($vl_email_adicional) . "', ";
				 	$sSQL .= " '" . $this->bd->escape(@$_REQUEST['permitir_outros_dominios']) . "', ";
				 	$sSQL .= " '" . $this->bd->escape($numero_contas) ."' ";
				 	$sSQL .= " )";

					if ($prod == "BL"){
								         
								         		
						// INSERÇÃO NA TABELA prtb_produto_bandalarga
						$tSQL  = "INSERT INTO ";
						$tSQL .= "prtb_produto_bandalarga ";
						$tSQL .= "(id_produto, banda_upload_kbps, banda_download_kbps, ";
						$tSQL .= "franquia_trafego_mensal_gb, valor_trafego_adicional_gb)";
						$tSQL .= "VALUES (";
						$tSQL .= " '" . $this->bd->escape($id_produto) . "', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['banda_upload_kbps']) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['banda_download_kbps']) ."', ";
						$tSQL .= " '" . $this->bd->escape($franquia_trafego_mensal_gb) ."', ";
						$tSQL .= " '" . $this->bd->escape($valor_trafego_adicional_gb) ."' ";
						$tSQL .= " )";
					
								         		
						//$template = "cobranca_produtos_bandalarga.html";
						
								         
					}else if ($prod == "D"){
								         		
						//INSERÇÃO NA TABELA prtb_produto_discado
						$tSQL  = "INSERT INTO ";
						$tSQL .= "prtb_produto_discado ";
						$tSQL .= "(id_produto, franquia_horas, permitir_duplicidade, ";
						$tSQL .= "valor_hora_adicional) ";
						$tSQL .= "VALUES (";
						$tSQL .= " '" . $this->bd->escape($id_produto) . "', ";
						$tSQL .= " '" . $this->bd->escape($franquia_horas) ."', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['permitir_duplicidade']) ."', ";
						$tSQL .= " '" . $this->bd->escape($valor_hora_adicional) ."' ";
						$tSQL .= " )";
											
						//$template = "cobranca_produtos_discado.html";
					
								         	
					}else if ($prod == "H"){
										
						//INSERÇÃO NA TABELA prtb_produto_hospedagem
						$tSQL  = "INSERT INTO ";
						$tSQL .= "prtb_produto_hospedagem ";
						$tSQL .= "(id_produto, dominio, franquia_em_mb, ";
						$tSQL .= "valor_mb_adicional) ";
						$tSQL .= "VALUES (";
						$tSQL .= " '" . $this->bd->escape($id_produto) . "', ";
						$tSQL .= " '" . $this->bd->escape(@$_REQUEST['dominio']) ."', ";
						$tSQL .= " '" . $this->bd->escape($franquia_em_mb) ."', ";
						$tSQL .= " '" . $this->bd->escape($valor_mb_adicional) ."' ";
						$tSQL .= " )";
											
						//$template = "cobranca_produtos_hospedagem.html";
								         	
								         	
			         	}


			         

				// FINAL DO CADASTRO DE PRODUTOS
			      } else {
			        // INICIO DO UPDATE DE PRODUTOS
			         // Alteração

					/* Início do tratamento de erros */
			      		
			      		$descricao = trim(@$_REQUEST["descricao"]);
					if(!$descricao) $descricao = "Nao informado";			      
			      		
					$valor = number_format(trim(@$_REQUEST['valor']),2,'.',',');
					if(!$valor) $valor = 0.00;	
					
					$num_emails = (int) trim(@$_REQUEST["num_emails"]);
					if(!$num_emails) $num_emails = 0;
					
					$quota_por_conta = (int) trim(@$_REQUEST['quota_por_conta']);
					if(!$quota_por_conta) $quota_por_conta = 0;
					
					$vl_email_adicional = number_format(trim(@$_REQUEST['vl_email_adicional']),2,'.',',');
					if(!$vl_email_adicional) $vl_email_adicional = 0.00;

					$numero_contas = (int) trim(@$_REQUEST['numero_contas']);
					if(!$numero_contas) $numero_contas = 0;
					
					$prod = strtoupper(@$_REQUEST['prod']);
					
					$franquia_trafego_mensal_gb = (int) trim(@$_REQUEST['franquia_trafego_mensal_gb']);
					if(!$franquia_trafego_mensal_gb) $franquia_trafego_mensal_gb = 0;
					
					$valor_trafego_adicional_gb = number_format(trim(@$_REQUEST['valor_trafego_adicional_gb']),2,'.',',');
					if(!$valor_trafego_adicional_gb) $valor_trafego_adicional_gb = 0.00;
					
					$franquia_horas = (int) trim(@$_REQUEST['franquia_horas']);
					if(!$franquia_horas) $franquia_horas = 0;
					
					$valor_hora_adicional = number_format(trim(@$_REQUEST['valor_hora_adicional']),2,'.',',');
					if(!$valor_hora_adicional) $valor_hora_adicional = 0.00;
					
					$franquia_em_mb = (int) trim(@$_REQUEST['franquia_em_mb']);
					if(!$franquia_em_mb) $franquia_em_mb = 0;					
					
					$valor_mb_adicional = number_format(trim(@$_REQUEST['valor_mb_adicional']),2,'.',',');
					if(!$valor_mb_adicional) $valor_mb_adicional = 0.00;
					
					/* Final do tratamento de erros */


					//UPDATE DA TABELA prtb_produto
					$sSQL  = "UPDATE ";
					$sSQL .= "   prtb_produto ";
					$sSQL .= "SET ";
					$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
					$sSQL .= "   descricao = '" . $this->bd->escape($descricao) . "', ";
					$sSQL .= "   tipo = '" . $this->bd->escape(@$_REQUEST["tipo"]) . "', ";
					$sSQL .= "   valor = '" . $this->bd->escape($valor) . "', ";
					$sSQL .= "   disponivel = '" . $this->bd->escape(@$_REQUEST["disponivel"]) . "', ";
					$sSQL .= "   num_emails = '" . $this->bd->escape($num_emails) . "', ";
					$sSQL .= "   quota_por_conta = '" . $this->bd->escape($quota_por_conta) . "', ";
					$sSQL .= "   vl_email_adicional = '" . $this->bd->escape($vl_email_adicional) . "', ";
					$sSQL .= "   permitir_outros_dominios = '" . $this->bd->escape(@$_REQUEST["permitir_outros_dominios"]) . "', ";
					$sSQL .= "   numero_contas = '" . $this->bd->escape($numero_contas) . "' ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_produto = " . $this->bd->escape(@$_REQUEST["id_produto"]) . " ";
										
										
									
								         
					if($prod=="BL"){
								         	
						//UPDATE DA TABELA prtb_produto_bandalarga
						$tSQL  = "UPDATE ";
						$tSQL .= "   prtb_produto_bandalarga ";
						$tSQL .= "SET ";
						$tSQL .= "   banda_upload_kbps = '" . $this->bd->escape(@$_REQUEST["banda_upload_kbps"]) . "', ";
						$tSQL .= "   banda_download_kbps = '" . $this->bd->escape(@$_REQUEST["banda_download_kbps"]) . "', ";
						$tSQL .= "   franquia_trafego_mensal_gb = '" . $this->bd->escape($franquia_trafego_mensal_gb) . "', ";
						$tSQL .= "   valor_trafego_adicional_gb = '" . $this->bd->escape($valor_trafego_adicional_gb) . "' ";
						$tSQL .= "WHERE ";
						$tSQL .= "   id_produto = " . $this->bd->escape(@$_REQUEST["id_produto"]) . " ";
										
								         	
					}else if($prod=="D"){
								         	
						//UPDATE DA TABELA prtb_produto_discado
						$tSQL  = "UPDATE ";
						$tSQL .= "   prtb_produto_discado ";
						$tSQL .= "SET ";
						$tSQL .= "   franquia_horas = '" . $this->bd->escape($franquia_horas) . "', ";
						$tSQL .= "   permitir_duplicidade = '" . $this->bd->escape(@$_REQUEST["permitir_duplicidade"]) . "', ";
						$tSQL .= "   valor_hora_adicional = '" . $this->bd->escape($valor_hora_adicional) . "' ";
						$tSQL .= "WHERE ";
						$tSQL .= "   id_produto = " . $this->bd->escape(@$_REQUEST["id_produto"]) . " ";
										
								         	
					}else if($prod=="H"){
								         	
						//UPDATE DA TABELA prtb_produto_hospedagem
						$tSQL  = "UPDATE ";
						$tSQL .= "   prtb_produto_hospedagem ";
						$tSQL .= "SET ";
						$tSQL .= "   dominio = '" . $this->bd->escape(@$_REQUEST["dominio"]) . "', ";
						$tSQL .= "   franquia_em_mb = '" . $this->bd->escape($franquia_em_mb) . "', ";
						$tSQL .= "   valor_mb_adicional = '" . $this->bd->escape($valor_mb_adicional) . "' ";
						$tSQL .= "WHERE ";
						$tSQL .= "   id_produto = " . $this->bd->escape(@$_REQUEST["id_produto"]) . " ";
					
								         	
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
			      $this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=lista");
			      $this->tpl->atribui("target","_top");



			      $this->arquivoTemplate="msgredirect.html"; //faz exibir o msgredirect.html que tem vai receber a mensagem de erro ou sucesso.

			      // cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
			      return;
			   }

			}

			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			
			
			// Atribui as listas
			global $_LS_DISPONIVEL;
			$this->tpl->atribui("ls_disponivel",$_LS_DISPONIVEL);
			
			global $_LS_BANDA;
			$this->tpl->atribui("ls_banda_download_kbps",$_LS_BANDA);

			//global $_LS_UPLOAD;
			$this->tpl->atribui("ls_banda_upload_kbps",$_LS_BANDA);
				
			global $_LS_DUPLICIDADE;
			$this->tpl->atribui("ls_permitir_duplicidade",$_LS_DUPLICIDADE);
			
			global $_LS_DOMINIO;
			$this->tpl->atribui("ls_dominio",$_LS_DOMINIO);
			
			global $_LS_OUTROS_DOMINIOS;
			$this->tpl->atribui("ls_permitir_outros_dominios",$_LS_OUTROS_DOMINIOS);
			
			global $_LS_EMAIL_ANEXADO;
			$this->tpl->atribui("ls_email_anexado",$_LS_EMAIL_ANEXADO);
			
			
			
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
			
			
			//FINAL DO CADASTRAMENTO E ALTERAÇÃO DE PRODUTO


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

				
		
		}else if ($op == "bloqueados"){
		
		$this->arquivoTemplate = "cobranca_versaolight.html";
		
		
		} else if ($op == "boleto"){
		
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$data = @$_REQUEST["data"];
			$id_cliente = @$_REQUEST["id_cliente"];
			
			$sSQL  = "SELECT cl.nome_razao, cl.endereco, cl.id_cidade, cl.estado, cl.cep, cl.cpf_cnpj, cd.cidade as nome_cidade, cd.id_cidade  ";
			$sSQL .= "FROM";
			$sSQL .= "cltb_cliente cl, cftb_cidade cd ";
			$sSQL .= "WHERE ";
			$sSQL .= "cl.id_cliente = '$id_cliente' AND ";
			$sSQL .= "cd.id_cidade = cl.id_cidade";
			
			$cliente = $this->bd->obtemUnicoRegistro($sSQL);
			
			
			$sSQL  = "SELECT valor, id_cobranca,  FROM ";
			$sSQL .= "cbtb_faturas ";
			$sSQL .= "WHERE ";
			$sSQL .= "id_cliente_produto = '$id_cliente_produto' AND ";
			$sSQL .= "data = '$data' ";
			
			//echo "fatura: $sSQL<br>";

			$fatura = $this->bd->obtemRegistros($sSQL);
			
			
			// PEGANDO INFORMAÇÕES DAS PREFERENCIAS
			$sSQL  = "SELECT ";
			$sSQL .= " tx_juros, multa, dia_venc, carencia, cod_banco, carteira, agencia, num_conta, convenio, cnpj, observacoes,nome ";
			$sSQL .= "FROM ";
			$sSQL .= " cftb_preferencias ";
			$sSQL .= "WHERE id_provedor = '1'";
			
			$provedor = $this->bd->obtemUnicoRegistro($sSQL);
			
			$codigo = @$_REQUEST["codigo"];
			$data_venc = "30/04/2006";
			//echo $codigo;
			$endereco = $cliente["endereco"]." - ". $cliente["nome_cidade"]." - ".$cliente["estado"]."<br> CEP: ".$cliente["cep"];
			
			
			
			if( $codigo ) {
				MBoleto::barCode($codigo);
			} else {
				$this->b = new MBoleto($provedor["cod_banco"],$provedor["carteira"],$provedor["agencia"],$$provedor["num_conta"],$provedor["convenio"],$data_venc,$fatura["valor"],$fatura["id_cobranca"],$cliente["nome_razao"],$cliente["cpf_cnpj"],$provedor["nome"],$provedor["cnpj"],$provedor["tx_juros"],$provedor["multa"],$endereco,$provedor["observacoes"]);
				$this->b->setTplPath("template/boletos/");
				$this->b->setImgPath("template/boletos/imagens");
				
				$this->b->exibe("001"); // Gera boleto para o banco "001";
			}
		
			//$this->arquivoTemplate = "";
			
		
		}else if ($op == "amortizacao"){
		
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$data = @$_REQUEST["data"];
			$acao = @$_REQUEST["acao"];
			$id_cliente = @$_REQUEST["id_cliente"];
						
			$sSQL  = "SELECT ";
			$sSQL .= "* ";
			$sSQL .= "FROM ";
			$sSQL .= "cbtb_faturas ";
			$sSQL .= "WHERE ";
			$sSQL .= "id_cliente_produto = '$id_cliente_produto' AND ";
			$sSQL .= "data = '$data'";
			
			//ECHO "AMORT: $sSQL<br>";
			
			$amort = $this->bd->obtemUnicoRegistro($sSQL);
			
			$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
			$cliente = $this->bd->obtemUnicoRegistro($sSQL);
				
			//echo "sql: $sSQL<br> Nome:".$cliente["nome_razao"]."<br> ";
				
			$this->tpl->atribui("cliente",$cliente);

		
			global $_LS_STATUS_FATURA;
			$this->tpl->atribui("ls_status_fatura",$_LS_STATUS_FATURA);

			$data = $amort["data"];

			    if (strstr($data, "/")){ 
			        $A = explode ("/", $data); 
			        $data = $A[2] . "-". $A[1] . "-" . $A[0]; 
			    } 
			    else{ 
			        $A = explode ("-", $data); 
			        $data = $A[2] . "/". $A[1] . "/" . $A[0];     
			    } 


			//echo $data;
			$this->tpl->atribui("id_cliente",$id_cliente);
			$this->tpl->atribui("data",$data);
			$this->tpl->atribui("amort",$amort);
			$this->arquivoTemplate = "cliente_cobranca_amortizacao.html";
			
			
			if ($acao == "alt"){
					
				
				$this->amortizar();
				
				
				$msg_final = "Amortização/Pagamento efetuado com sucesso!";
				
				$this->tpl->atribui("mensagem",$msg_final); 
				$this->tpl->atribui("url", "clientes.php?op=cobranca&id_cliente=".$id_cliente."&rotina=resumo");
				$this->tpl->atribui("target","_top");
				
				
				$this->arquivoTemplate="msgredirect.html";

			}
	}
	
}
	
public function amortizar(){


	$data = @$_REQUEST["reagendamento"];
	$data_pagamento = @$_REQUEST["data_pagamento"];
	$reagendamento = @$_REQUEST["reagendamento"];
	$reagendar = @$_REQUEST["reagendar"];
	
	
	//Se existir uma data de reagendamento então faz 
	//o tratamento dessa data de reagendamento
	if($reagendamento) {
		list($d, $m, $a) = explode("/", $reagendamento);
		$reagendamento = "$a-$m-$d";
	}

	//Se existir uma data de vencimento então faz 
	//o tratamento dessa data de vencimento
	if ($data) {
		if (strstr($data, "/")){ 
			$A = explode ("/", $data); 
			$data = $A[2] . "-". $A[1] . "-" . $A[0]; 
		} else { 
			$A = explode ("-", $data); 
			$data = $A[2] . "/". $A[1] . "/" . $A[0];     
		} 

	}
	
	
	//Se existir uma data de pagamento então faz 
	//o tratamento dessa data de pagamento
	if ($data_pagamento) {
		if (strstr($data_pagamento, "/")){ 
			$A = explode ("/", $data_pagamento); 
			$data_pagamento = $A[2] . "-". $A[1] . "-" . $A[0]; 
		} else { 
			$A = explode ("-", $data_pagamento); 
			$data_pagamento = $A[2] . "/". $A[1] . "/" . $A[0];     
		} 
	}
	
	$amortizar = str_replace(",",".",@$_REQUEST["amortizar"]);
	$desconto = str_replace(",",".",@$_REQUEST["desconto"]);
	$acrescimo = str_replace(",",".",@$_REQUEST["acrescimo"]);
	
	
	$sSQL  = "UPDATE ";
	$sSQL .= "	cbtb_faturas ";
	$sSQL .= "SET ";
	$sSQL .= "	status = '".@$_REQUEST["status"]."', ";
	$sSQL .= "	observacoes = '".@$_REQUEST["observacoes"]."', ";
	
	if ($reagendar && $reagendamento)
		$sSQL .= "	reagendamento = '$reagendamento', ";
	
	$sSQL .= "	pagto_parcial = pagto_parcial + '".$amortizar."', ";
	$sSQL .= "	data_pagamento = '".$data_pagamento."', ";
	$sSQL .= "	desconto = '".$desconto."', ";
	$sSQL .= "	acrescimo = '".$acrescimo."', ";
	$sSQL .= "	valor_pago = '".$amortizar."' ";
	$sSQL .= "WHERE ";
	$sSQL .= "	id_cliente_produto = '".@$_REQUEST["id_cliente_produto"]."' AND ";
	$sSQL .= "	data = '".@$_REQUEST["data"]."' ";
				
				
	//echo "QUERY: $sSQL <br>\n";
	$this->bd->consulta($sSQL);
	
	return;



}
	
public function __destruct() {
      	parent::__destruct();
}
}
?>
