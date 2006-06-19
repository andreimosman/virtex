<?

require_once( PATH_LIB . "/VirtexAdmin.class.php" );

require_once( "jpgraph.php" );
require_once( "jpgraph_line.php" );
require_once( "jpgraph_bar.php" );

require_once('MRetornoPagContas.class.php');

class VACobranca extends VirtexAdmin {

	public function VACobranca() {
		parent::VirtexAdmin();


	}


	protected function validaFormulario() {
	   $erros = array();
	   return $erros;
	}

	public function obtem_mes($numero_mes) {	
			if ($numero_mes < 10 && strlen($numero_mes) > 1)
				$numero_mes = substr($numero_mes,1,1);
				
			global $_LS_MESES_ANO;
			return $_LS_MESES_ANO[$numero_mes];		
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
			      $this->tpl->atribui("target","_self");



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
		
		
		require_once("dede_bloqueios.php");
		//$this->arquivoTemplate = "cobranca_versaolight.html";
		
		
		} else if ($op == "boleto"){
			$codigo = @$_REQUEST['codigo'];
		
			if( $codigo ) {
				$this->arquivoTemplate = "";
				MArrecadacao::barCode($codigo);
				return;
			} else {
			
				/* BOLETO BANCO DO BRASIL 

				$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
				$data = @$_REQUEST["data"];
				$id_cliente = @$_REQUEST["id_cliente"];

				$sSQL  = "SELECT cl.nome_razao, cl.endereco, cl.id_cidade, cl.estado, cl.cep, cl.cpf_cnpj, cd.cidade as nome_cidade, cd.id_cidade  ";
				$sSQL .= "FROM ";
				$sSQL .= "cltb_cliente cl, cftb_cidade cd ";
				$sSQL .= "WHERE ";
				$sSQL .= "cl.id_cliente = '$id_cliente' AND ";
				$sSQL .= "cd.id_cidade = cl.id_cidade";

				$cliente = $this->bd->obtemUnicoRegistro($sSQL);
				//echo "CLIENTE: $sSQL  <br>";


				$sSQL  = "SELECT valor, id_cobranca,to_char(data, 'DD/mm/YYYY') as data  FROM ";
				$sSQL .= "cbtb_faturas ";
				$sSQL .= "WHERE ";
				$sSQL .= "id_cliente_produto = '$id_cliente_produto' AND ";
				$sSQL .= "data = '$data' ";

				//echo "fatura: $sSQL<br>";

				$fatura = $this->bd->obtemUnicoRegistro($sSQL);


				// PEGANDO INFORMAÇÕES DAS PREFERENCIAS
				$sSQL  = "SELECT ";
				$sSQL .= " pc.tx_juros, pc.multa, pc.dia_venc, pc.carencia, pc.cod_banco, pc.carteira, pc.agencia, pc.num_conta, pc.convenio, pp.cnpj, pc.observacoes,pg.nome,pp.endereco,pp.localidade,pp.cep ";
				$sSQL .= "FROM ";
				$sSQL .= " pftb_preferencia_geral pg, pftb_preferencia_provedor pp, pftb_preferencia_cobranca pc ";
				$sSQL .= "WHERE pc.id_provedor = '1'";

				$provedor = $this->bd->obtemUnicoRegistro($sSQL);

				$codigo = @$_REQUEST["codigo"];
				//$data_venc = "30/04/2006";
				$data_venc = $fatura["data"];
				//echo $codigo;
				$valor = $fatura["valor"];
				$id_cobranca = $fatura["id_cobranca"];
				$nome_cliente = $cliente["nome_razao"];
				$cpf_cliente = $cliente["cpf_cnpj"];
				//echo "VALOR: $valor <BR>";

				$endereco = $cliente["endereco"]." - ". $cliente["nome_cidade"]." - ".$cliente["estado"]."<br> CEP: ".$cliente["cep"];



				if( $codigo ) {
					MBoleto::barCode($codigo);
				} else {
					$this->b = new MBoleto($provedor["cod_banco"],$provedor["carteira"],$provedor["agencia"],$provedor["num_conta"],$provedor["convenio"],$data_venc,$valor,$id_cobranca,$nome_cliente,$cpf_cliente,$provedor["nome"],$provedor["cnpj"],$provedor["tx_juros"],$provedor["multa"],$endereco,$provedor["observacoes"],$provedor["endereco"],$provedor["localidade"]);
					$this->b->setTplPath("template/boletos/");
					$this->b->setImgPath("template/boletos/imagens");

					$this->b->exibe("003"); // Gera boleto para o banco "001";
				}

				//$this->arquivoTemplate = "";
				*/
						
			
				/* PAGCONTAS */

				$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
				$data = @$_REQUEST["data"];
				$id_cliente = @$_REQUEST["id_cliente"];
				
				$this->carne($id_cliente_produto,$data,$id_cliente);
				
				//echo $carne;

//				copy("/mosman/virtex/dados/carnes/codigos/".$codigo_barras.".png","codigos/".$codigo_barras.".png");
				$template = $this->tpl->obtemPagina("../boletos/pc-estilo.html");
				$template .= $this->tpl->obtemPagina("../boletos/layout-pc.html");
				//echo $template;
				//$this->arquivoTemplate = "../boletos/layout-pc.html";

				/* PAGCONTAS */ 
			}
			
			
			
		}else if($op == "gerar_boletos"){
		
			$acao = @$_REQUEST["acao"];
		
		
			$this->arquivoTemplate = "cliente_cobranca_fechamento.html";
			$dia_inicio = @$_REQUEST["dia_inicio"];
			$dia_final  = @$_REQUEST["dia_final"];
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];
			$dti = date("Y-m-d", mktime(0,0,0, $mes, $dia_inicio, $ano));
			$dtf = date("Y-m-d", mktime(0,0,0, $mes, $dia_final, $ano));
			//echo "DTI: $dti<br>\n";
			//echo "DTF: $dtf<br>\n";
			
			//phpinfo();
			
		
		
