<?

require_once( PATH_LIB . "/VirtexAdmin.class.php" );
require_once("MArrecadacao.class.php");
require_once("MUtils.class.php");

class VAClientes extends VirtexAdmin {

	public function VAClientes() {
		parent::VirtexAdmin();
	}

	protected function validaFormulario() {
	   $erros = array();
	   return $erros;
	}
	
	private function specialChars($valor) {
		if( is_array($valor) ) {
			while(list($tc,$tv)=each($valor) ) {
			   $valor[$tc] = $this->specialChars($tv);
			}
		} else {
		   $valor = htmlspecialchars($valor);
		}
		return($valor);
	}
	
	/**
	 * Obtem Informações do produto contratado
	 */
	private function obtemInfoProdutoContratado($id_cliente_produto) {
		$sSQL  = "SELECT ";
		$sSQL .= "   cp.id_cliente, cp.id_produto, ";
		$sSQL .= "   p.nome, p.descricao, p.tipo, p.valor, p.disponivel, p.num_emails, ";
		$sSQL .= "   p.quota_por_conta, p.vl_email_adicional, p.permitir_outros_dominios, ";
		$sSQL .= "   p.email_anexado, p.numero_contas, p.comodato, p.valor_comodato, ";
		$sSQL .= "   p.desconto_promo, p.periodo_desconto, p.tx_instalacao, ";
		$sSQL .= "   ph.dominio, ph.franquia_em_mb, ph.valor_mb_adicional, ";
		$sSQL .= "   pd.franquia_horas, pd.permitir_duplicidade, pd.valor_hora_adicional, ";
		$sSQL .= "   pbl.banda_upload_kbps, pbl.banda_download_kbps, ";
		$sSQL .= "   pbl.franquia_trafego_mensal_gb, pbl.valor_trafego_adicional_gb, ";
		$sSQL .= "   pbl.roteado ";
		$sSQL .= "FROM ";
		$sSQL .= "   cbtb_cliente_produto cp INNER JOIN prtb_produto p USING(id_produto) FULL OUTER JOIN ";
		$sSQL .= "   prtb_produto_discado pd USING(id_produto) FULL OUTER JOIN ";
		$sSQL .= "   prtb_produto_hospedagem ph USING(id_produto) FULL OUTER JOIN ";
		$sSQL .= "   prtb_produto_bandalarga pbl USING(id_produto) ";
		$sSQL .= "WHERE ";
		$sSQL .= "   cp.id_cliente is not null ";
		$sSQL .= "   AND cp.id_cliente_produto = '".$this->bd->escape($id_cliente_produto)."' ";
		
		//////echo $sSQL . "<br>\n";
		
		return($this->bd->obtemUnicoRegistro($sSQL));
		
		
	}
	
	
	private function obtemPR($id_cliente){
	
		$sSQL  = "SELECT ";
		$sSQL .= "pr.tipo ";
		$sSQL .= "FROM cbtb_cliente_produto cp, prtb_produto pr ";
		$sSQL .= "WHERE cp.id_cliente = '$id_cliente' ";
		$sSQL .= "AND cp.id_produto = pr.id_produto ";
		$sSQL .= "GROUP BY pr.tipo";
									
		$prcliente = $this->bd->obtemRegistros($sSQL);
		////////echo "QUERY: $sSQL<br> ";
									
		$prod_contr = array();
									
		////////echo count($prcliente);
									
		for($i = 0; $i < count($prcliente); $i++ ) {
			$prod_contr[ trim(strtolower($prcliente[$i]["tipo"])) ] = true;
									
		}
									
				
									
		$this->tpl->atribui("prod_contr",$prod_contr);
		return;
	
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

	public function obtemPOP($id_pop) {
		$sSQL = "SELECT ";
		$sSQL .= "   id_pop,nome,info,tipo,id_pop_ap ";
		$sSQL .= "FROM ";
		$sSQL .= "   cftb_pop ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_pop = '". $this->bd->escape($id_pop) . "' ";
		
		return( $this->bd->obtemUnicoRegistro($sSQL) );
	}
	
	public function obtemNAS($id_nas) {
		$sSQL = "SELECT ";
		$sSQL .= "   id_nas,nome,ip,secret,tipo_nas ";
		$sSQL .= "FROM ";
		$sSQL .= "   cftb_nas ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_nas = '". $this->bd->escape($id_nas) . "' ";
		////////echo "OBTEM NAS: $sSQL <br>";
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
	   	
	   	////echo "OBTEM_IP: $sSQL <br>";

		return( $this->bd->obtemUnicoRegistro($sSQL) );
	
	}
	
	private function obtemIPExterno($id_nas){
	
	
		$sSQL  = "SELECT ";
		$sSQL .= "  ex.ip_externo ";
		$sSQL .= "FROM ";
		$sSQL .= "  cftb_ip_externo ex ";
		$sSQL .= "  LEFT OUTER JOIN cntb_conta_bandalarga cbl USING(ip_externo) ";
		$sSQL .= "WHERE ";
		$sSQL .= "  ex.id_nas = '$id_nas' ";
		$sSQL .= "  AND cbl.ip_externo is null ";
		$sSQL .= "ORDER BY ";
		$sSQL .= "  ex.ip_externo ";
		$sSQL .= "LIMIT 1 ";


		////////echo "IP_EXTERNO: $sSQL <br>";
		return( $this->bd->obtemUnicoRegistro($sSQL));
	

	
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
		////////echo "retorno do obtemNAS: $sSQL;";
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
		
		if( ! $this->privPodeLer("_CLIENTES") ) {
			$this->privMSG();
			return;
		}			
		
		// Utilizado pelo menu ou por outras funcionalidades quaisquer.
		if( $id_cliente ) {
			$cliente = $this->obtemCliente($id_cliente);   
			$this->tpl->atribui("cliente",$cliente);
		}


		if ($op == "cadastro"){
				if( ! $this->privPodeGravar("_CLIENTES") ) {
					$this->privMSG();
					return;
				}			
		

			$erros = array();

			$acao = @$_REQUEST["acao"];
			$id_cliente = @$_REQUEST["id_cliente"];
			$cpf_cnpj = @$_REQUEST["cpf_cnpj"];
			//$msg_final = @$_REQUEST["msg_final"];

			$enviando = false;


			$reg = array();

			$this->obtemPR($id_cliente);




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
					  $checa = null;
					//$checa = $this->bd->obtemUnicoRegistro($tSQL);
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
					////////echo "$sSQL";
					$this->bd->consulta($sSQL);  

					//if( $this->bd->obtemErro() != MDATABASE_OK ) {
					//	//////echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
					//	//////echo "QUERY: " . $sSQL . "<br>\n";
					//
					//}


					// Exibir mensagem de cadastro executado com sucesso e jogar pra página de listagem.
					$this->tpl->atribui("mensagem",$msg_final); 
					$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=cadastro&id_cliente=$id_cliente");
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
			
				
				
				////////echo $erros;
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
				if( ! $this->privPodeLer("_CLIENTES_FICHA") ) {
							$this->privMSG();
							return;
				}		
				
				if( ! $this->privPodeGravar("_CLIENTES_FICHA") ) {
							$this->privMSG();
							return;
				}		
		

				$erros = array();

				$texto_pesquisa = @$_REQUEST['texto_pesquisa'];
				$tipo_pesquisa = @$_REQUEST['tipo_pesquisa'];
				$a = @$_REQUEST['a'];
				$retorno = @$_REQUEST['retorno'];
				
				if( $retorno == "XML" ) {
					// Retorno em XML para utilização com ajax
					$this->arquivoTemplate = "clientes_pesquisa.xml";
					header("Content-type: text/xml");
				} else {
					$this->arquivoTemplate = "clientes_pesquisa.html";
				}

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
								// //////echo "MAC<br>\n";
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
							$campos_conta   = " cn.username,cn.dominio,cn.tipo_conta,cn.id_conta ";

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
				
				/**
				for($i=0;$i<count($clientes);$i++) {
					
					while(list($campo,$valor) = each($clientes[$i]) ) {
						//if( $campo != "conta" ) {
						if( is_array( $valor ) ) {
						   $tmp = $clientes[$i][$campo];
						   while( list($tc,$tv) = each($tmp) ) {
						      $tmp[$tc] = htmlspecialchars($tv);
						   }
						   $clientes[$i][$campo] = $tmp;
						} else {
						   $clientes[$i][$campo] = htmlspecialchars($valor);
						}
					}
					
				}
				*/
				$clientes = $this->specialChars($clientes);
				
				$this->tpl->atribui("erros",$erros);
				$this->tpl->atribui("clientes",$clientes);
										
				$this->tpl->atribui("tipo_pesquisa",$tipo_pesquisa);
				$this->tpl->atribui("texto_pesquisa",$texto_pesquisa);

		} else if ($op == "cobranca") {
			// Sistema de contratação de produtos e resumo de cobrança
			////////echo "cobranca<br>";
					
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
			////////echo "resumo<br>";
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
						   
					////////echo $sSQL ."<hr>\n";
					
					$this->tpl->atribui("id_cliente",$id_cliente);
						   
					$contas = $this->bd->obtemRegistros($sSQL);
						   
					$produtos[$i]["contas"] = $contas;
					
				}
			
			
				$this->obtemPR($id_cliente);
	
			
				//require_once( PATH_LIB . "/hugo2.php" );
///////////////////HUGO2

	$hoje = date("d/m/Y");
	$id_cliente = @$_REQUEST["id_cliente"];
	$tipo_lista = @$_REQUEST["tipo_lista"];
	
	if ($tipo_lista == 'tudo'){
	
		$sSQL  = "SELECT ";
		$sSQL .= "f.id_cliente_produto, to_char(f.data, 'DD/mm/YYYY') as data_conv,f.data, f.valor, f.observacoes,f.descricao,to_char(f.reagendamento, 'DD/mm/YYYY') as reagendamento, f.pagto_parcial, ";
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
		//$sSQL .= "AND (f.status = 'A' OR f.status = 'R') ";
		//$sSQL .= "AND f.data < now() + interval '10 day' ";
		$sSQL .= "ORDER BY f.data ASC ";







		$lista_faturas = $this->bd->obtemRegistros($sSQL);
		////////echo "Lista: $sSQL <br>";
		
		
				$this->obtemPR($id_cliente);

		
		
		
		$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
		$cliente = $this->bd->obtemUnicoRegistro($sSQL);
		
		//$this->tpl->atribui("lista_contrato",$lista_contrato);
		
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
			$sSQL .= "	cl.id_cliente_produto, cl.id_cliente, ";
			$sSQL .= "	pr.id_produto, pr.nome ";
			$sSQL .= "FROM ";
			$sSQL .= "	cbtb_contrato ct, cbtb_cliente_produto cl, prtb_produto pr ";
			$sSQL .= "WHERE ";
			$sSQL .= "	cl.id_cliente_produto = ct.id_cliente_produto  AND cl.id_cliente = '$id_cliente' AND ct.id_produto = pr.id_produto ";
			$sSQL .= " ORDER BY ct.data_contratacao DESC ";
			
			$lista_contrato = $this->bd->obtemRegistros($sSQL);
			
			////////echo "lista: $sSQL <br>";
				$this->tpl->atribui("lista_contrato",$lista_contrato);
				$this->tpl->atribui("cliente",$cliente);
				$this->tpl->atribui("id_cliente", $id_cliente);
			//$this->tpl->atribui("lista_faturas",$lista_faturas);
			$this->arquivoTemplate = "cliente_contratos_todos.html";
			return;
	
	
	
	}else{
		
		$sSQL  = "SELECT ";
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
		////////echo "Lista: $sSQL <br>";
		
		$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
		$cliente = $this->bd->obtemUnicoRegistro($sSQL);
		
		
		$sSQL  = "SELECT ";
		$sSQL .= "	ct.id_cliente_produto, ct.data_contratacao, ct.vigencia, ct.id_produto, ct.tipo_produto, ct.valor_contrato, ct.status, ";
		$sSQL .= "	cl.id_cliente_produto, cl.id_cliente, ";
		$sSQL .= "	pr.id_produto, pr.nome ";
		$sSQL .= "FROM ";
		$sSQL .= "	cbtb_contrato ct, cbtb_cliente_produto cl, prtb_produto pr ";
		$sSQL .= "WHERE ";
		$sSQL .= "	cl.id_cliente_produto = ct.id_cliente_produto  AND cl.id_cliente = '$id_cliente' AND ct.id_produto = pr.id_produto AND ct.status = 'A' ";
		$sSQL .= " ORDER BY ct.data_contratacao DESC ";
		
		$lista_contrato = $this->bd->obtemRegistros($sSQL);
		
		////////echo "lista: $sSQL <br>";
			$this->tpl->atribui("lista_contrato",$lista_contrato);
			$this->tpl->atribui("cliente",$cliente);
			$this->tpl->atribui("id_cliente", $id_cliente);
			$this->tpl->atribui("lista_faturas",$lista_faturas);


	
	}







//////////////////HUGO2 FIM
				
				
				
				
				$largura = "700";
				$altura = "400";

				$this->tpl->atribui("largura",$largura);
				$this->tpl->atribui("altura",$altura);
				
				
				$this->tpl->atribui("produtos",$produtos);
				$this->arquivoTemplate = "cliente_cobranca_resumo.html";
				
				

			} else if( $rotina == "contratar" ) {
				
								
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
						//if( $this->bd->obtemErro() ) {
						//////echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
						////echo "cliente_produto: $sSQL <br>\n";
						//}
						
						$username = @$_REQUEST["username"];
						$dominio = @$_REQUEST["dominio"];
						$tipo_conta = @$_REQUEST["tipo_conta"];
						$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];
						
						//$sSQL  = "SELECT * from cntb_contas where username = '$username', dominio = '$dominio', tipo_conta = '$tipo_conta'";
						//$prep = $this->bd->obtemUnicoRegistro($sSQL);

								
								

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
						//	//////echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
						////echo "conta: $sSQL <br>\n";
						//}
						
												
						if ($email_igual == "1"){
							
							$prefs = $this->prefs->obtem("total");
							//$prefs = $this->prefs->obtem();
							
						
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
							//	//////echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
							////echo "conta: $sSQL <br>\n";
							//}
							
							

							
							
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
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
							$sSQL .= "     'E', ";
							$sSQL .= "     '" . $dominioPadrao . "', ";
							$sSQL .= "     '$quota', ";
							$sSQL .= "     '". $this->bd->escape(@$_REQUEST["username"])."@". $dominioPadrao ."' ";
							$sSQL .= " )";
							
							$this->bd->consulta($sSQL);
							//if( $this->bd->obtemErro() ) {
							//	//////echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
							//	//////echo "SQL: $sSQL <br>\n";
							//}

							
						
						}
						
						
						$tipo = @$_REQUEST["tipo"];
						
						//PEGA CAMPOS COMUNS EM cftb_preferencias
						

						//$prefs = $this->prefs->obtem("total");
						$prefs = $this->prefs->obtem();
						
						
						switch($tipo) {
							case 'D':
								
								$username = @$_REQUEST["username"];
								$tipo_conta = @$_REQUEST["tipo"];
								$dominio = $prefs["geral"]["dominio_padrao"];
								$foneinfo = @$_REQUEST["foneinfo"];
								
								$sSQL  = "INSERT INTO ";
								$sSQL .= "cntb_conta_discado ";
								$sSQL .= "( ";
								$sSQL .= "username, tipo_conta, dominio, foneinfo ";
								$sSQL .= ")VALUES ( ";
								$sSQL .= "'$username', '$tipo_conta', '$dominio', '$foneinfo' )";
								
								////////echo "SQL DISCADO: $sSQL <br>\n";
								
								$this->bd->consulta($sSQL);
								
								$this->tpl->atribui("foneinfo",$foneinfo);
								
							break;	
							case 'BL':
							////echo "TIPO: " . $this->bd->escape(trim(@$_REQUEST["selecao_ip"])) . "<br>\n";
							
								// PRODUTO BANDA LARGA
								$tipo_de_ip = $this->bd->escape(trim(@$_REQUEST["selecao_ip"]));
								if($tipo_de_ip == "A"){
									$nas = $this->obtemNAS($_REQUEST["id_nas"]);
									////////echo "NAS: ".$nas["id_nas"]."<BR>";
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
								
								
									//$erro = array();
									
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
										////echo count($erro);
										//for($i=0;$i<count($erro);$i++) {
										   ////echo $erro[$i] . "<br>\n";
										//}
									//}


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
									//////echo $ip_externo["ip_externo"];
									
									if($nas["tipo_nas"] == "P"){
										
										$ipaddr = $ip_disp;
									
									}else if ($nas["tipo_nas"] == "I"){
									
										$ipaddr = $rede_disp;
									
									}
									
									$username = @$_REQUEST["username"];
									$tipo_conta = @$_REQUEST["tipo"];
									//$dominio = $prefs["geral"]["dominio_padrao"];
									//$dom = $prefs["total"];
									
									$dSQL = "SELECT dominio_padrao FROM pftb_preferencia_geral WHERE id_provedor = '1' ";
									$dom = $this->bd->obtemUnicoRegistro($dSQL);
									////echo "SQL DOMINIO: $dSQL <br>";
									
									$dominio = $dom["dominio_padrao"];

									
									$sSQL = "SELECT id_conta FROM cntb_conta WHERE username = '$username' AND tipo_conta = 'BL' AND dominio = '$dominio' ";
									$_id_conta = $this->bd->obtemUnicoRegistro($sSQL);
									////echo "ID_CONTA: $sSQL";
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
									////////echo "rede:". $rede_disponivel["rede"]. "<br>";
								
								
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
								
								//$id_conta_banda_larga = $this->bd->proximoID("clsq_id_conta_bandalarga_seq");
								
								//$id_pop = $_REQUEST["id_pop"];
								////////echo "IDPOP: $id_pop <br>";
								
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
								$sSQL .= "     ". $_MAC .", ";
								$sSQL .= "	   ". $ip_externo ."  ";
								$sSQL .= "     )";						
								
								
								//////echo "INSERT NA BL: $sSQL <br>";
								$this->bd->consulta($sSQL);  
								//if( $this->bd->obtemErro() ) {
								//	//////echo "ERRO: " , $this->bd->obtemMensagemErro() . "<br>\n";
								//	//////echo "SQL: $sSQL <br>\n";
								//}
								
								break;
								
							case 'H':
								// PRODUTO HOSPEDAGEM
								//$sSQL  = "SELECT * from cftb_preferencias where id_provedor = '1'";							
								
								
								$prefs = $this->prefs->obtem("total");								
								//$prefs = $this->prefs->obtem();
						
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
									////////echo "QUERY INSERÇÃO: $sSQL <BR>\n";



									//SPOOL
									////////echo "Tipo: $tipo_hospedagem <br> Username: $username <br> Dominio: $dominio <br> DominioHosp: $dominio_hospedagem<br>";
									$this->spool->hospedagemAdicionaRede($server,$id_conta,$tipo_hospedagem,$username,$dominio,$dominio_hospedagem);
								//}
								break;

						}
						
						
						require_once( PATH_LIB . "/dede.php" );						

						if ($tipo && $tipo == "BL"){
						
						////////echo $tipo;
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
								////////echo "SQL : " . $sSQL . "<br>\n";

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
								
								//$destino = $nas['ip'];	
								$destino = $nas['id_nas'];

								
								$username = @$_REQUEST["username"];
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
							
						}
							
						if ($tipo == "BL"){
						
						$prefs = $this->prefs->obtem("total");
						$sSQL = "SELECT ip_externo FROM cntb_conta_bandalarga WHERE username = '".@$_REQUEST["username"]."' AND tipo_conta = 'BL' and dominio = '".$prefs["dominio_padrao"]."' ";
						$externo = $this->bd->obtemUnicoRegistro($sSQL);
						
						
						//////echo "EXTERNO: $sSQL <br>";
							if(count($externo)){
								$this->tpl->atribui("ip_externo",$externo["ip_externo"]);
							}
						}

						
						// Joga a mensagem de produto contratado com sucesso.
						$this->tpl->atribui("username",@$_REQUEST["username"]);
						//$this->tpl->atribui("tipo_produto",$tipo_produto);
						$this->tpl->atribui("pop",@$lista_pops["nome"]);
						$this->tpl->atribui("nas",@$nas["nome"]);
						$this->tpl->atribui("mac",@$_MAC);
						$this->tpl->atribui("ip",@$ip_disp);
						$this->tpl->atribui("dominio",@$prefs["dominio_padrao"]);
						$this->tpl->atribui("dominio_hospedagem",@$dominio_hospedagem);
						
						
						/*////echo "id_cliente: $id_cliente <br>";
						////echo "id_cliente_produto: $id_cliente_produto <br>";
						////echo "id_carne: $id_carne <br>";
						////echo "data: $data <br>";*/
						
						$this->tpl->atribui("id_cliente",$id_cliente);
						$this->tpl->atribui("id_clçiente_produto",$id_cliente_produto);
						$this->tpl->atribui("id_carne",$id_carne);
						$this->tpl->atribui("data",$data);
						$this->tpl->atribui("primeira",true);
						
						
						$this->obtemPR($id_cliente);
						
						
						
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


					require_once( PATH_LIB . "/hugo.php" );


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
					$sSQL .= "WHERE status = 'A' ";
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
			
				////////echo "PASSO 1 DA EXCLUSÃO: executa o excluiContrato";
				
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
			
				$sSQL  = "SELECT ";
				$sSQL .= "	cp.id_cliente_produto, cp.id_cliente, cp.id_produto, cp.dominio, ";
				$sSQL .= "	p.id_produto, p.nome, p.descricao, p.tipo, p.valor, p.disponivel, p.num_emails, p.quota_por_conta, ";
				$sSQL .= "	p.vl_email_adicional, p.permitir_outros_dominios, p.email_anexado ";
				$sSQL .= "FROM cbtb_cliente_produto cp INNER JOIN prtb_produto p ";
				$sSQL .= "USING( id_produto ) ";
				$sSQL .= "WHERE cp.id_cliente_produto='".@$_REQUEST['id_cliente_produto']."' ";
				////////echo $sSQL ."<hr>\n";
			
			
				$produtos = $this->bd->obtemRegistros($sSQL);
			
				for($i=0;$i<count($produtos);$i++) {

				   $id_cp = $produtos[$i]["id_cliente_produto"];
				   
				   $sSQL  = "SELECT ";
				   $sSQL .= "	username, dominio, tipo_conta, id_conta ";
				   $sSQL .= "FROM ";
				   $sSQL .= "	cntb_conta ";
				   $sSQL .= "WHERE ";
				   $sSQL .= "	id_cliente_produto = '$id_cp'";
			   
				   ////////echo $sSQL ."<hr>\n";
			   
				   $contas = $this->bd->obtemRegistros($sSQL);
				   
				   $produtos[$i]["contas"] = $contas;
			
				}
			
			
			$this->tpl->atribui("produtos",$produtos);				
			$this->arquivoTemplate = "confirma_exclusao.html";
			return;
			
			
			}else if ($rotina == "carne"){
			
				$id_cliente = @$_REQUEST["id_cliente"];
				$id_carne = @$_REQUEST["id_carne"];
				$p = @$_REQUEST["p"];
				
				$sSQL = "SELECT id_carne,id_cliente_produto,valor,status,vigencia,to_char(data_geracao,'DD/mm/YYYY') as data_geracao, data_geracao as dtg  FROM cbtb_carne where id_cliente = '$id_cliente'";
						
				$carnes = $this->bd->obtemRegistros($sSQL);
				$this->obtemPR($id_cliente);
				
				//////echo "CARNES: $sSQL <br>";
				
				if(count($carnes)){
				
					$id_cliente_produto = @$carnes[0]["id_cliente_produto"];
				
				}
				
				if ($p == "faturas"){
					
					
					$sSQL = "select id_carne,id_cliente_produto, to_char(data,'DD/mm/YYYY') as data, descricao, valor, status from cbtb_faturas where id_carne = '$id_carne'";
					$faturas = $this->bd->obtemRegistros($sSQL);
					
					$this->tpl->atribui("faturas",$faturas);
					$this->tpl->atribui("id_cliente",$id_cliente);
					$this->tpl->atribui("id_cliente_produto",$id_cliente_produto);
					
					$this->arquivoTemplate = "cobranca_carnes_faturas.html";
					return;
					
				} else if ($p == "segunda_via"){
				
				
				
				
				
					
				
				
				}
				$this->tpl->atribui("carnes",$carnes);				
				$this->arquivoTemplate = "cobranca_carnes.html";

			
			
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
			
			
			
			
			
			
			
			$this->obtemPR($id_cliente);

			
			
			
			
			
			
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
			
			
			$id_cliente = @$_REQUEST["id_cliente"];
			
			
			
			
			$sSQL  = "SELECT ";
			$sSQL .= "	cp.id_cliente_produto, cp.id_cliente, cp.id_produto, cp.dominio, ";
			$sSQL .= "	p.id_produto, p.nome, p.descricao, p.tipo, p.valor, p.disponivel, p.num_emails, p.quota_por_conta, ";
			$sSQL .= "	p.vl_email_adicional, p.permitir_outros_dominios, p.email_anexado, p.numero_contas ";
			$sSQL .= "FROM cbtb_cliente_produto cp INNER JOIN prtb_produto p ";
			$sSQL .= "USING( id_produto ) ";
			$sSQL .= "WHERE cp.id_cliente='$id_cliente' AND p.tipo = '$tipo' ";
			////////echo $sSQL ."<hr>\n";
			
			
			$produtos = $this->bd->obtemRegistros($sSQL);
			
			//$produtos["quant"] = "0";
			
			for($i=0;$i<count($produtos);$i++) {

			   $id_cp = $produtos[$i]["id_cliente_produto"];
			   
			   $sSQL  = "SELECT ";
			   $sSQL .= "	username, dominio, tipo_conta, id_conta ";
			   $sSQL .= "FROM ";
			   $sSQL .= "	cntb_conta ";
			   $sSQL .= "WHERE ";
			   $sSQL .= "	id_cliente_produto = '$id_cp' ";
			   //$sSQL .= "AND tipo_conta = '$tipo' ";
			   
			   ////////echo $sSQL ."<hr>\n";
			   
			   $contas = $this->bd->obtemRegistros($sSQL);
			   
			   $produtos[$i]["contas"] = $contas;
			   
			   $sSQL = "SELECT count(id_conta) as quantidade FROM cntb_conta WHERE id_cliente_produto = '$id_cp' AND tipo_conta = '$tipo'";
			   $cnt = $this->bd->obtemRegistros($sSQL);
			   ////echo $sSQL;
			   
			   @$produtos[$i]["quant"] = @$cnt[$i]["quantidade"];
			   
			   
			
			}
			
			//$this->tpl->atribui("quant_contas",$quant);
			$this->tpl->atribui("produtos",$produtos);
			
			$this->arquivoTemplate = "cliente_produto.html";
			
		} else if ($op == "conta") {
			$erros = array();
		
			$id_cliente = @$_REQUEST["id_cliente"];
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$username = @$_REQUEST["username"];
			$dominio  = @$_REQUEST["dominio"];
			$tipo_conta = @$_REQUEST["tipo_conta"];
			$sop = @$_REQUEST["sop"];
			$acao = @$_REQUEST["acao"];
			
			$this->obtemPR($id_cliente);
			
			
			// LISTA DE POPS
			$sSQL  = "SELECT ";
			$sSQL .= "   id_pop, nome ";
			$sSQL .= "FROM ";
			$sSQL .= "   cftb_pop ";
			$sSQL .= "ORDER BY ";
			$sSQL .= "   nome";


			////////echo "POPs: $sSQL <br>";
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

			////////echo "NASs: $sSQL <br>";

			$lista_nas = $this->bd->obtemRegistros($sSQL);

			global $_LS_TIPO_NAS;

			for($i=0;$i<count($lista_nas);$i++) {
				 $lista_nas[$i]["tp"] = $_LS_TIPO_NAS[ $lista_nas[$i]["tipo_nas"] ];
			}

			$this->tpl->atribui("lista_nas",$lista_nas);

			$prefs = $this->prefs->obtem("geral");
			$dominio = @$_REQUEST["dominio"];
			if(!$dominio) $dominio = $prefs["dominio_padrao"];



			if ($sop == "nova_conta"){
			
				$this->tpl->atribui("id_cliente_produto",@$_REQUEST["id_cliente_produto"]);
				// Obtem os dados do produto contratado
				$dados_pcontratado = $this->obtemInfoProdutoContratado(@$_REQUEST["id_cliente_produto"]);
				
				
				while(list($vr,$vl)=each($dados_pcontratado)) {
					// DEBUG
					//////echo "$vr = $vl <br>\n";
					
					// Atribui os valores ao template
					if($vr) {
						$this->tpl->atribui($vr,$vl);	
					}
				}
				$tipo_conta = @$_REQUEST["tipo_conta"];
				$upload_kbps = @$_REQUEST["upload_kbps"];
				$download_kbps = @$_REQUEST["download_kbps"];
				
				if($tipo_conta == "BL") {
					if(!$upload_kbps) $upload_kbps = $dados_pcontratado["banda_upload_kbps"];
					if(!$download_kbps) $download_kbps = $dados_pcontratado["banda_download_kbps"];

					$this->tpl->atribui("upload_kbps",$upload_kbps);
					$this->tpl->atribui("download_kbps",$download_kbps);

				}
				
				
				$this->tpl->atribui("id_cliente",$id_cliente);
				$this->tpl->atribui("username",$username);
				$this->tpl->atribui("dominio",$dominio);
				$this->tpl->atribui("tipo_conta",$tipo_conta);
				$this->tpl->atribui("sop",$sop);
				
				global $_LS_BANDA;
				$this->tpl->atribui("lista_upload",$_LS_BANDA);
				$this->tpl->atribui("lista_download",$_LS_BANDA);
				
				global $_LS_ST_CONTA;
				$this->tpl->atribui("lista_status",$_LS_ST_CONTA);
				
				$_username = @$_REQUEST["_username"];
				$_dominio = @$_REQUEST["_dominio"];
				$_tipo_conta = @$_REQUEST["_tipo_conta"];
				
				$prefs = $this->prefs->obtem("total");								
				$dominio_padrao = $prefs["dominio_padrao"];
				//////echo "DOMINIO: $dominio_padrao <br>";
				$this->arquivoTemplate = "cliente_nova_conta.html";
				
				$acao = @$_REQUEST["acao"];
				if ($acao == "cad"){
				
					$_username = @$_REQUEST["_username"];
					$dominio = @$_REQUEST["dominio"];
					$tipo_conta = @$_REQUEST["tipo_conta"];
					$id_cliente = @$_REQUEST["id_cliente"];
					$email_igual = @$_REQUEST["email_igual"];
					
					
					$this->obtemPR($id_cliente);

					
					//$sSQL  = "SELECT cp.id_produto FROM cbtb_cliente_produto cp, cntb_conta cn WHERE ";
					//$sSQL .= "cn.username = '$_username' AND ";
					//$sSQL .= "cn.tipo_conta = '$tipo_conta' AND ";
					//$sSQL .= "cn.dominio = '$dominio' AND ";
					//$sSQL .= "cn.id_cliente = '$id_cliente' AND ";
					//$sSQL .= "cn.id_cliente_produto = cp.id_cliente_produto";
					//$_produto = $this->bd->obtemUnicoRegistro($sSQL);
					
					//$id_produto = $_produto["id_produto"];
					$sSQL = "SELECT id_produto from cbtb_cliente_produto WHERE id_cliente_produto = '".@$_REQUEST["id_cliente_produto"]."' AND id_cliente = '".@$_REQUEST["id_cliente"]."' ";
					$_prod = $this->bd->obtemUnicoRegistro($sSQL);
					
					$id_produto = $_prod["id_produto"];
					
					//echo $id_produto ."<br>";
					
					$lista_dominiop = $this->prefs->obtem("geral");

					//$dominioPadrao = $lista_dominiop["dominio_padrao"]; 
					//$dominioPadrao2 = $lista_dominiop["dominio_padrao2"]; 
					//echo "TIPO: ".$_prod["tipo_conta"]."<BR>";
					
					
					//	$dominioPdrao = $lista_dominiop["dominio_padrao2"];
					
				
					
						$dominioPadrao = $lista_dominiop["dominio_padrao"]; 
					
				
					
					// Valida os dados

					// TODO: Colocar isso em uma funcao private
					$sSQL  = "SELECT ";
					$sSQL .= "   username ";
					$sSQL .= "FROM ";
					$sSQL .= "   cntb_conta ";
					$sSQL .= "WHERE ";
					$sSQL .= "   username = '".@$_REQUEST["username"]."' ";
					$sSQL .= "   and tipo_conta = '".@$_REQUEST["tipo_conta"] ."' ";
					$sSQL .= "   and dominio = '$dominio' ";
					$sSQL .= "ORDER BY ";
					$sSQL .= "   username ";

					$lista_user = $this->bd->obtemUnicoRegistro($sSQL);

					if(count($lista_user) && $lista_user["username"]){
						// ver como processar
						$erros[] = "Já existe outra conta cadastrada com esse username";
					}

					if (!count($erros)){
					
						//$id_cliente_produto = $this->bd->proximoID("cbsq_id_cliente_produto");

						// Insere no banco de dados

						//$sSQL  = "INSERT INTO ";
						//$sSQL .= "   cbtb_cliente_produto( ";
						//$sSQL .= "      id_cliente_produto,id_cliente, id_produto ) ";
						//$sSQL .= "   VALUES (";
						//$sSQL .= "     '$id_cliente_produto', ";
						//$sSQL .= "     '" .@$_REQUEST["id_cliente"] . "', ";
						//$sSQL .= "     '" . $_produto["id_produto"] . "' ";
						//$sSQL .= "     )";						
						
						
						// INSERT INTO cntb_conta blablabla 
						// VERIFICA TIPO DO CONTRATO
						// BLABLABLA

						//$this->bd->consulta($sSQL);  

						$senhaCr = $this->criptSenha($this->bd->escape(trim(@$_REQUEST["senha"])));

						$id_conta = $this->bd->proximoID("cnsq_id_conta");

						$sSQL  = "INSERT INTO ";
						$sSQL .= "   cntb_conta ( ";
						$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript, conta_mestre,status ) ";
						$sSQL .= "   VALUES (";
						$sSQL .= "			'".$id_conta."', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
						
						if(trim(@$_REQUEST["tipo_conta"]) == "E"){

							$sSQL .= " '". @$_REQUEST["dominio"] ."', ";
						
						
						}else{

							$sSQL .= "     '" . $dominioPadrao . "', ";
						}
						$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo_conta"])) . "', ";
						$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
						$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
						$sSQL .= "     '" .	$id_cliente_produto . "', ";
						$sSQL .= "     '" . $senhaCr . "', ";
						$sSQL .= "     false, ";
						$sSQL .= "     'A' )";						

						$this->bd->consulta($sSQL);  
						////echo "CNTB_CONTA: $sSQL <br>";

						if ($email_igual == "1"){

							$prefs = $this->prefs->obtem("total");


							$id_conta = $this->bd->proximoID("cnsq_id_conta");

							$sSQL  = "INSERT INTO ";
							$sSQL .= "   cntb_conta( ";
							$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript,conta_mestre, status) ";
							$sSQL .= "   VALUES (";
							$sSQL .= "			'". $id_conta. "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"]) . "', ";
							$sSQL .= "     '" . $dominioPadrao . "', ";
							$sSQL .= "     'E', ";
							$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
							$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
							$sSQL .= "     '" .	$id_cliente_produto . "', ";
							$sSQL .= "     '" . $senhaCr . "', ";
							$sSQL .= "     false, ";
							$sSQL .= "     'A' )";						

							$this->bd->consulta($sSQL);  
							////echo "CNTB_CONTA: $sSQL <br>";

							$id_produto = @$_REQUEST['id_produto'];
							$prod = $this->obtemProduto($id_produto);	
							
							////echo "QUOTA: " . $prod["quota_por_conta"] . "<br>\n";

							if ($prod["quota_por_conta"] == "" || !$prod ){
								$quota = "0";
							}else {
								$quota = $prod["quota_por_conta"];
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
							////echo "E-MAIL: $sSQL <br>";

						}
	
						$tipo = trim(@$_REQUEST["tipo"]);
						$prefs = $this->prefs->obtem();
						$tipo_conta = trim($tipo_conta);
						
						switch($tipo_conta) {
							case 'D':

								$username = @$_REQUEST["username"];
								$tipo_conta = @$_REQUEST["tipo_conta"];
								$dominio = $prefs["geral"]["dominio_padrao"];
								$foneinfo = @$_REQUEST["foneinfo"];

								$sSQL  = "INSERT INTO ";
								$sSQL .= "cntb_conta_discado ";
								$sSQL .= "( ";
								$sSQL .= "username, tipo_conta, dominio, foneinfo ";
								$sSQL .= ")VALUES ( ";
								$sSQL .= "'$username', '$tipo_conta', '$dominio', '$foneinfo' )";

								////echo "SQL DISCADOC: $sSQL <br>\n";

								$this->bd->consulta($sSQL);

								$this->tpl->atribui("foneinfo",$foneinfo);

							break;	
							case 'BL':
								// PRODUTO BANDA LARGA
								$tipo_de_ip = $this->bd->escape(trim(@$_REQUEST["selecao_ip"]));
								if($tipo_de_ip == "A"){
									$nas = $this->obtemNAS($_REQUEST["id_nas"]);
									////////echo "NAS: ".$nas["id_nas"]."<BR>";
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

								$redirecionar = @$_REQUEST["redirecionar"];

								if($redirecionar == "true"){

									$ip_externo = $this->obtemIPExterno($_REQUEST["id_nas"]);
									//////echo $ip_externo["ip_externo"];

									if($nas["tipo_nas"] == "P"){

										$ipaddr = $ip_disp;

									}else if ($nas["tipo_nas"] == "I"){

										$ipaddr = $rede_disp;

									}

									$username = @$_REQUEST["username"];
									$tipo_conta = @$_REQUEST["tipo"];
									
									// alteracao do hugo 20/07/06
									$dominio = $prefs["geral"]["dominio_padrao2"];


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

									$rede_disp = "'".$rede_disponivel["rede"]."'";
									////////echo "rede:". $rede_disponivel["rede"]. "<br>";


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

								////////echo "IDPOP: $id_pop <br>";

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
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["username"])  . "', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo_conta"])). "', ";
								$sSQL .= "     '" . $dominioPadrao . "', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_pop"])) . "', ";
								$sSQL .= "     '" . $nas["tipo_nas"] . "', ";
								$sSQL .= "     " . $ip_disp . ", ";
								$sSQL .= "     " . $rede_disp . ", ";
								$sSQL .= "     '" . @$_REQUEST["upload_kbps"] . "', ";
								$sSQL .= "     '" . @$_REQUEST["download_kbps"] . "', ";
								$sSQL .= "     'A', ";
								$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_nas"])) . "', ";
								$sSQL .= "     ". $_MAC .", ";
								$sSQL .= "	   ". $ip_externo ."  ";
								$sSQL .= "     )";						


								//////echo "INSERT NA BL: $sSQL <br>";
								$this->bd->consulta($sSQL);  

								break;

							case 'H':
								// PRODUTO HOSPEDAGEM
								//$sSQL  = "SELECT * from cftb_preferencias where id_provedor = '1'";							


								$prefs = $this->prefs->obtem("total");								
								//$prefs = $this->prefs->obtem();

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
									////////echo "QUERY INSERÇÃO: $sSQL <BR>\n";

									//SPOOL
									////////echo "Tipo: $tipo_hospedagem <br> Username: $username <br> Dominio: $dominio <br> DominioHosp: $dominio_hospedagem<br>";
									$this->spool->hospedagemAdicionaRede($server,$id_conta,$tipo_hospedagem,$username,$dominio,$dominio_hospedagem);
								//}
								break;
								case "E":
								
									$sSQL  = "INSERT INTO ";
									$sSQL .= "	cntb_conta_email( ";
									$sSQL .= "		username, tipo_conta, dominio, quota, email) ";
									$sSQL .= "VALUES (";
									$sSQL .= "     '" . @$_REQUEST["username"] . "', ";
									$sSQL .= "     'E', ";
									$sSQL .= "     '" . $dominioPadrao . "', ";
									$sSQL .= "     '".(int)@$_REQUEST["quota"]."', ";
									$sSQL .= "     '". @$_REQUEST["username"]."@". $dominioPadrao ."' ";
									$sSQL .= " )";

									$this->bd->consulta($sSQL);
									//echo "E-MAIL: $sSQL <br>";
								
								break;
						}						
						$tipo = $tipo_conta;
						if ($tipo && $tipo == "BL"){

						////////echo $tipo;
							// Envia instrucao pra spool
							if ($nas && $nas["tipo_nas"] == "I"){

								$id_nas = $_REQUEST["id_nas"];
								$banda_upload_kbps = @$_REQUEST["banda_upload_kbps"];
								$banda_download_kbps = @$_REQUEST["banda_download_kbps"];
								$rede = $rede_disponivel["rede"];
								$mac = $_REQUEST["mac"];

								$sSQL  = "SELECT ";
								$sSQL .= "   id_nas, nome, ip, tipo_nas ";
								$sSQL .= "FROM ";
								$sSQL .= "   cftb_nas ";
								$sSQL .= "WHERE ";
								$sSQL .= "   id_nas = '$id_nas'";
								////////echo "SQL : " . $sSQL . "<br>\n";

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
							$prefs = $this->prefs->obtem("geral");				
							$sSQL = "SELECT ip_externo FROM cntb_conta_bandalarga WHERE username = '".@$_REQUEST["username"]."' AND tipo_conta = 'BL' and dominio = '".$prefs["dominio_padrao"]."' ";
							$externo = $this->bd->obtemUnicoRegistro($sSQL);


							//////echo "EXTERNO: $sSQL <br>";
							$this->tpl->atribui("ip_externo",$externo["ip_externo"]);


						}
			
						// Joga a mensagem de conta adicionada com sucesso.
						$this->tpl->atribui("username",@$_REQUEST["username"]);
						$this->tpl->atribui("pop",@$lista_pops["nome"]);
						$this->tpl->atribui("nas",@$nas["nome"]);
						$this->tpl->atribui("mac",@$_MAC);
						$this->tpl->atribui("ip",@$ip_disp);
						$this->tpl->atribui("dominio",$dominio_padrao);
						$this->tpl->atribui("dominio_hospedagem",@$dominio_hospedagem);

						//$this->arquivoTemplate="cliente_cobranca_intro.html";

						//$url = $_SERVER["PHP_SELF"] . "?op=conta&pg=ficha&id_cliente=" . $id_cliente . "&username=" . @$_REQUEST["username"] . "&dominio=" . @$_REQUEST["dominio"] . "&tipo_conta=" . $tipo_conta;
						$url = $_SERVER["PHP_SELF"] . "?op=cadastro&pg=&tipo=" . $tipo_conta . "&id_cliente=" . $id_cliente;
						
						$msg_final = "Conta cadastrada com sucesso.";
						$this->tpl->atribui("mensagem",$msg_final);
						$this->tpl->atribui("url",$url);
						$this->tpl->atribui("target","_top");

						$this->arquivoTemplate="msgredirect.html";
						return;

						$exibeForm = false;


					} else {
						$tipo = @$_REQUEST["tipo_conta"];
						
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
						//$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=produto&id_cliente=$id_cliente");
						$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=conta&sop=nova_conta&tipo_conta=$tipo_conta&id_cliente=$id_cliente&id_cliente_produto=$id_cliente_produto");

						$this->tpl->atribui("target","_top");

						$this->arquivoTemplate="msgredirect.html";
						return;


					}

				}// acao = cad
				
				$sSQL = "SELECT * FROM dominio WHERE dominio_provedor is true";
				$dominios_provedor = $this->bd->obtemRegistros($sSQL);
				
				$this->tpl->atribui("dominios_provedor", $dominios_provedor);
				
				
				
				
				$this->arquivoTemplate = "cliente_nova_conta.html";
				RETURN;


			}else if ($sop == "novo_email"){
			
			
			
			
			
			}
			
			
			
			
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


			////////echo "POPs: $sSQL <br>";
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
			
			////////echo "NASs: $sSQL <br>";
			
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
			
			////////echo "sql conta: $sSQL <br>/n";
			$conta = $this->bd->obtemUnicoRegistro($sSQL);
			
			
			
			/** PEGA O PRODUTO CONTRATADO */
			
			$sSQL  = "SELECT ";
			$sSQL .= "   p.id_produto, p.nome, p.tipo, p.numero_contas,p.quota_por_conta ";
			$sSQL .= "";
			$sSQL .= "FROM ";
			$sSQL .= "   cbtb_cliente_produto cp INNER JOIN prtb_produto p USING (id_produto) ";
			$sSQL .= "WHERE ";
			$sSQL .= "   id_cliente_produto = '".$conta["id_cliente_produto"]."'";
			$sSQL .= "";
			
			$produto = $this->bd->obtemUnicoRegistro($sSQL);
			
			$numero_contas = $produto["numero_contas"];
			$this->tpl->atribui("numero_contas",$numero_contas);
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
					
					////////echo "SQL: $sSQL <BR>";
					
					$dsc = $this->bd->obtemUnicoRegistro($sSQL);					
					$conta = array_merge($conta,$dsc);

				
				
				
					break;
				case 'BL':
					$sSQL  = "SELECT ";
					$sSQL .= "   cbl.id_pop, cbl.tipo_bandalarga, cbl.ipaddr, cbl.rede, cbl.id_nas, cbl.mac, cbl.upload_kbps, cbl.download_kbps, cbl.ip_externo "; // alterei
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

					////////echo "$sSQL;<br>\n";



					$cbl = $this->bd->obtemUnicoRegistro($sSQL);

					$conta = array_merge($conta,$cbl);


					$this->tpl->atribui("status",@$_REQUEST["status"]);
					$this->tpl->atribui("upload_kbps",@$_REQUEST["upload_kbps"]);
					$this->tpl->atribui("download_kbps",@$_REQUEST["download_kbps"]);

					////////echo "ID NAS:". $conta["id_nas"] ."<BR>";

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
					
					////echo "CONTA EMAIL: $sSQL <br>";
					
					$conta = array_merge($conta,$conta_email);
					
					
					//$server = $this->prefs->obtem("total");
					$server = $this->prefs->obtem();
					
					
					$this->tpl->atribui("quota",@$conta["quota"]);
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
				
					////echo "$sSQL;<br>\n";
				
				
				
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
						
						////////echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";
						////////echo "UP: " . $conta["upload_kbps"] . "|" . $upload_kbps . "<br>\n";
						////////echo "DN: " . $conta["download_kbps"] . "|" . $download_kbps . "<br>\n";
						////////echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";
						////////echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";

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
				$status = @$_REQUEST["status"];		
				
					switch($tipo_conta) {
						case 'D':
						
							
							$senha_cript = $this->criptSenha($senha);
													
														
							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta_discado ";
							$sSQL .= "SET ";
							$sSQL .= "	foneinfo = '".@$_REQUEST['foneinfo']."' ";
							$sSQL .= "WHERE ";
							$sSQL .= "	username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";
							////////echo "SQL: $sSQL <br>";
													
							$this->bd->consulta($sSQL);
							
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
													
							$this->bd->consulta($sSQL);

						
							break;
					
						case 'BL':
						////////echo "TESTE";
				
							// SPOOL
							if( $excluir ) {
								////////echo "excluir";
								$this->spool->bandalargaExcluiRede($nas_atual["id_nas"],$conta["id_conta"],$conta["rede"]);
							}

							if( $incluir ) {
								////////echo "incluir<br>";
								$id_conta = $conta["id_conta"];
								$this->spool->bandalargaAdicionaRede($nas_novo["id_nas"],$id_conta,$rede,$mac,$upload_kbps,$download_kbps,$username);
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

							////////echo "$sSQL;<br>\n";

							$this->bd->consulta($sSQL);

							$uSQL  = "UPDATE ";
							$uSQL .= "   cntb_conta_bandalarga ";
							$uSQL .= "SET ";


							$uSQL .= "   id_nas = '".$this->bd->escape($id_nas)."', ";
							$uSQL .= "   id_pop = '".$this->bd->escape($id_pop)."', ";
							$uSQL .= "   upload_kbps = '".$this->bd->escape($upload_kbps)."', ";
							$uSQL .= "   download_kbps = '".$this->bd->escape($download_kbps)."', ";
							
							if ( !$mac || $mac == "" ){
							$uSQL .= "   mac = NULL ";
							} else {
							
							$uSQL .= "   mac = '".$this->bd->escape($mac)."' ";
							
							}
							
							if( $rede ) {
								
								$uSQL .= ", ipaddr = null, ";
								$uSQL .= "  rede = '".$rede."' ";
								
							}

							$redirecionar = @$_REQUEST["redirecionar"];
							$ip_externo = @$_REQUEST["ip_externo"];


							if ( $ip_externo != "" && $redirecionar == null ){
							
								$uSQL .= "  , ip_externo = null ";
							
							}else if ( $ip_externo == "" && $redirecionar == "true" ){
								
								$ip_externo = $this->obtemIPExterno($this->bd->escape($id_nas));
								$ip_externo = $ip_externo["ip_externo"];
								
								$uSQL .= " ,ip_externo = '$ip_externo' ";
								
							}
							
							$uSQL .= "WHERE ";
							$uSQL .= "   username = '".$this->bd->escape($username)."' ";
							$uSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
							$uSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
							$uSQL .= "";

							////////echo "$uSQL;<br>\n";
							$this->bd->consulta($uSQL);
							
							// SPOOL MEGAFOCKER
							$sSQL = "SELECT ip_externo, id_nas, ipaddr, rede, tipo_conta FROM cntb_conta_bandalarga WHERE username = '".$this->bd->escape($username)."' AND dominio = '".$this->bd->escape($dominio)."' AND tipo_conta = '".$this->bd->escape($tipo_conta)."'";
							$cntb = $this->bd->obtemUnicoRegistro($sSQL);
							
							$id_nas_antigo = @$_REQUEST["id_nas"];
							
							
							
							if ( $id_nas_antigo != $cntb["id_nas"] ){
							
							$this->spool->excluiIpExterno($id_nas_antigo,$ip_externo,$id_conta);
							//$this->spool->adicionaIpExterno();
							
							}


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

							////////echo "SQL1: $sSQL <br>\n";


							$this->bd->consulta($sSQL);

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
							////////echo "SQL1: $sSQL <br>\n";

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
							////////echo "SQL: $sSQL <br>";
						
							$this->bd->consulta($sSQL);
							
							
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
						
							$this->bd->consulta($sSQL);

						
							break;
					}

					



					// Exibe mensagem (joga pra msgredirect)
					$this->tpl->atribui("mensagem","Conta Alterada com sucesso!");
					$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=produto&tipo=$tipo_conta&id_cliente=$id_cliente");
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
				////////echo "indisponivel";
				//return;
			}
			
			// Trata o tipo de exibicao.
			$pg = @$_REQUEST["pg"];
			
			if( $pg == "ficha" ) {
				$this->tpl->atribui("str_status",$_LS_ST_CONTA[$conta["status"]]);
				
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

						////////echo $nas;
						////////echo $pop;

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
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		}else if ($op == "segunda_via"){
		
		  $id_carne = @$_REQUEST["id_carne"];
		  $faturas = array();

			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$id_cliente = @$_REQUEST["id_cliente"];
			$data = @$_REQUEST["data"];
			
			$forma_pagamento = "PRE";
		  
		  if( !$id_carne ) {
				// Se não tiver o id_carne é pq é pra exibir uma única fatura.
				
		  	
		  	$fatura_html = $this->carne($id_cliente_produto,$data,$id_cliente,$forma_pagamento,true);   
		  	
		  	$faturas[] = array( "fatura_html" => $fatura_html, "pagebrake" => false );
		     
		     
		  } else {
					// Exibe TODAS as faturas em ABERTO		  	


				$sSQL  = "SELECT ";
				$sSQL .= "   id_cliente_produto, data, id_carne ";
				$sSQL .= "FROM ";		
				$sSQL .= "   cbtb_faturas ";
				$sSQL .= "WHERE ";
	//			$sSQL .= "id_cliente_produto = '".$REQUEST["id_cliente_produto"]."' AND ";
				$sSQL .= "id_carne = '".$_REQUEST["id_carne"]."' AND ";
				$sSQL .= "status = 'A' ";
				
				$fat = $this->bd->obtemRegistros($sSQL);

				for($i=0;$i<count($fat);$i++) {
						// Se nãoi passar o último parametro como true o sistema fica gerando o "Nosso Numero"
					 $fatura_html = $this->carne($fat[$i]["id_cliente_produto"],$fat[$i]["data"],$id_cliente,$forma_pagamento,true);
					 
					 $pagebrake=false;

					 // blablabla do pagebrake
					 if( $i>0 && ($i+1) != count($fat) && ($i+1) % 3 == 0 ) {
						$pagebrake = true;
					 }

					 $faturas[] = array( "fatura_html" => $fatura_html,
															 "pagebreak" => $pagebrake );

				}// for
				
			}

			$this->tpl->atribui("faturas",$faturas);
			$this->arquivoTemplate = "carne_segunda_via.html";
		
				
		
		
		} else if ($op =="altera_contrato"){
		
				$sSQL  = "SELECT ";
				$sSQL .= "	username, tipo_conta, id_conta ";
				$sSQL .= "FROM ";
				$sSQL .= "	cntb_conta ";
				$sSQL .= "WHERE ";
				$sSQL .= "	id_conta = '". @$_REQUEST["$id_conta"] ."'";
				
		} else if ($op == "excluir_cliente"){
		// CHAMADA PARA EXCLUSÃO DE CLIENTES
		
			$rotina = @$_REQUEST["rotina"];
			////////echo "rotina = $rotina <br>";
			$id_cliente = @$_REQUEST["id_cliente"];
		
			if (!$rotina){
			
				$this->arquivoTemplate = "clientes_exclusao.html";
				return;
			}
			
			if ($rotina){
				if ($rotina == "ficha"){



					$sSQL  = "SELECT ";
					$sSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
					$sSQL .= "   rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
					$sSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
					$sSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
					$sSQL .= "   ativo, obs, excluido ";
					$sSQL .= "FROM ";
					$sSQL .= "   cltb_cliente ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_cliente = '$id_cliente'";

					$cliente = $this->bd->obtemUnicoRegistro($sSQL);


					$sSQL  = "SELECT ";
					$sSQL .= "   username,dominio,tipo_conta,senha,senha_cript,id_cliente, ";
					$sSQL .= "   id_cliente_produto,id_conta,conta_mestre,status,observacoes ";
					$sSQL .= "FROM ";
					$sSQL .= "   cntb_conta ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_cliente_produto = '$id_cliente' ";

					$conta = $this->bd->obtemRegistros($sSQL);
					$cliente = array_merge($cliente,$conta);




					$sSQL  = "SELECT ";
					$sSQL .= "	cp.id_cliente_produto, cp.id_cliente, cp.id_produto, cp.dominio, ";
					$sSQL .= "	p.id_produto, p.nome, p.descricao, p.tipo, p.valor, p.disponivel, p.num_emails, p.quota_por_conta, ";
					$sSQL .= "	p.vl_email_adicional, p.permitir_outros_dominios, p.email_anexado, p.numero_contas ";
					$sSQL .= "FROM cbtb_cliente_produto cp INNER JOIN prtb_produto p ";
					$sSQL .= "USING( id_produto ) ";
					$sSQL .= "WHERE cp.id_cliente='$id_cliente' ";
					////////echo $sSQL ."<hr>\n";


					$produtos = $this->bd->obtemRegistros($sSQL);

					for($i=0;$i<count($produtos);$i++) {

						$id_cp = $produtos[$i]["id_cliente_produto"];

						$sSQL  = "SELECT ";
						$sSQL .= "	username, dominio, tipo_conta, id_conta ";
						$sSQL .= "FROM ";
						$sSQL .= "	cntb_conta ";
						$sSQL .= "WHERE ";
						$sSQL .= "	id_cliente_produto = '$id_cp'";

						////////echo $sSQL ."<hr>\n";

						$contas = $this->bd->obtemRegistros($sSQL);

						$produtos[$i]["contas"] = $contas;

					}




					/*
					$sSQL  = "SELECT * FROM ";
					$sSQL .= "cntb_conta_bandalarga cb, cntb_conta_email ce, cntb_conta_hospedagem ch, cntb_conta_discado cd ";
					$sSQL .= "WHERE ";
					$sSQL .= "username = '". $conta["username"] ."' AND ";
					$sSQL .= "dominio = '". $conta["dominio"] ."' ";

					$contas = $this->bd->obtemRegistros
					*/
					
					$this->tpl->atribui("produtos",$produtos);
					$this->tpl->atribui("cliente",$cliente);
					$this->arquivoTemplate = "clientes_exclusao_confirma.html";



				}else if ($rotina == "exclusao"){
					//DETONA OS DADOS DO CLIENTE E OS CONTRATOS DELE!!!

					
					$permanente = @$_REQUEST["permanente"];

					/**
					 * Exclui TODOS os contratos deste cliente
					 */
					$sSQL  = "SELECT ";
					$sSQL .= "   * ";
					$sSQL .= "FROM ";
					$sSQL .= "   cbtb_cliente_produto ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_cliente = '".$this->bd->escape($id_cliente)."'";

					$lista_cp = $this->bd->obtemRegistros($sSQL);

					$obs = "Excluido com cliente '".$id_cliente."'";

					for($i=0;$i<count($lista_cp);$i++) {
					   $this->excluiContrato($lista_cp[$i]["id_cliente_produto"],$permanente,$obs);
					}


					if( $permanente == 't' ) {
						/**
						 * Desvincula o domínio do cliente
						 */

						$sSQL  = "UPDATE ";
						$sSQL .= "   dominio ";
						$sSQL .= "SET ";
						$sSQL .= "   id_cliente = null";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_cliente = '".$this->bd->escape($id_cliente)."' ";

						$this->bd->obtemRegistros($sSQL);

						$sSQL  = "DELETE FROM ";
						$sSQL .= "   cltb_cliente ";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_cliente = '".$this->bd->escape($id_cliente)."' ";

						$this->bd->consulta($sSQL);





					} else {
						$sSQL  = "UPDATE ";
						$sSQL .= "   cltb_cliente ";
						$sSQL .= "SET ";
						$sSQL .= "   excluido = 't' ";
						$sSQL .= "WHERE ";
						$sSQL .= "   id_cliente = '".$this->bd->escape($id_cliente)."' ";

						$this->bd->consulta($sSQL);
					}

					$id_excluido = $id_cliente;
					$tipo = "CL" . ($permanente == 't' ? 'P' : "");
					$this->gravarLogExclusao($id_excluido,$tipo);


					// EXIBE A MENSAGEM DE REGISTRO PAPOCADO COM SUCESSO.

					$this->tpl->atribui("mensagem","Cliente Excluido!");
					$this->tpl->atribui("url","clientes.php?op=pesquisa");
					$this->tpl->atribui("target","_top");


					$this->arquivoTemplate = "msgredirect.html";

					// Cai fora.
					return;




				}
			}
		
		
		}else if ($op =="confirmaaltcli"){
			$this->arquivoTemplate = "confirma_alteracao_pops.html";
			
			
		}else if ($op == "imprime_contrato"){
		
			$rotina = @$_REQUEST["rotina"];
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$id_cliente = @$_REQUEST["id_cliente"];
			$this->obtemPR($id_cliente);

			$sSQL = "SELECT * FROM cbtb_contrato WHERE id_cliente_produto = '$id_cliente_produto'";
			$contr = $this->bd->obtemUnicoRegistro($sSQL);

			$data_contratacao = $contr["data_contratacao"];

			//$arqPDF = $this->contratoPDF($id_cliente_produto,$data_contratacao);

			$sSQL = "SELECT path_contrato FROM pftb_preferencia_cobranca WHERE id_provedor = '1'";
			$_path = $this->bd->obtemUnicoRegistro($sSQL);
			$path = $_path["path_contrato"];
			$host = "dev.mosman.com.br";

			////////echo "path_contratos: $sSQL <br>";
			////////echo "path: $path <br>";
			//contrato-418-2006-05-10.html

			$base_nome = "contrato-".$id_cliente_produto."-".$data_contratacao;
			$nome_arq = $path.$base_nome.".html";
			$arq_mostra = $path."/".$base_nome.".pdf";
			$arq = $base_nome.".html";
			
			
			
			if ($rotina == "pdf"){

				////////echo "nome arquivo: $nome_arq <br>";	

				$p = new MHTML2PDF();
				$p->setDebug(0);
				$arqPDF = $p->converte($nome_arq,$host,$path);
				copy($arqPDF,$path.$base_nome.".pdf");
				//copy($arqPDF,"/home/hugo".$base_nome.".pdf");

				if (!$arqPDF){

					//////echo "papocou esta bosta";
					//////echo "path_contratos: $sSQL <br>";
					//////echo "path: $path <br>";

				}else{


				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment; filename="'.$base_nome.'.pdf"');
				readfile($arqPDF);

				}
				
			}else{
			
			////////echo $arqPDF;
			////////echo "BOSTA";
			
			//$this->arquivoTemplate = "home.html";
			
			
			//$this->tpl->atribui("arquivo_contrato",$arquivo_contrato);
			$this->arquivoTemplate = $nome_arq;
			}
		
		}else if ($op == "teste"){
		
			$this->testePDF();
			
		}
	}
	
	
