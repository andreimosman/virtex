/*
Created		22/2/2006
Modified		13/9/2006
Project		
Model		
Company		
Author		
Version		
Database		PostgreSQL 7.3 (beta) 
*/







Create table  cftb_uf
(
	uf Char(2) NOT NULL,
	estado Varchar(50) NULL ,
 primary key (uf)
);

Create table  cftb_cidade
(
	id_cidade Smallint NOT NULL,
	uf Char(2) NOT NULL,
	cidade Varchar(50) NULL ,
	disponivel Boolean NULL  Default false,
 primary key (id_cidade)
);

Create table  sptb_spool
(
	id_spool Serial NOT NULL,
	registro Timestamp NULL  Default now(),
	execucao Timestamp NULL ,
	destino Varchar(50) NULL ,
	tipo Varchar(2) NOT NULL,
	op Char(1) NULL ,
	id_conta Smallint NULL ,
	parametros Text NULL ,
	status Varchar(2) NOT NULL,
	cod_erro Smallint NULL ,
 primary key (id_spool)
);

Create table  cltb_cliente
(
	id_cliente Smallint NOT NULL,
	data_cadastro Date NULL ,
	nome_razao Varchar(50) NULL ,
	tipo_pessoa Char(1) NULL ,
	rg_inscr Varchar(20) NULL ,
	rg_expedicao Varchar(20) NULL ,
	cpf_cnpj Varchar(25) NULL ,
	email Varchar(255) NULL ,
	endereco Varchar(50) NULL ,
	complemento Varchar(50) NULL ,
	id_cidade Smallint NULL ,
	cidade Varchar(35) NULL ,
	estado Char(2) NULL ,
	CEP Char(10) NULL ,
	bairro Varchar(30) NULL ,
	fone_comercial Varchar(30) NULL ,
	fone_residencial Varchar(30) NULL ,
	fone_celular Varchar(30) NULL ,
	contato Varchar(20) NULL ,
	Banco Varchar(20) NULL ,
	conta_corrente Varchar(10) NULL ,
	agencia Varchar(10) NULL ,
	dia_pagamento Smallint NULL ,
	ativo Boolean NULL ,
	obs Text NULL ,
	provedor Boolean NULL  Default 'f',
	excluido Boolean NULL  Default false,
	info_cobranca Boolean NULL  Default false,
 primary key (id_cliente)
);

Create table  prtb_produto
(
	id_produto Smallint NOT NULL,
	nome Varchar(30) NULL ,
	descricao Text NULL ,
	tipo Char(2) NULL ,
	valor Numeric(7,2) NULL ,
	disponivel Boolean NULL ,
	num_emails Smallint NULL ,
	quota_por_conta integer NULL ,
	vl_email_adicional Numeric(7,2) NULL ,
	permitir_outros_dominios Boolean NULL ,
	email_anexado Boolean NULL ,
	numero_contas Smallint NULL ,
	comodato Boolean NULL ,
	valor_comodato Numeric(7,2) NULL ,
	desconto_promo Numeric(7,2) NULL ,
	periodo_desconto Smallint NULL ,
	tx_instalacao Numeric(7,2) NULL ,
 primary key (id_produto)
);

Create table  prtb_produto_discado
(
	id_produto Smallint NOT NULL,
	franquia_horas Smallint NULL ,
	permitir_duplicidade Boolean NULL  Default false,
	valor_hora_adicional Numeric(7,2) NULL ,
 primary key (id_produto)
);

Create table  prtb_produto_bandalarga
(
	id_produto Smallint NOT NULL,
	banda_upload_kbps Smallint NULL ,
	banda_download_kbps Smallint NULL ,
	franquia_trafego_mensal_gb Smallint NULL ,
	valor_trafego_adicional_gb Numeric(7,2) NULL ,
	roteado Boolean NULL ,
 primary key (id_produto)
);

Create table  prtb_produto_hospedagem
(
	id_produto Smallint NOT NULL,
	dominio Boolean NULL ,
	franquia_em_mb Smallint NULL ,
	valor_mb_adicional Numeric(7,2) NULL ,
 primary key (id_produto)
);

Create table  cbtb_cliente_produto
(
	id_cliente_produto Smallint NOT NULL,
	id_cliente Smallint NOT NULL,
	id_produto Smallint NOT NULL,
	dominio Varchar(255) NULL ,
	excluido Boolean NULL  Default false,
 primary key (id_cliente_produto)
);

