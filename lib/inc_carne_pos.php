<?

// emissão de carnê pós-pago!!!


	$ini_carne = @$_REQUEST["ini_carne"];
	$data_carne = @$_REQUEST["data_carne"];
	$prorata = @$_REQUEST["prorata"];
	$valor_prorata = @$_REQUEST["valor_prorata"];
	$pri_venc = @$_REQUEST["pri_venc"];

	//echo "PRORATA: $prorata <br>";
	//echo "VALOR: $valor_prorata <br>";





	list($ini_d, $ini_m, $ini_a) = explode("/", $ini_carne);
	list($dat_d, $dat_m, $dat_a) = explode("/", $data_carne);

	$stamp_inicial = mktime(0,0,0, $ini_m, $ini_d, $ini_a);
	$stamp_final = mktime(0,0,0, $dat_m, $dat_d, $dat_a);

	$diferenca_meses = (($stamp_final - $stamp_inicial) / 86400) / 30;

	if($tx_instalacao > 0) {

		$fatura_valor = $tx_instalacao;
		$hoje = date("Y-m-d");

		if (!$pri_venc){
			$fatura_dt_vencimento = $hoje;
		}else{
			$fatura_dt_vencimento = $pri_venc;
		}

		//Calcula a data dos próximos pagamentos de fatura.

		//echo "VALOR_FATURA: $fatura_valor <br>";



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

	}

	for($i=0; $i<=floor($diferenca_meses); $i++) {

		//Aplica descontos, caso haja algum período de desconto declarado
		if($qt_desconto > 0) {

			$fatura_desconto = $desconto_promo;
			$qt_desconto--;

		} else {

			$fatura_desconto = 0;

		}

		//Adiciona taxa de instalação na fatura, caso haja.
		if ($i==0) { //Cria primeira fatura pós-paga

				if ($prorata == true){ // pega se existe prorata e soma no valor da primeira fatura

					$fatura_valor = $valor_prorata;
					//echo "valor com prorata: $fatura_valor <br>";
				}

				if($pri_venc != ""){
					list($ini_d, $ini_m, $ini_a) = explode("/", $pri_venc);
					$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));
				}else{
					$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m, $ini_d, $ini_a));
					//$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $cm+$i, $dia_vencimento, $ca));
				}


			//Adiciona-se ao valor da fatura o valor do pro-rata																														

			//Se houver taxa de instalação no pós pago, então a primeira fatura do carnê será referente à taxa de instalação

			/*if($tx_instalacao > 0) {

				$fatura_valor += $tx_instalacao;

				//Calcula a data dos próximos pagamentos de fatura.

				//echo "VALOR_FATURA: $fatura_valor <br>";


				$sSQL =  "INSERT INTO cbtb_faturas(";
				$sSQL .= "	id_cliente_produto, data, descricao, valor, status, observacoes, ";
				$sSQL .= "	reagendamento, pagto_parcial, data_pagamento, desconto, ";
				$sSQL .= "	acrescimo, valor_pago ";
				$sSQL .= ") VALUES (";
				$sSQL .= "	$id_cliente_produto, '$fatura_dt_vencimento', '$fatura_desc', $fatura_valor, '$fatura_status', '$fatura_obs', ";
				$sSQL .= "	NULL, $fatura_pg_parcial, NULL, $fatura_desconto, ";
				$sSQL .= "	$fatura_pg_acrescimo, $fatura_vl_pago ";
				$sSQL .= ")";

				//echo "Fatura:  $sSQL<br>\n";
				$this->bd->consulta($sSQL);

			}*/





			//fputs($fd,$fatura);
			//if( ($i+1) % 3 == 0 ) {
			///	$new_page = "<hr>";
			//	fputs($fd,$new_page);
			//}


		}else{


			$fatura_valor = $valor_cont_temp;

		}

		//Calcula o desconto sobre a fatura.
		$fatura_valor -= $fatura_desconto;

		//Calcula a data dos próximos pagamentos de fatura.
			$fatura_dt_vencimento = date("Y-m-d", mktime(0,0,0, $ini_m+$i, $ini_d, $ini_a));


		//echo "VALOR FATURA: $fatura_valor <br>";
		//echo "DT VENC: $fatura_dt_vencimento <br>";


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

		if( $i>0 && $i % 3 == 0 ) {
			$new_page = "<hr>";
			fputs($fd,$new_page);
		}


		fputs($fd,$fatura);

		/*
		list($dt_final_a, $dt_final_m, $dt_final_d) = explode("-", $fatura_dt_vencimento);

		$stamp_dt1 = mktime(0,0,0,$dt_final_m, $dt_final_d, $dt_final_a);

		//if("$dt_final_d/$dt_final_m/$dt_final_a" == "$data_carne") break; */




	}
?>