public function __destruct() {
      	parent::__destruct();
}

	
public function contrataProduto(){

	
}
public function obtemProduto($id_produto){

  $zSQL  = "SELECT id_produto, nome, descricao, tipo, valor, disponivel, num_emails, quota_por_conta, vl_email_adicional, permitir_outros_dominios, email_anexado,  ";
  $zSQL .= "numero_contas, comodato, valor_comodato, desconto_promo, periodo_desconto, tx_instalacao ";
  $zSQL .= "FROM prtb_produto where id_produto = '". $this->bd->escape(@$_REQUEST["id_produto"]) ."'";
	$produto = $this->bd->obtemUnicoRegistro($zSQL);
	////echo $zSQL;
	return $produto;

}

public function excluiContrato($id_cliente_produto,$permanente,$obs=""){

	
	
	
	

	/**
	 * Verifica quais são as contas afetadas para enviar função à spool e realizar exclusão
	 */
	
	$sSQL  = "SELECT ";
	$sSQL .= "   username,dominio,tipo_conta,senha,senha_cript,id_cliente, ";
	$sSQL .= "   id_cliente_produto,id_conta,conta_mestre,status,observacoes ";
	$sSQL .= "FROM ";
	$sSQL .= "   cntb_conta ";
	$sSQL .= "WHERE ";
	$sSQL .= "   id_cliente_produto = '".$this->bd->escape($id_cliente_produto)."' ";
	
	$contas = $this->bd->obtemRegistros($sSQL);
	
	$tabs = array( 	"D" => "cntb_conta_discado",
					"BL" => "cntb_conta_bandalarga",
					"E" => "cntb_conta_email",
					"H" => "cntb_conta_hospedagem");
	for($i=0;$i<count($contas);$i++) {
	
		/**
		 * Desabilitar usuários e afins
		 */

		switch( trim($contas[$i]["tipo_conta"]) ) {
			case 'D':
				break;			
			case 'BL':

				// Pegar dados de cntb_conta_bandalarga
				
				$sSQL  = "SELECT ";
				$sSQL .= "   id_nas,ipaddr,rede ";
				$sSQL .= "FROM ";
				$sSQL .= "   cntb_conta_bandalarga ";
				$sSQL .= "WHERE ";
				$sSQL .= "   username = '". $this->bd->escape($contas[$i]["username"])."' ";
				$sSQL .= "   AND dominio = '".$this->bd->escape($contas[$i]["dominio"])."'";
				$sSQL .= "   AND tipo_conta = '".$this->bd->escape($contas[$i]["tipo_conta"])."'";
				
				$info = $this->bd->obtemUnicoRegistro($sSQL);
				
				$id_nas = @$info["id_nas"];
				
				$nas = $this->obtemNas($id_nas);
				
				
				if( $nas["tipo_nas"] == "I" ) {
					// Nas do tipo IP, enviar instrução de excluir p/ spool.
					$this->spool->bandalargaExcluiRede($nas["id_nas"],$contas[$i]["id_conta"],$info["rede"]);
				}
				
				/*
				//////echo "<hr>\n";
				//////echo "ID_CONTA: " . $contas[$i]["id_conta"] . "<br>\n";
				//////echo "USERNAME: " . $contas[$i]["username"] . "<br>\n";
				//////echo "DOMINIO: " . $contas[$i]["dominio"] . "<br>\n";
				//////echo "ID_NAS: " . $info["id_nas"] . "<br>\n";
				//////echo "REDE: " . $info["rede"] . "<br>\n";
				//////echo "IP: " . $info["ipaddr"] . "<br>\n";
				//////echo "IP NAS: " . $nas["ip"] . "<br>\n";
				//////echo "<hr>";
				
				break;			
			case 'E':
				break;			
			case 'H':
				break;
				*/
		
		}
		
		
		
		/**
		 * Remover registro do sistema de acordo com regra de exclusão definida
		 */
		
		
		if( $permanente == "t" ) {
			// Exclusão destrutiva. Eliminar registro.
			// TODO: fazer backup dos dados excluídos em arquivo de log.

			$sSQL  = "DELETE FROM ";
			$sSQL .= "   " . $tabs[ trim( $contas[$i]["tipo_conta"]) ]. " ";
			$sSQL .= "WHERE ";
			$sSQL .= "   username = '". $this->bd->escape($contas[$i]["username"])."' ";
			$sSQL .= "   AND dominio = '".$this->bd->escape($contas[$i]["dominio"])."'";
			$sSQL .= "   AND tipo_conta = '".$this->bd->escape($contas[$i]["tipo_conta"])."'";
			
			$this->bd->consulta($sSQL);
			
			////////echo "$sSQL<hr>";

		} else {
			// Exclusão não destrutiva - Alterar flag.
			
			//$sSQL  = "";
			//$sSQL .= "";
			//$sSQL .= "";
		
		}
	
	}
	
	if( $permanente == "t" ) {
		// Exclusão destrutiva. Eliminar

		$sSQL  = "DELETE FROM ";
		$sSQL .= "cntb_conta ";
		$sSQL .= "WHERE ";
		$sSQL .= "id_cliente_produto = '$id_cliente_produto' ";
		
		$this->bd->consulta($sSQL);
		////////echo "$sSQL<hr>";
		
		$sSQL  = "DELETE FROM ";
		$sSQL .= "cbtb_cliente_produto ";
		$sSQL .= "WHERE ";
		$sSQL .= "id_cliente_produto = '$id_cliente_produto' ";
		////////echo "$sSQL<hr>";

		$this->bd->consulta($sSQL);



	} else {
		// Exclusão não destrutiva - Alterar flags
		
		$sSQL  = "UPDATE ";
		$sSQL .= "	cbtb_cliente_produto ";
		$sSQL .= "SET ";
		$sSQL .= "	excluido = 't' ";
		$sSQL .= "WHERE ";
		$sSQL .= "	id_cliente_produto = '$id_cliente_produto'";
		
		$this->bd->consulta($sSQL);
		
		////////echo "$sSQL<hr>";
		
	}

	$tipo = "CT" . ($permanente == 't' ? 'P' : "");
	$id_excluido = $id_cliente_produto;
	$this->gravarLogExclusao($id_excluido,$tipo,$obs);

	return;
}