			if ($acao == "ok"){
		
				/* PAGCONTAS */

				$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
				$data = @$_REQUEST["data"];
				$id_cliente = @$_REQUEST["id_cliente"];
				
				$this->geraCarne($dia_inicio,$dia_final,$mes,$ano,$dti,$dtf);
				
				//echo $carne;
				//$this->tpl->atribui("",);
				//$this->tpl->obtemPagina("../boletos/layout-pc.html");
				//$this->arquivoTemplate = "../boletos/layout-pc.html";

				/* PAGCONTAS */ 
				

			
			
			
			}
		}else if($op == "gerar_carne"){
				
			$acao = @$_REQUEST["acao"];

			//$this->arquivoTemplate = "cliente_cobranca_fechamento.html";
			$dia_inicio = @$_REQUEST["dia_inicio"];
			$dia_final  = @$_REQUEST["dia_final"];
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];

			$dti = date("Y-m-d", mktime(0,0,0, $mes, $dia_inicio, $ano));
			$dtf = date("Y-m-d", mktime(0,0,0, $mes, $dia_final, $ano));

			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$data = @$_REQUEST["data"];
			$id_cliente = @$_REQUEST["id_cliente"];

			return;


	
		}else if ($op == "lista_boletos") {
			
		
			$dia_inicio = @$_REQUEST["dia_inicio"];
			$dia_final  = @$_REQUEST["dia_final"];
			$mes = @$_REQUEST["mes"];
			$ano = @$_REQUEST["ano"];
			$dti = date("Y-m-d", mktime(0,0,0, $mes, $dia_inicio, $ano));
			$dtf = date("Y-m-d", mktime(0,0,0, $mes, $dia_final, $ano));

		
			$provedor = $this->prefs->obtem("total");
			//$provedor = $this->prefs->obtem();
			
			$sSQL  = "SELECT ";
			$sSQL .= " * from cbtb_contrato where status = 'A' AND vencimento BETWEEN '$dia_inicio' AND '$dia_final'";

			$contrato = $this->bd->obtemRegistros($sSQL);

			for($i=0;$i<count($contrato);$i++){
		
				$vencimento = $contrato[$i]["vencimento"];
		
					
				$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $mes, $vencimento, $ano));
		
				$sSQL  = "SELECT ";
				$sSQL .= "nome FROM prtb_produto WHERE id_produro = '".$contrato[$i]["id_produto"]."'";
		
				$produto = $this->bd->obtemUnicoRegistro($sSQL);
				//echo "QUERY PRODUTO($i): $sSQL <br>\n";
										
				// Verifica se existe fatura emitida para o contrato selecionado na data especificada
				// em $fatura_dt_vencimento
							
				$sSQL = "SELECT * FROM cbtb_fatura WHERE id_cliente_produto = '" . $contrato[$i]["id_cliente_produto"]."' AND data = '".$fatura_dt_vencimento."' ";
				$faturas = $this->bd->obtemRegistros($sSQL);
							
				// Se nao retornou registros cria a fatura
				if( !count($faturas) ) {
		        	$sSQL =  "INSERT INTO cbtb_faturas(";
		            $sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
		            $sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
		            $sSQL .= "	acrescimo, valor_pago ";
		            $sSQL .= ") VALUES (";
		            $sSQL .= " '".$contrato[$i]["id_cliente_produto"]."', '$fatura_dt_vencimento','".$produto["nome"]."', '".$contrato[$i]["valor"]."', '".$contrato[$i]["status"]."', null, ";
		            $sSQL .= "	NULL, '0', NULL, '0', ";
		            $sSQL .= "	'0', '0' ";
		            $sSQL .= ")";
		
		            $this->bd->consulta($sSQL);
		            //echo "FATURA($i): $sSQL <br>\n";
		        }
			}

			$sSQL  = "SELECT cf.valor, cf.id_cobranca,to_char(cf.data, 'DD/mm/YYYY') as data_conv, cf.data, cf.id_cliente_produto, cp.id_cliente_produto, cp.id_cliente, cl.id_cliente, cl.nome_razao  FROM ";
			$sSQL .= "cbtb_faturas cf, cltb_cliente cl, cbtb_cliente_produto cp ";
			$sSQL .= "WHERE ";
			$sSQL .= "data BETWEEN '$dti' AND '$dtf' AND ";
			$sSQL .= "cf.id_cliente_produto = cp.id_cliente_produto AND ";
			$sSQL .= "cp.id_cliente = cl.id_cliente";
					
			$faturas = $this->bd->obtemRegistros($sSQL);
			//echo "$sSQL <br>\n";

			$largura = "720";
			$altura = "400";

			$this->tpl->atribui("faturas",$faturas);
			$this->tpl->atribui("largura",$largura);
			$this->tpl->atribui("altura",$altura);
			$this->arquivoTemplate = "cliente_cobranca_fechamento.html";
			
		
		}else if ($op == "amortizacao"){
		
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$data = @$_REQUEST["data"];
			$acao = @$_REQUEST["acao"];
			$id_cliente = @$_REQUEST["id_cliente"];

			$this->obtemPR($id_cliente);

						
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
			
			$sSQL = "SELECT status, id_cliente, id_cliente_produto FROM cntb_conta where id_cliente = '$id_cliente' AND id_cliente_produto = '$id_cliente_produto' AND status = 'S' ";
			//echo $sSQL;
			$suspenso = $this->bd->obtemRegistros($sSQL);
				
			//echo "sql: $sSQL<br> Nome:".$cliente["nome_razao"]."<br> ";
			
			$this->tpl->atribui("suspenso",$suspenso);
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
				
				/*$sSQL  = "SELECT ";
				$sSQL .= "f.id_cliente_produto, to_char(f.data, 'DD/mm/YYYY') as data_conv,f.data, f.valor, f.observacoes,f.descricao, to_char(f.reagendamento, 'DD/mm/YYYY') as reagendamento, f.pagto_parcial, ";
				$sSQL .= "to_char(f.data_pagamento, 'DD/mm/YYYY') as data_pagamento, f.desconto, f.acrescimo, f.valor_pago, ";
				$sSQL .= "c.id_cliente_produto, c.id_cliente, ";
				$sSQL .= "CASE WHEN (f.data < CAST(now() as date) AND f.status='A') OR (f.reagendamento < CAST(now() as date) AND f.status='R') ";
				$sSQL .= "THEN 'S' ELSE ";
				$sSQL .= "CASE WHEN f.reagendamento is not null AND f.status != 'P' ";
				$sSQL .= "THEN 'G' ELSE f.status ";
				$sSQL .= "END ";
				$sSQL .= "END as extstatus ";
				$sSQL .= "FROM ";
				$sSQL .= "cbtb_faturas f, cbtb_cliente_produto c ";
				$sSQL .= "WHERE ";
				$sSQL .= "id_cliente = '$id_cliente' ";
				$sSQL .= "AND ";
				$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
				$sSQL .= "AND (f.status = 'A' OR f.status = 'R') ";
				$sSQL .= "AND f.data < now() + interval '10 day' ";
				$sSQL .= "ORDER BY f.data ASC ";

				$lista_faturas = $this->bd->obtemRegistros($sSQL);
				//echo "Lista: $sSQL <br>";

				$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
				$cliente = $this->bd->obtemUnicoRegistro($sSQL);*/

				$sSQL = "SELECT * FROM cbtb_faturas WHERE id_cliente_produto = '$id_cliente_produto' AND reagendamento is null AND status = 'A' AND data <= now() + interval '10 day' ";
				$suspenso = $this->bd->obtemRegistros($sSQL);
				//echo $sSQL ."<BR>";
				
				if (!$suspenso){
				
					$sSQL = "UPDATE cntb_conta SET status = 'A' WHERE id_cliente_produto = '$id_cliente_produto' AND status = 'S' ";
					//echo $sSQL ."<BR>";
					$this->bd->consulta($sSQL);
					
					$msg_final = "Amortização/Pagamento efetuado com sucesso!";
				
				}else{
				
					$msg_final = "Amortização/Pagamento efetuado com sucesso!<br>Existem outras faturas em atrazo que impossibilitam o desbloqueio automático.";
				
				}
				//$this->tpl->atribui("cliente",$cliente);
				//$this->tpl->atribui("lista_faturas",$lista_faturas);
				//$this->tpl->atribui("susp",$suspenso);
				$this->tpl->atribui("mensagem",$msg_final); 
				$this->tpl->atribui("url", "clientes.php?op=cobranca&id_cliente=".$id_cliente."&rotina=resumo");
				$this->tpl->atribui("target","_self");
				
				$this->arquivoTemplate="msgredirect.html";

			}
	}else if ($op == "retornos"){
	
		$acao = @$_REQUEST["acao"];
		global $_LS_FORMATOS_PAG;
		$admin = $this->admLogin->obtemAdmin();

		$this->tpl->atribui("ls_formatos",$_LS_FORMATOS_PAG);
		$this->tpl->atribui("op",@$_REQUEST["op"]);

		$sop = @$_REQUEST['sop'];


		if( !$sop ) $sop = "upload";
		
			$sSQL  = "SELECT id_arquivo, nome_arquivo, to_char(data,'DD/MM/YYYY') as data, status, nra, nrpe, nrsc FROM lgtb_retorno ORDER BY data DESC limit 10";
			$ret = $this->bd->obtemRegistros($sSQL);
			
			$this->tpl->atribui("ret",$ret);
			$this->arquivoTemplate = "cobranca_retorno.html";


		if( @$_REQUEST["submit"] ) {
		   $sErro = "";

		   // Verifica se foi feito upload do arquivo.
		   //echo "F: " . $_FILES['retorno']['tmp_name'] . "<br>\n";

		   $arquivo = $_FILES['arquivo'];
		   //phpinfo();

		   if( !$arquivo['tmp_name'] ) {
			   $sErro = "Você não enviou o arquivo para processamento";
		   } else {
			   // Verifica se o arquivo bate com o tipo especificado
			   $formato = @$_REQUEST["formato"];

			   if( $formato == "PC" ) {

				   ////////////////
				   // Pag Contas //
				   ////////////////
				   $r = new MRetornoPagContas($arquivo['tmp_name']);
				   $registros  = $r->obtemRegistros();
				   

				   if( !count($registros) || !$r->checkSum() ) {
					   $sErro = "Arquivo inválido ou adulterado.";
				   } else {

						$nome = $arquivo["name"];
						$tamanho = $arquivo["size"];


						$sSQL = "SELECT nome_arquivo,to_char(data,'DD/MM/YYYY HH24:MM:SS') as data FROM lgtb_retorno WHERE nome_arquivo = '$nome' order by data desc limit 1";
						$checa_arquivo = $this->bd->obtemUnicoRegistro($sSQL);
						//echo $sSQL;
						
						if (!$checa_arquivo || $checa_arquivo == ""){


							$sSQL = "INSERT INTO lgtb_retorno (nome_arquivo,tamanho,data,admin) VALUES ('$nome','$tamanho',now(),'$admin')";
							$this->bd->consulta($sSQL);
						
							//echo "RETORNO: $sSQL <br>";

						}else{

							$sErro = "Arquivo já processado em ".$checa_arquivo["data"];
							$mostra = "nao";
							$this->tpl->atribui("mostra",$mostra);


						}

					   // Varre o arquivo
					    $sop = "processa";
						$qtde = 0;
					   for($i=0;$i<count($registros);$i++) {

						  $registros[$i]["nsr"] 			= (int)$registros[$i]["nsr"];
						  $registros[$i]["data_pagamento"] 	= $r->formataData($registros[$i]["data_pagamento"]);
						  $registros[$i]["data_credito"] 	= $r->formataData($registros[$i]["data_credito"]);
						  $registros[$i]["valor_recebido"] 	= $r->formataValor($registros[$i]["valor_recebido"]);
						  //$registros[$i]["codigo_barras"]	= $r->formataValor($registros[$i]["codigo_barras"]);
						  $registros[$i]["codigo_barras"]	= $registros[$i]["codigo_barras"];
						  $registros[$i]["valor_tarifa"]	= $r->formataValor($registros[$i]["valor_tarifa"]);
						  //$registros[$i]["id_ag_cc_dig"]	= ($registros[$i]["is_ag_cc_dig"]);
						  

						  						  
						  $sSQL  = "SELECT ";
						  $sSQL .= " f.id_cliente_produto, f.descricao, f.cod_barra, f.valor, f.status, to_char(f.data, 'DD/mm/YYYY') as vencimento,to_char(f.data_pagamento,'DD/mm/YYYY') as data_pgto, ";
						  $sSQL .= " cn.id_cliente_produto, cn.id_cliente, ";
						  $sSQL .= " cl.id_cliente, cl.nome_razao ";
						  $sSQL .= "FROM ";
						  $sSQL .= " cbtb_faturas f, cntb_conta cn, cltb_cliente cl ";
						  $sSQL .= "WHERE ";
						  $sSQL .= " f.cod_barra = '".$registros[$i]["codigo_barras"]."' AND ";
						  $sSQL .= " f.id_cliente_produto = cn.id_cliente_produto AND ";
						  $sSQL .= " cn.id_cliente = cl.id_cliente ";
						  $_faturas = $this->bd->obtemUnicoRegistro($sSQL);
						  
						  //echo "SELEÇÃO: $sSQL <br>";
						  
						  if ($_faturas && $_faturas["nome_razao"] != ""){
						  	//echo $_faturas["nome_razao"]."<br>";
						  	$qtde = $qtde + 1;
						  	$_status = "P";
						  	$motivo = "";
						  
						  }else{
						  	$_status = "S";
						  	$motivo = "Sem correspondente em Faturas";
						  }
						  //$qtde_validos = count($_faturas);
						  
						  $registros[$i] = array_merge($registros[$i],$_faturas);
						  //echo "FATURAS: $sSQL <br>";
						  
						  
						  $dt_pgto = list($dia,$mes,$ano) = explode("/",$registros[$i]["data_pagamento"]);
						  $dt_pgto = $ano."-".$mes."-".$dia;
						  
						  $dt_crdt = list($dia,$mes,$ano) = explode("/",$registros[$i]["data_credito"]);
						  $dt_crdt = $ano."-".$mes."-".$dia;
						  
						  $vlr = str_replace(",",".",$registros[$i]["valor_recebido"]);
						  $vlr_tarifa = str_replace(",",".",$registros[$i]["valor_tarifa"]);
						  
						  						  
						  $sSQL  = "INSERT INTO lgtb_retorno_faturas ";
						  $sSQL .= "(nsr,data_pagamento,data_credito,valor_recebido,codigo_barras,valor_tarifa,status,id_arquivo,motivo) ";
						  $sSQL .= "VALUES ( ";
						  $sSQL .= " '".$registros[$i]["nsr"]."',";
						  $sSQL .= " '$dt_pgto',";
						  $sSQL .= " '$dt_crdt',";
						  $sSQL .= " '$vlr',";
						  $sSQL .= " '".$registros[$i]["codigo_barras"]."', ";
						  $sSQL .= " '$vlr_tarifa',";
						  $sSQL .= " '$_status', ";
						  $sSQL .= " currval('lgtb_retorno_id_arquivo_seq'), ";
						  $sSQL .= " '$motivo' ";
						  $sSQL .= ")";
						  $this->bd->consulta($sSQL);
						  
						  //echo "FATURAS: $sSQL <br>";
						  
						  
						  
						  
						  
						  //echo $registros[$i]["nsr"] . " - " . $registros[$i]["data_pagamento"] . " - " . $registros[$i]["data_credito"] . " - " . $registros[$i]["valor_recebido"] . " - " . $registros[$i]["valor_tarifa"] . " - ".$registros[$i]["codigo_barras"] . "<br>";
						  
						}
					   
					   $qtde_validos = $qtde;
					   $qtde_sem = $i - $qtde_validos;
					   
					   //echo "QTDE VALIDOS: $qtde_validos<br>";
					   //echo "QTDE INVALIDOS: $qtde_sem<br>";
					   
					   if ($i == $qtde){
					   	$status = "S";
					   }else{
					   	$status = "P";
					   }
					   
					   
					   $sSQL  = "UPDATE lgtb_retorno SET qtde_registros = '$i', NRA='$i', NRSC='$qtde_sem', NRPE='$qtde_validos', status = '$status' WHERE id_arquivo = currval('lgtb_retorno_id_arquivo_seq')";
					   $this->bd->consulta($sSQL);
					   
					   //echo $sSQL ."<br>";
					
						
						
						$this->tpl->atribui("erro",$sErro);
						$this->tpl->atribui("registros",$registros);
						$this->tpl->atribui("arquivo",$arquivo["name"]);
						$this->arquivoTemplate = "cobranca_retorno_registros.html";

				   }

			   } else {
					   $sErro = "Formato desconhecido";
		   	   }
		   }
	   }
	   
	   if ($acao == "amortiza"){
	   
	   	$total = @$_REQUEST["total"];
			
			while(list($i,$lixo)=each($_REQUEST["nsr"])){


					
				
					$valor_recebido = str_replace(",",".",$_REQUEST["valor_recebido"][$i]);
					$data_pagamento = $_REQUEST["data_pagamento"][$i];
					$codigo_barras = $_REQUEST["codigo_barras"][$i];
					$dt = list($dia,$mes,$ano) = explode("/",$data_pagamento);
					$data_pagamento = $ano."-".$mes."-".$dia;
					
					//echo " I: $i <br>";


					$sSQL  = "SELECT ";
					$sSQL .= " f.id_cliente_produto, f.descricao, f.cod_barra, f.valor, f.status, to_char(f.data, 'DD/mm/YYYY') as vencimento,f.status,to_char(f.data_pagamento,'DD/mm/YYYY') as data_pgto, ";
					$sSQL .= " cn.id_cliente_produto, cn.id_cliente, ";
					$sSQL .= " cl.id_cliente, cl.nome_razao ";
					$sSQL .= "FROM ";
					$sSQL .= " cbtb_faturas f, cntb_conta cn, cltb_cliente cl ";
					$sSQL .= "WHERE ";
					$sSQL .= " f.cod_barra = '$codigo_barras' AND ";
					$sSQL .= " f.id_cliente_produto = cn.id_cliente_produto AND ";
					$sSQL .= " cn.id_cliente = cl.id_cliente ";
					$_faturas = $this->bd->obtemUnicoRegistro($sSQL);

					//echo "FATURAS: $sSQL <br>";

					if ($valor_recebido > $_faturas["valor"]){

						$acrescimo = $valor_recebido - $_faturas["valor"];
						$valor_pago = $valor_recebido;
						$desconto = "0.00";

					}else if ($valor_recebido < $_faturas["valor"]){

						$desconto = $_faturas["valor"] - $valor_recebido;
						$valor_pago = $valor_recebido;
						$acrescimo = "0.00";

					}else if ($valor_recebido == $_faturas["valor"]){

						$valor_pago = $valor_recebido;
						$desconto = "0.00";
						$acrescimo = "0.00";

					}

					//echo "VALOR RECEBIDO: $valor_recebido <br>";
					//echo "VALOR FATURA: ".$_faturas["valor"]."<br>";

					$sSQL  = "UPDATE lgtb_retorno_faturas SET status = 'A' WHERE codigo_barras = '$codigo_barras'";
					$this->bd->consulta($sSQL);
					

					$sSQL  = "UPDATE cbtb_faturas SET ";
					$sSQL .= "valor_pago = '$valor_pago', ";
					$sSQL .= "data_pagamento = '$data_pagamento', ";
					$sSQL .= "desconto = '$desconto', ";
					$sSQL .= "acrescimo = '$acrescimo', ";
					$sSQL .= "status = 'P' ";
					$sSQL .= "WHERE cod_barra = '$codigo_barras' ";

					$this->bd->consulta($sSQL);
					//echo "AMORT: $sSQL <br>";
					
					$sSQL  = "UPDATE lgtb_retorno_faturas SET ";
					$sSQL .= "status = 'A', ";
					$sSQL .= "motivo = 'Atualizado com sucesso' ";
					$sSQL .= "WHERE codigo_barras = '$codigo_barras' ";
					$this->bd->consulta($sSQL);
					
					$sSQL  = "UPDATE lgtb_retorno_faturas SET ";
					$sSQL .= "status = 'D', ";
					$sSQL .= "motivo = 'Desmarcado pelo operador' ";
					$sSQL .= "WHERE status = 'P' ";
					$this->bd->consulta($sSQL);					
					

			}
			
		$msg_final = "Retornos registrados com sucesso.";
		$this->tpl->atribui("mensagem",$msg_final); 
		$this->tpl->atribui("url", "cobranca.php?op=retornos");
		$this->tpl->atribui("target","_self");

		$this->arquivoTemplate="msgredirect.html";

		return;

		}else if ($acao == "detalhe"){
	
			$id_arquivo = @$_REQUEST["id_arquivo"];
		
			$sSQL  = "SELECT ";
			$sSQL .= "id_arquivo, nsr, to_char(data_pagamento,'DD/MM/YYYY') as data_pagamento, to_char(data_credito,'DD/MM/YYYY') as data_credito, to_char(valor_recebido, '999D99') as valor, status, motivo, codigo_barras ";
			$sSQL .= "FROM ";
			$sSQL .= "lgtb_retorno_faturas ";
			$sSQL .= "WHERE ";
			$sSQL .= "id_arquivo = '$id_arquivo' ";
			$sSQL .= "ORDER BY nsr ASC";
		
			$detalhe = $this->bd->obtemRegistros($sSQL);
		
			$this->tpl->atribui("detalhe",$detalhe);
			
			$this->arquivoTemplate = "cobranca_retorno_detalhe.html";
			return;

		}



	   //echo $sErro . "<br>\n";
	   $this->tpl->atribui("sop",@$_REQUEST["sop"]);
	   //$this->tpl->atribui("erro",$sErro);

	
	
	   /*global $_LS_FORMATOS_PAG;
	   $this->tpl->atribui("ls_formatos",$_LS_FORMATOS_PAG);

	
	   $this->arquivoTemplate = "cobranca_retorno.html";*/
	
	}else if ($op == "contratos"){
	
		$acao = @$_REQUEST["acao"];
		$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
		$id_cliente = @$_REQUEST["id_cliente"];
		$tipo_produto = @$_REQUEST["tipo_produto"];
		$rotina = @$_REQUEST["rotina"];
		$dominio = @$_REQUEST["dominio"];
		$tipo_conta = @$_REQUEST["tipo_conta"];
		$username = @$_REQUEST["username"];
		
		$this->tpl->atribui("id_cliente_produto",$id_cliente_produto);
		$this->tpl->atribui("id_cliente",$id_cliente);
		$this->tpl->atribui("tipo_produto",$tipo_produto);
		
		
		$this->obtemPR($id_cliente);
		
		
		
		$sSQL  = "SELECT ";
		//$sSQL .= "   id_produto,nome,descricao,tipo,valor ";
		$sSQL .= " * ";
		$sSQL .= "FROM ";
		$sSQL .= "   prtb_produto ";
		$sSQL .= "WHERE ";
		$sSQL .= "   disponivel is true ";
		$sSQL .= "";
		
		$cond_discado    = "   AND tipo='D' ";
		$cond_bandalarga = "   AND tipo='BL' ";
		$cond_hospedagem = "   AND tipo='H' ";
		
		$ordem = "ORDER BY nome ";
		
		$lista_discado    = $this->bd->obtemRegistros("$sSQL $cond_discado $ordem");
		$lista_bandalarga = $this->bd->obtemRegistros("$sSQL $cond_bandalarga $ordem");
		$lista_hospedagem = $this->bd->obtemRegistros("$sSQL $cond_hospedagem $ordem");

		
		if ($acao == "cancelar"){
			
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$id_cliente = @$_REQUEST["id_cliente"];
			$tipo_produto = @$_REQUEST["tipo_produto"];
			$p = @$_REQUEST["p"];
			
			//$sSQL  = "SELECT ";
			//$sSQL .= "* FROM cbtb_contrato where id_cliente_produto = '$id_cliente_produto' AND tipo_produto = '$tipo_produto' ";
			
			$sSQL  = "SELECT ";
			$sSQL .= "ct.id_cliente_produto, to_char(ct.data_contratacao, 'DD/mm/YYYY') as data_contratacao, ct.vigencia, ct.data_renovacao, ct.valor_contrato, ct.id_cobranca, ct.status, ct.tipo_produto, ";
			$sSQL .= "ct.valor_produto,";
			$sSQL .= "cn.id_cliente_produto, cn.id_cliente, cn.dominio, cn.tipo_conta, cn.username, ";
			$sSQL .= "cl.id_cliente, cl.nome_razao ";
			$sSQL .= "FROM ";
			$sSQL .= "cbtb_contrato ct, cntb_conta cn, cltb_cliente cl ";
			$sSQL .= "WHERE ";
			$sSQL .= "ct.id_cliente_produto = '$id_cliente_produto' AND ";
			$sSQL .= "ct.id_cliente_produto = cn.id_cliente_produto AND ";
			$sSQL .= "cn.id_cliente = '$id_cliente' AND ";
			$sSQL .= "cn.id_cliente = cl.id_cliente AND ";
			$sSQL .= "ct.tipo_produto = '$tipo_produto' ";
			
			
			
			$contrato = $this->bd->obtemUnicoRegistro($sSQL);
			//echo "CONTRATO: $sSQL <br>";
			
				
			
			$this->tpl->atribui("contrato",$contrato);
			
			$sSQL  = "SELECT * FROM cbtb_faturas where id_cliente_produto = '$id_cliente_produto' ";
			
			$faturas = $this->bd->obtemRegistros($sSQL);
			//echo "FATURA: $sSQL <br>";
			
			$sSQL  = "SELECT ";
				switch ($tipo_produto){
					case 'BL':
						$sSQL .= "bl.username, bl.tipo_conta, bl.dominio, bl.id_pop, bl.tipo_bandalarga, bl.ipaddr, bl.rede, bl.upload_kbps, bl.download_kbps, bl.status, bl.mac, bl.id_nas, ";
						$sSQL .= "pop.id_pop, pop.nome as nome_pop, pop.info, pop.tipo, pop.id_pop_ap, nas.id_nas, nas.nome as nome_nas, nas.ip, nas.secret, nas.tipo_nas ";
						$sSQL .= "FROM cntb_conta_bandalarga bl, cftb_pop pop, cftb_nas nas ";
						$sSQL .= "WHERE ";
						$sSQL .= "username = '".$contrato["username"]."' AND ";
						$sSQL .= "tipo_conta = '".$contrato["tipo_conta"]."' AND ";
						$sSQL .= "dominio = '".$contrato["dominio"]."' AND ";
						$sSQL .= "bl.id_nas = nas.id_nas AND ";
						$sSQL .= "bl.id_pop = pop.id_pop ";
					break;
					case 'D':
						$sSQL .= "username, tipo_conta, dominio, foneinfo ";
						$sSQL .= "FROM cntb_conta_discado ";
						$sSQL .= "WHERE ";
						$sSQL .= "username = '".$contrato["username"]."' AND ";
						$sSQL .= "tipo_conta = '".$contrato["tipo_conta"]."' AND ";
						$sSQL .= "dominio = '".$contrato["dominio"]."' ";
					break;
					case 'H':
						$sSQL .= "username, tipo_conta, dominio, tipo_hospedagem, uid, gid ";
						$sSQL .= "FROM cntb_conta_hospedagem ";
						$sSQL .= "WHERE ";
						$sSQL .= "username = '".$contrato["username"]."' AND ";
						$sSQL .= "tipo_conta = '".$contrato["tipo_conta"]."' AND ";
						$sSQL .= "dominio = '".$contrato["dominio"]."' ";
					break;
				}

			$produto_carac = $this->bd->obtemUnicoRegistro($sSQL);		
			//echo "PRODUTO CARACT: $sSQL <br>";
			
			$sSQL  = "SELECT cp.id_cliente_produto, cp.id_cliente, cp.id_produto, pr.id_produto, pr.nome as nome_produto ";
			$sSQL .= "FROM cbtb_cliente_produto cp, prtb_produto pr ";
			$sSQL .= "WHERE ";
			$sSQL .= "cp.id_cliente_produto = '$id_cliente_produto' AND ";
			$sSQL .= "cp.id_cliente = '$id_cliente' AND ";
			$sSQL .= "cp.id_produto = pr.id_produto ";
			
			$produto = $this->bd->obtemUnicoRegistro($sSQL);
			//echo "PRODUTO: $sSQL <br>";
			
			$this->tpl->atribui("produto",$produto);
			$this->tpl->atribui("produto_carac",$produto_carac);
			$this->tpl->atribui("faturas",$faturas);
			$this->tpl->atribui("acao",$acao);
			$this->tpl->atribui("id_cliente",$id_cliente);
			$this->tpl->atribui("id_cliente_produto",$id_cliente_produto);
			$this->tpl->atribui("tipo_produto",$tipo_produto);
			
			
			
			if (!$p || $p != "ok"){
			$this->arquivoTemplate = "cliente_contrato_cancel.html";
			return;
			}else if ($p == "ok"){
			
				if ($tipo_produto == "BL"){
					
					/* SPOOL */
					
					$sSQL  = "SELECT ";
					$sSQL .= "	bl.username, bl.tipo_conta, bl.dominio, bl.tipo_bandalarga, bl.ipaddr, bl.rede, bl.id_nas, ";
					$sSQL .= "	cn.username, cn.dominio, cn.tipo_conta, cn.id_conta ";
					$sSQL .= "FROM cntb_conta_bandalarga bl, cntb_conta cn ";
					$sSQL .= "WHERE ";
					$sSQL .= "bl.username = '".$contrato["username"]."' AND bl.tipo_conta = '$tipo_produto' AND bl.dominio = '".$contrato["dominio"]."' AND ";
					$sSQL .= "bl.username = cn.username AND bl.tipo_conta = cn.tipo_conta AND bl.dominio = cn.dominio ";
					$bl = $this->bd->obtemUnicoRegistro($sSQL);
			//		echo "SPOOL BL: $sSQL <br>";
					
					$sSQL  = "SELECT ip FROM cftb_nas WHERE id_nas = '".$bl["id_nas"]."' ";
					$nas = $this->bd->obtemUnicoRegistro($sSQL);
			//		echo "SPOOL NAS: $sSQL <br>";
					
					
					
					if ($bl["tipo_bandalarga"] == "P"){
						
			//			ECHO "PPPOE<BR>";
						$this->spool->bandalargaExcluiRedePPPoE($nas["ip"],$bl["id_conta"],$bl["ipaddr"]);
					
					}else {
						
			//			echo "IP <BR>";
						$this->spool->bandalargaExcluiRede($nas["ip"],$bl["id_conta"],$bl["rede"]);
					
					}
					
				/* FINAL SPOOL */

				}
				
			
				$sSQL = "UPDATE cbtb_contrato SET status = 'C' where id_cliente_produto = '$id_cliente_produto' ";
				$this->bd->consulta($sSQL);
			//	echo "UPDATE CANCELAR1: $sSQL <br>";
				
				$sSQL = "UPDATE cbtb_faturas SET status = 'C' where id_cliente_produto = '$id_cliente_produto' ";
				$this->bd->consulta($sSQL);
			//	echo "UPDATE CANCELAR2: $sSQL <br>";
				
				$sSQL = "UPDATE cntb_conta SET status = 'C' where username = '".$contrato["username"]."' AND tipo_conta = '".$contrato["tipo_produto"]."' AND dominio = '".$contrato["dominio"]."' ";
				$this->bd->consulta($sSQL);
			//	echo "UPDATE CANCELAR3: $sSQL <br>";
			
				$sSQL = "UPDATE cbtb_carne SET status = 'C' where id_carne = '".$faturas[0]["id_carne"]."' ";
				$this->bd->consulta($sSQL);
				//echo "UPDATE CARNE: $sSQL <br>";
				
				$sSQL  = "UPDATE "; 
				
						 switch ($tipo_produto){
						 	case 'BL':
						 	$sSQL .= "cntb_conta_bandalarga ";
						 	break;
						 	case 'D':
						 	$sSQL .= "cntb_conta_discado ";
						 	break;
						 	case 'H':
						 	$sSQL .= "cntb_conta_hospedagem ";
						 	break;						 	
						 
						 }
						 
				$sSQL .= "SET status = 'C' where username = '".$contrato["username"]."' AND tipo_conta = '".$contrato["tipo_produto"]."' AND dominio = '".$contrato["dominio"]."' ";
				$this->bd->consulta($sSQL);
			//	echo "UPDATE CANCELAR4: $sSQL <br>";
				
				$msg_final = "CONTRATOS CANCELADOS COM SUCESSO!<BR>FATURAS CANCELADAS COM SUCESSO!<BR>CONTAS CANCELADAS COM SUCESSO!<br>CARNE CANCELADO COM SUCESSO!";
				$this->tpl->atribui("mensagem",$msg_final);
				$this->tpl->atribui("url", "clientes.php?op=cobranca&id_cliente=".$id_cliente."&rotina=resumo");
				$this->tpl->atribui("target","_self");
				$this->arquivoTemplate="msgredirect.html";
				return;

			
			
			}
			
		}else if ($acao == "alterar"){
		


		
		}else if ($acao == "excluir"){
		
		
			if (!$rotina){
			
				$sSQL  = "SELECT ";
				$sSQL .= "ct.id_cliente_produto, to_char(ct.data_contratacao, 'DD/mm/YYYY') as data_contratacao, ct.vigencia, ct.data_renovacao, ct.valor_contrato, ct.id_cobranca, ct.status, ct.tipo_produto, ";
				$sSQL .= "ct.valor_produto,";
				$sSQL .= "cn.id_cliente_produto, cn.id_cliente, cn.dominio, cn.tipo_conta, cn.username,cn.id_conta, ";
				$sSQL .= "cl.id_cliente, cl.nome_razao ";
				$sSQL .= "FROM ";
				$sSQL .= "cbtb_contrato ct, cntb_conta cn, cltb_cliente cl ";
				$sSQL .= "WHERE ";
				$sSQL .= "ct.id_cliente_produto = '$id_cliente_produto' AND ";
				$sSQL .= "ct.id_cliente_produto = cn.id_cliente_produto AND ";
				$sSQL .= "cn.id_cliente = '$id_cliente' AND ";
				$sSQL .= "cn.id_cliente = cl.id_cliente AND ";
				$sSQL .= "ct.tipo_produto = '$tipo_produto' ";

				//echo "QUERY: $sSQL <br>";

				$contrato = $this->bd->obtemUnicoRegistro($sSQL);

				$this->tpl->atribui("contrato",$contrato);

				$sSQL  = "SELECT to_char(data, 'DD/mm/YYYY') as data, valor, status, id_carne FROM cbtb_faturas where id_cliente_produto = '$id_cliente_produto' ";
				//echo "fatura: $sSQL <br>";
				$faturas = $this->bd->obtemRegistros($sSQL);

				$this->tpl->atribui("fatura",$faturas);
				$this->tpl->atribui("acao",$acao);

				$this->arquivoTemplate = "cliente_contrato_excluir.html";
				return;
				
			}else if ( $rotina == "excluir" ){
				
				if ($tipo_produto == "BL"){
					
					/* SPOOL */
					
					$sSQL  = "SELECT ";
					$sSQL .= "	bl.username, bl.tipo_conta, bl.dominio, bl.tipo_bandalarga, bl.ipaddr, bl.rede, bl.id_nas, ";
					$sSQL .= "	cn.username, cn.dominio, cn.tipo_conta, cn.id_conta ";
					$sSQL .= "FROM cntb_conta_bandalarga bl, cntb_conta cn ";
					$sSQL .= "WHERE ";
					$sSQL .= "bl.username = '$username' AND bl.tipo_conta = '$tipo_conta' AND bl.dominio = '$dominio' AND ";
					$sSQL .= "bl.username = cn.username AND bl.tipo_conta = cn.tipo_conta AND bl.dominio = cn.dominio ";
					$bl = $this->bd->obtemUnicoRegistro($sSQL);
					//echo "SPOOL BL: $sSQL <br>";
					
					$sSQL  = "SELECT ip, id_nas FROM cftb_nas WHERE id_nas = '".$bl["id_nas"]."' ";
					$nas = $this->bd->obtemUnicoRegistro($sSQL);
					//echo "SPOOL NAS: $sSQL <br>";
					
					
					
					if ($bl["tipo_bandalarga"] == "P"){
						
						//ECHO "PPPOE<BR>";
						$this->spool->bandalargaExcluiRedePPPoE($nas["id_nas"],$bl["id_conta"],$bl["ipaddr"]);
					
					}else {
						
						//echo "IP <BR>";
						$this->spool->bandalargaExcluiRede($nas["id_nas"],$bl["id_conta"],$bl["rede"]);
					
					}
					
				/* FINAL SPOOL */

				}
				
				$sSQL  = "SELECT ";
				$sSQL .= "ct.id_cliente_produto, to_char(ct.data_contratacao, 'DD/mm/YYYY') as data_contratacao, ct.vigencia, ct.data_renovacao, ct.valor_contrato, ct.id_cobranca, ct.status, ct.tipo_produto, ";
				$sSQL .= "ct.valor_produto,";
				$sSQL .= "cn.id_cliente_produto, cn.id_cliente, cn.dominio, cn.tipo_conta, cn.username,cn.id_conta, ";
				$sSQL .= "cl.id_cliente, cl.nome_razao ";
				$sSQL .= "FROM ";
				$sSQL .= "cbtb_contrato ct, cntb_conta cn, cltb_cliente cl ";
				$sSQL .= "WHERE ";
				$sSQL .= "ct.id_cliente_produto = '$id_cliente_produto' AND ";
				$sSQL .= "ct.id_cliente_produto = cn.id_cliente_produto AND ";
				$sSQL .= "cn.id_cliente = '$id_cliente' AND ";
				$sSQL .= "cn.id_cliente = cl.id_cliente AND ";
				$sSQL .= "ct.tipo_produto = '$tipo_produto' ";
				$CONTR = $this->bd->obtemUnicoRegistro($sSQL);
				
				$username = $CONTR["username"];
				$dominio = $CONTR["dominio"];
				$tipo_conta = $CONTR["tipo_conta"];
				$id_conta = $CONTR["id_conta"];
				
				$sSQL  = "SELECT ";
				$sSQL .= " cn.senha, cn.conta_mestre, cn.observacoes, ";

				switch ($tipo_conta){
					
					case 'BL':
						$sSQL .= "bl.id_pop, bl.tipo_bandalarga, bl.ipaddr, bl.rede, bl.upload_kbps, bl.download_kbps, bl.status, bl.mac, bl.id_nas, bl.ip_externo ";
						$sSQL .= "FROM cntb_conta_bandalarga bl, cntb_conta cn WHERE ";
						$sSQL .= "bl.username = cn.username AND ";
						$sSQL .= "bl.dominio = cn.dominio AND ";
						$sSQL .= "bl.tipo_conta = cn.tipo_conta ";
					break;
					case 'D':
						$sSQL .= "d.foneinfo ";
						$sSQL .= "FROM cntb_conta_discado d, cntb_conta cn WHERE ";
						$sSQL .= "d.username = cn.username AND ";
						$sSQL .= "d.dominio = cn.dominio AND ";
						$sSQL .= "d.tipo_conta = cn.tipo_conta ";
					break;
					case 'E':
						$sSQL .= "e.quota, e.email ";
						$sSQL .= "FROM cntb_conta_email e, cntb_conta cn WHERE ";
						$sSQL .= "e.username = cn.username AND ";
						$sSQL .= "e.dominio = cn.dominio AND ";
						$sSQL .= "e.tipo_conta = cn.tipo_conta ";
					break;
					case 'H':
						$sSQL .= "h.tipo_hospedagem, h.senha_cript, h.uid, h.gid, h.home, h.shell, h.dominio_hospedagem ";
						$sSQL .= "FROM cntb_conta_hospedagem h, cntb_conta cn WHERE ";
						$sSQL .= "h.username = cn.username AND ";
						$sSQL .= "h.dominio = cn.dominio AND ";
						$sSQL .= "h.tipo_conta = cn.tipo_conta ";
					break;
				}
				
				$sSQL .= "AND ";
				$sSQL .= "cn.username = '$username' AND ";
				$sSQL .= "cn.dominio = '$dominio' AND ";
				$sSQL .= "cn.tipo_conta = '$tipo_conta' ";

				$outros = $this->bd->obtemUnicoRegistro($sSQL);
				//echo "OUTROS: $sSQL <br>";
				
				$id_pop = @$outros["id_pop"];
				$tipo_bandalarga = @$outros["tipo_bandalarga"];
				$ipaddr = @$outros["ipaddr"];
				$rede = @$outros["rede"];
				$upload_kbps = @$outros["upload_kbps"];
				$download_kbps = @$outros["download_kbps"];
				$status = @$outros["status"];
				$mac = @$outros["mac"];
				$id_nas = @$outros["id_nas"];
				$ip_externo = @$outros["ip_externo"];
				$quota = @$outros["quota"];
				$email = @$outros["email"];
				$foneinfo = @$outros["foneinfo"];
				$tipo_hospedagem = @$outros["tipo_hospedagem"];
				$senha_cript = @$outros["senha_cript"];
				$uid = @$outros["uid"];
				$gid = @$outros["gid"];
				$home = @$outros["home"];
				$shell = @$outros["shell"];
				$dominio_hospedagem = @$outros["dominio_hospedagem"];
				$senha = @$outros["senha"];
				$conta_mestre = @$outros["conta_mestre"];
				$observacoes = @$outros["observacoes"];
				$admin = $this->admLogin->obtemAdmin();
				
				
				$sSQL = "SELECT id_carne FROM cbtb_faturas WHERE id_cliente_produto = $id_cliente_produto GROUP BY id_carne";
				$fat = $this->bd->obtemUnicoRegistro($sSQL);
				//echo "ID_CARNE: $sSQL <br>";

				$sSQL  = "INSERT INTO lgtb_contas_excluidas ";
				$sSQL .= "(id_cliente, id_cliente_produto, id_conta, username, tipo_conta, dominio, id_pop, tipo_bandalarga, ipaddr, rede, upload_kbps, ";
				$sSQL .= "download_kbps, status, mac, id_nas, ip_externo, quota, email, foneinfo, tipo_hospedagem, senha_cript, uid, gid, home, shell, ";
				$sSQL .= "dominio_hospedagem, senha, conta_mestre, observacoes, admin) ";
  			$sSQL .= "VALUES ";
  			$sSQL .= "('$id_cliente','$id_cliente_produto','$id_conta','$username','$tipo_conta','$dominio','$id_pop','$tipo_bandalarga','$ipaddr','$rede','$upload_kbps', ";
  			$sSQL .= "'$download_kbps','$status','$mac','$id_nas','$ip_externo','$quota','$email','$foneinfo','$tipo_hospedagem','$senha_cript','$uid','$gid','$home','$shell', ";
  			$sSQL .= "'$dominio_hospedagem','$senha','$conta_mestre','$observacoes','$admin' ) ";
  			$this->bd->consulta($sSQL);
				//echo "INSERT LOG: $sSQL <br>";

			
				$sSQL  = "DELETE FROM cbtb_faturas WHERE id_cliente_produto = '$id_cliente_produto'";
				$this->bd->consulta($sSQL);

				$aSQL = "DELETE FROM cbtb_carne WHERE id_carne = '".$fat["id_carne"]."' ";
				$this->bd->consulta($aSQL);
				//echo "DELETAO: $aSQL <br>";
				
				$sSQL  = "DELETE FROM cbtb_contrato WHERE id_cliente_produto = '$id_cliente_produto' AND tipo_produto = '$tipo_produto' ";
				$this->bd->consulta($sSQL);
				//echo "DELETA CONTRATO: $sSQL <br>";
				
				
				$sSQL  = "DELETE FROM ";
					switch($tipo_conta){
						case 'BL':
						$sSQL .= "cntb_conta_bandalarga ";
						break;
						case 'D':
						$sSQL .= "cntb_conta_discado ";
						break;
						case 'H':
						$sSQL .= "cntb_conta_hospedagem ";
						break;
					}
				$sSQL .= "WHERE ";
				$sSQL .= "dominio = '$dominio' AND ";
				$sSQL .= "username = '$username' AND ";
				$sSQL .= "tipo_conta = '$tipo_conta' ";
				$this->bd->consulta($sSQL);
				//echo "DELETA CONTA ESPECIFICA: $sSQL <br>";
				
				$sSQL = "DELETE FROM cntb_conta WHERE username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta' ";
				$this->bd->consulta($sSQL);
				//echo "DELETA CONTAS: $sSQL <br>";
				
				$sSQL = "DELETE FROM cbtb_cliente_produto WHERE id_cliente_produto = '$id_cliente_produto' ";
				$this->bd->consulta($sSQL);
				
				//echo "DELETA CLIENTE_PRODUTO: $sSQL <br>";
				
				
				
				$msg_final = "CONTRATOS EXCLUIDOS COM SUCESSO!<BR>FATURAS EXCLUIDAS COM SUCESSO!<br>CONTAS EXCLUIDAS COM SUCESSO!<BR> ";
				$this->tpl->atribui("mensagem",$msg_final);
				$this->tpl->atribui("url", "clientes.php?op=cobranca&id_cliente=".$id_cliente."&rotina=resumo");
				$this->tpl->atribui("target","_self");
				$this->arquivoTemplate="msgredirect.html";
				return;
			
				
			
			
			}
		} else if ($acao == "migrar"){
		
			$rotina = @$_REQUEST["rotina"];
			$tipo = @$_REQUEST["tipo"];
			$p = @$_REQUEST["p"];
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$id_cliente = @$_REQUEST["id_cliente"];
			
			if (!$tipo_produto && $tipo){
				$tipo_produto = $tipo;
			}
			
			if (!$p || $p != "ok"){
			
				$sSQL  = "SELECT ";
				$sSQL .= "ct.id_cliente_produto, to_char(ct.data_contratacao, 'DD/mm/YYYY') as data_contratacao, ct.vigencia, ct.data_renovacao, ct.valor_contrato, ct.id_cobranca, ct.status, ct.tipo_produto,ct.id_produto, ";
				$sSQL .= "ct.vigencia, ct.carencia, ct.vencimento, ";
				$sSQL .= "ct.valor_produto,";
				$sSQL .= "cn.id_cliente_produto, cn.id_cliente, cn.dominio, cn.tipo_conta, cn.username, ";
				$sSQL .= "cl.id_cliente, cl.nome_razao ";
				$sSQL .= "FROM ";
				$sSQL .= "cbtb_contrato ct, cntb_conta cn, cltb_cliente cl ";
				$sSQL .= "WHERE ";
				$sSQL .= "ct.id_cliente_produto = '$id_cliente_produto' AND ";
				$sSQL .= "ct.id_cliente_produto = cn.id_cliente_produto AND ";
				$sSQL .= "cn.id_cliente = '$id_cliente' AND ";
				$sSQL .= "cn.id_cliente = cl.id_cliente AND ";
				$sSQL .= "ct.tipo_produto = '$tipo_produto' ";

				//echo "QUERY: $sSQL <br>";

				$contrato = $this->bd->obtemUnicoRegistro($sSQL);

				$sSQL = "SELECT * FROM prtb_produto WHERE tipo = '$tipo_produto' AND disponivel = 't' ";
				$produto = $this->bd->obtemRegistros($sSQL);
				//echo "PRODUTO: $sSQL <br>";
			}else{

				$sSQL  = "SELECT ";
				$sSQL .= "ct.id_cliente_produto, to_char(ct.data_contratacao, 'DD/mm/YYYY') as data_contratacao, ct.vigencia, ct.data_renovacao, ct.valor_contrato, ct.id_cobranca, ct.status, ct.tipo_produto,ct.id_produto, ";
				$sSQL .= "ct.vigencia, ct.carencia, ct.vencimento, ";
				$sSQL .= "ct.valor_produto,";
				$sSQL .= "cn.id_cliente_produto, cn.id_cliente, cn.dominio, cn.tipo_conta, cn.username, ";
				$sSQL .= "cl.id_cliente, cl.nome_razao ";
				$sSQL .= "FROM ";
				$sSQL .= "cbtb_contrato ct, cntb_conta cn, cltb_cliente cl ";
				$sSQL .= "WHERE ";
				$sSQL .= "ct.id_cliente_produto = '$id_cliente_produto' AND ";
				$sSQL .= "ct.id_cliente_produto = cn.id_cliente_produto AND ";
				$sSQL .= "cn.id_cliente = '$id_cliente' AND ";
				$sSQL .= "cn.id_cliente = cl.id_cliente AND ";
				$sSQL .= "ct.tipo_produto = '$tipo_produto' ";

				//echo "QUERY: $sSQL <br>";

				$contrato = $this->bd->obtemUnicoRegistro($sSQL);

				$sSQL = "SELECT * FROM prtb_produto WHERE tipo = '$tipo_produto' AND disponivel = 't' ";
				$produto = $this->bd->obtemRegistros($sSQL);
				//echo "PRODUTO: $sSQL <br>";

			
			
			}	
			
			
			$sSQL  = "SELECT * FROM cftb_forma_pagamento WHERE disponivel = TRUE ";
			$tipo_cobranca = $this->bd->obtemRegistros($sSQL);

			$sSQL  = "SELECT ";
				switch ($tipo_produto){
					case 'BL':
						$sSQL .= "bl.username, bl.tipo_conta, bl.dominio, bl.id_pop, bl.tipo_bandalarga, bl.ipaddr, bl.rede, bl.upload_kbps, bl.download_kbps, bl.status, bl.mac, bl.id_nas, ";
						$sSQL .= "pop.id_pop, pop.nome, pop.info, pop.tipo, pop.id_pop_ap, nas.id_nas, nas.nome, nas.ip, nas.secret, nas.tipo_nas ";
						$sSQL .= "FROM cntb_conta_bandalarga bl, cftb_pop pop, cftb_nas nas ";
						$sSQL .= "WHERE ";
						$sSQL .= "username = '".$contrato["username"]."' AND ";
						$sSQL .= "tipo_conta = '".$contrato["tipo_conta"]."' AND ";
						$sSQL .= "dominio = '".$contrato["dominio"]."' AND ";
						$sSQL .= "bl.id_nas = nas.id_nas AND ";
						$sSQL .= "bl.id_pop = pop.id_pop ";
					break;
					case 'D':
						$sSQL .= "username, tipo_conta, dominio, foneinfo ";
						$sSQL .= "FROM cntb_conta_discado ";
						$sSQL .= "WHERE ";
						$sSQL .= "username = '".$contrato["username"]."' AND ";
						$sSQL .= "tipo_conta = '".$contrato["tipo_conta"]."' AND ";
						$sSQL .= "dominio = '".$contrato["dominio"]."' ";
					break;
					case 'H':
						$sSQL .= "username, tipo_conta, dominio, tipo_hospedagem, uid, gid ";
						$sSQL .= "FROM cntb_conta_hospedagem ";
						$sSQL .= "WHERE ";
						$sSQL .= "username = '".$contrato["username"]."' AND ";
						$sSQL .= "tipo_conta = '".$contrato["tipo_conta"]."' AND ";
						$sSQL .= "dominio = '".$contrato["dominio"]."' ";
					break;
				}

			$produto_carac = $this->bd->obtemUnicoRegistro($sSQL);

			//echo "CARACT. PRODUTOS: $sSQL <br>";

			$sSQL  = "SELECT * FROM cftb_pop ";
			$lista_pop = $this->bd->obtemRegistros($sSQL);

			$sSQL  = "SELECT * FROM cftb_nas ";
			$lista_nas = $this->bd->obtemRegistros($sSQL);

			$sSQL  = "SELECT * FROM prtb_produto WHERE id_produto = '".$contrato["id_produto"]."'";
			$produto_geral = $this->bd->obtemUnicoRegistro($sSQL);

		
			$provedor = $this->prefs->obtem("geral");
			$dominio = $provedor["dominio_padrao"];

			//echo "dominio: $dominio <br>";

			global $_LS_FORMA_PAGAMENTO;

			$this->tpl->atribui("forma_pagamento",$_LS_FORMA_PAGAMENTO);
			$this->tpl->atribui("lista_pop",$lista_pop);
			$this->tpl->atribui("lista_nas",$lista_nas);
			$this->tpl->atribui("produto_carac",$produto_carac);
			$this->tpl->atribui("tipo_cobranca",$tipo_cobranca);
			$this->tpl->atribui("contrato",$contrato);
			$this->tpl->atribui("produto",$produto);
			$this->tpl->atribui("lista_discado",$lista_discado);
			$this->tpl->atribui("lista_hospedagem",$lista_hospedagem);
			$this->tpl->atribui("lista_bandalarga",$lista_bandalarga);
			$this->tpl->atribui("produto_geral",$produto_geral);
			
							$this->arquivoTemplate = "cliente_contrato_migracao.html";


			if (!$rotina){
				
				$this->arquivoTemplate = "cliente_contrato_migracao.html";
				return;
				
				
			} else if ($rotina == "modificar"){
			
			
				//request das variaveis novas atribuidas
				
				$id_produto = @$_REQUEST["id_produto"];
				$tipo = @$_REQUEST["tipo"];
	
				//final do request

				if ($id_produto == $contrato["id_produto"]){
				
					//echo "NADA DIFERENTE";
					//$this->arquivoTemplate = "cliente_contrato_migracao_confirmacao.html";
					
					$msg_final = "NÃO HOUVE NENHUMA MODIFICAÇÃO NO CONTRATO!";
					$this->tpl->atribui("mensagem",$msg_final);
					$this->tpl->atribui("url", "clientes.php?op=cobranca&id_cliente=".$id_cliente."&rotina=resumo");
					$this->tpl->atribui("target","_self");
					$this->arquivoTemplate="msgredirect.html";
					return;


					
				} else {
				
					$tipo = @$_REQUEST["tipo"];
					$id_produto = @$_REQUEST["id_produto"];
					$email_igual = @$_REQUEST["email_igual"];
					$data_contratacao = @$_REQUEST["data_contratacao"];
					$vigencia = @$_REQUEST["vigencia"];
					$status = @$_REQUEST["status"];
					$dia_vencimento = @$_REQUEST["dia_vencimento"];
					$carencia_pagamento = @$_REQUEST["carencia_pagamento"];
					$desconto_promo = @$_REQUEST["desconto_promo"];
					$periodo_desconto = @$_REQUEST["periodo_desconto"];
					$tx_instalacao = @$_REQUEST["tx_instalacao"];
					$comodato = @$_REQUEST["comodato"];
					$valor_comodato = @$_REQUEST["valor_comodato"]; 
					$prorata = @$_REQUEST["prorata"];
					$tipo_cobranca = @$_REQUEST["tipo_cobranca"];
					$forma_pagamento = @$_REQUEST["forma_pagamento"];
					$ini_carne = @$_REQUEST["ini_carne"];
					$data_carne = @$_REQUEST["data_carne"];
					$username = @$_REQUEST["username"];
					$senha = @$_REQUEST["senha"];
					$conf_senha = @$_REQUEST["conf_senha"];
					$id_pop = $produto_carac["id_pop"];
					$id_nas = $produto_carac["id_nas"];
					$selecao_ip = @$_REQUEST["selecao_ip"];
					$mac = $produto_carac["mac"];
					$op = @$_REQUEST["op"];
					$id_cliente = @$_REQUEST["id_cliente"];
					$rotina = @$_REQUEST["rotina"];
					
					$provedor = $this->prefs->obtem("geral");
					$dominio = $provedor["dominio_padrao"];
					$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
					$acao = @$_REQUEST["acao"];
					$foneinfo = @$_REQUEST["foneinfo"];
					$tipo_hospedagem = @$_REQUEST["tipo_hospedagem"];
					$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];
					$pagamento = @$_REQUEST["pagamento"];
					$pri_venc = @$_REQUEST["pri_venc"];		
					
					$sSQL = "SELECT * FROM prtb_produto WHERE id_produto = '$id_produto'";
					$info_produto = $this->bd->obtemUnicoRegistro($sSQL);

					$sSQL = "SELECT nome_cobranca FROM cftb_forma_pagamento WHERE id_cobranca = '$tipo_cobranca'";
					$info_pagamento = $this->bd->obtemUnicoRegistro($sSQL);
					
					$valor_contrato = $info_produto["valor"];
					$valor_contrato += $valor_comodato;
					
					if ($periodo_desconto >= $vigencia)
						$valor_contrato -= $desconto_promo;
						
					//Informações referente ao cliente e ao produto
					//$this->tpl->atribui("info_cliente", $info_cliente);
					$this->tpl->atribui("info_produto", $info_produto);
					$this->tpl->atribui("info_pagamento", $info_pagamento);

					//Formatação de valores monetários
					$valor_comodato = number_format($valor_comodato, 2, '.', '');
					$desconto_promo = number_format($desconto_promo, 2, '.', '');
					$tx_instalacao = number_format($tx_instalacao, 2, '.', '');
					$valor_contrato = number_format($valor_contrato, 2, '.', '');

					//Informações de banco e cartão de crédito
					$cc_vencimento = @$_REQUEST["cc_vencimento"];
					$cc_numero = @$_REQUEST["cc_numero"];
					$cc_operadora = @$_REQUEST["cc_operadora"];
					$db_banco = @$_REQUEST["db_banco"];
					$db_agencia = @$_REQUEST["db_agencia"];
					$db_conta = @$_REQUEST["db_conta"];
				
				
					
				
					global $_LS_FORMA_PAGAMENTO;
					
					$this->tpl->atribui("tipo", $tipo );
					$this->tpl->atribui("id_produto_new", $id_produto);
					$this->tpl->atribui("email_igual_new", $email_igual);
					$this->tpl->atribui("data_contratacao_new", $data_contratacao);
					$this->tpl->atribui("vigencia_new", $vigencia );
					$this->tpl->atribui("status_new", $status );
					$this->tpl->atribui("dia_vencimento_new", $dia_vencimento);
					$this->tpl->atribui("carencia_pagamento_new", $carencia_pagamento);
					$this->tpl->atribui("desconto_promo_new", $desconto_promo );
					$this->tpl->atribui("periodo_desconto_new", $periodo_desconto);
					$this->tpl->atribui("tx_instalacao_new", $tx_instalacao);
					$this->tpl->atribui("comodato_new", $comodato);
					$this->tpl->atribui("valor_comodato_new", $valor_comodato);
					$this->tpl->atribui("prorata_new", $prorata);
					$this->tpl->atribui("tipo_cobranca_new", $tipo_cobranca);
					$this->tpl->atribui("forma_pagamento_new", $forma_pagamento );
					$this->tpl->atribui("ini_carne_new", $ini_carne );
					$this->tpl->atribui("data_carne_new", $data_carne );
					$this->tpl->atribui("username_new", $username );
					$this->tpl->atribui("senha_new", $senha);
					$this->tpl->atribui("conf_senha_new", $conf_senha);
					$this->tpl->atribui("id_nas_new", $id_nas );
					$this->tpl->atribui("id_pop_new", $id_pop);
					$this->tpl->atribui("selecao_ip_new", $selecao_ip );
					$this->tpl->atribui("mac_new", $mac );
					$this->tpl->atribui("id_cliente", $id_cliente );
					$this->tpl->atribui("id_cliente_produto", $id_cliente_produto );
					$this->tpl->atribui("valor_contrato_new", $valor_contrato);
					$this->tpl->atribui("dominio_hospedagem_new", $dominio_hospedagem);
					$this->tpl->atribui("tipo_hospedagem_new", $tipo_hospedagem);
					$this->tpl->atribui("pri_venc_new",$pri_venc);
					$this->tpl->atribui("pagamento_new",$pagamento);
					$this->tpl->atribui("dominio", $dominio);

					$this->tpl->atribui("cc_vencimento_new",$cc_vencimento); 
					$this->tpl->atribui("cc_numero_new",$cc_numero);
					$this->tpl->atribui("cc_operadora_new",$cc_operadora);
					$this->tpl->atribui("db_banco_new",$db_banco);
					$this->tpl->atribui("db_agencia_new",$db_agencia);
					$this->tpl->atribui("db_conta_new",$db_conta);

					
					$this->tpl->atribui("forma_pagamento",$_LS_FORMA_PAGAMENTO);
					$this->tpl->atribui("lista_pop",$lista_pop);
					$this->tpl->atribui("lista_nas",$lista_nas);
					$this->tpl->atribui("produto_carac",$produto_carac);
					$this->tpl->atribui("tipo_cobranca",$tipo_cobranca);
					$this->tpl->atribui("contrato",$contrato);
					$this->tpl->atribui("produto",$produto);
					$this->tpl->atribui("produto_geral",$produto_geral);
						
					switch($tipo) {
						case 'D':
								$sSQL = "SELECT * FROM prtb_produto_discado WHERE id_produto = '$id_produto'";
								$info_produto = $this->bd->obtemUnicoRegistro($sSQL);

								$d_franquia_horas = $info_produto["franquia_horas"];
								$d_permitir_duplicidade = $info_produto["permitir_duplicidade"];
								$d_valor_hora_adicional = $info_produto["valor_hora_adicional"];

								$this->tpl->atribui("foneinfo", $foneinfo );
								$this->tpl->atribui("d_franquia_horas", $d_franquia_horas);
								$this->tpl->atribui("d_permitir_duplicidade", $d_permitir_duplicidade);
								$this->tpl->atribui("d_valor_hora_adicional", $d_valor_hora_adicional);

							break;
						case 'H':
								$sSQL = "SELECT * FROM prtb_produto_hospedagem WHERE id_produto = '$id_produto'";
								$info_produto = $this->bd->obtemUnicoRegistro($sSQL);

								$h_dominio = $info_produto["dominio"];
								$h_franquia_em_mb = $info_produto["franquia_em_mb"];
								$h_valor_mb_adicional = $info_produto["valor_mb_adicional"];

								$this->tpl->atribui("h_dominio", $h_dominio);
								$this->tpl->atribui("h_franquia_em_mb", $h_franquia_em_mb);
								$this->tpl->atribui("h_valor_mb_adicional", $h_valor_mb_adicional);

							break;
						case 'BL':
								$sSQL = "SELECT * FROM prtb_produto_bandalarga WHERE id_produto = '$id_produto'";
								//echo "query: $sSQL <br>";

								$info_produto = $this->bd->obtemUnicoRegistro($sSQL);

								$bl_banda_upload_kbps = $info_produto["banda_upload_kbps"];
								$bl_banda_download_kbps = $info_produto["banda_download_kbps"];
								$bl_franquia_trafego_mensal_gb = $info_produto["franquia_trafego_mensal_gb"];
								$bl_valor_trafego_adicional_gb = $info_produto["valor_trafego_adicional_gb"];

								$this->tpl->atribui("bl_banda_upload_kbps", $bl_banda_upload_kbps);
								$this->tpl->atribui("bl_banda_download_kbps", $bl_banda_download_kbps);
								$this->tpl->atribui("bl_franquia_trafego_mensal_gb", $bl_franquia_trafego_mensal_gb);
								$this->tpl->atribui("bl_valor_trafego_adicional_gb", $bl_valor_trafego_adicional_gb);
						break;
				}

					$sSQL  = "SELECT to_char(data, 'DD/mm/YYYY') as data, valor, status FROM cbtb_faturas WHERE id_cliente_produto = '$id_cliente_produto' AND status = 'A'";
					$faturas_antigas = $this->bd->obtemRegistros($sSQL);
					
					$this->tpl->atribui("ft_ant",$faturas_antigas);
					
						if (!$p || $p != "ok"){
							$this->arquivoTemplate = "cliente_contrato_migracao_confirmacao.html";
							return;
						} else if ($p == "ok"){
						
							$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
							
							/* CRIANDO NOVO CONTRATO */
							
							$info_prov = $this->prefs->obtem();

							$cod_banco = $info_prov["cobranca"]["cod_banco"];
							$carteira = $info_prov["cobranca"]["carteira"];
							$agencia = $info_prov["cobranca"]["agencia"];
							$num_conta = $info_prov["cobranca"]["num_conta"];
							$convenio = $info_prov["cobranca"]["convenio"];
							$pagamento = $info_prov["cobranca"]["pagamento"];

							if (!$cod_banco) $cod_banco = 0;
							if (!$carteira) $carteira = 0;
							if (!$agencia) $agencia = 0;
							if (!$num_conta) $num_conta = 0;
							if (!$convenio) $convenio = 0;

							//Informações sobre o produto
							$sSQL = "SELECT * from prtb_produto where id_produto = $id_produto";
							$info_produto = $this->bd->obtemUnicoRegistro($sSQL);

							$data_contratacao = @$_REQUEST["data_contratacao"];
							$vigencia = @$_REQUEST["vigencia"];

							list($d, $m, $a) = explode("/", $data_contratacao);
							$data_renovacao = date("Y-m-d", mktime(0, 0, 0, $m+$vigencia, $d, $a));										
							$data_contratacao = "$a-$m-$d";


							$id_cobranca = @$_REQUEST['tipo_cobranca'];
							$status = @$_REQUEST["status"];
							$tipo_produto = @$_REQUEST["tipo_produto"];
							$valor_produto = $info_produto["valor"];
							$num_emails = $info_produto["num_emails"];
							$quota = $info_produto["quota_por_conta"];
							$tx_instalacao = @$_REQUEST["tx_instalacao"];
							$comodato = @$_REQUEST["comodato"];
							$valor_comodato = @$_REQUEST["valor_comodato"];
							$desconto_promo = @$_REQUEST["desconto_promo"];
							$periodo_desconto = @$_REQUEST["periodo_desconto"];
							$valor_prorata = @$_REQUEST["valor_prorata"];
							$carencia = @$_REQUEST["carencia_pagamento"];


							//Informações sobre banco e cartão de crédito
							$cc_vencimento = @$_REQUEST["cc_vencimento"];
							$cc_numero = @$_REQUEST["cc_numero"];
							$cc_operadora = @$_REQUEST["cc_operadora"];

							$db_banco = @$_REQUEST["db_banco"];
							$db_agencia = @$_REQUEST["db_agencia"];
							$db_conta = @$_REQUEST["db_conta"];



							//Corrige possíveis falhas de entrada em alguns campos
							if (!$comodato) {
								$comodato = 'f';
								$valor_comodato = 0;
							} else if(!$valor_comodato) {
								$valor_comodato = 0;
							}


							if (!$desconto_promo) $desconto_promo = 0;
							if (!$periodo_desconto) $periodo_desconto = 0;
							if (!$tx_instalacao) $tx_instalacao = 0;


							//Substitui todos as "," por "."
							$desconto_promo = str_replace(",",".",$desconto_promo);
							$$valor_comodato = str_replace(",",".",$valor_comodato);
							$tx_instalacao = str_replace(",",".",$tx_instalacao);


							//Calcula o valor do contrato
							//$valor_contrato = ($valor_produto *  $vigencia) - ($desconto_promo * $periodo_desconto);
							//$valor_contrato += $valor_comodato + $tx_instalacao;

							$valor_contrato = number_format($valor_produto + $valor_comodato, 2, '.', '');					
							$valor_cont_temp = $valor_contrato;
							//Diminui o desconto no valor real do contrato caso este tenha mesmo período que a vigência do contrato
							if ($periodo_desconto >= $vigencia) $valor_contrato -= $desconto_promo;

							if (!$cc_vencimento || $cc_vencimento == "") $cc_vencimento = "0";
							if (!$cc_numero || $cc_numero == "") $cc_numero = "0";
							if (!$cc_operadora || $cc_operadora == "") $cc_operadora = "0";
							if (!$db_banco || $db_banco == "") $db_banco = "0";
							if (!$db_agencia || $db_agencia == "") $db_agencia = "0";
							if (!$db_conta || $db_conta == "") $db_conta = "0";

							$id_cliente_produto_novo = $this->bd->proximoID("cbsq_id_cliente_produto");

							$sSQL = "INSERT INTO cbtb_cliente_produto (id_cliente_produto, id_cliente, id_produto, dominio, excluido) VALUES ('$id_cliente_produto_novo', '$id_cliente', '$id_produto', '$dominio', FALSE) ";
							$this->bd->consulta($sSQL);
							
							//echo "Cliente Produto: $sSQL <br>";
							
							$sSQL = "SELECT currval('cbsq_id_cliente_produto') as icpn";
							$icpn = $this->bd->obtemUnicoRegistro($sSQL);
							
							$id_cliente_produto_new = $icpn["icpn"];
							
							// LEGENDA:
							// - $id_cliente_produto : é o id_cliente_produto do contrato/conta cadastrado anteriormente.
							// - $id_cliente_produto_novo : é a variavel que puxa o nextval para o novo id_cliente_produto.
							// - $id_cliente_produto_new : é o resultado de currval do id_cliente_produto NOVO
							// - $icpn : currval
							
							
							

							$sSQL =  "INSERT INTO cbtb_contrato ( ";
							$sSQL .= "	id_cliente_produto, data_contratacao, vigencia, data_renovacao, valor_contrato, id_cobranca, status, ";
							$sSQL .= "	tipo_produto, valor_produto, num_emails, quota_por_conta, tx_instalacao, comodato, valor_comodato, ";
							$sSQL .= "	desconto_promo, periodo_desconto, id_produto, cod_banco, agencia, num_conta, carteira, ";
							$sSQL .= "	convenio, cc_vencimento, cc_numero, cc_operadora, db_banco, db_agencia, db_conta, carencia";
							$sSQL .= ") VALUES ( ";
							$sSQL .= "	'$id_cliente_produto_new', '$data_contratacao', '$vigencia', '$data_renovacao', '$valor_contrato', '$id_cobranca', '$status', ";
							$sSQL .= "	'$tipo_produto', '$valor_produto', '$num_emails', '$quota', '$tx_instalacao', '$comodato', '$valor_comodato', ";
							$sSQL .= "	'$desconto_promo', '$periodo_desconto', '$id_produto', '$cod_banco', '$agencia', '$num_conta', '$carteira', ";
							$sSQL .= "	'$convenio', '$cc_vencimento', '$cc_numero', '$cc_operadora', '$db_banco', '$db_agencia', '$db_conta', '$carencia'";
							$sSQL .= ")";
							
							//echo "contrato novo: $sSQL <br>";
							$this->bd->consulta($sSQL);
							
							$this->contratoHTML($id_cliente,$id_cliente_produto_new,$tipo_produto);

							
							/* FINAL CRIAÇÃO CONTRATO */
						
							/* SETA CONTRATO ANTIGO COM STATUS = M (modificado) */
							
							$sSQL = "UPDATE cbtb_contrato SET status = 'M', data_alt_status = now() WHERE id_cliente_produto = '$id_cliente_produto'";
							$this->bd->consulta($sSQL);
							
							/* FIM UPDATE CONTRATO */
						
							/* UPDATE DE CONTA */

							$sSQL = "UPDATE cntb_conta SET id_cliente_produto = '$id_cliente_produto_new' WHERE username = '$username' AND tipo_conta = '$tipo_produto' AND dominio = '$dominio' ";
							$this->bd->consulta($sSQL);

							
							/* FINAL UPDATE DE CONTA */
						
							/* ESTORNANDO FATURAS */
							
							$sSQL = "UPDATE cbtb_faturas SET status = 'E' WHERE id_cliente_produto = '$id_cliente_produto' AND status = 'A' ";
							$this->bd->consulta($sSQL);
								
							
							/* FINAL DO ESTORNO DE FATURAS */
							
							/* ESTORNO DO CARNE */
							
							$sSQL = "SELECT id_carne FROM cbtb_faturas WHERE id_cliente_produto = '$id_cliente_produto' AND status = 'E' ";
							$carne = $this->bd->obtemUnicoRegistro($sSQL);
							
							$id_carne = $carne["id_carne"];
							
							
							$sSQL = "UPDATE cbtb_carne SET status = 'E' WHERE id_carne ='$id_carne' ";
							$this->bd->consulta($sSQL);
							
							/* FINAL DO ESTORNO DO CARNE */
							
							require_once("hugo_faturas.php");
						
							$msg_final = "CONTRATO MIGRADO COM SUCESSO!";
							$this->tpl->atribui("mensagem",$msg_final);
							$this->tpl->atribui("url", "clientes.php?op=cobranca&id_cliente=".$id_cliente."&rotina=resumo");
							$this->tpl->atribui("target","_self");
							$this->arquivoTemplate="msgredirect.html";
							return;
						
						
						
						}
					
				
				}
			

			
			
			} 
	
	
		}// migrar
		
		

						$this->arquivoTemplate = "cliente_contrato_modificacao.html";

	
	} else if ($op == "faturamento"){

	
		global $_LS_MESES_ANO;
								
		$hoje = date("Y-m-d");
		list($ano, $mes, $dia) = explode("-", $hoje);
		
		//TODO: Inver ter alista de periodos
		
		//Cria um array referente aos ultimos 
		$ls_ultimos_meses = array();
		
		for ($i=0; $i<12; $i++) {
			list($ca, $cm) = explode("-", date("Y-m", mktime(0, 0, 0, $mes - $i, 1, $ano)));
			
			//$cperiodo = array( "ano" => $ca, "mes" => $cm);
			$ls_ultimos_meses[] = array( "valor" => $ca."-". $cm, "texto" => $_LS_MESES_ANO[(int)$cm] . "/" . $ca ); 
		}
		
		
		$acao = @$_REQUEST["acao"];
		$op = @$_REQUEST["op"];
		$periodo = @$_REQUEST["periodo"];
		$extra = @$_REQUEST["extra"];
		
		$mes_periodo=null;
		$relat = null;
		$fat = null;
		
		
		if(!$periodo) $periodo = "total";
		if(!$acao || $periodo == "total") $acao = "geral";
		
		
		
		if($acao == "geral") {
			
			$meses_periodo = 12;
			
			$data_fim = $hoje;
			
			list($da, $dm, $dd) = explode("-", $data_fim);
			$data_ini = date("Y-m-d", mktime(0,0,0,$dm-$meses_periodo,1,$da));
			
			$sSQL  = "SELECT ";
			$sSQL .= "	SUM(cb.valor_pago) as faturamento,  ";
			$sSQL .= "	SUM(cb.valor) as valor,  ";
			$sSQL .= "	SUM(cb.acrescimo) as acrescimo,  ";
			$sSQL .= "	SUM(cb.desconto) as desconto,  ";
			$sSQL .= "	EXTRACT(month from cb.data_pagamento) as mes,  ";
			$sSQL .= "	EXTRACT(year from cb.data_pagamento) as ano  ";
			$sSQL .= "FROM  ";
			$sSQL .= "	cbtb_faturas cb ";
			$sSQL .= "WHERE  ";
			$sSQL .= "	status = 'P'  ";
			$sSQL .= "	AND data_pagamento BETWEEN  ";
			$sSQL .= "		CAST( '$data_ini' as date) ";
			$sSQL .= "		AND CAST( '$data_fim' as date ) ";
			$sSQL .= "GROUP BY  ";
			$sSQL .= "	ano, mes ";
			$sSQL .= "ORDER BY  ";
			$sSQL .= "	ano, mes ";

			
			$eSQL  = "SELECT ";
			$eSQL .= "	SUM(cb.valor_pago) as faturamento,  ";
			$eSQL .= "	SUM(cb.valor) as valor,  ";
			$eSQL .= "	SUM(cb.desconto) as desconto,  ";
			$eSQL .= "	SUM(cb.acrescimo) as acrescimo  ";
			$eSQL .= "FROM  ";
			$eSQL .= "	cbtb_faturas cb ";
			$eSQL .= "WHERE  ";
			$eSQL .= "	status = 'P'  ";
			$eSQL .= "	AND data_pagamento BETWEEN  ";
			$eSQL .= "		CAST( '$data_ini' as date) ";
			$eSQL .= "		AND CAST( '$data_fim' as date ) ";

			//echo "$sSQL";

			$relat = $this->bd->obtemRegistros($sSQL);
			$fat = $this->bd->obtemUnicoRegistro($eSQL);
			
			$tmp_relat = array();
			
			//echo "<br>\n";
			
			for ($i=0; $i<$meses_periodo; $i++) {
			
				list($da, $dm, $dd) = explode("-", $data_ini);
												
				$data_teste = date("Y-m-d", mktime(0, 0, 0, $dm+$i, 1, $da));
				
				list($da, $dm, $dd) = explode("-", $data_teste);
				$indice = "$da$dm";
				
				$tmp_relat["$indice"] = array("faturamento" => "0.00", "valor" => "0.00", "acrescimo" => "0.00", "desconto" => "0.00", "mes" => "$dm", "ano" => "$da");
				
				//$tmp_relat["$indice"] = array("faturamento" => 0, "mes" => "$dm", "ano" => "$da");
				
				//echo "$i - $data_teste = [$indice]<br>";
						
			}
			
			//echo "<br>";
			
			for ($i=0; $i<count($relat); $i++) {
				
				$indice  =  $relat[$i]["ano"];
				$indice .= ($relat[$i]["mes"]<10) ? "0" : "";
				$indice .=  $relat[$i]["mes"];
				
				$relat[$i]["mes"] = ($relat[$i]["mes"]<10?"0":"") . $relat[$i]["mes"] ;
				
				
				//echo "[$indice]<br>";
				
				$tmp_relat["$indice"] = $relat[$i];
								
			}
			
			$relat = $tmp_relat;
			
						
		} else if( $acao == "sub_mes" ) {
		
			$meses_periodo = 1;
			
			$data_ini = $periodo . "-01";
			
			list($da, $dm, $dd) = explode("-", $data_ini);
			
			$data_fim = date("Y-m-d", mktime(0,0,0,$dm+1, $dd-1, $da));			
			
			$mes_periodo = $this->obtem_mes($dm) . " de " . $da;
						
			$sSQL  = "SELECT ";
			$sSQL .= "	SUM(cb.valor_pago) as faturamento,  ";
			$sSQL .= "	SUM(cb.valor) as valor,  ";
			$sSQL .= "	SUM(cb.acrescimo) as acrescimo,  ";
			$sSQL .= "	SUM(cb.desconto) as desconto,  ";
			$sSQL .= "	EXTRACT(day from cb.data_pagamento) as dia, ";
			$sSQL .= "	EXTRACT(month from cb.data_pagamento) as mes, ";
			$sSQL .= "	EXTRACT(year from cb.data_pagamento) as ano ";
			$sSQL .= "FROM ";
			$sSQL .= "	cbtb_faturas cb ";
			$sSQL .= "WHERE ";
			$sSQL .= "	status = 'P' ";
			$sSQL .= "	AND data_pagamento BETWEEN ";
			$sSQL .= "	CAST( '$data_ini' as date ) ";
			$sSQL .= "	AND CAST( '$data_fim' as date ) ";
			$sSQL .= "GROUP BY ";
	 		$sSQL .= "ano, mes, dia ";
	 		
	 		
			$eSQL  = "SELECT ";
			$eSQL .= "	SUM(cb.valor_pago) as faturamento,  ";
			$eSQL .= "	SUM(cb.valor) as valor,  ";
			$eSQL .= "	SUM(cb.desconto) as desconto,  ";
			$eSQL .= "	SUM(cb.acrescimo) as acrescimo  ";
			$eSQL .= "FROM  ";
			$eSQL .= "	cbtb_faturas cb ";
			$eSQL .= "WHERE  ";
			$eSQL .= "	status = 'P'  ";
			$eSQL .= "	AND data_pagamento BETWEEN  ";
			$eSQL .= "		CAST( '$data_ini' as date) ";
			$eSQL .= "		AND CAST( '$data_fim' as date ) ";
		
			
			//echo $sSQL;
			
			//pega o ultimo dia do ano
			
			list($lixo, $lixo, $ultimo_dia_mes) = explode("-", $data_fim);
			
			$relat = $this->bd->obtemRegistros($sSQL);
			$fat = $this->bd->obtemUnicoRegistro($eSQL);

			$tmp_relat = array();

			//echo "<br>\n";
			

			for ($i=0; $i<$ultimo_dia_mes; $i++) {

				list($da, $dm, $dd) = explode("-", $data_ini);

				$data_teste = date("Y-m-d", mktime(0, 0, 0, $dm, $dd+$i, $da));

				list($da, $dm, $dd) = explode("-", $data_teste);
				$indice = "$da$dm$dd";

				$tmp_relat["$indice"] = array("faturamento" => "0.00", "valor" => "0.00", "acrescimo" => "0.00", "desconto" => "0.00", "dia" => "$dd", "mes" => "$dm", "ano" => "$da");

				//$tmp_relat["$indice"] = array("faturamento" => 0, "dia" => "$dd", "mes" => "$dm", "ano" => "$da");

				//echo "$i - $data_teste = [$indice]<br>";

			}

			//echo "<br>";

			for ($i=0; $i<count($relat); $i++) {

				$indice  =  $relat[$i]["ano"];
				
				$indmes  = ($relat[$i]["mes"]<10) ? "0" : "";
				$indmes .=  $relat[$i]["mes"];
				
				$inddia  = ($relat[$i]["dia"]<10) ? "0" : "";
				$inddia .=  $relat[$i]["dia"];
				
				$indice .= $indmes . $inddia;
				
				$relat[$i]["mes"] = $indmes;
				$relat[$i]["dia"] = $inddia;

				//$relat[$i]["mes"] = ($relat[$i]["mes"]<10?"0":"") . $relat[$i]["mes"] ;

				//echo "[$indice]<br>";

				$tmp_relat["$indice"] = $relat[$i];

			}
						
			$relat = $tmp_relat;			
			
		} else if($acao == "sub_dia") {
		
			list($da, $dm, $dd) = explode("-", $periodo);
			
			$mes_periodo = $dd . " de " . $this->obtem_mes($dm) . " de " . $da;
								
			$sSQL  = "SELECT ";
			$sSQL .= "   cp.id_cliente_produto, ";
			$sSQL .= "   cl.id_cliente, ";
			$sSQL .= "   cl.nome_razao, ";
			$sSQL .= "   cn.username, ";
			$sSQL .= "   f.valor,  ";
			$sSQL .= "   f.valor_pago,  ";
			$sSQL .= "   f.acrescimo,  ";
			$sSQL .= "   f.desconto,  ";
			$sSQL .= "   p.nome  ";
			$sSQL .= "FROM  ";
			$sSQL .= "   cltb_cliente cl INNER JOIN cbtb_cliente_produto cp USING(id_cliente) ";
			$sSQL .= "   LEFT OUTER JOIN cntb_conta cn ON (cn.id_cliente_produto = cp.id_cliente_produto AND conta_mestre is true),	 ";
			$sSQL .= "   prtb_produto p,  ";
			$sSQL .= "   cbtb_faturas f  ";
			$sSQL .= "WHERE  ";
			$sSQL .= "   f.id_cliente_produto = cp.id_cliente_produto ";
			$sSQL .= "   AND p.id_produto = cp.id_produto ";
			$sSQL .= "   AND (cn.username is null OR (cn.tipo_conta = p.tipo AND cn.conta_mestre is true)) ";
			$sSQL .= "   AND f.status = 'P' ";
			$sSQL .= "   AND f.data_pagamento = '$periodo' ";
			
			$eSQL  = "SELECT ";
			$eSQL .= "	SUM(cb.valor_pago) as faturamento,  ";
			$eSQL .= "	SUM(cb.valor) as valor,  ";
			$eSQL .= "	SUM(cb.desconto) as desconto,  ";
			$eSQL .= "	SUM(cb.acrescimo) as acrescimo  ";
			$eSQL .= "FROM  ";
			$eSQL .= "	cbtb_faturas cb ";
			$eSQL .= "WHERE  ";
			$eSQL .= "	status = 'P'  ";
			$eSQL .= "	AND data_pagamento = '$periodo' ";
			
			$relat = $this->bd->obtemRegistros($sSQL);
			$fat = $this->bd->obtemUnicoRegistro($eSQL);
			
			//echo($eSQL);
		
		}
		
		
		if($acao == "geral") krsort($relat);
		
		$this->tpl->atribui("relat", $relat);
		$this->tpl->atribui("mes_periodo", $mes_periodo);
		$this->tpl->atribui("fat", $fat);
		$this->tpl->atribui("periodo", $periodo);
		$this->tpl->atribui("acao", $acao);
		$this->tpl->atribui("op", $op);
		
		ksort($relat);
				
		$meses_ano = array();
		while( list($m,$s) = each($_LS_MESES_ANO) ) {
			//echo "idx: " . ($m<10?"0":"").$m;
			$meses_ano[ ($m<10?"0":"") . $m ] = $s;
		}
		
		$this->tpl->atribui("meses_ano", $meses_ano);
		$this->tpl->atribui("ls_ultimos_meses", $ls_ultimos_meses);	
		$this->arquivoTemplate = "relatorio_faturamento.html";

			
							
		
			
		//Exibição do gráfico alucinaaaaaaaaaaaaaáááádooooo!!!!			
		//Manda ver negão q eu sei q tu é bão 

		if ($extra == "grafico") {

			$dados = array();
			$legendas = array();
			
			while( list($i,$v) = each($relat) ) {
			
			   $dados[] = $v["faturamento"];
			   
			   if($acao == "geral") {
				   $mes_corrente = substr($this->obtem_mes($relat[$i]["mes"]),0,3);
				   $leg = $mes_corrente . "/" . $relat[$i]["ano"];  
				   $legendas[] = $leg;
			   }
			

			
			}
			
			


			//$relat = $tmp_relat;

			//$pontos = array();
			//$legendas = array();


			//for($i=0;$i<count($relat)-10;$i++) {
			//   //$mes_corrente = $this->obtem_mes($relat[$i]["mes"]);
			//   //$legendas[] =  $mes_corrente . "/" . $relat[$i]["ano"];
			//   $pontos[] = $relat[$i]["faturamento"];			   
			//}


			// GERA O Gráfico

			header("pragma: no-cache");
			header("Content-type: Image/png");

			//$pontos = array("9", "16", "20");
			
			if ($acao=="sub_mes") $larg_grafico = 550; else $larg_grafico = 500;
			
			$grafico = new Graph($larg_grafico,250,"png");


			$grafico->SetScale("textlin"); 
			//$grafico->SetShadow(); 
			//$grafico->title->Set('Relatório de Adesões');
			$grafico->img->SetMargin(40,40,40,80);

			//Imagem de Fundo
			$grafico->SetBackgroundImage("./template/default/images/gr_back1.jpg",BGIMG_FILLPLOT); //BGIMG_FILLFRAME);
			$grafico->SetMarginColor("white");

			//Cria uma nova mostragem gráfica
			$gBarras = new BarPlot($dados); 

			//$grafico->xaxis->SetMajTickPositions($positions,$titulos);

			// ajuste de cores 
			//$gBarras->SetFillColor("#ff0000");
			$gBarras->SetFillGradient("#aa0000","red",GRAD_VER);;
			$gBarras->SetColor("#aa0000");


			//$gBarras->SetShadow("darkblue"); 
			//$grafico->xaxis->labels = $legendas;
			//$gBarras->label->Set($legendas);

			// título das barras
			$grafico->xaxis->SetTickLabels($legendas);
			if ($acao == "geral")
				$grafico->xaxis->SetLabelAngle(90);

			// adicionar mostrage de barras ao gráfico 
			$grafico->Add($gBarras); 

			// imprimir gráfico 
			$grafico->Stroke();

			$this->arquivoTemplate = '';		
			return;

		}	
		
	
	}else if ($op == "relatorio_reagendamento"){
		
		$sSQL  = "SELECT ";
		$sSQL .= "re.id_cliente_produto, re.data as data_vencimento, to_char(re.data_reagendamento, 'DD/mm/YYYY') as data_reagendamento, re.admin, to_char(re.data_para_reagendamento, 'DD/mm/YYYY') as reagendado_para, re.id_reagendamento, ";
		$sSQL .= "fa.id_cliente_produto, fa.data as data, fa.valor, fa.status, fa.id_carne, to_char(fa.reagendamento, 'DD/mm/YYYY') as reagendamento, fa.id_cobranca, ";
		$sSQL .= "ca.id_cliente_produto, ca.id_cliente, ";
		$sSQL .= "ad.admin, ad.id_admin ";
		$sSQL .= "FROM ";
		$sSQL .= "lgtb_reagendamento re, cbtb_faturas fa, cntb_conta ca, adtb_admin ad ";
		$sSQL .= "WHERE ";
		$sSQL .= "re.id_cliente_produto = fa.id_cliente_produto AND "; 
		$sSQL .= "re.data = fa.data AND ";
		$sSQL .= "fa.id_cliente_produto = ca.id_cliente_produto AND ";
		$sSQL .= "ad.admin = re.admin ";
		
		//echo "REAGENDAMENTO: $sSQL <br>";
		
		$reagendamentos = $this->bd->obtemRegistros($sSQL);
		
		$this->tpl->atribui("reagendamentos",$reagendamentos);
		
		$this->arquivoTemplate = "relatorio_reagendamento.html";
	
	
	
	}else if ($op == "boleto_pc"){
	
	$id_cliente = @$_REQUEST["id_cliente"];
	$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
	$data = @$_REQUEST["data"];
	
	
		
	$sSQL  = "SELECT cl.nome_razao, cl.endereco, cl.id_cidade, cl.estado, cl.cep, cl.cpf_cnpj, cd.cidade as nome_cidade, cd.id_cidade  ";
	$sSQL .= "FROM ";
	$sSQL .= "cltb_cliente cl, cftb_cidade cd ";
	$sSQL .= "WHERE ";
	$sSQL .= "cl.id_cliente = '$id_cliente' AND ";
	$sSQL .= "cd.id_cidade = cl.id_cidade";

	$cliente = $this->bd->obtemUnicoRegistro($sSQL);
	//echo "CLIENTE: $sSQL  <br>";


	$sSQL  = "SELECT valor, id_cobranca,to_char(data, 'DD/mm/YYYY') as data, cod_barra, descricao, status, observacoes, cod_barra, id_carne, nosso_numero, linha_digitavel FROM ";
	$sSQL .= "cbtb_faturas ";
	$sSQL .= "WHERE ";
	$sSQL .= "id_cliente_produto = '$id_cliente_produto' AND ";
	$sSQL .= "data = '$data' ";


	$fatura = $this->bd->obtemUnicoRegistro($sSQL);
	//echo "fatura: $sSQL<br>";


	// PEGANDO INFORMAÇÕES DAS PREFERENCIAS
	//$provedor = $this->prefs->obtem("total");
	$provedor = $this->prefs->obtem("total");

	$sSQL = "SELECT ct.id_produto, pd.nome from cbtb_contrato ct, prtb_produto pd WHERE ct.id_cliente_produto = '$id_cliente_produto' and ct.id_produto = pd.id_produto";
	$produto = $this->bd->obtemUnicoRegistro($sSQL);
		
	
	
	//$nosso_numero = $nn['nosso_numero'];
	$data_venc = $fatura["data"];
	
	@list($dia,$mes,$ano) = explode("/",$fatura["data"]);
	$vencimento = $ano.$mes.$dia;
	
	$valor = $fatura["valor"];
	$id_cobranca = $fatura["id_cobranca"];
	$nome_cliente = $cliente["nome_razao"];
	$cpf_cliente = $cliente["cpf_cnpj"];
	$id_empresa = $provedor["cnpj"];
	//$nosso_numero = 1;
	$nome_cedente = $provedor['nome'];
	$cendereco = $provedor['endereco'];
	$clocalidade = $provedor['localidade'];
	$observacoes = $provedor['observacoes'];
	$nome_produto = $produto["nome"];
	
	
	
	
	$ph = new MUtils;
	
	$_path = MUtils::getPwd();
	
	$images = $_path."/template/boletos/imagens";

	$sSQL = "SELECT nextval('blsq_carne_nossonumero') as nosso_numero ";
	$nn = $this->bd->obtemUnicoRegistro($sSQL);

	//$nosso_numero = $nn['nosso_numero'];
	$nosso_numero = $fatura["nosso_numero"];
	$codigo_barras = $fatura["cod_barra"];
	$linha_digitavel = $fatura["linha_digitavel"];
	
	
	
	//$codigo_barras = MArrecadacao::codigoBarrasPagContas($valor,$id_empresa,$nosso_numero,$vencimento);
	
	//$codigo_barras = $fatura["cod_barra"];
	//$linha_digitavel = MArrecadacao::linhaDigitavel($codigo_barras);
	$hoje = date("d/m/Y");

	$this->tpl->atribui("codigo_barras",$codigo_barras);

	copy("/mosman/virtex/dados/carnes/codigos/".$codigo_barras.".png","codigos/".$codigo_barras.".png");

	$this->tpl->atribui("linha_digitavel",$linha_digitavel);
	$this->tpl->atribui("valor",$valor);
	$this->tpl->atribui("imagens",$images);
	$this->tpl->atribui("vencimento", $data_venc);
	$this->tpl->atribui("hoje",$hoje);
	$this->tpl->atribui("nosso_numero",$nosso_numero);
	$this->tpl->atribui("sacado",$nome_cliente);
	$this->tpl->atribui("sendereco",$cliente['endereco']);
	$this->tpl->atribui("scidade",$cliente['nome_cidade']);
	$this->tpl->atribui("suf",$cliente['estado']);
	$this->tpl->atribui("scep",$cliente['cep']);
	$this->tpl->atribui("juros",$provedor['tx_juros']);
	$this->tpl->atribui("multa",$provedor['multa']);
	$this->tpl->atribui("nome_cedente",$provedor['nome']);
	$this->tpl->atribui("cendereco",$cendereco);
	$this->tpl->atribui("clocalidade",$clocalidade);
	$this->tpl->atribui("observacoes",$observacoes);
	$this->tpl->atribui("produto",$nome_produto);
	$this->tpl->atribui("path",$_path);

	
	
	$template  = $this->tpl->obtemPagina("../boletos/pc-estilo.html");
	$template .= $this->tpl->obtemPagina("../boletos/layout-pc.html");
	
	
	echo ($template);
	
	
	}
	
	
	
	// op = contratos

} // function processa
	
	public function amortizar(){


		$data = @$_REQUEST["reagendamento"];
		$data_pagamento = @$_REQUEST["data_pagamento"];
		$reagendamento = @$_REQUEST["reagendamento"];
		$reagendar = @$_REQUEST["reagendar"];
		$id_cliente_produto = @$_REQUEST["id_cliente_produto"];


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
		$sSQL .= "	status = '".@$_REQUEST["status_fatura"]."', ";
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

		if ($reagendar && $reagendamento){

			$adm = $this->admLogin->obtemAdmin();

			$sSQL = "INSERT INTO lgtb_reagendamento (data, id_cliente_produto, admin, data_para_reagendamento) VALUES ('".@$_REQUEST["data"]."','$id_cliente_produto','$adm','$reagendamento') ";
			$this->bd->consulta($sSQL);

			//echo "LOG REAGENDAMENTO: $sSQL <br>";
		}


		return($id_cliente_produto);



	}

	public function geraCarne($dia_inicio,$dia_final,$mes,$ano,$dti,$dtf){




					//MBoleto::barCode($codigo);

	}

	public function carne($id_cliente_produto,$data,$id_cliente,$segunda_via=false){
	
	$sSQL  = "SELECT cl.nome_razao, cl.endereco, cl.id_cidade, cl.estado, cl.cep, cl.cpf_cnpj, cd.cidade as nome_cidade, cd.id_cidade  ";
	$sSQL .= "FROM ";
	$sSQL .= "cltb_cliente cl, cftb_cidade cd ";
	$sSQL .= "WHERE ";
	$sSQL .= "cl.id_cliente = '$id_cliente' AND ";
	$sSQL .= "cd.id_cidade = cl.id_cidade";

	$cliente = $this->bd->obtemUnicoRegistro($sSQL);
	//echo "CLIENTE: $sSQL  <br>";


	$sSQL  = "SELECT valor, id_cobranca,to_char(data, 'DD/mm/YYYY') as data  FROM ";
	$sSQL .= "cbtb_faturas ";
	$sSQL .= "WHERE ";
	$sSQL .= "id_cliente_produto = '$id_cliente_produto' AND ";
	$sSQL .= "data = '$data' ";


	$fatura = $this->bd->obtemUnicoRegistro($sSQL);
	//echo "fatura: $sSQL<br>";


	// PEGANDO INFORMAÇÕES DAS PREFERENCIAS
	//$provedor = $this->prefs->obtem("total");
	$provedor = $this->prefs->obtem("total");

	$sSQL = "SELECT ct.id_produto, pd.nome from cbtb_contrato ct, prtb_produto pd WHERE ct.id_cliente_produto = '$id_cliente_produto' and ct.id_produto = pd.id_produto";
	$produto = $this->bd->obtemUnicoRegistro($sSQL);
	//echo "PRODUTO: $sSQL <br>";

	//$codigo = @$_REQUEST["codigo"];
	//$data_venc = "30/04/2006";
	
	$sSQL = "SELECT nextval('blsq_carne_nossonumero') as nosso_numero ";
	$nn = $this->bd->obtemUnicoRegistro($sSQL);
	
	$nosso_numero = $nn['nosso_numero'];
	$data_venc = $fatura["data"];
	@list($dia,$mes,$ano) = explode("/",$fatura["data"]);
	$vencimento = $ano.$mes.$dia;
	//echo $codigo;
	$valor = $fatura["valor"];
	$id_cobranca = $fatura["id_cobranca"];
	$nome_cliente = $cliente["nome_razao"];
	$cpf_cliente = $cliente["cpf_cnpj"];
	$id_empresa = $provedor["cnpj"];
	//$nosso_numero = 1;
	$nome_cedente = $provedor['nome'];
	$cendereco = $provedor['endereco'];
	$clocalidade = $provedor['localidade'];
	$observacoes = $provedor['observacoes'];
	$nome_produto = $produto["nome"];
    
	$codigo_barras = MArrecadacao::codigoBarrasPagContas($valor,$id_empresa,$nosso_numero,$vencimento);
	$linha_digitavel = MArrecadacao::linhaDigitavel($codigo_barras);
	$hoje = date("d/m/Y");
	
		
	//	$codigo = MArrecadacao::pagConta(...);
		
		
		  if( $segunda_via ) {
		
		     $hoje = $fatura["data"];
		     $codigo_barras = $fatura["cod_barra"];
		     $linha_digitavel = $fatura["linha_digitavel"];
		     
		  } else {
		  
		   	$codigo_barras = MArrecadacao::codigoBarrasPagContas($valor,$id_empresa,$nosso_numero,$vencimento);
		   	$hoje = date("d/m/Y");
		   	$linha_digitavel = MArrecadacao::linhaDigitavel($codigo_barras);
			
		   	$sSQL  = "UPDATE ";
				$sSQL .= "cbtb_faturas SET ";
				$sSQL .= "nosso_numero = '$nosso_numero', ";
				$sSQL .= "linha_digitavel = '$linha_digitavel', ";
				$sSQL .= "cod_barra = '$codigo_barras' ";
				$sSQL .= "WHERE ";
				$sSQL .= "id_cliente_produto = '$id_cliente_produto' AND ";
				$sSQL .= "data = '$data' ";
			
				$this->bd->consulta($sSQL);
			}

	//$barra = MArrecadacao::barCode($codigo_barras);
	
	
	$ph = new MUtils;
	
	$_path = MUtils::getPwd();
	
	
	//$code_bar = "carnes/".$codigo_barras.".png";
	$images = $_path."/template/boletos/imagens";
	//$this->tpl->atribui("codigo_barras",$codigo_barras);
	$this->tpl->atribui("codigo_barras",$codigo_barras);
//	copy("/mosman/virtex/dados/carnes/codigos/".$codigo_barras.".png","codigos/".$codigo_barras.".png");

	$this->tpl->atribui("linha_digitavel",$linha_digitavel);
	$this->tpl->atribui("valor",$valor);
	$this->tpl->atribui("imagens",$images);
	$this->tpl->atribui("vencimento", $data_venc);
	$this->tpl->atribui("hoje",$hoje);
	$this->tpl->atribui("nosso_numero",$nosso_numero);
	$this->tpl->atribui("sacado",$nome_cliente);
	$this->tpl->atribui("sendereco",$cliente['endereco']);
	$this->tpl->atribui("scidade",$cliente['nome_cidade']);
	$this->tpl->atribui("suf",$cliente['estado']);
	$this->tpl->atribui("scep",$cliente['cep']);
	$this->tpl->atribui("juros",$provedor['tx_juros']);
	$this->tpl->atribui("multa",$provedor['multa']);
	$this->tpl->atribui("nome_cedente",$provedor['nome']);
	$this->tpl->atribui("cendereco",$cendereco);
	$this->tpl->atribui("clocalidade",$clocalidade);
	$this->tpl->atribui("observacoes",$observacoes);
	$this->tpl->atribui("produto",$nome_produto);
	$this->tpl->atribui("path",$_path);
	//$this->tpl->atribui("barra",$barra);
	
	//return($carne_emitido);
	$fatura = $this->tpl->obtemPagina("template/boletos/layout-pc.html");
	return($fatura);

	}
	
	
	
	
	
	
	
	
	
	private function obtemPR($id_cliente){
	
		$sSQL  = "SELECT ";
		$sSQL .= "pr.tipo ";
		$sSQL .= "FROM cbtb_cliente_produto cp, prtb_produto pr ";
		$sSQL .= "WHERE cp.id_cliente = '$id_cliente' ";
		$sSQL .= "AND cp.id_produto = pr.id_produto ";
		$sSQL .= "GROUP BY pr.tipo";
									
		$prcliente = $this->bd->obtemRegistros($sSQL);
		//echo "QUERY: $sSQL<br> ";
									
		$prod_contr = array();
									
		//echo count($prcliente);
									
		for($i = 0; $i < count($prcliente); $i++ ) {
			$prod_contr[ trim(strtolower($prcliente[$i]["tipo"])) ] = true;
									
		}
									
				
									
		$this->tpl->atribui("prod_contr",$prod_contr);
		return;
	
	
	}

