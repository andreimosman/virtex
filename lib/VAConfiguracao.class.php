<?

//require_once(PATH_LIB."/RedeIP.class.php");


class VAConfiguracao extends VirtexAdminWeb {

	public function __construt() {
		parent::__construct();
	}


	protected function validaFormulario() {
	   $erros = array();
	   return $erros;
	}
	
	 protected function obtemListaPOPs($id_pop="",$nivel=0) {

	   $sSQL  = "SELECT ";
	   $sSQL .= "   id_pop, nome, info, tipo , id_pop_ap , status, ipaddr, infoserver, snmp_versao, snmp_ro_com, snmp_rw_com, ativar_snmp, ativar_monitoramento  ";
	   $sSQL .= "FROM ";
	   $sSQL .= "   cftb_pop ";
	   $sSQL .= "WHERE ";

	   if( $id_pop ) {

		   $sSQL .= "   id_pop_ap = '".$this->bd->escape($id_pop)."' ";

	   } else {

		   $sSQL .= "   id_pop_ap is null ";

	   }
	
		$sSQL .= " AND status != 'D' ";
		$sSQL .= "ORDER BY ";
		$sSQL .= "   nome";



	   $lista = $this->bd->obtemRegistros($sSQL);

	   $retorno = array();

	   for($i=0;$i<count($lista);$i++) {

		   $lista[$i]["nivel"] = $nivel;
		   $retorno[] = $lista[$i];
		   $sub = $this->obtemListaPOPs($lista[$i]["id_pop"],$nivel+1);

		   for($x=0;$x<count($sub);$x++) {

			   $retorno[] = $sub[$x];

		   }

	   }


	   return($retorno);

	}