Create table  cbtb_contrato
(
	id_cliente_produto Smallint NOT NULL,
	data_contratacao Date NULL ,
	vigencia Smallint NULL ,
	data_renovacao Date NULL ,
	valor_contrato Numeric(7,2) NULL ,
	id_cobranca Smallint NOT NULL,
	status Char(2) NULL ,
	tipo_produto Char(2) NULL ,
	valor_produto Numeric(7,2) NULL ,
	num_emails Smallint NULL ,
	quota_por_conta integer NULL ,
	tx_instalacao Numeric(7,2) NULL ,
	comodato Boolean NULL ,
	valor_comodato Numeric(7,2) NULL ,
	desconto_promo Numeric(7,2) NULL ,
	periodo_desconto Smallint NULL ,
	hosp_dominio Boolean NULL ,
	hosp_franquia_em_mb Smallint NULL ,
	hosp_valor_mb_adicional Numeric(7,2) NULL ,
	disc_franquia_horas Smallint NULL ,
	disc_permitir_duplicidade Boolean NULL ,
	disc_valor_hora_adicional Numeric(7,2) NULL ,
	bl_banda_upload_kbps Smallint NULL ,
	bl_banda_download_kbps Smallint NULL ,
	bl_franquia_trafego_mensal_gb Smallint NULL ,
	bl_valor_trafego_adicional_gb Numeric(7,2) NULL ,
	cod_banco Smallint NULL ,
	carteira Varchar(100) NULL ,
	agencia integer NULL ,
	num_conta integer NULL ,
	convenio integer NULL ,
	cc_vencimento Varchar(5) NULL ,
	cc_numero Varchar(25) NULL ,
	cc_operadora Char(2) NULL ,
	db_banco Smallint NULL ,
	db_agencia Smallint NULL ,
	db_conta Smallint NULL ,
	vencimento Smallint NULL ,
	carencia Smallint NULL ,
	data_alt_status Date NULL ,
	id_produto Smallint NULL ,
 primary key (id_cliente_produto)
);

Create table  dominio
(
	dominio Varchar(255) NOT NULL,
	id_cliente Smallint NULL ,
	provedor Boolean NULL  Default 'f',
	status Char(1) NULL ,
	dominio_provedor Boolean NULL ,
 primary key (dominio)
);

Create table  cftb_forma_pagamento
(
	id_cobranca Smallint NOT NULL,
	nome_cobranca Varchar(20) NULL ,
	disponivel Boolean NULL ,
 primary key (id_cobranca)
);

Create table  adtb_admin
(
	id_admin Smallint NOT NULL,
	admin Varchar(20) NOT NULL UNIQUE ,
	senha Varchar(64) NOT NULL,
	status Char(1) NULL ,
	nome Varchar(40) NULL ,
	email Varchar(255) NULL  UNIQUE ,
	primeiro_login Boolean NULL ,
 primary key (id_admin)
);

Create table  adtb_usuario_privilegio
(
	id_admin Smallint NOT NULL,
	id_priv Smallint NOT NULL,
	pode_gravar Boolean NULL ,
 primary key (id_admin,id_priv)
);

Create table  adtb_privilegio
(
	id_priv Smallint NOT NULL,
	cod_priv Varchar(60) NULL  UNIQUE ,
	nome Varchar(60) NULL ,
 primary key (id_priv)
);

Create table  cntb_conta
(
	username Varchar(30) NOT NULL,
	dominio Varchar(255) NOT NULL,
	tipo_conta Varchar(2) NOT NULL,
	senha Varchar(64) NULL ,
	id_cliente Smallint NULL ,
	id_cliente_produto Smallint NOT NULL,
	id_conta Smallint NULL  UNIQUE ,
	senha_cript Varchar(64) NULL ,
	conta_mestre Boolean NULL  Default true,
	status Char(1) NULL  Default 'A',
	observacoes Text NULL ,
 primary key (username,dominio,tipo_conta)
);

Create table  cntb_conta_hospedagem
(
	username Varchar(30) NOT NULL,
	tipo_conta Varchar(2) NOT NULL,
	dominio Varchar(255) NOT NULL,
	tipo_hospedagem Char(1) NULL ,
	senha_cript Varchar(64) NULL ,
	uid integer NULL ,
	gid integer NULL ,
	home Varchar(255) NULL ,
	shell Varchar(255) NULL ,
	dominio_hospedagem Varchar(255) NULL ,
 primary key (username,tipo_conta,dominio)
);