public function contratoHTML($id_cliente,$id_cliente_produto,$tipo_produto){
	
	//$tipo_produto = @$_REQUEST["tipo_produto"];
	$rotina = @$_REQUEST["rotina"];

	$hoje = date("Y-m-d");

	$provedor = $this->prefs->obtem("provedor");
	$geral = $this->prefs->obtem("geral");
	$id_cliente = @$_REQUEST["id_cliente"];
	$cobranca = $this->prefs->obtem("cobranca");



	$this->tpl->atribui("nome_provedor",$geral["nome"]);
	$this->tpl->atribui("localidade",$provedor["localidade"]);
	$this->tpl->atribui("cnpj_provedor",$provedor["cnpj"]);

	$sSQL  = "SELECT ";
	$sSQL .= "	ct.id_cliente_produto, ct.data_contratacao, ct.vigencia, ct.data_renovacao, ct.valor_contrato, ct.id_cobranca, ct.status, ";
	$sSQL .= "	ct.tipo_produto, ct.valor_produto, ct.num_emails, ct.quota_por_conta, ct.comodato, ct.valor_comodato, ct.desconto_promo, ";
	$sSQL .= "	ct.periodo_desconto, ct.bl_banda_download_kbps, ct.id_produto, ";
	$sSQL .= "	pr.id_produto,pr.nome ";
	$sSQL .= "FROM ";
	$sSQL .= "	cbtb_contrato ct, prtb_produto pr ";
	$sSQL .= "WHERE ";
	$sSQL .= "ct.id_cliente_produto = '$id_cliente_produto' ";
	$sSQL .= "AND ct.id_produto = pr.id_produto ";

	$contrato = $this->bd->obtemUnicoRegistro($sSQL);

	////echo "SQL: $sSQL <br>";

	$this->tpl->atribui("data_contratacao", $contrato["data_contratacao"]);
	$this->tpl->atribui("vigencia", $contrato["vigencia"]);
	$this->tpl->atribui("valor_contrato", $contrato["valor_contrato"]);
	$this->tpl->atribui("tipo_produto", $contrato["tipo_produto"]);
	$this->tpl->atribui("valor_produto", $contrato["valor_produto"]);
	$this->tpl->atribui("banda_kbps", $contrato["bl_banda_download_kbps"]);
	$this->tpl->atribui("id_produto", $contrato["id_produto"]);
	$this->tpl->atribui("nome_produto", $contrato["nome"]);

	$sSQL  = "SELECT * FROM cltb_cliente WHERE id_cliente = '$id_cliente' ";

	$cli = $this->bd->obtemUnicoRegistro($sSQL);


	$valor_extenso = $this->extenso($contrato["valor_contrato"]);
	$hoje_extenso = $this->escreveData($hoje);
	$data_extenso = $this->escreveData($contrato["data_contratacao"]);
	$this->tpl->atribui("data_extenso",$data_extenso);
	$this->tpl->atribui("valor_extenso",$valor_extenso);
	$this->tpl->atribui("hoje_extenso",$hoje_extenso);
	$this->tpl->atribui("cli",$cli);
	$this->tpl->atribui("tipo_produto",$tipo_produto);

	if ($tipo_produto == "BL"){

		$arquivo_contrato = "../../contratos/contrato_padrao_BL.html";

	}else if ($tipo_produto == "D"){

		$arquivo_contrato = "../../contratos/contrato_padrao_D.html";

	}else if ($tipo_produto == "H"){

		$arquivo_contrato = "../../contratos/contrato_padrao_H.html";

	}


		$this->arquivoTemplate = $arquivo_contrato;

		//$this->contratoHTML($nome_provedor,$localidade,cnpj_provedor,$data_contratacao,$vigencia,$valor_contrato,$tipo_produto,$valor_produto,$banda_kbps,$id_produto,$nome_produto,$valor_extenso,$hoje_extenso,$data_extenso,$arquivo_contrato);

	$hoje = date("Y-m-d");
	
	$ph = new MUtils;
	
	$_image_path = MUtils::getPwd();
	$host = "http://dev.mosman.com.br";
	$image_path = $host.$_image_path."/template/default/images";
	$sSQL = "SELECT path_contrato FROM pftb_preferencia_cobranca WHERE id_provedor = '1'";
	
	$provedor = $this->prefs->obtem("total");
	$path = $provedor["path_contrato"];
	//$_path = $this->bd->obtemUnicoRegistro($sSQL);
	//$path = $_path["path_contratos"];
	
	$nome_arq = $path."contrato-".$id_cliente_produto."-".$contrato["data_contratacao"].".html";
	$fd = fopen($nome_arq,"w");
	
	//////echo "path: $_path - $path<br>";

	//$arq = explode("/",$arquivo_contrato);
	//$arq = $arq[count($arq)-1];
	$arq = $arquivo_contrato;

	//$image_path = $path."/template/default/images";
	//////echo "<BR>IMAGE PATH".$image_path ."<br>";

	$this->tpl->atribui("path",$image_path);
	$arquivo = $path."/".$arq;
	$arqtmp = $this->tpl->obtemPagina($arq);

	fwrite($fd,$arqtmp);
	
	fclose($fd);
	
	return;
	
	
	

}

