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
	
	private function obtemCliente($id_cliente) {

		$sSQL  = "SELECT ";
		$sSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
		$sSQL .= "   rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
		$sSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
		$sSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
		$sSQL .= "   ativo, obs, excluido ";
		$sSQL .= "FROM ";
		$sSQL .= "   cltb_cliente ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_cliente = '$id_cliente' AND excluido = 'f'";
		
   
		return( $this->bd->obtemUnicoRegistro($sSQL) );

	}

	private function obtemPOP($id_pop) {
		$sSQL = "SELECT ";
		$sSQL .= "   id_pop,nome,info,tipo,id_pop_ap ";
		$sSQL .= "FROM ";
		$sSQL .= "   cftb_pop ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_pop = '". $this->bd->escape($id_pop) . "' ";
		
		return( $this->bd->obtemUnicoRegistro($sSQL) );
	}
	
	private function obtemNAS($id_nas) {
		$sSQL = "SELECT ";
		$sSQL .= "   id_nas,nome,ip,secret,tipo_nas ";
		$sSQL .= "FROM ";
		$sSQL .= "   cftb_nas ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_nas = '". $this->bd->escape($id_nas) . "' ";
		
		return( $this->bd->obtemUnicoRegistro($sSQL) );
	}
	
	private function obtemIP($id_nas) {

		$sSQL = "SELECT ";
		$sSQL .= "   	i.ipaddr ";
		$sSQL .= "FROM ";
		$sSQL .= "   cntb_conta_bandalarga cbl RIGHT OUTER JOIN cftb_ip i USING(ipaddr), "; 
		$sSQL .= "   cftb_rede r,cftb_nas_rede nr, cftb_nas n ";
		$sSQL .= "WHERE ";
		$sSQL .= "	nr.id_nas = n.id_nas ";
		$sSQL .= "    AND r.rede = nr.rede ";
	   	$sSQL .= "	AND nr.id_nas = n.id_nas ";
	   	$sSQL .= "	AND n.id_nas=$id_nas ";
		$sSQL .= "   	AND i.ipaddr << r.rede ";
	   	$sSQL .= "	AND r.tipo_rede = 'C' ";
	   	$sSQL .= "	AND cbl.ipaddr is null ";
		$sSQL .= "ORDER BY ";
	   	$sSQL .= "	i.ipaddr ";
		$sSQL .= "LIMIT ";
	   	$sSQL .= "	1 ";

		return( $this->bd->obtemUnicoRegistro($sSQL) );
	
	}
	
	private function obtemRede($id_nas) {
		$sSQL = "SELECT ";
		$sSQL .= "   r.rede ";
		$sSQL .= "FROM ";
		$sSQL .= "   cntb_conta_bandalarga cbl RIGHT OUTER JOIN cftb_rede r USING(rede), cftb_nas_rede nr, cftb_nas n ";
		$sSQL .= "WHERE ";
		$sSQL .= "   nr.rede = r.rede ";
		$sSQL .= "   AND nr.id_nas = n.id_nas ";
		$sSQL .= "   AND n.id_nas=$id_nas ";
		$sSQL .= "   AND cbl.rede is null ";
		$sSQL .= "   and r.tipo_rede = 'C' ";
		$sSQL .= "ORDER BY ";
		$sSQL .= "   r.rede DESC ";
		$sSQL .= "LIMIT ";
		$sSQL .= "   1 ";
		//echo "retorno do obtemNAS: $sSQL;";
		return( $this->bd->obtemUnicoRegistro($sSQL) );
	}
	
	private function obtemDowUp($id_produto){
		$sSQL = "SELECT ";
		$sSQL .= "   banda_upload_kbps, ";
		$sSQL .= "   banda_download_kbps ";
		$sSQL .= "FROM ";
		$sSQL .= "   prtb_produto_bandalarga ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_produto = $id_produto";
		$sSQL .= "LIMIT ";
		$sSQL .= "   1 ";
		return( $this->bd->obtemUnicoRegistro($sSQL) );;	
	}

	public function processa($op=null) {
		$id_cliente = @$_REQUEST["id_cliente"];
		$tipo = @$_REQUEST["tipo"];
		// Variáveis gerais de template
		$this->tpl->atribui("op",$op);
		$this->tpl->atribui("id_cliente",$id_cliente);
		$this->tpl->atribui("tipo",$tipo);
		
		// Utilizado pelo menu ou por outras funcionalidades quaisquer.
		if( $id_cliente ) {
			$cliente = $this->obtemCliente($id_cliente);   
			$this->tpl->atribui("cliente",$cliente);
		}


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
					$reg = $this->obtemCliente($id_cliente);

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
					$tSQL .= "   rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
					$tSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
					$tSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, excluido ";
					$tSQL .= "   ativo,obs ";
					$tSQL .= "FROM ";
					$tSQL .= "   cltb_cliente ";
					$tSQL .= "WHERE ";
					$tSQL .= "   cpf_cnpj = '$cpf_cnpj' AND excluido = 'f'";
					
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
					$this->tpl->atribui("rg_expedicao",@$_REQUEST["rg_expedicao"]);
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
						$sSQL .= "   cltb_cliente ( ";
						$sSQL .= "      id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
						$sSQL .= "      rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
						$sSQL .= "      cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
						$sSQL .= "      fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
						$sSQL .= "      ativo,obs ) ";
						$sSQL .= "   VALUES (";
						$sSQL .= "     '" . $this->bd->escape($id_cliente) . "', ";
						$sSQL .= "     now(), ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome_razao"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo_pessoa"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["rg_inscr"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["rg_expedicao"]) . "', ";
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
						$sSQL .= "   rg_expedicao = '" . $this->bd->escape(@$_REQUEST["rg_expedicao"]) . "', ";
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

					//if( $this->bd->obtemErro() != MDATABASE_OK ) {
					//	echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
					//	echo "QUERY: " . $sSQL . "<br>\n";
					//
					//}


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
		        $this->tpl->atribui("rg_expedicao",@$reg["rg_expedicao"]);
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

				$texto_pesquisa = @$_REQUEST['texto_pesquisa'];
				$tipo_pesquisa = @$_REQUEST['tipo_pesquisa'];
				$a = @$_REQUEST['a'];
				$this->arquivoTemplate = "clientes_pesquisa.html";

				$texto_pesquisa = trim($texto_pesquisa);


				$where = "";


				if(!$tipo_pesquisa){
					$tipo_pesquisa = "NOME";
					
					
					//select dos ultimos cadastrados
					$aSQL  = "SELECT ";
					$aSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
					$aSQL .= "   rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
					$aSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
					$aSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
					$aSQL .= "   ativo,obs ";
					$aSQL .= "FROM cltb_cliente ";
					$aSQL .= "WHERE excluido = 'f' ";
					$aSQL .= "ORDER BY id_cliente DESC LIMIT (10)";		
					
					$clientes = $this->bd->obtemRegistros($aSQL);
					
				
				}else{
				
					if ($tipo_pesquisa == "NOME" || $tipo_pesquisa == "DOCTOS"){

						$sSQL  = "SELECT ";
						$sSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
						$sSQL .= "   rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
						$sSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
						$sSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
						$sSQL .= "   ativo,obs ";
						$sSQL .= "FROM cltb_cliente ";
						//$sSQL .= "WHERE $campo = '$campo_pesquisa' ";
						$sSQL .= "WHERE ";
						$sSQL .= " excluido = 'f' AND ";

						switch($tipo_pesquisa) {

						   case 'NOME':
							  $sSQL .= "   nome_razao ilike '%$texto_pesquisa%' ";
							  break;
						   case 'DOCTOS':
							  $sSQL .= "   cpf_cnpj = '" . $this->bd->escape(@$_REQUEST["texto_pesquisa"]) . "' OR rg_inscr = '" . $this->bd->escape(@$_REQUEST["texto_pesquisa"]) . "' ";
							  break;
						   case 'cod':
							  $sSQL .= "   id_cliente = '" . $this->bd->escape(@$_REQUEST["texto_pesquisa"]) . "' ";
							  break;
						}
						
						$clientes = $this->bd->obtemRegistros($sSQL);

											
					} else if ($tipo_pesquisa == "CONTA" || $tipo_pesquisa == "EMAIL"){
					
						if( $tipo_pesquisa == "CONTA" ) {
							$tp = "CONTA";
							if( preg_match( '/([0-9A-Fa-f]{1,2}[:\-]){5}([0-9A-Fa-f]{1,2})/', $texto_pesquisa) ) {
								// echo "MAC<br>\n";
								$tp = "MAC";
						    }
						               
						    if( preg_match( '/([0-9]\.){1,3}[0-9]{1,3}(\/[0-9]{1,2})*$/', $texto_pesquisa ) ) {
						    	$tp = "IP";
						    	
						    	@list($endIP,$bitsREDE) = explode("/",$texto_pesquisa);
						    	
						    	$qr = $bitsREDE ? $texto_pesquisa : $r = $endIP . "/24";
						    	$r = new RedeIP($qr);

						    	if( ! $r->isValid() ) {
						    	   $erros[] = "O endereço IP entrado não é válido.";
						    	} else {
						    	   if( $bitsREDE ) { 
						    	      $texto_pesquisa = $r->obtemRede() . "/" . $bitsREDE;
						    	   }
						    	}
						    }
						               
            			} else {
            				$tp = 'EMAIL';
            			}
            			

            			if( count($erros) ) {
            				$clientes = array();
            			} else {

							@list($usr,$dom) = explode("@",$texto_pesquisa);

							$campos_cliente = " cl.id_cliente,cl.nome_razao ";
							$campos_conta   = " cn.username,cn.dominio,cn.tipo_conta ";

							$from  = "	cntb_conta cn LEFT OUTER JOIN cntb_conta_bandalarga cbl USING(username,dominio,tipo_conta),  ";
							$from .= "	cbtb_cliente_produto cp, cltb_cliente cl ";
							//$sSQLBase .= "WHERE ";

							$whereJoin = "	cn.id_cliente_produto = cp.id_cliente_produto ";
							$whereJoin .= "	AND cp.id_cliente = cl.id_cliente ";
							$whereJoin .= " AND cl.excluido = 'f' ";


							$whereFiltro = "";
							switch($tp) {

								case 'EMAIL':
									$whereFiltro .= "	cn.dominio = '$dom'  ";

								case 'CONTA':

									$whereFiltro .= "	cn.username ilike '$usr' ";

									break;
								case 'MAC':
									$whereFiltro .= "	cbl.mac = '$texto_pesquisa' ";

									break;

								case 'IP':
									$whereFiltro .= "  ( ";

									if( strstr($texto_pesquisa,"/") ) {
									   // Rede
									   $whereFiltro .= " cbl.rede = '$texto_pesquisa' OR cbl.rede << '$texto_pesquisa' OR cbl.ipaddr << '$texto_pesquisa' ";
									} else {
									   // IP
									   $whereFiltro .= " cbl.rede >> '$texto_pesquisa' OR cbl.ipaddr = '$texto_pesquisa' ";

									}

									$whereFiltro .= " ) ";

									break;

							}

							$clientes = $this->bd->obtemRegistros("SELECT $campos_cliente FROM $from WHERE $whereJoin AND $whereFiltro GROUP BY $campos_cliente " );

							// Pega as contas
							for( $i=0;$i<count($clientes);$i++ ) {
							   $sSQL = "SELECT $campos_conta FROM $from WHERE $whereJoin AND $whereFiltro AND cl.id_cliente = '".$clientes[$i]["id_cliente"] ."' GROUP BY $campos_conta ";
							   $clientes[$i]["contas"] = $this->bd->obtemRegistros( $sSQL );
							}
						
						}
						
					}
					
					
				}
				
				$this->tpl->atribui("erros",$erros);
				$this->tpl->atribui("clientes",$clientes);
										
				$this->tpl->atribui("tipo_pesquisa",$tipo_pesquisa);
				$this->tpl->atribui("texto_pesquisa",$texto_pesquisa);

		} else if ($op == "cobranca") {
			// Sistema de contratação de produtos e resumo de cobrança
			
			$rotina = @$_REQUEST["rotina"];
			$acao = @$_REQUEST["acao"];
			$email_igual = @$_REQUEST['email_igual'];

			
			$this->tpl->atribui("rotina",$rotina);
			$this->arquivoTemplate = "cliente_cobranca.html";
			
			$erros = array();

			
			if( !$rotina ){ 
			$rotina = "contratar";
			}
			if( $rotina == "resumo" ) {
			
				$id_cliente = @$_REQUEST['id_cliente'];
			
			
				$sSQL  = "SELECT ";
				$sSQL .= "	cp.id_cliente_produto, cp.id_cliente, cp.id_produto, cp.dominio, ";
				$sSQL .= "	p.id_produto, p.nome, p.descricao, p.tipo, p.valor, p.disponivel, p.num_emails, p.quota_por_conta, ";
				$sSQL .= "	p.vl_email_adicional, p.permitir_outros_dominios, p.email_anexado ";
				$sSQL .= "FROM cbtb_cliente_produto cp INNER JOIN prtb_produto p ";
				$sSQL .= "USING( id_produto ) ";
				$sSQL .= "WHERE cp.id_cliente='$id_cliente' AND cp.excluido = 'f'";
				//echo $sSQL ."<hr>\n";
						
				//echo "SQL: $sSQL <br>\n";		
				$produtos = $this->bd->obtemRegistros($sSQL);
						
				for($i=0;$i<count($produtos);$i++) {
			
					$id_cp = $produtos[$i]["id_cliente_produto"];
						   
					$sSQL  = "SELECT ";
					$sSQL .= "	username, dominio, tipo_conta, id_conta ";
					$sSQL .= "FROM ";
					$sSQL .= "	cntb_conta ";
					$sSQL .= "WHERE ";
					$sSQL .= "	id_cliente_produto = '$id_cp'";
						   
					//echo $sSQL ."<hr>\n";
					
					$this->tpl->atribui("id_cliente",$id_cliente);
						   
					$contas = $this->bd->obtemRegistros($sSQL);
						   
					$produtos[$i]["contas"] = $contas;
				}
			
			
				
				$this->tpl->atribui("produtos",$produtos);
				$this->arquivoTemplate = "cliente_cobranca_resumo.html";
				
				

			} else if( $rotina == "contratar" ) {

				$enviando = false;
				$exibeForm = true;
				
				if($acao == "cad" ) {
					$enviando = true;
				}

				if( $enviando ) {
				
					// Pega dominio padrão 
					$sSQL  = "select dominio_padrao from cftb_preferencias";
					$lista_dominop = $this->bd->obtemUnicoRegistro($sSQL);
					$dominioPadrao = $lista_dominop["dominio_padrao"]; 

					// Valida os dados
					
					// TODO: Colocar isso em uma funcao private
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
					
						// pega id_cliente_prodruto
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
						//if( $this->bd->obtemErro() ) {
						//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
						//	echo "SQL: $sSQL <br>\n";
						//}
						
						$username = @$_REQUEST["username"];
						$dominio = @$_REQUEST["dominio"];
						$tipo_conta = @$_REQUEST["tipo_conta"];
						$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];
						
						
						//$sSQL  = "SELECT * from cntb_contas where username = '$username', dominio = '$dominio', tipo_conta = '$tipo_conta'";
						//$prep = $this->bd->obtemUnicoRegistro($sSQL);

								
								
//*HUGO					
						//$erros2 = array();
						//if (count($prep)){
						//	$erros2 = "Já existe um usuario com este dominio neste tipo de conta cadastrado. Por favor cadastre um novo usuario";
															
						//	$this->tpl->atribui("username", $username);
						//	$this->tpl->atribui("dominio_hospedagem",$dominio_hospedagem);
						//	$this->tpl->atribui("mensagem", $erros2);
						//	$this->tpl->atribui("url","clientes.php?op=pesquisa");
						//	$this->arquivoTemplate = "msgredirect.html";
						//	return;
						//}
//-*HUGO

						$senhaCr = $this->criptSenha($this->bd->escape(trim(@$_REQUEST["senha"])));
						
						$id_conta = $this->bd->proximoID("cnsq_id_conta");
						
						$sSQL  = "INSERT INTO ";
						$sSQL .= "   cntb_conta( ";
						$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript, status) ";
						$sSQL .= "   VALUES (";
						$sSQL .= "			'".$id_conta."', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
						$sSQL .= "     '" . $dominioPadrao . "', ";
						$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo"])) . "', ";
						$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
						$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
						$sSQL .= "     '" .	$id_cliente_produto . "', ";
						$sSQL .= "     '" . $senhaCr . "', ";
						$sSQL .= "     'A' )";						
												
						$this->bd->consulta($sSQL);  
						//if( $this->bd->obtemErro() ) {
						//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
						//	echo "SQL: $sSQL <br>\n";
						//}
						
						
						if ($email_igual == "1"){
//*HUGO							
							$sSQL  = "SELECT * from cftb_preferencias where id_provedor = '1'";
							$prefs = $this->bd->obtemUnicoRegistro($sSQL);

							
							if (count($prefs)){
								$erros2 = "Já existe um usuario com este dominio neste tipo de conta cadastrado. Por favor cadastre um novo usuario";
							
								$this->tpl->atribui("username", $username);
								$this->tpl->atribui("dominio_hospedagem",$dominio_hospedagem);
								$this->tpl->atribui("mensagem", $erros2);
								$this->tpl->atribui("url","clientes.php?op=pesquisa");
								$this->arquivoTemplate = "msgredirect.html";
								return;
							}
//-*HUGO
						
							$id_conta = $this->bd->proximoID("cnsq_id_conta");
							
							$sSQL  = "INSERT INTO ";
							$sSQL .= "   cntb_conta( ";
							$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript, status) ";
							$sSQL .= "   VALUES (";
							$sSQL .= "			'". $id_conta. "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
							$sSQL .= "     '" . $dominioPadrao . "', ";
							$sSQL .= "     'E', ";
							$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
							$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
							$sSQL .= "     '" .	$id_cliente_produto . "', ";
							$sSQL .= "     '" . $senhaCr . "', ";
							$sSQL .= "     'A' )";						
																		
							$this->bd->consulta($sSQL);  
							//if( $this->bd->obtemErro() ) {
							//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
							//	echo "SQL: $sSQL <br>\n";
							//}
							
							
							$id_produto = @$_REQUEST['id_produto'];
							$prod = $this->obtemProduto($id_produto);	
							
							if ($prod["quota_por_conta"] == "" || !$prod ){
								$quota = "0";
							}else {
								$quota = $produto["quota_por_conta"];
							}
							
							$sSQL  = "INSERT INTO ";
							$sSQL .= "	cntb_conta_email( ";
							$sSQL .= "		username, tipo_conta, dominio, quota, email) ";
							$sSQL .= "VALUES (";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
							$sSQL .= "     'E', ";
							$sSQL .= "     '" . $dominioPadrao . "', ";
							$sSQL .= "     '$quota', ";
							$sSQL .= "     '". $this->bd->escape(@$_REQUEST["username"])."@". $dominioPadrao ."' ";
							$sSQL .= " )";
							
							$this->bd->consulta($sSQL);
							//if( $this->bd->obtemErro() ) {
							//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
							//	echo "SQL: $sSQL <br>\n";
							//}

							
						
						}
						
						
						$tipo = @$_REQUEST["tipo"];
						
						//PEGA CAMPOS COMUNS EM cftb_preferencias
						
						$sSQL  = "SELECT * from cftb_preferencias where id_provedor = '1'";
						$prefs = $this->bd->obtemUnicoRegistro($sSQL);
						
						
						switch($tipo) {
							case 'D':
								
								$username = @$_REQUEST["username"];
								$tipo_conta = @$_REQUEST["tipo"];
								$dominio = $prefs["dominio_padrao"];
								$foneinfo = @$_REQUEST["foneinfo"];
								
								$sSQL  = "INSERT INTO ";
								$sSQL .= "cntb_conta_discado ";
								$sSQL .= "( ";
								$sSQL .= "username, tipo_conta, dominio, foneinfo ";
								$sSQL .= ")VALUES ( ";
								$sSQL .= "'$username', '$tipo_conta', '$dominio', '$foneinfo' )";
								
								//echo "SQL DISCADO: $sSQL <br>\n";
								
								$this->bd->consulta($sSQL);
								
								$this->tpl->atribui("foneinfo",$foneinfo);
								
							break;	
							case 'BL':
								// PRODUTO BANDA LARGA
								$tipo_de_ip = $this->bd->escape(trim(@$_REQUEST["selecao_ip"]));
								if($tipo_de_ip == "A"){
									$nas = $this->obtemNAS($_REQUEST["id_nas"]);
									//echo "NAS: ".$nas["id_nas"]."<BR>";
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
									
								}
								
								
								if($rede_disp != "NULL"){
								
									$rede_disp = "'".$rede_disponivel["rede"]."'";
									//echo "rede:". $rede_disponivel["rede"]. "<br>";
								
								
								}
								
								if($ip_disp !="NULL"){
								
									$ip_disp = "'".$ip_disponivel["ipaddr"]."'";
								
								
								}
								
								$id_produto = $this->bd->escape(@$_REQUEST["id_produto"]);
								$bandaUp_dow = $this->obtemDowUp($id_produto);
								$MAC = @$_REQUEST["mac"];
								
								if($MAC ==""){
									$_MAC = "NULL";
								}else {
									$_MAC = "'".$MAC."'";
								}
								
								//$id_conta_banda_larga = $this->bd->proximoID("clsq_id_conta_bandalarga_seq");
								
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
								$sSQL .= "      mac ";
								$sSQL .= ") ";
								$sSQL .= "   VALUES (";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"])  . "', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo"])). "', ";
								$sSQL .= "     '" . $dominioPadrao . "', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_pop"])) . "', ";
								$sSQL .= "     '" . $nas["tipo_nas"] . "', ";
								$sSQL .= "     " . $ip_disp . ", ";
								$sSQL .= "     " . $rede_disp . ", ";
								$sSQL .= "     '" . $bandaUp_dow["banda_upload_kbps"] . "', ";
								$sSQL .= "     '" . $bandaUp_dow["banda_download_kbps"] . "', ";
								$sSQL .= "     'A', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_nas"])) . "', ";
								$sSQL .= "     ". $_MAC ." ";
								$sSQL .= "     )";						
								
								
								//ECHO "INSERT NA BL: $sSQL <br>";
								$this->bd->consulta($sSQL);  
								//if( $this->bd->obtemErro() ) {
								//	echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
								//	echo "SQL: $sSQL <br>\n";
								//}
								
								break;
								
							case 'H':
								// PRODUTO HOSPEDAGEM