Create table  cntb_conta_bandalarga
(
	username Varchar(30) NOT NULL,
	tipo_conta Varchar(2) NOT NULL,
	dominio Varchar(255) NOT NULL,
	id_pop Smallint NOT NULL,
	tipo_bandalarga Char(1) NULL ,
	ipaddr Inet NULL ,
	rede Cidr NULL ,
	upload_kbps Smallint NULL ,
	download_kbps Smallint NULL ,
	status Char(1) NULL ,
	mac Macaddr NULL ,
	id_nas Smallint NOT NULL,
	ip_externo Inet NULL ,
 primary key (username,tipo_conta,dominio)
);

Create table  cntb_conta_discado
(
	username Varchar(30) NOT NULL,
	tipo_conta Varchar(2) NOT NULL,
	dominio Varchar(255) NOT NULL,
	foneinfo Varchar(64) NULL ,
 primary key (username,tipo_conta,dominio)
);

Create table  cntb_conta_email
(
	username Varchar(30) NOT NULL,
	tipo_conta Varchar(2) NOT NULL,
	dominio Varchar(255) NOT NULL,
	quota integer NULL ,
	email Varchar(255) NULL ,
 primary key (username,tipo_conta,dominio)
);

Create table  cftb_pop
(
	id_pop Smallint NOT NULL,
	nome Varchar(40) NOT NULL,
	info Text NULL ,
	tipo Varchar(2) NULL ,
	id_pop_ap Smallint NULL ,
	status Char(1) NULL  Default 'A',
 primary key (id_pop)
);

Create table  cftb_ip
(
	ipaddr Inet NOT NULL,
 primary key (ipaddr)
);

Create table  cftb_rede
(
	rede Cidr NOT NULL,
	tipo_rede Char(1) NULL ,
	id_rede Smallint NULL ,
 primary key (rede)
);

Create table  cftb_nas
(
	id_nas Smallint NOT NULL,
	nome Varchar(20) NOT NULL,
	ip Inet NOT NULL,
	secret Varchar(64) NULL ,
	tipo_nas Char(1) NULL ,
 primary key (id_nas)
);

Create table  cftb_nas_rede
(
	rede Cidr NOT NULL,
	id_nas Smallint NOT NULL,
 primary key (rede,id_nas)
);

Create table  cftb_preferencias
(
	id_provedor Smallint NOT NULL UNIQUE ,
	dominio_padrao Varchar(150) NULL ,
	nome Varchar(255) NULL ,
	localidade Varchar(255) NULL ,
	radius_server Inet NULL ,
	hosp_server Inet NULL ,
	hosp_ns1 Inet NULL ,
	hosp_ns2 Inet NULL ,
	hosp_uid Smallint NULL ,
	hosp_gid Smallint NULL ,
	mail_server Inet NULL ,
	mail_uid Smallint NULL ,
	mail_gid Smallint NULL ,
	pop_host Varchar(255) NULL ,
	smtp_host Varchar(255) NULL ,
	hosp_base Varchar(255) NULL ,
	tx_juros Smallint NULL ,
	multa Smallint NULL ,
	dia_venc Smallint NULL ,
	carencia Smallint NULL ,
	cnpj Char(25) NULL ,
	cod_banco Smallint NULL ,
	agencia Smallint NULL ,
	num_conta Smallint NULL ,
	observacoes Text NULL ,
	carteira Smallint NULL ,
	convenio Smallint NULL ,
	pagamento Char(3) NULL ,
 primary key (id_provedor)
);

Create table  lgtb_exclusao
(
	id_exclusao Smallint NOT NULL,
	admin Varchar(255) NULL ,
	data Timestamp NULL ,
	tipo Varchar(3) NULL ,
	id_excluido Smallint NULL ,
	observacao Text NULL ,
 primary key (id_exclusao)
);

Create table  cbtb_faturas
(
	id_cliente_produto Smallint NOT NULL,
	data Date NOT NULL,
	descricao Text NULL ,
	valor Numeric(7,2) NOT NULL,
	status Varchar(2) NULL ,
	observacoes Text NULL ,
	reagendamento Date NULL ,
	pagto_parcial Numeric(7,2) NULL ,
	data_pagamento Date NULL ,
	desconto Numeric(7,2) NULL ,
	acrescimo Numeric(7,2) NULL ,
	valor_pago Numeric(7,2) NULL ,
	id_cobranca Serial NOT NULL,
	cod_barra Varchar(50) NULL  UNIQUE ,
	anterior Boolean NULL  Default false,
	id_carne Smallint NULL ,
	nosso_numero Varchar(100) NULL ,
	linha_digitavel Varchar(150) NULL ,
	nosso_numero_banco Numeric(17,0) NULL ,
 primary key (id_cliente_produto,data)
);

