<?

require_once("MLicenca.class.php");

class VirtexAdminLicenca extends MLicenca {



	function VirtexAdminLicenca() {
		parent::MLicenca("etc/virtex.lic");
		
	}
	
	/**
	function obtemEmpresa() {
		return( @$this->lic["empresa"] );
	}
	*/




}





?>
