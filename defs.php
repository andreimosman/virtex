<?

if(!defined('_DEFS_PHP')) {
	define('_DEFS_PHP',true);


/**
 * Path para os aplicativos
 */

//ini_set('include_path',ini_get('include_path').':/mosman/virtex/cfrontend/framework:/usr/local/share/pear:/usr/local/share/smarty');

/**
 * Defini��es
 */

define('PATH_LIB','./lib');
define('PATH_ETC','./etc');
define('PATH_TMP','./tmp');

// Definicao de autoload utilizada no framework.
// O arquivo abaixo define __autoload para que n�o seja necess�rio chamar require dos aplicativos
require_once("autoload.def.php");

/**
 * Carregar arquivos necess�rios
 * TODO: defini��es dentro de classes est�ticas.
 */
__autoload("status.defs");

//session_start();

}

?>
