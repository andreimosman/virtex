<?

class VAInterface_cliente_home extends VirtexAdminWeb {
	
	public function __construct() {
		parent::__construct();
	}
	

	public function processa($op="") {
	
	
		$acao = @$_REQUEST["acao"];

		$user = $this->usrLogin->obtemUser();
		$id_conta = $this->usrLogin->obtemId();
		$conta_mestre = $this->usrLogin->obtemConta();
		$id_cliente = $this->usrLogin->obtemIdCliente();	

		$this->tpl->atribui("conta_mestre",$conta_mestre);

		$sSQL  = " SELECT ";
		$sSQL .= " cl.nome_razao, cc.conta_mestre, cc.status, cc.tipo_conta, cc.id_cliente, cc.username, cc.id_conta, cc.dominio, cl.info_cobranca " ;
		$sSQL .= " FROM cntb_conta cc, cltb_cliente cl ";
		$sSQL .= " WHERE status = 'A' ";
		$sSQL .= " AND cc.id_cliente = cl.id_cliente ";
		$sSQL .= " AND id_conta = '$id_conta' ";

		$this->bd->consulta($sSQL);

		$conta = $this->bd->obtemUnicoRegistro($sSQL);



		$username = $conta['username'];
		$info_cobranca = $conta['info_cobranca'];
		$conta_mestre = $conta['conta_mestre'];
		$dominio = $conta['dominio'];
		$tipo_conta = $conta['tipo_conta'];
		///echo $info_cobranca;

		$this->tpl->atribui("tipo_conta_fix",$tipo_conta);
		$this->tpl->atribui("conta_mestre",$conta_mestre);
		$this->tpl->atribui("info_cobranca",$info_cobranca);

		$this->tpl->atribui("username",$username);
		$this->tpl->atribui("username_conf",$username);
		$this->tpl->atribui("dominio",$dominio);	
		$this->tpl->atribui("id_conta",$id_conta);
		$this->tpl->atribui("conta",$conta);

		$this->arquivoTemplate = "interface_home.html";

		if ($op == "dados") {

			$dados = "true";	

			$username = @$_REQUEST["username"];
			$tipo_conta = @$_REQUEST["tipo_conta"];
			$dominio = @$_REQUEST["dominio"];

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
					
					$discado = $this->bd->obtemUnicoRegistro($sSQL);
					$this->tpl->atribui("discado",$discado);
					
					/////////echo $sSQL;
				
				break;
				
				
				case 'BL':
					$sSQL  = "SELECT ";
					$sSQL .= "   cbl.id_pop, cbl.tipo_bandalarga, cbl.ipaddr, cbl.rede, cbl.id_nas, cbl.mac, cbl.upload_kbps, cbl.download_kbps, cbl.ip_externo, cbl.username, cbl.dominio "; // alterei
					$sSQL .= "";
					$sSQL .= "FROM ";
					$sSQL .= "   cntb_conta_bandalarga cbl ";
					$sSQL .= "";
					$sSQL .= "WHERE ";
					$sSQL .= "   cbl.username = '".$this->bd->escape($username)."' ";
					$sSQL .= "   AND cbl.dominio = '".$this->bd->escape($dominio)."' ";
					$sSQL .= "   AND cbl.tipo_conta = '".$this->bd->escape($tipo_conta)."' ";
					$sSQL .= "";

					$bandalarga = $this->bd->obtemUnicoRegistro($sSQL);
					$this->tpl->atribui("bandalarga",$bandalarga);
					
					////////echo $sSQL;
				
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

					$email = $this->bd->obtemUnicoRegistro($sSQL);
					$this->tpl->atribui("email",$email);
					
					/////////echo $sSQL;

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
					
					$hosp = $this->bd->obtemUnicoRegistro($sSQL);
					$this->tpl->atribui("hosp",$hosp);
				
				////////echo $sSQL;

				break;
				
				
			}		
				
			$this->tpl->atribui("tipo_conta",$tipo_conta);
			$this->tpl->atribui("username",$username);
			$this->tpl->atribui("dominio",$dominio);	



			$this->tpl->atribui("dados",$dados);

			$this->arquivoTemplate = "interface_home.html";

	
		} else if ($op == "home") {
		
			$this->arquivoTemplate = "interface_home.html";

		} else if ($op == "alterar_senha"){

			$id_conta2 = @$_REQUEST["id_conta"];
			$atual = @$_REQUEST["atual"];

			if (!$acao && !$id_conta2){
				$username = $_REQUEST["username"];
				$dominio = @$_REQUEST ["dominio"];
				$tipo_conta = @$_REQUEST ["tipo_conta"] ;
			}
		
		
		
			$alterar_senhas = "true";
			$msg = "Senha Alterada com sucesso!";
			$url ="index_home.php?op=home";

			if ($acao == "alterar" ){

			   if ($atual){
			
					$aSQL  = "SELECT senha FROM cntb_conta WHERE username= '" . $this->bd->escape(@$_REQUEST["username"]) . "' AND dominio = '" . $this->bd->escape(@$_REQUEST["dominio"]) . "' AND tipo_conta = '" . $this->bd->escape(@$_REQUEST["tipo_conta"]) . "' " ;

					$senha_conta = $this->bd->obtemUnicoRegistro($aSQL);
					$senha_atual = $senha_conta['senha'];


					if ($senha_atual == $this->bd->escape(@$_REQUEST["senha_atual"])){
						$msg = "Senha Alterada com sucesso!";
						$url ="index_home.php?op=home";
					}else{
						$msg = "Erro ao processar, as senhas não conferem.Tente Novamente";
						$url ="javascript:history.back();";
					}
				}
		
				$sSQL  = " UPDATE ";
				$sSQL .= " cntb_conta ";
				$sSQL .= " SET ";
				$sSQL .= " senha_cript = '" . $this->criptSenha($this->bd->escape(@$_REQUEST["senha"]))  . "', ";
				$sSQL .= " senha = '" . $this->bd->escape(@$_REQUEST["senha"])  . "' ";
				$sSQL .= " WHERE ";
				$sSQL .= " '" . $this->bd->escape(@$_REQUEST["senha"]) . "' = '" . $this->bd->escape(@$_REQUEST["senha_conf"]) . "' ";
				$sSQL .= " AND username= '" . $this->bd->escape(@$_REQUEST["username"]) . "' ";
				$sSQL .= " AND dominio = '" . $this->bd->escape(@$_REQUEST["dominio"]) . "' ";
				$sSQL .= " AND tipo_conta = '" . $this->bd->escape(@$_REQUEST["tipo_conta"]) . "' ";

				if ($atual){
					$sSQL .= " AND senha = '" . $this->bd->escape(@$_REQUEST["senha_atual"]) . "' ";
				}


				$this->bd->consulta($sSQL);

				$this->tpl->atribui("mensagem",$msg); 
				$this->tpl->atribui("url",$url);
				$this->tpl->atribui("target","_top");

				$this->arquivoTemplate = "interface_msgredirect.html";

				$alterar_senhas = 0 ;
				return;
		
			}
		

			$this->tpl->atribui("tipo_conta",$tipo_conta);
			$this->tpl->atribui("username",$username);
			$this->tpl->atribui("dominio",$dominio);
			$this->tpl->atribui("alterar_senhas",$alterar_senhas);
			$this->arquivoTemplate = "interface_home.html";
		
		} else if ($op == "contas") {

			$sSQL  = " SELECT ";
			$sSQL .= " cc.username, pp.nome, cc.id_cliente, cc.dominio, cc.id_conta , cc.tipo_conta , cc.id_cliente_produto ";
			$sSQL .= " FROM cntb_conta cc, cltb_cliente clc, prtb_produto pp, cbtb_cliente_produto prp  ";
			$sSQL .= " WHERE ";
			$sSQL .= " clc.id_cliente = prp.id_cliente ";
			$sSQL .= " AND cc.id_cliente = '$id_cliente' ";
			$sSQL .= " AND cc.id_cliente_produto = prp.id_cliente_produto ";
			$sSQL .= " AND pp.id_produto = prp.id_produto ";
			$sSQL .= " AND cc.status = 'A' ";
			$sSQL .= "  GROUP BY tipo_conta, username, cc.dominio, nome, cc.id_cliente, cc.id_conta, cc.id_cliente_produto ORDER BY tipo_conta DESC, username ASC, nome ASC "; 

			////////////////////////echo "$sSQL;	<br>";


			$contas = $this->bd->obtemRegistros($sSQL);

			$this->tpl->atribui("contas",$contas);
			$this->arquivoTemplate = "interface_home.html";
			
		} else if ($op == "cobranca" ) {
			$cobranca = true;

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

				//////////////////////////echo $dSQL ."<hr>\n";

				$contas = $this->bd->obtemRegistros($dSQL);

				$lista_contrato[$i]["conta"] = $contas;

			}	


