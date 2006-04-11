
function dataValida(d) {
   // Validação primaria de formato DD/MM/AAAA
   regex = new RegExp("(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}");
   match = regex.exec(d);

   if( match == null ) return false;

   // split;
   var tmp = d.split("/");
   dia = tmp[0];
   mes = tmp[1];
   ano = tmp[2];

   // Verificação do numero de dias no mês.
   
   var maxdias = 0;

   switch( parseInt(mes)  ) {
      case 2:
         // Verificação de ano bissexto
         maxdias = ano % 4 ? 28 : 29;
         break;
         
      case 4:
      case 6:
      case 9:
      case 11:
         maxdias = 30
         break;

      default:
         maxdias = 31;
      
   }
   
   return( dia <= maxdias );

}

function criaData(d,m,a) {
   if( d == "08" ) d = "8";
   if( d == "09" ) d = "9";
   d = parseInt(d);
   data  = d < 10 ? "0" + d : d;
   data += "/";
   data += m < 10 ? "0" + m : m;
   data += "/";
   data += a;
   
   return(data);

}

// Incrementa a refrida data em 1 mês
function incrementaMes(d,meses) {

   // split;
   var tmp = d.split("/");
   d = tmp[0];
   m = tmp[1];
   // PAU NO JAVASCRIPT DO IE 6.0.2900.2180.xpsp_sp2_rtm.040803-2158
   if( d == "08" ) d = "8";
   if( d == "09" ) d = "9";
   if( m == "08" ) m = "8";
   if( m == "09" ) m = "9";
   
   //window.alert("MES: " + tmp[1]);
   dia = parseInt(d);
   mes = parseInt(m);
   ano = parseInt(tmp[2]);

   //window.alert("d: " + d + "(mes+meses): " + mes + " + " + meses + " = " + (mes+meses) );

   mes += meses;

   if( mes > 12 ) {
      ano += (mes-1)/12;
      mes = mes % 12;
      if(mes==0)mes=12;
   }
   
   dia++;
   
   do {
      --dia;
      
      dia = parseInt(dia);
      mes = parseInt(mes);
      ano = parseInt(ano);

      novadata = criaData(dia,mes,ano);

   } while ( !dataValida(novadata) && dia > 0 )
   
   return( novadata );
   
}



// Arredondamento de acordo com as casas decimais
function RoundToNdp(X, N) {
   var T = Number('1e'+N);
   return Math.round(X*T)/T;
}


// Base para lpad e rpad
function _PAD(texto,num,chr,sentido) {
   // Sentido == 1: Direita, == 2: Esquerda

   tmp = new String(texto);
   
   if( num <= texto.length ) return texto;
   
   pad = "";
   
   for(x=0;x<num-texto.length;x++) {
      pad += chr;
   }
   
   return sentido == 1 ? texto + pad : pad + texto;
}

function rpad(texto,num,chr) {
   return(_PAD(texto,num,chr,1));
}

function lpad(texto,num,chr) {
   return(_PAD(texto,num,chr,2));
}

// formata numero de acordo com numero de casas decimais. separador: ","
function numberFormat(numero,decimais) {
   tmpNum = String(numero+"");
   
   //aTMP = new Array(2);
   aTMP = tmpNum.split(".");
   
   retorno = aTMP[0]+",";
   
   if( aTMP[1] == undefined ) 
      retorno += "00";
   else {
      retorno += rpad(aTMP[1],2,"0");
   }
   
   return(retorno);

}

function hoje() {
   d = new Date();
   data = lpad(d.getDate() + "",2,"0");
   data += "/";
   data += lpad( (d.getMonth()+1) + "",2,"0");
   data += "/";
   data += d.getFullYear();
   return( data );
}