//*HUGO								
								//ECHO "BOSTA";
								$sSQL  = "SELECT * from cftb_preferencias where id_provedor = '1'";
								$prefs = $this->bd->obtemUnicoRegistro($sSQL);
								
								
						
								$username = @$_REQUEST["username"];
								$tipo_conta = @$_REQUEST["tipo"];
								$dominio = $prefs["dominio_padrao"];
								$tipo_hospedagem = @$_REQUEST["tipo_hospedagem"];
								$senha_cript = $this->criptSenha(@$_REQUEST["senha"]);
								$uid = $prefs["hosp_uid"];
								$gid = $prefs["hosp_gid"];
								$home = $prefs["hosp_base"];
								$shell = "/bin/false";
								$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];
								$server = $prefs["hosp_server"];
								
								$sSQL  = "select * from cntb_conta where username = $username AND tipo_conta = $tipo_conta AND dominio = $dominio";
								$prep = $this->bd->obtemRegistros($sSQL);
								


								
								
								//if (!count($erros2)){
									$sSQL  = "INSERT INTO ";
									$sSQL .= " cntb_conta_hospedagem ( ";
									$sSQL .= "		username, tipo_conta, dominio, tipo_hospedagem, senha_cript, uid, gid, home, shell, dominio_hospedagem ";
									$sSQL .= ") VALUES ( ";
									$sSQL .= " 		'$username', '$tipo_conta', '$dominio', '$tipo_hospedagem', '$senha_cript', '$uid', '$gid', '$home', '$shell', '$dominio_hospedagem' ";
									$sSQL .= ") ";

									$this->bd->consulta($sSQL);
									//echo "QUERY INSERÇÃO: $sSQL <BR>\n";



									//SPOOL
									$this->spool->hospedagemAdicionaRede($server,$id_conta,$tipo_hospedagem,$username,$dominio,$dominio_hospedagem);
								//}
