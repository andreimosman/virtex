<?
	//Bloqueio e Desbloqueio de clientes atrasados.
	
	$acao = @$_REQUEST["acao"];
	$op = @$_REQUEST["op"];
	
	
	if($acao == "bloquear") {
			
			$id_bloqueio = array();
			$id_bloqueio = @$_REQUEST["id_bloqueio_box"];
			//echo "id_bloqueio: $id_bloqueio <br><br>";
			$n_bloqueio = count($id_bloqueio);
			//echo "NUMERO BLOQUEIOS: $n_bloqueio <br>";
			$tipo_bloqueio = "B";
			
			
			if ($n_bloqueio && $n_bloqueio > 0) {
			
				$admin = $this->admLogin->obtemAdmin();
				
				for ($i=1; $i<=$n_bloqueio; $i++) {
					
					$id_processo = $this->bd->proximoID("lgsq_id_processo");
					list($id_cli_produto, $tipo) = explode("-", $id_bloqueio[$i]);
					//echo "ID_CLI_PRODUTO: $id_cli_produto <br>";
					//echo "TIPO CONTA: $tipo <br>";
					
					$tipo = trim($tipo);
					
					$sSQL  = "INSERT INTO ";
					$sSQL .= "	lgtb_bloqueio_automatizado (";
					$sSQL .= "id_processo, id_cliente_produto, data_hora, tipo, admin ";
					$sSQL .= ") VALUES ( ";
					$sSQL .= "  $id_processo, $id_cli_produto, now(), '$tipo_bloqueio', '$admin' ";
					$sSQL .= ") ";
					
					//echo "QUERY INERT: $sSQL<br>\n";
					
					$this->bd->consulta($sSQL);
					
					
					if ($tipo == "BL"){

						/* SPOOL */

						$sSQL  = "SELECT ";
						$sSQL .= "	bl.username, bl.tipo_conta, bl.dominio, bl.tipo_bandalarga, bl.ipaddr, bl.rede, bl.id_nas, ";
						$sSQL .= "	cn.username, cn.dominio, cn.tipo_conta, cn.id_conta ";
						$sSQL .= "FROM cntb_conta_bandalarga bl, cntb_conta cn ";
						$sSQL .= "WHERE ";
						$sSQL .= "cn.id_cliente_produto = '".$id_cli_produto."' AND cn.tipo_conta = '$tipo' AND ";
						$sSQL .= "bl.username = cn.username AND ";
						$sSQL .= "bl.dominio = cn.dominio AND ";
						$sSQL .= "bl.tipo_conta = cn.tipo_conta ";
						//$sSQL .= "bl.username = '".$contrato["username"]."' AND bl.tipo_conta = '$tipo_produto' AND bl.dominio = '".$contrato["dominio"]."' AND ";
						//$sSQL .= "bl.username = cn.username AND bl.tipo_conta = cn.tipo_conta AND bl.dominio = cn.dominio ";
						$bl = $this->bd->obtemUnicoRegistro($sSQL);
						//echo "SPOOL BL: $sSQL <br>";

						$sSQL  = "SELECT ip FROM cftb_nas WHERE id_nas = '".$bl["id_nas"]."' ";
						$nas = $this->bd->obtemUnicoRegistro($sSQL);
						//echo "SPOOL NAS: $sSQL <br>";
						
						if ($bl["tipo_bandalarga"] == "P"){

						//ECHO "PPPOE<BR>";
							$this->spool->bandalargaExcluiRedePPPoE($nas["id_nas"],$bl["id_conta"],$bl["ipaddr"]);

						}else {

							///echo "IP <BR>";
							$this->spool->bandalargaExcluiRede($nas["id_nas"],$bl["id_conta"],$bl["rede"]);

						}

					/* FINAL SPOOL */
					
				}
					
					
					$sSQL  = "UPDATE ";
					$sSQL .= "   cntb_conta ";
					$sSQL .= "SET ";
					$sSQL .= "   status='S' ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_cliente_produto = $id_cli_produto ";
					$sSQL .= "   AND tipo_conta = '$tipo' "; /* esse bl é o tipo do produto contratado */
   					$sSQL .= "AND status = 'A' "; /* conta ativa */
   					
   					$this->bd->consulta($sSQL);
					//echo "QUERY UPDATE: $sSQL<br>\n";
	
				}
			}
	}
		

	$sSQL  = "SELECT";
	$sSQL .= "   f.data,f.descricao,f.valor,f.status,ctt.status as cnt_status, ";
	$sSQL .= "   cp.id_cliente_produto, cnt.username, prd.tipo, ";
	$sSQL .= "	 cl.id_cliente, cl.nome_razao ";
	$sSQL .= "FROM ";
	$sSQL .= "	 ((cltb_cliente cl INNER JOIN cbtb_cliente_produto cp USING (id_cliente)) INNER JOIN cntb_conta cnt USING(id_cliente_produto)) ";
	$sSQL .= "	 INNER JOIN ";
	$sSQL .= "   (cbtb_faturas f INNER JOIN cbtb_contrato ctt USING(id_cliente_produto))";
	$sSQL .= "	 USING(id_cliente_produto), prtb_produto as prd " ;
	$sSQL .= "WHERE ";
	$sSQL .= "   prd.id_produto = cp.id_produto AND ";
	$sSQL .= "	 cnt.conta_mestre is true AND ";
	$sSQL .= "   CASE WHEN ";
	$sSQL .= "      f.reagendamento is not null ";
	$sSQL .= "   THEN ";
	$sSQL .= "      f.reagendamento < CAST(now() as date)  ";
	$sSQL .= "   ELSE ";
	$sSQL .= "      f.data < CAST(now() as date) - INTERVAL '10 days' ";
	$sSQL .= "   END  ";
	$sSQL .= "   AND (f.status != 'P' AND f.status != 'E' AND f.status != 'C') ";
	$sSQL .= "   AND ctt.status = 'A' AND cnt.status = 'A'";
	$sSQL .= "ORDER BY f.data, cl.nome_razao, f.descricao, f.status, f.valor";

	
	//echo $sSQL;
	
	/*
	$sSQL  = "SELECT";
	$sSQL .= "   f.data,f.descricao,f.valor,f.status,ctt.status as cnt_status ";
	$sSQL .= "FROM ";
	$sSQL .= "   cbtb_faturas f INNER JOIN cbtb_contrato ctt USING(id_cliente_produto)  ";
	$sSQL .= "WHERE ";
	$sSQL .= "   CASE WHEN ";
	$sSQL .= "      f.reagendamento is not null ";
	$sSQL .= "   THEN ";
	$sSQL .= "      f.reagendamento < CAST(now() as date)  ";
	$sSQL .= "   ELSE ";
	$sSQL .= "      f.data < CAST(now() as date) - INTERVAL '10 days' ";
	$sSQL .= "   END  ";
	$sSQL .= "   AND (f.status != 'P' AND f.status != 'E' AND f.status != 'C') ";
	$sSQL .= "   AND ctt.status = 'A' ";
	$sSQL .= "ORDER BY f.data, f.descricao, f.status, f.valor";*/
	
	
	$relat = $this->bd->obtemRegistros($sSQL);
	
	$this->tpl->atribui("relat", $relat);	
	$this->tpl->atribui("op", $op);
	
	$this->arquivoTemplate="cobranca_bloqueios.html";
	return;

?>
