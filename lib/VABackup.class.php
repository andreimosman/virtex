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
		
			$sSQL = "SELECT b.id_backup,b.data_backup,b.arquivo_backup,b.tipo_backup,b.status_backup,b.admin as id_admin,a.admin FROM bktb_backup b,adtb_admin a WHERE b.admin = a.id_admin ORDER BY id_backup,arquivo_backup DESC LIMIT 10";
			$lista = $this->bd->obtemRegistros($sSQL);
			
			
			for ($i=0;$i<count($lista);$i++){
									
				$arquivo = "/mosman/backup/".$lista[$i]["arquivo_backup"];
				$tamanho = filesize($arquivo);
				$lista[$i]["tamanho"] = $tamanho;
			}
			
			
			$this->tpl->atribui("lista",$lista);
			$this->arquivoTemplate = "backup_inicio.html";
			
		
		
		
		}else if ($op == "backup"){
		
			$configuracao = @$_REQUEST["configuracao"];
			$bd = @$_REQUEST["bd"];
			$sistema = @$_REQUEST["sistema"];
			$hoje = DATE("d-m-Y-H-i-s");
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
				
				//echo "ok<br>";
					if($bd){
					//echo "banco<br>";
						
						$arquivo = "bd_$hoje.sql";
						system('pg_dump --disable-triggers -U virtex > /mosman/backup/'.$arquivo, $retvalbd);
						
						if ($retvalbd != 0){
						
							$status = "ERRO";
								
						}else{
						
							$status = "OK";
							
						}
						
						$sSQL  = "INSERT INTO bktb_backup ";
						$sSQL .= "(data_backup,arquivo_backup,tipo_backup,status_backup,admin,operador_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$hoje','$arquivo','Banco de Dados','$status','$admin','GU') ";
						$this->bd->consulta($sSQL);
						
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
						
					
						system($comando1,$retvalconf1);
						
						
						if ($retvalconf1 !=0){
						
							$status = "ERRO";
						
						}else{
						
							$status = "OK";
						
						}
						
						
						$sSQL  = "INSERT INTO bktb_backup ";
						$sSQL .= "(data_backup,arquivo_backup,tipo_backup,status_backup,admin,operador_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$hoje','$nome1','Configurações','$status','$admin','GU') ";
						$this->bd->consulta($sSQL);

						//echo "GRAVAÇÃO DE BKP: $sSQL <br>";

						system($comando2,$retvalconf2);
						
						
						
						if ($retvalconf2 !=0){
						
							$status = "ERRO";
						
						}else{
						
							$status = "OK";
						
						}
						
						$sSQL  = "INSERT INTO bktb_backup ";
						$sSQL .= "(data_backup,arquivo_backup,tipo_backup,status_backup,admin,operador_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$hoje','$nome2','Configurações','$status','$admin','GU') ";
						$this->bd->consulta($sSQL);		
						
						//echo "GRAVAÇÃO DE BKP: $sSQL <br>";
						
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
						
							$status = "ERRO";
						
						}else{
						
							$status = "OK";
						
						}
						
						$sSQL  = "INSERT INTO bktb_backup ";
						$sSQL .= "(data_backup,arquivo_backup,tipo_backup,status_backup,admin,operador_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$hoje','$nome','Sistema','$status','$admin','GU') ";
						$this->bd->consulta($sSQL);		
						
						//echo "GRAVAÇÃO DE BKP: $sSQL <br>";						
						
						
					}
					

					list($d,$m,$a,$h,$i,$s) = explode("-",$hoje);
					$dt = "$d-$m-$a";
					
					$sSQL = "SELECT b.data_backup,b.arquivo_backup,b.tipo_backup,b.status_backup,b.admin as id_admin,b.operador_backup, a.admin FROM bktb_backup WHERE b.admin = a.id_admin AND data_backup ilike '$dt%' ORDER BY id_backup,arquivo_backup ";
					$lista = $this->bd->obtemRegistros($sSQL);
					
					echo $sSQL."<br>";

					
					$this->tpl->atribui("lista",$lista);
					$this->arquivoTemplate = "backup_final.html";
					return;
			
				}

			} else if ($acao == "download"){
				
					$id_backup = @$_REQUEST["id_backup"];
					
					$sSQL = "SELECT * FROM bktb_backup where id_backup = $id_backup";
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
					$this->arquivoTemplate = "msgredirect.html";
				
				}
		
		
			$this->arquivoTemplate = "backup_efetua.html";
		
		
		
		
		
		
		}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	}// final de processa

}// final da classe
?>