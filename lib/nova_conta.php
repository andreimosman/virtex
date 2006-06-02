<?




	$enviando = false;
	$exibeForm = true;

	$id_cliente = @$_REQUEST["id_cliente"];

	$this->obtemPR($id_cliente);

	$sSQL  = "SELECT cp.id_produto FROM cbtb_cliente_produto cp, cntb_conta cn WHERE ";
	$sSQL .= "cn.username = '".@$_REQUEST["_username"]."' AND ";
	$sSQL .= "cn.tipo_conta = '".@$_REQUEST["_tipo_conta"]."' AND ";
	$sSQL .= "cn.dominio = '".@$_REQUEST["_dominio"]."' AND ";
	$sSQL .= "cn.id_cliente = '".@$_REQUEST["id_cliente"]."' AND ";
	$sSQL .= "cn.id_cliente_produto = cp.id_cliente_produto";
	$_produto = $this->bd->obtemRegistros($sSQL);

	//echo "_PRODUTO: $sSQL <br>";
	



	if( $acao == "cad"  ) {

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
		$sSQL .= "   and tipo_conta = '". $this->bd->escape(trim(@$_REQUEST["tipo_conta"])) ."' ";
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
			$sSQL .= "     '" . $_produto["id_produto"] . "' ";
			$sSQL .= "     )";						

			$this->bd->consulta($sSQL);  


			$username = @$_REQUEST["username"];
			$dominio = @$_REQUEST["dominio"];
			$tipo_conta = @$_REQUEST["tipo_conta"];
			$dominio_hospedagem = @$_REQUEST["dominio_hospedagem"];



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


			if ($email_igual == "1"){

				$prefs = $this->prefs->obtem("total");

				if (count($prefs)){
					$erros2 = "Já existe um usuario com este dominio neste tipo de conta cadastrado. Por favor cadastre um novo usuario";

					$this->tpl->atribui("username", $username);
					$this->tpl->atribui("dominio_hospedagem",$dominio_hospedagem);
					$this->tpl->atribui("mensagem", $erros2);
					$this->tpl->atribui("url","clientes.php?op=pesquisa");
					$this->arquivoTemplate = "msgredirect.html";
					return;
				}

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


			}


			$tipo = @$_REQUEST["tipo"];
			//PEGA CAMPOS COMUNS EM cftb_preferencias
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

					////echo "SQL DISCADO: $sSQL <br>\n";

					$this->bd->consulta($sSQL);

					$this->tpl->atribui("foneinfo",$foneinfo);

				break;	
				case 'BL':
					// PRODUTO BANDA LARGA
					$tipo_de_ip = $this->bd->escape(trim(@$_REQUEST["selecao_ip"]));
					if($tipo_de_ip == "A"){
						$nas = $this->obtemNAS($_REQUEST["id_nas"]);
						////echo "NAS: ".$nas["id_nas"]."<BR>";
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
						//echo $ip_externo["ip_externo"];

						if($nas["tipo_nas"] == "P"){

							$ipaddr = $ip_disp;

						}else if ($nas["tipo_nas"] == "I"){

							$ipaddr = $rede_disp;

						}

						$username = @$_REQUEST["username"];
						$tipo_conta = @$_REQUEST["tipo"];
						$dominio = $prefs["geral"]["dominio_padrao"];


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
						////echo "rede:". $rede_disponivel["rede"]. "<br>";


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

					////echo "IDPOP: $id_pop <br>";

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


					//echo "INSERT NA BL: $sSQL <br>";
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
						////echo "QUERY INSERÇÃO: $sSQL <BR>\n";



						//SPOOL
						////echo "Tipo: $tipo_hospedagem <br> Username: $username <br> Dominio: $dominio <br> DominioHosp: $dominio_hospedagem<br>";
						$this->spool->hospedagemAdicionaRede($server,$id_conta,$tipo_hospedagem,$username,$dominio,$dominio_hospedagem);
					//}
					break;

			}


			//require_once( PATH_LIB . "/dede.php" );						

			if ($tipo && $tipo == "BL"){

			////echo $tipo;
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
					////echo "SQL : " . $sSQL . "<br>\n";

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

			}

			if ($tipo == "BL"){

			$sSQL = "SELECT ip_externo FROM cntb_conta_bandalarga WHERE username = '".@$_REQUEST["username"]."' AND tipo_conta = 'BL' and dominio = '".$prefs["dominio_padrao"]."' ";
			$externo = $this->bd->obtemUnicoRegistro($sSQL);


			//echo "EXTERNO: $sSQL <br>";
			$this->tpl->atribui("ip_externo",$externo["ip_externo"]);

			}


			// Joga a mensagem de produto contratado com sucesso.
			$this->tpl->atribui("username",@$_REQUEST["username"]);
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



				
				
?>