<?
// emissão de boletos BB pos-pago

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
		//echo $_hoje."<br>";

		if (!$pri_venc){
			$fatura_dt_vencimento = $_hoje;
		}else{
			$fatura_dt_vencimento = $pri_venc;
		}

		//Calcula a data dos próximos pagamentos de fatura.

		//echo "VALOR_FATURA: $fatura_valor <br>";

		//echo $fatura_dt_vencimento."<br>";

		$sSQL =  "INSERT INTO cbtb_faturas(";
		$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
		$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
		$sSQL .= "	acrescimo, valor_pago, id_carne ";
		$sSQL .= ") VALUES (";
		$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
		$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
		$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago, $id_carne ";
		$sSQL .= ")";

		//echo "Fatura:  $sSQL<br>\n";
		$this->bd->consulta($sSQL);
		
		$data = $fatura_dt_vencimento;
		$fatura = $this->boleto($id_cliente_produto,$data,$id_cliente,$forma_pagamento);

	}

	


?>