//*-HUGO								
								break;

						}
						
						
						
						if ($tipo && $tipo == "BL"){
						
						//echo $tipo;
							// Envia instrucao pra spool
							if ($nas && $nas["tipo_nas"] == "I"){

								$id_nas = $_REQUEST["id_nas"];
								$banda_upload_kbps = $bandaUp_dow["banda_upload_kbps"];
								$banda_download_kbps = $bandaUp_dow["banda_download_kbps"];
								$rede = $rede_disponivel["rede"];
								$mac = $_REQUEST["mac"];

								$sSQL  = "SELECT ";
								$sSQL .= "   id_nas, nome, ip, tipo_nas ";
								$sSQL .= "FROM ";
								$sSQL .= "   cftb_nas ";
								$sSQL .= "WHERE ";
								$sSQL .= "   id_nas = '$id_nas'";
								//echo "SQL : " . $sSQL . "<br>\n";

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
								$destino = $nas['ip'];	

								//ECHO "BOSTA'";
								$username = @$_REQUEST["username"];
								$this->spool->bandalargaAdicionaRede($destino,$id_conta,$rede,$mac,$banda_upload_kbps,$banda_download_kbps,$username);



							}
						
							// LISTA DE POPS
							$sSQL  = "SELECT ";
							$sSQL .= "   id_pop, nome ";
							$sSQL .= "FROM ";
							$sSQL .= "   cftb_pop ";
							$sSQL .= "WHERE ";
							$sSQL .= "   id_pop = '". $this->bd->escape(trim(@$_REQUEST["id_pop"])) ."'";
						
							$lista_pops = $this->bd->obtemUnicoRegistro($sSQL);
							
						}
							
							

						
						// Joga a mensagem de produto contratado com sucesso.
						$this->tpl->atribui("username",@$_REQUEST["username"]);
						//$this->tpl->atribui("tipo_produto",$tipo_produto);
						$this->tpl->atribui("pop",@$lista_pops["nome"]);
						$this->tpl->atribui("nas",@$nas["nome"]);
						$this->tpl->atribui("mac",@$_MAC);
						$this->tpl->atribui("ip",@$ip_disp);
						$this->tpl->atribui("dominio",$prefs["dominio_padrao"]);
						$this->tpl->atribui("dominio_hospedagem",@$dominio_hospedagem);
						
						
						
						
						
						
						
						
						$this->arquivoTemplate="cliente_cobranca_intro.html";
											
						
						return;
						
						$exibeForm = false;
						
						
						
					}else{
					
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
					$sSQL .= "   id_produto,nome,descricao,tipo,valor ";
					$sSQL .= "";
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

					$this->tpl->atribui("lista_discado",$lista_discado);
					$this->tpl->atribui("lista_bandalarga",$lista_bandalarga);
					$this->tpl->atribui("lista_hospedagem",$lista_hospedagem);

					// LISTA DE POPS
					$sSQL  = "SELECT ";
					$sSQL .= "   id_pop, nome ";
					$sSQL .= "FROM ";
					$sSQL .= "   cftb_pop ";
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
					
					
					
					
					
					
					$this->tpl->atribui("lista_nas",@$lista_nas);
					
					
					$this->tpl->atribui("msg",@$_REQUEST["msg"]);
					$this->tpl->atribui("tipo",$tipo);
					$this->tpl->atribui("username",@$_REQUEST["username"]);
					$this->tpl->atribui("dominio",@$_REQUEST["dominio"]);
					$this->tpl->atribui("id_pop",@$_REQUEST["id_pop"]);
					$this->tpl->atribui("id_nas",@$_REQUEST["id_nas"]);
					$this->tpl->atribui("selecao_ip",@$_REQUEST["selecao_ip"]);
					$this->tpl->atribui("endereco_ip",@$_REQUEST["endereco_ip"]);
					$this->tpl->atribui("mac",@$_REQUEST["mac"]);
					
					$this->tpl->atribui("id_produto",@$_REQUEST["id_produto"]);

				}

				
				
			} else if( $rotina == "relatorio" ) {
				

				$this->arquivoTemplate = "cliente_cobranca_relatorio.html";
			
			} else if( $rotina == "excluir" ){
			
				ECHO "PASSO 1 DA EXCLUSÃO: executa o excluiContrato";
				
				$id_cliente_produto = @$_REQUEST['id_cliente_produto'];
				$id_cliente = @$_REQUEST['id_cliente'];
				$permanente = $_REQUEST['permanente'];
				
				$this->excluiContrato($id_cliente_produto,$permanente);
				
				$this->tpl->atribui("mensagem","Conta Excluida com Sucesso! "); 
				$this->tpl->atribui("url","clientes.php?op=cobranca&rotina=resumo&id_cliente=$id_cliente");
				$this->tpl->atribui("target","_top");
				
				$this->arquivoTemplate = "msgredirect.html";
			
			
			
			
			}else if ( $rotina == "excl_confirma"){
			
				$sSQL  = "SELECT ";
				$sSQL .= "	cp.id_cliente_produto, cp.id_cliente, cp.id_produto, cp.dominio, ";
				$sSQL .= "	p.id_produto, p.nome, p.descricao, p.tipo, p.valor, p.disponivel, p.num_emails, p.quota_por_conta, ";
				$sSQL .= "	p.vl_email_adicional, p.permitir_outros_dominios, p.email_anexado ";
				$sSQL .= "FROM cbtb_cliente_produto cp INNER JOIN prtb_produto p ";
				$sSQL .= "USING( id_produto ) ";
				$sSQL .= "WHERE cp.id_cliente_produto='".@$_REQUEST['id_cliente_produto']."' ";
				//echo $sSQL ."<hr>\n";
			
			
				$produtos = $this->bd->obtemRegistros($sSQL);
			
				for($i=0;$i<count($produtos);$i++) {

				   $id_cp = $produtos[$i]["id_cliente_produto"];
				   
				   $sSQL  = "SELECT ";
				   $sSQL .= "	username, dominio, tipo_conta, id_conta ";
				   $sSQL .= "FROM ";
				   $sSQL .= "	cntb_conta ";
				   $sSQL .= "WHERE ";
				   $sSQL .= "	id_cliente_produto = '$id_cp'";
			   
				   //echo $sSQL ."<hr>\n";
			   
				   $contas = $this->bd->obtemRegistros($sSQL);
				   
				   $produtos[$i]["contas"] = $contas;
			
				}
			
			
			$this->tpl->atribui("produtos",$produtos);				
			$this->arquivoTemplate = "confirma_exclusao.html";
			return;
			
			
			}
			
			
			
		
		
		} else if ($op == "produto") {


			// PRECISA PASSAR O TIPO PRO MENU
			//$tipo = @$_REQUEST["tipo"];
			
			
			//SELECTS PARA POPULAR OS CAMPOS DROP-DOWN
						
			//Lista de Clientes
			//$aSQL  = "SELECT ";
			//$aSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
			//$aSQL .= "   rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
			//$aSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
			//$aSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
			//$aSQL .= "   ativo,obs ";
			//$aSQL .= "FROM cltb_cliente ";
			
			
			//Lista de Produtos
			//$bSQL  = "SELECT ";
			//$bSQL .= "   id_produto, nome, descricao, tipo, ";
			//$bSQL .= "   valor, disponivel ";
			//$bSQL .= "FROM prtb_produto ";
			
			
			//Lista de POP
			//$cSQL  = "SELECT ";
			//$cSQL .= "   id_pop, nome, info, tipo, id_pop_ap ";
			//$cSQL .= "FROM cftb_pop ";
			
					
			//Lista de NAS
			//$dSQL  = "SELECT ";
			//$dSQL .= "   id_nas, nome, ip, secret, tipo_nas ";
			//$dSQL .= "FROM cftb_nas ";
			//$dSQL .= "WHERE id_nas = '$id_nas'";
			
			//Pega Provedor Padrão
			//$eSQL  = "SELECT ";
			//$eSQL .= "	id_provedor, dominio_padrao, nome, localidade ";
			//$eSQL .= "FROM ";
			//$eSQL .= "cftb_provedor ";
			//$eSQL .= "WHERE ";
			//$eSQL .= "id_provedor = '1'";
			
			//Lista Dominios
			//$fSQL  = "SELECT ";
			//$fSQL .= "	dominio, id_cliente ";
			//$fSQL .= "FROM ";
			//$fSQL .= "	dominio ";
			//$fSQL .= "WHERE ";
			//$fSQL .= "	id_cliente = '". $this->bd->escape(@$_REQUEST["id_cliente"]) ."' ";
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			//$lista_clientes = $this->bd->obtemRegistros($aSQL);
			//$lista_produtos = $this->bd->obtemRegistros($bSQL);
			//$lista_pop = $this->bd->obtemRegistros($cSQL);
			//$lista_nas = $this->bd->obtemRegistros($dSQL);
			//$provedor_padrao = $this->bd->obtemUnicoRegistro($eSQL);
			//$dominios = $this->bd->obtemUnicoRegistro($fSQL);
			
			
			//Atribuição de valores ao template
			//$this->tpl->atribui($lista_clientes);
			//$this->tpl->atribui($lista_produtos);
			//$this->tpl->atribui($lista_pop);
			//$this->tpl->atribui($lista_nas);
			//$this->tpl->atribui($provedor_padrao);
			//$this->tpl->atribui($dominios);
			
			//$this->tpl->atribui("tipo",$tipo);
			
			
			$sSQL  = "SELECT ";
			$sSQL .= "	cp.id_cliente_produto, cp.id_cliente, cp.id_produto, cp.dominio, ";
			$sSQL .= "	p.id_produto, p.nome, p.descricao, p.tipo, p.valor, p.disponivel, p.num_emails, p.quota_por_conta, ";
			$sSQL .= "	p.vl_email_adicional, p.permitir_outros_dominios, p.email_anexado ";
			$sSQL .= "FROM cbtb_cliente_produto cp INNER JOIN prtb_produto p ";
			$sSQL .= "USING( id_produto ) ";
			$sSQL .= "WHERE cp.id_cliente='$id_cliente' AND p.tipo = '$tipo' ";
			//echo $sSQL ."<hr>\n";
			
			
			$produtos = $this->bd->obtemRegistros($sSQL);
			
			for($i=0;$i<count($produtos);$i++) {

			   $id_cp = $produtos[$i]["id_cliente_produto"];
			   
			   $sSQL  = "SELECT ";
			   $sSQL .= "	username, dominio, tipo_conta, id_conta ";
			   $sSQL .= "FROM ";
			   $sSQL .= "	cntb_conta ";
			   $sSQL .= "WHERE ";
			   $sSQL .= "	id_cliente_produto = '$id_cp'";
			   
			   //echo $sSQL ."<hr>\n";
			   
			   $contas = $this->bd->obtemRegistros($sSQL);
			   
			   $produtos[$i]["contas"] = $contas;
			
			}
			
			
			$this->tpl->atribui("produtos",$produtos);
			
			$this->arquivoTemplate = "cliente_produto.html";
			
		} else if ($op == "conta") {
			$erros = array();
		
			$id_cliente = @$_REQUEST["id_cliente"];
			$username = @$_REQUEST["username"];
			$dominio  = @$_REQUEST["dominio"];
			$tipo_conta = @$_REQUEST["tipo_conta"];
			
			$this->tpl->atribui("id_cliente",$id_cliente);
			$this->tpl->atribui("username",$username);
			$this->tpl->atribui("dominio",$dominio);
			$this->tpl->atribui("tipo_conta",$tipo_conta);
			
			
			/*******************************
			 * DADOS GERAIS                *
			 *******************************/

			// LISTA DE POPS
			$sSQL  = "SELECT ";
			$sSQL .= "   id_pop, nome ";
			$sSQL .= "FROM ";
			$sSQL .= "   cftb_pop ";
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
			
			$lista_nas = $this->bd->obtemRegistros($sSQL);
			
			global $_LS_TIPO_NAS;

			for($i=0;$i<count($lista_nas);$i++) {
			   $lista_nas[$i]["tp"] = $_LS_TIPO_NAS[ $lista_nas[$i]["tipo_nas"] ];
			}

			$this->tpl->atribui("lista_nas",$lista_nas);

			$sSQL  = "SELECT ";
			$sSQL .= "   username, dominio, tipo_conta, senha, status, id_conta, id_cliente_produto ";
			$sSQL .= "";
			$sSQL .= "FROM ";
			$sSQL .= "   cntb_conta ";
			$sSQL .= "";
			$sSQL .= "WHERE ";
			$sSQL .= "   username = '".$this->bd->escape($username)."' ";
			$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
			$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
			$sSQL .= "";
			
			//ECHO "sql conta: $sSQL <br>/n";
			$conta = $this->bd->obtemUnicoRegistro($sSQL);
			
			
			
			/** PEGA O PRODUTO CONTRATADO */
			
			$sSQL  = "SELECT ";
			$sSQL .= "   p.id_produto, p.nome, p.tipo ";
			$sSQL .= "";
			$sSQL .= "FROM ";
			$sSQL .= "   cbtb_cliente_produto cp INNER JOIN prtb_produto p USING (id_produto) ";
			$sSQL .= "WHERE ";
			$sSQL .= "   id_cliente_produto = '".$conta["id_cliente_produto"]."'";
			$sSQL .= "";
			
			$produto = $this->bd->obtemUnicoRegistro($sSQL);
			
			$tipo = $produto["tipo"]; // DO CONTRATO
			$this->tpl->atribui("tipo",$tipo);
			
			global $_LS_ST_CONTA;
			$this->tpl->atribui("lista_status",$_LS_ST_CONTA);

			
			/*******************************
			 * Carrega Dados Específicos   *
			 *******************************/
			
			switch($tipo_conta) {
			
				case 'D':
				
					$sSQL  = "SELECT ";
					$sSQL .= "	username, foneinfo ";
					$sSQL .= "FROM ";
					$sSQL .= "	cntb_conta_discado ";					
					$sSQL .= "WHERE ";
					$sSQL .= "   username = '".$this->bd->escape($username)."' ";
					$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
					$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
					
					//echo "SQL: $sSQL <BR>";
					
					$dsc = $this->bd->obtemUnicoRegistro($sSQL);					
					$conta = array_merge($conta,$dsc);

				
				
				
					break;
				case 'BL':
					$sSQL  = "SELECT ";
					$sSQL .= "   cbl.id_pop, cbl.tipo_bandalarga, cbl.ipaddr, cbl.rede, cbl.id_nas, cbl.mac, cbl.upload_kbps, cbl.download_kbps "; // alterei
					$sSQL .= "";
					$sSQL .= "FROM ";
					$sSQL .= "   cntb_conta_bandalarga cbl ";
					$sSQL .= "";
					$sSQL .= "WHERE ";
					$sSQL .= "   cbl.username = '".$this->bd->escape($username)."' ";
					$sSQL .= "   AND cbl.dominio = '".$this->bd->escape($dominio)."' ";
					$sSQL .= "   AND cbl.tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
					$sSQL .= "";
					$sSQL .= "";
					$sSQL .= "";
					$sSQL .= "";
					$sSQL .= "";

					//echo "$sSQL;<br>\n";



					$cbl = $this->bd->obtemUnicoRegistro($sSQL);

					$conta = array_merge($conta,$cbl);


					$this->tpl->atribui("status",@$_REQUEST["status"]);
					$this->tpl->atribui("upload_kbps",@$_REQUEST["upload_kbps"]);
					$this->tpl->atribui("download_kbps",@$_REQUEST["download_kbps"]);

					//echo "ID NAS:". $conta["id_nas"] ."<BR>";

					$nas = $this->obtemNas($conta["id_nas"]);			
					$conta["endereco_ip"] = $nas["tipo_nas"] == "I" ? $conta["rede"] : $conta["ipaddr"];




					//$nas_orig = @$_REQUEST["nas_orig"];

					$endereco_ip = @$_REQUEST["endereco_ip"];
					if( !$endereco_ip ) $endereco_ip = $conta["endereco_ip"];

					$selecao_ip = @$_REQUEST["selecao_ip"];


					// ATRIBUI AS VARIAVEIS DE TEMPLATE COM BASE EM REQUEST.
					global $_LS_BANDA;
					$this->tpl->atribui("lista_upload",$_LS_BANDA);
					$this->tpl->atribui("lista_download",$_LS_BANDA);

					$altera_rede = @$_REQUEST["altera_rede"];
					$this->tpl->atribui("altera_rede",$altera_rede);
					$this->tpl->atribui("selecao_ip",$selecao_ip);
					$this->tpl->atribui("endereco_ip",$endereco_ip);


					//if( !$nas_orig ) 
					$nas_orig = $nas["id_nas"];
					$this->tpl->atribui("nas_orig",$nas_orig);
					break;

				case 'E':
					$sSQL  = "SELECT ";
					$sSQL .= "	ce.username, ce.dominio, ce.tipo_conta, ce.quota, ce.email ";
					$sSQL .= "FROM ";
					$sSQL .= "	cntb_conta_email ce ";
					$sSQL .= "WHERE ";
					$sSQL .= "	ce.username = '$username'";
					$sSQL .= "	AND ce.dominio = '$dominio'";
					$sSQL .= "	AND ce.tipo_conta = '$tipo_conta'";
					
					$conta_email = $this->bd->obtemUnicoRegistro($sSQL);
					$conta = array_merge($conta,$conta_email);
					
					$sSQL  = "SELECT ";
					$sSQL .= "	* ";
					$sSQL .= "FROM ";
					$sSQL .= "	cftb_preferencias ";
					
					$server = $this->bd->obtemUnicoRegistro($sSQL);
					
					$this->tpl->atribui("quota",$conta["quota"]);
					$this->tpl->atribui("server",$server);


					break;

				case 'H':
					$sSQL  = "SELECT ";
					$sSQL .= "   username, dominio, tipo_conta, tipo_hospedagem, senha_cript, uid, gid, home, shell, dominio_hospedagem "; 
					$sSQL .= "";
					$sSQL .= "FROM ";
					$sSQL .= "   cntb_conta_hospedagem ";
					$sSQL .= "";
					$sSQL .= "WHERE ";
					$sSQL .= "   username = '".$this->bd->escape($username)."' ";
					$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
					$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
				
					//echo "$sSQL;<br>\n";
				
				
				
					$hosp = $this->bd->obtemUnicoRegistro($sSQL);
				
					$conta = array_merge($conta,$hosp);
					

				
				
				
					break;

			}

			$acao = @$_REQUEST["acao"];
			$senha = @$_REQUEST["senha"];
			
			if( $acao == "cad" ) {
				// Processar
				
				/********************************
				 * Verificações                 *
				 *******************************/
				
				switch ($tipo_conta) {
					case 'D':
						break;
					
				
					case 'BL':

						$id_nas = @$_REQUEST["id_nas"];
						$id_pop = @$_REQUEST["id_pop"];
						$status = @$_REQUEST["status"];

						$mac    		= @$_REQUEST["mac"];
						$upload_kbps	= @$_REQUEST["upload_kbps"];
						$download_kbps  = @$_REQUEST["download_kbps"];

						$rede			= @$_REQUEST["rede"];

						$endereco_ip	= @$_REQUEST["endereco_ip"];

						$endereco_rede = "";


						$excluir = false;
						$incluir = false;

						$altstatus = false;

						// VERDADES ABSOLUDAS DO ALÉM
						// exclusão é sempre no nas antigo ($conta["id_nas"])
						// inclusão é sempre no nas novo ($id_nas)


						// SPOOL SE:

						// Alterar status
						// Alterar mac
						// Alterar banda
						// Alterar endereco

						$nas_atual = $this->obtemNas($conta["id_nas"]);
						$nas_novo  = $this->obtemNas($id_nas);

						$rede = $conta["rede"];
						
						//echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";
						//echo "UP: " . $conta["upload_kbps"] . "|" . $upload_kbps . "<br>\n";
						//echo "DN: " . $conta["download_kbps"] . "|" . $download_kbps . "<br>\n";
						//echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";
						//echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";

						if( $conta["mac"] != $mac || $conta["upload_kbps"] != $upload_kbps || $conta["download_kbps"] != $download_kbps ) {
							$excluir = true;
							$incluir = true;
						}

						if( $altera_rede && $selecao_ip == "A" ) {
							$endereco_rede = $this->obtemRede($id_nas);
							$rede = $endereco_rede["rede"];
							$excluir = true;
							$incluir = true;
						}

						if( $id_nas != $nas_orig ) {

							if( $nas_atual["tipo_nas"] == "I" ) {
								$excluir = true;
							} else {
								$excluir = false;
							}

							if( $nas_novo["tipo_nas"] == "I" ) {
								$incluir = true;
							} else {
								$incluir = false;
							}

						}

						if( $conta["status"] != $status ) {
							if( $status != "A" ) {
								$excluir = true;
								$incluir = false;
							} else {
								$excluir = false;
								$incluir = true;
							}
						}

						break;
					case 'E':
						break;

					case 'H':
						break;
				
				}


				/********************************
				 * PROCESSAMENTO                *
				 * Se não houver erros	        *
				 *******************************/
				
				if( !count($erros) ) {
				
				$tipo_conta = trim(@$_REQUEST["tipo_conta"]);
				
				
					switch($tipo_conta) {
						case 'D':
						
							
							$senha_cript = $this->criptSenha($senha);
													
														
							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta_discado ";
							$sSQL .= "SET ";
							$sSQL .= "	foneinfo = '".@$_REQUEST['foneinfo']."' ";
							$sSQL .= "WHERE ";
							$sSQL .= "	username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";
							//echo "SQL: $sSQL <br>";
													
							$this->bd->consulta($sSQL);
							
							
							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta ";
							$sSQL .= "SET ";
							$sSQL .= "senha = '$senha', ";
							$sSQL .= "senha_cript = '$senha_cript' ";
							$sSQL .= "WHERE ";
							$sSQL .= "username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";
							//echo "SQL: $sSQL <br>";
													
							$this->bd->consulta($sSQL);

						
							break;
					
						case 'BL':
						//echo "TESTE";
				
							// SPOOL
							if( $excluir ) {
								//echo "excluir";
								$this->spool->bandalargaExcluiRede($nas_atual["ip"],$conta["id_conta"],$conta["rede"]);
							}

							if( $incluir ) {
								//echo "incluir<br>";
								$id_conta = $conta["id_conta"];
								$this->spool->bandalargaAdicionaRede($nas_novo["ip"],$id_conta,$rede,$mac,$upload_kbps,$download_kbps,$username);
							}

							// Faz o update nos dados em cntb_conta e cntb_conta_bandalarga

							$sSQL  = "UPDATE ";
							$sSQL .= "   cntb_conta ";
							$sSQL .= "SET ";
							$sSQL .= "   status = '".$this->bd->escape($status)."' ";
							if( $senha ) {
								$sSQL .= "   , senha = '".$this->bd->escape($senha)."' ";
								$sSQL .= "   , senha_cript = '".$this->criptSenha($senha)."' ";
							}

							$sSQL .= "WHERE ";
							$sSQL .= "   username = '".$this->bd->escape($username)."' ";
							$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
							$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
							$sSQL .= "";

							//echo "$sSQL;<br>\n";

							$this->bd->consulta($sSQL);

							$sSQL  = "UPDATE ";
							$sSQL .= "   cntb_conta_bandalarga ";
							$sSQL .= "SET ";


							$sSQL .= "   id_nas = '".$this->bd->escape($id_nas)."', ";
							$sSQL .= "   id_pop = '".$this->bd->escape($id_pop)."', ";
							$sSQL .= "   upload_kbps = '".$this->bd->escape($upload_kbps)."', ";
							$sSQL .= "   download_kbps = '".$this->bd->escape($download_kbps)."', ";
							$sSQL .= "   mac = '".$this->bd->escape($mac)."' ";

							if( $rede ) {
								$sSQL .= ", ipaddr = null, ";
								$sSQL .= "  rede = '".$rede."' ";
							}

							$sSQL .= "WHERE ";
							$sSQL .= "   username = '".$this->bd->escape($username)."' ";
							$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
							$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
							$sSQL .= "";

							//echo "$sSQL;<br>\n";
							$this->bd->consulta($sSQL);

						break;
						
						case 'E':

							$quota = @$_REQUEST["quota"];
							$senha = @$_REQUEST["senha"];
							$senha_cript = $this->criptSenha($senha);
							$id_conta = $conta["id_conta"];


							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta_email ";
							$sSQL .= "SET ";
							$sSQL .= "	quota = '$quota' ";
							$sSQL .= "WHERE ";
							$sSQL .= "	username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";

							//echo "SQL1: $sSQL <br>\n";


							$this->bd->consulta($sSQL);

							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta ";
							$sSQL .= "SET ";
							$sSQL .= "senha = '$senha', ";
							$sSQL .= "senha_cript = '$senha_cript' ";
							$sSQL .= "WHERE ";
							$sSQL .= "username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";

							//echo "SQL1: $sSQL <br>\n";

							$this->bd->consulta($sSQL);
							


							break;
							
						case 'H':
							
							$tipo_hospedagem = @$_REQUEST["tipo_hospedagem"];
							$senha = @$_REQUEST["senha"];
							$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];
							$senha_cript = $this->criptSenha($senha);
							$id_conta = $conta["id_conta"];
							$server = $conta["mail_server"];
							$dominio_padrao = $conta["dominio_padrao"];
														
							
							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta_hospedagem ";
							$sSQL .= "SET ";
							$sSQL .= "	dominio_hospedagem = '$dominio_hospedagem', ";
							$sSQL .= "	senha_cript = '$senha_cript' ";
							$sSQL .= "WHERE ";
							$sSQL .= "	username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";
							//echo "SQL: $sSQL <br>";
						
							$this->bd->consulta($sSQL);
							
							
							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta ";
							$sSQL .= "SET ";
							$sSQL .= "senha = '$senha', ";
							$sSQL .= "senha_cript = '$senha_cript' ";
							$sSQL .= "WHERE ";
							$sSQL .= "username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";
							//echo "SQL: $sSQL <br>";
						
							$this->bd->consulta($sSQL);

						
							break;
					}

					// Exibe mensagem (joga pra msgredirect)
					$this->tpl->atribui("mensagem","Conta Alterada com sucesso!");
					$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=pesquisa");
					$this->tpl->atribui("target","_top");

								   
					$this->arquivoTemplate = "msgredirect.html";

					// Cai fora.
					return;
					
					
				}


			   		$destino = $nas_status["ip"];
			   		$id_conta = $status["id_conta"];
			   		$rede = $status["rede"];
			   		$mac = $status["mac"];
			   		$banda_upload_kbps = $status["banda_upload_kbps"];
			   		$banda_download_kbps = $status["banda_download_kbps"];
			   		
			   		
			   		
			   		
			   		if($status["status"] == "A"){
			   		
			   			
			   		
			   		
			   		
			   		}else {
			   		
			   			$sSQL  = "SELECT ";
						$sSQL .= "   id_nas, nome, ip, tipo_nas ";
						$sSQL .= "FROM ";
						$sSQL .= "   cftb_nas ";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_nas = '".$id_nas."' ";
										
						$nas_status_novo = $this->bd->obtemUnicoRegistro($sSQL);
						
						$destino = $nas_status["ip"];						
			   			
			   			//$this->spool->bandalargaAdicionaRede($destino,$id_conta,$rede,$mac,$banda_upload_kbps,$banda_download_kbps);
						
			   			
			   			
			   		}
			   		
			   		// Se estava manda uma requisição de exclusão pro NAS ($conta["id_nas"]) - via spool
			   		
			   		// Caso contrário manda uma requisição de inclusão pro NAS ($id_nas) - via spool

			   
			   
			   // Exibe que a conta foi alterada com sucesso e papoca fora.
			   
			   $this->tpl->atribui("mensagem","Conta Alterada com sucesso!");
			   
			   $this->arquivoTemplate = "msgredirect.html";
			   
			   return;
			  
			   
			
			
			} else {
				while( list($nome,$valor)=each($conta) ){
					$this->tpl->atribui($nome,$valor);
				}
			
			}
			
			
			
			if( $tipo_conta == "D" || $tipo_conta == "H" ) {
				// Exibir mensagem de indisponivel
				//echo "indisponivel";
				//return;
			}
			
			// Trata o tipo de exibicao.
			$pg = @$_REQUEST["pg"];
			
			if( $pg == "ficha" ) {
				$this->tpl->atribui("str_status",$_LS_ST_CONTA[ $conta["status"] ]);
				
				switch($tipo_conta) {
				
					case 'D':
						// Consulta específica de discado
						break;

					case 'BL':
						// Consulta específica de banda larga
						
						$nas = $this->obtemNas($conta["id_nas"]);
						$pop = $this->obtemPop($conta["id_nas"]);
						$this->tpl->atribui("nas",$nas);
						$this->tpl->atribui("pop",$pop);

						//echo $nas;
						//echo $pop;

						if( $nas["tipo_nas"] == "I" ) {
						   $r = new RedeIP($endereco_ip);

						   $gateway    = $r->minHost();
						   $mascara    = $r->mascara();
						   $ip_cliente = $r->maxHost();

						   $this->tpl->atribui("ip_cliente",$ip_cliente);
						   $this->tpl->atribui("gateway",$gateway);
						   $this->tpl->atribui("mascara",$mascara);
						}
						
						break;
						
					case 'E':
						// Consulta específica do e-mail
						break;

					case 'H':
						// Consulta especifica da hospedagem
						
						
						
						
						break;
				}
				
				$this->arquivoTemplate = "cliente_ficha.html";
			} else {
				$this->arquivoTemplate = "cliente_conta.html";
			}

		
		} else if ($op == "helpdesk") {
		
			$this->arquivoTemplate = "cliente_helpdesk.html";
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
			
		} else if ($op =="altera_contrato"){
		
				$sSQL  = "SELECT ";
				$sSQL .= "	username, tipo_conta, id_conta ";
				$sSQL .= "FROM ";
				$sSQL .= "	cntb_conta ";
				$sSQL .= "WHERE ";
				$sSQL .= "	id_conta = '". @$_REQUEST["$id_conta"] ."'";
				
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
	
public function __destruct() {
      	parent::__destruct();
}
	
public function contrataProduto(){

	
}
public function obtemProduto($id_produto){

    $zSQL = "SELECT * FROM prtb_produto where id_produto = '". $this->bd->escape(@$_REQUEST["id_produto"]) ."'";
	$produto = $this->bd->obtemUnicoRegistro($zSQL);
	return;

}
public function excluiContrato($id_cliente_produto,$permanente){

	ECHO "INICIO PASSO 2 DA EXCLUSÃO: verifica se é permanente ou não";

	if ($permanente != "t"){
	ECHO "PASSO 2 DA EXCLUSÃO: a exclusão não é permanente. a variavel permanente = f. Executando update no cbtb_cliente_produto";
		
		$sSQL  = "UPDATE ";
		$sSQL .= "	cbtb_cliente_produto ";
		$sSQL .= "SET ";
		$sSQL .= "	excluido = 't' ";
		$sSQL .= "WHERE ";
		$sSQL .= "	id_cliente_produto = '$id_cliente_produto'";
		
		$this->bd->consulta($sSQL);

		//echo "sql exclusão: $sSQL";
	}else if ($permanente == "t"){
	ECHO "PASSO 2 DA EXCLUSÃO: a exclusão é permanente. a variavel permanente = f. EXCUINDO TUDO!!! FUDEU!!!";

		
		$sSQL  = "SELECT * FROM cntb_conta WHERE id_cliente_produto = '$id_cliente_produto'";
		$contas = $this->bd->obtemRegistros($sSQL);
		
		
		$tab["BL"] = "cntb_conta_bandalarga";
		$tab["D"]  = "cntb_conta_discado";
		$tab["H"]  = "cntb_conta_hospedagem";
		$tab["E"]  = "cntb_conta_email";
		
		for($i=0;$i<count($contas);$i++){
		
		// dentro do loop
		
			if( $tab[ trim($contas[$i]["tipo_conta"]) ] ) {
			   $sSQL  = "DELETE FROM ".$tab[ trim($contas[$i]["tipo_conta"]) ]." ";
			   $sSQL .= "WHERE ";
			   $sSQL .= "username = '".$contas[$i]["username"]."' AND ";
			   $sSQL .= "dominio = '".$contas[$i]["dominio"]."' AND ";
			   $sSQL .= "tipo_conta = '".$contas[$i]["tipo_conta"]."' ";
		   
		   
			}
			
		}
		
		$sSQL  = "DELETE FROM ";
		$sSQL .= "cntb_conta ";
		$sSQL .= "WHERE ";
		$sSQL .= "id_cliente_produto = '$id_cliente_produto' ";
		
		$this->bd->consulta($sSQL);
		
		$sSQL  = "DELETE FROM ";
		$sSQL .= "cbtb_cliente_produto ";
		$sSQL .= "WHERE ";
		$sSQL .= "id_cliente_produto = '$id_cliente_produto' ";

		$this->bd->consulta($sSQL);
		
		
			
	}
	return;
}
		
	
	

}
	

				
				

			














?>
