<?


require_once( PATH_LIB . "/VirtexAdmin.class.php" );

class VAGrafico extends VirtexAdmin {

	public function VAHome() {
		parent::VirtexAdmin();
	
		$adm = $this->admLogin->obtemAdmin();
		$this->tpl->atribui("admin",$adm);	



		$this->arquivoTemplate = "";
	
	
	}
	
	
	public function processa($op=null) {
	
	
		if( $op == "carga" ) {
			$tipo=@$_REQUEST["tipo"];
			$rl=strtolower(@$_REQUEST["rl"]);		// Indica se o tipo de relatório é de upload ou download
			
			$rel = new VARelatorio($this->bd);
			
			$carga   = $rel->obtemCarga($tipo); 
			
			$up = array();
			$dn = array();
			$titulos = array();
			
			for($i=0;$i<count($carga);$i++) {
			   $up[]      = $carga[$i]["carga_up"];
			   $dn[]      = $carga[$i]["carga_down"];
			   $titulos[] = $carga[$i]["nome"];
			}
			
			$dados = $rl == "u" ? $up : $dn;
			$tit   = "Carga por " . $tipo . "\n" . ($rl == "u" ? "(upload)" : "(download)");
			
			
			$width  = 540;
			$heigth = 200;
			$size	= 0.4;
			
			$inc_heigth = 0;
			$dec_size   = 0;
			
			if( count($titulos) > 9 ) {
				$tit_extra = (count($titulos) - 9);
				$inc_heigth = $tit_extra * 20;
				
				if($tit_extra > 3 ) {
					$dec_size = 0.1;
				}
				
				if($tit_extra > 7 ) {
					$dec_size = 0.2;
				}
			}
			
			$size -= $dec_size;
			
			$heigth += $inc_heigth;
			
			
			
			$graph = new PieGraph($width,$heigth,"auto");
			$graph->SetShadow();

			$graph->title->Set($tit);
			$graph->title->SetFont(FF_FONT1,FS_BOLD);

			$gr = new PiePlot($dados);
			$gr->SetSize($size);
			$gr->SetCenter(0.30);
			$gr->SetLegends($titulos);
			$graph->Add($gr);
			
			$graph->Stroke();

		}
	
	}

	public function __destruct() {
			parent::__destruct();
	}

}



?>