Create table  lgtb_bloqueio_automatizado
(
	id_processo Smallint NOT NULL,
	id_cliente_produto Smallint NOT NULL,
	data_hora Timestamp NOT NULL Default now(),
	tipo Varchar(1) NOT NULL,
	admin Varchar(20) NULL ,
	auto_obs Varchar(50) NULL ,
	admin_obs Text NULL ,
 primary key (id_processo)
);

Create table  pftb_preferencia_cobranca
(
	id_provedor Smallint NOT NULL UNIQUE ,
	tx_juros Smallint NULL ,
	multa Smallint NULL ,
	dia_venc Smallint NULL ,
	carencia Smallint NULL ,
	cod_banco integer NULL ,
	carteira Varchar(10) NULL ,
	agencia integer NULL ,
	num_conta integer NULL ,
	convenio integer NULL ,
	observacoes Text NULL ,
	pagamento Char(3) NULL ,
	path_contrato Varchar(255) NULL ,
	cod_banco_boleto integer NULL ,
	carteira_boleto Varchar(10) NULL ,
	agencia_boleto integer NULL ,
	conta_boleto integer NULL ,
	convenio_boleto integer NULL ,
 primary key (id_provedor)
);

Create table  pftb_preferencia_geral
(
	id_provedor Smallint NOT NULL UNIQUE ,
	dominio_padrao Varchar(150) NULL ,
	nome Varchar(255) NULL ,
	radius_server Inet NULL ,
	hosp_server Inet NULL ,
	hosp_ns1 Inet NULL ,
	hosp_ns2 Inet NULL ,
	hosp_uid integer NULL ,
	hosp_gid integer NULL ,
	mail_server Inet NULL ,
	mail_uid integer NULL ,
	mail_gid integer NULL ,
	pop_host Varchar(255) NULL ,
	smtp_host Varchar(255) NULL ,
	hosp_base Varchar(255) NULL ,
	agrupar Smallint NULL ,
	email_base Varchar(255) NULL ,
 primary key (id_provedor)
);

Create table  pftb_preferencia_provedor
(
	id_provedor Smallint NOT NULL UNIQUE ,
	endereco Varchar(255) NULL ,
	localidade Varchar(255) NULL ,
	cep Varchar(20) NULL ,
	cnpj Char(25) NULL ,
	fone Varchar(30) NULL ,
 primary key (id_provedor)
);

Create table  lgtb_backup
(
	id_backup Smallint NOT NULL,
	admin Varchar(50) NULL ,
	data Date NULL ,
	nome_arq Varchar(100) NULL ,
	status Char(2) NULL ,
	bd_dados Boolean NULL ,
	bd_struct Boolean NULL ,
	cfg_so Boolean NULL ,
	cfg_vtx Boolean NULL ,
	cfg_utilitarios Boolean NULL ,
	obs Text NULL ,
 primary key (id_backup)
);

Create table  rdtb_accounting
(
	session_id Varchar(64) NOT NULL,
	username Varchar(64) NULL ,
	login Timestamp NULL  Default now(),
	logout Timestamp NULL ,
	tempo Numeric(30,0) NULL ,
	caller_id Varchar(30) NULL ,
	nas Varchar(128) NULL ,
	framed_ip_address Varchar(20) NULL ,
	terminate_cause Varchar(128) NULL ,
	bytes_in Numeric(30,0) NULL ,
	bytes_out Numeric(30,0) NULL ,
 primary key (session_id)
);

Create table  rdtb_log
(
	id_log Smallint NOT NULL Default nextval('rdtb_log_id_log'),
	datahora Timestamp NULL  Default now(),
	tipo Varchar(2) NULL ,
	username Varchar(64) NULL ,
	mensagem Text NULL ,
	caller_id Varchar(30) NULL ,
 primary key (id_log)
);

Create table  cbtb_carne
(
	id_carne Smallint NOT NULL,
	data_geracao Date NULL ,
	status Varchar(2) NULL  Default 'A',
	id_cliente_produto Smallint NULL ,
	valor Numeric(30,2) NULL ,
	vigencia Smallint NULL ,
	id_cliente Smallint NULL ,
 primary key (id_carne)
);

Create table  lgtb_reagendamento
(
	id_reagendamento Serial NOT NULL,
	id_cliente_produto Smallint NOT NULL,
	data Date NOT NULL,
	admin Smallint NULL ,
	data_reagendamento Date NULL  Default now(),
	data_para_reagendamento Date NULL ,
 primary key (id_reagendamento)
);

