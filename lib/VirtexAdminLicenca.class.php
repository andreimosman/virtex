<?

if(!defined("VA_SALT")) define("VA_SALT","VA0200");

class VirtexAdminLicenca extends MLicenca {



	function __construct() {
		parent::__construct("etc/virtex.lic",VA_SALT);
		
	}
	
	protected function verificaExpiracao($campo) {
		/**
		 * Se a licença não for valida ou não tiver a informação da expiração retorna que expirou.
		 */
		if(!$this->isValid() || !@$campo) return true;
		@list($ano,$mes,$dia) = explode("-",chop(@$campo));
		
		// Se não tiver o dia, mês ou ano retorna que expirou.
		if( !$dia || !$mes || !$ano ) return true;
		@list($a,$m,$d) = explode("-",date("Y-m-d"));
		
		$t_campo   = mktime(0,0,0,$mes,$dia,$ano);
		$t_sistema = mktime(0,0,0,$m,$d,$a);
		return( $t_sistema >= $t_campo );
		
	}
	
	
	public function expirou() {
		return($this->verificaExpiracao(@$this->lic["geral"]["expira_em"]));
	}
	
	public function congelou() {
		return($this->verificaExpiracao(@$this->lic["geral"]["congela_em"]));
	}
	
}

?>
