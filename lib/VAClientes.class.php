<?

class VAClientes extends VirtexAdminWeb {


	protected $id_cliente;


	public function __construct() {
		parent::__construct();
		
		$this->id_cliente = @$_REQUEST["id_cliente"];
		$this->tpl->atribui("id_cliente",$this->id_cliente);
		if($this->id_cliente) {
			$prod_contr = $this->obtemPR($this->id_cliente);
			$this->tpl->atribui("prod_contr",$prod_contr);
		}
	}

	protected function validaFormulario() {
	   $erros = array();
	   return $erros;
	}
	
	protected function specialChars($valor) {
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
		
		////////echo $sSQL . "<br>\n";
		
		return($this->bd->obtemUnicoRegistro($sSQL));
		
		
	}
	
	
	protected function obtemPR($id_cliente){
	
		$sSQL  = "SELECT ";
		$sSQL .= "pr.tipo ";
		$sSQL .= "FROM cbtb_cliente_produto cp, prtb_produto pr ";
		$sSQL .= "WHERE cp.id_cliente = '$id_cliente' ";
		$sSQL .= "AND cp.id_produto = pr.id_produto ";
		$sSQL .= "AND cp.excluido = false ";
		$sSQL .= "GROUP BY pr.tipo";
									
		$prcliente = $this->bd->obtemRegistros($sSQL);
		/////////echo "QUERY: $sSQL<br> ";
									
		$prod_contr = array();
									
	////echo count($prcliente);
									
		for($i = 0; $i < count($prcliente); $i++ ) {
			$prod_contr[ trim(strtolower($prcliente[$i]["tipo"])) ] = true;
									
		}

									
		// $this->tpl->atribui("prod_contr",$prod_contr);
		return $prod_contr;
	
	}
	
	
	private function obtemCliente($id_cliente) {


		$sSQL  = "SELECT ";
		$sSQL .= "   id_cliente, data_cadastro, nome_razao, tipo_pessoa, ";
		$sSQL .= "   rg_inscr, rg_expedicao, cpf_cnpj, email, endereco, complemento, id_cidade, ";
		$sSQL .= "   cidade, estado, cep, bairro, fone_comercial, fone_residencial, ";
		$sSQL .= "   fone_celular, contato, banco, conta_corrente, agencia, dia_pagamento, ";
		$sSQL .= "   ativo, obs, excluido , info_cobranca ";
		$sSQL .= "FROM ";
		$sSQL .= "   cltb_cliente ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_cliente = '$id_cliente' AND excluido = 'f'";
		
		///////echo $sSQL ;
   
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
		////echo "OBTEM NAS: $sSQL <br>";
		return( $this->bd->obtemUnicoRegistro($sSQL) );
	}
	