Create table  cftb_ip_externo
(
	ip_externo Inet NOT NULL,
	id_nas Smallint NOT NULL,
 primary key (ip_externo)
);

Create table  lgtb_retorno
(
	id_arquivo Serial NOT NULL,
	nome_arquivo Varchar(50) NULL ,
	tamanho Smallint NULL ,
	data Timestamp NULL ,
	qtde_registros Smallint NULL ,
	status Char(1) NULL ,
	NRA Char(2) NULL ,
	NRPR Char(2) NULL ,
	NRSC Char(2) NULL ,
	NRPE Char(2) NULL ,
	admin Varchar(20) NULL ,
	tipo_retorno Varchar(20) NULL ,
	agencia integer NULL ,
	dv_agencia integer NULL ,
	cedente integer NULL ,
	dv_cedente integer NULL ,
	convenente integer NULL ,
	nome_empresa Varchar(255) NULL ,
	seq_retorno integer NULL ,
 primary key (id_arquivo)
);

Create table  lgtb_retorno_faturas
(
	nsr Smallint NULL ,
	data_pagamento Date NULL ,
	data_credito Date NULL ,
	valor_recebido Numeric(7,2) NULL ,
	codigo_barras Varchar(50) NULL ,
	valor_tarifa Numeric(7,2) NULL ,
	status Char(2) NULL ,
	id_arquivo integer NOT NULL,
	motivo Varchar(100) NULL ,
	agencia integer NULL ,
	dv_agencia integer NULL ,
	cedente integer NULL ,
	dv_cedente integer NULL ,
	convenente integer NULL ,
	nome_empresa Varchar(20) NULL ,
	seq_retorno integer NULL 
);

Create table  cftb_banda
(
	banda Smallint NULL 
);

Create table  lgtb_contas_excluidas
(
	id_excluida Serial NOT NULL,
	id_cliente Varchar(100) NULL ,
	id_cliente_produto Varchar(100) NULL ,
	id_conta Varchar(100) NULL ,
	username Varchar(30) NULL ,
	tipo_conta Varchar(2) NULL ,
	dominio Varchar(255) NULL ,
	id_pop Varchar(100) NULL ,
	tipo_bandalarga Varchar(1) NULL ,
	ipaddr Varchar(100) NULL ,
	rede Varchar(100) NULL ,
	upload_kbps Varchar(100) NULL ,
	download_kbps Varchar(100) NULL ,
	status Char(1) NULL ,
	mac Varchar(100) NULL ,
	id_nas Varchar(100) NULL ,
	ip_externo Varchar(100) NULL ,
	quota Varchar(100) NULL ,
	email Varchar(255) NULL ,
	foneinfo Varchar(64) NULL ,
	tipo_hospedagem Char(1) NULL ,
	senha_cript Varchar(64) NULL ,
	uid Varchar(100) NULL ,
	gid Varchar(100) NULL ,
	home Varchar(255) NULL ,
	shell Varchar(255) NULL ,
	dominio_hospedagem Varchar(255) NULL ,
	senha Varchar(64) NULL ,
	conta_mestre Varchar(20) NULL ,
	observacoes Text NULL ,
	admin Varchar(255) NULL ,
	data_exclusao Date NULL  Default now(),
 primary key (id_excluida)
);

Create table  lgtb_renovacao
(
	id_cliente_produto Smallint NOT NULL,
	data_renovacao Date NULL ,
	data_proxima_renovacao Date NULL ,
	historico Text NULL ,
	id_renovacao Serial NOT NULL,
 primary key (id_renovacao)
);

Create table  lgtb_administradores
(
	id_admin Smallint NOT NULL,
	data Timestamp NULL  Default now(),
	operacao Varchar(255) NULL ,
	valor_original Varchar(100) NULL ,
	valor_alterado Varchar(100) NULL ,
	username Varchar(100) NULL ,
	id_fatura Smallint NULL ,
	tipo_conta Varchar(2) NULL ,
	ip Inet NULL 
);

Create table  bktb_backup
(
	id_backup Serial NOT NULL,
	data_backup Date NULL ,
	status_backup Varchar(4) NULL ,
	admin Smallint NULL ,
	operador_backup Varchar(2) NULL ,
	data Timestamp NULL  Default now(),
 primary key (id_backup)
);

Create table  cftb_backup
(
	path_backup Varchar(150) NULL ,
	ftp Varchar(255) NULL ,
	usuario Varchar(100) NULL ,
	senha Varchar(150) NULL 
);

