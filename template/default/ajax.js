function createAjaxObj() {
	var httprequest=false;
	if (window.XMLHttpRequest){ // if Mozilla, Safari etc
		httprequest=new XMLHttpRequest();
		if (httprequest.overrideMimeType);
			httprequest.overrideMimeType('text/xml')
		} else if (window.ActiveXObject){ // if IE
		try {
			httprequest=new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				//httprequest=new ActiveXObject("Microsoft.XMLHTTP");
				httprequest=new XMLHttpRequest(); 
			} catch (e){
				
			}
		}
	}
	return httprequest;
}

/**
 * Retorna o xmlhttp
 */
function ajax(url) {

  xmlhttp = createAjaxObj();
	var response;

	try {
		xmlhttp.open("GET", url, false);
		xmlhttp.send(null);
		//response = xmlhttp.responseXML;
		//htmlResponse = xmlhttp.responseText;
	} catch(e) {
		//window.f(e);
	}

	return xmlhttp;
}

/**
 * Retorna o XML de uma consulta AJAX
 */
function ajaxXML(url) {
   var response = ajax(url);
   retorno = false;

   try {
      retorno = response.responseXML;
   } catch(e) {
      window.alert(e);
   }

   return(retorno);
}


/**
 * Retorna o texto de uma consulta AJAX
 */
function ajaxText(url) {
   var response = ajax(url);
   retorno = false;

   try {
      retorno = response.responseText;
   } catch(e) {
      window.alert(e);
   }

   return(retorno);
}

