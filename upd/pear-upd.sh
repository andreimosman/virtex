#!/bin/sh
##############################################
# utilizado para atualizar o pear e instalar #
# os novos modulos requeridos.               #
# CopyRight(c) Mosman Consultoria            #
# Autor: Andrei de Oliveira Mosman           #
#       (consultoria@mosman.com.br)          #
#############################################

if [ ! -f "pear-upd.sh" ] ; then
	echo ""
	echo "EXECUTE ESSE SCRIPT NO DIRETORIO UPD"
	echo ""
	exit -1;
fi

PEARLIB="/usr/local/lib/php/pear"


#LIST_STABLE="Archive_Tar PEAR MDB2 MDB2_Driver_pgsql XML_Tree"
LIST_STABLE="Archive_Tar PEAR MDB2 MDB2_Driver_pgsql"
#LIST_UNSTABLE="channel://pear.php.net/MDB2_Schema-0.7.0"
LIST_UNSTABLE=""

# Parametros do SED sao diferentes entre o linux e o freebsd.
SO=$(uname)
if [ "${SO}" = "Linux" ] ; then
	SED=/bin/sed
	SED_REGEXFLAG="-r"
	PEAR_PATH="/usr/share/pear/"
	MD5=/usr/bin/md5sum
	CUT=/bin/cut
	PEAR=pear
	PHP_INI=/etc/php.ini	
else
	SED=/usr/bin/sed
	SED_REGEXFLAG="-E"
	PEAR_PATH="/usr/local/share/pear/"
	MD5=/sbin/md5
	CUT=/usr/bin/cut
	PEAR=pear
	PHP_INI=/usr/local/etc/php.ini
fi

for pkg in ${LIST_STABLE} ; do
	#echo PKG: $pkg
	${PEAR} list|grep "${pkg} " >/dev/null
	
	if [ $? -eq 0 ] ; then
		# Tem o pacote, upgrade
		echo UPG ${pkg}...
		${PEAR} upgrade ${pkg} > /dev/null
		if [ ${pkg} = "PEAR" ] ; then
			# Corrige o pau do pear no FreeBSD
			if [ -f ${PEARLIB}/pearcmd.php ] ; then
				cp ${PEARLIB}/pearcmd.php ${PEARLIB}/pearcmd.php.$$
				cp pearcmd.php ${PEARLIB}
			fi
		fi
	else
		# Nao tem o pacote
		echo INS ${pkg}...
		${PEAR} install ${pkg} > /dev/null
	fi
done

for pkg in ${LIST_UNSTABLE} ; do
	ppkg=$( echo -n $pkg | ${SED} ${SED_REGEXFLAG} 's/(^(.*\/))|(-.*$)//g' )

	pear list|grep "${ppkg} " >/dev/null	
	if [ $? -eq 0 ] ; then
		# Tem o pacote, upgrade
		echo UPG ${ppkg}...
		${PEAR} upgrade ${pkg} > /dev/null

	else
		# Nao tem o pacote
		echo INS ${ppkg}...
		${PEAR} install ${pkg} > /dev/null
	fi
done

##################################
# Correcao no Datatype do driver do MDB2 para suportar os tipos
# nativos do pgsql (inet,cidr e macaddr)
###########################

MDB2_NATIVE_PG=${PEAR_PATH}/MDB2/Driver/Datatype/pgsql.php
MDB2_NATIVE_CORR_PG=datatype_pgsql.php

if [ "${SO}" = "Linux" ] ; then
	SUM_PG=$( ${MD5} ${MDB2_NATIVE_PG} | ${CUT} -d ' ' -f 1)
	SUM_CORR=$( ${MD5} ${MDB2_NATIVE_CORR_PG} | ${CUT} -d ' ' -f 1 )
else
	if [ ! -f MDB2_NATIVE_PG ] ; then
		MDB2_NATIVE_PG=${PEARLIB}/MDB2/Driver/Datatype/pgsql.php
	fi
	SUM_PG=$( ${MD5} ${MDB2_NATIVE_PG}|${SED} -E 's/ //g'|${CUT} -d'=' -f2)
	SUM_CORR=$( ${MD5} ${MDB2_NATIVE_CORR_PG}|${SED} -E 's/ //g'|${CUT} -d'=' -f2 )
fi

if [ "${SUM_PG}" != "${SUM_CORR}" ] ; then
	echo "Arquivos datatype do pgsql diferente... atualizando"
	#echo "PG: ${SUM_PG}";
	#echo "CO: ${SUM_CORR}";
	cp ${MDB2_NATIVE_PG} ${MDB2_NATIVE_PG}.bak.$$
	cp -f ${MDB2_NATIVE_CORR_PG} ${MDB2_NATIVE_PG}
#else
#	echo "arquivos iguais"
fi

#######################################
# Verificacao/Correcao do php.ini
######################
if [ ! -x php_ini_correct ] ; then
	chmod +x php_ini_correct
fi
./php_ini_correct ${PHP_INI}