Create table  bktb_arquivos
(
	id_backup integer NOT NULL,
	data_backup Date NULL ,
	tipo_backup Varchar(50) NULL ,
	arquivo_backup Varchar(150) NULL ,
	status_backup Varchar(4) NULL 
);

Create table  lgtb_restore
(
	id_restore Serial NOT NULL,
	arquivo_restore Varchar(255) NULL ,
	data_restore Timestamp NULL ,
	admin Varchar(50) NULL ,
	status_restore Varchar(4) NULL ,
 primary key (id_restore)
);

Create table  lgtb_remessas
(
	id_remessa Smallint NOT NULL,
	id_cliente_produto Smallint NULL ,
	data_remessa Timestamp NULL ,
	data_vencimento Date NULL ,
	valor Numeric(7,2) NULL ,
	periodo Smallint NULL ,
	mes Smallint NULL ,
	ano Smallint NULL 
);























































Alter table cftb_cidade add  foreign key (uf) references cftb_uf (uf)  on update restrict  on delete restrict ;
Alter table cltb_cliente add  foreign key (id_cidade) references cftb_cidade (id_cidade)  on update restrict  on delete restrict ;
Alter table cbtb_cliente_produto add  foreign key (id_cliente) references cltb_cliente (id_cliente)  on update restrict  on delete restrict ;
Alter table dominio add  foreign key (id_cliente) references cltb_cliente (id_cliente)  on update restrict  on delete restrict ;
Alter table prtb_produto_discado add  foreign key (id_produto) references prtb_produto (id_produto)  on update restrict  on delete restrict ;
Alter table prtb_produto_bandalarga add  foreign key (id_produto) references prtb_produto (id_produto)  on update restrict  on delete restrict ;
Alter table prtb_produto_hospedagem add  foreign key (id_produto) references prtb_produto (id_produto)  on update restrict  on delete restrict ;
Alter table cbtb_cliente_produto add  foreign key (id_produto) references prtb_produto (id_produto)  on update restrict  on delete restrict ;
Alter table cbtb_contrato add  foreign key (id_cliente_produto) references cbtb_cliente_produto (id_cliente_produto)  on update restrict  on delete restrict ;
Alter table cntb_conta add  foreign key (id_cliente_produto) references cbtb_cliente_produto (id_cliente_produto)  on update restrict  on delete restrict ;
Alter table lgtb_bloqueio_automatizado add  foreign key (id_cliente_produto) references cbtb_cliente_produto (id_cliente_produto)  on update restrict  on delete restrict ;
Alter table cbtb_faturas add  foreign key (id_cliente_produto) references cbtb_contrato (id_cliente_produto)  on update restrict  on delete restrict ;
Alter table cntb_conta add  foreign key (dominio) references dominio (dominio)  on update cascade  on delete restrict ;
Alter table cbtb_contrato add  foreign key (id_cobranca) references cftb_forma_pagamento (id_cobranca)  on update restrict  on delete restrict ;
Alter table adtb_usuario_privilegio add  foreign key (id_admin) references adtb_admin (id_admin)  on update restrict  on delete restrict ;
Alter table lgtb_administradores add  foreign key (id_admin) references adtb_admin (id_admin)  on update restrict  on delete restrict ;
Alter table adtb_usuario_privilegio add  foreign key (id_priv) references adtb_privilegio (id_priv)  on update restrict  on delete restrict ;
Alter table cntb_conta_email add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update cascade  on delete restrict ;
Alter table cntb_conta_discado add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update cascade  on delete restrict ;
Alter table cntb_conta_hospedagem add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update cascade  on delete restrict ;
Alter table cntb_conta_bandalarga add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update cascade  on delete restrict ;
Alter table cntb_conta_bandalarga add  foreign key (id_pop) references cftb_pop (id_pop)  on update restrict  on delete restrict ;
Alter table cftb_pop add  foreign key (id_pop_ap) references cftb_pop (id_pop)  on update restrict  on delete restrict ;
Alter table cntb_conta_bandalarga add  foreign key (ipaddr) references cftb_ip (ipaddr)  on update restrict  on delete restrict ;
Alter table cntb_conta_bandalarga add  foreign key (rede) references cftb_rede (rede)  on update restrict  on delete restrict ;
Alter table cftb_nas_rede add  foreign key (rede) references cftb_rede (rede)  on update restrict  on delete restrict ;
Alter table cftb_nas_rede add  foreign key (id_nas) references cftb_nas (id_nas)  on update restrict  on delete restrict ;
Alter table cntb_conta_bandalarga add  foreign key (id_nas) references cftb_nas (id_nas)  on update restrict  on delete restrict ;
Alter table cftb_ip_externo add  foreign key (id_nas) references cftb_nas (id_nas)  on update restrict  on delete restrict ;
Alter table lgtb_reagendamento add  foreign key (id_cliente_produto,data) references cbtb_faturas (id_cliente_produto,data)  on update restrict  on delete restrict ;
Alter table cbtb_faturas add  foreign key (id_carne) references cbtb_carne (id_carne)  on update restrict  on delete restrict ;
Alter table cntb_conta_bandalarga add  foreign key (ip_externo) references cftb_ip_externo (ip_externo)  on update restrict  on delete restrict ;
Alter table lgtb_retorno_faturas add  foreign key (id_arquivo) references lgtb_retorno (id_arquivo)  on update restrict  on delete restrict ;
Alter table bktb_arquivos add  foreign key (id_backup) references bktb_backup (id_backup)  on update restrict  on delete restrict ;


