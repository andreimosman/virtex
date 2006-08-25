<?
require_once( PATH_LIB . "/VirtexAdmin.class.php" );


class VABackup extends VirtexAdmin {

	public function VABackup() {
		parent::VirtexAdmin();
	}

	public function processa($op=null) {	
		if( ! $this->privPodeGravar("_SUPORTE_BACKUP") ) {
			$this->privMSG();
			return;
		}	

	
		if ($op == "inicio"){
		
			//$sSQL = "SELECT b.id_backup,b.data_backup,b.arquivo_backup,b.tipo_backup,b.status_backup,b.admin as id_admin,a.admin FROM bktb_backup b,adtb_admin a WHERE b.admin = a.id_admin ORDER BY id_backup,arquivo_backup DESC LIMIT 10";
			
			
			/*$sSQL  = "SELECT ";
			$sSQL .= "b.id_backup, b.data_backup, b.status_backup, b.admin as id_admin, b.operador_backup, ";
			$sSQL .= "ba.arquivo_backup, ba.status_backup as status_arq, ";
			$sSQL .= "a.admin ";
			$sSQL .= "FROM ";
			$sSQL .= "bktb_backup b, bktb_arquivos ba, adtb_admin a ";
			$sSQL .= "WHERE ";
			$sSQL .= "b.id_backup = ba.id_backup ";
			$sSQL .= "AND b.admin = a.id_admin ";
			$sSQL .= "ORDER BY b.data_backup DESC ";*/
			
			$sSQL = "SELECT * FROM bktb_backup ORDER BY data_backup LIMIT 10";
			
			$lista = $this->bd->obtemRegistros($sSQL);
			
			//echo "LISTA INICIO: $sSQL <br>";
			
			$this->tpl->atribui("lista",$lista);
			$this->arquivoTemplate = "backup_inicio.html";
			
		
		
		
		}else if ($op == "backup"){
		
			$configuracao = @$_REQUEST["configuracao"];
			$bd = @$_REQUEST["bd"];
			$sistema = @$_REQUEST["sistema"];
			$hoje = DATE("Y-m-d");
			$DATA = date("Y-m-d H:i:s");
			
			
			$DATA2 = str_replace(" ","_",$DATA);
			$DATA2 = str_replace(":","-",$DATA2);
			
			//ECHO $hoje ."<br>";
			$acao = @$_REQUEST["acao"];
			$op = @$_REQUEST["op"];
			$sop = @$_REQUEST["sop"];
			$msg = "";
			$admin = $this->admLogin->obtemId();


			if ($acao == "backup") {
			
				$operacao = 
				//echo "acao <br>";
				$erro = 0;
			
				if ($sop == "ok"){
				
						$sSQL  = "INSERT INTO bktb_backup ";
						$sSQL .= "(data_backup,admin,operador_backup,data) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$hoje','$admin','GU','$DATA') ";
						$this->bd->consulta($sSQL);
				
						//echo "GRAVAÇÃO DE BKP1: $sSQL<br>";
				
				
						$sSQL = "select max(id_backup) FROM bktb_backup";
						$id = $this->bd->obtemRegistros($sSQL);
						
						
						$id_backup = $id["id_backup;"];
						//echo "ID: $id_backup - ".$id["id_backup;"];
				
				
				
				//echo "ok<br>";
					if($bd){
					//echo "banco<br>";
						
						$arquivo = "bd_$DATA2.sql";
						system('pg_dump --disable-triggers -U virtex > /mosman/backup/'.$arquivo, $retvalbd);
						
						if ($retvalbd != 0){
						
							$status = "ERRO";
							$erro = 1;
								
						}else{
						
							$status = "OK";
							
						}
						

						
						$sSQL  = "INSERT INTO bktb_arquivos ";
						$sSQL .= "(id_backup,arquivo_backup, tipo_backup, status_backup, data_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "((select max(id_backup) FROM bktb_backup),'$arquivo', 'Banco de Dados','$status', '$hoje' )";
						$this->bd->consulta($sSQL);
						//ECHO "GRAVAÇÃO DE BKP´: $sSQL<br>";


					}
					if($configuracao){
					
						$pathbackup = " /mosman/backup/";
					
						$nome1 = "etc_$DATA2.tgz";
						$nome2 = "appetc_$DATA2.tgz";
						
						$caminho1 = " /mosman/virtex/etc/";
						$caminho2 = " /mosman/virtex/app/etc/";
						
						$comando1 = "tar -czvf $pathbackup$nome1  $caminho1";
						$comando2 = "tar -czvf $pathbackup$nome2  $caminho2";
						
					
						system($comando1,$retvalconf1);
						
						
						if ($retvalconf1 !=0){
						
							$status = "ERRO";
							$erro =1;						
						}else{
						
							$status = "OK";
						
						}
						
						
						$sSQL  = "INSERT INTO bktb_arquivos ";
						$sSQL .= "(id_backup,arquivo_backup, tipo_backup, status_backup, data_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "((select max(id_backup) FROM bktb_backup),'$nome1', 'Configurações','$status', '$hoje' )";
						
						$this->bd->consulta($sSQL);

						//echo "GRAVAÇÃO DE BKP: $sSQL <br>";

						system($comando2,$retvalconf2);
						
						
						
						if ($retvalconf2 !=0){
						
							$status = "ERRO";
							$erro = 1;
						
						}else{
						
							$status = "OK";
						
						}
						
						$sSQL  = "INSERT INTO bktb_arquivos ";
						$sSQL .= "(id_backup,arquivo_backup, tipo_backup, status_backup, data_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "((select max(id_backup) FROM bktb_backup),'$nome2', 'Configurações','$status', '$hoje' )";
						$this->bd->consulta($sSQL);		
						
						//echo "GRAVAÇÃO DE BKP: $sSQL <br>";
						
					}
					if($sistema){
					
						$pathbackup = " /mosman/backup/";
										
						$nome = "virtex_$DATA2.tgz";
						$caminho = " /mosman/virtex/";
					
						$comando = "tar -czvf $pathbackup$nome  $caminho";
					
						system($comando,$retvalsystem);
						
						//echo "comando: $comando<br>";
						
						//copy("/mosman/virtex/app/virtex_".$hoje.".tgz","/mosman/backup/sys/virtex_".$hoje.".tgz");
						//$msg .= $retvalsystem."<br>";
						if ($retvalsystem != 0){
						
							$status = "ERRO";
							$erro = 1;
						
						}else{
						
							$status = "OK";
						
						}

						$sSQL  = "INSERT INTO bktb_arquivos ";
						$sSQL .= "(id_backup,arquivo_backup, tipo_backup, status_backup, data_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "((select max(id_backup) FROM bktb_backup),'$nome', 'Sistema','$status', '$hoje' )";

						$this->bd->consulta($sSQL);		
						
						//echo "GRAVAÇÃO DE BKP: $sSQL <br>";						
						
						
					}
					
					if ($erro == 1){
						$status2 = "ERRO";
					}else{
						$status2 = "OK";
					}
					
					$sSQL = "UPDATE bktb_backup SET status_backup = '$status2' WHERE id_backup = (select max(id_backup) FROM bktb_backup)";
					$this->bd->consulta($sSQL);
					//ECHO "UPDATE: $sSQL <br>";
					
					//list($d,$m,$a,$h,$i,$s) = explode("-",$hoje);
					//$dt = "$d-$m-$a";
					
					/*$sSQL  = "SELECT ";
					$sSQL .= "b.id_backup, b.data_backup, b.status_backup, b.admin as id_admin, b.operador_backup, ";
					$sSQL .= "ba.arquivo_backup, ba.status_backup as status_arq, ";
					$sSQL .= "a.admin ";
					$sSQL .= "FROM ";
					$sSQL .= "bktb_backup b, bktb_arquivos ba, adtb_admin a ";
					$sSQL .= "WHERE ";
					$sSQL .= "b.id_backup = ba.id_backup ";
					$sSQL .= "AND b.admin = a.id_admin ";
					$sSQL .= "AND b.data_backup = '$hoje' ";
					$sSQL .= "ORDER BY b.data_backup DESC ";*/
					
					$sSQL = "SELECT * FROM bktb_arquivos where id_backup = (select max(id_backup) FROM bktb_backup) ORDER BY arquivo_backup";

					
					
					//$sSQL = "SELECT b.id_backup,b.data_backup,b.arquivo_backup,b.tipo_backup,b.status_backup,b.admin as id_admin,b.operador_backup, a.admin FROM bktb_backup b,adtb_admin a WHERE b.admin = a.id_admin AND data_backup = '$hoje' ORDER BY id_backup,arquivo_backup ";
					$lista = $this->bd->obtemRegistros($sSQL);
					
					//echo "LISTA: $sSQL<br>";

					
					$this->tpl->atribui("lista",$lista);
					$this->arquivoTemplate = "backup_final.html";
					return;
			
				}

			} else if ($acao == "download"){
				
					$id_backup = @$_REQUEST["id_backup"];
					$arquivo = @$_REQUEST["arquivo"];
					
					$sSQL = "SELECT * FROM bktb_arquivos WHERE id_backup = $id_backup AND arquivo_backup = '$arquivo'";
					$bkp = $this->bd->obtemUnicoRegistro($sSQL);
					
					$arquivo = "/mosman/backup/".$bkp["arquivo_backup"];
					$arq_down = fopen($arquivo,"r");
					$arq = fread($arq_down,filesize($arquivo));
				
					header('Pragma: public');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Content-type: application/force-download');
					//header("Content-type: application/octet-stream ");
					header('Content-Disposition: attachment; filename="'.basename($arquivo).'"');
					//header('Content-Length: ' . filesize($arquivo));
					readfile($arquivo);
				
					fclose($arq_down);

					//echo $arq_down;
					return;
				
				}else if ($acao == "detalhes"){
		
				$sSQL = "SELECT * FROM bktb_backup WHERE id_backup = '$id_backup'"
				$detalhe = $this->bd->obtemRegistros($sSQL);
				
				$this->tpl->atribui("detalhe",$detalhe);
				$this->arquivoTemplate = "backup_efetua.html";
				
				
				
				
				}
		
		
		
			$this->arquivoTemplate = "backup_efetua.html";
		
		
		
		
		
		
		}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	}// final de processa

}// final da classe
?>