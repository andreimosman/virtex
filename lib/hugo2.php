<?
	$id_cliente = @$_REQUEST["id_cliente"];
		
	$sSQL  = "SELECT ";
	$sSQL .= "f.id_cliente_produto, f.data, f.valor, f.status, f.observacoes,f.descricao, f.reagendamento, f.pagto_parcial, ";
	$sSQL .= "f.data_pagamento, f.desconto, f.acrescimo, f.valor_pago, ";
	$sSQL .= "c.id_cliente_produto, c.id_cliente ";
	$sSQL .= "FROM ";
	$sSQL .= "cbtb_faturas f, cbtb_cliente_produto c ";
	$sSQL .= "WHERE ";
	$sSQL .= "id_cliente = '$id_cliente' ";
	$sSQL .= "AND ";
	$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
	$sSQL .= "ORDER BY f.data ASC ";

	$lista_faturas = $this->bd->obtemRegistros($sSQL);
	//echo "Lista: $sSQL <br>";
	
	$sSQL = "SELECT nome_razao FROM cltb_cliente WHERE id_cliente = '$id_cliente'";
	$cliente = $this->bd->obtemUnicoRegistro($sSQL);
	
	//echo "sql: $sSQL<br> Nome:".$cliente["nome_razao"]."<br> ";
	
	$this->tpl->atribui("cliente",$cliente);
	$this->tpl->atribui("id_cliente", $id_cliente);
	$this->tpl->atribui("lista_faturas",$lista_faturas);

?>