-- CRIANDO SEQUENCES

CREATE SEQUENCE adsq_id_admin;
CREATE SEQUENCE adsq_id_priv;
CREATE SEQUENCE cbsq_id_cliente_produto;
CREATE SEQUENCE cbsq_id_cobranca;
CREATE SEQUENCE cfsq_id_nas;
CREATE SEQUENCE cfsq_id_pop;
CREATE SEQUENCE cfsq_id_rede;
CREATE SEQUENCE clsq_id_cliente;
CREATE SEQUENCE clsq_id_cliente_produto;
CREATE SEQUENCE clsq_id_conta;
CREATE SEQUENCE prsq_id_produto;
CREATE SEQUENCE sptb_spool_id_spool;
CREATE SEQUENCE lgsq_id_exclusao;
CREATE SEQUENCE lgtb_backup_id_backup;
CREATE SEQUENCE rdtb_accounting_session_id;
CREATE SEQUENCE rdtb_log_id_log;
CREATE SEQUENCE rdsq_id_accounting;
CREATE SEQUENCE cbsq_id_carne;
CREATE SEQUENCE blsq_carne_nossonumero;
CREATE SEQUENCE cnsq_id_conta;
CREATE SEQUENCE blsq_nosso_numero_banco;
CREATE SEQUENCE lgsq_id_remessa;


-- POPULANDO A TABELA DE FORMAS DE PAGAMENTO
INSERT INTO cftb_forma_pagamento (id_cobranca,nome_cobranca,disponivel) VALUES ('1','Boleto Bancário',false);
INSERT INTO cftb_forma_pagamento (id_cobranca,nome_cobranca,disponivel) VALUES ('2','Carnê',false);
INSERT INTO cftb_forma_pagamento (id_cobranca,nome_cobranca,disponivel) VALUES ('3','Outras Formas',false);



-- POPULANDO A TABELA DE BANDAS
INSERT INTO cftb_banda (banda) VALUES ('0');
INSERT INTO cftb_banda (banda) VALUES ('32');
INSERT INTO cftb_banda (banda) VALUES ('64');
INSERT INTO cftb_banda (banda) VALUES ('96');
INSERT INTO cftb_banda (banda) VALUES ('128');
INSERT INTO cftb_banda (banda) VALUES ('192');
INSERT INTO cftb_banda (banda) VALUES ('256');
INSERT INTO cftb_banda (banda) VALUES ('384');
INSERT INTO cftb_banda (banda) VALUES ('512');
INSERT INTO cftb_banda (banda) VALUES ('768');
INSERT INTO cftb_banda (banda) VALUES ('1024');


