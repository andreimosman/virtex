<?

require_once( PATH_LIB . "/VirtexAdmin.class.php" );
require_once( PATH_LIB . "/ICClient.class.php" );
require_once( PATH_LIB . "/ICHostInfo.class.php");

class VASuporte extends VirtexAdmin {

	public function VASuporte() {
		parent::VirtexAdmin();
	}

	// metodo para pegar as propriedadas enviadas via menu na interface.
/*	public function obtemRede(){
	
	}*/
	
	 protected function obtemListaMonitorPOPs($id_pop="",$nivel=0) {

		$sSQL  = "SELECT ";
		$sSQL .= "    p.id_pop_ap, p.nome, p.ipaddr, p.ativar_monitoramento, p.tipo, p.id_pop,  t.min_ping , t.max_ping ,t.media_ping ,t.num_perdas ,t.num_ping ,t.status ,t.num_erros, t.laststats ";
		$sSQL .= "FROM ";
		$sSQL .= "   cftb_pop p LEFT OUTER JOIN sttb_pop_status t USING(id_pop) ";
		$sSQL .= "WHERE ";

		if( $id_pop ) {
			$sSQL .= "   id_pop_ap = '".$this->bd->escape($id_pop)."' ";
		} else {
			$sSQL .= "   id_pop_ap is null ";
		}

		//$sSQL .=" AND  t.id_pop = p.id_pop ORDER BY p.nome ";
		$sSQL .= "ORDER BY ";
		$sSQL .= "   p.nome ";

	   $lista = $this->bd->obtemRegistros($sSQL);

	   $retorno = array();

	   for($i=0;$i<count($lista);$i++) {
		   $lista[$i]["nivel"] = $nivel;
		   $retorno[] = $lista[$i];
		   $sub = $this->obtemListaMonitorPOPs($lista[$i]["id_pop"],$nivel+1);

		   for($x=0;$x<count($sub);$x++) {
			   $retorno[] = $sub[$x];
		   }
	   }

	   return($retorno);

	}
	