	private function obtemIP($id_nas) {

		$sSQL  = "SELECT ";
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
	   	
	   	//////echo "OBTEM_IP: $sSQL <br>";

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


		//////////echo "IP_EXTERNO: $sSQL <br>";
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
		//////////echo "retorno do obtemNAS: $sSQL;";
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
			$this->tpl->atribui("op",$op);

			  ////PRIVILEGIOS DE LICENÇA

			$lic_interface = 'nao';
			$lic_email = 'nao';
			$lic_hospedagem = 'nao';
			$lic_interface = 'nao';
			$lic_discado = 'nao';
			$lic_bandalarga = 'nao';
		
			 $licenca = $this->lic->obtemLicenca();
				if(($licenca["frontend"]["discado"]) == "1"){
		
					$lic_discado = 'sim';
		
				}
				if(($licenca["frontend"]["banda_larga"]) == "1"){
					
					$lic_bandalarga = 'sim';
		
				}
				if(($licenca["frontend"]["email"]) == "1"){
		
					$lic_email = 'sim';
				}
				if(($licenca["frontend"]["hospedagem"]) == "1"){
					 	
					$lic_hospedagem = 'sim';
				
			 	}
				if(($licenca["frontend"]["interface"]) == "1"){
							 	
					$lic_interface = 'sim';
						
			 	}
		

				$this->tpl->atribui("lic_discado",$lic_discado);
				$this->tpl->atribui("lic_email",$lic_email);
				$this->tpl->atribui("lic_hospedagem",$lic_hospedagem);
				$this->tpl->atribui("lic_email",$lic_email);
				$this->tpl->atribui("lic_interface",$lic_interface);
				$this->tpl->atribui("lic_bandalarga",$lic_bandalarga);

	
		$id_cliente = @$_REQUEST["id_cliente"];
		$tipo = @$_REQUEST["tipo"];
		// Variáveis gerais de template
		//$this->tpl->atribui("op",$op);
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
		
		if ($op == "cadastro") {
				$tela = new VAClientesCadastro();
				$tela->processa($op);
				$tela->exibe();
		} else if ( $op == "pesquisa" ) {
				$tela = new VAClientesPesquisa();
				$tela->processa($op);
				$tela->exibe();
		
		//} else if ( $op == "contrato" ) {
			// NEW!!!
		

		} else if ($op == "cobranca") {
			
			$tela = new VAClientesCobranca();
			$tela->processa($op);
			$tela->exibe();
			
			//return;
			
			
			
			//require_once("cobranca_teste.php");

		
		} else if ($op == "produto") {

				if ((($lic_bandalarga == 'nao')&&($tipo == "BL"))||(($lic_discado == 'nao')&&($tipo == "D"))||(($lic_hospedagem == 'nao')&&($tipo == "H"))||(($lic_email == 'nao')&&($tipo == "E"))){

				$this->licProib();
				return;

				} 
		
					if ($tipo == "BL"){
						if( ! $this->privPodeLer("_CLIENTES_BANDALARGA") ) {
								$this->privMSG();
								return;
							} if(( ! $this->privPodeGravar("_CLIENTES_BANDALARGA") )&&( ! $this->privPodeLer("_CLIENTES_BANDALARGA") ) ) {
								$this->privMSG();
								return;
						}
					}else if($tipo == "D"){
						if( ! $this->privPodeLer("_CLIENTES_DISCADO") ) {
								$this->privMSG();
								return;
						}
							 if(( ! $this->privPodeGravar("_CLIENTES_DISCADO") )&&( ! $this->privPodeLer("_CLIENTES_DISCADO") ) ) {
														$this->privMSG();
														return;
												}

										
					}else if($tipo == "H"){
					
						if( ! $this->privPodeLer("_CLIENTES_HOSPEDAGEM") ) {
								$this->privMSG();
								return;
							} if(( ! $this->privPodeGravar("_CLIENTES_HOSPEDAGEM") )&&( ! $this->privPodeLer("_CLIENTES_HOSPEDAGEM") ) ) {
								$this->privMSG();
								return;
						}
					

					}else if ($tipo == "E"){
					
						if( ! $this->privPodeLer("_CLIENTES_EMAIL") ) {
								$this->privMSG();
								return;
						}
						 if(( ! $this->privPodeGravar("_CLIENTES_EMAIL") )&&( ! $this->privPodeLer("_CLIENTES_EMAIL") ) ) {
								$this->privMSG();
								return;
						}
					
					


					
					}





			// PRECISA PASSAR O TIPO PRO MENU
			//
			
			
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
			
			
			
			
			
			
			
			//$prod_contr = $this->obtemPR($id_cliente);
			//$this->tpl->atribui("prod_contr",$prod_contr);


			
			
			
			
			
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
			$sSQL .= "WHERE cp.id_cliente='$id_cliente' AND p.tipo = '$tipo' AND cp.excluido is false ";
			//////////echo $sSQL ."<hr>\n";
			
			
			$produtos = $this->bd->obtemRegistros($sSQL);
			
			//$produtos["quant"] = "0";
			
			for($i=0;$i<count($produtos);$i++) {

			   $id_cp = $produtos[$i]["id_cliente_produto"];
			   
			   $sSQL  = "SELECT ";
			   $sSQL .= "	username, dominio, tipo_conta, id_conta , conta_mestre ";
			   $sSQL .= "FROM ";
			   $sSQL .= "	cntb_conta  ";
			   $sSQL .= "WHERE ";
			   $sSQL .= "	id_cliente_produto = '$id_cp' ";
			   $sSQL .= " AND status != 'C' ";

			   
			   
			   //////////echo $sSQL ."<hr>\n";
			   
			   $contas = $this->bd->obtemRegistros($sSQL);
			   
				



			   $produtos[$i]["contas"] = $contas;

				

			   
			   $sSQL = "SELECT count(id_conta) as quantidade FROM cntb_conta WHERE id_cliente_produto = '$id_cp' AND tipo_conta = '$tipo'";
			   $cnt = $this->bd->obtemRegistros($sSQL);
			   //////echo $sSQL;
			   
			   @$produtos[$i]["quant"] = @$cnt[$i]["quantidade"];
			   
			   
			
			}
			
			//$this->tpl->atribui("quant_contas",$quant);
			$this->tpl->atribui("produtos",$produtos);
			
			$this->arquivoTemplate = "cliente_produto.html";
			
		} else if ($op == "conta") {


		
			$erros = array();
		
			$id_cliente = @$_REQUEST["id_cliente"];
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$username = @str_replace(" ","",$_REQUEST["username"]);
			$dominio  = @str_replace("/","",$_REQUEST["dominio"]);
			$tipo_conta = @$_REQUEST["tipo_conta"];
			$sop = @$_REQUEST["sop"];
			$acao = @$_REQUEST["acao"];
			$tipo = @$_REQUEST["tipo_conta"];
			$id_cliente = @$_REQUEST["id_cliente"];
			$conta_mestre = @$_REQUEST["conta_mestre"];

			//$prod_contr = $this->obtemPR($id_cliente);
			//$this->tpl->abribui("prod_contr",$prod_contr);
			
			
			$bnSQL  = " SELECT n.infoserver  " ;
			$bnSQL .= "FROM cftb_nas n, cntb_conta_bandalarga cn " ;
			$bnSQL .= " WHERE ";
			$bnSQL .= " cn.username = '$username' " ;
			$bnSQL .= " AND cn.dominio  = '$dominio' " ;
			$bnSQL .= " AND cn.id_nas = n.id_nas ";
			////////////////////echo $bnSQL;
			
			$info_server = $this->bd->obtemUnicoRegistro($bnSQL);
			$infoserver = @$info_server['infoserver'];
			
			$this->tpl->atribui("infoserver",$infoserver);

				if ((($lic_bandalarga == 'nao')&&($tipo_conta == "BL"))||(($lic_discado == 'nao')&&($tipo_conta == "D"))||(($lic_hospedagem == 'nao')&&($tipo_conta == "H"))||(($lic_email == 'nao')&&($tipo_conta == "E"))){
	  
	  				$this->licProib();
	  
	  			return;
	  
		}


			
			
			// LISTA DE POPS
			$sSQL  = "SELECT ";
			$sSQL .= "   id_pop, nome ";
			$sSQL .= "FROM ";
			$sSQL .= "   cftb_pop ";
			$sSQL .= "WHERE status = 'A' AND tipo != 'B' ";
			$sSQL .= "ORDER BY ";
			$sSQL .= "   nome";


			//echo "POPs: $sSQL <br>";
			$lista_pops = $this->bd->obtemRegistros($sSQL);
			///echo $sSQL;
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

			//////////echo "NASs: $sSQL <br>";

			$lista_nas = $this->bd->obtemRegistros($sSQL);

			global $_LS_TIPO_NAS;
			
			$bSQL  = " SELECT tipo_bandalarga FROM cntb_conta_bandalarga WHERE username = '$username' AND dominio = '$dominio'  " ;
			$tipo_bandalarga = $this->bd->obtemUnicoRegistro($bSQL);
			
			
			$this->tpl->atribui("tipo_bandalarga",@$tipo_bandalarga['tipo_bandalarga']);
			
			//////$nsSQL  = "SELECT infoserver FROM cftb_nas WHERE id_nas = '$id_nas'  ";
			///echo $nsSQL;
			


			for($i=0;$i<count($lista_nas);$i++) {
				 $lista_nas[$i]["tp"] = $_LS_TIPO_NAS[ $lista_nas[$i]["tipo_nas"] ];
			}

			$this->tpl->atribui("lista_nas",$lista_nas);

			$prefs = $this->prefs->obtem("geral");
			$dominio = str_replace("/","",@$_REQUEST["dominio"]);
			if(!$dominio) $dominio = $prefs["dominio_padrao"];

			if ($sop == "nova_conta"){	
			
				$this->tpl->atribui("id_cliente_produto",@$_REQUEST["id_cliente_produto"]);
				// Obtem os dados do produto contratado
				$dados_pcontratado = $this->obtemInfoProdutoContratado(@$_REQUEST["id_cliente_produto"]);
				$id_produto = $dados_pcontratado["id_produto"];
				
				
				while(list($vr,$vl)=each($dados_pcontratado)) {
					// DEBUG
					////////echo "$vr = $vl <br>\n";
					
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
				$this->tpl->atribui("conta_mestre",$conta_mestre);
				$this->tpl->atribui("tipo_conta",$tipo_conta);
				$this->tpl->atribui("sop",$sop);
				
				$sSQL  = "SELECT * FROM cftb_banda ";
				$lista_download = $this->bd->obtemRegistros($sSQL);
				//echo $sSQL;
				
				$this->tpl->atribui("lista_upload",$lista_download);
				$this->tpl->atribui("lista_download",$lista_download);
				
				global $_LS_ST_CONTA;
				$this->tpl->atribui("lista_status",$_LS_ST_CONTA);
				
				$_username = @str_replace(" ","",$_REQUEST["_username"]);
				$_dominio = @$_REQUEST["_dominio"];
				$_tipo_conta = @$_REQUEST["_tipo_conta"];
				
				$prefs = $this->prefs->obtem("total");								
				$dominio_padrao = $prefs["dominio_padrao"];
				////////echo "DOMINIO: $dominio_padrao <br>";
				$this->arquivoTemplate = "cliente_nova_conta.html";
				
				$acao = @$_REQUEST["acao"];
				if ($acao == "cad"){


				
					$_username = @str_replace(" ","",$_REQUEST["_username"]);
					$dominio = @str_replace("/","",$_REQUEST["dominio"]);
					$tipo_conta = @trim($_REQUEST["tipo_conta"]);
					$id_cliente = @$_REQUEST["id_cliente"];
					$email_igual = @$_REQUEST["email_igual"];

					if ((($lic_bandalarga == 'nao')&&($tipo_conta == "BL"))||(($lic_discado == 'nao')&&($tipo_conta == "D"))||(($lic_hospedagem == 'nao')&&($tipo_conta == "H"))||(($lic_email == 'nao')&&($tipo_conta == "E"))){

						$this->licProib();

					return;

					}
					
					
					
					if ($tipo_conta == "BL"){
					
						if( ! $this->privPodeGravar("_CLIENTES_BANDALARGA") ) {
								$this->privMSG();
								return;
						}
						
					}else if($tipo_conta == "D"){
						if( ! $this->privPodeGravar("_CLIENTES_DISCADO") ) {
								$this->privMSG();
								return;
						}					
										
					}else if($tipo_conta == "H"){
					
						if( ! $this->privPodeGravar("_CLIENTES_HOSPEDAGEM") ) {
								$this->privMSG();
								return;
						}					

					}else if ($tipo_conta == "E"){
					
						if( ! $this->privPodeGravar("_CLIENTES_EMAIL") ) {
								$this->privMSG();
								return;
						}					
					
					
					}
					
					
					
					
					//$prod_contr = $this->obtemPR($id_cliente);
					//$this->tpl->atribui("prod_contr",$prod_contr);

					
					//$sSQL  = "SELECT cp.id_produto FROM cbtb_cliente_produto cp, cntb_conta cn WHERE ";
					//$sSQL .= "cn.username = '$_username' AND ";
					//$sSQL .= "cn.tipo_conta = '$tipo_conta' AND ";
					//$sSQL .= "cn.dominio = '$dominio' AND ";
					//$sSQL .= "cn.id_cliente = '$id_cliente' AND ";
					//$sSQL .= "cn.id_cliente_produto = cp.id_cliente_produto";
					//$_produto = $this->bd->obtemUnicoRegistro($sSQL);
					
					//$id_produto = $_produto["id_produto"];
					//$sSQL = "SELECT id_produto from cbtb_cliente_produto WHERE id_cliente_produto = '".@$_REQUEST["id_cliente_produto"]."' AND id_cliente = '".@$_REQUEST["id_cliente"]."' ";
					//$_prod = $this->bd->obtemUnicoRegistro($sSQL);
					
					//$id_produto = $_prod["id_produto"];
					
					////echo $id_produto ."<br>";
					
					$lista_dominiop = $this->prefs->obtem("geral");

					//$dominioPadrao = $lista_dominiop["dominio_padrao"]; 
					//$dominioPadrao2 = $lista_dominiop["dominio_padrao2"]; 
					////echo "TIPO: ".$_prod["tipo_conta"]."<BR>";
					
					
					//	$dominioPdrao = $lista_dominiop["dominio_padrao2"];
					
				
						$dominio_novo = @str_replace("/","",$_REQUEST["dominio"]);
						$dominio_host = @$_REQUEST["dominio"];
						$dominioPadrao = $lista_dominiop["dominio_padrao"]; 
						
					$sSQL = "SELECT * FROM dominio WHERE dominio = '$dominio_novo'";
					$_prov = $this->bd->obtemRegistros($sSQL);
					
					
					if (!count($_prov)){
					
						$sSQL = "INSERT INTO dominio (dominio,id_cliente,provedor,status,dominio_provedor) VALUES ('$dominio','$id_cliente',false,'A',false)";
						$this->bd->consulta($sSQL);
					
					}				
					
					
					$sSQL = "SELECT * FROM dominio WHERE dominio_provedor is true AND dominio = '$dominio_novo'";
					$_prov2 = $this->bd->obtemRegistros($sSQL);
					
					
						$dominio = @$_REQUEST["dominio"];
					
					
				////echo "DOMINIO: $dominio <br>";
					
					// Valida os dados

					// TODO: Colocar isso em uma funcao private
					$sSQL  = "SELECT ";
					$sSQL .= "   username ";
					$sSQL .= "FROM ";
					$sSQL .= "   cntb_conta ";
					$sSQL .= "WHERE ";
					$sSQL .= "   username = '".@str_replace(" ","",$_REQUEST["username"])."' ";
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
						$sSQL .= "     '" . @str_replace(" ","",$_REQUEST["username"]) . "', ";
						
						//if(trim(@$_REQUEST["tipo_conta"]) == "E"){

						//	$sSQL .= " '". @$_REQUEST["dominio"] ."', ";
						
						
						//}else{

							$sSQL .= "     '" . $dominio . "', ";
						//}
						$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo_conta"])) . "', ";
						$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
						$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
						$sSQL .= "     '" .	$id_cliente_produto . "', ";
						$sSQL .= "     '" . $senhaCr . "', ";
						$sSQL .= "     false, ";
						$sSQL .= "     'A' )";	
						
						$this->bd->consulta($sSQL);  
						//////echo "CNTB_CONTA: $sSQL <br>";
						
						$operacao = "NOVA_CONTA";

						$this->logConta("1", $id_cliente_produto, @$_REQUEST["tipo_conta"], @$_REQUEST["username"], $operacao, $dominio);


						if ($email_igual == "1"){

							$prefs = $this->prefs->obtem("total");


							$id_conta = $this->bd->proximoID("cnsq_id_conta");

							$sSQL  = "INSERT INTO ";
							$sSQL .= "   cntb_conta( ";
							$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript,conta_mestre, status) ";
							$sSQL .= "   VALUES (";
							$sSQL .= "			'". $id_conta. "', ";
							$sSQL .= "     '" . str_replace(" ","",$_REQUEST["username"]) . "', ";
							$sSQL .= "     '" . $dominio . "', ";
							$sSQL .= "     'E', ";
							$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
							$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
							$sSQL .= "     '" .	$id_cliente_produto . "', ";
							$sSQL .= "     '" . $senhaCr . "', ";
							$sSQL .= "     false, ";
							$sSQL .= "     'A' )";						

							$this->bd->consulta($sSQL);  
							//////echo "CNTB_CONTA: $sSQL <br>";

							$id_produto = @$_REQUEST['id_produto'];
							$prod = $this->obtemProduto($id_produto);	
							
							//////echo "QUOTA: " . $prod["quota_por_conta"] . "<br>\n";

							if ($prod["quota_por_conta"] == "" || !$prod ){
								$quota = "0";
							}else {
								$quota = $prod["quota_por_conta"];
							}

							$sSQL  = "INSERT INTO ";
							$sSQL .= "	cntb_conta_email( ";
							$sSQL .= "		username, tipo_conta, dominio, quota, email) ";
							$sSQL .= "VALUES (";
							$sSQL .= "     '" . @str_replace(" ","",$_REQUEST["username"]) . "', ";
							$sSQL .= "     'E', ";
							$sSQL .= "     '" . $dominio . "', ";
							$sSQL .= "     '$quota', ";
							$sSQL .= "     '". @str_replace(" ","",$_REQUEST["username"])."@". $dominio ."' ";
							$sSQL .= " )";

							$this->bd->consulta($sSQL);
							//////echo "E-MAIL: $sSQL <br>";
							
							$server = $this->prefs->obtem("geral","mail_server");
							$this->spool->adicionarEmail($server, $id_conta, @str_replace(" ","",$_REQUEST["username"]), $dominioPadrao);

						}
	
						$tipo = trim(@$_REQUEST["tipo"]);
						$prefs = $this->prefs->obtem();
						$tipo_conta = trim($tipo_conta);
						
						switch($tipo_conta) {
							case 'D':

								$username = @str_replace(" ","",$_REQUEST["username"]);
								$tipo_conta = @$_REQUEST["tipo_conta"];
								$dominio = $prefs["geral"]["dominio_padrao"];
								$foneinfo = @$_REQUEST["foneinfo"];

								$sSQL  = "INSERT INTO ";
								$sSQL .= "cntb_conta_discado ";
								$sSQL .= "( ";
								$sSQL .= "username, tipo_conta, dominio, foneinfo ";
								$sSQL .= ")VALUES ( ";
								$sSQL .= "'$username', '$tipo_conta', '$dominio', '$foneinfo' )";

								//////echo "SQL DISCADOC: $sSQL <br>\n";

								$this->bd->consulta($sSQL);

								$this->tpl->atribui("foneinfo",$foneinfo);

							break;	
							case 'BL':
							
							
							
														//////echo "TIPO: " . $this->bd->escape(trim(@$_REQUEST["selecao_ip"])) . "<br>\n";
														
															// PRODUTO BANDA LARGA
															$tipo_de_ip = $this->bd->escape(trim(@$_REQUEST["selecao_ip"]));
															if($tipo_de_ip == "A"){
																$nas = $this->obtemNAS($_REQUEST["id_nas"]);
																//////////echo "NAS: ".$nas["id_nas"]."<BR>";
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
																	//////echo count($erro);
																	//for($i=0;$i<count($erro);$i++) {
																	   //////echo $erro[$i] . "<br>\n";
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
																////////echo $ip_externo["ip_externo"];
																
																if($nas["tipo_nas"] == "P"){
																	
																	$ipaddr = $ip_disp;
																
																}else if ($nas["tipo_nas"] == "I"){
																
																	$ipaddr = $rede_disp;
																
																}
																
																$username = @str_replace(" ","",$_REQUEST["username"]);
																$tipo_conta = @$_REQUEST["tipo"];
																//$dominio = $prefs["geral"]["dominio_padrao"];
																//$dom = $prefs["total"];
																
																$dSQL = "SELECT dominio_padrao FROM pftb_preferencia_geral WHERE id_provedor = '1' ";
																$dom = $this->bd->obtemUnicoRegistro($dSQL);
																//////echo "SQL DOMINIO: $dSQL <br>";
																
																$dominio = $dom["dominio_padrao"];
							
																
																$sSQL = "SELECT id_conta FROM cntb_conta WHERE username = '$username' AND tipo_conta = 'BL' AND dominio = '$dominio' ";
																$_id_conta = $this->bd->obtemUnicoRegistro($sSQL);
																//////echo "ID_CONTA: $sSQL";
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
																//////echo "rede:". $rede_disponivel["rede"]. "<br>";
															
															
															}
															
															if($ip_disp !="NULL"){
															
																$ip_disp = "'".$ip_disp."'";
															
															
															}
															
															$id_produto = $this->bd->escape(@$_REQUEST["id_produto"]);
															$bandaUp_dow = $this->obtemDowUp($id_produto);
															$download_kbps = @$_REQUEST["download_kbps"];
															$upload_kbps = @$_REQUEST["upload_kbps"];
															
															//echo "DOWN: $download_kbps <br>";
															//echo "UP: $upload_kbps <br>";
															
															
															$MAC = @$_REQUEST["mac"];
															
															if($MAC ==""){
																$_MAC = "NULL";
															}else {
																$_MAC = "'".$MAC."'";
															}
															
															//$id_conta_banda_larga = $this->bd->proximoID("clsq_id_conta_bandalarga_seq");
															
															//$id_pop = $_REQUEST["id_pop"];
															//////////echo "IDPOP: $id_pop <br>";
															
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
															$sSQL .= "     '" . str_replace(" ","",$_REQUEST["username"])  . "', ";
															$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["tipo_conta"])). "', ";
															$sSQL .= "     '" . $dominioPadrao . "', ";
															$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_pop"])) . "', ";
															$sSQL .= "     '" . $nas["tipo_nas"] . "', ";
															$sSQL .= "     "  . $ip_disp . ", ";
															$sSQL .= "     "  . $rede_disp . ", ";
															$sSQL .= "     '" . $_REQUEST["upload_kbps"] . "', ";
															$sSQL .= "     '" . $_REQUEST["download_kbps"] . "', ";
															$sSQL .= "     'A', ";
															$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["id_nas"])) . "', ";
															$sSQL .= "     "  . $_MAC .", ";
															$sSQL .= "	   "  . $ip_externo ."  ";
															$sSQL .= "     )";						
															
															
															//echo  $sSQL;
															$this->bd->consulta($sSQL);  
							
															break;
							
							
							
							
							
							
							
							
							
							
					/*			// PRODUTO BANDA LARGA
								$tipo_de_ip = $this->bd->escape(trim(@$_REQUEST["selecao_ip"]));
								if($tipo_de_ip == "A"){
									$nas = $this->obtemNAS($_REQUEST["id_nas"]);
									//////////echo "NAS: ".$nas["id_nas"]."<BR>";
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
								
								
								
								
								
								
								/////////////////////////////////////////////////
								

								$redirecionar = @$_REQUEST["redirecionar"];

								if($redirecionar == "true"){

									$ip_externo = $this->obtemIPExterno($_REQUEST["id_nas"]);
									////////echo $ip_externo["ip_externo"];

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
									//////////echo "rede:". $rede_disponivel["rede"]. "<br>";


								}

								if($ip_disp !="NULL"){

									$ip_disp = "'".$ip_disponivel["ipaddr"]."'";

								}

								

								////echo "$id_produto";
								$bandaUp_dow = $this->obtemDowUp($id_produto);
								$MAC = @$_REQUEST["mac"];

								if($MAC ==""){
									$_MAC = "NULL";
								}else {
									$_MAC = "'".$MAC."'";
								}

								//////////echo "IDPOP: $id_pop <br>";

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


								////////echo "INSERT NA BL: $sSQL <br>";
								$this->bd->consulta($sSQL);  

								break;*/

							case 'H':
								// PRODUTO HOSPEDAGEM
								//$sSQL  = "SELECT * from cftb_preferencias where id_provedor = '1'";							


								$prefs = $this->prefs->obtem("total");								
								//$prefs = $this->prefs->obtem();

								$username = @str_replace(" ","",$_REQUEST["username"]);
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





								//if (!count($erros2)){
									$sSQL  = "INSERT INTO ";
									$sSQL .= " cntb_conta_hospedagem ( ";
									$sSQL .= "		username, tipo_conta, dominio, tipo_hospedagem, senha_cript, uid, gid, home, shell, dominio_hospedagem ";
									$sSQL .= ") VALUES ( ";
									$sSQL .= " 		'$username', '$tipo_conta', '$dominio', '$tipo_hospedagem', '$senha_cript', '$uid', '$gid', '$home', '$shell', '$dominio_hospedagem' ";
									$sSQL .= ") ";

									$this->bd->consulta($sSQL);
									//////////echo "QUERY INSERÇÃO: $sSQL <BR>\n";
									$ns1 = $this->prefs->obtem("geral","hosp_ns1");
									$ns2 = $this->prefs->obtem("geral","hosp_ns2");
									
									$this->spool->configuraDNS($ns1, "N1", $id_conta, $dominio_hospedagem);
									$this->spool->configuraDNS($ns2, "N2", $id_conta, $dominio_hospedagem);

									//SPOOL
									//////////echo "Tipo: $tipo_hospedagem <br> Username: $username <br> Dominio: $dominio <br> DominioHosp: $dominio_hospedagem<br>";
									$this->spool->hospedagemAdicionaRede($server,$id_conta,$tipo_hospedagem,$username,$dominio,$dominio_hospedagem);
								//}
								break;
								case "E":
								
									$sSQL  = "INSERT INTO ";
									$sSQL .= "	cntb_conta_email( ";
									$sSQL .= "		username, tipo_conta, dominio, quota, email) ";
									$sSQL .= "VALUES (";
									$sSQL .= "     '" . @str_replace(" ","",$_REQUEST["username"]) . "', ";
									$sSQL .= "     'E', ";
									$sSQL .= " '$dominio' , ";
									//$sSQL .= "     '" . $dominioPadrao . "', ";
									$sSQL .= "     '".(int)@$_REQUEST["quota"]."', ";
									$sSQL .= "     '". @str_replace(" ","",$_REQUEST["username"])."@". $dominio ."' ";
									$sSQL .= " )";

									$this->bd->consulta($sSQL);
								////echo "E-MAIL: $sSQL <br>";
								
								$server = $this->prefs->obtem("geral","mail_server");
								
								$this->spool->adicionarEmail($server, $id_conta, str_replace(" ","",$_REQUEST["username"]), $dominio);
								
								break;
						}						
						$tipo = $tipo_conta;
						if ($tipo && $tipo == "BL"){

						//////////echo $tipo;
							// Envia instrucao pra spool
							
							
														if ($nas && $nas["tipo_nas"] == "I"){
							
															$id_nas = $_REQUEST["id_nas"];
															$banda_upload_kbps = @$_REQUEST["upload_kbps"];
															$banda_download_kbps = @$_REQUEST["download_kbps"];
															$rede = str_replace("'","",$rede_disp); //$rede_disponivel["rede"];
															$mac = $_REQUEST["mac"];
							
															$sSQL  = "SELECT ";
															$sSQL .= "   id_nas, nome, ip, tipo_nas ";
															$sSQL .= "FROM ";
															$sSQL .= "   cftb_nas ";
															$sSQL .= "WHERE ";
															$sSQL .= "   id_nas = '$id_nas'";
															//////////echo "SQL : " . $sSQL . "<br>\n";
							
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
							
															
															$username = @str_replace(" ","",$_REQUEST["username"]);
															$this->spool->bandalargaAdicionaRede($destino,$id_conta,$rede,$mac,$banda_upload_kbps,$banda_download_kbps,$username);
							
							
							
							}
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							

							/*	$id_nas = $_REQUEST["id_nas"];
								$rede_disponivel = $this->obtemRede("");
								$banda_upload_kbps = @$_REQUEST["upload_kbps"];
								$banda_download_kbps = @$_REQUEST["download_kbps"];
								$rede = $rede_disponivel["rede"];
								$mac = $_REQUEST["mac"];

								$sSQL  = "SELECT ";
								$sSQL .= "   id_nas, nome, ip, tipo_nas ";
								$sSQL .= "FROM ";
								$sSQL .= "   cftb_nas ";
								$sSQL .= "WHERE ";
								$sSQL .= "   id_nas = '$id_nas'";
								//////////echo "SQL : " . $sSQL . "<br>\n";

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

*/
















							

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


							////////echo "EXTERNO: $sSQL <br>";
							$this->tpl->atribui("ip_externo",$externo["ip_externo"]);


						}
			