			$this->tpl->atribui("id_cliente", $id_cliente);
			$this->tpl->atribui("cobranca",$cobranca);
			$this->tpl->atribui("lista_contrato",$lista_contrato);

		} else if ($op == "criar_email") {
			$acao = @$_REQUEST["acao"];
			$criar_email = true;
	  
			$sSQL  = "SELECT ";
			$sSQL .= "   cp.id_cliente_produto, p.nome as produto, p.num_emails, conta.num_emails as emails_cliente, p.id_produto ";
			$sSQL .= "FROM ";
			$sSQL .= "   cbtb_cliente_produto cp, prtb_produto p, ";
			$sSQL .= "	(SELECT ";
			$sSQL .= "	   cp.id_cliente_produto, count(cnt.username) as num_emails ";
			$sSQL .= "	FROM ";
			$sSQL .= "	   cbtb_cliente_produto cp LEFT OUTER JOIN cntb_conta cnt USING(id_cliente_produto) ";
			$sSQL .= "	WHERE ";
			$sSQL .= "	   cp.id_cliente = '$id_cliente' ";
			$sSQL .= "	   AND tipo_conta = 'E' ";
			$sSQL .= "	   AND status = 'A' ";
			$sSQL .= "	GROUP BY ";
			$sSQL .= "	   cp.id_cliente_produto ";
			$sSQL .= "	) conta ";
			$sSQL .= "WHERE ";
			$sSQL .= "   p.id_produto = cp.id_produto ";
			$sSQL .= "   AND cp.id_cliente_produto = conta.id_cliente_produto ";
			$sSQL .= "   AND p.num_emails > conta.num_emails ";
		
			$num_email = $this->bd->obtemRegistros($sSQL);

			for ($i=0;$i<count($num_email);$i++){

				$id_cliente_produto = $num_email[$i]["id_cliente_produto"];

				$dSQL  = "SELECT ";
				$dSQL .= " username ";
				$dSQL .= " FROM cntb_conta ";
				$dSQL .= " WHERE id_cliente_produto = '$id_cliente_produto' ";
				$dSQL .= " AND conta_mestre = true ";
				$dSQL .= " AND status = 'A' ";
				$dSQL .= " AND tipo_conta <> 'E' ";
				$user = $this->bd->obtemRegistros($dSQL);

				$num_email[$i]["user"] = $user;

			}
			
			if ($acao == "ficha") {

			
				//SELECT PARA POPULAR O DROP DE DOMINIO
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
					
		

				$produto_conta = @$_REQUEST["produto_conta"];			

				@list($produto_conta,$total_emails) = explode("/",$produto_conta);


				$mostrar = true;

				$aSQL  = " SELECT ";
				$aSQL .= " p.nome, cp.id_cliente_produto ";
				$aSQL .= " FROM prtb_produto p, cbtb_cliente_produto cp ";
				$aSQL .= " WHERE p.id_produto='$produto_conta' ";
				$aSQL .= " AND cp.id_cliente = '$id_cliente' ";
				$aSQL .= " AND cp.id_produto = p.id_produto ";

				/////////////////echo $aSQL ;

				$nome_produto = $this->bd->obtemUnicoRegistro($aSQL);

				$nome = strtoupper($nome_produto["nome"]);

				$id_cliente_produto_slctd = $nome_produto["id_cliente_produto"];

				$this->tpl->atribui("mostrar",$mostrar);			
				$this->tpl->atribui("id_cliente",$id_cliente);
				$this->tpl->atribui("total_emails",$total_emails);
				$this->tpl->atribui("id_cliente_produto",$id_cliente_produto_slctd);
				$this->tpl->atribui("nome",$nome);			
			} else if ($acao == "papocar") {
			
				$id_cliente_produto = @$_REQUEST["id_cliente_produto"];

				$sSQL  = "SELECT ";
				$sSQL .= "   username ";
				$sSQL .= "FROM ";
				$sSQL .= "   cntb_conta ";
				$sSQL .= "WHERE ";
				$sSQL .= "   username = '".@$_REQUEST["username"]."' ";
				$sSQL .= "   and tipo_conta = 'E' ";
				$sSQL .= "   and dominio = '".@$_REQUEST["dominio"]."' ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   username ";

				$lista_user = $this->bd->obtemUnicoRegistro($sSQL);

				if(count($lista_user) && $lista_user["username"]){
					// ver como processar
					$msg = "Já existe outra conta cadastrada com esse username!";
					$url = "index_home.php?op=criar_email ";
					$criar_email = true;
					$this->tpl->atribui("criar_email",$criar_email);

				} else {
					$id_conta = $this->bd->proximoID("cnsq_id_conta");

					$sSQL  = "INSERT INTO ";
					$sSQL .= "   cntb_conta( ";
					$sSQL .= "      id_conta, username, dominio, tipo_conta, senha, id_cliente, id_cliente_produto, senha_cript,conta_mestre, status) ";
					$sSQL .= "   VALUES (";
					$sSQL .= "			'". $id_conta. "', ";
					$sSQL .= "     '" . $this->bd->escape(strtolower(@$_REQUEST["username"])) . "', ";
					$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["dominio"]) . "', ";
					$sSQL .= "     'E', ";
					$sSQL .= "     '" . $this->bd->escape(trim(@$_REQUEST["senha"])) . "', "; 						
					$sSQL .= "     '" .  $this->bd->escape(trim(@$_REQUEST["id_cliente"])) . "', "; 						
					$sSQL .= "     '" .	$id_cliente_produto . "', ";
					$sSQL .= "     '" . $this->criptSenha($this->bd->escape(trim(@$_REQUEST["senha"]))) . "', ";
					$sSQL .= "     false, ";
					$sSQL .= "     'A' )";	
					
						$this->bd->consulta($sSQL);

					///echo $sSQL ;
					
					$dSQL  = "INSERT INTO ";
					$dSQL .= "	cntb_conta_email( ";
					$dSQL .= "		username, tipo_conta, dominio, quota, email) ";
					$dSQL .= "VALUES (";
					$dSQL .= "     '" . @$_REQUEST["username"] . "', ";
					$dSQL .= "     'E', ";
					$dSQL .= "     '" . $this->bd->escape(@$_REQUEST["dominio"]) . "', ";
					$dSQL .= "     '".(int)@$_REQUEST["quota"]."', ";
					$dSQL .= "     '". @$_REQUEST["username"]."@". @$_REQUEST["dominio"]."' ";
					$dSQL .= " )";

						$this->bd->consulta($dSQL);
					//////echo "E-MAIL: $dSQL <br>";
					
					$server = $this->prefs->obtem("geral","mail_server");
													
					$this->spool->adicionarEmail($server, $id_conta, $this->bd->escape(@$_REQUEST["username"]), $this->bd->escape(@$_REQUEST["dominio"]));
					
					
					$msg = "E-mail adicionado com sucesso! ";
					$url = "index_home.php" ;
				
				}
				
				$this->tpl->atribui("mensagem",$msg); 
				$this->tpl->atribui("url",$url);
				$this->tpl->atribui("target","_top");
				$this->arquivoTemplate = "interface_msgredirect.html";
				return;

			
			}
			
			$this->tpl->atribui("num_email",$num_email);
			$this->tpl->atribui("criar_email",$criar_email);
	
		} else if ($op == "faturas") {
	
			$faturas_segunda_via = true;
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];

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
			$sSQL .= " AND f.reagendamento is null AND f.valor_pago = '0.00' ";
			$sSQL .= " AND f.id_cliente_produto = '$id_cliente_produto' ";
			$sSQL .= " AND f.status != 'E' AND f.status != 'C' ";
			$sSQL .= "ORDER BY f.data ASC ";

			$lista_faturas = $this->bd->obtemRegistros($sSQL);

			$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
			$cliente = $this->bd->obtemUnicoRegistro($sSQL);

			$this->tpl->atribui("cliente",$cliente);
			$this->tpl->atribui("id_cliente", $id_cliente);
			$this->tpl->atribui("lista_faturas",$lista_faturas);
			$this->tpl->atribui("faturas_segunda_via",$faturas_segunda_via);	
		} else if ($op == "segunda_via") {
			
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
	
				}					
			}
	
			$this->tpl->atribui("faturas",$faturas);
			$this->arquivoTemplate = "carne_segunda_via.html";
			return;

		
		} else if ($op == "imprime_contrato") {

			$rotina = @$_REQUEST["rotina"];
			$id_cliente_produto = @$_REQUEST["id_cliente_produto"];
			$id_cliente = @$_REQUEST["id_cliente"];
			///$this->obtemPR($id_cliente);

			$sSQL = "SELECT * FROM cbtb_contrato WHERE id_cliente_produto = '$id_cliente_produto'";
			$contr = $this->bd->obtemUnicoRegistro($sSQL);
			///echo $sSQL ;

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
			///////echo $arq;
				
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

				} else {

					header('Pragma: public');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment; filename="'.$base_nome.'.pdf"');
					readfile($arqPDF);

				}

			} else {

				$contr = fopen($nome_arq, "r");
				fclose($contr);
				$this->arquivoTemplate = $nome_arq;
				return;
			}
			
			return;
		}
		
		$this->arquivoTemplate = "interface_home.html";	
	
	}

	public function carne($id_cliente_produto,$data,$id_cliente,$forma_pagamento,$segunda_via=false){
		
		$sSQL  = "SELECT cl.nome_razao, cl.endereco, cl.complemento, cl.id_cidade, cl.estado, cl.cep, cl.cpf_cnpj,cl.bairro, cd.cidade as nome_cidade, cd.id_cidade  ";
		$sSQL .= "FROM ";
		$sSQL .= "cltb_cliente cl, cftb_cidade cd ";
		$sSQL .= "WHERE ";
		$sSQL .= "cl.id_cliente = '$id_cliente' AND ";
		$sSQL .= "cd.id_cidade = cl.id_cidade";
	
		$cliente = $this->bd->obtemUnicoRegistro($sSQL);
		
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
		list ($dia,$mes,$ano) = explode("/",$fatura["data"]);
		$mes_array = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
		
		if ($forma_pagamento == "PRE") {
			$referente = $mes_array[(int)$mes-1]."/".$ano;
		}else if ($forma_pagamento == "POS") {
			$referente = $mes_array[(int)$mes-1]."/".$ano;
		}

		// PEGANDO INFORMAÇÕES DAS PREFERENCIAS
		$provedor = $this->prefs->obtem("total");
	
		$sSQL = "SELECT ct.id_produto, pd.nome from cbtb_contrato ct, prtb_produto pd WHERE ct.id_cliente_produto = '$id_cliente_produto' and ct.id_produto = pd.id_produto";
		$produto = $this->bd->obtemUnicoRegistro($sSQL);
		
		if (!$segunda_via) {
		
			$sSQL = "SELECT nextval('blsq_carne_nossonumero') as nosso_numero ";
			$nn = $this->bd->obtemUnicoRegistro($sSQL);
	
			$nosso_numero = $nn['nosso_numero'];
			
		} else {
			$nosso_numero = $fatura["nosso_numero"];
		}
	
		$data_venc = $fatura["data"];
		@list($dia,$mes,$ano) = explode("/",$fatura["data"]);
		$vencimento = $ano.$mes.$dia;

		$valor = $fatura["valor"];
		$id_cobranca = $fatura["id_cobranca"];
		$nome_cliente = $cliente["nome_razao"];
		$cpf_cliente = $cliente["cpf_cnpj"];
		$id_empresa = $provedor["cnpj"];

		$nome_cedente = $provedor['nome'];
		$cendereco = $provedor['endereco'];
		$clocalidade = $provedor['localidade'];
		$observacoes = $provedor['observacoes'];
		$nome_produto = $produto["nome"];
		$complemento = $cliente["complemento"];
		
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
		
		$target = "/mosman/virtex/dados/carnes/codigos";
		MArrecadacao::barCode($codigo_barras,"$target/$codigo_barras.png");
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
		
		if ( $segunda_via == true ) {
			$this->tpl->atribui("imprimir",true);
			$estilo = $this->tpl->obtemPagina("../boletos/pc-estilo.html");
			$fatura = $this->tpl->obtemPagina("../boletos/layout-pc.html");
			return($estilo.$fatura);
		} else {
			$fatura = $this->tpl->obtemPagina("../boletos/layout-pc.html");
			return($fatura);
		}
		
		
	}
	


	public function __destruct() {
		parent::__destruct();
	}
	

}



?>
