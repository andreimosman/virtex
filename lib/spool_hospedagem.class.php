<?
require_once(PATH_LIB . "/VirtexAdmin.class.php" );


class PapokerEmail extends VirtexAdmin {



	public function PapokerEmail() {
		parent::__construct();

	}

	public function processa($op=null){
	
/* 
CNTB_CONTA_EMAIL
	username varchar(30) NOT NULL,
  tipo_conta varchar(2) NOT NULL,
  dominio varchar(255) NOT NULL,
  quota int4,
  email varchar(255),


CNTB_CONTA
  username varchar(30) NOT NULL,
  dominio varchar(255) NOT NULL,
  tipo_conta varchar(2) NOT NULL,
  senha varchar(64),
  id_cliente int2,
  id_cliente_produto int2 NOT NULL,
  id_conta int2,
  senha_cript varchar(64),
  conta_mestre bool DEFAULT true,
  status char(1) DEFAULT 'A'::bpchar,
  observacoes text,
*/

		$sSQL = "SELECT * FROM cntb_conta WHERE tipo_conta = 'E' ";
		$conta = $this->bd->obtemRegistros($sSQL);


		for ($i=0;$i<count($conta);$i++){


			$sSQL  = "INSERT INTO cntb_conta_email (username,tipo_conta,dominio,quota,email) VALUE ( ";
			$sSQL .= " username = '" .$conta[$i]["username"]. "', ";
			$sSQL .= " tipo_conta = 'E', ";
			$sSQL .= " dominio = '" .$conta[$i]["dominio"]. "', ";
			$sSQL .= " quota = '50000', ";
			$sSQL .= " email = '" .$conta[$i]["username"]."@".$conta[$i]["dominio"]. "' ) ";
			$this->bd->consulta($sSQL);
			echo $sSQL . "<br>";
			

		}


		echo "FOI!! $i registros encontrados<BR>";
		

	
	}


	
}	


	
?>
