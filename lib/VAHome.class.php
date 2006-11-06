<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAHome extends VirtexAdmin {

	public function VAHome() {
		parent::VirtexAdmin();
	
		$adm = $this->admLogin->obtemAdmin();
		$this->tpl->atribui("admin",$adm);	

			$privilegio_cliente = 'sim';
			$privilegio_radius = 'sim';
			$privilegio_config = 'sim';
			$privilegio_cobranca = 'sim';
			$privilegio_gravar_cliente = 'sim';
			$privilegio_registro  = 'sim';
			$privilegio_links = 'sim';

			if( ! $this->privPodeLer("_SUPORTE") ) {
				$privilegio_radius = 'nao';
			}
			if( ! $this->privPodeLer("_CLIENTES") ) {
				$privilegio_cliente = 'nao';
			}
			if( ! $this->privPodeLer("_CONFIG_PREFERENCIAS") ) {
				$privilegio_config = 'nao';
			}
			if( ! $this->privPodeLer("_COBRANCA") ) {
				$privilegio_cobranca = 'nao';
			}
			if ( ! $this->privPodeGravar("_CLIENTES") ) {
				$privilegio_gravar_cliente  = 'nao';
			}
			if( ! $this->privPodeLer("_CONFIG_REGISTRO") ) {
				$privilegio_registro  = 'nao';
			}
			if( ! $this->privPodeLer("_LINKS") ) {
				$privilegio_links  = 'nao';
			}

			$this->tpl->atribui("privilegio_config",$privilegio_config);
			$this->tpl->atribui("privilegio_registro",$privilegio_registro);
			$this->tpl->atribui("privilegio_gravar_cliente",$privilegio_gravar_cliente);
			$this->tpl->atribui("privilegio_cobranca",$privilegio_cobranca);
			$this->tpl->atribui("privilegio_radius",$privilegio_radius);
			$this->tpl->atribui("privilegio_cliente",$privilegio_cliente);
			$this->tpl->atribui("privilegio_links",$privilegio_links);
				
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
				
				
				$sSQL = "SELECT * from cftb_forma_pagamento where id_cobranca = 1 and disponivel is true";
				$boleto_existe = $this->bd->obtemUnicoRegistro($sSQL);
				
				$aSQL = " SELECT exibir_monitor FROM pftb_preferencia_monitoracao ";
				
				$exibir_monitor = $this->bd->obtemUnicoRegistro($aSQL);
				
				//////echo $exibir_monitor['exibir_monitor'];
				
				$this->tpl->atribui("exibir_monitor",$exibir_monitor['exibir_monitor']);
				$this->tpl->atribui("boleto",$boleto_existe);
				$this->tpl->atribui("lic_discado",$lic_discado);
				$this->tpl->atribui("lic_email",$lic_email);
				$this->tpl->atribui("lic_hospedagem",$lic_hospedagem);
				$this->tpl->atribui("lic_email",$lic_email);
				$this->tpl->atribui("lic_interface",$lic_interface);
				$this->tpl->atribui("lic_bandalarga",$lic_bandalarga);
				
				
		
		$this->arquivoTemplate = "home.html";
	
	}
    
	public function enviaEmail(){

			$aSQL  = " SELECT enviar_email FROM pftb_preferencia_cobranca ";
			$email = $this->bd->obtemUnicoRegistro($aSQL);
			
			if ($email['enviar_email'] == 't'){
			
				$carencia = (int)$this->prefs->obtem("cobranca","carencia");
				$carencia_total = $carencia - 5 ;
				///////echo $carencia_total."<Br>";

				$sSQL  = "SELECT ";
				$sSQL .= "   cl.nome_razao, p.tipo, cp.id_cliente_produto, cl.id_cliente, f.nosso_numero, f.cod_barra, f.data, cl.email ";
				$sSQL .= "FROM ";
				$sSQL .= "   cltb_cliente cl, prtb_produto p, cbtb_faturas f,cntb_conta cn, ";
				$sSQL .= "   cbtb_cliente_produto cp, cbtb_contrato ctt ";
				$sSQL .= "WHERE ";
				$sSQL .= "	 f.valor > '0.00' AND ";
				$sSQL .= "	 cn.status != 'S' AND ";
				$sSQL .= "	 cn.tipo_conta = p.tipo AND ";
				$sSQL .= "	 cn.id_cliente_produto = cp.id_cliente_produto AND ";
				$sSQL .= "   cl.id_cliente = cp.id_cliente ";
				$sSQL .= "   AND p.id_produto = cp.id_produto ";
				$sSQL .= "   AND ctt.id_cliente_produto = cp.id_cliente_produto ";		
				$sSQL .= "   AND f.id_cliente_produto = cp.id_cliente_produto ";
				$sSQL .= "   AND ";
				$sSQL .= "   CASE WHEN ";
				$sSQL .= "      f.reagendamento is not null ";
				$sSQL .= "   THEN ";
				$sSQL .= "      f.reagendamento < CAST(now() as date)  ";
				$sSQL .= "   ELSE ";
				$sSQL .= "      f.data < CAST(now() as date) - INTERVAL '$carencia_total days' ";
				$sSQL .= "   END  ";
				$sSQL .= "   AND f.status not in ('P','E','C') ";
				$sSQL .= "   AND ctt.status = 'A' ";
				$sSQL .= "GROUP BY ";
				$sSQL .= "   cl.nome_razao, p.nome, p.tipo, cp.id_cliente_produto, cl.id_cliente, f.data, f.nosso_numero, f.cod_barra, cl.email ";
				$sSQL .= "ORDER BY ";
				$sSQL .= "   cl.nome_razao, p.nome ";
				
				///echo $sSQL ;


					$rel_clientes = $this->bd->obtemRegistros($sSQL);

					$aSQL  = " SELECT mensagem_email FROM pftb_preferencia_cobranca ";

					$mensagem = $this->bd->obtemUnicoRegistro($aSQL);
					
					
					
					$empresa = $this->prefs->obtem("geral","nome");
					$dominio = $this->prefs->obtem("geral","dominio_padrao");
					
					$email = $this->prefs->obtem("cobranca","email_remetente");
					////////echo $email;
					
					$html = "<div align='center'>
					<br>
					<html>
					<head>
					<title>Licença VirtexAdmin</title>
					<style type='text/css'>
					<!--
					.style3 {
						color: #FF3300;
						font-family: Verdana, Arial, Helvetica, sans-serif;
						font-size: 14;
					}
					.style5 {font-family: Verdana, Arial, Helvetica, sans-serif}
					.style8 {font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }
					.style10 {color: #6D9179; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; }
					.style12 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; }
					.style17 {color: #6D9179; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14; }
					.style19 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14; }
					.style20 {font-size: 14}
					-->
					</style>
					</head>
					
					<body bottommargin='0' topmargin='0' marginheight='0' marginwidth='0'>
						<table width='700' border='0' cellpadding='0' cellspacing='0' style='border:1px solid #CCCCCC;'>
						  <tr>
						  	<td width='700'><img src='http://www.mosman.com.br/logo_top_email_virtex.png'></td>
						  </tr>
						  <tr>
						  	<td><Br><Br>
						  </tr>
						  <tr>
						  	<td><span class='style17'>&nbsp;&nbsp;&nbsp;Prezado Cliente, </span></td>
						  </tr>						  
						  <tr>
						  	<td height='200px' align='center'>
							<table>
							 <tr>
							  <td width='650'>
								<span class='style17'>" . $mensagem['mensagem_email'] . "</span></td>
							 </tr>
							</table>
							</td>
						  </tr>
						  <tr>
						  	<td align='right'><span class='style17'>Atenciosamente,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <Br>" . $empresa . ".&nbsp;&nbsp;&nbsp;</span></td>
						  </tr>
						  <tr>
						  	<td height='10px'>&nbsp;</td>
						  </tr>
					    </table>
					   <br>
					</body>
					</html>
					</div>
					";
					$headers = "Content-type: text/html; charset=iso-8859-1\r\n"; 
					$headers .= "From: $empresa <$email>\r\n" ;
					
				$counter = "" ;
				
				for ( $i=0; $i <count($rel_clientes); $i++){

					$email_cliente = $rel_clientes[$i]["email"];
					$cod_barra = $rel_clientes[$i]["cod_barra"];
					$nosso_numero = $rel_clientes[$i]["nosso_numero"];
					$id_cliente_produto = $rel_clientes[$i]["id_cliente_produto"];


					$sSQL  = " SELECT email_aviso FROM cbtb_faturas WHERE id_cliente_produto = '$id_cliente_produto' AND cod_barra = '$cod_barra' AND nosso_numero = '$nosso_numero' ";

					$email_aviso = $this->bd->obtemUnicoRegistro($sSQL);
					
					///echo $email_cliente . '<br><hr><br>';


					if ($email_aviso['email_aviso'] == 'f' && $email_cliente != "" ){

						if(mail($email_cliente, "Problemas na Sua Conta" ,  $html, $headers)){

						$sSQL  = "UPDATE cbtb_faturas SET email_aviso = 't' WHERE cod_barra = '$cod_barra' AND nosso_numero = '$nosso_numero' AND id_cliente_produto = '$id_cliente_produto' ";
						$this->bd->consulta($sSQL);
						
						$counter++ ;

						}

					}

					$lista_id = $rel_clientes[$i]["id_cliente_produto"];
					

				}
				
								
				$this->tpl->atribui("mensagem",$mensagem['mensagem_email']);
				$this->tpl->atribui("empresa",$empresa);

			}
			
	}
    
	
	
	public function processa($op=null) {
	
	$this->enviaEmail();
	
		if( $op == "home" ) {

				$licenca = $this->lic->obtemLicenca();
				$hoje = Date("Y-m-d");
				//echo $hoje;
				if($licenca["geral"]["expira_em"] < $hoje && $licenca["geral"]["congela_em"] > $hoje){
					$status = "expirado";
				}else if ($licenca["geral"]["congela_em"] < $hoje ){
					$status = "congelado";
				}else if ($licenca["geral"]["congela_em"] > $hoje && $licenca["geral"]["expira_em"] > $hoje){
					$status = "ativo";
				}

			$sSQL  = "SELECT ";
			$sSQL .= "f.id_cliente_produto, cl.nome_razao, f.data_renovacao, f.valor_contrato, p.nome, ";
			$sSQL .= " c.id_cliente ";
			$sSQL .= "FROM ";
			$sSQL .= " cbtb_cliente_produto c, cltb_cliente cl, cbtb_contrato f, prtb_produto p ";
			$sSQL .= "WHERE ";
			$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
			$sSQL .= "AND p.tipo = f.tipo_produto ";
			$sSQL .= "AND f.data_renovacao < now() + interval '30 day' ";
			$sSQL .= "AND cl.id_cliente = c.id_cliente  ";
			$sSQL .= "AND c.id_cliente_produto = f.id_cliente_produto ";
			$sSQL .= "AND p.id_produto = c.id_produto ";
			$sSQL .= "AND f.status = 'A' ";
			$sSQL .= "ORDER BY f.data_renovacao ASC ";

				$lista_contrato = $this->bd->obtemRegistros($sSQL);
		
		
		$cbr = new VACobranca();
		
			$cli_bloq = $cbr->clientesParaBloqueio();
			
			$num_cli = count($cli_bloq);
	
		$this->tpl->atribui("status",$status);
		$this->tpl->atribui("licenca",$licenca);
		$this->tpl->atribui("lista_contrato",$lista_contrato);
		$this->tpl->atribui("num_cli",$num_cli);

	   $this->arquivoTemplate = "home_principal.html";
		   
		}
		if ($op == "lista_email"){
		
		
		$this->arquivoTemplate = 'relatorio_lista_email.html' ;
		return;
		
		
		}
		
		if ($op == "mostra_email"){
					
			$empresa = $this->prefs->obtem("geral","nome");
			
			
			
			$mensagem = @$_REQUEST['mensagem'];
			
			
		 	if ($mensagem != ""){
			
				$this->tpl->atribui("mensagem",$mensagem);
			
			}
			$this->tpl->atribui("empresa",$empresa);
			
			$this->arquivoTemplate = 'exemplo_email.html';
		
		
		}
		
		if ($op == "renovacao_contrato"){

				if( ! $this->privPodeGravar("_COBRANCA") ) {
							$this->privMSG();
							return;
				}


		
			$sSQL  = "SELECT ";
			$sSQL .= "f.id_cliente_produto, cl.nome_razao, f.data_renovacao, f.valor_contrato, p.nome, ";
			$sSQL .= " c.id_cliente ";
			$sSQL .= "FROM ";
			$sSQL .= " cbtb_cliente_produto c, cltb_cliente cl, cbtb_contrato f, prtb_produto p ";
			$sSQL .= "WHERE ";
			$sSQL .= "f.id_cliente_produto = c.id_cliente_produto ";
			$sSQL .= "AND p.tipo = f.tipo_produto ";
			$sSQL .= "AND f.data_renovacao <= now() + interval '30 day' ";
			$sSQL .= "AND cl.id_cliente = c.id_cliente  ";
			$sSQL .= "AND c.id_cliente_produto = f.id_cliente_produto ";
			$sSQL .= "AND p.id_produto = c.id_produto ";
			$sSQL .= "AND f.status = 'A' ";
			$sSQL .= "ORDER BY f.data_renovacao ASC ";

			$lista_contrato = $this->bd->obtemRegistros($sSQL);
			
			for($i=0;$i<count($lista_contrato);$i++){
			
				$sSQL = "SELECT COUNT(*) as faturas_pendentes FROM cbtb_faturas WHERE id_cliente_produto = '".$lista_contrato[$i]["id_cliente_produto"]."' AND status != 'P' ";
				$pendentes = $this->bd->obtemUnicoRegistro($sSQL);
				
				if ($pendentes["faturas_pendentes"] > 0 ){
					
					$lista_contrato[$i]["faturas_pendentes"] = $pendentes["faturas_pendentes"];
					
				}else{
					$lista_contrato[$i]["faturas_pendentes"] = "0";
				}
			
			
			
			}
			
			$this->tpl->atribui("lista_contrato",$lista_contrato);
			$this->arquivoTemplate = "renovacao_contrato.html";

		}
	
	
	
	
	}

public function __destruct() {
      	parent::__destruct();
}

}



?>