-- POPULANDO A TABELA DE PRIVILEGIOS
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (1, '_ADMIN', 'administradores');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (2, '_CLIENTES', 'clientes');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (3, '_CLIENTES_FICHA', 'clientes::ficha');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (4, '_CLIENTES_BANDALARGA', 'clientes::contas::banda larga');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (5, '_CLIENTES_DISCADO', 'clientes::contas::discado');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (6, '_CLIENTES_EMAIL', 'clientes::contas::email');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (7, '_CLIENTES_HOSPEDAGEM', 'clientes::contas::hospedagem');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (8, '_CLIENTES_COBRANCA', 'clientes::contas::cobrança');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (9, '_COBRANCA', 'cobrança');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (10, '_SUPORTE', 'suporte');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (12, '_CONFIG_MONITORAMENTO', 'configurações::monitoramento');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (13, '_CLIENTES_COBRANCA_ELIMINAR_CONTRATO', 'configurações::cobranca::eliminar contrato');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (14, '_ELIMINAR_CLIENTE', 'clientes:eliminar cliente');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (15, '_RELATORIOS_CLIENTE', 'cliente::relatorios');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (16, '_COBRANCA_BLOQUEIOS', 'cobranca::bloqueios');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (17, '_COBRANCA_PRODUTOS', 'cobranca:: cadastrar produtos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (19, '_COBRANCA_RETORNOS', 'cobranca::processar retornos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (20, '_RELATORIOS_COBRANCA', 'cobranca::relatorios');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (21, '_CONFIG_EQUIPAMENTOS', 'configuracao::equipamentos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (22, '_CONFIG_PREFERENCIAS', 'configuracao:preferencias');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (23, '_SUPORTE_BACKUP', 'suporte::backup');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (11, '_CONFIG', 'configurações::relatorios');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (24, '_CONFIG_REGISTRO', 'configurações::registro do sistema');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (25, '_RELATORIO_CONFIG', 'configurações::relatorios');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (26, '_RELATORIO_OUTROS', 'relatorios gerais');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (27, '_FATURAMENTO', 'faturamento');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (18, '_COBRANCA_FATURAS', 'cobranca::emitir faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (28, '_ADMIN_LOG', 'administradores::log de acesso');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome) VALUES (29, '_ADMIN_PRIV', 'administradores::privilegios');


-- CRIANDO O USUARIO ADMIN
INSERT INTO adtb_admin(id_admin,admin,senha,status,nome,email,primeiro_login) VALUES (1, 'admin', md5('admin123'), 'A','Administrador','web@mosman.com.br',true);


-- DANDO TODOS OS PRIVILEGIOS PARA ADMIN
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 1, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 28, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 29, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 15, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 2, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 4, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 8, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 5, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 6, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 7, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 14, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 3, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 9, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 16, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 17, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 18, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 19, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 20, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 21, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 22, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 13, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 12, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 24, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 11, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 25, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 27, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 26, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 10, true);
INSERT INTO adtb_usuario_privilegio (id_admin, id_priv, pode_gravar) VALUES (1, 23, true);


-- POPULANDO A TABELA DE ESTADOS
INSERT INTO cftb_uf VALUES ('AC', 'Acre');
INSERT INTO cftb_uf VALUES ('AL', 'Alagoas');
INSERT INTO cftb_uf VALUES ('AM', 'Amazonas');
INSERT INTO cftb_uf VALUES ('AP', 'Amapá');
INSERT INTO cftb_uf VALUES ('BA', 'Bahia');
INSERT INTO cftb_uf VALUES ('CE', 'Ceará');
INSERT INTO cftb_uf VALUES ('DF', 'Distrito Federal');
INSERT INTO cftb_uf VALUES ('ES', 'Espírito Santo');
INSERT INTO cftb_uf VALUES ('GO', 'Goiás');
INSERT INTO cftb_uf VALUES ('MA', 'Maranhão');
INSERT INTO cftb_uf VALUES ('MG', 'Minas Gerais');
INSERT INTO cftb_uf VALUES ('MS', 'Mato Grosso do Sul');
INSERT INTO cftb_uf VALUES ('MT', 'Mato Grosso');
INSERT INTO cftb_uf VALUES ('PA', 'Pará');
INSERT INTO cftb_uf VALUES ('PB', 'Paraíba');
INSERT INTO cftb_uf VALUES ('PE', 'Pernambuco');
INSERT INTO cftb_uf VALUES ('PI', 'Piauí');
INSERT INTO cftb_uf VALUES ('PR', 'Paraná');
INSERT INTO cftb_uf VALUES ('RJ', 'Rio de Janeiro');
INSERT INTO cftb_uf VALUES ('RN', 'Rio Grande do Norte');
INSERT INTO cftb_uf VALUES ('RO', 'Rondônia');
INSERT INTO cftb_uf VALUES ('RR', 'Roraima');
INSERT INTO cftb_uf VALUES ('RS', 'Rio Grande do Sul');
INSERT INTO cftb_uf VALUES ('SC', 'Santa Catarina');
INSERT INTO cftb_uf VALUES ('SE', 'Sergipe');
INSERT INTO cftb_uf VALUES ('SP', 'São Paulo');
INSERT INTO cftb_uf VALUES ('TO', 'Tocantins');



-- SETANDO AS SEQUENCES DO SISTEMA
SELECT setval('adsq_id_admin',(select max(id_admin) from adtb_admin));
SELECT setval('adsq_id_priv',(select max(id_priv) from adtb_privilegio));


