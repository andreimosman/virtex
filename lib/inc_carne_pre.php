<?

// emissão de carne pre-pago!!!

	/*
	$ini_carne = @$_REQUEST["ini_carne"];
	$data_carne = @$_REQUEST["data_carne"];

	list($ini_d, $ini_m, $ini_a) = explode("/", $ini_carne);
	list($dat_d, $dat_m, $dat_a) = explode("/", $data_carne);

	for ($i=0; $i < $vigencia; $i++) */

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
				//echo "DT: $fatura_dt_vencimento <br>";
			}
			//TODO: Procurar função de adição do pro-rata
			if($tx_instalacao > 0) $fatura_valor += $tx_instalacao;

		}else{

			$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));

		}



		//Calcula a data dos próximos pagamentos de fatura.

		//$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));



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

		//echo "FATURA: $sSQL<br>";
		$this->bd->consulta($sSQL);

		$data = $fatura_dt_vencimento;

		$fatura = $this->carne($id_cliente_produto,$data,$id_cliente,$forma_pagamento);
		/*if($i == 0){
			$head = "<html><head></head><body>";
			fputs($fd,$head);
		}*/

		if( $i>0 && $i % 3 == 0 ) {
			$new_page = "<hr>";
			fputs($fd,$new_page);
		}


		fputs($fd,$fatura);

	}
	
	
?>									