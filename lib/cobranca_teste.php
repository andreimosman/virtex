<?

	// Sistema de contratação de produtos e resumo de cobrança
			
			if( ! $this->privPodeLer("_CLIENTES_COBRANCA") ) {
				$this->privMSG();
				return;
			}	
			
			
			$rotina = @$_REQUEST["rotina"];
			$acao = @$_REQUEST["acao"];
			$email_igual = @$_REQUEST['email_igual'];

			
			$this->tpl->atribui("rotina",$rotina);
			$this->arquivoTemplate = "cliente_cobrancas.html";
			
			$erros = array();
			
			if( !$rotina ){ 
			$rotina = "contratar";
			}
			if( $rotina == "resumo" ) {
				$id_cliente = @$_REQUEST['id_cliente'];
				
				$sSQL  = "SELECT ";
				$sSQL .= "  ct.id_cliente_produto, ct.vigencia, ct.data_contratacao, ct.valor_contrato, ct.status, ct.tipo_produto, ";
				$sSQL .= "  cp.id_cliente_produto, cp.id_cliente, cp.id_produto, cp.dominio ";
				$sSQL .= "FROM ";
				$sSQL .= "  cbtb_cliente_produto cp INNER JOIN cbtb_contrato ct ";
				$sSQL .= "USING( id_produto ) ";
				$sSQL .= "WHERE cp.id_cliente='$id_cliente' AND cp.excluido = 'f'";
				
				$produtos = $this->bd->obtemRegistros($sSQL);
						
				for($i=0;$i<count($produtos);$i++) {
			
					$id_cp = $produtos[$i]["id_cliente_produto"];
						   
					$sSQL  = "SELECT ";
					$sSQL .= "	username, dominio, tipo_conta, id_conta ";
					$sSQL .= "FROM ";
					$sSQL .= "	cntb_conta ";
					$sSQL .= "WHERE ";
					$sSQL .= "	id_cliente_produto = '$id_cp'";
						   
					
					$this->tpl->atribui("id_cliente",$id_cliente);
						   
					$contas = $this->bd->obtemRegistros($sSQL);
						   
					$produtos[$i]["contas"] = $contas;
					
				}
			
			
				$this->obtemPR($id_cliente);
	
			

				$hoje = date("d/m/Y");
				$id_cliente = @$_REQUEST["id_cliente"];
				$tipo_lista = @$_REQUEST["tipo_lista"];

				if ($tipo_lista == 'tudo'){

					$sSQL  = "SELECT ";
					$sSQL .= "f.id_cliente_produto, to_char(f.data, 'DD/mm/YYYY') as data_conv,f.data, f.valor, f.observacoes,f.descricao, to_char(f.reagendamento, 'DD/mm/YYYY') as reagendamento, f.pagto_parcial, ";
					$sSQL .= "to_char(f.data_pagamento, 'DD/mm/YYYY') as data_pagamento, f.desconto, f.acrescimo, f.valor_pago, ";
					$sSQL .= "c.id_cliente_produto, c.id_cliente, ct.id_cobranca, ";
					$sSQL .= "CASE WHEN (f.data < CAST(now() as date) AND f.status='A') OR (f.reagendamento < CAST(now() as date) AND f.status='R') ";
					$sSQL .= "THEN 'S' ELSE ";
					$sSQL .= "CASE WHEN f.reagendamento is not null AND f.status != 'P' ";
					$sSQL .= "THEN 'G' ELSE f.status ";
					$sSQL .= "END ";
					$sSQL .= "END as extstatus ";
					$sSQL .= "FROM ";
					$sSQL .= "cbtb_faturas f, cbtb_cliente_produto c, cbtb_contrato ct ";
					$sSQL .= "WHERE ";
					$sSQL .= "id_cliente = '$id_cliente' ";
					$sSQL .= "AND ";
					$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
					$sSQL .= "AND ct.id_cliente_produto = f.id_cliente_produto ";
					$sSQL .= "ORDER BY f.data ASC ";


				
					$largura = "700";
					$altura = "400";

					$this->tpl->atribui("largura",$largura);
					$this->tpl->atribui("altura",$altura);




					$lista_faturas = $this->bd->obtemRegistros($sSQL);


					$this->obtemPR($id_cliente);

					$estornar_pag = '' ;

					if( ! $this->privPodeLer("_ADMIN_ESTORNO") ) {

						$estornar_pag = 'nao' ;

					}else if( ! $this->privPodeGravar("_ADMIN_ESTORNO") && ! $this->privPodeLer("_ADMIN_ESTORNO") ) {

						$estornar_pag = 'nao' ;	


					}else if ( $this->privPodeGravar("_ADMIN_ESTORNO") &&  $this->privPodeLer("_ADMIN_ESTORNO") ){

						$estornar_pag = 'sim' ;

					}

					$this->tpl->atribui("estornar_pag",$estornar_pag);		


					$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
					$cliente = $this->bd->obtemUnicoRegistro($sSQL);


					$this->tpl->atribui("hoje",$hoje);
					$this->tpl->atribui("cliente",$cliente);
					$this->tpl->atribui("id_cliente", $id_cliente);
					$this->tpl->atribui("lista_faturas",$lista_faturas);

					$this->arquivoTemplate = "cliente_cobranca_resumo_faturas.html";
					return;
				}else if ($tipo_lista == 'contratos'){
	
					$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
					$cliente = $this->bd->obtemUnicoRegistro($sSQL);


					$sSQL  = "SELECT ";
					$sSQL .= "	ct.id_cliente_produto, ct.data_contratacao, ct.vigencia, ct.id_produto, ct.tipo_produto, ct.valor_contrato, ct.status, ";
					$sSQL .= "	cl.id_cliente_produto, cl.id_cliente,  ";
					$sSQL .= "	pr.id_produto, pr.nome ";
					$sSQL .= "FROM ";																	  
					$sSQL .= "	cbtb_contrato ct, cbtb_cliente_produto cl, prtb_produto pr  ";
					$sSQL .= "WHERE ";
					$sSQL .= "	cl.id_cliente_produto = ct.id_cliente_produto  AND cl.id_cliente = '$id_cliente' AND ct.id_produto = pr.id_produto ";
					$sSQL .= " ORDER BY ct.data_contratacao DESC ";

					$lista_contrato = $this->bd->obtemRegistros($sSQL);

					for($i=0;$i<count($lista_contrato);$i++) {

						$id_cp = $lista_contrato[$i]["id_cliente_produto"];

						$dSQL  = "SELECT ";
						$dSQL .= "	username, dominio, tipo_conta, id_conta , id_cliente_produto ";
						$dSQL .= "FROM ";
						$dSQL .= "	cntb_conta ";
						$dSQL .= "WHERE ";
						$dSQL .= "	id_cliente_produto = '$id_cp'";
						$dSQL .= " AND conta_mestre = true ";


						$contas = $this->bd->obtemRegistros($dSQL);

						$lista_contrato[$i]["conta"] = $contas;

					}	

						$this->tpl->atribui("conta",@$contas);
						$this->tpl->atribui("lista_contrato",$lista_contrato);
						$this->tpl->atribui("cliente",$cliente);
						$this->tpl->atribui("id_cliente", $id_cliente);

						$this->arquivoTemplate = "cliente_contratos_todos.html";
						return;



				}else{
		
					$sSQL  = "SELECT ";
					$sSQL .= "f.id_cliente_produto, to_char(f.data, 'DD/mm/YYYY') as data_conv,f.data, f.valor, f.observacoes,f.descricao, to_char(f.reagendamento, 'DD/mm/YYYY') as reagendamento, f.pagto_parcial, ";
					$sSQL .= "to_char(f.data_pagamento, 'DD/mm/YYYY') as data_pagamento, f.desconto, f.acrescimo, f.valor_pago, ";
					$sSQL .= "c.id_cliente_produto, c.id_cliente, ct.id_cobranca, ";
					$sSQL .= "CASE WHEN (f.data < CAST(now() as date) AND f.status='A') OR (f.reagendamento < CAST(now() as date) AND f.status='R') ";
					$sSQL .= "THEN 'S' ELSE ";
					$sSQL .= "CASE WHEN f.reagendamento is not null AND f.status != 'P' ";
					$sSQL .= "THEN 'G' ELSE f.status ";
					$sSQL .= "END ";
					$sSQL .= "END as extstatus ";
					$sSQL .= "FROM ";
					$sSQL .= "cbtb_faturas f, cbtb_cliente_produto c, cbtb_contrato ct ";
					$sSQL .= "WHERE ";
					$sSQL .= "id_cliente = '$id_cliente' ";
					$sSQL .= "AND ";
					$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
					$sSQL .= "AND ct.id_cliente_produto = f.id_cliente_produto ";
					$sSQL .= "AND (f.status = 'A' OR f.status = 'R') ";
					$sSQL .= "AND f.data < now() + interval '10 day' ";
					$sSQL .= "ORDER BY f.data ASC ";

					$lista_faturas = $this->bd->obtemRegistros($sSQL);

					$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
					$cliente = $this->bd->obtemUnicoRegistro($sSQL);


					$sSQL  = "SELECT ";
					$sSQL .= "	ct.id_cliente_produto, ct.data_contratacao, ct.vigencia, ct.id_produto, ct.tipo_produto, ct.valor_contrato, ct.status, ";
					$sSQL .= "	cl.id_cliente_produto, cl.id_cliente,  ";
					$sSQL .= "	pr.id_produto, pr.nome ";
					$sSQL .= "FROM ";																	  
					$sSQL .= "	cbtb_contrato ct, cbtb_cliente_produto cl, prtb_produto pr  ";
					$sSQL .= "WHERE ";
					$sSQL .= "	cl.id_cliente_produto = ct.id_cliente_produto  AND cl.id_cliente = '$id_cliente' AND ct.id_produto = pr.id_produto AND ct.status = 'A' ";
					$sSQL .= " ORDER BY ct.data_contratacao DESC ";

					$lista_contrato = $this->bd->obtemRegistros($sSQL);

					for($i=0;$i<count($lista_contrato);$i++) {

						$id_cp = $lista_contrato[$i]["id_cliente_produto"];

						$dSQL  = "SELECT ";
						$dSQL .= "	username, dominio, tipo_conta, id_conta , id_cliente_produto ";
						$dSQL .= "FROM ";
						$dSQL .= "	cntb_conta ";
						$dSQL .= "WHERE ";
						$dSQL .= "	id_cliente_produto = '$id_cp'";
						$dSQL .= " AND conta_mestre = true ";


						$contas = $this->bd->obtemRegistros($dSQL);

						$lista_contrato[$i]["conta"] = $contas;

					}	


					$this->tpl->atribui("conta",@$contas);
					$this->tpl->atribui("lista_contrato",$lista_contrato);
					$this->tpl->atribui("cliente",$cliente);
					$this->tpl->atribui("id_cliente", $id_cliente);
					$this->tpl->atribui("lista_faturas",$lista_faturas);


					$largura = "700";
					$altura = "400";

					$this->tpl->atribui("largura",$largura);
					$this->tpl->atribui("altura",$altura);


					$this->tpl->atribui("produtos",$produtos);
					$this->arquivoTemplate = "cliente_cobranca_resumo.html";
				
				}

			} else if( $rotina == "contratar" ) {// fim ROTINA = resumo
			
				if( ! $this->privPodeGravar("_CLIENTES_COBRANCA") ) {
					$this->privMSG();
					return;
				}	
								
				$enviando = false;
				$exibeForm = true;
				
				$id_cliente = @$_REQUEST["id_cliente"];
				
				$this->obtemPR($id_cliente);
												
				
				if($acao == "cad" ) {
					$enviando = true;
				}
				
				if( $acao == "conf" ) {
					require_once( PATH_LIB . "/dede2.php" );
					return;
				}

				if( $enviando ) {
				
					// Pega dominio padrão 
					
					$lista_dominop = $this->prefs->obtem("geral");
					

					// Valida os dados
					//DOMINIO PADRAO
					$dominioPadrao = $lista_dominop['dominio_padrao'];
					
					$sSQL  = "SELECT ";
					$sSQL .= "   username ";
					$sSQL .= "FROM ";
					$sSQL .= "   cntb_conta ";
					$sSQL .= "WHERE ";
					$sSQL .= "   username = '".$this->bd->escape(trim(@$_REQUEST["username"]))."' ";
					$sSQL .= "   and tipo_conta = '". $this->bd->escape(trim(@$_REQUEST["tipo"])) ."' ";
					$sSQL .= "   and dominio = '".$dominioPadrao."' ";
					$sSQL .= "ORDER BY ";
					$sSQL .= "   username ";
					
					$lista_user = $this->bd->obtemUnicoRegistro($sSQL);

					if(count($lista_user) && $lista_user["username"]){
						// ver como processar
						$erros[] = "Já existe outra conta cadastrada com esse username";
					}
					
					// Se nao tiver erros faz o cadastro
					if( !count($erros) ) {
// COMEÇA O TRATAMENTO DE ERROS

						$this->bd->begin();
						while(true){
							// pega id_cliente_produto
							$id_cliente_produto = $this->bd->proximoID("cbsq_id_cliente_produto");

							// Insere no banco de dados

							$sSQL  = "INSERT INTO ";
							$sSQL .= "   cbtb_cliente_produto( ";
							$sSQL .= "      id_cliente_produto,id_cliente, id_produto ) ";
							$sSQL .= "   VALUES (";
							$sSQL .= "     '" . $id_cliente_produto . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["id_cliente"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["id_produto"]) . "' ";
							$sSQL .= "     )";						
							$this->bd->consulta($sSQL);  
							if ($this->bd->obtemErro())	break;


							$username = @str_replace(" ","",$_REQUEST["username"]);
							$dominio = @str_replace("/","",$_REQUEST["dominio"]);
							$tipo_conta = @$_REQUEST["tipo_conta"];
							$dominio_hospedagem = @str_replace("/","",$_REQUEST["dominio_hospedagem"]);

							$senhaCr = $this->criptSenha($this->bd->escape(trim(@$_REQUEST["senha"])));

							$id_conta = $this->bd->proximoID("cnsq_id_conta");

							$sSQL  = "INSERT INTO ";
							$sSQL .= "   cntb_conta( ";
							$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript, status) ";
							$sSQL .= "   VALUES (";
							$sSQL .= "			'".$id_conta."', ";
							$sSQL .= "     '" . $username . "', ";
							$sSQL .= "     '" . $dominioPadrao . "', ";
							$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo"])) . "', ";
							$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
							$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
							$sSQL .= "     '" .	$id_cliente_produto . "', ";
							$sSQL .= "     '" . $senhaCr . "', ";
							$sSQL .= "     'A' )";						

							$this->bd->consulta($sSQL);  
							if ($this->bd->obtemErro())	break;
							
							$operacao = "NOVA_CONTA";
							$this->logConta("1", $id_cliente_produto, @$_REQUEST["tipo"], @$_REQUEST["username"], $operacao, $dominioPadrao);


							if ($email_igual == "1"){

								$prefs = $this->prefs->obtem("total");


								$id_conta = $this->bd->proximoID("cnsq_id_conta");

								$sSQL  = "INSERT INTO ";
								$sSQL .= "   cntb_conta( ";
								$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript, status) ";
								$sSQL .= "   VALUES (";
								$sSQL .= "			'". $id_conta. "', ";
								$sSQL .= "     '" . $username . "', ";
								$sSQL .= "     '" . $dominioPadrao . "', ";
								$sSQL .= "     'E', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
								$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
								$sSQL .= "     '" .	$id_cliente_produto . "', ";
								$sSQL .= "     '" . $senhaCr . "', ";
								$sSQL .= "     'A' )";						

								$this->bd->consulta($sSQL);  
								if ($this->bd->obtemErro())	break;

								$id_produto = @$_REQUEST['id_produto'];
								$prod = $this->obtemProduto($id_produto);	

								if ($prod["quota_por_conta"] == "" || !$prod ){
									$quota = "0";
								}else {
									$quota = $prod["quota_por_conta"];
								}

								$sSQL  = "INSERT INTO ";
								$sSQL .= "	cntb_conta_email( ";
								$sSQL .= "		username, tipo_conta, dominio, quota, email) ";
								$sSQL .= "VALUES (";
								$sSQL .= "     '" . $username . "', ";
								$sSQL .= "     'E', ";
								$sSQL .= "     '" . $dominioPadrao . "', ";
								$sSQL .= "     '$quota', ";
								$sSQL .= "     '". $username."@". $dominioPadrao ."' ";
								$sSQL .= " )";
								$this->bd->consulta($sSQL);
								if ($this->bd->obtemErro())	break;
								
								$server = $this->prefs->obtem("geral","mail_server");

								$this->spool->adicionarEmail($server, $id_conta, $username, $dominioPadrao);

							}


							$tipo = @$_REQUEST["tipo"];

							//PEGA CAMPOS COMUNS EM cftb_preferencias

							$prefs = $this->prefs->obtem();

							switch($tipo) {
								case 'D':

									$username = @str_replace(" ","",$_REQUEST["username"]);
									$tipo_conta = @$_REQUEST["tipo"];
									$dominio = $prefs["geral"]["dominio_padrao"];
									$foneinfo = @$_REQUEST["foneinfo"];

									$sSQL  = "INSERT INTO ";
									$sSQL .= "cntb_conta_discado ";
									$sSQL .= "( ";
									$sSQL .= "username, tipo_conta, dominio, foneinfo ";
									$sSQL .= ")VALUES ( ";
									$sSQL .= "'$username', '$tipo_conta', '$dominio', '$foneinfo' )";

									$this->bd->consulta($sSQL);
									if ($this->bd->obtemErro())	break;
									
									$this->tpl->atribui("foneinfo",$foneinfo);

								break;	
								case 'BL':

									// PRODUTO BANDA LARGA
									$tipo_de_ip = $this->bd->escape(trim(@$_REQUEST["selecao_ip"]));
									if($tipo_de_ip == "A"){
										$nas = $this->obtemNAS($_REQUEST["id_nas"]);
										if( $nas["tipo_nas"] == "I" ) {
											 // Cadastrar REDE em cntb_conta
											 $rede_disponivel = $this->obtemRede($nas["id_nas"]);
											 $rede_disp = $rede_disponivel["rede"];
											 $ip_disp = "NULL";
										} else if( $nas["tipo_nas"] == "P" ) {
											 // Cadastrar IPADDR em cntb_conta
											 $ip_disponivel = $this->obtemIP($nas["id_nas"]);
											 $ip_disp = $ip_disponivel["ipaddr"];
											 $rede_disp = "NULL";
										}

									} else if ($tipo_de_ip == "M"){


										$erro = array();

										$id_nas = @$_REQUEST["id_nas"];
										$endereco_ip = @$_REQUEST["endereco_ip"];
										$nas = $this->obtemNAS($_REQUEST["id_nas"]);

										$sSQL = "SELECT rede FROM cftb_rede WHERE rede >> '$endereco_ip' or rede = '$endereco_ip'	";
										$_rede = $this->bd->obtemUnicoRegistro($sSQL);
										$rede = @$_rede["rede"];

										if( !$rede ) {
											 $erro = "Rede não cadastrada no sistema.";
										} else {
											 $sSQL = "SELECT rede FROM cftb_nas_rede WHERE rede = '$rede' AND id_nas = '$id_nas'";
											 $nas_rede = $this->bd->obtemUnicoRegistro($sSQL);

											 if( !count($nas_rede) ) {
													$erro = "Rede não disponível para este NAS";
											 } else {
										// verificar de acordo com o tipo do nas
													$sSQL = "SELECT username,rede FROM cntb_conta_bandalarga WHERE ";
													if ($nas["tipo_nas"] == "I"){
													 $sSQL .= " rede = '$rede' ";

													}else if ($nas["tipo_nas"] == "P"){
															$sSQL .= " ipaddr = '$endereco_ip' ";

													}
													$rede_bl = $this->bd->obtemUnicoRegistro($sSQL);
													if(count($rede_bl)){
															$erro = "Endereço utilizado por outro cliente (".$rede_bl["username"].")";
													} 

											}
										}




										if (!@$erro){

											if ($nas["tipo_nas"] == "I"){

												$ip_disp = "NULL";
												$rede_disp = $rede;

											} else if ($nas["tipo_nas"] == "P"){

												$rede_disp = "NULL";
												$ip_disp = $endereco_ip;

											}

										}else{

											$this->tpl->atribui("mensagem",$erro);
											$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=cobranca&id_cliente=$id_cliente");
											$this->tpl->atribui("target","_top");

											$this->arquivoTemplate="msgredirect.html";
											return;

										}	




									}

									$redirecionar = @$_REQUEST["redirecionar"];

									if($redirecionar == "true"){

										$ip_externo = $this->obtemIPExterno($_REQUEST["id_nas"]);

										if($nas["tipo_nas"] == "P"){

											$ipaddr = $ip_disp;

										}else if ($nas["tipo_nas"] == "I"){

											$ipaddr = $rede_disp;

										}

										$username = @str_replace(" ","",$_REQUEST["username"]);
										$tipo_conta = @$_REQUEST["tipo"];

										$dSQL = "SELECT dominio_padrao FROM pftb_preferencia_geral WHERE id_provedor = '1' ";
										$dom = $this->bd->obtemUnicoRegistro($dSQL);

										$dominio = $dom["dominio_padrao"];


										$sSQL = "SELECT id_conta FROM cntb_conta WHERE username = '$username' AND tipo_conta = 'BL' AND dominio = '$dominio' ";
										$_id_conta = $this->bd->obtemUnicoRegistro($sSQL);

										$id_conta = $_id_conta["id_conta"];
										$_ip_externo = $ip_externo["ip_externo"];

										$this->spool->adicionaIpExterno($_REQUEST["id_nas"],$_ip_externo,$ipaddr,$id_conta);


									}else{
										$ip_externo = "null";

									}


									if ($ip_externo != "null"){

										$ip_externo = "'".$ip_externo["ip_externo"]."'";
									}


									if($rede_disp != "NULL"){

										$rede_disp = "'".$rede_disp."'";



									}

									if($ip_disp !="NULL"){

										$ip_disp = "'".$ip_disp."'";


									}

									$id_produto = $this->bd->escape(@$_REQUEST["id_produto"]);
									$bandaUp_dow = $this->obtemDowUp($id_produto);
									$MAC = @$_REQUEST["mac"];

									if($MAC ==""){
										$_MAC = "NULL";
									}else {
										$_MAC = "'".$MAC."'";
									}


									// INSERE EM CNTB_CONTA_BANDALARGA
									$sSQL  = "INSERT INTO ";
									$sSQL .= "   cntb_conta_bandalarga( ";
									$sSQL .= "      username, ";
									$sSQL .= "      tipo_conta, ";
									$sSQL .= "      dominio, ";
									$sSQL .= "      id_pop, ";
									$sSQL .= "      tipo_bandalarga, ";
									$sSQL .= "      ipaddr, ";
									$sSQL .= "      rede, ";
									$sSQL .= "      upload_kbps, ";
									$sSQL .= "      download_kbps, ";
									$sSQL .= "      status, ";
									$sSQL .= "      id_nas, ";
									$sSQL .= "      mac, ";
									$sSQL .= "		ip_externo ";
									$sSQL .= ") ";
									$sSQL .= "   VALUES (";
									$sSQL .= "     '" . $username  . "', ";
									$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo"])). "', ";
									$sSQL .= "     '" . $dominioPadrao . "', ";
									$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_pop"])) . "', ";
									$sSQL .= "     '" . $nas["tipo_nas"] . "', ";
									$sSQL .= "     "  . $ip_disp . ", ";
									$sSQL .= "     "  . $rede_disp . ", ";
									$sSQL .= "     '" . $bandaUp_dow["banda_upload_kbps"] . "', ";
									$sSQL .= "     '" . $bandaUp_dow["banda_download_kbps"] . "', ";
									$sSQL .= "     'A', ";
									$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_nas"])) . "', ";
									$sSQL .= "     "  . $_MAC .", ";
									$sSQL .= "	   "  . $ip_externo ."  ";
									$sSQL .= "     )";						

									$this->bd->consulta($sSQL);  
									if ($this->bd->obtemErro())	break;
									
									break;

								case 'H':
									// PRODUTO HOSPEDAGEM
									$prefs = $this->prefs->obtem("total");								

									$username = @str_replace(" ","",$_REQUEST["username"]);
									$tipo_dominio = @$_REQUEST["tipo_dominio"];
									$tipo_conta = @$_REQUEST["tipo"];
									$dominio = $prefs["dominio_padrao"];
									$tipo_hospedagem = @$_REQUEST["tipo_hospedagem"];
									$senha_cript = $this->criptSenha(@$_REQUEST["senha"]);
									$uid = $prefs["hosp_uid"];
									$gid = $prefs["hosp_gid"];
									$home = $prefs["hosp_base"];
									$shell = "/bin/false";
									$dominio_hospedagem = @str_replace("/","",$_REQUEST["dominio_hospedagem"]);
									$server = $prefs["hosp_server"];


									$sSQL  = "select * from cntb_conta where username = $username AND tipo_conta = $tipo_conta AND dominio = $dominio";
									$prep = $this->bd->obtemRegistros($sSQL);



									if ($tipo_hospedagem == "D"){

										$sSQL  = "INSERT INTO ";
										$sSQL .= " cntb_conta_hospedagem ( ";
										$sSQL .= "		username, tipo_conta, dominio, tipo_hospedagem, senha_cript, uid, gid, home, shell, dominio_hospedagem ";
										$sSQL .= ") VALUES ( ";
										$sSQL .= " 		'$username', '$tipo_conta', '$dominio', '$tipo_hospedagem', '$senha_cript', '$uid', '$gid', '$home/$dominio_hospedagem', '$shell', '$dominio_hospedagem' ";
										$sSQL .= ") ";

										$ns1 = $this->prefs->obtem("geral","hosp_ns1");
										$ns2 = $this->prefs->obtem("geral","hosp_ns2");

										$this->spool->configuraDNS($ns1, "N1", $id_conta, $dominio_hospedagem);
										$this->spool->configuraDNS($ns2, "N2", $id_conta, $dominio_hospedagem);


									} else {

										$sSQL  = "INSERT INTO ";
										$sSQL .= " cntb_conta_hospedagem ( ";
										$sSQL .= "		username, tipo_conta, dominio, tipo_hospedagem, senha_cript, uid, gid, home, shell, dominio_hospedagem ";
										$sSQL .= ") VALUES ( ";
										$sSQL .= " 		'$username', '$tipo_conta', '$dominio', '$tipo_hospedagem', '$senha_cript', '$uid', '$gid', '$home/USUARIOS/$username', '$shell', '$dominio_hospedagem' ";
										$sSQL .= ") ";

									}




									$this->bd->consulta($sSQL);
									if ($this->bd->obtemErro())	break;
									
									$this->spool->hospedagemAdicionaRede($server,$id_conta,$tipo_hospedagem,$username,$dominio,$dominio_hospedagem);
									break;


							}



						/*<<=====INICIO DA PARTE DE CONTRATAÇÃO=====>>*/

						//Cadastra o contrato do cliente.
						$id_produto = @$_REQUEST["id_produto"];

						//Informações sobre o provedor

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
						$tipo_produto = @$_REQUEST["tipo"];
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
						$vencimento = @$_REQUEST["dia_vencimento"];


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


						$valor_contrato = number_format($valor_produto + $valor_comodato, 2, '.', '');					
						$valor_cont_temp = $valor_contrato;
						//Diminui o desconto no valor real do contrato caso este tenha mesmo período que a vigência do contrato
						if ($periodo_desconto >= $vigencia) $valor_contrato -= $desconto_promo;


						$sSQL =  "INSERT INTO cbtb_contrato ( ";
						$sSQL .= "	id_cliente_produto, data_contratacao, vigencia, data_renovacao, valor_contrato, id_cobranca, status, ";
						$sSQL .= "	tipo_produto, valor_produto, num_emails, quota_por_conta, tx_instalacao, comodato, valor_comodato, ";
						$sSQL .= "	desconto_promo, periodo_desconto, id_produto, cod_banco, agencia, num_conta, carteira, ";
						$sSQL .= "	convenio, cc_vencimento, cc_numero, cc_operadora, db_banco, db_agencia, db_conta, carencia, vencimento";
						$sSQL .= ") VALUES ( ";
						$sSQL .= "	$id_cliente_produto, '$data_contratacao', $vigencia, '$data_renovacao', $valor_contrato, $id_cobranca, '$status', ";
						$sSQL .= "	'$tipo_produto', $valor_produto, $num_emails, $quota, $tx_instalacao, '$comodato', $valor_comodato, ";
						$sSQL .= "	$desconto_promo, $periodo_desconto, $id_produto, $cod_banco, $agencia, $num_conta, $carteira, ";
						$sSQL .= "	$convenio, '$cc_vencimento', '$cc_numero', '$cc_operadora', $db_banco, $db_agencia, $db_conta, $carencia, $vencimento ";
						$sSQL .= ")";
						$this->bd->consulta($sSQL);	//Salva as configurações de contrato
						if ($this->bd->obtemErro())	break;

						switch($tipo_produto) {
							case 'BL':
								$sSQL = "SELECT * FROM prtb_produto_bandalarga WHERE id_produto = $id_produto";
								$info_ad_produto = $this->bd->obtemUnicoRegistro($sSQL);

								$bl_banda_upload_kbps = $info_ad_produto["banda_upload_kbps"];
								$bl_banda_download_kbps = $info_ad_produto["banda_download_kbps"];
								$bl_franquia_trafego_mensal_gb = $info_ad_produto["franquia_trafego_mensal_gb"];
									$bl_valor_trafego_adicional_gb = $info_ad_produto["valor_trafego_adicional_gb"];

									$sSQL =  "UPDATE cbtb_contrato SET";
									$sSQL .= "	bl_banda_upload_kbps = $bl_banda_upload_kbps, ";
								$sSQL .= "	bl_banda_download_kbps = $bl_banda_download_kbps, ";
								$sSQL .= "	bl_franquia_trafego_mensal_gb = $bl_franquia_trafego_mensal_gb,";
								$sSQL .= "	bl_valor_trafego_adicional_gb = $bl_valor_trafego_adicional_gb ";
								$sSQL .= "WHERE id_cliente_produto = $id_cliente_produto";
								break;
								
								
							case 'D':
								$sSQL = "SELECT * FROM prtb_produto_discado WHERE id_produto = $id_produto";
								$info_ad_produto = $this->bd->obtemUnicoRegistro($sSQL);

								$disc_franquia_horas = $info_ad_produto["franquia_horas"];
								$disc_permitir_duplicidade = $info_ad_produto["permitir_duplicidade"];
								$disc_valor_hora_adicional = $info_ad_produto["valor_hora_adicional"];

								$sSQL =  "UPDATE cbtb_contrato SET";
								$sSQL .= "	disc_franquia_horas = $disc_franquia_horas,";
								$sSQL .= "	disc_permitir_duplicidade = '$disc_permitir_duplicidade',";
								$sSQL .= "	disc_valor_hora_adicional = $disc_valor_hora_adicional ";
								$sSQL .= "WHERE id_cliente_produto = $id_cliente_produto";
								break;

							case 'H':
								$sSQL = "SELECT * FROM prtb_produto_hospedagem WHERE id_produto = $id_produto";
								$info_ad_produto = $this->bd->obtemUnicoRegistro($sSQL);


								$hosp_dominio = $info_ad_produto["dominio"];
								$hosp_franquia_em_mb = $info_ad_produto["franquia_em_mb"];
								$hosp_valor_mb_adicional = $info_ad_produto["valor_mb_adicional"];


								$sSQL =  "UPDATE cbtb_contrato SET";
								$sSQL .= "	hosp_dominio = '$hosp_dominio',";
								$sSQL .= "	hosp_franquia_em_mb = $hosp_franquia_em_mb, ";
								$sSQL .= "	hosp_valor_mb_adicional = $hosp_valor_mb_adicional ";
								$sSQL .= "WHERE id_cliente_produto = $id_cliente_produto";
								break;
						}


						$this->bd->consulta($sSQL);
						if ($this->bd->obtemErro())	break;
						
						$this->contratoHTML($id_cliente,$id_cliente_produto,$tipo_produto);


						/*<<=====FIM DA PARTE DE CONTRATAÇÃO=====>>*/


						/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/
						$_id_carne = $this->bd->proximoID('cbsq_id_carne');
						$q = 0;

						$sSQL  = "INSERT INTO cbtb_carne ";
						$sSQL .= "(id_carne, data_geracao,id_cliente_produto,valor,status,vigencia,id_cliente) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$_id_carne','$data_contratacao','$id_cliente_produto','$valor_contrato','A','$vigencia','$id_cliente') ";

						$this->bd->consulta($sSQL);
						if ($this->bd->obtemErro())	break;
						
						$id_carne = $_id_carne;

						//Cadastro de faturas do contrato.

						$pro_rata = @$_REQUEST["prorata"];

						$dia_vencimento = @$_REQUEST["dia_vencimento"];
						$pri_venc = @$_REQUEST["pri_venc"];

						$qt_desconto = $periodo_desconto;

						$fatura_status = "A";
						$fatura_v_pago = 0;
						$fatura_dt_vencimento="";
						$fatura_obs="";

						$fatura_desc = $info_produto["nome"];

						$fatura_pg_acrescimo = 0;
						$fatura_pg_parcial=0;
						$fatura_vl_pago=0;
						$fatura_desconto=0;

						$fatura_valor = $valor_produto;

						$pos = 0; //Jogar para o próximo mês

						$forma_pagamento = @$_REQUEST["forma_pagamento"];

						list($ca, $cm, $cd) = explode("-", $data_contratacao);

						$arqtmp = tempnam("/tmp","cn-");
						$fd = fopen($arqtmp,"w");

						$estilo = $this->tpl->obtemPagina("../boletos/pc-estilo.html");
						fputs($fd,$estilo,strlen($estilo));

						switch($forma_pagamento) {
							case 'POS':
									if($id_cobranca == 2) { //Tipo de pagamento for carnê

	// CARNÊ POS PAGO - INICIO

												$ini_carne = @$_REQUEST["ini_carne"];
												$data_carne = @$_REQUEST["data_carne"];
												$prorata = @$_REQUEST["prorata"];
												$valor_prorata = @$_REQUEST["valor_prorata"];
												$pri_venc = @$_REQUEST["pri_venc"];

												list($ini_d, $ini_m, $ini_a) = explode("/", $ini_carne);
												list($dat_d, $dat_m, $dat_a) = explode("/", $data_carne);

												$stamp_inicial = mktime(0,0,0, $ini_m, $ini_d, $ini_a);
												$stamp_final = mktime(0,0,0, $dat_m, $dat_d, $dat_a);

												$diferenca_meses = (($stamp_final - $stamp_inicial) / 86400) / 30;

												if($tx_instalacao > 0) {

													$fatura_valor = $tx_instalacao;
													$hoje = date("Y-m-d");

													if (!$pri_venc){
														$fatura_dt_vencimento = $hoje;
													}else{
														$fatura_dt_vencimento = $pri_venc;
													}

													//Calcula a data dos próximos pagamentos de fatura.

													$sSQL  =  "INSERT INTO cbtb_faturas(";
													$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
													$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
													$sSQL .= "	acrescimo, valor_pago, id_carne ";
													$sSQL .= ") VALUES (";
													$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
													$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
													$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
													$sSQL .= ")";

													$this->bd->consulta($sSQL);
													if ($this->bd->obtemErro())	break;
												}

												for($i=0; $i<=floor($diferenca_meses); $i++) {

													//Aplica descontos, caso haja algum período de desconto declarado
													if($qt_desconto > 0) {

														$fatura_desconto = $desconto_promo;
														$qt_desconto--;

													} else {

														$fatura_desconto = 0;

													}

													//Adiciona taxa de instalação na fatura, caso haja.
													if ($i==0) { //Cria primeira fatura pós-paga

															if ($prorata == true){ // pega se existe prorata e soma no valor da primeira fatura

																$fatura_valor = $valor_prorata;
															}

															if($pri_venc != ""){
																list($ini_d, $ini_m, $ini_a) = explode("/", $pri_venc);
																$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));
															}else{
																$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));
															}


													}else{


														$fatura_valor = $valor_cont_temp;

													}

													//Calcula o desconto sobre a fatura.
													$fatura_valor -= $fatura_desconto;

													//Calcula a data dos próximos pagamentos de fatura.
														$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m+$i, $ini_d, $ini_a));

													$sSQL =  "INSERT INTO cbtb_faturas(";
													$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
													$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
													$sSQL .= "	acrescimo, valor_pago, id_carne ";
													$sSQL .= ") VALUES (";
													$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
													$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
													$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
													$sSQL .= ")";

													$this->bd->consulta($sSQL);
													if ($this->bd->obtemErro())	break;
													
													$data = $fatura_dt_vencimento;
													$fatura = $this->carne($id_cliente_produto,$data,$id_cliente,$forma_pagamento);

													if( $i>0 && $i % 3 == 0 ) {
														$new_page = "<hr>";
														fputs($fd,$new_page);
													}


													fputs($fd,$fatura);
												}

	// CARNÊ POS PAGO - FINAL




									} else if ($id_cobranca == 1){ // caso a cobrança seja boleto	

	// BOLETO POS PAGO - INICIO

											$ini_carne = @$_REQUEST["ini_carne"];
											$data_carne = @$_REQUEST["data_carne"];
											$prorata = @$_REQUEST["prorata"];
											$valor_prorata = @$_REQUEST["valor_prorata"];
											$pri_venc = @$_REQUEST["pri_venc"];

											$hoje = date("d/m/Y");
											list ($d,$m,$a) = explode("/",$hoje);
											$data_pri_venc = date("d/m/Y", mktime($m+1,$d,$a));

											$forma_pagamento = "POS";

											if($tx_instalacao > 0) {

												$fatura_valor = $tx_instalacao;
												$_hoje = date("Y-m-d");

												if (!$pri_venc){
													$fatura_dt_vencimento = $_hoje;
												}else{
													$fatura_dt_vencimento = $pri_venc;
												}

												$sSQL =  "INSERT INTO cbtb_faturas(";
												$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
												$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
												$sSQL .= "	acrescimo, valor_pago, id_carne ";
												$sSQL .= ") VALUES (";
												$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
												$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
												$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
												$sSQL .= ")";

												$this->bd->consulta($sSQL);
												if ($this->bd->obtemErro())	break;
												
												$data = $fatura_dt_vencimento;
												$fatura = $this->boleto($id_cliente_produto,$data,$id_cliente,$forma_pagamento);
	// BOLETO POS PAGO - FINAL
											}
											
											
									} else { //Caso a cobrança não seja do tipo carnê nem boleto (carne = 2 e boleto = 1).


										$fatura_valor = $valor_cont_temp;

										//Aplica descontos, caso haja algum período de desconto declarado
										if($qt_desconto > 0) {
											$fatura_desconto = $desconto_promo;
											$qt_desconto--;
										} else
											$fatura_desconto = 0;

										//Adiciona taxa de instalação na fatura, caso haja.
										if($tx_instalacao != 0) {
											$fatura_valor = $tx_instalacao;

											//Calcula a data dos próximos pagamentos de fatura.

											$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));

											$sSQL  =  "INSERT INTO cbtb_faturas(";
											$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
											$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
											$sSQL .= "	acrescimo, valor_pago, id_carne ";
											$sSQL .= ") VALUES (";
											$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
											$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
											$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
											$sSQL .= ")";

											$this->bd->consulta($sSQL);
																						
											if ($this->bd->obtemErro()) break;											
										}

								}									


							break;
							case 'PRE':
							
									
									if($id_cobranca == 2) {	//Tipo pagamento for Carnê.

	// CARNE PRE PAGO - INICIO

												$ini_carne = @$_REQUEST["ini_carne"];
												$data_carne = @$_REQUEST["data_carne"];
												$prorata = @$_REQUEST["prorata"];
												$valor_prorata = @$_REQUEST["valor_prorata"];

												list($ini_d, $ini_m, $ini_a) = explode("/", $ini_carne);
												list($dat_d, $dat_m, $dat_a) = explode("/", $data_carne);

												$stamp_inicial = mktime(0,0,0, $ini_m, $ini_d, $ini_a);
												$stamp_final = mktime(0,0,0, $dat_m, $dat_d, $dat_a);

												$diferenca_meses = (($stamp_final - $stamp_inicial) / 86400) / 30;


												for($i=0; $i<=floor($diferenca_meses); $i++) {									

													$fatura_valor = $valor_cont_temp;

													//Aplica descontos, caso haja algum período de desconto declarado
													if($qt_desconto > 0) {
														$fatura_desconto = $desconto_promo;
														$qt_desconto--; 
													} else
														$fatura_desconto = 0;


													//Adiciona taxa de instalação na fatura, caso haja.
													if ($i==0) { //Cria primeira fatura pré-paga

														//Adiciona-se ao valor da fatura o valor do pro-rata																														
														if ($prorata == true){
															$fatura_valor += $valor_prorata;
															$fatura_valor -= $valor_cont_temp;
														}

														if ($pri_venc) {
															list($d, $m, $a) = explode("/",$pri_venc);
															$fatura_dt_vencimento = $a."-".$m."-".$d;
														}
														//TODO: Procurar função de adição do pro-rata
														if($tx_instalacao > 0) $fatura_valor += $tx_instalacao;

													}else{

														$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));

													}

													//Calcula o desconto sobre a fatura.
													$fatura_valor -= $fatura_desconto;


													$sSQL =  "INSERT INTO cbtb_faturas(";
													$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
													$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
													$sSQL .= "	acrescimo, valor_pago, id_carne ";
													$sSQL .= ") VALUES (";
													$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
													$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
													$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
													$sSQL .= ")";

													$this->bd->consulta($sSQL);
													if ($this->bd->obtemErro())	break;

													$data = $fatura_dt_vencimento;

													$fatura = $this->carne($id_cliente_produto,$data,$id_cliente,$forma_pagamento);

													if( $i>0 && $i % 3 == 0 ) {
														$new_page = "<hr>";
														fputs($fd,$new_page);
													}


													fputs($fd,$fatura);

												}


	// CARNE PRE PAGO - FINAL

									}else if ($id_cobranca == 1){// se cobranca é por boleto

	// BOLETO PRE PAGO - INICIO

												$ini_carne = @$_REQUEST["ini_carne"];
												$data_carne = @$_REQUEST["data_carne"];
												$prorata = @$_REQUEST["prorata"];
												$valor_prorata = @$_REQUEST["valor_prorata"];

												//Adiciona-se ao valor da fatura o valor do pro-rata																														
												if ($prorata == true){
													$fatura_valor += $valor_prorata;
													$fatura_valor -= $valor_cont_temp;
												}

												if ($pri_venc) {

													list($d, $m, $a) = explode("/",$pri_venc);
													$fatura_dt_vencimento = $a."-".$m."-".$d;
												}
												//TODO: Procurar função de adição do pro-rata
												if($tx_instalacao > 0) $fatura_valor += $tx_instalacao;

													//echo $fatura_valor ."<br>";
													$sSQL =  "INSERT INTO cbtb_faturas(";
													$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
													$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
													$sSQL .= "	acrescimo, valor_pago, id_carne ";
													$sSQL .= ") VALUES (";
													$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
													$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
													$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
													$sSQL .= ")";

													$this->bd->consulta($sSQL);
													if ($this->bd->obtemErro())	break;

													$data = $fatura_dt_vencimento;

													$fatura = $this->boleto($id_cliente_produto,$data,$id_cliente,$forma_pagamento);




	// BOLETO PRE PAGO - FINAL
									} else {//se a cobranca não é por carne nem boleto


										$fatura_valor = $valor_cont_temp;

										//Aplica descontos, caso haja algum período de desconto declarado
										if($qt_desconto > 0) {
											$fatura_desconto = $desconto_promo;
											$qt_desconto--; 
										} else
											$fatura_desconto = 0;


										if($tx_instalacao != 0) $fatura_valor += $tx_instalacao;

										//Calcula a data dos próximos pagamentos de fatura.
										if($pri_venc != ""){
										list($cd, $cm, $ca) = explode("/", $pri_venc);
											$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));
										}else{
											$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm, $cd, $ca));
										}

										//Calcula o desconto sobre a fatura.
										$fatura_valor -= $fatura_desconto;

										$sSQL =  "INSERT INTO cbtb_faturas(";
										$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
										$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
										$sSQL .= "	acrescimo, valor_pago, id_carne ";
										$sSQL .= ") VALUES (";
										$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
										$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
										$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
										$sSQL .= ")";

										$this->bd->consulta($sSQL);
										if ($this->bd->obtemErro())	break;
									}
									
								break;						
						}

									$hoje = date("Y-m-d");
									$nome_arquivo = "carne-".$hoje."-".$id_cliente_produto;
									$host = "dev.mosman.com.br";
									fclose($fd);


						/*<<=====INICIO DA PARTE DE CADASTRO DE FATURAS=====>>*/						
							if ($tipo && $tipo == "BL"){

								// Envia instrucao pra spool
								if ($nas && $nas["tipo_nas"] == "I"){

									$id_nas = $_REQUEST["id_nas"];
									$banda_upload_kbps = $bandaUp_dow["banda_upload_kbps"];
									$banda_download_kbps = $bandaUp_dow["banda_download_kbps"];
									$rede = str_replace("'","",$rede_disp); //$rede_disponivel["rede"];
									$mac = $_REQUEST["mac"];

									$sSQL  = "SELECT ";
									$sSQL .= "   id_nas, nome, ip, tipo_nas ";
									$sSQL .= "FROM ";
									$sSQL .= "   cftb_nas ";
									$sSQL .= "WHERE ";
									$sSQL .= "   id_nas = '$id_nas'";

									$nas = $this->bd->obtemUnicoRegistro($sSQL);
									$this->tpl->atribui("n",$nas);
									$this->tpl->atribui("tipo_nas",$nas["tipo_nas"]);

									$r =new RedeIP($rede);
									$ip_gateway = $r->minHost();
									$ip_cliente	= $r->maxHost(); // TODO: ObtemProximoIP();
									$mascara    = $r->mascara();

									$this->tpl->atribui("ip_gateway",$ip_gateway);
									$this->tpl->atribui("mascara",$mascara);
									$this->tpl->atribui("ip_cliente",$ip_cliente);


									$this->tpl->atribui("tipo",$tipo);


									$destino = $nas['id_nas'];


									$username = @str_replace(" ","",$_REQUEST["username"]);
									$this->spool->bandalargaAdicionaRede($destino,$id_conta,$rede,$mac,$banda_upload_kbps,$banda_download_kbps,$username);



								}

								// LISTA DE POPS
								$sSQL  = "SELECT ";
								$sSQL .= "   id_pop, nome ";
								$sSQL .= "FROM ";
								$sSQL .= "   cftb_pop ";
								$sSQL .= "WHERE ";
								$sSQL .= "   id_pop = '". $this->bd->escape(trim(@$_REQUEST["id_pop"])) ."' AND status = 'A'";

								$lista_pops = $this->bd->obtemUnicoRegistro($sSQL);

								$prefs = $this->prefs->obtem("total");
								$sSQL = "SELECT ip_externo FROM cntb_conta_bandalarga WHERE username = '".@str_replace(" ","",$_REQUEST["username"])."' AND tipo_conta = 'BL' and dominio = '".$prefs["dominio_padrao"]."' ";
								$externo = $this->bd->obtemUnicoRegistro($sSQL);

								if(count($externo)){
									$this->tpl->atribui("ip_externo",$externo["ip_externo"]);
								}
							}

							// Joga a mensagem de produto contratado com sucesso.
							$this->tpl->atribui("username",@str_replace(" ","",$_REQUEST["username"]));
							$this->tpl->atribui("pop",@$lista_pops["nome"]);
							$this->tpl->atribui("nas",@$nas["nome"]);
							$this->tpl->atribui("mac",@$_MAC);
							$this->tpl->atribui("ip",@$ip_disp);
							$this->tpl->atribui("dominio",@$prefs["dominio_padrao"]);
							$this->tpl->atribui("dominio_hospedagem",@$dominio_hospedagem);
							$this->tpl->atribui("id_cliente",$id_cliente);
							$this->tpl->atribui("id_cliente_produto",$id_cliente_produto);
							$this->tpl->atribui("id_carne",$id_carne);
							$this->tpl->atribui("primeira",true);
							$this->tpl->atribui("id_cobranca",$id_cobranca);

							$this->obtemPR($id_cliente);

							$this->arquivoTemplate="cliente_cobranca_intro.html";

							return;

							$exibeForm = false;

						}// fim do while
						
						// Se saiu com um erro:
						if ($this->bd->obtemErro()){
							// Descarta os erros
							$this->bd->rollback();

							// PEGA AS INFORMACOES (lista de sql, mensagem de erro, etc)
							$queries  = $this->bd->obtemListaSQL();
							$mensagem = $this->bd->obtemMensagemErro();
							// SALVA AS COISAS NO ARQUIVO DE LOG.

						}else{
							$this->bd->commit();
						}