						// Joga a mensagem de conta adicionada com sucesso.
						$this->tpl->atribui("username",@str_replace(" ","",$_REQUEST["username"]));
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
				
				$sSQL  = "SELECT h.dominio_hospedagem as dominio FROM cntb_conta c, cntb_conta_hospedagem h WHERE ";
				$sSQL .= "c.username = h.username AND ";
				$sSQL .= "c.tipo_conta = h.tipo_conta AND ";
				$sSQL .= "c.dominio = h.dominio AND ";
				$sSQL .= "c.id_cliente = $id_cliente ";
				$hospeda = $this->bd->obtemRegistros($sSQL);
				////echo $sSQL ."<br>";
				if (count($hospeda)) {
				
					$dominios_provedor = array_merge($dominios_provedor, $hospeda);
					
				}
				
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
			$sSQL .= "WHERE status = 'A' AND tipo != 'B' ";
			$sSQL .= "ORDER BY ";
			$sSQL .= "   nome";


			//////////echo "POPs: $sSQL <br>";
			$lista_pops = $this->bd->obtemRegistros($sSQL);
			///echo $sSQL;
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
			
			//////////echo "NASs: $sSQL <br>";
			
			$lista_nas = $this->bd->obtemRegistros($sSQL);
			
			global $_LS_TIPO_NAS;

