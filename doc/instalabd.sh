#########
#--createuser --no-adduser --createdb -U pgsql virtex
#--no-createrole -U pgsql virtex
#--createdb --encoding LATIN1 -U virtex virtex
psql -U virtex < virtex.sql
psql -U virtex < cftb_uf.sql
psql -U virtex < cftb_cidade.sql
psql -U virtex < administrador.sql