public function gravarLogExclusao($id_excluido,$tipo,$obs=""){

	$id_exclusao = $this->bd->proximoID("lgsq_id_exclusao");
	$admin = $this->admLogin->obtemAdmin();
	
	
	$sSQL  = "INSERT INTO ";
	$sSQL .= "	lgtb_exclusao ";
	$sSQL .= "	( id_exclusao, admin, data, tipo, id_excluido, observacao ) ";
	$sSQL .= "VALUES ";
	$sSQL .= "	( ";
	$sSQL .= "		'$id_exclusao', '$admin', now(), '$tipo', '$id_excluido','".$this->bd->escape($obs)."' ";
	$sSQL .= " ) ";
	
	////////echo "SQL: $sSQL ";
	
	$this->bd->consulta($sSQL);
	
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

public function carne($id_cliente_produto,$data,$id_cliente,$forma_pagamento,$segunda_via=false){


	//////echo "DATA ENVIADA: $data <BR>\n";

	
	$sSQL  = "SELECT cl.nome_razao, cl.endereco, cl.complemento, cl.id_cidade, cl.estado, cl.cep, cl.cpf_cnpj,cl.bairro, cd.cidade as nome_cidade, cd.id_cidade  ";
	$sSQL .= "FROM ";
	$sSQL .= "cltb_cliente cl, cftb_cidade cd ";
	$sSQL .= "WHERE ";
	$sSQL .= "cl.id_cliente = '$id_cliente' AND ";
	$sSQL .= "cd.id_cidade = cl.id_cidade";

	$cliente = $this->bd->obtemUnicoRegistro($sSQL);
	//////echo "CLIENTE: $sSQL  <br>";
	
	if( strstr($data,"/") && $segunda_via) {
	   list($d,$m,$y) = explode("/",$data);
	   $data = "$y-$m-$d";
	}


	$sSQL  = "SELECT valor, id_cobranca,to_char(data, 'DD/mm/YYYY') as data, nosso_numero, linha_digitavel, cod_barra  FROM ";
	$sSQL .= "cbtb_faturas ";
	$sSQL .= "WHERE ";
	$sSQL .= "id_cliente_produto = '$id_cliente_produto' AND ";
	$sSQL .= "data = '$data' ";

	$fatura = $this->bd->obtemUnicoRegistro($sSQL);
	
	//////echo "fatura: $sSQL<br>";
	
	//$data_cadastrada = $fatura["data"];
	//////echo "DATA: $data_cadastrada <br>";
	//////echo "SHIT: " . $fatura["data"] . "<br>\n";
	
	list ($dia,$mes,$ano) = explode("/",$fatura["data"]);
	
	
	$mes_array = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
	
	if ($forma_pagamento == "PRE"){
	
		$referente = $mes_array[(int)$mes-1]."/".$ano;
	
	}else if ($forma_pagamento == "POS"){
	
		//$mes_ref = mktime(0, 0, 0, $mes-1);
		//////echo "MES: $mes <br>\n";
		//////echo "MES REF: $mes_ref <br>\n";
		$referente = $mes_array[(int)$mes-1]."/".$ano;
	
	}
	


	// PEGANDO INFORMAÇÕES DAS PREFERENCIAS
	$provedor = $this->prefs->obtem("total");
	//$provedor = $this->prefs->obtem();

	$sSQL = "SELECT ct.id_produto, pd.nome from cbtb_contrato ct, prtb_produto pd WHERE ct.id_cliente_produto = '$id_cliente_produto' and ct.id_produto = pd.id_produto";
	$produto = $this->bd->obtemUnicoRegistro($sSQL);
	////////echo "PRODUTO: $sSQL <br>";

	//$codigo = @$_REQUEST["codigo"];
	//$data_venc = "30/04/2006";
	
	if (!$segunda_via){
	
		$sSQL = "SELECT nextval('blsq_carne_nossonumero') as nosso_numero ";
		$nn = $this->bd->obtemUnicoRegistro($sSQL);

		$nosso_numero = $nn['nosso_numero'];
		
	}else {
	
		$nosso_numero = $fatura["nosso_numero"];
		
	}

	$data_venc = $fatura["data"];
	@list($dia,$mes,$ano) = explode("/",$fatura["data"]);
	$vencimento = $ano.$mes.$dia;
	////////echo $codigo;
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
	$complemento = $cliente["complemento"];
	
	//$informacoes = $provedor["observacoes"];

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
	
   	
	//////echo "FATURA: $sSQL <br>";
	
	$target = "/mosman/virtex/dados/carnes/codigos";
	//$target = "carnes/codigos";
	MArrecadacao::barCode($codigo_barras,"$target/$codigo_barras.png");
		
	//	$codigo = MArrecadacao::pagConta(...);
		
	//copy ("/mosman/virtex/dados/carnes/codigos/".$codigo_barras.".png","/home/hugo/public_html/virtex/codigos/".$codigo_barras.".png");
		

	//$barra = MArrecadacao::barCode($codigo_barras);
	
	$ph = new MUtils;
	
	$_path = MUtils::getPwd();
	
	
	$images = $_path."/template/boletos/imagens";	
	
	$this->tpl->atribui("codigo_barras",$codigo_barras);

	$this->tpl->atribui("linha_digitavel",$linha_digitavel);
	$this->tpl->atribui("valor",$valor);
	$this->tpl->atribui("imagens",$images);
	$this->tpl->atribui("vencimento", $data_venc);
	$this->tpl->atribui("hoje",$hoje);
	$this->tpl->atribui("nosso_numero",$nosso_numero);
	$this->tpl->atribui("sacado",$nome_cliente);
	$this->tpl->atribui("sendereco",$cliente['endereco']);
	$this->tpl->atribui("complemento",$complemento);
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
	$this->tpl->atribui("referente",$referente);
	$this->tpl->atribui("cpf_cnpj",$cliente["cpf_cnpj"]);
	$this->tpl->atribui("bairro",$cliente["bairro"]);
	//$this->tpl->atribui("barra",$barra);
	
	//return($carne_emitido);
	
	if ( $segunda_via == true ){
	
	
		$this->tpl->atribui("imprimir",true);
		$estilo = $this->tpl->obtemPagina("../boletos/pc-estilo.html");
		$fatura = $this->tpl->obtemPagina("../boletos/layout-pc.html");
		
		return($estilo.$fatura);
	
	}else{
	
		$fatura = $this->tpl->obtemPagina("../boletos/layout-pc.html");
		return($fatura);
	
	}
	
	
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
	$this->tpl->atribui("fone_provedor",$provedor["fone"]);
	

	$sSQL  = "SELECT ";
	$sSQL .= "	ct.id_cliente_produto, ct.data_contratacao, ct.vigencia, ct.data_renovacao, ct.valor_contrato, ct.id_cobranca, ct.status, ";
	$sSQL .= "	ct.tipo_produto, ct.valor_produto, ct.num_emails, ct.quota_por_conta, ct.comodato, ct.valor_comodato, ct.desconto_promo, ";
	$sSQL .= "	ct.periodo_desconto, ct.bl_banda_download_kbps, ct.id_produto,ct.vencimento, ";
	$sSQL .= "	pr.id_produto,pr.nome as produto ";
	$sSQL .= "FROM ";
	$sSQL .= "	cbtb_contrato ct, prtb_produto pr ";
	$sSQL .= "WHERE ";
	$sSQL .= "ct.id_cliente_produto = '$id_cliente_produto' ";
	$sSQL .= "AND ct.id_produto = pr.id_produto ";

	$contrato = $this->bd->obtemUnicoRegistro($sSQL);
	
	if ($tipo_produto == "BL"){
	
		$sSQL  = "SELECT cn.username, cn.dominio, cn.tipo_conta, cn.id_conta, cn.senha, ";
		$sSQL .= " bl.id_pop, bl.ipaddr, bl.rede, bl.download_kbps, ";
		$sSQL .= " p.nome as pop ";
		$sSQL .= "FROM cntb_conta cn, cntb_conta_bandalarga bl, cftb_pop p ";
		$sSQL .= "WHERE ";
		$sSQL .= "cn.id_cliente_produto = '$id_cliente_produto' AND ";
		$sSQL .= "bl.username = cn.username AND ";
		$sSQL .= "bl.dominio = cn.dominio AND ";
		$sSQL .= "bl.tipo_conta = cn.tipo_conta AND ";
		$sSQL .= "p.id_pop = bl.id_pop";
		$tecnico = $this->bd->obtemUnicoRegistro($sSQL);
		////echo "TECNICO: $sSQL <br>";
		
		$this->tpl->atribui("tec",$tecnico);
	
	
	}

	//////echo "SQL: $sSQL <br>";

	$this->tpl->atribui("data_contratacao", $contrato["data_contratacao"]);
	$this->tpl->atribui("vigencia", $contrato["vigencia"]);
	$this->tpl->atribui("valor_contrato", $contrato["valor_contrato"]);
	$this->tpl->atribui("tipo_produto", $contrato["tipo_produto"]);
	$this->tpl->atribui("valor_produto", $contrato["valor_produto"]);
	$this->tpl->atribui("banda_kbps", $contrato["bl_banda_download_kbps"]);
	$this->tpl->atribui("id_produto", $contrato["id_produto"]);
	$this->tpl->atribui("nome_produto", $contrato["produto"]);
	$this->tpl->atribui("dia_vencimento",$contrato["vencimento"]);

	//$sSQL  = "SELECT * FROM cltb_cliente WHERE id_cliente = '$id_cliente' ";
	
	$sSQL  = "SELECT cl.id_cliente, cl.data_cadastro, cl.nome_razao, cl.tipo_pessoa, cl.rg_inscr, cl.rg_expedicao, cl.cpf_cnpj, cl.email, cl.endereco, ";
	$sSQL .= "  cl.complemento, cl.id_cidade, cl.cep, cl.bairro, cl.fone_comercial, cl.fone_residencial, cl.fone_celular, cl.contato, cl.banco, cl.conta_corrente, ";
	$sSQL .= "  cl.agencia, cl.dia_pagamento, cl.ativo, cl.obs, cl.provedor, cl.excluido, ";
	$sSQL .= "  cd.id_cidade, cd.cidade, cd.uf as estado ";
	$sSQL .= "FROM cltb_cliente cl, cftb_cidade cd ";
	$sSQL .= "WHERE cl.id_cliente = '$id_cliente' AND ";
	$sSQL .= "cd.id_cidade = cl.id_cidade ";
	
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
	
	////////echo "path: $_path - $path<br>";

	//$arq = explode("/",$arquivo_contrato);
	//$arq = $arq[count($arq)-1];
	$arq = $arquivo_contrato;

	//$image_path = $path."/template/default/images";
	////////echo "<BR>IMAGE PATH".$image_path ."<br>";

	$this->tpl->atribui("path",$image_path);
	$arquivo = $path."/".$arq;
	$arqtmp = $this->tpl->obtemPagina($arq);

	fwrite($fd,$arqtmp);
	
	fclose($fd);
	
	return;
	
	
	

}

public function contratoPDF($id_cliente_produto,$data_contratacao){

	$sSQL = "SELECT path_contratos FROM pftb_preferencia_cobranca WHERE id_provedor = '1'";
	$_path = $this->bd->obtemUnicoRegistro($sSQL);
	$path = $_path["path_contratos"];
	$host = "dev.mosman.com.br";
	
	////////echo "path_contratos: $sSQL <br>";
	////////echo "path: $path <br>";
		
	$nome_arq = "contrato-".$id_cliente_produto."-".$data_contratacao.".html";
	////////echo "nome arquivo: $nome_arq <br>";	

	$p = new MHTML2PDF();
	$p->setDebug(1);
	$arqPDF = $p->converteHTML($nome_arq,$host,$defaultPath=$path);
	
	return($arqPDF);




}
public function testePDF(){

				$nome_arq = "/template/default/boletos/teste_carne.html";
				$base_nome = "teste_carne";
				$host = "dev.mosman.com.br";
				$path = "/tmp";
				
				//////echo $nome_arq."<br>";
				//////echo $base_nome."<br>";
				//////echo $host."<br>";
				//////echo $path."<br>";
				

				$p = new MHTML2PDF();
				$p->setDebug(1);
				$arqPDF = $p->converte($nome_arq,$host,"/tmp");
				copy($arqPDF,$path.$base_nome.".pdf");
				
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment; filename="'.$base_nome.'.pdf"');
				readfile($arqPDF);


}


	
	

}
	

				
				

			














?>