// FINAL DO TRATAMENTO DE ERRO
						
					}else{ // se contem erros
					
						switch($tipo){
						case "BL":
						$msg_final = "Existe outro usuário de Banda Larga cadastrado com esses dados!";
						break;
						case "E":
						$msg_final = "Existe outra conta de E-Mail cadastrada com esses dados!";
						break;
						case "H":
						$msg_final = "Existe outra conta de Hospedagem cadastrada com esses dados!";
						break;
						case "D":
						$msg_final = "Existe outro usuario de Discado cadastrado com esses dados!";
						break;
						}
																	  		
						$this->tpl->atribui("mensagem",$msg_final);
						$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=cobranca&id_cliente=$id_cliente");
						$this->tpl->atribui("target","_top");
															      
						$this->arquivoTemplate="msgredirect.html";
						return;

					}
					
				
				}
				
				$this->tpl->atribui("erros",$erros);
			
				if( $exibeForm ) {
				
					if( !$acao ){

						$this->arquivoTemplate = "cliente_cobranca_contratar.html";

						$sSQL  = "SELECT ";
						$sSQL .= "	username, dominio, tipo_conta, senha, id_cliente, conta_mestre, status, observacoes, id_conta ";
						$sSQL .= "FROM ";
						$sSQL .= "	cntb_conta ";
						$sSQL .= "WHERE ";
						$sSQL .= "	id_conta = '". @$_REQUEST["$id_conta"] ."'";

						$conta = $this->bd->obtemUnicoRegistro($sSQL);


						$sSQL  = "SELECT ";
						$sSQL .= "   dominio,id_cliente ";
						$sSQL .= "FROM ";
						$sSQL .= "   dominio ";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_cliente = '".$this->bd->escape($id_cliente)."' ";
						$sSQL .= "ORDER BY ";
						$sSQL .= "   dominio ";

						$lista_dominios = $this->bd->obtemRegistros($sSQL);

						$sSQL  = "SELECT ";
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

						$sSQL  = "SELECT ";
						$sSQL .= "  id_cobranca, nome_cobranca, disponivel ";
						$sSQL .= "FROM ";
						$sSQL .= "cftb_forma_pagamento ";
						$sSQL .= "WHERE ";
						$sSQL .= "disponivel = true";
						$tipo_cobranca = $this->bd->obtemRegistros($sSQL);
						
						$preferencias = $this->prefs->obtem("total");
						
						$sSQL  = "SELECT ";
						$sSQL .= "	id_produto, nome, descricao, tipo, valor, disponivel, num_emails, quota_por_conta, ";
						$sSQL .= "	vl_email_adicional, permitir_outros_dominios, email_anexado, numero_contas ";
						$sSQL .= "FROM ";
						$sSQL .= "	prtb_produto ";
						
						$lista_de_produtos = $this->bd->obtemRegistros($sSQL);
						
						global $_LS_FORMA_PAGAMENTO;
						$this->tpl->atribui("forma_pagamento",$_LS_FORMA_PAGAMENTO);
						$this->tpl->atribui("listaGeralProdutos",$lista_de_produtos);
						
						$hoje = date("d/m/Y"); 
						
						$this->tpl->atribui("carencia", $preferencias["carencia"]);
						$this->tpl->atribui("tx_juros", $preferencias["tx_juros"]);
						$this->tpl->atribui("multa", $preferencias["multa"]);
						$this->tpl->atribui("dia_venc", $preferencias["dia_venc"]);
						$this->tpl->atribui("dominio_padrao",$preferencias["dominio_padrao"]);
						$this->tpl->atribui("nome_provedor",$preferencias["nome"]);
						$this->tpl->atribui("localidade",$preferencias["localidade"]);
						$this->tpl->atribui("cod_banco",$preferencias["cod_banco"]);
						$this->tpl->atribui("carteira",$preferencias["carteira"]);
						$this->tpl->atribui("agencia",$preferencias["agencia"]);
						$this->tpl->atribui("num_conta",$preferencias["num_conta"]);
						$this->tpl->atribui("convenio",$preferencias["convenio"]);
						$this->tpl->atribui("observacoes",$preferencias["observacoes"]);
						$this->tpl->atribui("cnpj",$preferencias["cnpj"]);
						$this->tpl->atribui("pagamento",$preferencias["pagamento"]);
						$this->tpl->atribui("hoje",$hoje);
						$this->tpl->atribui("lista_discado",$lista_discado);
						$this->tpl->atribui("lista_bandalarga",$lista_bandalarga);
						$this->tpl->atribui("lista_hospedagem",$lista_hospedagem);
						$this->tpl->atribui("tipo_cobranca",$tipo_cobranca);
						$this->tpl->atribui("preferencias",$preferencias);

						// LISTA DE POPS
						$sSQL  = "SELECT ";
						$sSQL .= "   id_pop, nome ";
						$sSQL .= "FROM ";
						$sSQL .= "   cftb_pop ";
						$sSQL .= "WHERE status = 'A' AND tipo != 'B' ";
						$sSQL .= "ORDER BY ";
						$sSQL .= "   nome";

						$lista_pops = $this->bd->obtemRegistros($sSQL);
						$this->tpl->atribui("lista_pops",$lista_pops);

						// LISTA DE NAS
						$sSQL  = "SELECT ";
						$sSQL .= "   id_nas, nome, ip, tipo_nas ";
						$sSQL .= "FROM ";
						$sSQL .= "   cftb_nas ";
						$sSQL .= "WHERE ";
						$sSQL .= "   tipo_nas = 'I' OR tipo_nas = 'P' ";
						$sSQL .= "ORDER BY ";
						$sSQL .= "   tipo_nas,nome ";

						global $_LS_TIPO_NAS;

						$lista_nas = $this->bd->obtemRegistros($sSQL);
							for($i=0;$i<count($lista_nas);$i++) {
								 $lista_nas[$i]["tp"] = $_LS_TIPO_NAS[ $lista_nas[$i]["tipo_nas"] ];
							}

					}
					
					
					global $_LS_OPERADORAS_CC;
					
					
					$this->tpl->atribui("cc_operadoras", $_LS_OPERADORAS_CC);
					$this->tpl->atribui("lista_nas",@$lista_nas);
					
					
					$this->tpl->atribui("msg",@$_REQUEST["msg"]);
					$this->tpl->atribui("tipo",$tipo);
					$this->tpl->atribui("username",@str_replace(" ","",$_REQUEST["username"]));
					$this->tpl->atribui("dominio",@$_REQUEST["dominio"]);
					$this->tpl->atribui("id_pop",@$_REQUEST["id_pop"]);
					$this->tpl->atribui("id_nas",@$_REQUEST["id_nas"]);
					$this->tpl->atribui("selecao_ip",@$_REQUEST["selecao_ip"]);
					$this->tpl->atribui("endereco_ip",@$_REQUEST["endereco_ip"]);
					$this->tpl->atribui("mac",@$_REQUEST["mac"]);
					
					$this->tpl->atribui("id_produto",@$_REQUEST["id_produto"]);

				}// fim de exibe form
				
				
				
				
			} else if( $rotina == "relatorio" ) {
				
				$this->arquivoTemplate = "cliente_cobranca_relatorio.html";
			
			} else if( $rotina == "excluir" ){
			
				if( ! $this->privPodeGravar("_CLIENTES_COBRANCA_ELIMINAR_CONTRATO") ) {
					$this->privMSG();
					return;
					
				}	
			
		//echo "PASSO 1 DA EXCLUSÃO: executa o excluiContrato";
				
				$id_cliente_produto = @$_REQUEST['id_cliente_produto'];
				$id_cliente = @$_REQUEST['id_cliente'];
				$permanente = @$_REQUEST['permanente'];
				
				$this->excluiContrato($id_cliente_produto,$permanente);
				
				$this->tpl->atribui("id_cliente",$id_cliente);
				$this->tpl->atribui("mensagem","Contrato excluído com sucesso! "); 
				$this->tpl->atribui("url","clientes.php?op=pesquisa");
				$this->tpl->atribui("target","_top");
				
				$this->arquivoTemplate = "msgredirect.html";
			
			
			
			
			}else if ( $rotina == "excl_confirma"){
			
				if( ! $this->privPodeGravar("_CLIENTES_COBRANCA_ELIMINAR_CONTRATO") ) {
					$this->privMSG();
					return;
				}				
			
			
			
				$sSQL  = "SELECT ";
				$sSQL .= "	cp.id_cliente_produto, cp.id_cliente, cp.id_produto, cp.dominio, ";
				$sSQL .= "	p.id_produto, p.nome, p.descricao, p.tipo, p.valor, p.disponivel, p.num_emails, p.quota_por_conta, ";
				$sSQL .= "	p.vl_email_adicional, p.permitir_outros_dominios, p.email_anexado ";
				$sSQL .= "FROM cbtb_cliente_produto cp INNER JOIN prtb_produto p ";
				$sSQL .= "USING( id_produto ) ";
				$sSQL .= "WHERE cp.id_cliente_produto='".@$_REQUEST['id_cliente_produto']."' ";
				$produtos = $this->bd->obtemRegistros($sSQL);
			
				for($i=0;$i<count($produtos);$i++) {

				   $id_cp = $produtos[$i]["id_cliente_produto"];
				   
				   $sSQL  = "SELECT ";
				   $sSQL .= "	username, dominio, tipo_conta, id_conta ";
				   $sSQL .= "FROM ";
				   $sSQL .= "	cntb_conta ";
				   $sSQL .= "WHERE ";
				   $sSQL .= "	id_cliente_produto = '$id_cp'";
			   
				   $contas = $this->bd->obtemRegistros($sSQL);
				   
				   $produtos[$i]["contas"] = $contas;
			
				}
			
			
			$this->tpl->atribui("produtos",$produtos);				
			$this->arquivoTemplate = "confirma_exclusao.html";
			return;
			
			
		}else if ($rotina == "carne"){
			
			if( ! $this->privPodeGravar("_CLIENTES_COBRANCA") ) {
				$this->privMSG();
				return;
			}				



			$id_cliente = @$_REQUEST["id_cliente"];
			$id_carne = @$_REQUEST["id_carne"];
			$p = @$_REQUEST["p"];

			$sSQL = "SELECT c.id_carne,c.id_cliente_produto,c.valor,c.status,c.vigencia,to_char(c.data_geracao,'DD/mm/YYYY') as data_geracao, c.data_geracao as dtg, ct.id_cobranca  FROM cbtb_carne c, cbtb_contrato ct where id_cliente = '$id_cliente' AND ct.id_cliente_produto = c.id_cliente_produto";

			$carnes = $this->bd->obtemRegistros($sSQL);
			$this->obtemPR($id_cliente);


			if(count($carnes)){

				$id_cliente_produto = @$carnes[0]["id_cliente_produto"];

			}

			if ($p == "faturas"){


				$sSQL = "select id_carne,id_cliente_produto, to_char(data,'DD/mm/YYYY') as data, descricao, valor, status from cbtb_faturas where id_carne = '$id_carne' order by data ";
				$faturas = $this->bd->obtemRegistros($sSQL);

				$this->tpl->atribui("faturas",$faturas);
				$this->tpl->atribui("id_cliente",$id_cliente);
				$this->tpl->atribui("id_cliente_produto",$id_cliente_produto);

				$this->arquivoTemplate = "cobranca_carnes_faturas.html";
				return;

			} else if ($p == "segunda_via"){
					// NÃO FAZ NADA
			}
			
			$this->tpl->atribui("carnes",$carnes);				
			$this->arquivoTemplate = "cobranca_carnes.html";

			
			}

?>