	public function processa($op=null) {// Cria fun??o processa

		if ($op == "lista_pop"){
			if( ! $this->privPodeLer("_CONFIG_EQUIPAMENTOS") ) {
				$this->privMSG();
				return;
			}		

			$erros = array();


			$enviando = false;



			$reg = array();

			$lista = $this->obtemListaPOPs();

			for($i=0;$i<count($lista);$i++) {

			//   echo str_repeat(" ",$lista[$i]["nivel"]) ."&nbsp;". $lista[$i]["nome"] . "\n<br>";

			 ///echo $lista[$i]["nivel"] . "/" . $lista[$i]["nome"] . "\n<br>";

			}


			$this->tpl->atribui("lista_pop",$lista);



			$this->arquivoTemplate = "configuracao_pop_lista.html";
		} else if ($op == "ajax_loop") {
			// $next_id = $reg["id_pop_ap"]; // Vem do formulario
			$id_pop  = @$_REQUEST["id_pop"];
			$next_id = @$_REQUEST["id_pop_ap"];

			$linkados=array();
			$erro = false;
			//echo "ID POP: $id_pop<br>\n";
			if( $next_id ) {
				while(true) {
					$sSQL  = "SELECT ";
					$sSQL .= "   id_pop,id_pop_ap ";
					$sSQL .= "FROM ";
					$sSQL .= "   cftb_pop ";
					$sSQL .= "WHERE ";
					$sSQL .= "   id_pop = '$next_id' ";

					$r = $this->bd->obtemUnicoRegistro($sSQL);
					$next_id = $r["id_pop_ap"];

					if( !$next_id ) {
						// Return ok
						//echo "<BR>P1<bR>\n";
						break;
					} else {
						if( in_array($next_id,$linkados) ) {
							// Erro - Recursividade do mau.
							//echo "<BR>P2<bR>\n";
							$erro = true;
							break;
						} else {
							//echo "<BR>P3<bR>\n";
							$linkados[] = $next_id;
						}
					}
				}
			}

			if( $erro ) {
				echo "Erro ao processar:\n - Erro recursivo.\n\rO POP n?o pode ser escolhido, por favor escolha outro.";
			} else {
				//echo "OK";
			} 

		}
		
		else if ($op == "ajax_ping"){
		
				$tamanho = @$_REQUEST["tamanho"];
				$endereco_ip = @$_REQUEST["ip"];
				$pacotes = @$_REQUEST["pacotes"];
				$id_nas = @$_REQUEST["id_nas"];
				$host = @$_REQUEST['host'];
				
				$aSQL  = " SELECT tipo_nas FROM cftb_nas WHERE id_nas = '$id_nas' ";
				///echo $aSQL;
				$nas = $this->bd->obtemUnicoRegistro($aSQL);
				
				$erros = array();
				
				if(!$tamanho || $tamanho < 1) $tamanho = 32;
				if(!$pacotes || $pacotes < 1) $pacotes = 4;


				header("Content-type: text/html; charset=iso-8859-1");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("pragma: no-cache");
				header("connection: keep-state");
				echo "<p>\n";
				
				
				if( $nas["tipo_nas"] == "I" ) {
					   $r = new RedeIP($endereco_ip);

					   $gateway    = $r->minHost();
					   $mascara    = $r->mascara();
					   $ip_cliente = $r->maxHost();

					   
					  // echo $ip_cliente;
					  
				$ich = new ICHostInfo();
				$icc = new ICClient();

				$info = $ich->obtemInfoServidor($host);

				if(!$icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {
					
					if (!$icc->estaConectado() ){
												
						echo "<br>
							<table border='0' cellspacing='0' cellpadding='0' align='center'>
							  <tr>
							  	<td height='2'></td>
							  </tr>
							  <tr>
							    <td colspan='2'><div align=center><strong><font color='#001100' size='2' >Nao foi possivel conecta-lo ao servidor ".$host."</font></strong></div></td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
							  </tr>
							</table>
							";
						return;

					}

					continue;
				}
				
				////$ip_cliente = 'www.google.com.br';
	

				$dados = $icc->getFPING($ip_cliente,$pacotes,$tamanho) ;
				
				$counter="0";
				$counter_received="0";
				$counter_loss="0";
				$tempo = '0';

				for($i=0; $i<count($dados); $i++){

					if (($dados[$i] != '-') && ($dados[$i] >0) && ($dados[$i] !="-") && ($dados[$i] !="") ){
						
						$counter++;
						$counter_received++;
						$tempo += $dados[$i];
					
					}else{
						
						$counter++;
						$counter_loss++;
						$tempo += '754.25';
					}
					

			}
			
			$percent = ((($counter_loss)*100)/$counter) ;
			
			if ($counter){
			
			echo "			
				<table width='430' border='0' cellpadding='0' cellspacing='0'>
				  <tr>
					<td bgcolor='#F9F9F9' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>Ping</font></strong></p></td>
				  <td colspan='4' bgcolor='#F9F9F9' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>Pacotes</font></strong></p></td
				  >
				  </tr>
				  <tr>
					<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>IP</font></strong></p></td>
					<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>enviados</font></strong></p></td>
					<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>recebidos</font></strong></p></td>
					<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>perdidos</font></strong></p></td>
					<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>tempo</font></strong></p></td>
				  </tr>
				  <tr>
					<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$ip_cliente."</p></td>
					<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter. "</p></td>
					<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter_received. "</p></td>
					<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter_loss. "</p></td>
					<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$tempo." ms</p></td>
				  </tr>
				  <tr>
					<td colspan='4' bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p>Pacotes enviados pelo servidor " .$host. "(".$info['host'] . ")</p></td> 
					<td align='right' valign='bottom'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
				  </tr>
				</table>" ;


			return;
			
			}

			}

				else if ( $nas["tipo_nas"] == "P"  ){				

					$ich = new ICHostInfo();
					$icc = new ICClient();

					  

					$info = $ich->obtemInfoServidor($host);

						if(!$icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {
							
							if (!$icc->estaConectado() ){
							
								echo "<br>
									<table border='0' cellspacing='0' cellpadding='0' align='center'>
									  <tr>
										<td height='2'></td>
									  </tr>
									  <tr>
										<td colspan='2'><div align=center><strong><font color='#001100' size='2' >Nao foi possivel conecta-lo ao servidor ".$host."</font></strong></div></td>
									  </tr>
									  <tr>
										<td>&nbsp;</td>
										<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
									  </tr>
									</table>
											";
								return;

							}

							continue;

						}

				$dados = $icc->getFPING($endereco_ip,$pacotes,$tamanho) ;
				
				$counter="0";
				$counter_received="0";
				$counter_loss="0";
				$tempo = '0';

				for($i=0; $i<count($dados); $i++){

					if (($dados[$i] != '-') && ($dados[$i] >0) && ($dados[$i] !="-") && ($dados[$i] !="") ){
						
				///	echo $tamanho . " bytes para " . $endereco_ip . ": icmp_seq=".$i." time=".trim($dados[$i])." ms <br>\n" ;
						$counter++;
						$counter_received++;
						$tempo += $dados[$i];
					
					}else{
						
				///		echo "tempo esgotado.<br>\n";
						$counter++;
						$counter_loss++;
						$tempo += '754.25';
					}
					

			}
			
			$percent = ((($counter_loss)*100)/$counter) ;
			
			if ($counter){
			
						echo "			
			<table width='430' border='0' cellpadding='0' cellspacing='0'>
			  <tr>
			    <td bgcolor='#F9F9F9' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>Ping</font></strong></p></td>
			  <td colspan='4' bgcolor='#F9F9F9' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>Pacotes</font></strong></p></td
			  >
			  </tr>
			  <tr>
			    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>IP</font></strong></p></td>
			    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>enviados</font></strong></p></td>
			    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>recebidos</font></strong></p></td>
			    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>perdidos</font></strong></p></td>
			    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>tempo</font></strong></p></td>
			  </tr>
			  <tr>
			    <td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$endereco_ip."</p></td>
			    <td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter. "</p></td>
			    <td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter_received. "</p></td>
			    <td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter_loss. "</p></td>
			    <td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$tempo." ms</p></td>
			  </tr>
			  <tr>
			    <td colspan='4' bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p>Pacotes enviados pelo servidor " .$host. "(".$info['host'] . ")</p></td> 
    			<td align='right' valign='bottom'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
			  </tr>
			</table>
	" ;
			
			}


				
		}else {
			
			for($i=0; $i<count($erros); $i++) echo "$erros[$i]<br>";
			
		}
		echo "</p>";
				//$this->arquivoTemplate = "";


		///echo $ip ;

		}

		
		else if ($op == "ajax_arp"){
		
		
		$host = @$_REQUEST["host"];
		$ip = @$_REQUEST["ip"];
		
		$r = new RedeIP($ip);

		$gateway    = $r->minHost();
		$mascara    = $r->mascara();
		$ip_cliente = $r->maxHost();

		$arp=array();

		if( $ip ) {
			
			$ich = new ICHostInfo();
			$icc = new ICClient();

			$arp = array();  

				$info = $ich->obtemInfoServidor($host);

				if(!$icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {
					

					if (!$icc->estaConectado() ){

							echo "<br>
								<table border='0' cellspacing='0' cellpadding='0' align='center'>
								  <tr>
									<td height='2'></td>
								  </tr>
								  <tr>
									<td colspan='2'><div align=center><strong><font color='#001100' size='2' >Nao foi possivel conecta-lo ao servidor ".$host."</font></strong></div></td>
								  </tr>
								  <tr>
									<td>&nbsp;</td>
									<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
								  </tr>
								</table>
								";
							return;
					}
					
					continue;
				}
				
				

				$arp[] = array("host"=>$host, "tabela"=>$icc->getARP($ip_cliente) );
			
				

				for ($z=0;$z<count($arp);$z++){
				
				
					$tabela = $arp[$z]['tabela'];
					
					for ($a=0;$a<count($tabela);$a++){
					
						if ($tabela[$a]['mac'] != "" && $tabela[$a]['addr'] != "" && $tabela[$a]['iface'] != ""){

						echo "
							  <table width='430' border='0' cellpadding='0' cellspacing='0'>
							  <tr>
								<td colspan='3' bgcolor='#F9F9F9' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>Servidor " .$host. "</font></strong></p></td>
							  </tr>
							  <tr>
							    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>IP</font></strong></p></td>
							    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>MAC</font></strong></p></td>
							    <td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>IFACE</font></strong></p></td>
							  </tr>
							  <tr>
							    <td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" . $tabela[$a]['addr'] . "</p></td>
							    <td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" . $tabela[$a]['mac'] . "</p></td>
							    <td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" . $tabela[$a]['iface'] . "</p></td>
							  </tr>
							  <tr>
							  	<td colspan='3' bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
							  </tr>
							</table>
						
						";
							return;

						}else{
						
							echo "<br>
								<table border='0' cellspacing='0' cellpadding='0' align='center'>
								  <tr>
									<td colspan='2'><div align=center><strong><font color='#FF0000' size='2'>Sem resposta para o IP " . $ip_cliente . "</font></strong></div></td>
								  </tr>
								  <tr>
									<td>&nbsp;</td>
									<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF1133'>[fechar]</font></a></td>
								  </tr>
								</table>";
							return;

						
						}
					
					}
				
				
				}
			
			}


		



		}
		else if ($op =='ajax_ping_pop'){
		
		
		$ip = @$_REQUEST['ip'];
		$host = @$_REQUEST['host'];
		$pacotes = @$_REQUEST['pacotes'];
		$tamanho = @$_REQUEST['tamanho'];
		
			if(!$tamanho || $tamanho < 1) $tamanho = 32;
			if(!$pacotes || $pacotes < 1) $pacotes = 4;


				header("Content-type: text/html; charset=iso-8859-1");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("pragma: no-cache");
				header("connection: keep-state");
				echo "<p>\n";

				$ich = new ICHostInfo();
				$icc = new ICClient();

				$info = $ich->obtemInfoServidor($host);

				if(!$icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {

				if (!$icc->estaConectado() ){

				echo "<br>
					<table border='0' cellspacing='0' cellpadding='0' align='center'>
					  <tr>
						<td height='2'></td>
					  </tr>
					  <tr>
						<td colspan='2'><div align=center><strong><font color='#001100' size='2' >Nao foi possivel conecta-lo ao servidor ".$host."</font></strong></div></td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
						<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
					  </tr>
					</table>
					";
				return;

				}

				continue;
				}

				////$ip_cliente = 'www.google.com.br';


				$dados = $icc->getFPING($ip,$pacotes,$tamanho) ;

				$counter="0";
				$counter_received="0";
				$counter_loss="0";
				$tempo = '0';

				for($i=0; $i<count($dados); $i++){

					if (($dados[$i] != '-') && ($dados[$i] >0) && ($dados[$i] !="-") && ($dados[$i] !="") ){

						$counter++;
						$counter_received++;
						$tempo += $dados[$i];

					}else{

						$counter++;
						$counter_loss++;
						$tempo += '754.25';
					}
				}

				$percent = ((($counter_loss)*100)/$counter) ;

				if ($counter){

					echo "			
						<table width='410' border='0' cellpadding='0' cellspacing='0'>
						  <tr>
							<td bgcolor='#F9F9F9' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>Ping</font></strong></p></td>
						  <td colspan='4' bgcolor='#F9F9F9' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>Pacotes</font></strong></p></td
						  >
						  </tr>
						  <tr>
							<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>IP</font></strong></p></td>
							<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>enviados</font></strong></p></td>
							<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>recebidos</font></strong></p></td>
							<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>perdidos</font></strong></p></td>
							<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='center'><p><strong><font color='#BCD3C4'>tempo</font></strong></p></td>
						  </tr>
						  <tr>
							<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$ip."</p></td>
							<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter. "</p></td>
							<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter_received. "</p></td>
							<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$counter_loss. "</p></td>
							<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" .$tempo." ms</p></td>
						  </tr>
						  <tr>
							<td colspan='4' bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p>Pacotes enviados pelo servidor " .$host. "(".$info['host'] . ")</p></td> 
							<td align='right' valign='bottom'><a href='javascript:;' onClick=' Fecha();' >[fechar]</a></td>
						  </tr>
						</table>" ;


					return;
				}	
		
		}else if ($op == "ajax_arp_pop"){
		
				
			$host = @$_REQUEST["host"];
			$ip = @$_REQUEST["ip"];

			$arp=array();

			if( $ip ) {

				$ich = new ICHostInfo();
				$icc = new ICClient();

				$arp = array();  

					$info = $ich->obtemInfoServidor($host);

					if(!$icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {


						if (!$icc->estaConectado() ){

							echo "<br>
								<table border='0' cellspacing='0' cellpadding='0' align='center'>
								  <tr>
									<td height='2'></td>
								  </tr>
								  <tr>
									<td colspan='2'><div align=center><strong><font color='#001100' size='2' >Nao foi possivel conecta-lo ao servidor ".$host."</font></strong></div></td>
								  </tr>
								  <tr>
									<td>&nbsp;</td>
									<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
								  </tr>
								</table>
								";
							return;

						}
					continue;
					}

					$arp[] = array("host"=>$host, "tabela"=>$icc->getARP($ip) );

					for ($z=0;$z<count($arp);$z++){

						$tabela = $arp[$z]['tabela'];

						for ($a=0;$a<count($tabela);$a++){

							if ($tabela[$a]['mac'] != "" && $tabela[$a]['addr'] != "" && $tabela[$a]['iface'] != ""){

								echo "
									<table width='410' border='0' cellpadding='0' cellspacing='0'>
									  <tr>
										<td colspan='3' bgcolor='#F9F9F9' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>Servidor " .$host. "</font></strong></p></td>
									  </tr>
									  <tr>
										<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>IP</font></strong></p></td>
										<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>MAC</font></strong></p></td>
										<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;'><p><strong><font color='#BCD3C4'>IFACE</font></strong></p></td>
									  </tr>
									  <tr>
										<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" . $tabela[$a]['addr'] . "</p></td>
										<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" . $tabela[$a]['mac'] . "</p></td>
										<td bgcolor='#FDFDFD' style='border: 1px solid #FFFFFF;' align='center'><p>" . $tabela[$a]['iface'] . "</p></td>
									  </tr>
									  <tr>
										<td colspan='3' bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' >[fechar]</a></td>
									  </tr>
									</table>

									";
								return;

							}else{

								echo "
									<table border='0' cellspacing='0' cellpadding='0' align='center'>
									  <tr>
										<td colspan='2'><div align=center><strong><font color='#FF0000' size='2'>Sem resposta para o IP " . $ip . "</font></strong></div></td>
									  </tr>
									  <tr>
										<td>&nbsp;</td>
										<td bgcolor='#FCFCFC' style='border: 1px solid #FFFFFF;' align='right'><a href='javascript:;' onClick=' Fecha();' ><font color='#FF0033'>[fechar]</font></a></td>
									  </tr>
									</table>";
								return;

							}

						}


					}

				}



		}
		
		
		else if ($op == "ajax"){
		
				$tipo = @$_REQUEST["tipo"]; 
				//$id_pop = @$_REQUEST["id_pop"]; 

				$sSQL  = " SELECT id_pop, nome, tipo " ;
				$sSQL .= " FROM cftb_pop " ;
				$sSQL .= " WHERE status = 'A' " ;


				if ($tipo != "CL" ){

					$sSQL .= " AND tipo = 'B' ";
					//echo $sSQL;

				}
				if ($tipo == "CL" ){

					$sSQL .= " AND tipo = 'AP' ";

				}

				$sSQL .= " ORDER BY nome ASC ";


				$pop = $this->bd->obtemRegistros($sSQL);
				$this->tpl->atribui("pop",$pop);
				$this->arquivoTemplate = "pop.xml";
				header("Content-type: text/xml");

			return;

		}else if($op == "pop"){

			if( ! $this->privPodeLer("_CONFIG_EQUIPAMENTOS") ) {
				$this->privMSG();
				return;
			}		
			
			$erros = array();

	
			$rotina = @$_REQUEST["rotina"];
			$acao = @$_REQUEST["acao"];
			$id_pop = @$_REQUEST["id_pop"];

			$enviando = false;
			
			$host_info = new ICHostInfo();
			$hosts = $host_info->obtemListaServidores();
			//echo $hosts;

			$this->tpl->atribui("hosts",$hosts);
			

			$tSQL  = "SELECT ";
			$tSQL .= "   id_pop, nome, info, tipo, id_pop_ap, status, ipaddr, infoserver, snmp_versao, snmp_ro_com, snmp_rw_com, ativar_snmp, ativar_monitoramento ";
			$tSQL .= "FROM cftb_pop ";
			$tSQL .= "WHERE tipo = 'AP' AND status != 'D' ";
			$tSQL .= "ORDER BY nome ";
			
			////////echo $tSQL;

			$aps = $this->bd->obtemRegistros($tSQL);

			
			///////////echo $aSQL;



			$reg = array();

			if ($rotina == "desativar"){
				if( ! $this->privPodeGravar("_CONFIG_EQUIPAMENTOS") ) {
					$this->privMSG();
					return;
				}		

				$p = @$_REQUEST["p"];

				$sSQL = "SELECT count(id_pop) as qtde_cli_pop FROM cntb_conta_bandalarga WHERE id_pop = '$id_pop' ";
				$qtde = $this->bd->obtemUnicoRegistro($sSQL);
				//echo "QTDE_POP: $sSQL <br>";
				
				$dSQL = " SELECT count(id_pop_status) as links FROM cftb_pop WHERE id_pop_status = '$id_pop' " ;

				$sSQL = "SELECT nome, tipo FROM cftb_pop WHERE id_pop = $id_pop ORDER BY nome ";
				$_pop = $this->bd->obtemUnicoRegistro($sSQL);
				//echo "POP: $sSQL <br>";

					if ($p == "ok"){

						$sSQL = "UPDATE cftb_pop SET status = 'D' WHERE id_pop = $id_pop ";
						$this->bd->consulta($sSQL);

						$msg_final = "POP DESATIVADO COM SUCESSO!!!";

						$this->tpl->atribui("mensagem",$msg_final);
						$this->tpl->atribui("url",$_SERVER["PHP_SELF"] . "?op=lista_pop");
						$this->tpl->atribui("target","_self");



			  $this->arquivoTemplate="msgredirect.html";
					return;



					}




				$this->tpl->atribui("qtde_cli_pop",$qtde["qtde_cli_pop"]);
				$this->tpl->atribui("id_pop", $id_pop);
				$this->tpl->atribui("nome_pop",$_pop["nome"]);
				$this->tpl->atribui("tipo_pop",$_pop["tipo"]);

				$this->arquivoTemplate = "configuracao_pop_desativar.html";
				return;



			}

			if( $acao ) {
			   // Se ele recebeu o campo a??o ? pq veio de um submit
			   $enviando = true;
			} else {
				// Se n?o recebe o campo a??o e tem id_pop, ? altera??o, caso contr?rio ? cadastro.
				if( $id_pop ) {

					// SELECT
					$sSQL  = "SELECT ";
					$sSQL .= "   id_pop, nome, ipaddr, info, tipo, id_pop_ap, status, infoserver, snmp_versao, snmp_ro_com, snmp_rw_com, ativar_snmp, ativar_monitoramento ";
					$sSQL .= "FROM cftb_pop ";
					$sSQL .= "WHERE id_pop = '$id_pop' AND status != 'D' ";
					$reg = $this->bd->obtemUnicoRegistro($sSQL);
					
					$this->tpl->atribui("ipaddr",$reg["ipaddr"]);
					$this->tpl->atribui("snmp",$reg["ativar_snmp"]);
					$this->tpl->atribui("snmp_versao",$reg["snmp_versao"]);
					$this->tpl->atribui("snmp_ro_com",$reg["snmp_ro_com"]);
					$this->tpl->atribui("snmp_rw_com",$reg["snmp_rw_com"]);
					$this->tpl->atribui("ativar_monitoramento", $reg['ativar_monitoramento']);
					$this->tpl->atribui("infoserver",$reg["infoserver"]);

					$sSQL = "SELECT count(id_pop) as qtde_cli_pop FROM cntb_conta_bandalarga WHERE id_pop = '$id_pop' ";
					$qtde = $this->bd->obtemUnicoRegistro($sSQL);

					$qtde_cli_pop = 0;
					$qtde_cli_pop = $qtde["qtde_cli_pop"];

					$this->tpl->atribui("qtde_cli_pop",$qtde["qtde_cli_pop"]);


					$acao = "alt";
					$titulo = "Alterar";

				} else {
					$acao = "cad";
					$titulo = "Cadastrar";
				}
			}

		if( $enviando ) {
			if( ! $this->privPodeGravar("_CONFIG_EQUIPAMENTOS") ) {
				$this->privMSG();
				return;
			}		

			if( !count($erros) ) {
			   // Grava no banco.
				if( $acao == "cad" ) {
					// CADASTRO

					$id_pop_ap = @$_REQUEST['id_pop_ap'];

					$msg_final = "POP Cadastrado com sucesso!";
					$url = "configuracao.php?op=lista_pop";

					$id_pop = $this->bd->proximoID("cfsq_id_pop");
					
					$ip = @$_REQUEST["ip"];
					
					if ($ip == ""){
					
						$ip = 'NULL' ;
					
					}
					
					$ativar_snmp = @$_REQUEST["snmp"];

					if ($ativar_snmp == "" ){

						$ativar_snmp = 'f' ;

					}
					
					$ativar_monitoramento = @$_REQUEST["ativar_monitoramento"];

					if ($ativar_monitoramento == "" ){

						$ativar_monitoramento = 'f' ;

					}


					$sSQL  = "INSERT INTO ";
					$sSQL .= "   cftb_pop( ";
					$sSQL .= "      id_pop, nome, info, tipo, id_pop_ap, status, ipaddr, infoserver, snmp_versao, snmp_ro_com, snmp_rw_com, ativar_snmp, ativar_monitoramento) ";
					$sSQL .= "   VALUES (";
					$sSQL .= "     '" . $this->bd->escape($id_pop) . "', ";
					$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
					$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["info"]) . "', ";
					$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo"]) . "', ";
					$sSQL .= "      " . ($id_pop_ap ? "$id_pop_ap" : "NULL") . ",  ";
					$sSQL .= "		 '" . $this->bd->escape(@$_REQUEST["status"]).  "', ";

					if ($ip == "" || $ip == "NULL"){

						$sSQL .= " 		$ip, ";

					}else{

						$sSQL .= " 		'$ip', ";

					}

					
					//$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["id_pop_ap"]) . "' ";
					$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["infoserver"]) . "', ";
					$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["snmp_versao"]) . "', ";
					$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["snmp_ro_com"]) . "', ";
					$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["snmp_rw_com"]) . "', ";
					$sSQL .= "     '$ativar_snmp', ";
					$sSQL .= "	   '$ativar_monitoramento' ";
					$sSQL .= "     )";
					///echo $sSQL;


				} else {
				   // ALTERACAO
					$msg_final = "POP Alterado com sucesso!";
					$url = "configuracao.php?op=lista_pop";
					
					$id_pop_ap = @$_REQUEST["id_pop_ap"];
					$ip = @$_REQUEST["ip"];
						
					$ativar_snmp = @$_REQUEST["snmp"];
					$ativar_monitoramento = @$_REQUEST["ativar_monitoramento"];
					
					///valor para boolean snmp
					if ($ativar_snmp == "" ){
					
						$ativar_snmp = 'f' ;
					
					}else{
					
						$ativar_snmp = 't';
					
					}
					
					///valor para boolean monitoramento
					if ($ativar_monitoramento == "" ){
					
						$ativar_monitoramento = 'f' ;
					
					}else{
					
						$ativar_monitoramento  = 't';
					
					}
					
					$sSQL  = "UPDATE ";
					$sSQL .= "   cftb_pop ";
					$sSQL .= "SET ";
					$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
					$sSQL .= "   info = '" . $this->bd->escape(@$_REQUEST["info"]) . "', ";
					$sSQL .= "	 ativar_snmp = '$ativar_snmp', ";
					$sSQL .= "	 snmp_versao = '" . $this->bd->escape(@$_REQUEST["snmp_versao"]) . "', ";
					$sSQL .= "	 snmp_ro_com = '" . $this->bd->escape(@$_REQUEST["snmp_ro_com"]) . "', ";
					$sSQL .= "	 snmp_rw_com = '" . $this->bd->escape(@$_REQUEST["snmp_rw_com"]) . "', ";
					$sSQL .= "   tipo = '" . $this->bd->escape(@$_REQUEST["tipo"]) . "', ";
					$sSQL .= " 	 ativar_monitoramento =  '$ativar_monitoramento'	 , ";
					$sSQL .= "   infoserver = '" . $this->bd->escape(@$_REQUEST["infoserver"]) . "', ";
					
					////echo $sSQL ;

					if (!$id_pop_ap){

						$sSQL .= "  id_pop_ap = NULL , ";

					}else{

						$sSQL .= " 	id_pop_ap = '$id_pop_ap', ";

					}
					
					$sSQL .= "	 status = '" . $_REQUEST["status"] . "', ";

					if (!$ip){

						$sSQL .= "  ipaddr = NULL ";

					}else{

						$sSQL .= " 	ipaddr = '$ip' ";

					}

					$sSQL .= "WHERE ";
					$sSQL .= "   id_pop = '" . $this->bd->escape(@$_REQUEST["id_pop"]) . "' ";  
					
				}

				$this->bd->consulta($sSQL);  

				if( $this->bd->obtemErro() != MDATABASE_OK ) {
					
					echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
					echo "QUERY: " . $sSQL . "<br>\n";
					
				}


				// Exibir mensagem de cadastro executado com sucesso e jogar pra p?gina de listagem.
				$this->tpl->atribui("mensagem",$msg_final); 
				$this->tpl->atribui("url",$url);
				$this->tpl->atribui("target","_self");

				$this->arquivoTemplate = "msgredirect.html";


				// cai fora da fun??o (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
				return;
			}else{


			}

		}



		// Atribui a vari?vel de erro no template.
		$this->tpl->atribui("erros",$erros);
		$this->tpl->atribui("mensagem",$erros);
		$this->tpl->atribui("acao",$acao);
		$this->tpl->atribui("op",$op);

		// Atribui as listas
		//global $_LS_ESTADOS;
		//$this->tpl->atribui("lista_estados",$_LS_ESTADOS);


		global $_LS_TIPO_POP;
		$this->tpl->atribui("tipo_pop",$_LS_TIPO_POP);

		global $_STATUS_POP;
		$this->tpl->atribui("status_pop",$_STATUS_POP);

		// Atribui os campos
					$this->tpl->atribui("status",@$reg["status"]);
			$this->tpl->atribui("id_pop",@$reg["id_pop"]);
			$this->tpl->atribui("nome",@$reg["nome"]);
			$this->tpl->atribui("info",@$reg["info"]);// pega a info do db e atribui ao campo correspon do form
			$this->tpl->atribui("tipo",@$reg["tipo"]);
			$this->tpl->atribui("id_pop_ap",@$reg["id_pop_ap"]);
			$this->tpl->atribui("titulo",@$titulo);// para que no template a variavel do smart titulo consiga pegar o que foi definido no $titulo.

			$this->tpl->atribui("lista_pops2",@$aps);


		// Seta as vari?veis do template.
		$this->arquivoTemplate = "configuracao_pop_cadastro.html";



	}else if ($op == "lista_nas"){
			if( ! $this->privPodeLer("_CONFIG_EQUIPAMENTOS") ) {
				$this->privMSG();
				return;
			}		

			$erros = array();


			$enviando = false;


			$reg = array();

			$sSQL  = "SELECT ";
			$sSQL .= "   id_nas, nome, ip, secret, tipo_nas,infoserver, padrao ";
			$sSQL .= "FROM cftb_nas ORDER BY id_nas ASC ";

			$reg = $this->bd->obtemRegistros($sSQL);
			
			

			$this->tpl->atribui("lista_nas",$reg);



			$this->arquivoTemplate = "configuracao_nas_lista.html";








	}else if ($op =="nas"){

			if( ! $this->privPodeLer("_CONFIG_EQUIPAMENTOS") ) {
				$this->privMSG();
				return;
			}		

			$erros = array();

			$acao = @$_REQUEST["acao"];
			$id_nas = @$_REQUEST["id_nas"];

			$enviando = false;

			$reg = array();

			$host_info = new ICHostInfo();
			$hosts = $host_info->obtemListaServidores();
			//echo $hosts;

			$this->tpl->atribui("hosts",$hosts);


			if( $acao ) {
				// Se ele recebeu o campo a??o ? pq veio de um submit
				$enviando = true;
			} else {
				// Se n?o recebe o campo a??o e tem id_pop ? altera??o, caso contr?rio ? cadastro.
				if( $id_nas ) {
					// SELECT
					$sSQL  = "SELECT ";
					$sSQL .= "   id_nas, nome, ip, secret, tipo_nas, infoserver, padrao ";
					$sSQL .= "FROM cftb_nas ";
					$sSQL .= "WHERE id_nas = '$id_nas'";


					$reg = $this->bd->obtemUnicoRegistro($sSQL);
					
					$padrao = $reg['padrao'];
					
					$this->tpl->atribui("padrao",$padrao);


					$acao = "alt";
					$titulo = "Alterar";

				} else {
					$acao = "cad";
					$titulo = "Cadastrar";
				}
			}//hugo2

			if( $enviando ) {
				if( ! $this->privPodeGravar("_CONFIG_EQUIPAMENTOS") ) {
					$this->privMSG();
					return;
				}		

				if( !count($erros) ) {
					// Grava no banco.
					if( $acao == "cad" ) {
					// CADASTRO
						$tipo_nas = @$_REQUEST["tipo_nas"];
						$secret = @$_REQUEST["secret"];
						$ip = @$_REQUEST["ip"];

						$msg_final = "NAS Cadastrado com sucesso!";


						if($tipo_nas == "R" || $tipo_nas == "P"){   		


							$id_nas = $this->bd->proximoID("cfsq_id_nas");
							
							$tipo_nas = @$_REQUEST['tipo_nas'];


							$sSQL  = "INSERT INTO ";
							$sSQL .= "   cftb_nas( ";
							$sSQL .= "      id_nas, nome, ip, secret, tipo_nas, infoserver, padrao) ";
							$sSQL .= "   VALUES (";
							$sSQL .= "     '" . $id_nas . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["ip"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["secret"]) . "', ";
							$sSQL .= "     '$tipo_nas', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["infoserver"]) . "', ";
							if ($tipo_nas == 'P' ){
							
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["padrao"]) . "' ";
							
							}
							else{
							
								$sSQL .= " NULL ";
							
							}
							$sSQL .= "     )";
							
							////echo $sSQL ;



							$this->spool->radiusAdicionaNAS($ip,$secret);

						} else if($tipo_nas == "I"){

							$id_nas = $this->bd->proximoID("cfsq_id_nas");

							$sSQL  = "INSERT INTO ";
							$sSQL .= "   cftb_nas( ";
							$sSQL .= "      id_nas, nome, ip, secret, tipo_nas, infoserver ) ";
							$sSQL .= "   VALUES (";
							$sSQL .= "     '" . $this->bd->escape($id_nas) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["ip"]) . "', ";
							$sSQL .= "     NULL, ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["tipo_nas"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["infoserver"]) . "' ";
							$sSQL .= "     )";
							
							///echo $sSQL ;


						}





					} else {
					// ALTERACAO
						$msg_final = "NAS Alterado com sucesso!";
						$tipo_nas = @$_REQUEST["tipo_nas"];
						$secret = @$_REQUEST["secret"];
						$ip = @$_REQUEST["ip"];
						$tipo_nas_up = @$_REQUEST['tipo_nas_up'];

						//echo "Tipo NAS: $tipo_nas";


						if($tipo_nas_up == "R" || $tipo_nas_up == "P"){

							$tSQL  = "SELECT ";
							$tSQL .= "	ip, secret,infoserver ";
							$tSQL .= "FROM ";
							$tSQL .= "	cftb_nas ";
							$tSQL .= "WHERE ";
							$tSQL .= "   id_nas = '" . $this->bd->escape(@$_REQUEST["id_nas"]) . "' ";

							$compara = $this->bd->obtemUnicoRegistro($tSQL);

							$tipo_nas = @$_REQUEST['tipo_nas_up'];
							
							
							$sSQL  = "UPDATE ";
							$sSQL .= "   cftb_nas ";
							$sSQL .= "SET ";
							$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
							$sSQL .= "   ip = '" . $this->bd->escape(@$_REQUEST["ip"]) . "', ";
							$sSQL .= "   secret = '" . $this->bd->escape(@$_REQUEST["secret"]) . "', ";
							$sSQL .= "   tipo_nas = '$tipo_nas', ";
							$sSQL .= "   infoserver = '" . $this->bd->escape(@$_REQUEST["infoserver"]) . "', ";
							
							if ($tipo_nas == 'P'){
							
								$sSQL .= "	padrao ='" . $this->bd->escape(@$_REQUEST["padrao"]) . "' ";
							
							}else{
							
								$sSQL .= "	padrao = NULL ";
								
							}
							$sSQL .= "WHERE ";
							$sSQL .= "   id_nas = '" . $this->bd->escape(@$_REQUEST["id_nas"]) . "' ";  

							///echo $sSQL ;


							if ($ip != $compara['ip'] || $secret != $compara['secret']){


								$this->spool->radiusExcluiNAS($ip);
								$this->spool->radiusAdicionaNAS($ip,$secret);

							}


						}else if($tipo_nas_up == "I"){



							$sSQL  = "UPDATE ";
							$sSQL .= "   cftb_nas ";
							$sSQL .= "SET ";
							$sSQL .= "   nome = '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
							$sSQL .= "   ip = '" . $this->bd->escape(@$_REQUEST["ip"]) . "', ";
							$sSQL .= "   infoserver = '" . $this->bd->escape(@$_REQUEST["infoserver"]) . "', ";
							$sSQL .= "   secret = NULL, ";
							$sSQL .= "   tipo_nas = '$tipo_nas_up' ";
							$sSQL .= "WHERE ";
							$sSQL .= "   id_nas = '" . $this->bd->escape(@$_REQUEST["id_nas"]) . "' ";  

							////echo $sSQL ;

						}



					}

					$this->bd->consulta($sSQL);  

					//if( $this->bd->obtemErro() != MDATABASE_OK ) {
					//echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
					//echo "QUERY: " . $sSQL . "<br>\n";

					//}


					// Exibir mensagem de cadastro executado com sucesso e jogar pra p?gina de listagem.
					$this->tpl->atribui("mensagem",$msg_final); 
					$this->tpl->atribui("url","configuracao.php?op=lista_nas");
					$this->tpl->atribui("target","_self");

					$this->arquivoTemplate = "msgredirect.html";


					// cai fora da fun??o (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
					return;
					}else{


					}

				}