			for($i=0;$i<count($lista_nas);$i++) {
			   $lista_nas[$i]["tp"] = $_LS_TIPO_NAS[ $lista_nas[$i]["tipo_nas"] ];
			}

			$this->tpl->atribui("lista_nas",$lista_nas);

			$sSQL  = "SELECT ";
			$sSQL .= "   username, dominio, tipo_conta, senha, status, id_conta, id_cliente_produto , conta_mestre ";
			$sSQL .= "";
			$sSQL .= "FROM ";
			$sSQL .= "   cntb_conta ";
			$sSQL .= "";
			$sSQL .= "WHERE ";
			$sSQL .= "   username = '".$this->bd->escape($username)."' ";
			$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
			$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
			$sSQL .= "";
			
			
			$conta = $this->bd->obtemUnicoRegistro($sSQL);
			 $conta_mestre_val = $conta["conta_mestre"]	;
	
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
			$this->tpl->atribui("conta_mestre_val",$conta_mestre_val);



			
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

					 /////////echo "$sSQL;<br>\n";

					$cbl = $this->bd->obtemUnicoRegistro($sSQL);
					/////////////echo $cbl["tipo_bandalarga"];

					$conta = array_merge($conta,$cbl);


					$this->tpl->atribui("status",@$_REQUEST["status"]);
					$this->tpl->atribui("upload_kbps",@$_REQUEST["upload_kbps"]);
					$this->tpl->atribui("download_kbps",@$_REQUEST["download_kbps"]);

					//////echo "ID NAS:". $conta["id_nas"] ."<BR>";

					$nas = $this->obtemNas(@$conta["id_nas"]);
					$conta["endereco_ip"] = @$nas["tipo_nas"] == "I" ? @$conta["rede"] : @$conta["ipaddr"];

					  if (!$cbl){ 
					  		
							$id_cliente = @$_REQUEST["id_cliente"];
							$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
							$username = @str_replace(" ","",$_REQUEST["username"]);
							$dominio  = @str_replace("/","",$_REQUEST["dominio"]);
							$tipo_conta = @$_REQUEST["tipo_conta"];
							$sop = @$_REQUEST["sop"];
							$acao = @$_REQUEST["acao"];
							$tipo = @$_REQUEST["tipo_conta"];
							$id_cliente = @$_REQUEST["id_cliente"];

							

							$sSQL  = " SELECT ";
							$sSQL .= " clc.id_cliente, cc.valor_contrato, cnc.username, pr.nome, clc.nome_razao";
							$sSQL .= " FROM ";
							$sSQL .= " cltb_cliente clc , cbtb_contrato cc , cntb_conta cnc , prtb_produto pr , cbtb_cliente_produto cp ";
							$sSQL .= " WHERE ";
							$sSQL .= " cp.id_cliente = clc.id_cliente ";
							$sSQL .= " AND cc.id_cliente_produto = cc.id_cliente_produto ";
							$sSQL .= " AND pr.tipo = '$tipo' ";
							$sSQL .= " AND clc.id_cliente = '$id_cliente' ";
							$sSQL .= " AND cnc.username = '$username' ";
							$sSQL .= " AND cnc.dominio = '$dominio' ";
							$sSQL .= " AND pr.tipo = cnc.tipo_conta ";
							$sSQL .= " AND cnc.id_cliente_produto = cc.id_cliente_produto ";
							$sSQL .= " AND cp.id_produto = pr.id_produto ";	


					  $erro_conta = $this->bd->obtemUnicoRegistro($sSQL) ;
					  $this->tpl->atribui("erro_conta",$erro_conta);
					  
					  }


					//$nas_orig = @$_REQUEST["nas_orig"];

					$endereco_ip = @$_REQUEST["endereco_ip"];
					if( !$endereco_ip ) $endereco_ip = $conta["endereco_ip"];

					$selecao_ip = @$_REQUEST["selecao_ip"];

					


					// ATRIBUI AS VARIAVEIS DE TEMPLATE COM BASE EM REQUEST.
					
					$sSQL  = " SELECT * FROM cftb_banda ORDER BY id";
					$lista = $this->bd->obtemRegistros($sSQL);
					//echo $sSQL ;
					
					$this->tpl->atribui("lista_upload",$lista);
					$this->tpl->atribui("lista_download",$lista);

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
					
					//////echo "CONTA EMAIL: $sSQL <br>";
					
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
				
					//////echo "$sSQL;<br>\n";
				
				
				
					$hosp = $this->bd->obtemUnicoRegistro($sSQL);
				