public function extenso($valor=0, $maiusculas=false) { 

	$rt = null;
    // verifica se tem virgula decimal 
    if (strpos($valor,",") > 0) 
    { 
      // retira o ponto de milhar, se tiver 
      $valor = str_replace(".","",$valor); 

      // troca a virgula decimal por ponto decimal 
      $valor = str_replace(",",".",$valor); 
    } 

        $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"); 
        $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"); 

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"); 
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"); 
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"); 
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"); 

        $z=0; 

        $valor = number_format($valor, 2, ".", "."); 
        $inteiro = explode(".", $valor); 
        for($i=0;$i<count($inteiro);$i++) 
                for($ii=strlen($inteiro[$i]);$ii<3;$ii++) 
                        $inteiro[$i] = "0".$inteiro[$i]; 

        $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2); 
        for ($i=0;$i<count($inteiro);$i++) { 
                $valor = $inteiro[$i]; 
                $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]]; 
                $rd = ($valor[1] < 2) ? "" : $d[$valor[1]]; 
                $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : ""; 

                $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru; 
                $t = count($inteiro)-1-$i; 
                $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : ""; 
                if ($valor == "000")$z++; elseif ($z > 0) $z--; 
                if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 
                if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r; 
        } 

         if(!$maiusculas){ 
                          return($rt ? $rt : "zero"); 
         } elseif($maiusculas == "2") { 
                          return (strtoupper($rt) ? strtoupper($rt) : "Zero"); 
         } else { 
                          return (ucwords($rt) ? ucwords($rt) : "Zero"); 
         } 
         
        

}

public function escreveData($data)  {  
	list($ano,$mes,$dia) = explode("-",$data);
	$mes_array = array("janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"); 
	return $dia ." de ". $mes_array[(int)$mes-1] ." de ". $ano;

}

public function days_diff($date_ini, $date_end, $round = 1) { 
    $date_ini = strtotime($date_ini); 
    $date_end = strtotime($date_end); 

    $date_diff = ($date_end - $date_ini) / 86400; 

    if($round != 0) {
        return floor($date_diff); 
    } else {
        return $date_diff; 
    }
} 




	
	public function __destruct() {
			parent::__destruct();
	}


}
?>
