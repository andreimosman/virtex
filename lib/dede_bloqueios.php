<?
	//Bloquieio e Desbloqueio de clientes atrasados.
	
	$acao = @$_REQUEST["acao"];
	$op = @$_REQUEST["op"];
	
	
	if($acao == "bloquear") {
		
			$id_bloqueio = @$_REQUEST["id_bloqueio"];
			$n_bloqueio = count($id_bloqueio);
			
			$tipo_bloqueio = "B";
			
			
			if ($n_bloqueio && $n_bloqueio > 0) {
			
				$admin = $this->admLogin->obtemAdmin();
				
				for ($i=0; $i<$n_bloqueio; $i++) {
					
					$id_processo = $this->bd->proximoID("lgsq_id_processo");
					list($id_cli_produto, $tipo) = explode("-", $id_bloqueio[$i]);
					
					$tipo = trim($tipo);
					
					$sSQL  = "INSERT INTO ";
					$sSQL .= "	lgtb_bloqueio_automatizado (";
					$sSQL .= "id_processo, id_cliente_produto, data_hora, tipo, admin ";
					$sSQL .= ") VALUES ( ";
					$sSQL .= "  $id_processo, $id_cli_produto, now(), '$tipo_bloqueio', '$admin' ";
					$sSQL .= ") ";
					
					//echo ""QUERY INERT: $sSQL<br>\n";
					
					$this->bd->consulta($sSQL);
					
					
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
	//$sSQL .= "	 cnt.conta_mestre is true AND ";
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

	
	echo $sSQL;
	
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