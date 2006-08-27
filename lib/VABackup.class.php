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
		
			
			$sSQL = "SELECT b.id_backup,b.data_backup,b.status_backup,b.admin as id_admin,b.data, a.id_admin, a.admin FROM bktb_backup b,adtb_admin a WHERE b.admin = a.id_admin ORDER BY b.data DESC LIMIT 10";
			
			$lista = $this->bd->obtemRegistros($sSQL);
			
			//echo "LISTA INICIO: $sSQL <br>";
			$mensagem = "Últimos Backups Realizados";
			
			$this->tpl->atribui("mensagem",$mensagem);
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
						
						$arquivo = "bd_$DATA2.gz";
						system('pg_dump --clean --disable-triggers --compress=9 -U virtex > /mosman/backup/'.$arquivo, $retvalbd);
						
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
					
					
					$sSQL = "SELECT a.id_backup,a.data_backup,a.arquivo_backup,a.tipo_backup,a.status_backup,b.data FROM bktb_arquivos a, bktb_backup b where a.id_backup = (select max(id_backup) FROM bktb_backup) AND a.id_backup = b.id_backup ORDER BY a.arquivo_backup";

					
					
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
					header('Content-Disposition: attachment; filename="'.basename($arquivo).'"');
					readfile($arquivo);
				
					fclose($arq_down);

					return;
				
				}else if ($acao == "detalhes"){
		
		
				$id_backup = @$_REQUEST["id_backup"];
				$sSQL = "SELECT a.id_backup,a.data_backup,a.arquivo_backup,a.tipo_backup,a.status_backup,b.data FROM bktb_arquivos a, bktb_backup b where a.id_backup = $id_backup AND a.id_backup = b.id_backup ORDER BY a.arquivo_backup";
				//$sSQL = "SELECT * FROM bktb_arquivos WHERE id_backup = '$id_backup'";
				$detalhe = $this->bd->obtemRegistros($sSQL);
				
				$this->tpl->atribui("detalhe",$detalhe);
				$this->arquivoTemplate = "backup_inicio.html";
				
				return;
				
				
				
				}else if ($acao == "hist"){
				
					$mensagem = "Histórico de Backups";
				
					$sSQL = "SELECT b.id_backup,b.data_backup,b.status_backup,b.admin as id_admin,b.data, a.id_admin, a.admin FROM bktb_backup b,adtb_admin a WHERE b.admin = a.id_admin ORDER BY b.data DESC";
							
					$lista = $this->bd->obtemRegistros($sSQL);
							
					//echo "LISTA INICIO: $sSQL <br>";
					$this->tpl->atribui("mensagem",$mensagem);
					$this->tpl->atribui("lista",$lista);
					$this->arquivoTemplate = "backup_inicio.html";
				
					return;
					
				}
		
		
		
			$this->arquivoTemplate = "backup_efetua.html";
		
		
		
		
		
		
		}else if ($op == "restore"){
		
			$id_backup = @$_REQUEST["id_backup"];
			$arquivo = @$_FILE_["arquivo"];
			$upload = @$_REQUEST["upload"];
			$sop = @$_REQUEST["sop"];
			
			$acao = @$_REQUEST["acao"];
			
			$hoje = DATE("Y-m-d");
			$DATA = date("Y-m-d H:i:s");


			$DATA2 = str_replace(" ","_",$DATA);
			$DATA2 = str_replace(":","-",$DATA2);
			
			$msg = "";
			$admin = $this->admLogin->obtemId();			
			
			
			$sSQL = "SELECT b.data, b.id_backup from bktb_backup b, bktb_arquivos a WHERE b.id_backup = a.id_backup AND a.tipo_backup = 'Banco de Dados' ORDER BY b.data DESC";
			$data = $this->bd->obtemRegistros($sSQL);
			//echo "datas: $sSQL<br>";
			
			$this->tpl->atribui("datas",$data);
			//$this->arquivoTemplate = "restore_inicio.html";
			
			if($sop == "ok"){
			
				$acao = @$_REQUEST["acao"];
				
				//echo "OK<BR>";
			
				if ($upload){


					






				}else{

					$sSQL  = "SELECT b.id_backup, b.data_backup, a.status_backup, b.operador_backup, b.data, a.arquivo_backup, a.tipo_backup, b.admin as id_admin, ad.admin ";
					$sSQL .= "FROM ";
					$sSQL .= "bktb_backup b, bktb_arquivos a, adtb_admin ad ";
					$sSQL .= "WHERE ";
					$sSQL .= "b.id_backup = $id_backup AND ";
					$sSQL .= "b.admin = ad.id_admin AND ";
					$sSQL .= "b.id_backup = a.id_backup AND ";
					$sSQL .= "a.tipo_backup = 'Banco de Dados' ";
					$arq = $this->bd->obtemUnicoRegistro($sSQL);


					$this->tpl->atribui("arq",$arq);
					
					//echo "acao: $acao<br>";
					//ECHO "confirma>br>";
					

					if ($acao){
					
						$arq = $_REQUEST["arquivo"];
						
						//echo "MERDA<BR>";
						$arquivo = "bd_$DATA2.gz";
						

						system('pg_dump --clean --disable-triggers --compress=9 -U virtex > /mosman/backup/'.$arquivo, $retvalbd);
												
						if ($retvalbd != 0){
												
							$status = "ERRO";
							$erro = 1;
														
						}else{
												
							$status = "OK";
													
						}
												
						$sSQL  = "INSERT INTO bktb_backup ";
						$sSQL .= "(data_backup,admin,operador_backup,data,status_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$hoje','$admin','GS','$DATA','$status') ";
						$this->bd->consulta($sSQL);
						//echo "GRAVAÇÃO BACKUP: $sSQL<br> ";

												
						$sSQL  = "INSERT INTO bktb_arquivos ";
						$sSQL .= "(id_backup,arquivo_backup, tipo_backup, status_backup, data_backup) ";
						$sSQL .= "VALUES ";
						$sSQL .= "((select max(id_backup) FROM bktb_backup),'$arquivo', 'Banco de Dados','$status', '$hoje' )";
						$this->bd->consulta($sSQL);
						//ECHO "GRAVAÇÃO ARQUIVOS: $sSQL<br>";

						//FAZ O RESTORE
						
						system('pg_dump -U virtex --clean -t bktb_backup > /mosman/backup/temp1.sql',$ret);
						system('pg_dump -U virtex --clean -t bktb_arquivos > /mosman/backup/temp2.sql',$ret);
						
						
						
						//$comando = "pg_restore --file /mosman/backup/$arq -U virtex";
						$comando = "zcat /mosman/backup/$arq |psql -U pgsql virtex 2>&1 >/mosman/backup/log/imp.log";
					
						system("$comando 2>&1",$retval);
						//echo "RETVAL: ".$retval."<br>";
						
						if ($retval > 0){
						
							$msg = "ERRO";
						
						
						}else{
							
							$msg = "OK";
						
						}
					
						system('psql -U virtex < /mosman/backup/temp1.sql 2>&1 >/mosman/backup/log/t.log',$ret);
						system('psql -U virtex < /mosman/backup/temp2.sql 2>&1 >/mosman/backup/log/t.log',$ret);
					
					
					
					
						$sSQL = "INSERT INTO lgtb_restore (arquivo_restore, data_restore, admin, status_restore) ";
						$sSQL .= "VALUES ";
						$sSQL .= "('$arq','$DATA', '$admin', '$msg') ";
						$this->bd->consulta($sSQL);
						
						$this->tpl->atribui("msg",$msg);
						$this->arquivoTemplate = "restore_final.html";
						return;

					
					}
					
					$this->arquivoTemplate = "restore_confirma.html";

				}
			}else{
					
					$this->arquivoTemplate = "restore_inicio.html";
					
			}
		
		
		
		
		}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	}// final de processa

}// final da classe
?>