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

		public function processa($op=null) {// Cria função processa
		
			if ($op == "lista_pop"){
		
				$erros = array();
											
															
				$enviando = false;
														
														
				$reg = array();
				
				$sSQL  = "SELECT ";
				$sSQL .= "   id_pop, nome, info, tipo, id_pop_ap ";
				$sSQL .= "FROM cftb_pop ";
								
				$reg = $this->bd->obtemRegistros($sSQL);
								
				$this->tpl->atribui("lista_pop",$reg);
				
				
				
				$this->arquivoTemplate = "configuracao_pop_lista.html";
					

			}else if($op == "pop"){


				$erros = array();

				$acao = @$_REQUEST["acao"];
				$id_pop = @$_REQUEST["id_pop"];

				$enviando = false;
				
				$tSQL  = "SELECT ";
				$tSQL .= "   id_pop, nome, info, tipo, id_pop_ap ";
				$tSQL .= "FROM cftb_pop ";
				$tSQL .= "WHERE tipo = 'AP'";
	
				$aps = $this->bd->obtemRegistros($tSQL);
				
				$reg = array();


				if( $acao ) {
				   // Se ele recebeu o campo ação é pq veio de um submit
				   $enviando = true;
				} else {
					// Se não recebe o campo ação e tem id_pop é alteração, caso contrário é cadastro.
					if( $id_pop ) {
						// SELECT
						$sSQL  = "SELECT ";
						$sSQL .= "   id_pop, nome, info, tipo, id_pop_ap ";
						$sSQL .= "FROM cftb_pop ";
						$sSQL .= "WHERE id_pop = '$id_pop'";
					
										
						$reg = $this->bd->obtemUnicoRegistro($sSQL);
					

						$acao = "alt";
						$titulo = "Alterar";

					} else {
						$acao = "cad";
						$titulo = "Cadastrar";
					}
				}
			
			if( $enviando ) {

				
				if( !count($erros) ) {
				   // Grava no banco.
					if( $acao == "cad" ) {
				   		// CADASTRO
				   		
				   		$id_pop_ap = @$_REQUEST['id_pop_ap'];
				   		
						$msg_final = "POP Cadastrado com sucesso!";
						$url = "configuracao.php?op=lista_pop";
						
						$id_pop = $this->bd->proximoID("cfsq_id_pop");

					
						$sSQL  = "INSERT INTO ";
						$sSQL .= "   cftb_pop( ";
						$sSQL .= "      id_pop, nome, info, tipo, id_pop_ap ) ";
						$sSQL .= "   VALUES (";
						$sSQL .= "     '" . $this->bd->escape($id_pop) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["info"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo"]) . "', ";
						$sSQL .= "      " . ($id_pop_ap ? "$id_pop_ap" : "NULL") . "  ";
						//$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["id_pop_ap"]) . "' ";
						$sSQL .= "     )";
					
						
					} else {
					   // ALTERACAO
						$msg_final = "POP Alterado com sucesso!";
						$url = "configuracao.php?op=lista_pop";


						$sSQL  = "UPDATE ";
						$sSQL .= "   cftb_pop ";
						$sSQL .= "SET ";
						$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
						$sSQL .= "   info = '" . $this->bd->escape(@$_REQUEST["info"]) . "', ";
						$sSQL .= "   tipo = '" . $this->bd->escape(@$_REQUEST["tipo"]) . "', ";
						$sSQL .= "   id_pop_ap = ". (@$_REQUEST["tipo"] != "CL" ? "NULL" : "'" . $this->bd->escape(@$_REQUEST["id_pop_ap"]) . "'" ) .   " ";
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
					$this->tpl->atribui("url",$url);
					$this->tpl->atribui("target","_top");

					$this->arquivoTemplate = "msgredirect.html";
					
					
					// cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
					return;
				}else{
				
				
				}
				
			}
			
			
			
			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("mensagem",$erros);
			$this->tpl->atribui("acao",$acao);
			$this->tpl->atribui("op",$op);
			
			// Atribui as listas
			//global $_LS_ESTADOS;
			//$this->tpl->atribui("lista_estados",$_LS_ESTADOS);
			

			global $_LS_TIPO_POP;
			$this->tpl->atribui("tipo_pop",$_LS_TIPO_POP);

			// Atribui os campos
		        $this->tpl->atribui("id_pop",@$reg["id_pop"]);
		        $this->tpl->atribui("nome",@$reg["nome"]);
		        $this->tpl->atribui("info",@$reg["info"]);// pega a info do db e atribui ao campo correspon do form
		        $this->tpl->atribui("tipo",@$reg["tipo"]);
		        $this->tpl->atribui("id_pop_ap",@$reg["id_pop_ap"]);
		        $this->tpl->atribui("titulo",@$titulo);// para que no template a variavel do smart titulo consiga pegar o que foi definido no $titulo.
		        
		        $this->tpl->atribui("lista_pops2",@$aps);
		        

			// Seta as variáveis do template.
			$this->arquivoTemplate = "configuracao_pop_cadastro.html";
			
			
			
		}else if ($op == "lista_nas"){
		
				
				$erros = array();
															
																			
				$enviando = false;
																		
																		
				$reg = array();
								
				$sSQL  = "SELECT ";
				$sSQL .= "   id_nas, nome, ip, secret, tipo_nas ";
				$sSQL .= "FROM cftb_nas ";
												
				$reg = $this->bd->obtemRegistros($sSQL);
												
				$this->tpl->atribui("lista_nas",$reg);
								
								
								
				$this->arquivoTemplate = "configuracao_nas_lista.html";
				
				
				
				
				
				
				
		
		}else if ($op =="nas"){

			
			
				$erros = array();
			
				$acao = @$_REQUEST["acao"];
				$id_nas = @$_REQUEST["id_nas"];
			
				$enviando = false;
						
				$reg = array();

			
				if( $acao ) {
					// Se ele recebeu o campo ação é pq veio de um submit
					$enviando = true;
				} else {
					// Se não recebe o campo ação e tem id_pop é alteração, caso contrário é cadastro.
					if( $id_nas ) {
						// SELECT
						$sSQL  = "SELECT ";
						$sSQL .= "   id_nas, nome, ip, secret, tipo_nas ";
						$sSQL .= "FROM cftb_nas ";
						$sSQL .= "WHERE id_nas = '$id_nas'";
								
													
						$reg = $this->bd->obtemUnicoRegistro($sSQL);
								
			
						$acao = "alt";
						$titulo = "Alterar";
			
					} else {
						$acao = "cad";
						$titulo = "Cadastrar";
					}
				}//hugo2
						
				if( $enviando ) {
			
							
					if( !count($erros) ) {
						// Grava no banco.
						if( $acao == "cad" ) {
						// CADASTRO
							$tipo_nas = @$_REQUEST["tipo_nas"];
							$secret = @$_REQUEST["secret"];
							$ip = @$_REQUEST["ip"];

							$msg_final = "NAS Cadastrado com sucesso!";
							
							
							if($tipo_nas == "R" || $tipo_nas == "P"){   		
								
							   		
								$id_nas = $this->bd->proximoID("cfsq_id_nas");
			
								
								$sSQL  = "INSERT INTO ";
								$sSQL .= "   cftb_nas( ";
								$sSQL .= "      id_nas, nome, ip, secret, tipo_nas ) ";
								$sSQL .= "   VALUES (";
								$sSQL .= "     '" . $id_nas . "', ";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["ip"]) . "', ";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["secret"]) . "', ";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo_nas"]) . "' ";
								$sSQL .= "     )";
										
						
								$this->spool->radiusAdicionaNAS($ip,$secret);
							
							} else if($tipo_nas == "I"){
							
								$id_nas = $this->bd->proximoID("cfsq_id_nas");
								
								$sSQL  = "INSERT INTO ";
								$sSQL .= "   cftb_nas( ";
								$sSQL .= "      id_nas, nome, ip, secret, tipo_nas ) ";
								$sSQL .= "   VALUES (";
								$sSQL .= "     '" . $this->bd->escape($id_nas) . "', ";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["ip"]) . "', ";
								$sSQL .= "     NULL, ";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo_nas"]) . "' ";
								$sSQL .= "     )";

							}
									
									
									
									
									
						} else {
						// ALTERACAO
							$msg_final = "NAS Alterado com sucesso!";
							$tipo_nas = @$_REQUEST["tipo_nas"];
							$secret = @$_REQUEST["secret"];
							$ip = @$_REQUEST["ip"];
							$tipo_nas_up = @$_REQUEST['tipo_nas_up'];
							
							//echo "Tipo NAS: $tipo_nas";
			
				
							if($tipo_nas_up == "R" || $tipo_nas_up == "P"){
							
								$tSQL  = "SELECT ";
								$tSQL .= "	ip, secret ";
								$tSQL .= "FROM ";
								$tSQL .= "	cftb_nas ";
								$tSQL .= "WHERE ";
								$tSQL .= "   id_nas = '" . $this->bd->escape(@$_REQUEST["id_nas"]) . "' ";
								
								$compara = $this->bd->obtemUnicoRegistro($tSQL);
								
								
								$sSQL  = "UPDATE ";
								$sSQL .= "   cftb_nas ";
								$sSQL .= "SET ";
								$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
								$sSQL .= "   ip = '" . $this->bd->escape(@$_REQUEST["ip"]) . "', ";
								$sSQL .= "   secret = '" . $this->bd->escape(@$_REQUEST["secret"]) . "', ";
								$sSQL .= "   tipo_nas = '" . $this->bd->escape(@$_REQUEST["tipo_nas_up"]) . "' ";
								$sSQL .= "WHERE ";
								$sSQL .= "   id_nas = '" . $this->bd->escape(@$_REQUEST["id_nas"]) . "' ";  
						
								
								
								if ($ip != $compara['ip'] || $secret != $compara['secret']){
								
								
									$this->spool->radiusExcluiNAS($ip);
									$this->spool->radiusAdicionaNAS($ip,$secret);
							
								}
							
							
							}else if($tipo_nas_up == "I"){
							
							
							
								$sSQL  = "UPDATE ";
								$sSQL .= "   cftb_nas ";
								$sSQL .= "SET ";
								$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
								$sSQL .= "   ip = '" . $this->bd->escape(@$_REQUEST["ip"]) . "', ";
								$sSQL .= "   secret = NULL, ";
								$sSQL .= "   tipo_nas = '$tipo_nas_up' ";
								$sSQL .= "WHERE ";
								$sSQL .= "   id_nas = '" . $this->bd->escape(@$_REQUEST["id_nas"]) . "' ";  
							
							
							
							}

			
			
						}
								
						$this->bd->consulta($sSQL);  
			
						//if( $this->bd->obtemErro() != MDATABASE_OK ) {
						//echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
						//echo "QUERY: " . $sSQL . "<br>\n";
								
						//}
			
			
						// Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
						$this->tpl->atribui("mensagem",$msg_final); 
						$this->tpl->atribui("url","configuracao.php?op=lista_nas");
						$this->tpl->atribui("target","_top");
			
						$this->arquivoTemplate = "msgredirect.html";
								
								
						// cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
						return;
						}else{
							
							
						}
							
					}
			
			
				// Atribui a variável de erro no template.
				$this->tpl->atribui("erros",$erros);
				$this->tpl->atribui("mensagem",$erros);
				$this->tpl->atribui("acao",$acao);
				$this->tpl->atribui("op",$op);
							
				// Atribui as listas
				//global $_LS_ESTADOS;
				//$this->tpl->atribui("lista_estados",$_LS_ESTADOS);
							
				
				global $_LS_TIPO_NAS;
				$this->tpl->atribui("ls_tipo_nas",$_LS_TIPO_NAS);
				
				// Atribui os campos
				$this->tpl->atribui("id_nas",@$reg["id_nas"]);
				$this->tpl->atribui("nome",@$reg["nome"]);
				$this->tpl->atribui("ip",@$reg["ip"]);// pega a info do db e atribui ao campo correspon do form
				$this->tpl->atribui("secret",@$reg["secret"]);
				$this->tpl->atribui("tipo_nas",@$reg["tipo_nas"]);
				$this->tpl->atribui("titulo",@$titulo);// para que no template a variavel do smart titulo consiga pegar o que foi definido no $titulo.
						        
										        
				
				// Seta as variáveis do template.
				$this->arquivoTemplate = "configuracao_nas_cadastro.html";
			
			
//////////////////////////////HUGO
		}else if ($op == "nas_rede"){
			//LISTA REDES CADASTRADAS EM DETERMINADO NAS
			
			$id_nas = @$_REQUEST["id_nas"];
			
			$erro = "";
			
			if( !$id_nas ) {
			   $erro = "Tentativa de acesso inválido";
			}
			
			if( !$erro ) {
				// Informações do NAS
				$sSQL  = "SELECT ";
				$sSQL .= "   id_nas, nome, ip, secret, tipo_nas ";
				$sSQL .= "FROM ";
				$sSQL .= "   cftb_nas ";
				$sSQL .= "WHERE ";
				$sSQL .= "   id_nas = '".$this->bd->escape($id_nas)."' ";
				
				$nas = $this->bd->obtemUnicoRegistro($sSQL);
				
				$this->tpl->atribui("nas",$nas);
				
				// $sSQL .= "";
			
			
				// Lista das redes deste NAS
				//$sSQL  = "SELECT ";
				//$sSQL .= "   r.rede,r.tipo_rede,r.id_rede, nr.id_rede ";
				//$sSQL .= "FROM ";
				//$sSQL .= "   cftb_nas_rede nr, cftb_rede r ";
				//$sSQL .= "WHERE ";
				//$sSQL .= "   r.id_rede = nr.id_rede ";
				//$sSQL .= "   AND nr.id_nas = '" . $this->bd->escape($id_nas) . "' ";
				//$sSQL .= "   id_nas = '" . $this->bd->escape($id_nas) . "' ";
				
				$sSQL  = "SELECT ";
				$sSQL .= "   r.rede, r.tipo_rede, r.id_rede ";
				$sSQL .= "FROM ";
				$sSQL .= "   cftb_rede r, cftb_nas_rede nr ";
				$sSQL .= "WHERE ";
				$sSQL .= "   r.rede = nr.rede ";
				$sSQL .= "   AND nr.id_nas = $id_nas ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   r.rede ";
				$sSQL .= "";
				
				
				
				// TODO: aplicar filtros e paginação...
				$redes = $this->bd->obtemRegistros($sSQL);
				
				$this->tpl->atribui("redes",$redes);
				
				$this->arquivoTemplate = "configuracao_nas_rede.html";

				
			}
			
			
		
		}else if($op == "rede"){
			// CADASTRA E ALTERA REDE EM DETERMINADO NAS
			
			$erros = array();
						
			$acao = @$_REQUEST["acao"];
			$id_rede = @$_REQUEST["id_rede"];
			$id_nas = @$_REQUEST["id_nas"];
			$tipo_rede = @$_REQUEST["tipo_rede"];
			$rede = @$_REQUEST["rede"];
			$rede_origem = @$_REQUEST["rede_origem"];
			$rede_inicial = @$_REQUEST["rede_inicial"];
			$bits_subredes = @$_REQUEST["bits_subredes"];
			$num_redes = @$_REQUEST['num_redes'];
			$tipo_nas = @$_REQUEST['tipo_nas'];
			$network = @$_REQUEST['network'];
			

			
			
			
			$enviando = false;
									
			$reg = array();
			
			$sSQL  = "SELECT ";
			$sSQL .= "   id_nas, nome, ip, secret, tipo_nas ";
			$sSQL .= "FROM ";
			$sSQL .= "   cftb_nas ";
			$sSQL .= "WHERE ";
			$sSQL .= "   id_nas = '".$this->bd->escape($id_nas)."' ";
								
			$nas = $this->bd->obtemUnicoRegistro($sSQL);

			
			
			if($acao){
				$enviando = true;
				
			}else{
			
				if($id_rede){
					$acao = "alt";
					$titulo = "Alterar";
				} else {
					$acao = "cad";
					$titulo = "Cadastrar";
				}

			}

			if( $enviando ) {
				if( !count($erros) ) {
					// Grava no banco.
					if( $acao == "cad" ) {
						// CADASTRO
										   		
										   		
						$msg_final = "Rede Cadastrada com sucesso!";
						$url = "configuracao.php?op=lista_nas";
										   		
						
						if($nas["tipo_nas"] == "P"){
							// cadastraRede (a rede que o cara digitou)
							$this->cadastraRede($rede,$tipo_rede);
							
							// vinculaRede (a mesma)
							$this->vinculaRede($id_nas,$rede);
							$this->cadastraIPs(@$_REQUEST['rede']);
							
							//$this->cadastraIPs($rede);
						}else if ($nas["tipo_nas"] == "I"){
						$this->cadastraSubredes($id_nas,$rede_origem,$rede_inicial,$bits_subredes,$num_redes,$tipo_rede);
							//$rede = $rede_origem;						
							//$this->cadastraSubRedes($id_nas,$rede_origem,$rede_inicial,$bits_subredes,$num_redes,$tipo_rede);

						}
						
						//$this->cadastraRede($rede,$tipo_rede);	
						//$this->vinculaRede($id_nas,$rede);

					
																
					} else {
						// ALTERACAO
						
						$msg_final = "Rede Alterada com sucesso!";
						$url = "configuracao.php?op=lista_nas";
						
						$this->alteraRede($rede,$tipo_rede);
						
						
						
					}
											
											
				
						
						
				// Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
				$this->tpl->atribui("mensagem",$msg_final); 
				$this->tpl->atribui("url",$url);
				$this->tpl->atribui("target","_top");
						
				$this->arquivoTemplate = "msgredirect.html";
											
											
				// cai fora da função (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
				return;
				} else {
										
										
				}
										
			}					
						
						
			// Atribui a variável de erro no template.
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("mensagem",$erros);
			$this->tpl->atribui("acao",$acao);
			$this->tpl->atribui("op",$op);
			$this->tpl->atribui("nas",@$nas);		
						
			// Atribui os campos
			$this->tpl->atribui("network",$network);
			$this->tpl->atribui("id_rede",@$id_rede);
			$this->tpl->atribui("id_nas",@$id_nas);
			$this->tpl->atribui("tipo_rede",@$tipo_rede);// pega a info do db e atribui ao campo correspon do form
			$this->tpl->atribui("rede",@$rede);
			$this->tpl->atribui("tipo_nas",@$tipo_nas);
			$this->tpl->atribui("titulo",@$titulo);// para que no template a variavel do smart titulo consiga pegar o que foi definido no $titulo.
									        
													        
							
			
			// Seta as variáveis do template.
			
			$this->arquivoTemplate = "configuracao_redes_cadastro.html";
			


///////////////////////////////		

		}else if ($op == "cidades"){
		
				
				
				$eSQL  = "SELECT ";
				$eSQL .= "   uf, estado ";
				$eSQL .= "FROM cftb_uf ";
				$eSQL .= "ORDER BY estado ";

				$lista_estados = $this->bd->obtemRegistros($eSQL);
				
				$this->tpl->atribui("lista_estados",$lista_estados);
				$city = @$_REQUEST['pesquisa'];
				$uf = @$_REQUEST['uf'];
				$acao = @$_REQUEST['acao'];
				//$erro = "";
				$mov = @$_REQUEST['mov'];
				
				$this->tpl->atribui("acao",$acao);
				
				if (!$city && !$uf){
				$dSQL  = "SELECT ";
				$dSQL .= "   id_cidade, uf, cidade, disponivel ";
				$dSQL .= "FROM cftb_cidade ";
				$dSQL .= "WHERE disponivel = 't'";
				$dSQL .= "ORDER BY cidade ";

				$erro = "";
				
				
				$lista_cidades = $this->bd->obtemRegistros($dSQL);
				$this->tpl->atribui("lista_cidades",$lista_cidades);
				
					if (!count($lista_cidades)){
						$erro = "nenhuma cidade disponivel no momento";
					}
				

				
				}
				
			
			
			
				if ( $city ) {
					
					
					
					$city = @$_REQUEST['pesquisa'];;
					
					$city = ereg_replace("[áàâãª]","a",$city);
					$city = ereg_replace("[ÁÀÂÃ]","A",$city);
					$city = ereg_replace("[éèê]","e",$city);
					$city = ereg_replace("[ÉÈÊ]","E",$city);
					$city = ereg_replace("[óòôõº]","o",$city);
					$city = ereg_replace("[ÓÒÔÕ]","O",$city);
					$city = ereg_replace("[úùû]","u",$city);
					$city = ereg_replace("[ÚÙÛ]","U",$city);
					$city = str_replace("ç","c",$city);
					$city = str_replace("Ç","C",$city);
					//$city = ereg_replace(" ","",$city); 
					$city = strtoupper($city);
					
										
					if ( !$uf ){
				
					
					$cSQL  = "SELECT ";
					$cSQL .= "   id_cidade, uf, cidade, disponivel ";
					$cSQL .= "FROM cftb_cidade ";
					//$cSQL .= "WHERE nome ilike '%$city%'";
					$cSQL .= "WHERE cidade ilike '". str_replace("*","%",$city) ."' ";
					$cSQL .= "ORDER BY cidade ASC";
					
					
					
					$pesquisa_resultado = $this->bd->obtemRegistros($cSQL);
					
					$this->tpl->atribui("pesquisa_resultado",$pesquisa_resultado);
					$this->tpl->atribui("pesquisa",$city);
					$acao = "search";
					$this->tpl->atribui("acao",$acao);
					
					}else{
					
										
						$cSQL  = "SELECT ";
						$cSQL .= "   id_cidade, uf, cidade, disponivel ";
						$cSQL .= "FROM cftb_cidade ";
						//$cSQL .= "WHERE nome ilike '%$city%' AND uf = '$estado'";
						$cSQL .= "WHERE cidade ilike '". str_replace("*","%",$city) ."' AND uf = '$uf'";
						$cSQL .= "ORDER BY cidade ASC";
						
						$eSQL  = "SELECT ";
						$eSQL .= "   estado ";
						$eSQL .= "FROM cftb_uf ";
						$eSQL .= "WHERE uf = '$uf'";
						
						

									
						$pesquisa_resultado = $this->bd->obtemRegistros($cSQL);
						$nome_estado = $this->bd->obtemUnicoRegistro($eSQL);
						
						$this->tpl->atribui("nome_uf",$nome_estado["estado"]);
						$this->tpl->atribui("pesquisa_resultado",$pesquisa_resultado);
						$this->tpl->atribui("pesquisa",$city);
						$this->tpl->atribui("uf",$uf);
						$acao = "search";
						$this->tpl->atribui("acao",$acao);
					}
				
				
				}else if ( $uf ){
				
					$cSQL  = "SELECT ";
					$cSQL .= "   id_cidade, uf, cidade, disponivel ";
					$cSQL .= "FROM cftb_cidade ";
					$cSQL .= "WHERE uf = '$uf'";
					$cSQL .= "ORDER BY cidade ASC";
					
					$eSQL  = "SELECT ";
					$eSQL .= "   estado ";
					$eSQL .= "FROM cftb_uf ";
					$eSQL .= "WHERE uf = '$uf'";

					$pesquisa_resultado = $this->bd->obtemRegistros($cSQL);
					$nome_estado = $this->bd->obtemUnicoRegistro($eSQL);
												
					$this->tpl->atribui("pesquisa_resultado",$pesquisa_resultado);
					$this->tpl->atribui("nome_uf",$nome_estado["estado"]);
					$this->tpl->atribui("uf",$uf);
					$acao = "search";
					$this->tpl->atribui("acao",$acao);
				
				
				

				
					}
				

					if($mov == "cadastro"){
											
											
						while(list($id,$valor)=each($_REQUEST['disponivel'])){
											
							$uSQL  = "UPDATE ";
							$uSQL .= "   cftb_cidade ";
							$uSQL .= "SET ";
							$uSQL .= "   disponivel = '$valor' ";
							$uSQL .= "WHERE ";
							$uSQL .= "   id_cidade = '$id' ";
											
							$this->bd->consulta($uSQL);
							
							$this->tpl->atribui("op","cidades");
							$this->tpl->atribui("acao","ok");
											
											
						}
					}
				//$this->tpl->atribui("erro",$erro);
				global $_LS_ST_CIDADE;
				$this->tpl->atribui("lista_st_cidades",$_LS_ST_CIDADE);

				$this->arquivoTemplate = "configuracao_cadastro_cidades.html";
				
				
				
					
						
					
					
					}else if ($op == "monitor"){
					$this->arquivoTemplate = "cobranca_versaolight.html";
					
					}else if ($op == "preferencia"){
						
						$acao = @$_REQUEST["acao"];
						
						

						$prefs = $this->prefs->obtem("geral");
						
						//echo "preferencia <br>";
						
						
						if ($acao == "alt"){
							//echo "alt";
							if(!count($prefs)){
						
								$sSQL  = "INSERT INTO ";
								$sSQL .= "  pftb_preferencia_geral ";						
								$sSQL .= "    (id_provedor) ";
								$sSQL .= "VALUES ";
								$sSQL .= "	('1')";
								
								$this->bd->consulta($sSQL);

								
								$sSQL  = "INSERT INTO ";
								$sSQL .= "  pftb_preferencia_cobranca ";						
								$sSQL .= "    (id_provedor) ";
								$sSQL .= "VALUES ";
								$sSQL .= "	('1')";

								$this->bd->consulta($sSQL);

								$sSQL  = "INSERT INTO ";
								$sSQL .= "  pftb_preferencia_provedor ";						
								$sSQL .= "    (id_provedor) ";
								$sSQL .= "VALUES ";
								$sSQL .= "	('1')";

								$this->bd->consulta($sSQL);
	
								//echo "SQL INSERT: $sSQL <br>";
	
								$this->bd->consulta($sSQL);
								
								$sSQL  = "SELECT * from cltb_cliente where id_cliente = '1'";
								//echo "SELECT CLIENTE: $sSQL <br>";
																
								$primeiro = $this->bd->obtemUnicoRegistro($sSQL);
								
								if (!count($primeiro)){
								
								//echo "primeiro registro <br>";
								
									$id_cliente = $this->bd->proximoID("clsq_id_cliente");
									
									$sSQL  = "INSERT INTO ";
									$sSQL .= "   cltb_cliente ( ";
									$sSQL .= "      id_cliente, data_cadastro, nome_razao, provedor, excluido ";
									$sSQL .= " )  VALUES (";
									$sSQL .= "     '" . $this->bd->escape($id_cliente) . "', ";
									$sSQL .= "     now(), ";
									$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
									$sSQL .= "     't', ";
									$sSQL .= "     'f' ";
									$sSQL .= "     )";									
									//echo "INSERT NO CLIENTE: $sSQL <br>";
									$this->bd->consulta($sSQL);
									
									$sSQL  = "INSERT INTO ";
									$sSQL .= "  dominio ";
									$sSQL .= "  (dominio, id_cliente, status, dominio_provedor) ";
									$sSQL .= "VALUES ";
									$sSQL .= "  ('".@$_REQUEST["dominio_padrao"]."', ";
									$sSQL .= "  '1', ";
									$sSQL .= "  'A', ";
									$sSQL .= "  't') ";
									//echo "INSERT NO DOMINIO: $sSQL <br>";
									$this->bd->consulta($sSQL);
									
									
									
								}
								
								
						
							}
						
							$sSQL  = "UPDATE ";
							$sSQL .= "  pftb_preferencia_geral ";
							$sSQL .= "SET ";
							$sSQL .= "  dominio_padrao = '".@$_REQUEST["dominio_padrao"]."', ";
							$sSQL .= "  nome = '".@$_REQUEST["nome"]."', ";
							$sSQL .= "  radius_server = '".@$_REQUEST["radius_server"]."', ";
							$sSQL .= "  hosp_server = '".@$_REQUEST["hosp_server"]."', ";
							$sSQL .= "  hosp_ns1 = '".@$_REQUEST["hosp_ns1"]."', ";
							$sSQL .= "  hosp_ns2 = '".@$_REQUEST["hosp_ns2"]."', ";
							$sSQL .= "  hosp_uid = '".@$_REQUEST["hosp_uid"]."', ";
							$sSQL .= "  hosp_gid = '".@$_REQUEST["hosp_gid"]."', ";
							$sSQL .= "  mail_server = '".@$_REQUEST["mail_server"]."', ";
							$sSQL .= "  mail_uid = '".@$_REQUEST["mail_uid"]."', ";
							$sSQL .= "  mail_gid = '".@$_REQUEST["mail_gid"]."', ";
							$sSQL .= "  pop_host = '".@$_REQUEST["pop_host"]."', ";
							$sSQL .= "  smtp_host = '".@$_REQUEST["smtp_host"]."', ";
							$sSQL .= "  hosp_base = '".@$_REQUEST["hosp_base"]."' ";
							
							//echo "SQL UPDATE: $sSQL <br>";
							$this->bd->consulta($sSQL);
							
							
							$this->tpl->atribui("mensagem","PREFERENCIAS GRAVADAS COM SUCESSO! "); 
							$this->tpl->atribui("url","home.php");
							$this->tpl->atribui("target","_top");
													
							$this->arquivoTemplate = "msgredirect.html";

							return;
							
						
						}
						
						
						
						$this->tpl->atribui("op",$op);
						$this->tpl->atribui("acao",$acao);
						$this->tpl->atribui("prefs",$prefs);
						//$this->tpl->atribui("frm_pagamento",$frm_pagamento);
						$this->arquivoTemplate = "configuracao_preferencia.html";
												
							
					
					
					}else if ($op == "preferencia_cobranca"){
					
						$acao = @$_REQUEST["acao"];
					
						$prefs = $this->prefs->obtem("cobranca");
					
					
						$sSQL  = "SELECT id_cobranca, nome_cobranca, disponivel FROM cftb_forma_pagamento ORDER BY id_cobranca asc";
						$frm_pagamento = $this->bd->obtemRegistros($sSQL);

					
						if ($acao == "alt"){
						
							$sSQL  = "UPDATE ";
							$sSQL .= "	pftb_preferencia_cobranca ";
							$sSQL .= "SET ";
							$sSQL .= "  tx_juros = '".@$_REQUEST["tx_juros"]."', ";
							$sSQL .= "  multa = '".@$_REQUEST["multa"]."', ";
							$sSQL .= "  dia_venc = '".@$_REQUEST["dia_venc"]."', ";
							$sSQL .= "  cod_banco = '".@$_REQUEST["cod_banco"]."', ";
							$sSQL .= "  carteira = '".@$_REQUEST["carteira"]."', ";
							$sSQL .= "  agencia = '".@$_REQUEST["agencia"]."', ";
							$sSQL .= "  num_conta = '".@$_REQUEST["num_conta"]."', ";
							$sSQL .= "  convenio = '".@$_REQUEST["convenio"]."', ";
							$sSQL .= "	pagamento = '".@$_REQUEST["pagamento"]."', ";
							$sSQL .= "	observacoes = '".@$_REQUEST["observacoes"]."' ";

							$this->bd->consulta($sSQL);
							//echo "update cobrança: $sSQL <br>";


							$sSQL = "UPDATE cftb_forma_pagamento SET disponivel = 'f'";
							//echo "zerando fp: $sSQL <br>";
							
							$this->bd->consulta($sSQL);

							while(list($id,$valor)=each($_REQUEST['disponivel'])){


								$uSQL  = "UPDATE ";
								$uSQL .= "   cftb_forma_pagamento ";
								$uSQL .= "SET ";
								$uSQL .= "   disponivel = '$valor' ";
								$uSQL .= "WHERE ";
								$uSQL .= "   id_cobranca = '$id' ";

								$this->bd->consulta($uSQL);
								//echo $uSQL ."<br>";			
								
								
								
							}
							
							$this->tpl->atribui("mensagem","PREFERENCIAS GRAVADAS COM SUCESSO! "); 
							$this->tpl->atribui("url","home.php");
							$this->tpl->atribui("target","_top");
																												
							$this->arquivoTemplate = "msgredirect.html";
															
							return;

						}
						
						$this->tpl->atribui("op",$op);
						$this->tpl->atribui("acao",$acao);
						$this->tpl->atribui("prefs",$prefs);
						$this->tpl->atribui("frm_pagamento",$frm_pagamento);
						$this->arquivoTemplate = "configuracao_preferencia_cobranca.html";

					
					
					}else if ($op == "preferencia_provedor"){
					
					
						$acao = @$_REQUEST["acao"];
					
						$prefs = $this->prefs->obtem("provedor");
						
							if ($acao == "alt"){
								
								$sSQL  = "UPDATE ";
								$sSQL .= "pftb_preferencia_provedor ";
								$sSQL .= "SET ";
								$sSQL .= "	endereco = '".$_REQUEST["endereco"]."', ";
								$sSQL .= "	localidade = '".$_REQUEST["localidade"]."', ";
								$sSQL .= "	cep = '".$_REQUEST["cep"]."', ";
								$sSQL .= "	cnpj = '".$_REQUEST["cnpj"]."' ";
								
								$this->bd->consulta($sSQL);
								
								$this->tpl->atribui("mensagem","PREFERENCIAS GRAVADAS COM SUCESSO! "); 
								$this->tpl->atribui("url","home.php");
								$this->tpl->atribui("target","_top");
																					
								$this->arquivoTemplate = "msgredirect.html";
								
								return;
								
								
							}
						
						
						
						$this->tpl->atribui("op",$op);
						$this->tpl->atribui("acao",$acao);
						$this->tpl->atribui("prefs",$prefs);
						//$this->tpl->atribui("frm_pagamento",$frm_pagamento);
						$this->arquivoTemplate = "configuracao_preferencia_provedor.html";
				
					
					}else if ($op == "contratos"){
					
						$acao = @$_REQUEST["acao"];
						$tipo_contrato = @$_REQUEST["tipo_contrato"];
						$contrato = @$_REQUEST["contrato"];
						$hoje = date("dmY-His");
						
						global $_LS_TIPO_CONTRATO;
						$this->tpl->atribui("tipo_contrato",$_LS_TIPO_CONTRATO);
						
						
						$nome_arq = "contrato_padrao_".$tipo_contrato.".html";
						
						$_file_ = @$_FILES['contrato'];

						
						
						
						
						if ($acao == "ok"){
							
							$extensao = $_file_["type"];
							//echo "HOJE: $hoje<br>";
							//echo "extensao: $extensao<br>";

							if ($extensao && $extensao != "text/html"){
								$_erro = "EXTENSÃO DE ARQUIVO INVÁLIDA.<br>SÓ É PERMITIDO O ENVIO DE ARQUIVOS HTML";
								$this->tpl->atribui("erro",$_erro);
								$this->arquivoTemplate = "configuracao_upload_contrato.html";
								return;

							}else{

								$arqtmp = $_file_["name"];
								//$fd = fopen($arqtmp,"w");
								
								$diretorio = "/tmp";
										
								$nome_aceitavel = $nome_arq;
								$diretorio_destino = "./contratos";
								
								$arquivo = $diretorio_destino."/".$nome_aceitavel;

								$_name_ = $_file_['name'];
								$_tmp_name_ = $_file_['tmp_name'];


								if (file_exists($arquivo)) {
									//copy($_tmp_name,$diretorio."/_".$nome_aceitavel);
									rename($diretorio_destino."/".$nome_aceitavel, $diretorio_destino."/_".$nome_aceitavel);
								}

								copy($_tmp_name_,$diretorio_destino . "/" . $nome_aceitavel);

								$mensagem = "Arquivo Enviado com Sucesso";
								$this->tpl->atribui("mensagem",$mensagem);

								


							}
							
							
							
							
							
						
						}
						
						
						
						
						$this->arquivoTemplate = "configuracao_upload_contrato.html";

						
					
					}
				
		
			}// fecha function processa()
			
	public function __destruct() {
			parent::__destruct();
	}
		
	private function cadastraRede($rede,$tipo_rede) {
			
			
		$id_rede = $this->bd->proximoID("cfsq_id_rede");
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "   cftb_rede ( ";
		$sSQL .= "      rede,tipo_rede,id_rede ";
		$sSQL .= "   ) VALUES ( ";
		$sSQL .= "      '$rede','$tipo_rede','$id_rede' ";
		$sSQL .= "   )";
		$sSQL .= "";
		
		$this->bd->consulta($sSQL);
		
		return($rede);
		
	}
		
	// Altera o tipo de uma rede
	private function alteraRede($rede,$tipo_rede) {
		  
		
		$sSQL  = "UPDATE ";
		$sSQL .= "   cftb_rede ";
		$sSQL .= "SET ";
		$sSQL .= "   tipo_rede = '$tipo_rede' ";
		$sSQL .= "WHERE ";
		$sSQL .= "   rede = '$rede' ";
		$sSQL .= "";
		$sSQL .= "";
		
		$this->bd->consulta($sSQL);
		       		
		/*if( $this->bd->obtemErro() != MDATABASE_OK ) {
			echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
			echo "QUERY: " . $sSQL . "<br>\n";
													
		}*/

		       		
		
	}
		    
	// Vincula uma rede a um nas.
	private function vinculaRede($id_nas,$rede) {
		    
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "   cftb_nas_rede ( ";
		$sSQL .= "      id_nas, rede ";
		$sSQL .= "   ) VALUES ( ";
		$sSQL .= "      '$id_nas', '$rede' ";
		$sSQL .= "   );";
		$sSQL .= "";
		$sSQL .= "";
		
		$this->bd->consulta($sSQL);
		
		return;
		
	}
		
	// Cadastra um ip no sistema
	private function cadastraIP($ipaddr) {
		  
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "   cftb_ip ( ";
		$sSQL .= "      ipaddr ";
		$sSQL .= "   ) VALUES ( ";
		$sSQL .= "      '$ipaddr' ";
		$sSQL .= "   )";
		
		$this->bd->consulta($sSQL);
		
		return($ipaddr);
		
    }

	// Cadastra todos os ips de uma rede no sistema.
    private function cadastraIPs($rede) {

    	$_rede = new RedeIP($rede);
    	$ips = $_rede->listaIPs();

    	for($x=0;$x<count($ips);$x++) {
          
			// Insere no BD
			$this->cadastraIP($ips[$x]);
   	    		
   	    		
   	    }
   	}		

	private function cadastraSubRedes($id_nas,$rede_origem,$rede_inicial,$bits_subredes,$num_redes,$tipo_rede) {
		
		$_rede_origem = new RedeIP($rede_origem);
		$_rede_inicio = new RedeIP($rede_inicial);
		
		$_subredes = $_rede_origem->listaSubRedes($bits_subredes);
		
		$conta = 0;
		
		$comecou = false;
	
		for($x=0;$x<count($_subredes);$x++) {
		
			if( bin2addr($_subredes[$x]->network) == bin2addr($_rede_inicio->network) ){
				$comecou = true;
			}
	
			if( $comecou ) {
				$rede = bin2addr($_subredes[$x]->network) . "/". $bits_subredes;
				$this->cadastraRede($rede,$tipo_rede);
				$this->vinculaRede($id_nas,$rede);
				$conta++;
			}
	
			if( $conta == $num_redes ) {
				break;
			}
			
		}
		
	}





}// fecha classe VirtexAdmin




?>