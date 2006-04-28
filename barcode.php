<?

require_once("MArrecadacao.class.php");
require_once("MUtils.class.php");

$ph = new MUtils;
	
$_path = MUtils::getPwd();


MArrecadacao::barCode(@$_REQUEST['codigo']);

?>