			// Atribui a vari?vel de erro no template.
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("mensagem",$erros);
			$this->tpl->atribui("acao",$acao);
			$this->tpl->atribui("op",$op);

			// Atribui as listas
			//global $_LS_ESTADOS;
			//$this->tpl->atribui("lista_estados",$_LS_ESTADOS);


			global $_LS_TIPO_NAS;
			$this->tpl->atribui("ls_tipo_nas",$_LS_TIPO_NAS);

			// Atribui os campos
			
			$this->tpl->atribui("id_nas",@$reg["id_nas"]);
			$this->tpl->atribui("infoserver",@$reg["infoserver"]);
			$this->tpl->atribui("nome",@$reg["nome"]);
			$this->tpl->atribui("ip",@$reg["ip"]);// pega a info do db e atribui ao campo correspon do form
			$this->tpl->atribui("secret",@$reg["secret"]);
			$this->tpl->atribui("tipo_nas",@$reg["tipo_nas"]);
			$this->tpl->atribui("titulo",@$titulo);// para que no template a variavel do smart titulo consiga pegar o que foi definido no $titulo.



			// Seta as vari?veis do template.
			$this->arquivoTemplate = "configuracao_nas_cadastro.html";


//////////////////////////////HUGO
	}else if ($op == "nas_rede"){




	}else if($op == "rede"){
		// CADASTRA E ALTERA REDE EM DETERMINADO NAS
			if( ! $this->privPodeLer("_CONFIG_EQUIPAMENTOS") ) {
				$this->privMSG();
				return;
			}		
			
		$erros = array();

		$acao = @$_REQUEST["acao"];
		$id_rede = @$_REQUEST["id_rede"];
		$id_nas = @$_REQUEST["id_nas"];
		$tipo_rede = @$_REQUEST["tipo_rede"];
		$rede = @$_REQUEST["rede"];
		$rede_origem = @$_REQUEST["rede_origem"];
		$rede_inicial = @$_REQUEST["rede_inicial"];
		$bits_subredes = @$_REQUEST["bits_subredes"];
		$num_redes = @$_REQUEST['num_redes'];
		$tipo_nas = @$_REQUEST['tipo_nas'];
		$network = @$_REQUEST['network'];





		$enviando = false;

		$reg = array();

		$sSQL  = "SELECT ";
		$sSQL .= "   id_nas, nome, ip, secret, tipo_nas, infoserver ";
		$sSQL .= "FROM ";
		$sSQL .= "   cftb_nas ";
		$sSQL .= "WHERE ";
		$sSQL .= "   id_nas = '".$this->bd->escape($id_nas)."' ";

		$nas = $this->bd->obtemUnicoRegistro($sSQL);



		if($acao){
			$enviando = true;

		}else{

			if($id_rede){
				$acao = "alt";
				$titulo = "Alterar";
			} else {
				$acao = "cad";
				$titulo = "Cadastrar";
			}

		}

		if( $enviando ) {
			if( ! $this->privPodeGravar("_CONFIG_EQUIPAMENTOS") ) {
				$this->privMSG();
				return;
			}				
			if( !count($erros) ) {
				// Grava no banco.
				if( $acao == "cad" ) {
					// CADASTRO


					$msg_final = "Rede Cadastrada com sucesso!";
					$url = "configuracao.php?op=lista_nas";


					if($nas["tipo_nas"] == "P"){
						// cadastraRede (a rede que o cara digitou)

						//$_rede = new RedeIp($rede);
					//$rede = $_rede->obtemRede();
					//echo $rede;


						$this->cadastraRede($rede,$tipo_rede);

						// vinculaRede (a mesma)
						$this->vinculaRede($id_nas,$rede);
						$this->cadastraIPs(@$_REQUEST['rede']);

						//$this->cadastraIPs($rede);
					}else if ($nas["tipo_nas"] == "I"){
					$this->cadastraSubredes($id_nas,$rede_origem,$rede_inicial,$bits_subredes,$num_redes,$tipo_rede);
						//$rede = $rede_origem;						
						//$this->cadastraSubRedes($id_nas,$rede_origem,$rede_inicial,$bits_subredes,$num_redes,$tipo_rede);

					}

					//$this->cadastraRede($rede,$tipo_rede);	
					//$this->vinculaRede($id_nas,$rede);



				} else {
					// ALTERACAO

					$msg_final = "Rede Alterada com sucesso!";
					$url = "configuracao.php?op=lista_nas";

					$this->alteraRede($rede,$tipo_rede);



				}







			// Exibir mensagem de cadastro executado com sucesso e jogar pra p?gina de listagem.
			$this->tpl->atribui("mensagem",$msg_final); 
			$this->tpl->atribui("url",$url);
			$this->tpl->atribui("target","_self");

			$this->arquivoTemplate = "msgredirect.html";


			// cai fora da fun??o (ou seja, deixa de processar o resto do aplicativo: a parte de exibicao da tela);
			return;
			} else {


			}

		}					

		//LISTA REDES CADASTRADAS EM DETERMINADO NAS

		$id_nas = @$_REQUEST["id_nas"];

		$erro = "";

		if( !$id_nas ) {
		   $erro = "Tentativa de acesso inv?lido";
		}

		if( !$erro ) {
			// Informa??es do NAS
			$sSQL  = "SELECT ";
			$sSQL .= "   id_nas, nome, ip, secret, tipo_nas, infoserver ";
			$sSQL .= "FROM ";
			$sSQL .= "   cftb_nas ";
			$sSQL .= "WHERE ";
			$sSQL .= "   id_nas = '".$this->bd->escape($id_nas)."' ";

			$nas = $this->bd->obtemUnicoRegistro($sSQL);

			$this->tpl->atribui("nas",$nas);

			// $sSQL .= "";


			// Lista das redes deste NAS
			//$sSQL  = "SELECT ";
			//$sSQL .= "   r.rede,r.tipo_rede,r.id_rede, nr.id_rede ";
			//$sSQL .= "FROM ";
			//$sSQL .= "   cftb_nas_rede nr, cftb_rede r ";
			//$sSQL .= "WHERE ";
			//$sSQL .= "   r.id_rede = nr.id_rede ";
			//$sSQL .= "   AND nr.id_nas = '" . $this->bd->escape($id_nas) . "' ";
			//$sSQL .= "   id_nas = '" . $this->bd->escape($id_nas) . "' ";

			$sSQL  = "SELECT ";
			$sSQL .= "   r.rede, r.tipo_rede, r.id_rede ";
			$sSQL .= "FROM ";
			$sSQL .= "   cftb_rede r, cftb_nas_rede nr ";
			$sSQL .= "WHERE ";
			$sSQL .= "   r.rede = nr.rede ";
			$sSQL .= "   AND nr.id_nas = $id_nas ";
			$sSQL .= "ORDER BY ";
			$sSQL .= "   r.rede ";
			$sSQL .= "";


			//echo $sSQL;
			// TODO: aplicar filtros e pagina??o...
			$redes = $this->bd->obtemRegistros($sSQL);
			
			
			for ($i=0; $i<count($redes); $i++){
			
			
				$num_rede = @$redes[$i]["rede"];
				$tipo_rede = @$redes[$i]["tipo_rede"];
				
					$aSQL  = " select rede,tipo_bandalarga,ipaddr from cntb_conta_bandalarga ";
					$aSQL .= " WHERE rede = '$num_rede' OR ipaddr = '$num_rede' ";
					
					$redes_disponiveis = $this->bd->obtemRegistros($aSQL);
					
					$redes[$i]["redes_disponiveis"] = $redes_disponiveis;
					
					//echo $aSQL ."<Br><hr>";
				
				}
			
			

			$this->tpl->atribui("redes",$redes);

			$this->arquivoTemplate = "configuracao_nas_rede.html";
			
			
		}



		// Atribui a vari?vel de erro no template.asdsa
		$this->tpl->atribui("erros",$erros);
		$this->tpl->atribui("mensagem",$erros);
		$this->tpl->atribui("acao",$acao);
		$this->tpl->atribui("op",$op);
		$this->tpl->atribui("nas",@$nas);		

		// Atribui os campos
		$this->tpl->atribui("network",$network);
		$this->tpl->atribui("id_rede",@$id_rede);
		$this->tpl->atribui("id_nas",@$id_nas);
		$this->tpl->atribui("tipo_rede",@$tipo_rede);// pega a info do db e atribui ao campo correspon do form
		$this->tpl->atribui("rede",@$rede);
		$this->tpl->atribui("tipo_nas",@$tipo_nas);
		$this->tpl->atribui("titulo",@$titulo);// para que no template a variavel do smart titulo consiga pegar o que foi definido no $titulo.




		// Seta as vari?veis do template.

		$this->arquivoTemplate = "configuracao_redes_cadastro.html";



///////////////////////////////		
	
	
	
	}else if ($op == "listar_bandas"){
	
	
	$bSQL  = "SELECT * FROM cftb_banda ";
	$relatorio_banda = $this->bd->obtemRegistros($bSQL);
	
	if (!$relatorio_banda){
		$mostrar='false';
	}else{
		$mostrar='true';
	}
	
	$this->tpl->atribui("mostrar",$mostrar);
	$this->tpl->atribui("relatorio_banda",$relatorio_banda);
	
			$velocidade_um_down = @$_REQUEST["velocidade_um_down"];
			$velocidade_um_desc = @$_REQUEST["velocidade_um_desc"];

			$velocidade_dois_down = @$_REQUEST["velocidade_dois_down"];
			$velocidade_dois_desc = @$_REQUEST["velocidade_dois_desc"];

			$velocidade_tres_down = @$_REQUEST["velocidade_tres_down"];
			$velocidade_tres_desc = @$_REQUEST["velocidade_tres_desc"];

			$velocidade_quatro_down = @$_REQUEST["velocidade_quatro_down"];
			$velocidade_quatro_desc = @$_REQUEST["velocidade_quatro_desc"];

			$velocidade_cinco_down = @$_REQUEST["velocidade_cinco_down"];
			$velocidade_cinco_desc = @$_REQUEST["velocidade_cinco_desc"];

			$velocidade_seis_down = @$_REQUEST["velocidade_seis_down"];
			$velocidade_seis_desc = @$_REQUEST["velocidade_seis_desc"];

			$velocidade_sete_down = @$_REQUEST["velocidade_sete_down"];
			$velocidade_sete_desc = @$_REQUEST["velocidade_sete_desc"];

			$velocidade_oito_down = @$_REQUEST["velocidade_oito_down"];
			$velocidade_oito_desc = @$_REQUEST["velocidade_oito_desc"];

			$velocidade_nove_down = @$_REQUEST["velocidade_nove_down"];
			$velocidade_nove_desc = @$_REQUEST["velocidade_nove_desc"];

			$velocidade_dez_down = @$_REQUEST["velocidade_dez_down"];
			$velocidade_dez_desc = @$_REQUEST["velocidade_dez_desc"];

			$velocidade_onze_down = @$_REQUEST["velocidade_onze_down"];
			$velocidade_onze_desc = @$_REQUEST["velocidade_onze_desc"];

			$velocidade_doze_down = @$_REQUEST["velocidade_doze_down"];
			$velocidade_doze_desc = @$_REQUEST["velocidade_doze_desc"];

			$velocidade_treze_down = @$_REQUEST["velocidade_treze_down"];
			$velocidade_treze_desc = @$_REQUEST["velocidade_treze_desc"];

			$velocidade_catorze_down = @$_REQUEST["velocidade_catorze"];
			$velocidade_catorze_desc = @$_REQUEST["velocidade_catorze_desc"];

			$velocidade_quinze_down = @$_REQUEST["velocidade_quinze_down"];
			$velocidade_quinze_desc = @$_REQUEST["velocidade_quinze_desc"];

			$acao = @$_REQUEST["acao"];
			$sop = @$_REQUEST["sop"];
			
	if ($acao != "" && $sop == "fazer" ){		

	if ($acao == "cadastrar" && $velocidade_um_down != "" && $sop == "fazer" ){
	
		$sSQL  = " BEGIN; ";
		$sSQL  .= " DELETE FROM cftb_banda; ";
	
		$sSQL .= "INSERT INTO ";
		$sSQL .= " cftb_banda (id, banda) ";
		$sSQL .= " VALUES ('$velocidade_um_down', '$velocidade_um_desc'); ";
		
		
		if ($velocidade_dois_down != ""){
		
			$sSQL .= "INSERT INTO ";
			$sSQL .= " cftb_banda (id, banda) ";
			$sSQL .= " VALUES ('$velocidade_dois_down', '$velocidade_dois_desc'); ";
			
			
		
		}
		
		if ($velocidade_tres_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_tres_down', '$velocidade_tres_desc'); ";
					 
					
		}
		
		if ($velocidade_quatro_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_quatro_down', '$velocidade_quatro_desc'); ";
					
					
		}
		
		if ($velocidade_cinco_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_cinco_down', '$velocidade_cinco_desc'); ";
					
					
		}
		
		if ($velocidade_seis_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_seis_down', '$velocidade_seis_desc'); ";
					
				
		}
		
		if ($velocidade_sete_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_sete_down', '$velocidade_sete_desc'); ";
					
				
		}
		
		if ($velocidade_oito_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_oito_down', '$velocidade_oito_desc'); ";
					
				
		}
		
		if ($velocidade_nove_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_nove_down', '$velocidade_nove_desc'); ";
					
				
		}
		
		if ($velocidade_dez_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_dez_down', '$velocidade_dez_desc'); ";
									
		}
		
		if ($velocidade_onze_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_onze_down', '$velocidade_onze_desc'); ";
					
				
		}
		
		if ($velocidade_doze_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_doze_down', '$velocidade_doze_desc'); ";
					
				
		}
		
		if ($velocidade_treze_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_treze_down', '$velocidade_treze_desc'); ";
					
				
		}
		
		if ($velocidade_catorze_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_catorze_down', '$velocidade_catorze_desc'); ";
					
				
		}
		
		if ($velocidade_quinze_down != ""){
				
					$sSQL .= "INSERT INTO ";
					$sSQL .= " cftb_banda (id, banda) ";
					$sSQL .= " VALUES ('$velocidade_quinze_down', '$velocidade_quinze_desc'); ";
					
				
		}
		
			$sSQL .= " COMMIT; ";
			
			$this->bd->consulta($sSQL);
			
		///echo $sSQL;
		
		$this->tpl->atribui("mensagem","Velocidades Alteradas com Sucesso!"); 
		$this->tpl->atribui("url","configuracao.php?op=listar_bandas");
		$this->tpl->atribui("target","_top");

		$this->arquivoTemplate = "msgredirect.html";
		return;
			
		
		}
		
		$this->tpl->atribui("alterar",$sop);
		

		
		}



	
	
	$this->arquivoTemplate = "configuracao_lista_banda.html";
	return;



	}else if ($op == "cidades"){
			if( ! $this->privPodeLer("_CONFIG_PREFERENCIAS") ) {
				$this->privMSG();
				return;
			}		


			$eSQL  = "SELECT ";
			$eSQL .= "   uf, estado ";
			$eSQL .= "FROM cftb_uf ";
			$eSQL .= "ORDER BY estado ";

			$lista_estados = $this->bd->obtemRegistros($eSQL);

			$this->tpl->atribui("lista_estados",$lista_estados);
			$city = @$_REQUEST['pesquisa'];
			$uf = @$_REQUEST['uf'];
			$acao = @$_REQUEST['acao'];
			//$erro = "";
			$mov = @$_REQUEST['mov'];

			$this->tpl->atribui("acao",$acao);

			if (!$city && !$uf){
			$dSQL  = "SELECT ";
			$dSQL .= "   id_cidade, uf, cidade, disponivel ";
			$dSQL .= "FROM cftb_cidade ";
			$dSQL .= "WHERE disponivel = 't'";
			$dSQL .= "ORDER BY cidade ";

			$erro = "";


			$lista_cidades = $this->bd->obtemRegistros($dSQL);
			$this->tpl->atribui("lista_cidades",$lista_cidades);

				if (!count($lista_cidades)){
					$erro = "nenhuma cidade disponivel no momento";
				}



			}




			if ( $city ) {



				$city = @$_REQUEST['pesquisa'];;

				$city = ereg_replace("[?????]","a",$city);
				$city = ereg_replace("[????]","A",$city);
				$city = ereg_replace("[???]","e",$city);
				$city = ereg_replace("[???]","E",$city);
				$city = ereg_replace("[?????]","o",$city);
				$city = ereg_replace("[????]","O",$city);
				$city = ereg_replace("[???]","u",$city);
				$city = ereg_replace("[???]","U",$city);
				$city = str_replace("?","c",$city);
				$city = str_replace("?","C",$city);
				//$city = ereg_replace(" ","",$city); 
				$city = strtoupper($city);


				if ( !$uf ){


				$cSQL  = "SELECT ";
				$cSQL .= "   id_cidade, uf, cidade, disponivel ";
				$cSQL .= "FROM cftb_cidade ";
				//$cSQL .= "WHERE nome ilike '%$city%'";
				$cSQL .= "WHERE cidade ilike '". str_replace("*","%",$city) ."' ";
				$cSQL .= "ORDER BY cidade ASC";



				$pesquisa_resultado = $this->bd->obtemRegistros($cSQL);

				$this->tpl->atribui("pesquisa_resultado",$pesquisa_resultado);
				$this->tpl->atribui("pesquisa",$city);
				$acao = "search";
				$this->tpl->atribui("acao",$acao);

				}else{


					$cSQL  = "SELECT ";
					$cSQL .= "   id_cidade, uf, cidade, disponivel ";
					$cSQL .= "FROM cftb_cidade ";
					//$cSQL .= "WHERE nome ilike '%$city%' AND uf = '$estado'";
					$cSQL .= "WHERE cidade ilike '". str_replace("*","%",$city) ."' AND uf = '$uf'";
					$cSQL .= "ORDER BY cidade ASC";

					$eSQL  = "SELECT ";
					$eSQL .= "   estado ";
					$eSQL .= "FROM cftb_uf ";
					$eSQL .= "WHERE uf = '$uf'";




					$pesquisa_resultado = $this->bd->obtemRegistros($cSQL);
					$nome_estado = $this->bd->obtemUnicoRegistro($eSQL);

					$this->tpl->atribui("nome_uf",$nome_estado["estado"]);
					$this->tpl->atribui("pesquisa_resultado",$pesquisa_resultado);
					$this->tpl->atribui("pesquisa",$city);
					$this->tpl->atribui("uf",$uf);
					$acao = "search";
					$this->tpl->atribui("acao",$acao);
				}


			}else if ( $uf ){

				$cSQL  = "SELECT ";
				$cSQL .= "   id_cidade, uf, cidade, disponivel ";
				$cSQL .= "FROM cftb_cidade ";
				$cSQL .= "WHERE uf = '$uf'";
				$cSQL .= "ORDER BY cidade ASC";

				$eSQL  = "SELECT ";
				$eSQL .= "   estado ";
				$eSQL .= "FROM cftb_uf ";
				$eSQL .= "WHERE uf = '$uf'";

				$pesquisa_resultado = $this->bd->obtemRegistros($cSQL);
				$nome_estado = $this->bd->obtemUnicoRegistro($eSQL);

				$this->tpl->atribui("pesquisa_resultado",$pesquisa_resultado);
				$this->tpl->atribui("nome_uf",$nome_estado["estado"]);
				$this->tpl->atribui("uf",$uf);
				$acao = "search";
				$this->tpl->atribui("acao",$acao);





				}


				if($mov == "cadastro"){
					if( ! $this->privPodeGravar("_CONFIG_PREFERENCIAS") ) {
						$this->privMSG();
						return;
					}						

					if (@$_REQUEST['disponivel']){			


					while(list($id,$valor)=each($_REQUEST['disponivel'])){

						$uSQL  = "UPDATE ";
						$uSQL .= "   cftb_cidade ";
						$uSQL .= "SET ";
						$uSQL .= "   disponivel = '$valor' ";
						$uSQL .= "WHERE ";
						$uSQL .= "   id_cidade = '$id' ";

						$this->bd->consulta($uSQL);

						$this->tpl->atribui("op","cidades");
						$this->tpl->atribui("acao","ok");

					}

					}
				}
			//$this->tpl->atribui("erro",$erro);
			global $_LS_ST_CIDADE;
			$this->tpl->atribui("lista_st_cidades",$_LS_ST_CIDADE);

			$this->arquivoTemplate = "configuracao_cadastro_cidades.html";







				}else if ($op == "monitor"){
					if( ! $this->privPodeLer("_CONFIG_MONITORAMENTO") ) {
						$this->privMSG();
						return;
					}						
				
				$this->arquivoTemplate = "cobranca_versaolight.html";

				}else if ($op == "preferencia"){
					if( ! $this->privPodeGravar("_CONFIG_PREFERENCIAS") ) {
						$this->privMSG();
						return;
					}										

					$acao = @$_REQUEST["acao"];



					$prefs = $this->prefs->obtem("geral");

					//echo "preferencia <br>";


					if ($acao == "alt"){
						//echo "alt";
						if(!count($prefs)){

							$sSQL  = "INSERT INTO ";
							$sSQL .= "  pftb_preferencia_geral ";						
							$sSQL .= "    (id_provedor) ";
							$sSQL .= "VALUES ";
							$sSQL .= "	('1')";

							$this->bd->consulta($sSQL);


							$sSQL  = "INSERT INTO ";
							$sSQL .= "  pftb_preferencia_cobranca ";						
							$sSQL .= "    (id_provedor) ";
							$sSQL .= "VALUES ";
							$sSQL .= "	('1')";

							$this->bd->consulta($sSQL);

							$sSQL  = "INSERT INTO ";
							$sSQL .= "  pftb_preferencia_provedor ";						
							$sSQL .= "    (id_provedor) ";
							$sSQL .= "VALUES ";
							$sSQL .= "	('1')";

							$this->bd->consulta($sSQL);

							//echo "SQL INSERT: $sSQL <br>";

							$this->bd->consulta($sSQL);

							$sSQL  = "SELECT * from cltb_cliente where id_cliente = '1'";
							//echo "SELECT CLIENTE: $sSQL <br>";

							$primeiro = $this->bd->obtemUnicoRegistro($sSQL);

							if (!count($primeiro)){

							//echo "primeiro registro <br>";

								$id_cliente = $this->bd->proximoID("clsq_id_cliente");

								$sSQL  = "INSERT INTO ";
								$sSQL .= "   cltb_cliente ( ";
								$sSQL .= "      id_cliente, data_cadastro, nome_razao, provedor, excluido ";
								$sSQL .= " )  VALUES (";
								$sSQL .= "     '" . $this->bd->escape($id_cliente) . "', ";
								$sSQL .= "     now(), ";
								$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
								$sSQL .= "     't', ";
								$sSQL .= "     'f' ";
								$sSQL .= "     )";									
								//echo "INSERT NO CLIENTE: $sSQL <br>";
								$this->bd->consulta($sSQL);

								$sSQL  = "INSERT INTO ";
								$sSQL .= "  dominio ";
								$sSQL .= "  (dominio, id_cliente, status, dominio_provedor,provedor) ";
								$sSQL .= "VALUES ";
								$sSQL .= "  ('".@$_REQUEST["dominio_padrao"]."', ";
								$sSQL .= "  '1', ";
								$sSQL .= "  'A', ";
								$sSQL .= "  true, true) ";
								//echo "INSERT NO DOMINIO: $sSQL <br>";
								$this->bd->consulta($sSQL);



							}



						}
						
						$sSQL  = " BEGIN; ";
						$sSQL .= " DELETE FROM pftb_preferencia_geral where id_provedor = '1'; ";
						$sSQL .= " INSERT INTO pftb_preferencia_geral ";
						$sSQL .= " (id_provedor, dominio_padrao , nome, radius_server, hosp_server, hosp_ns1, ";
						$sSQL .= " hosp_ns2, hosp_uid, hosp_gid, mail_server, mail_uid, mail_gid, pop_host, ";
						$sSQL .= " smtp_host, hosp_base, agrupar, email_base) ";
						$sSQL .= " VALUES ( ";
						$sSQL .= " '1', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["dominio_padrao"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["nome"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["radius_server"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["hosp_server"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["hosp_ns1"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["hosp_ns2"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["hosp_uid"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["hosp_gid"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["mail_server"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["mail_uid"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["mail_gid"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["pop_host"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["smtp_host"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["hosp_base"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["agrupar"]) . "', ";
						$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["email_base"]) . "' ";
						$sSQL .= " ); ";
						$sSQL .= " COMMIT; ";
						
						/////echo "SQL UPDATE: $sSQL <br>";
						$this->bd->consulta($sSQL);


						$this->tpl->atribui("mensagem","PREFERENCIAS GRAVADAS COM SUCESSO! "); 
						$this->tpl->atribui("url","configuracao.php?op=resumo");
						$this->tpl->atribui("target","_top");

						$this->arquivoTemplate = "msgredirect.html";

						return;


					}



					$this->tpl->atribui("op",$op);
					$this->tpl->atribui("acao",$acao);
					$this->tpl->atribui("prefs",$prefs);
					//$this->tpl->atribui("frm_pagamento",$frm_pagamento);
					$this->arquivoTemplate = "configuracao_preferencia.html";




				}else if ($op == "preferencia_cobranca"){
					if( ! $this->privPodeGravar("_CONFIG_PREFERENCIAS") ) {
						$this->privMSG();
						return;
					}										

					$acao = @$_REQUEST["acao"];

					$prefs = $this->prefs->obtem("cobranca");


					$sSQL  = "SELECT id_cobranca, nome_cobranca, disponivel FROM cftb_forma_pagamento ORDER BY id_cobranca asc";
					$frm_pagamento = $this->bd->obtemRegistros($sSQL);

					$sSQL = "SELECT * FROM cftb_forma_pagamento where disponivel = true";
					$frm = $this->bd->obtemRegistros($sSQL);
					
					$disponivel = array();
					
					for($i=0;$i<count($frm);$i++){
					
						if($frm[$i]["disponivel"] = "true") {
							$disponivel[$frm[$i]["id_cobranca"]] = "true";
						}
					
					
					
					}


					if ($acao == "alt"){
					
					$carencia = @$_REQUEST["carencia"];
					$tx_juros = @$_REQUEST["tx_juros"];
					$multa = @$_REQUEST["multa"];
					$dia_venc = @$_REQUEST["dia_venc"];
					$cod_banco = @$_REQUEST["cod_banco"];
					$carteira = @$_REQUEST["carteira"];
					$agencia = @$_REQUEST["agencia"];
					$num_conta = @$_REQUEST["num_conta"];
					$convenio = @$_REQUEST["convenio"];
					$pagamento = @$_REQUEST["pagamento"];
					$observacoes = @$_REQUEST["observacoes"];
					$path_contrato = @$_REQUEST["path_contrato"];
					$cod_banco_boleto = @$_REQUEST["cod_banco_boleto"];
					$carteira_boleto = @$_REQUEST["carteira_boleto"];
					$agencia_boleto = @$_REQUEST["agencia_boleto"];
					$conta_boleto = @$_REQUEST["conta_boleto"];
					$convenio_boleto = @$_REQUEST["convenio_boleto"];	
					$enviar_email = @$_REQUEST["enviar_email"];
					$mensagem_email = nl2br(@$_REQUEST["mensagem_email"]);
					$email_remetente = @$_REQUEST['email_remetente'];
					$cnpj_ag_cedente = @$_REQUEST["cnpj_ag_cedente"];
					$codigo_cedente = @$_REQUEST["codigo_cedente"];
					$operacao_cedente = @$_REQUEST["operacao_cedente"];
					$div_agencia = @$_REQUEST["div_agencia"];




					
					if ($enviar_email == ""){
					
						$enviar_email = 'f';
						$mensagem_email = "";
				
					}
					
					if ($carteira == ""){
										
						$carteira = 'NULL';
					}

					if ($cod_banco == ""){
										
						$cod_banco = 'NULL';
					}

					
					if ($agencia == ""){
										
						$agencia = 'NULL';
					}

					
					if ($carencia == ""){
					
						$carencia = 'NULL';
					}
					
					if ($num_conta == ""){

						$num_conta = 'NULL';
					}
					if ($convenio == ""){

						$convenio = 'NULL';
					}
					if ($pagamento == ""){

						$pagamento = 'NULL';
					}
					if ($observacoes == ""){

						$observacoes = 'NULL';
					}
					if ($path_contrato == ""){

						$path_contrato = 'NULL';
					}
					if ($cod_banco_boleto == ""){

						$cod_banco_boleto = 'NULL';
					}
					if ($carteira_boleto == ""){

						$carteira_boleto = 'NULL';
					}
					if ($agencia_boleto == ""){

						$agencia_boleto = 'NULL';
					}
					if ($conta_boleto == ""){

						$conta_boleto = 'NULL';
					}
					if ($convenio_boleto == ""){

						$convenio_boleto = 'NULL';
					}
					
					
						
						$sSQL  = " BEGIN ;";
						$sSQL .= " DELETE FROM pftb_preferencia_cobranca where id_provedor = '1';";
						$sSQL .= " INSERT INTO ";
						$sSQL .= " pftb_preferencia_cobranca ";
						$sSQL .= " (id_provedor, carencia, tx_juros, multa, dia_venc, cod_banco, carteira, ";
						$sSQL .= " agencia, num_conta, convenio, pagamento, observacoes, path_contrato, cod_banco_boleto, ";
						$sSQL .= " carteira_boleto, agencia_boleto, conta_boleto, convenio_boleto, enviar_email, mensagem_email, email_remetente, ";
						$sSQL .= " cnpj_ag_cedente, operacao_cedente, codigo_cedente, div_agencia )";
						$sSQL .= " VALUES ( '1', '$carencia', '$tx_juros', '$multa', '$dia_venc', $cod_banco, $carteira, ";
						$sSQL .= "			$agencia, $num_conta, $convenio, '$pagamento', '$observacoes', '$path_contrato', $cod_banco_boleto, ";
						$sSQL .= "			$carteira_boleto, $agencia_boleto, $conta_boleto, $convenio_boleto, '$enviar_email', '$mensagem_email', '$email_remetente' , '$cnpj_ag_cedente', '$operacao_cedente', '$codigo_cedente', '$div_agencia' ); ";
						$sSQL .= " COMMIT ; ";
						
						
						$this->bd->consulta($sSQL);
						//echo "update cobran?a: $sSQL <br>";
						


						$sSQL = "UPDATE cftb_forma_pagamento SET disponivel = 'f'";
						//echo "zerando fp: $sSQL <br>";

						$this->bd->consulta($sSQL);

						if (@$_REQUEST['disponivel']){

							while(list($id,$valor)=each($_REQUEST['disponivel'])){


								$uSQL  = "UPDATE ";
								$uSQL .= "   cftb_forma_pagamento ";
								$uSQL .= "SET ";
								$uSQL .= "   disponivel = '$valor' ";
								$uSQL .= "WHERE ";
								$uSQL .= "   id_cobranca = '$id' ";

								$this->bd->consulta($uSQL);
								//echo $uSQL ."<br>";			


							}	
						}

						$this->tpl->atribui("mensagem","PREFERENCIAS GRAVADAS COM SUCESSO! "); 
						$this->tpl->atribui("url","configuracao.php?op=resumo");
						$this->tpl->atribui("target","_top");

						$this->arquivoTemplate = "msgredirect.html";

						return;

					}
					
					$this->tpl->atribui("disponivel",$disponivel);
					$this->tpl->atribui("disponivel_1",@$disponivel[1]);
					$this->tpl->atribui("disponivel_2",@$disponivel[2]);
					$this->tpl->atribui("disponivel_3",@$disponivel[3]);
					$this->tpl->atribui("op",$op);
					$this->tpl->atribui("acao",$acao);
					$this->tpl->atribui("prefs",$prefs);
					$this->tpl->atribui("frm_pagamento",$frm_pagamento);
					$this->arquivoTemplate = "configuracao_preferencia_cobranca.html";



				}else if ($op == "preferencia_provedor"){
					if( ! $this->privPodeGravar("_CONFIG_PREFERENCIAS") ) {
						$this->privMSG();
						return;
					}										


					$acao = @$_REQUEST["acao"];

					$prefs = $this->prefs->obtem("provedor");

						if ($acao == "alt"){
						
							$sSQL  = " BEGIN; ";
							$sSQL .= " DELETE FROM pftb_preferencia_provedor WHERE id_provedor = '1'; ";
							$sSQL .= " INSERT INTO ";
							$sSQL .= " pftb_preferencia_provedor ";
							$sSQL .= " (id_provedor,endereco, localidade, cep , cnpj, fone ) ";
							$sSQL .= " VALUES ( ";
							$sSQL .= " '1', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["endereco"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["localidade"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["cep"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["cnpj"]) . "', ";
							$sSQL .= "     '" . $this->bd->escape(@$_REQUEST["fone"]) . "' ";
							$sSQL .= " ) ;";
							$sSQL .= " COMMIT; ";
							

							$this->bd->consulta($sSQL);
							
							///////echo $sSQL;

							$this->tpl->atribui("mensagem","PREFERENCIAS GRAVADAS COM SUCESSO! "); 
							$this->tpl->atribui("url","configuracao.php?op=resumo");
							$this->tpl->atribui("target","_top");

							$this->arquivoTemplate = "msgredirect.html";

							return;


						}



					$this->tpl->atribui("op",$op);
					$this->tpl->atribui("acao",$acao);
					$this->tpl->atribui("prefs",$prefs);
					//$this->tpl->atribui("frm_pagamento",$frm_pagamento);
					$this->arquivoTemplate = "configuracao_preferencia_provedor.html";


				}else if ($op == "contratos"){
					if( ! $this->privPodeGravar("_CONFIG_PREFERENCIAS") ) {
						$this->privMSG();
						return;
					}										

					$acao = @$_REQUEST["acao"];
					$tipo_contrato = @$_REQUEST["tipo_contrato"];
					$contrato = @$_REQUEST["contrato"];
					$hoje = date("dmY-His");

					global $_LS_TIPO_CONTRATO;
					$this->tpl->atribui("tipo_contrato",$_LS_TIPO_CONTRATO);


					$nome_arq = "contrato_padrao_".$tipo_contrato.".html";

					$_file_ = @$_FILES['contrato'];


					$arquivos = array();
					// vari?vel que define o diret?rio das imagens 
					$dir = "./contratos"; 

					// esse seria o "handler" do diret?rio 
					$dh = opendir($dir); 
					//$arquivos = readdir($dh);
					$path = "../template/default/images";



					// loop que busca todos os arquivos at? que n?o encontre mais nada 
					while (false !== ($filename = readdir($dh))) { 
						// verificando se o arquivo ? .html 
						if (substr($filename,-5) == ".html" && substr($filename,0,1)!= "_") { 

							// mostra o nome do arquivo e um link para ele - pode ser mudado para mostrar diretamente a imagem :) 
							$arquivos[] = "$filename"; 
							//$arquivos = array_push($arquivos, $filename);
							//echo $filename."<br>";
						} 
					} 

					$this->tpl->atribui("arquivos",$arquivos);
					$this->tpl->atribui("path",$path);





					if ($acao == "ok"){

						$extensao = $_file_["type"];
						//echo "HOJE: $hoje<br>";
						//echo "extensao: $extensao<br>";

						if ($extensao && $extensao != "text/html"){
							$_erro = "EXTENS?O DE ARQUIVO INV?LIDA.<br>S? ? PERMITIDO O ENVIO DE ARQUIVOS HTML";
							$this->tpl->atribui("erro",$_erro);
							$this->arquivoTemplate = "configuracao_upload_contrato.html";
							return;

						}else{

							$arqtmp = $_file_["name"];
							//$fd = fopen($arqtmp,"w");

							$diretorio = "/tmp";

							$nome_aceitavel = $nome_arq;
							$diretorio_destino = "./contratos";

							$arquivo = $diretorio_destino."/".$nome_aceitavel;

							$_name_ = $_file_['name'];
							$_tmp_name_ = $_file_['tmp_name'];


							if (file_exists($arquivo)) {
								//copy($_tmp_name,$diretorio."/_".$nome_aceitavel);
								rename($diretorio_destino."/".$nome_aceitavel, $diretorio_destino."/_".$nome_aceitavel);
							}

							copy($_tmp_name_,$diretorio_destino . "/" . $nome_aceitavel);

							$mensagem = "Arquivo Enviado com Sucesso";
							$this->tpl->atribui("mensagem",$mensagem);




						}






					}




					$this->arquivoTemplate = "configuracao_upload_contrato.html";



				}else if ($op == "registro"){
					if( ! $this->privPodeLer("_CONFIG_REGISTRO") ) {
						$this->privMSG();
						return;
					}										

					$acao = @$_REQUEST["acao"];
					$x = @$_REQUEST["x"];
					
					if ($x=="fulscreen"){
					
						$barra = "mostra";
						$this->tpl->atribui("barra",$barra);
					
					}
					

					$prefs = $this->prefs->obtem("geral");

					$this->arquivoTemplate = "registro.html";
					//$_LICENSE_EMPRESA = $prefs["nome"];
					$_LICENSE_EMPRESA = "";
					$_LICENSE_EXPIRES = "28/02/2007";

				   //$local_id = $_MBM_LOCAL_ID;

					if( $this->lic->isValid() ) {

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
							



						$this->tpl->atribui("status",$status);
					   $this->tpl->atribui("licenca",$this->lic->obtemLicenca());
					   $this->tpl->atribui("local_id_info",$this->lic->obtemInfoLocalID());
					   $this->arquivoTemplate = "licenca.html";


					}else {
						$registrado = "nao";


						$this->tpl->atribui("registrado",$registrado);
						$this->tpl->atribui("empresa",$_LICENSE_EMPRESA);
						$this->tpl->atribui("expires",$_LICENSE_EXPIRES);
						
						
						/**
						 * Informa??es sobre os poss?veis ID locais
						 */
						
						$this->tpl->atribui("local_id_info",$this->lic->obtemInfoLocalID());
						

					   //echo "registrado: $registrado<br>";

					   //$SELF = $PHP_SELF;

					}


					if ($acao == "upload"){

						if( ! $this->privPodeGravar("_CONFIG_REGISTRO") ) {
							$this->privMSG();
							return;
						}							

						$diretorio = "./etc";

						$nome_aceitavel = "virtex.lic";
						$_file_ = $_FILES["arquivo_registro"];

						$arquivo = $diretorio."/".$nome_aceitavel;

						$_name_ = $_file_['name'];
						$_tmp_name_ = $_file_['tmp_name'];

						if ($_name_ != $nome_aceitavel){

							$mensagem = "Tentativa de envio de arquivo incorreto.<br>NOME CERTO: $nome_aceitavel<br>NOME ENVIADO: $_name_<br>";
							//$tplVars .= ",mensagem";
							$this->tpl->atribui("mensagem",$mensagem);

							$this->tpl->atribui("url","configuracao.php?op=registro");
							$this->tpl->atribui("target","_self");

							$this->arquivoTemplate = "msgredirect.html";
							return;


						}


						//echo $diretorio ."<br>";
						//echo $_name_ ."<br>";
						//echo $arquivo ."<br>";

						if (file_exists($arquivo)) {
							//copy($_tmp_name,$diretorio."/_".$nome_aceitavel);
							rename($diretorio."/virtex.lic", $diretorio."/_virtex.lic");
						}

						copy($_tmp_name_,$diretorio . "/" . $nome_aceitavel);


						$mensagem = "Arquivo de registro aplicado com sucesso!";

						$this->tpl->atribui("mensagem",$mensagem);

						$this->tpl->atribui("url","home.php");
						$this->tpl->atribui("target","_self");

						$this->arquivoTemplate = "msgredirect.html";



					}




				}else if ($op == "externo"){
					if( ! $this->privPodeLer("_CONFIG_EQUIPAMENTOS") ) {
						$this->privMSG();
						return;
					}												
				

					$acao = @$_REQUEST["acao"];
					$id_nas = @$_REQUEST["id_nas"];
					$tipo_nas = @$_REQUEST["tipo_nas"];


					$sSQL  = "SELECT ";
					$sSQL .= "id_nas, ip_externo ";
					$sSQL .= "FROM ";
					$sSQL .= "cftb_ip_externo ";
					$sSQL .= "WHERE id_nas = '$id_nas' ";
					$sSQL .= "ORDER BY ip_externo ";

					$externos = $this->bd->obtemRegistros($sSQL);

					$sSQL  = "SELECT ";
					$sSQL .= "id_nas, nome, ip, tipo_nas, infoserver ";
					$sSQL .= "FROM ";
					$sSQL .= "cftb_nas ";
					$sSQL .= "WHERE ";
					$sSQL .= "id_nas = '$id_nas' ";

					$nas = $this->bd->obtemUnicoRegistro($sSQL);

					$this->tpl->atribui("nas",$nas);
					$this->tpl->atribui("externos",$externos);
					$this->tpl->atribui("id_nas",$id_nas);
					$this->tpl->atribui("tipo_nas",$tipo_nas);


					if ($acao == "novo"){
						if( ! $this->privPodeGravar("_CONFIG_EQUIPAMENTOS") ) {
							$this->privMSG();
							return;
						}												
						$rede = @$_REQUEST["rede"];

						if ($rede){

							$network = strpos($rede, "/");

							if ($network == false){
							//cadastra s? um IP

								$sSQL  = "INSERT INTO ";
								$sSQL .= "cftb_ip_externo ";
								$sSQL .= "(id_nas, ip_externo) ";
								$sSQL .= "VALUES ";
								$sSQL .= "('$id_nas','$rede') ";
								$this->bd->consulta($sSQL);

								$mensagem = "IP Externo cadastrado com sucesso!";

								$this->tpl->atribui("mensagem",$mensagem);

								$this->tpl->atribui("url","configuracao.php?op=externo&id_nas={$id_nas}&tipo_nas={$tipo_nas}&acao=novo");
								$this->tpl->atribui("target","_self");

								$this->arquivoTemplate = "msgredirect.html";
								RETURN;

							}else{
							//cadastra uma rede de IPs
							$this->cadastraExternos($id_nas,$rede);

							$mensagem = "Rede de IPs Externos cadastrada com sucesso!";

							$this->tpl->atribui("mensagem",$mensagem);

							$this->tpl->atribui("url","configuracao.php?op=externo&id_nas={$id_nas}&tipo_nas={$tipo_nas}&acao=novo");
							$this->tpl->atribui("target","_self");

							$this->arquivoTemplate = "msgredirect.html";
							return;




							}


						}



						$titulo = "Cadastrar";
						$this->tpl->atribui("titulo",$titulo);
						$this->arquivoTemplate = "configuracao_redes_externo_novo.html";
						return;

					}






					$this->arquivoTemplate = "configuracao_rede_externa.html";

					}else if ($op == "resumo"){
					if( ! $this->privPodeLer("_CONFIG_PREFERENCIAS") ) {
						$this->privMSG();
						return;
					}	
					
					$sSQL  =" SELECT id_cobranca,nome_cobranca, disponivel FROM cftb_forma_pagamento ";
					
 					$forma_pag = $this->bd->obtemRegistros($sSQL);


						/*
						 for($i=0;$i<count($forma_pag);$i++){

							$id_cob = $forma_pag[$i]['id_cobranca'];

							$dSQL  = " SELECT ";
							$dSQL .= " nome_cobranca, id_cobranca, disponivel ";
							$dSQL .= " FROM cftb_forma_pagamento ";
							$dSQL .= " WHERE ";
							$dSQL .= " id_cobranca = '$id_cob' ";

							echo $dSQL . "<br><Hr><br>" ;

							$formas = $this->bd->obtemRegistros($sSQL);

							$forma_pag[$i]["formas"] = $formas;


						 }*/
					
					
					$this->tpl->atribui("forma_pag",$forma_pag);
					

					$geral = $this->prefs->obtem("geral");
					$cobranca = $this->prefs->obtem("cobranca");
					$provedor = $this->prefs->obtem("provedor");

					$this->tpl->atribui("geral",$geral);
					$this->tpl->atribui("cobranca",$cobranca);
					$this->tpl->atribui("provedor",$provedor);

					$this->arquivoTemplate = "preferencia_resumo.html";
					return;						

				}else if ($op == "preferencia_monitor"){
				
						$acao = @$_REQUEST["acao"];
						
						///echo $acao;
						$sSQL = "SELECT * FROM pftb_preferencia_monitoracao WHERE id_provedor = 1";
						$monitor = $this->bd->obtemUnicoRegistro($sSQL);
						//echo "SQL: $sSQL<BR>";
						
						$emails = $monitor["emails"];
						$exibir_monitor = $monitor["exibir_monitor"];
						$alerta_sonoro = $monitor["alerta_sonoro"];
						$num_pings = $monitor["num_pings"];
						
						$this->tpl->atribui("emails",$emails);
						$this->tpl->atribui("num_ping",$num_pings);
						$this->tpl->atribui("exibir_monitor",$exibir_monitor);
						$this->tpl->atribui("alerta_sonoro",$alerta_sonoro);
						
						if ($acao == "alt"){
						
							$emails = @$_REQUEST["emails"];
							$exibir_monitor = @$_REQUEST["exibir_monitor"];
							$alerta_sonoro = @$_REQUEST["alerta_sonoro"];
							$num_pings = @$_REQUEST["num_ping"];
							
							
							$sSQL  = "UPDATE pftb_preferencia_monitoracao set emails = '$emails', ";
							$sSQL .= " num_pings = '$num_pings', ";
							
							if(!$exibir_monitor){
														

							$sSQL .= " exibir_monitor = 'f',  ";

							}else{

							$sSQL .= " exibir_monitor = 't' , ";

							}
							
							if(!$alerta_sonoro){
							
							
							$sSQL .= " alerta_sonoro = 'f'  ";
							
							}else{
							
							$sSQL .= " alerta_sonoro = 't'  ";
							
							}
							$sSQL .= "WHERE id_provedor = 1";
							
							$this->bd->consulta($sSQL);
							///echo "UPDATE: $sSQL<br>";
							$mensagem = "Prefer?ncias cadastradas com sucesso!";
														
							$this->tpl->atribui("mensagem",$mensagem);
							
							$this->tpl->atribui("url","home.php");
							$this->tpl->atribui("targ","_top");
							
							$this->arquivoTemplate = "msgredirect.html";
							return;

						
						
						
						
						}
						
						
						
						
						
						
						
						
						$this->arquivoTemplate = "configuracao_preferencia_monitoracao.html";
						
				
				
				}else if ($op == "links"){
				
				if( ! $this->privPodeLer("_LINKS") ) {
					$this->privMSG();
					return;
				}	

					$id_link = @$_REQUEST["id_link"];
					$sop = @$_REQUEST["sop"];
					$pp = @$_REQUEST["pp"];

					//Mostra os Links

					$sSQL = "SELECT * FROM cftb_links ORDER BY titulo";
					$lista_link = $this->bd->obtemRegistros($sSQL);
					
					if (!$lista_link){
						$mostrar='false';					
					}else{
						$mostrar='true';					
					}

					$this->tpl->atribui("lista",$lista_link);
					$this->tpl->atribui("mostrar",$mostrar);

					if ($id_link){
						$acao = "alt";
					} else {

						$acao = "cad";

					}	

					if (!$sop && ($acao == "alt"||$pp == "new")){
							if( ! $this->privPodeGravar("_LINKS") ) {
								$this->privMSG();
								return;
							}	

						//echo "ACAO: $acao<br>";
						$sSQL = "SELECT * FROM cftb_links WHERE id_link = $id_link";
						$link = $this->bd->ObtemUnicoRegistro($sSQL);

						$this->tpl->atribui("lista",$link);
						$this->tpl->atribui("acao",$acao);
						$this->tpl->atribui("op",$op);
						$this->arquivoTemplate = "configuracao_cadastro_links.html";
						return;

				}

				if ($sop == "ok"){

					if( ! $this->privPodeGravar("_LINKS") ) {
						$this->privMSG();
						return;
					}	

					$titulo = @$_REQUEST["titulo"];
					$url = @$_REQUEST["url"];
					$target = @$_REQUEST["target"];
					$descricao = @$_REQUEST["descricao"];


					if ($acao == "alt") {

						if($pp == "excluir"){

							$sSQL = "DELETE FROM cftb_links WHERE id_link = $id_link";
							$this->bd->consulta($sSQL);

						}else{

							$sSQL = "UPDATE cftb_links SET titulo = '$titulo', url = '$url', target = '$target', descricao = '$descricao' WHERE id_link = $id_link";
							$this->bd->consulta($sSQL);

						}
						
					}else if ($acao == "cad"){

						$sSQL = "INSERT INTO cftb_links (titulo,url,target,descricao) VALUES ('$titulo','$url','$target','$descricao') ";
						$this->bd->consulta($sSQL);


					}
				//echo "SQL: $sSQL <br>";
				$sSQL = "SELECT * FROM cftb_links ORDER BY titulo";
				$lista_link = $this->bd->obtemRegistros($sSQL);

				$this->tpl->atribui("lista",$lista_link);

				}

				$this->arquivoTemplate = "configuracao_links_lista.html";

				}// $ops


		}// fecha function processa()
			
	public function __destruct() {
			parent::__destruct();
	}
		
	private function cadastraRede($rede,$tipo_rede) {
			
			
		$id_rede = $this->bd->proximoID("cfsq_id_rede");
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "   cftb_rede ( ";
		$sSQL .= "      rede,tipo_rede,id_rede ";
		$sSQL .= "   ) VALUES ( ";
		$sSQL .= "      '$rede','$tipo_rede','$id_rede' ";
		$sSQL .= "   )";
		$sSQL .= "";
		
		$this->bd->consulta($sSQL);
		//echo "CADASTRAREDE: $sSQL <br>";
		
		return($rede);
		
	}
		
	// Altera o tipo de uma rede
	private function alteraRede($rede,$tipo_rede) {
		  
		
		$sSQL  = "UPDATE ";
		$sSQL .= "   cftb_rede ";
		$sSQL .= "SET ";
		$sSQL .= "   tipo_rede = '$tipo_rede' ";
		$sSQL .= "WHERE ";
		$sSQL .= "   rede = '$rede' ";
		$sSQL .= "";
		$sSQL .= "";
		
		$this->bd->consulta($sSQL);
		       		
		/*if( $this->bd->obtemErro() != MDATABASE_OK ) {
			echo "ERRO: " . $this->bd->obtemMensagemErro() , "<br>\n";
			echo "QUERY: " . $sSQL . "<br>\n";
													
		}*/

		       		
		
	}
		    
	// Vincula uma rede a um nas.
	private function vinculaRede($id_nas,$rede) {
		    
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "   cftb_nas_rede ( ";
		$sSQL .= "      id_nas, rede ";
		$sSQL .= "   ) VALUES ( ";
		$sSQL .= "      '$id_nas', '$rede' ";
		$sSQL .= "   );";
		$sSQL .= "";
		$sSQL .= "";
		
		$this->bd->consulta($sSQL);
		
		return;
		
	}
		
	// Cadastra um ip no sistema
	private function cadastraIP($ipaddr) {
		  
		
		$sSQL  = "INSERT INTO ";
		$sSQL .= "   cftb_ip ( ";
		$sSQL .= "      ipaddr ";
		$sSQL .= "   ) VALUES ( ";
		$sSQL .= "      '$ipaddr' ";
		$sSQL .= "   )";
		
		$this->bd->consulta($sSQL);
		
		return($ipaddr);
		
    }
    
    private function cadastraExterno($id_nas,$rede){
    //cadastra 1 ip externo
    
    	$sSQL  = "INSERT INTO ";
    	$sSQL .= "cftb_ip_externo ";
    	$sSQL .= "(id_nas, ip_externo) ";
    	$sSQL .= "VALUES ";
    	$sSQL .= "('$id_nas','$rede')";
    	
    	$this->bd->consulta($sSQL);
    	
    	return;
    
    
    }
    
    private function cadastraExternos($id_nas,$rede){
    	$_rede = new RedeIp($rede);
    	$ips = $_rede->listaIPs();
    	
    	for($x=0;$x<count($ips);$x++){
    		$this->cadastraExterno($id_nas,$ips[$x]);
    	
    	}
    
    }

	// Cadastra todos os ips de uma rede no sistema.
    private function cadastraIPs($rede) {

    	$_rede = new RedeIP($rede);
    	$ips = $_rede->listaIPs();

    	for($x=0;$x<count($ips);$x++) {
          
			// Insere no BD
			$this->cadastraIP($ips[$x]);
   	    		
   	    		
   	    }
   	}		

	private function cadastraSubRedes($id_nas,$rede_origem,$rede_inicial,$bits_subredes,$num_redes,$tipo_rede) {
		
		$_rede_origem = new RedeIP($rede_origem);
		$_rede_inicio = new RedeIP($rede_inicial);
		
		$_subredes = $_rede_origem->listaSubRedes($bits_subredes);
		
		$conta = 0;
		
		$comecou = false;
	
		for($x=0;$x<count($_subredes);$x++) {
		
			if( RedeIP::bin2addr($_subredes[$x]->network) == RedeIP::bin2addr($_rede_inicio->network) ){
				$comecou = true;
			}
	
			if( $comecou ) {
				$rede = RedeIP::bin2addr($_subredes[$x]->network) . "/". $bits_subredes;
				$this->cadastraRede($rede,$tipo_rede);
				$this->vinculaRede($id_nas,$rede);
				$conta++;
			}
	
			if( $conta == $num_redes ) {
				break;
			}
			
		}
		
	}





}// fecha classe VirtexAdmin




?>
