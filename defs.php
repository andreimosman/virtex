<?

if(!defined('_DEFS_PHP')) {
	define('_DEFS_PHP',true);


/**
 * Path para os aplicativos
 */

//ini_set('include_path',ini_get('include_path').':/mosman/virtex/cfrontend/framework:/usr/local/share/pear:/usr/local/share/smarty');

/**
 * Definições
 */

define('PATH_LIB','./lib');
define('PATH_ETC','./etc');
define('PATH_TMP','./tmp');

// Definicao de autoload utilizada no framework.
// O arquivo abaixo define __autoload para que não seja necessário chamar require dos aplicativos
require_once("autoload.def.php");

/**
 * Carregar arquivos necessários
 * TODO: definições dentro de classes estáticas.
 */
__autoload("status.defs");

//session_start();

}

?>
