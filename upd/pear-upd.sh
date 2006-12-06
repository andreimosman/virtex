#!/bin/sh
##############################################
# utilizado para atualizar o pear e instalar #
# os novos modulos requeridos.               #
# CopyRight(c) Mosman Consultoria            #
# Autor: Andrei de Oliveira Mosman           #
#       (consultoria@mosman.com.br)          #
#############################################

#LIST_STABLE="Archive_Tar PEAR MDB2 MDB2_Driver_pgsql XML_Tree"
LIST_STABLE="Archive_Tar PEAR MDB2 MDB2_Driver_pgsql"
#LIST_UNSTABLE="channel://pear.php.net/MDB2_Schema-0.7.0"
LIST_UNSTABLE=""

# Parametros do SED sao diferentes entre o linux e o freebsd.
SO=$(uname)
if [ "${SO}" = "Linux" ] ; then
	SED_REGEXFLAG="-r"
else
	SED_REGEXFLAG="-E"
fi

for pkg in ${LIST_STABLE} ; do
	#echo PKG: $pkg
	pear list|grep "${pkg} " >/dev/null
	
	if [ $? -eq 0 ] ; then
		# Tem o pacote, upgrade
		echo UPG ${pkg}...
		pear upgrade ${pkg} > /dev/null
	else
		# Nao tem o pacote
		echo INS ${pkg}...
		pear install ${pkg} > /dev/null
	fi
done

for pkg in ${LIST_UNSTABLE} ; do
	ppkg=$( echo -n $pkg | sed ${SED_REGEXFLAG} 's/(^(.*\/))|(-.*$)//g' )

	pear list|grep "${ppkg} " >/dev/null	
	if [ $? -eq 0 ] ; then
		# Tem o pacote, upgrade
		echo UPG ${ppkg}...
		echo pear upgrade ${pkg} > /dev/null

	else
		# Nao tem o pacote
		echo INS ${ppkg}...
		pear install ${pkg} > /dev/null
	fi
done