	public function processa($op=null) {	
		if( ! $this->privPodeLer("_SUPORTE") ) {
					$this->privMSG();
					return;
		}		
	
	
		if($op == "graf"){	
		
			$pesquisa = @$_REQUEST["pesquisa"];
			$rotina = @$_REQUEST["rotina"];
			$tipo_pesquisa = @$_REQUEST["tipo_pesquisa"];		
		
			$sSQL  = "SELECT ";
			$sSQL .= "id_pop, nome ";
			$sSQL .= "FROM ";
			$sSQL .= "cftb_pop ";
			$sSQL .= "WHERE status = 'A' ";
			$sSQL .= "ORDER BY nome ASC ";
			
			$pops = $this->bd->obtemRegistros($sSQL);

			$aSQL  = "SELECT ";
			$aSQL .= "id_nas, nome ";
			$aSQL .= "FROM ";
			$aSQL .= "cftb_nas ";
			$aSQL .= "ORDER BY nome ASC ";

			$nas = $this->bd->obtemRegistros($aSQL);
			
			
			if ($pesquisa)
			
			$tipo_pesquisa = @$_REQUEST["tipo_pesquisa"];

			if (($tipo_pesquisa == "pop") && ($pesquisa!="")){
				
				if ($pesquisa == "todos"){

					$aSQL = " SELECT id_pop, nome FROM cftb_pop  ORDER BY id_pop ";

					$num_todos = $this->bd->obtemRegistros($aSQL);

					for ($i=0;$i<count($num_todos);$i++){

						$id_pop = $num_todos[$i]["id_pop"];

						$sSQL  = "SELECT ";
						$sSQL .= "username, tipo_conta, dominio, id_pop, mac, download_kbps, upload_kbps, ipaddr , rede  ";
						$sSQL .= "FROM ";
						$sSQL .= "cntb_conta_bandalarga ";
						$sSQL .= " WHERE id_pop = '$id_pop' ";

						////////////echo $sSQL . "<br>\n<hr>\n";

						$user_pop = $this->bd->obtemRegistros($sSQL);
						$num_todos[$i]["usuarios"] = $user_pop;

					}

					$this->tpl->atribui("num_todos",$num_todos);
					

				}else{
			
					$sSQL  = "SELECT ";
					$sSQL .= "username, tipo_conta, dominio, id_pop, mac, download_kbps, upload_kbps, ipaddr , rede  ";
					$sSQL .= "FROM ";
					$sSQL .= "cntb_conta_bandalarga ";
					$sSQL .= "WHERE id_pop = '".$_REQUEST["pesquisa"]."'";

					$cli = $this->bd->obtemRegistros($sSQL);
					$this->tpl->atribui("cli",$cli);

				}

			}

			else if (($tipo_pesquisa == "nas")&& ($pesquisa!="")){


				if ($pesquisa == "todos"){
				
					$aSQL = " SELECT id_nas, nome FROM cftb_nas ";

					$num_todos = $this->bd->obtemRegistros($aSQL);

					
					for ($i=0;$i<count($num_todos);$i++){

						$id_nas = $num_todos[$i]["id_nas"];

						$sSQL  = "SELECT ";
						$sSQL .= "username, tipo_conta, dominio, id_nas, mac, download_kbps , upload_kbps, ipaddr , rede ";
						$sSQL .= "FROM ";
						$sSQL .= "cntb_conta_bandalarga ";
						$sSQL .= " WHERE id_nas = '$id_nas' ";

					///echo $sSQL . "<br>\n<hr>\n";

						$user_nas = $this->bd->obtemRegistros($sSQL);
						$num_todos[$i]["usuarios"] = $user_nas;


					}

					$this->tpl->atribui("num_todos",$num_todos);

				}else{
			
					$sSQL  = "SELECT ";
					$sSQL .= "username, tipo_conta, dominio, id_nas, mac, download_kbps , upload_kbps, ipaddr , rede ";
					$sSQL .= "FROM ";
					$sSQL .= "cntb_conta_bandalarga ";
					$sSQL .= "WHERE id_nas = '".$_REQUEST["pesquisa"]."'";

					$cli = $this->bd->obtemRegistros($sSQL);
					$this->tpl->atribui("cli",$cli);

			   }

			}



				///echo $sSQL;
							
				
			
			$this->tpl->atribui("pops",$pops);
			$this->tpl->atribui("nas",$nas);
			$this->tpl->atribui("pesquisa",$pesquisa);
			$this->tpl->atribui("tipo_pesquisa",$tipo_pesquisa);
			
			
			if($rotina == "mostra"){
			
			$this->arquivoTemplate = "popup_grafico.html";
			return;
			
			}
		
		
			//$this->arquivoTemplate = "cobranca_versaolight.html";
			$this->arquivoTemplate = "suporte_grafico.html";
		}else if ($op == "monitoramento"){
			
			if( !$this->privPodeLer("_SUPORTE_MONITORAMENTO") ) {
				$this->privMSG();
				return;
			}	
			
			$tipo_lista = @$_REQUEST['tipo_lista'];
			
			if ($tipo_lista =="mostrar" || $tipo_lista ==""){
			
				$mostra_tudo = 'false';
				
			}else if($tipo_lista =="nao_mostrar"){
			
				$mostra_tudo = 'true';
				
			}

			$rel_monitoramento = $this->obtemListaMonitorPOPs();
			$this->tpl->atribui("rel_monitoramento",$rel_monitoramento);
			$this->tpl->atribui("mostra_tudo",$mostra_tudo);
			$this->arquivoTemplate = "suporte_monitoramento_pops.html";	

		}
		else if ($op == "log"){
		
			//$this->arquivoTemplate = "cobranca_versaolight.html";
			$this->arquivoTemplate = "suporte_radiuslog.html";
			
			$limite = @$_REQUEST["limite"];
			$username = @$_REQUEST["username"];
			$op = @$_REQUEST["op"];
			$erros = @$_REQUEST["erros"];
			
			if(!$limite) $limite = 50;
			

			$sSQL  = "SELECT ";
			$sSQL .= "username as usuario, ";
			$sSQL .= "to_char(login,'DD/MM/YYYY HH24:MI:SS') as inicio, ";
			$sSQL .= "to_char(logout,'DD/MM/YYYY HH24:MI:SS') as fim, "; 
			$sSQL .= " CAST (logout - login as time) as tempo,  ";
			$sSQL .= " caller_id as origem, session_id, ";
			$sSQL .= "terminate_cause as mensagem, bytes_in, bytes_out ";
			$sSQL .= "FROM ";
			$sSQL .= "	rdtb_accounting ";
			if($username) {
				$sSQL .= "WHERE ";
				$sSQL .= "	username LIKE '$username' ";
			}
			if ($erros == "sim" && $username){

				$sSQL .= " AND session_id ilike 'E:%' "; 

			}

			if ($erros == "sim" && !$username){
			
				$sSQL .= " WHERE ";
				$sSQL .= " session_id ilike 'E:%' "; 
			
			}
			//$sSQL .= "ORDER BY CASE WHEN logout is NULL then login ELSE logout END DESC ";
			$sSQL .= "ORDER BY login DESC ";
			$sSQL .= "LIMIT $limite ";
			
			//echo $sSQL;			
			
			$relat = $this->bd->obtemRegistros($sSQL);
			
			for ($i=0; $i<count($relat); $i++) {
				
				@list($tipo, $lixo) = explode(":",$relat[$i]["session_id"]);
				/*
				if ($tipo == "E") $relat[$i]["tipo_ng"] = "#E08E8E";
				else if ($tipo == "A") $relat[$i]["tipo_ng"] = "#E0BB8E";
				else if ($tipo == "I") $relat[$i]["tipo_ng"] = "#8E94E0";
				else $relat[$i]["tipo_ng"] = "#8EE098";
				*/
				
				if ($tipo == "E") $relat[$i]["tipo_ng"] = "#F2C6C6";
				else if ($tipo == "A") $relat[$i]["tipo_ng"] = "#F2DEC6";
				else if ($tipo == "I") $relat[$i]["tipo_ng"] = "#D8E8F3";
				else $relat[$i]["tipo_ng"] = "#D1F2C6";
				
				$username_id = $relat[$i]["usuario"];
				
				$dSQL  =" SELECT ";
				$dSQL .=" id_cliente FROM ";
				$dSQL .=" cntb_conta ";
				$dSQL .=" WHERE ";
				$dSQL .=" username = '$username_id' ";
				$dSQL .=" AND tipo_conta not ilike 'E' ";

				$id_cliente = $this->bd->obtemRegistros($dSQL);

				$relat[$i]["id_cliente"] = $id_cliente;
				
			}
			
						
			$this->tpl->atribui("op",$op);
			$this->tpl->atribui("relat", $relat);
			$this->tpl->atribui("tipo_pesq", $erros);
			$this->tpl->atribui("username", $username);
			$this->tpl->atribui("limite", $limite);
			
			//echo $sSQL;
			
		}else if($op == "monitoramento_barra"){
		
			$aSQL  = " SELECT " ;
			$aSQL .= " alerta_sonoro ";
			$aSQL .= " FROM pftb_preferencia_monitoracao ";

			$alerta = $this->bd->obtemUnicoRegistro($aSQL) ;

			///echo $alerta['alerta_sonoro'];
			
			$this->tpl->atribui("alerta_sonoro",$alerta['alerta_sonoro']);
			
		
			$sSQL  = "SELECT ";
			$sSQL .= " count(status) as num, status ";
			$sSQL .= " FROM sttb_pop_status ";
			$sSQL .= " GROUP BY status ";
			$r= $this->bd->obtemRegistros($sSQL);
			
			$erros=0;
			$alertas=0;
			$ok=0;
			for($i=0;$i<count($r);$i++) {
				switch($r[$i]["status"]) {
					case "OK":
						$ok=$r[$i]["num"];
						break;
					case "ERR":
						$erros=$r[$i]["num"];
						break;
					case "WRN":
						$alertas=$r[$i]["num"];
						break;
				}
			}
			
			$num_pops = $erros+$alertas+$ok;
			
		
			$this->tpl->atribui("erros",$erros);
			$this->tpl->atribui("alertas",$alertas);
			$this->tpl->atribui("ok",$ok);
			$this->tpl->atribui("num_pops",$num_pops);
		
			$this->arquivoTemplate = "suporte_monitoramento_barra.html";
		
		
		
		
		
		
		
		
		
		} else if ($op == "monit"){
			$this->arquivoTemplate = "cobranca_versaolight.html";
			//$this->arquivoTemplate = "suporte_monitoramento.html";
		} else if ($op == "calc"){
			$this->arquivoTemplate = "suporte_calculadoraip.html";

			$netmask = @$_REQUEST["netmask"];
			// adicionado pelo grande Hugo.. retira a / caso tenham colocado no campo mascara
			$netmask = substr(strrchr($netmask, "/"), 1);
			
			$ip = @$_REQUEST["ip"];

			if( !$netmask ) $netmask = 24;

			if( $ip ) {

				if( strlen($netmask) <= 2 ) {
					$oip = new RedeIP($ip."/".$netmask);
				} else {
					$oip = new RedeIP($ip,$netmask);
				}

				$ipaddr = array(
								"endereco" => bin2addr($oip->addr),
								"mascara"  => bin2addr($oip->mask),
								"wildcard" => bin2addr($oip->wildcard),
								"nw"       => bin2addr($oip->network),
								"bc"       => bin2addr($oip->broadcast),
								"maxhost"  => $oip->maxHost(),
								"minhost"  => $oip->minHost(),
								"numhosts" => $oip->numHosts()
								);

				$this->tpl->atribui("ip",$ip);
				$this->tpl->atribui("netmask",$netmask);
				$this->tpl->atribui("ipaddr",$ipaddr);

			}



		} else if ($op == "arp"){
			$this->arquivoTemplate = "suporte_arp.html";
			
			$host_info = new ICHostInfo();
			$hosts = $host_info->obtemListaServidores();
			//echo $hosts;

			$this->tpl->atribui("hosts",$hosts);

			$host = @$_REQUEST["host"];
			$ip = @$_REQUEST["ip"];
			
			$arp=array();
			
			if( $ip ) {
				$ich = new ICHostInfo();
				$icc = new ICClient();
				
				$hosts = $ich->obtemListaServidores();
				
				$arp = array();  
				
				for($i=0;$i<count($hosts);$i++) {
					if( $host && $host != $hosts[$i] ) continue;
					
					$info = $ich->obtemInfoServidor($hosts[$i]);
					
					if(!$icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {
						continue;
					}
					
					$arp[] = array("host"=>$hosts[$i], "tabela" => $icc->getARP($ip) );
					
				}

				
			}
			
			$this->tpl->atribui("ip",$ip);
			$this->tpl->atribui("arp",$arp);
			$this->tpl->atribui("op",$op);
			$this->tpl->atribui("host",$host);
			
			
		}else if ($op == "help_desk"){
			$this->arquivoTemplate = "cobranca_versaolight.html";
		
		}else if ($op == "ping") {
		
			$this->arquivoTemplate = "suporte_ping.html";
			
			$ping_limite = 20;
			$ping_max_pkg = 1400;
			
			$host_info = new ICHostInfo();
			$hosts = $host_info->obtemListaServidores();
			//echo $hosts;

			$this->tpl->atribui("hosts",$hosts);
				
			$erros = array();
			
			
			$tamanho = @$_REQUEST["tamanho"];
			$ip = @$_REQUEST["ip"];
			$pacotes = @$_REQUEST["pacotes"];
			$acao = @$_REQUEST["acao"];
			$extra = @$_REQUEST["extra"];
			$op = @$_REQUEST["op"];
			$host = @$_REQUEST['host'];
			///echo $host;
			
			
			if(!$tamanho || $tamanho < 1) $tamanho = 32;
			if(!$pacotes || $pacotes < 1) $pacotes = 4;


			if($acao == "ping") {
						
				if($pacotes > $ping_limite) $erros[] = "O limite máximo de pacotes enviados por esta operação é de $ping_limite pacotes";
				if($tamanho > $ping_max_pkg) $erros[] = "O tamanho máximo dos pacotes desta operação é de $ping_max_pkg bytes";
				if(!$ip || trim($ip) == "") $erros[] = "Não foi especificado um IP para esta operação";
				
				$this->tpl->atribui("erros", $erros);
			}
			
			
			$this->tpl->atribui("tamanho", $tamanho);
			$this->tpl->atribui("pacotes", $pacotes);
			$this->tpl->atribui("ip", $ip);
			$this->tpl->atribui("op", $op);
			$this->tpl->atribui("acao", $acao);
			//$this->tpl->atribui("ping_list", $ping_list);
			
			
			if ($extra == "ping") {
				header("pragma: no-cache");
				header("connection: keep-state");
				echo "<br><div style='padding-left=70px;'><font face='courier' size=-2>\n";
				if (!count($erros)) {			
					$ich = new ICHostInfo();
					$icc = new ICClient();



					$info = $ich->obtemInfoServidor($host);

					if(!@$icc->open($info["host"],$info["port"],$info["chave"],$info["username"],$info["password"])) {

						if (!$icc->estaConectado() ){

							echo "<br><Br><div align=center><strong><font color='#000000' face='verdana'>Não foi possível conectá-lo ao servidor " . $host . ".</font></strong></div><font color='#FFFFFF'>";
						

						}

						continue;

					}

					$dados = $icc->getFPING($ip,$pacotes,$tamanho) ;
					
					echo "PING ".$ip." (".$ip.") ".$tamanho." bytes de dados. <br>";
					$counter="0";
					$counter_received="0";
					$counter_loss="0";
					$tempo = '0';

					for($i=0; $i<count($dados); $i++){

						if (($dados[$i] != '-') && ($dados[$i] >0) && ($dados[$i] !="-") && ($dados[$i] !="") ){

							echo $tamanho . " bytes para " . $ip . ": icmp_seq=".$i." tempo=".trim($dados[$i])." ms <br>\n" ;
							$counter++;
							$counter_received++;
							$tempo += $dados[$i];

						}else{

							echo "tempo esgotado.<br>\n";
							$counter++;
							$counter_loss++;
							$tempo += '754.25';
						}


					}

					$percent = ((($counter_loss)*100)/$counter) ;

					if ($counter){

						echo "<br>" .  $counter ." pacotes enviados, " .$counter_received. " recebidos, " .$percent. "% perdidos, tempo " .$tempo . "ms<br> Pacotes enviados pelo servidor " .$host ."(".$info['host'] .")";
							
					}					
				
				
				} else {
					
					for($i=0; $i<count($erros); $i++) echo "$erros[$i]<br>";
					
				}
		
			echo "</font></div>";

			$this->arquivoTemplate = "";
			return;

		} 
				
		}else if($op == "backup") {
			if( ! $this->privPodeGravar("_SUPORTE_BACKUP") ) {
				$this->privMSG();
				return;
			}		
						
			
			$configuracao = @$_REQUEST["configuracao"];
			$bd = @$_REQUEST["bd"];
			$sistema = @$_REQUEST["sistema"];
			$hoje = DATE("d-m-Y-H-i-s");
			//ECHO $hoje ."<br>";
			$acao = @$_REQUEST["acao"];
			$op = @$_REQUEST["op"];
			$sop = @$_REQUEST["sop"];
			$msg = "";
			
			
			//echo "acao: $acao<br>op: $op<br>sop: $sop<br>";
			
			
			if ($acao == "backup") {
			
				//echo "acao <br>";
				$erro = 0;
			
				if ($sop == "ok"){
				
				//echo "ok<br>";
					if($bd){
					//echo "banco<br>";

						system('pg_dump --disable-triggers -U virtex > /mosman/backup/bd_'.$hoje.'.sql', $retvalbd);
						
						if ($retvalbd != 0){
						
							$msg .= "BANCO DE DADOS: <B>ERRO</B><BR>";
							$erro = 1;
						
						}else{
						
							$msg .= "BANCO DE DADOS: <B>OK</B><BR>";
							
						}
						
						
						//$msg .= $retvalbd."<br>";


					}
					if($configuracao){
					
						$pathbackup = " /mosman/backup/";
					
						$nome1 = "etc_$hoje.tgz";
						$nome2 = "appetc_$hoje.tgz";
						
						$caminho1 = " /mosman/virtex/etc/";
						$caminho2 = " /mosman/virtex/app/etc/";
						
						$comando1 = "tar -czvf $pathbackup$nome1  $caminho1";
						$comando2 = "tar -czvf $pathbackup$nome2  $caminho2";
						
						
						
						//ECHO "comando1: $comando1<br>";						
						//ECHO "comando2: $comando2<br>";
					
						system($comando1,$retvalconf1);
						system($comando2,$retvalconf2);
						
						if ($retvalconf1 != 0 || $retvalconf2 != 0){
							$msg .= "ARQ. DE CONFIGURAÇÕES: <B>ERRO</B><BR>";
							$erro = 1;
						}else{
							$msg .= "ARQ. DE CONFIGURAÇÕES: <B>OK</B><BR>";
						}
						
						
					}
					if($sistema){
					
						$pathbackup = " /mosman/backup/";
										
						$nome = "virtex_$hoje.tgz";
						$caminho = " /mosman/virtex/";
					
						$comando = "tar -czvf $pathbackup$nome  $caminho";
					
						system($comando,$retvalsystem);
						
						//echo "comando: $comando<br>";
						
						//copy("/mosman/virtex/app/virtex_".$hoje.".tgz","/mosman/backup/sys/virtex_".$hoje.".tgz");
						//$msg .= $retvalsystem."<br>";
						if ($retvalsystem != 0){
						
							$msg .="ARQ. DO SISTEMA: <B>ERRO</B><BR>";
							$erro = 1;
						
						}else{
						
							$msg .= "ARQ. DO SISTEMA: <B>OK</B><BR>";
						
						}
					}
					
					if ($erro == 1) {
						$result = "ERROS";
					}else{
						$result = "SUCESSO";
					}					
					
					
					$command = "tar -czvf /mosman/virtex/app/backup-$hoje.tgz *.tgz";
					
					system($command,$retval);
					
					$link = "/mosman/backup/backup-$hoje.tgz";
					$mensagem = "BACKUP EFETUADO COM $result!!<BR>".$msg."<a href=$link>FAZER O DOWNLOAD DO BACKUP EFETUADO</a>";
					$this->tpl->atribui("mensagem",$mensagem);
					$this->tpl->atribui("url","home.php");
					$this->tpl->atribui("targ","_top");
					$this->arquivoTemplate = "msgredirect.html";
					return;
			
				}
			
			

				
			
			
			
			}
			
			
			
			$this->arquivoTemplate="suporte_backup.html";
			
			
	
		}else if ($op == "extrato"){

			$mes = date('n');
			$ano = date('Y');

			global $_LS_MESES_ANO;

			//echo $_LS_MESES_ANO[$data];

			$this->tpl->atribui("meses",$_LS_MESES_ANO);
			$this->tpl->atribui("mes",$mes);
			$this->tpl->atribui("ano",$ano);


			$tipo_conta = @$_REQUEST["tipo_conta"];
			$acao = @$_REQUEST["acao"];
			$valor_pesquisa = @$_REQUEST["valor_pesquisa"];
			$periodo = @$_REQUEST["periodo"];

			if (!$periodo){

				$ano = date("Y");
				$mes = date("m");


			}

			if (!$acao && $valor_pesquisa){
			
				$sSQL  = "SELECT ";
				$sSQL .= "username as usuario, ";
				$sSQL .= "to_char(login,'DD/MM/YYYY HH24:MI:SS') as inicio, ";
				$sSQL .= "to_char(logout,'DD/MM/YYYY HH24:MI:SS') as fim, "; 
				$sSQL .= " CAST (logout - login as time) as tempo,  ";
				$sSQL .= " caller_id as origem, session_id, ";
				$sSQL .= "terminate_cause as mensagem, bytes_in, bytes_out ";
				$sSQL .= "FROM ";
				$sSQL .= "	rdtb_accounting ";
				$sSQL .= " WHERE " ;
				$sSQL .= " username = '$valor_pesquisa' "; 
				$sSQL .= " AND logout is not null ";
				$sSQL .= " AND tempo > 0 ";
				$sSQL .= " AND session_id not ilike '%:%' ";
				$sSQL .= " AND EXTRACT('month' FROM login) = '$mes' ";
				$sSQL .= " AND EXTRACT('year' FROM login) = '$ano' ";
				
				//////////echo $sSQL;

				if ($tipo_conta ==	"D" ){

				$sSQL .= " AND caller_id not ilike '%:%:%:%:%:%' ";

				}

				if ($tipo_conta ==	"BL" ){

				$sSQL .= " AND caller_id ilike '%:%:%:%:%:%' ";

				}


				$sSQL .= " ORDER BY inicio DESC " ; 

				$extrato = $this->bd->obtemRegistros($sSQL);
				


				//////////echo $sSQL ; 
				///echo $acao;

			
			}


			else if ($acao == "pesquisar"){

			@list($mes,$ano) = explode("/",$periodo);

			$sSQL  = "SELECT ";
			$sSQL .= "username , ";
			$sSQL .= "to_char(login,'DD/MM/YYYY HH24:MI:SS') as inicio, ";
			$sSQL .= "to_char(logout,'DD/MM/YYYY HH24:MI:SS') as fim, "; 
			$sSQL .= " CAST (logout - login as time) as tempo,  ";
			$sSQL .= " caller_id as origem, session_id, ";
			$sSQL .= "terminate_cause as mensagem, bytes_in, bytes_out ";
			$sSQL .= "FROM ";
			$sSQL .= "	rdtb_accounting ";
			$sSQL .= " WHERE " ;
			$sSQL .= " username = '$valor_pesquisa' "; 
			$sSQL .= " AND logout is not null ";
			$sSQL .= " AND tempo > 0 ";
			$sSQL .= " AND session_id not ilike '%:%' ";
			$sSQL .= " AND EXTRACT('month' FROM login) = '$mes' ";
			$sSQL .= " AND EXTRACT('year' FROM login) = '$ano' ";

			if ($tipo_conta ==	"discado" ){

				$sSQL .= " AND caller_id not ilike '%:%:%:%:%:%' ";

			}

			if ($tipo_conta ==	"pppoe" ){

				$sSQL .= " AND caller_id ilike '%:%:%:%:%:%' ";

			}


			$sSQL .= " ORDER BY inicio DESC " ; ////////echo $sSQL;

			$extrato = $this->bd->obtemRegistros($sSQL);

			}

			if ($tipo_conta == "BL" ){
			
				$tipo_conta = 'pppoe';
			
			}

			if ($tipo_conta == "D" ){

				$tipo_conta = 'discado';

			}
			
				for($i=0;$i<count(@$extrato);$i++){
							
							
					$username_id = $extrato[$i]["username"];

					$dSQL  =" SELECT ";
					$dSQL .=" id_cliente FROM ";
					$dSQL .=" cntb_conta ";
					$dSQL .=" WHERE ";
					$dSQL .=" username = '$username_id' ";
					$dSQL .=" AND tipo_conta not ilike 'E' ";

					$id_cliente = $this->bd->obtemRegistros($dSQL);

					$extrato[$i]["id_cliente"] = $id_cliente;
						/////////echo $dSQL . "<hr><br>\n" ;		
				}

			@$this->tpl->atribui("extrato",$extrato);
			$this->tpl->atribui("valor_pesquisa",$valor_pesquisa);
			$this->tpl->atribui("periodo",$periodo);
			$this->tpl->atribui("tipo_conta",$tipo_conta);
			$this->tpl->atribui("ano_pesq",$ano);
			$this->tpl->atribui("mes_pesq",$mes);
			///// ;
            
		$this->arquivoTemplate= "suporte_extrato_radios.html";
		
		}

		
		

		

		else if ($op=="online_users"){


			$sSQL  = "SELECT ";
			$sSQL .= "username , ";
			$sSQL .= "login as inicio, "; 
			$sSQL .= " CAST (now() - login as time) as tempo,  ";
			$sSQL .= " caller_id as origem, session_id, nas,  ";
			$sSQL .= "terminate_cause as mensagem, bytes_in, bytes_out ";
			$sSQL .= "FROM ";
			$sSQL .= "	rdtb_accounting ";
			$sSQL .= " WHERE " ;
			$sSQL .= " logout is null ";
			$sSQL .= " ORDER BY login DESC " ; 

			$relacao_users = $this->bd->obtemRegistros($sSQL);

			///echo $sSQL;


			for($i=0;$i<count($relacao_users);$i++){


					$username_id = $relacao_users[$i]["username"];

					$dSQL  =" SELECT ";
					$dSQL .=" id_cliente FROM ";
					$dSQL .=" cntb_conta ";
					$dSQL .=" WHERE ";
					$dSQL .=" username = '$username_id' ";
					$dSQL .=" AND tipo_conta not ilike 'E' ";

					$id_cliente = $this->bd->obtemRegistros($dSQL);

					$relacao_users[$i]["id_cliente"] = $id_cliente;
					
					///////echo $dSQL . "<hr><br>\n" ;

			}
	
			$this->tpl->atribui("relacao_users",$relacao_users);
			$this->arquivoTemplate="suporte_radius_online.html";
				
		
		}
	}

	public function __destruct() {
		parent::__destruct();
	}

}

?>