					$conta = array_merge($conta,$hosp);
					

				
				
				
					break;

			}

			$acao = @$_REQUEST["acao"];
			$senha = @$_REQUEST["senha"];
			
			if( $acao == "cad" ) {
			
					if ($tipo_conta == "BL"){
						if( ! $this->privPodeGravar("_CLIENTES_BANDALARGA") ) {
								$this->privMSG();
								return;
						}
					}else if($tipo_conta == "D"){
						if( ! $this->privPodeGravar("_CLIENTES_DISCADO") ) {
								$this->privMSG();
								return;
						}					
										
					}else if($tipo_conta == "H"){
					
						if( ! $this->privPodeGravar("_CLIENTES_HOSPEDAGEM") ) {
								$this->privMSG();
								return;
						}					

					}else if ($tipo_conta == "E"){
					
						if( ! $this->privPodeGravar("_CLIENTES_EMAIL") ) {
								$this->privMSG();
								return;
						}					
					
					
					}			
			
			
			
			
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
						
						//////////echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";
						//////////echo "UP: " . $conta["upload_kbps"] . "|" . $upload_kbps . "<br>\n";
						//////////echo "DN: " . $conta["download_kbps"] . "|" . $download_kbps . "<br>\n";
						//////////echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";
						//////////echo "MAC: " . $conta["mac"] . "|" . $mac . "<br>\n";

						if( $conta["mac"] != $mac || $conta["upload_kbps"] != $upload_kbps || $conta["download_kbps"] != $download_kbps ) {
							$excluir = true;
							$incluir = true;
						}

						if( $altera_rede ) {
							//ECHO "selecao ip = $selecao_ip <br>";

							if ($selecao_ip == "A"){
								//echo "AUTOMATICO <BR>";
						
									if ($nas_atual["tipo_nas"] == "I" && $nas_novo["tipo_nas"] == "P"){
										$endereco_ip = $this->obtemIP($id_nas);
										$ip = $endereco_ip["ipaddr"];
										$rede = NULL;
										////echo "IP: $ip <br>";
										//$excluir = true;
										//$incluir = true;
									}ELSE{

										$endereco_rede = $this->obtemRede($id_nas);
										$rede = $endereco_rede["rede"];
										$ip = NULL;
										$excluir = true;
										$incluir = true;

										////echo "REDE: $rede <br>";
									}
						
							} else {
									//echo "MANUAL <BR>";
							
									if ($nas_atual["tipo_nas"] == "I" && $nas_novo["tipo_nas"] == "P"){
										//$endereco_ip = $this->obtemIP($id_nas);
										$ip = @$_REQUEST["endereco_ip"];
										$rede = NULL;
										////echo "IP: $ip <br>";
										//$excluir = true;
										//$incluir = true;
									}ELSE{

										//$endereco_rede = $this->obtemRede($id_nas);
										$rede = @$_REQUEST["endereco_ip"];
										$ip = NULL;
										$excluir = true;
										$incluir = true;

										////echo "REDE: $rede <br>";
									}

							
							
							
							
							
							
							
							
							
							}
						
						
						
						
						
						
						
						
						
						}// termina primeiro if

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
				//$agora = DATE("Y-m-d h:i:s");
				
				if (!$status){
				
					$status = "S";
				
				}


				$sSQL = "SELECT status,conta_mestre,senha,senha_cript,username,dominio,tipo_conta,id_cliente_produto FROM cntb_conta WHERE username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta' ";
				$CONTA = $this->bd->obtemUnicoRegistro($sSQL);
				
				if ($status && $CONTA["status"] != $status){
				
					$operacao = "ALTSTATUS";
					$extra = $CONTA["dominio"];
					
					$this->logAdm($operacao,$CONTA["status"],$status,$username,$CONTA["id_cliente_produto"],$tipo_conta,$extra);

				}
				

					switch($tipo_conta) {
					



						case 'D':						
							
							$senha_cript = $this->criptSenha($senha);
							
							$_status = @$_REQUEST["status"];
							$status = @$_REQUEST["status"];

							if ($_status==""){

								$status = 'S' ;

							}

							
							$iSQL  = " SELECT id_cliente_produto, status, senha, conta_mestre FROM cntb_conta WHERE username ='$username' AND dominio = '$dominio' AND tipo_conta ='$tipo_conta' ";
							$reg = $this->bd->obtemUnicoRegistro($iSQL);
							
							$fSQL  = " SELECT foneinfo FROM cntb_conta_discado WHERE username = '$username' AND tipo_conta = '$tipo_conta' AND dominio = '$dominio' ";
							$reg_fone = $this->bd->obtemUnicoRegistro($fSQL);
							
							$id_cliente_produto = $reg['id_cliente_produto'];

							if ($status != $reg['status'] && $status == 'S' ){

								$operacao = "ALTSTATUS(SUSPENSO)";

							}
							if ($status != $reg['status'] && $status== 'B' ){

								$operacao = "ALTSTATUS(BLOQUEADO)";
								$this->logConta("4",$id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);

							}
							if ($status != $reg['status'] && $status=='A'){

								$operacao = "ALTSTATUS(ATIVO)";
								$this->logConta("3", $id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);

							}
							if (@$_REQUEST['foneinfo'] != $reg_fone['foneinfo'] && @$_REQUEST['foneinfo']){
							
								$operacao = 'ALTFONE';
								$this->logConta("6", $id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);
							
							}
							if ($senha != $reg['senha'] && $senha ){
														
								$operacao = 'ALTSENHA';

								$this->logConta("2",$id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);							
							}
							if ($conta_mestre != $reg['conta_mestre'] && $conta_mestre =='f' ){
														
								$operacao = 'ALTSTATUSCONTA(NORMAL)';

								$this->logConta("10",$id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);							
							}
							if ($conta_mestre != $reg['conta_mestre'] && $conta_mestre =='t' ){
														
								$operacao = 'ALTSTATUSCONTA(MESTRE)';

								$this->logConta("11",$id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);							
							}
							
														
							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta_discado ";
							$sSQL .= "SET ";
							$sSQL .= "	foneinfo = '".@$_REQUEST['foneinfo']."' ";
							$sSQL .= "WHERE ";
							$sSQL .= "	username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";
							///////////echo "SQL: $sSQL <br><hr>";
													
							$this->bd->consulta($sSQL);
							
							$sSQL  = "UPDATE ";
							$sSQL .= "   cntb_conta ";
							$sSQL .= "SET ";
							$sSQL .= "   status = '$status',  ";
							$sSQL .= "   conta_mestre = '$conta_mestre' ";
							if( $senha ) {
								$sSQL .= "   , senha = '".$this->bd->escape($senha)."' ";
								$sSQL .= "   , senha_cript = '".$this->criptSenha($senha)."' ";
							}

							$sSQL .= "WHERE ";
							$sSQL .= "   username = '".$this->bd->escape($username)."' ";
							$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
							$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
													
							$this->bd->consulta($sSQL);
							
							//////////echo $sSQL ."<br>\n<hr>" ;
							
						
							break;
					
						case 'BL':
						//////////echo "TESTE";
						

						
							$aSQL = "SELECT * from cntb_conta_bandalarga where username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta' ";
							$bandalarga = $this->bd->obtemUnicoRegistro($aSQL);
							//echo "BANDALARGA: $aSQL <br>";
							$extra = $bandalarga["dominio"];
							
							
							if ($upload_kbps != $bandalarga["upload_kbps"] || $download_kbps != $bandalarga["download_kbps"]){
							
								$valor_original = $bandalarga["upload_kbps"]."|".$bandalarga["download_kbps"];
								$valor_alterado = $upload_kbps."|".$download_kbps;
								$operacao = "ALTBANDA";
								
								$this->logAdm($operacao,$valor_original,$valor_alterado,$username,$CONTA["id_cliente_produto"],$tipo_conta,$extra);
								
								$operacao = "ALTBANDA - " . $valor_original . " - " . $valor_alterado ; 
								
								$this->logConta("8", $CONTA['id_cliente_produto'], $tipo_conta, $username, $operacao, $dominio);

							
							}
							
							if ($mac != $bandalarga["mac"] && $mac){

								$operacao = "ALTMAC";
								
								$this->logAdm($operacao,$bandalarga["mac"],$mac,$username,$CONTA["id_cliente_produto"],$tipo_conta,$extra);
								
								$this->logConta("9", $CONTA['id_cliente_produto'], $tipo_conta, $username, $operacao, $dominio);
							
														
							}
							$stSQL  = " SELECT status, senha, conta_mestre FROM cntb_conta WHERE username ='$username' AND tipo_conta ='$tipo_conta' AND dominio = '$dominio' ";
							$statusalt = $this->bd->obtemUnicoRegistro($stSQL);
							////////echo $stSQL ;


							if ($conta_mestre != $statusalt["conta_mestre"] && $conta_mestre=='f'){
														
								$operacao = "ALTSTATUSCONTA(NORMAL)";

								$this->logConta("10", $CONTA['id_cliente_produto'], $tipo_conta, $username, $operacao, $dominio);
							
							}
							if ($conta_mestre != $statusalt["conta_mestre"] && $conta_mestre=='t'){
														
								$operacao = "ALTSTATUSCONTA(MESTRE)";

								$this->logConta("11", $CONTA['id_cliente_produto'], $tipo_conta, $username, $operacao, $dominio);

							}
							if ($id_pop != $bandalarga["id_pop"] && $id_pop){
																					
								$operacao = "ALTPOP";

								$this->logConta("12", $CONTA['id_cliente_produto'], $tipo_conta, $username, $operacao, $dominio);

							}
							
							
							
							if ($status != $statusalt['status'] && $status){
							
								if($status == 'S'){

									$operacao = 'ALTSTATUS(SUSPENSO)';
									$cod_operacao = "5";

								}
								if($status == 'A'){

									$operacao = 'ALTSTATUS(ATIVO)';
									$cod_operacao = "3";

								}
								if($status == 'B'){
																
									$operacao = 'ALTSTATUS(BLOQUEADO)';
									$cod_operacao = "4";

								}
							
								$this->logConta($cod_operacao, $CONTA['id_cliente_produto'], $tipo_conta, $username, $operacao, $dominio);
							}
							
							
							if ($senha != $statusalt['senha'] && $senha){
							
							
								$operacao = 'ALTSENHA' ;
								
								$this->logConta("2", $CONTA['id_cliente_produto'], $tipo_conta, $username, $operacao, $dominio);
							
							
							}
							
							
							if ( $id_nas != $bandalarga['id_nas'] && $id_nas){
							
								$operacao = 'ALTNAS' ;
							
								$this->logConta("7", $CONTA['id_cliente_produto'], $tipo_conta, $username, $operacao, $dominio);
							
							
							}
				
							// SPOOL (ALTERADO HUGO)
							if ( $conta["tipo_bandalarga"] == "I"){
								if( $excluir ) {
									//////////echo "excluir";
									$this->spool->bandalargaExcluiRede($nas_atual["id_nas"],$conta["id_conta"],$conta["rede"]);
								}

								if( $incluir ) {
									//////////echo "incluir<br>";
									$id_conta = $conta["id_conta"];
									$this->spool->bandalargaAdicionaRede($nas_novo["id_nas"],$id_conta,$rede,$mac,$upload_kbps,$download_kbps,$username);
								}
							}

							// Faz o update nos dados em cntb_conta e cntb_conta_bandalarga
							
							$tipo_bandalarga = $nas_novo["tipo_nas"];

							$sSQL  = "UPDATE ";
							$sSQL .= "   cntb_conta ";
							$sSQL .= "SET ";
							$sSQL .= "   status = '$status', ";
							$sSQL .= "   conta_mestre = '$conta_mestre' ";
							if( $senha ) {
								$sSQL .= "   , senha = '".$this->bd->escape($senha)."' ";
								$sSQL .= "   , senha_cript = '".$this->criptSenha($senha)."' ";
							}

							$sSQL .= "WHERE ";
							$sSQL .= "   username = '".$this->bd->escape($username)."' ";
							$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
							$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
							$sSQL .= "";

							

							$this->bd->consulta($sSQL);

							$uSQL  = "UPDATE ";
							$uSQL .= "   cntb_conta_bandalarga ";
							$uSQL .= "SET ";

							$uSQL .= "   tipo_bandalarga = '$tipo_bandalarga', ";
							$uSQL .= "   id_nas = '".$this->bd->escape($id_nas)."', ";
							$uSQL .= "   id_pop = '".$this->bd->escape($id_pop)."', ";
							$uSQL .= "   upload_kbps = '".$this->bd->escape($upload_kbps)."', ";
							$uSQL .= "   download_kbps = '".$this->bd->escape($download_kbps)."', ";
							
							/////////////echo $uSQL . "<br>\n<hr>" ;
							///////////////echo $sSQL . "<br>\n<hr>" ;
							
							if ( !$mac || $mac == "" ){
							$uSQL .= "   mac = NULL ";
							} else {
							
							$uSQL .= "   mac = '".$this->bd->escape($mac)."' ";
							
							}
							
							if( $rede ) {
								
								$uSQL .= ", ipaddr = null, ";
								$uSQL .= "  rede = '".$rede."' ";
								
							}else if (@$ip){
							
								$uSQL .= ", ipaddr = '$ip' ";
								$uSQL .= ", rede = null ";
							
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

							
							
							
							
							//////////echo "$uSQL;<br>\n";
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

							//////////echo "SQL1: $sSQL <br>\n";


							$this->bd->consulta($sSQL);

							$sSQL  = "UPDATE ";
							$sSQL .= "   cntb_conta ";
							$sSQL .= "SET ";
							$sSQL .= "   status = '$status' ";
							if( $senha ) {
								$sSQL .= "   , senha = '".$this->bd->escape($senha)."' ";
								$sSQL .= "   , senha_cript = '".$this->criptSenha($senha)."' ";
							}

							$sSQL .= "WHERE ";
							$sSQL .= "   username = '".$this->bd->escape($username)."' ";
							$sSQL .= "   AND dominio = '".$this->bd->escape($dominio)."' ";
							$sSQL .= "   AND tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
							//////////echo "SQL1: $sSQL <br>\n";

							$this->bd->consulta($sSQL);
							


							break;
							
						case 'H':
							
							$tipo_hospedagem = @$_REQUEST["tipo_hospedagem"];
							$senha = @$_REQUEST["senha"];
							$dominio_hospedagem = @str_replace("/","",$_REQUEST["dominio_hospedagem"]);
							$senha_cript = $this->criptSenha($senha);
							$id_conta = $conta["id_conta"];
							//$server = $conta["mail_server"];
							//$dominio_padrao = $conta["dominio_padrao"];
							

							$iSQL  = " SELECT id_cliente_produto, status, senha, conta_mestre FROM cntb_conta WHERE username ='$username' AND dominio = '$dominio' AND tipo_conta ='$tipo_conta' ";
							$reg = $this->bd->obtemUnicoRegistro($iSQL);

							$id_cliente_produto = $reg['id_cliente_produto'];

							if ($status != $reg['status'] && $status == 'S' ){

								$operacao = "ALTSTATUS(SUSPENSO)";


							}
							if ($status != $reg['status'] && $status== 'B' ){

								$operacao = "ALTSTATUS(BLOQUEADO)";
								$this->logConta($id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);

							}
							if ($status != $reg['status'] && $status=='A'){

								$operacao = "ALTSTATUS(ATIVO)";
								$this->logConta($id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);
							}
							if ($senha != $reg['senha'] && $senha ){

								$operacao = 'ALTSENHA';

								$this->logConta("2", $id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);							
							}
							if ($conta_mestre != $reg['conta_mestre'] && $conta_mestre == 'f' ){
							
								$operacao = 'ALTSTATUSCONTA(NORMAL)';

								$this->logConta("10", $id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);							
							}
							if ($conta_mestre != $reg['conta_mestre'] && $conta_mestre == 't' ){

								$operacao = 'ALTSTATUSCONTA(MESTRE)';

								$this->logConta("11", $id_cliente_produto, $tipo_conta, @$_REQUEST["username"], $operacao, $dominio);							
							}
							
							$sSQL  = "UPDATE ";
							$sSQL .= "	cntb_conta_hospedagem ";
							$sSQL .= "SET ";
							$sSQL .= "	dominio_hospedagem = '$dominio_hospedagem' ";
							if ($senha){
								$sSQL .= "  , senha_cript = '$senha_cript' ";
							}
							$sSQL .= "WHERE ";
							$sSQL .= "	username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";
							//////////echo "SQL: $sSQL <br>";
						
							$this->bd->consulta($sSQL);
							
							
							$sSQL  = "UPDATE ";
							$sSQL .= "   cntb_conta ";
							$sSQL .= "SET ";
							$sSQL .= "   status = '$status' , ";
							$sSQL .= "   conta_mestre = '$conta_mestre' ";
							if( $senha ) {
								$sSQL .= "   , senha = '".$this->bd->escape($senha)."' ";
								$sSQL .= "   , senha_cript = '$senha_cript' ";
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
					//manda a confirmação de email para outra pagina
					if ($tipo_conta=="E"){
					   $this->tpl->atribui("url","javascript:history.back();history.back();");
					}
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
				//////////echo "indisponivel";
				//return;
			}
			
			// Trata o tipo de exibicao.
			$pg = @$_REQUEST["pg"];
			
			if( $pg == "ficha" ) {
				$bSQL  = " SELECT tipo_bandalarga FROM cntb_conta_bandalarga WHERE username = '$username' AND dominio = '$dominio'  " ;
				$tipo_bandalarga = $this->bd->obtemUnicoRegistro($bSQL);
				
				if ($conta['status'] == 'B' ){
				
					$str_status = 'Bloqueado' ;
				}
				if ($conta['status'] == 'S' ){
					
					$str_status = 'Suspenso' ;
				
				}if ($conta['status'] == 'A'){
				
					$str_status = 'Ativo';
			
				}
				
				$idSQL  = " SELECT id_cliente_produto FROM cntb_conta WHERE dominio = '$dominio' AND username ='$username' AND tipo_conta ='$tipo_conta' " ;
				$id = $this->bd->obtemUnicoRegistro($idSQL);
				$id_cliente_produto = $id['id_cliente_produto'];
				
				
				$hsSQL  = " SELECT  l.id_cliente_produto ,l.username, l.dominio, l.tipo_conta, l.data_hora, l.id_admin, ";
				$hsSQL .= " l.ip_admin, l.operacao, l.cod_operacao, a.admin    " ;       
				$hsSQL .= " FROM lgtb_status_conta l, adtb_admin a ";
				$hsSQL .= " WHERE l.id_admin = a.id_admin ";
				$hsSQL .= " AND username = '$username' AND tipo_conta = '$tipo_conta' AND dominio = '$dominio' ";
				$hsSQL .= " AND id_cliente_produto = '$id_cliente_produto' GROUP BY data_hora ,admin , l.id_admin, ip_admin ,cod_operacao , operacao, username, id_cliente_produto, dominio, tipo_conta ORDER BY data_hora, username, operacao, l.id_admin " ;
				$rel = $this->bd->obtemRegistros($hsSQL) ;
				
				///echo $hsSQL;
			
				if(!count($rel)){
					$count_historico = 'false';
				}else{
					$count_historico = 'true';
				}
					
				
				$hSQL  = "SELECT username, dominio, operacao, cod_operacao FROM lgtb_status_conta WHERE tipo_conta = '$tipo_conta' AND username = '$username' AND dominio = '$dominio' AND operacao <> '' ";
				$cont_historico = $this->bd->obtemRegistros($hSQL);
				
				@$nas = $this->obtemNas(@$conta["id_nas"]);
				
				$id_nas = $nas['id_nas'];
				$nSQL  = " SELECT infoserver FROM cftb_nas WHERE id_nas = '$id_nas' " ;
				
				$info_nas = $this->bd->obtemUnicoRegistro($nSQL);
				$infoserver = $info_nas['infoserver'];
				
				$this->tpl->atribui("count_historico",$count_historico);
				$this->tpl->atribui("str_status",$str_status);
				$this->tpl->atribui("infoserver",$infoserver);
				$this->tpl->atribui("tipo_bandalarga",@$tipo_bandalarga['tipo_bandalarga']);	
				
				switch($tipo_conta) {
				
					case 'D':
						// Consulta específica de discado
						
						break;

					case 'BL':
						// Consulta específica de banda larga
						
						$nas = $this->obtemNas($conta["id_nas"]);
						$pop = $this->obtemPop($conta["id_pop"]);
						$this->tpl->atribui("nas",$nas);
						$this->tpl->atribui("pop",$pop);
						

						//////////echo $nas;
						///echo "AI AI AI";
						//////////echo $pop;

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
		
		}else if ($op=="segunda_via_boleto"){
		
		  $id_carne = @$_REQUEST["id_carne"];
		  $faturas = array();

			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$id_cliente = @$_REQUEST["id_cliente"];
			$data = @$_REQUEST["data"];
			
			$forma_pagamento = "PRE";
		  
		  if( !$id_carne ) {
				// Se não tiver o id_carne é pq é pra exibir uma única fatura.
				
		  	
		  	$fatura_html = $this->boleto($id_cliente_produto,$data,$id_cliente,$forma_pagamento,true);   
		  	
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
					 $fatura_html = $this->boleto($fat[$i]["id_cliente_produto"],$fat[$i]["data"],$id_cliente,$forma_pagamento,true);
					 
					 $pagebrake=false;

					 // blablabla do pagebrake
					 if( $i>0 && ($i+1) != count($fat) && ($i+1) % 1 == 0 ) {
						$pagebrake = true;
					 }

					 $faturas[] = array( "fatura_html" => $fatura_html,
															 "pagebreak" => $pagebrake );

				}// for
				
			}
			//$preferencia = $this->prefs("total");
			$sSQL = "SELECT * FROM pftb_preferencia_cobranca WHERE id_provedor = 1";
			$pref = $this->bd->obtemUnicoRegistro($sSQL);
			
			$banco = $pref["cod_banco_boleto"];

			
			
			$this->tpl->atribui("faturas",$faturas);
			$this->arquivoTemplate = "boleto_segunda_via.html";		
		
		
		} else if ($op =="altera_contrato"){
						
				if( ! $this->privPodeGravar("_CLIENTES") ) {
						$this->privMSG();
						return;
				}
						
		
				$sSQL  = "SELECT ";
				$sSQL .= "	username, tipo_conta, id_conta ";
				$sSQL .= "FROM ";
				$sSQL .= "	cntb_conta ";
				$sSQL .= "WHERE ";
				$sSQL .= "	id_conta = '". @$_REQUEST["$id_conta"] ."'";
				
		} else if ($op == "excluir_cliente"){
				if( ! $this->privPodeGravar("_ELIMINAR_CLIENTE") ) {
						$this->privMSG();
						return;
				}
		
		
		
		
		
		// CHAMADA PARA EXCLUSÃO DE CLIENTES
		
			$rotina = @$_REQUEST["rotina"];
			//////////echo "rotina = $rotina <br>";
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
					$sSQL .= "   ativo, obs, excluido, info_cobranca  ";
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
					//////////echo $sSQL ."<hr>\n";


					$produtos = $this->bd->obtemRegistros($sSQL);

					for($i=0;$i<count($produtos);$i++) {

						$id_cp = $produtos[$i]["id_cliente_produto"];

						$sSQL  = "SELECT ";
						$sSQL .= "	username, dominio, tipo_conta, id_conta ";
						$sSQL .= "FROM ";
						$sSQL .= "	cntb_conta ";
						$sSQL .= "WHERE ";
						$sSQL .= "	id_cliente_produto = '$id_cp'";

						//////////echo $sSQL ."<hr>\n";

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
			//$prod_contr = $this->obtemPR($id_cliente);
			//$this->tpl->atribui("prod_contr",$prod_contr);

			$sSQL = "SELECT * FROM cbtb_contrato WHERE id_cliente_produto = '$id_cliente_produto'";
			$contr = $this->bd->obtemUnicoRegistro($sSQL);

			$data_contratacao = $contr["data_contratacao"];

			//$arqPDF = $this->contratoPDF($id_cliente_produto,$data_contratacao);

			$sSQL = "SELECT path_contrato FROM pftb_preferencia_cobranca WHERE id_provedor = '1'";
			$_path = $this->bd->obtemUnicoRegistro($sSQL);
			$path = $_path["path_contrato"];
			$host = "dev.mosman.com.br";

			//////////echo "path_contratos: $sSQL <br>";
			//////////echo "path: $path <br>";
			//contrato-418-2006-05-10.html

			$base_nome = "contrato-".$id_cliente_produto."-".$data_contratacao;
			$nome_arq = $path.$base_nome.".html";
			$arq_mostra = $path."/".$base_nome.".pdf";
			$arq = $base_nome.".html";
			
			//echo $nome_arq ."<br>";
			
			if ($rotina == "pdf"){

				//////////echo "nome arquivo: $nome_arq <br>";	

				$p = new MHTML2PDF();
				$p->setDebug(0);
				$arqPDF = $p->converte($nome_arq,$host,$path);
				copy($arqPDF,$path.$base_nome.".pdf");
				//copy($arqPDF,"/home/hugo".$base_nome.".pdf");

				if (!$arqPDF){

					////////echo "papocou esta bosta";
					////////echo "path_contratos: $sSQL <br>";
					////////echo "path: $path <br>";

				}else{


				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment; filename="'.$base_nome.'.pdf"');
				readfile($arqPDF);

				}
				
			}else{
			
			//////////echo $arqPDF;
			//////////echo "BOSTA";
			
			//$this->arquivoTemplate = "home.html";
			$contr = fopen($nome_arq, "r");
			echo fread($contr,filesize($nome_arq));
			fclose($contr);
			//$this->tpl->atribui("arquivo_contrato",$arquivo_contrato);
			//$this->arquivoTemplate = $nome_arq;
			}
		
		}else if ($op == "teste"){
		
			$this->testePDF();
			
		}else if ($op == "excluir_email"){
		
		$username = @$_REQUEST["username"];
		$dominio = @$_REQUEST["dominio"];
		$tipo_conta = @$_REQUEST["tipo_conta"];
		
			$sSQL  = "UPDATE ";
			$sSQL .= "	cntb_conta ";
			$sSQL .= "SET ";
			$sSQL .= "	status = 'C' ";
			$sSQL .= "WHERE ";
			$sSQL .= "	username = '$username' AND dominio = '$dominio' AND tipo_conta = '$tipo_conta'";

		$this->bd->consulta($sSQL);
		
		$this->tpl->atribui("mensagem","Email Excluido!");
		$this->tpl->atribui("url","clientes.php?op=pesquisa");
		$this->tpl->atribui("target","_top");


		$this->arquivoTemplate = "msgredirect.html";
		
		}else if ($op == "historico"){
		
		
			$username = @$_REQUEST['username'];
			$id_cliente_produto = @$_REQUEST['id_cliente_produto'];
			$dominio = @$_REQUEST['dominio'];
			$tipo_conta = @$_REQUEST['tipo_conta'];
		
			$sSQL  = " SELECT  l.id_cliente_produto ,l.username, l.dominio, l.tipo_conta, l.data_hora, l.id_admin, ";
			$sSQL .= " l.ip_admin, l.operacao, l.cod_operacao, a.admin    " ;       
			$sSQL .= " FROM lgtb_status_conta l, adtb_admin a ";
			$sSQL .= " WHERE l.id_admin = a.id_admin ";
			$sSQL .= " AND username = '$username' AND tipo_conta = '$tipo_conta' AND dominio = '$dominio' ";
			$sSQL .= " AND id_cliente_produto = '$id_cliente_produto' GROUP BY data_hora ,admin , l.id_admin, ip_admin ,cod_operacao , operacao, username, id_cliente_produto, dominio, tipo_conta ORDER BY data_hora, username, operacao, l.id_admin " ;
			
			$rel = $this->bd->obtemRegistros($sSQL) ;
			
			echo "			
				    <table border='0' cellspacing='1' style='border: 1px solid #FCFCFC;' width='430'>
					  <tr>
						<td style='border: 1px solid #DCDCDC;' bgcolor='#F0F0F0' width='125' align='center'><font face='verdana' size='1' color='#85B79E'><b> data/hora</b></font></td>
						<td style='border: 1px solid #DCDCDC;' bgcolor='#F0F0F0' width='248'align='center'><font face='verdana' size='1' color='#85B79E'><b>operacao</b></font></td>
						<td style='border: 1px solid #DCDCDC;' bgcolor='#F0F0F0'align='center'><font face='verdana' size='1' color='#85B79E'><b>admin</b></font></td>
					  </tr>
				 ";
				
			
			for ($i=0; $i<count($rel); $i++){
			
				$operacao = trim($rel[$i]['operacao']);
				$tipo_conta = $rel[$i]['tipo_conta'];
				$data_hora = $rel[$i]['data_hora'];
				$cod_operacao = $rel[$i]['cod_operacao'];
				$admin = $rel[$i]['admin'];
				$ip_admin = trim($rel[$i]['ip_admin']);
					list($data, $hora) = explode (" ",$data_hora);
					list($ano, $mes, $dia) = explode("-",$data);
					list($_hora, $resto) = explode(".",$hora);
				
				$mk_operacao = "";
				
				
				if ($cod_operacao=="1"){$mk_operacao = 'Conta Criada';}				
				if ($cod_operacao=="2"){$mk_operacao = 'Senha Alterada';}
				if ($cod_operacao=="3"){$mk_operacao = 'Alteração de status (ativo)';}
				if ($cod_operacao=="4"){$mk_operacao = 'Alteração de status (bloqueado)';}
				if ($cod_operacao=="5"){$mk_operacao = 'Alteração de status (suspenso)';}
				if ($cod_operacao=="6"){$mk_operacao = 'Telefone Alterado';}
				if ($cod_operacao=="7"){$mk_operacao = 'NAS Alterado';}
				if ($cod_operacao=='8'){								
					list($nome, $banda_origem, $banda_alt) = explode("-",$operacao);
					$mk_operacao = "Banda Alterada de" . $banda_origem . " para " . $banda_alt;
				}
				if ($cod_operacao=="9"){$mk_operacao = 'MAC Alterado';}
				if ($cod_operacao=="10"){$mk_operacao = 'Alteração da conta(normal)';}
				if ($cod_operacao=="11"){$mk_operacao = 'Alteração da conta(mestre)';}
				if ($cod_operacao=="12"){$mk_operacao = 'Alteração de POP';}
				
				
				echo "<tr>
						<td style='border: 1px solid #DCDCDC;' bgcolor='#FFFFFF' width='125'><font face='verdana' size='1'>" . $dia . "/" . $mes . "/" . $ano . "&nbsp;" .$_hora. "</font></td>
						<td style='border: 1px solid #DCDCDC;' bgcolor='#FFFFFF' width='251'><font face='verdana' size='1'>" . $mk_operacao . "</font></td>
						<td style='border: 1px solid #DCDCDC;' bgcolor='#FFFFFF'><font face='verdana' size='1' >" . $admin . "</font></td>
				 	  </tr>";
				
			}
			
			echo "	  <tr>
						<td colspan='3' align='right'><font size='1' face='verdana' color='#85B79E'>[<a href='javascript:;' onClick='Fecha_hist();'><font size='1' face='verdana' color='#85B79E'>fechar</font></a>]</font></td>
				  	  </tr>
				  	</table>";
			return;
		}
		
}// fecha processa
	
	
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
	//////echo $zSQL;
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
				////////echo "<hr>\n";
				////////echo "ID_CONTA: " . $contas[$i]["id_conta"] . "<br>\n";
				////////echo "USERNAME: " . $contas[$i]["username"] . "<br>\n";
				////////echo "DOMINIO: " . $contas[$i]["dominio"] . "<br>\n";
				////////echo "ID_NAS: " . $info["id_nas"] . "<br>\n";
				////////echo "REDE: " . $info["rede"] . "<br>\n";
				////////echo "IP: " . $info["ipaddr"] . "<br>\n";
				////////echo "IP NAS: " . $nas["ip"] . "<br>\n";
				////////echo "<hr>";
				
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
			
			//////////echo "$sSQL<hr>";

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
		//////////echo "$sSQL<hr>";
		
		$sSQL  = "DELETE FROM ";
		$sSQL .= "cbtb_cliente_produto ";
		$sSQL .= "WHERE ";
		$sSQL .= "id_cliente_produto = '$id_cliente_produto' ";
		//////////echo "$sSQL<hr>";

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
		
		//////////echo "$sSQL<hr>";
		
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
	
	//////////echo "SQL: $sSQL ";
	
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


	////////echo "DATA ENVIADA: $data <BR>\n";

	
	$sSQL  = "SELECT cl.nome_razao, cl.endereco, cl.complemento, cl.id_cidade, cl.estado, cl.cep, cl.cpf_cnpj,cl.bairro, cd.cidade as nome_cidade, cd.id_cidade  ";
	$sSQL .= "FROM ";
	$sSQL .= "cltb_cliente cl, cftb_cidade cd ";
	$sSQL .= "WHERE ";
	$sSQL .= "cl.id_cliente = '$id_cliente' AND ";
	$sSQL .= "cd.id_cidade = cl.id_cidade";

	$cliente = $this->bd->obtemUnicoRegistro($sSQL);
	////////echo "CLIENTE: $sSQL  <br>";
	
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
	
	////////echo "fatura: $sSQL<br>";
	
	//$data_cadastrada = $fatura["data"];
	////////echo "DATA: $data_cadastrada <br>";
	////////echo "SHIT: " . $fatura["data"] . "<br>\n";
	
	list ($dia,$mes,$ano) = explode("/",$fatura["data"]);
	
	
	$mes_array = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
	
	if ($forma_pagamento == "PRE"){
	
		$referente = $mes_array[(int)$mes-1]."/".$ano;
	
	}else if ($forma_pagamento == "POS"){
	
		//$mes_ref = mktime(0, 0, 0, $mes-1);
		////////echo "MES: $mes <br>\n";
		////////echo "MES REF: $mes_ref <br>\n";
		$referente = $mes_array[(int)$mes-1]."/".$ano;
	
	}
	


	// PEGANDO INFORMAÇÕES DAS PREFERENCIAS
	$provedor = $this->prefs->obtem("total");
	//$provedor = $this->prefs->obtem();

	$sSQL = "SELECT ct.id_produto, pd.nome from cbtb_contrato ct, prtb_produto pd WHERE ct.id_cliente_produto = '$id_cliente_produto' and ct.id_produto = pd.id_produto";
	$produto = $this->bd->obtemUnicoRegistro($sSQL);
	//////////echo "PRODUTO: $sSQL <br>";

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
	//////////echo $codigo;
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
	
   	
	////////echo "FATURA: $sSQL <br>";
	
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
		//////echo "TECNICO: $sSQL <br>";
		
		$this->tpl->atribui("tec",$tecnico);
	
	
	}

	////////echo "SQL: $sSQL <br>";

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
	$sSQL .= "  cl.agencia, cl.dia_pagamento, cl.ativo, cl.obs, cl.provedor, cl.excluido, cl.info_cobranca, ";
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
	
	//////////echo "path: $_path - $path<br>";

	//$arq = explode("/",$arquivo_contrato);
	//$arq = $arq[count($arq)-1];
	$arq = $arquivo_contrato;

	//$image_path = $path."/template/default/images";
	//////////echo "<BR>IMAGE PATH".$image_path ."<br>";

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
	
	//////////echo "path_contratos: $sSQL <br>";
	//////////echo "path: $path <br>";
		
	$nome_arq = "contrato-".$id_cliente_produto."-".$data_contratacao.".html";
	//////////echo "nome arquivo: $nome_arq <br>";	

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
				
				////////echo $nome_arq."<br>";
				////////echo $base_nome."<br>";
				////////echo $host."<br>";
				////////echo $path."<br>";
				

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

public function boleto($id_cliente_produto,$data,$id_cliente,$forma_pagamento,$segunda_via=false){


	////////echo "DATA ENVIADA: $data <BR>\n";

	
	$sSQL  = "SELECT cl.nome_razao, cl.endereco, cl.complemento, cl.id_cidade, cl.estado, cl.cep, cl.cpf_cnpj,cl.bairro, cd.cidade as nome_cidade, cd.id_cidade  ";
	$sSQL .= "FROM ";
	$sSQL .= "cltb_cliente cl, cftb_cidade cd ";
	$sSQL .= "WHERE ";
	$sSQL .= "cl.id_cliente = '$id_cliente' AND ";
	$sSQL .= "cd.id_cidade = cl.id_cidade";

	$cliente = $this->bd->obtemUnicoRegistro($sSQL);
	////////echo "CLIENTE: $sSQL  <br>";
	
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
	
	////////echo "fatura: $sSQL<br>";
	
	//$data_cadastrada = $fatura["data"];
	////////echo "DATA: $data_cadastrada <br>";
	////////echo "SHIT: " . $fatura["data"] . "<br>\n";
	
	list ($dia,$mes,$ano) = explode("/",$fatura["data"]);
	
	
	$mes_array = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
	
	if ($forma_pagamento == "PRE"){
	
		$referente = $mes_array[(int)$mes-1]."/".$ano;
	
	}else if ($forma_pagamento == "POS"){
	
		//$mes_ref = mktime(0, 0, 0, $mes-1);
		////////echo "MES: $mes <br>\n";
		////////echo "MES REF: $mes_ref <br>\n";
		$referente = $mes_array[(int)$mes-1]."/".$ano;
	
	}
	
	//echo "referente: $referente<br>";

	// PEGANDO INFORMAÇÕES DAS PREFERENCIAS
	$provedor = $this->prefs->obtem("total");
	//$provedor = $this->prefs->obtem();

	$sSQL = "SELECT ct.id_produto, pd.nome from cbtb_contrato ct, prtb_produto pd WHERE ct.id_cliente_produto = '$id_cliente_produto' and ct.id_produto = pd.id_produto";
	$produto = $this->bd->obtemUnicoRegistro($sSQL);
	//////////echo "PRODUTO: $sSQL <br>";

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
	//$vencimento = $ano.$mes.$dia;
	//////////echo $codigo;
	$valor = $fatura["valor"];
	$id_cobranca = $fatura["id_cobranca"];
	$nome_cliente = $cliente["nome_razao"];
	$cpf_cliente = $cliente["cpf_cnpj"];
	$id_empresa = $provedor["cnpj"];
	//$nosso_numero = 1;
	$nome_cedente = $provedor["nome"];
	$cendereco = $provedor['endereco'];
	$clocalidade = $provedor['localidade'];
	$observacoes = $provedor['observacoes'];
	$nome_produto = $produto["nome"];
	$complemento = $cliente["complemento"];
	$hoje = Date("d/m/Y");
	$empresa_cnpj = $this->prefs->obtem("provedor","cnpj");
	
	$banco   	= $this->prefs->obtem("cobranca","cod_banco_boleto");
	$agencia 	= $this->prefs->obtem("cobranca","agencia_boleto");		// Sem o DV
	$conta   	= $this->prefs->obtem("cobranca","conta_boleto");		// Sem o DV
	$carteira	= $this->prefs->obtem("cobranca","carteira_boleto");
	$convenio   = $this->prefs->obtem("cobranca","convenio_boleto");
	$vencimento = $fatura["data"];
	//$vencimento = '18/08/2006';	// Formato brasileiro
	//$valor		= '10,00';		// Tanto faz ponto ou virgula
	$id = $nosso_numero;///			= '9999999999';
	$preto = "template/boletos/imagens/preto.gif";
	$branco = "template/boletos/imagens/branco.gif";
	
	
	//$informacoes = $provedor["observacoes"];

  if( $segunda_via ) {

     $hoje = $fatura["data"];
     $codigo_barras = $fatura["cod_barra"];
     $linha_digitavel = $fatura["linha_digitavel"];
     
     $cb = MBoleto::htmlBarCode($codigo_barras,$preto,$branco);
     
  } else {
  
  
  	/*echo "BANCO: $banco<br>";
  	echo "AGENCIA: $agencia<br>";
  	echo "CONTA: $conta<br>";
  	echo "CARTEIRA: $carteira<br>";
  	echo "CONVENIO: $convenio<br>";
  	echo "VENCIMENTO: $vencimento<br>";
  	echo "VALOR: $valor<br>";
  	ECHO "ID: $id<br>";*/
		$blt = new MBoleto($banco,$agencia,$conta,$carteira,$convenio,$vencimento,$valor,$id);
	
		$linha_digitavel = $blt->obtemLinhaDigitavel();  
  	$codigo_barras = $blt->obtemCodigoBoleto();
  	$cb = MBoleto::htmlBarCode($blt->obtemCodigoBoleto(),$preto,$branco);
  	//$cb = $blt->htmlBarCode($blt->obtemCodigoBoleto(),"preto.gif","branco.gif");
  	
  	
  	
  	
  /*
   	$codigo_barras = MArrecadacao::codigoBarrasPagContas($valor,$id_empresa,$nosso_numero,$vencimento);
   	$hoje = date("d/m/Y");
   	$linha_digitavel = MArrecadacao::linhaDigitavel($codigo_barras);
	*/
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
	
   	
	////////echo "FATURA: $sSQL <br>";
	
	$target = "/mosman/virtex/dados/carnes/codigos";
	//$target = "carnes/codigos";
	
	//$boleto->obtemCodigoBoleto();
	
	//MArrecadacao::barCode($codigo_barras,"$target/$codigo_barras.png");
		
	//	$codigo = MArrecadacao::pagConta(...);
		
	//copy ("/mosman/virtex/dados/carnes/codigos/".$codigo_barras.".png","/home/hugo/public_html/virtex/codigos/".$codigo_barras.".png");
		

	//$barra = MArrecadacao::barCode($codigo_barras);
	
	$ph = new MUtils;
	
	$_path = MUtils::getPwd();
	
	
	$images = $_path."/template/boletos/imagens";	
	
	$this->tpl->atribui("codigo_barras",$codigo_barras);
	$this->tpl->atribui("cod_barra",$cb);
	$this->tpl->atribui("agencia",$agencia);
	$this->tpl->atribui("conta",$conta);
	$this->tpl->atribui("carteira",$carteira);
	$this->tpl->atribui("empresa_cnpj",$empresa_cnpj);

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
	$this->tpl->atribui("banco",$banco);
	//$this->tpl->atribui("barra",$barra);
	
	//return($carne_emitido);
	
	if ( $segunda_via == true ){
	
	
		$this->tpl->atribui("imprimir",true);
		$estilo = $this->tpl->obtemPagina("../boletos/pc-estilo.html");
		$fatura = $this->tpl->obtemPagina("../boletos/layout_bb.html");
		
		return($estilo.$fatura);
	
	}else{
	
		$fatura = $this->tpl->obtemPagina("../boletos/layout_bb.html");
		return($fatura);
	
	}
	
	






}
	
	

}

?>
