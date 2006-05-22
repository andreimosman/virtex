/*
Created		22/2/2006
Modified		19/5/2006
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
	rg_inscr Char(20) NULL ,
	rg_expedicao Varchar(20) NULL ,
	cpf_cnpj Char(25) NULL ,
	email Varchar(255) NULL ,
	endereco Varchar(50) NULL ,
	complemento Varchar(20) NULL ,
	id_cidade Smallint NULL ,
	cidade Varchar(20) NULL ,
	estado Char(2) NULL ,
	CEP Char(10) NULL ,
	bairro Varchar(15) NULL ,
	fone_comercial Varchar(15) NULL ,
	fone_residencial Varchar(15) NULL ,
	fone_celular Varchar(15) NULL ,
	contato Varchar(20) NULL ,
	Banco Varchar(20) NULL ,
	conta_corrente Varchar(10) NULL ,
	agencia Varchar(10) NULL ,
	dia_pagamento Smallint NULL ,
	ativo Boolean NULL ,
	obs Text NULL ,
	provedor Boolean NULL  Default 'f',
	excluido Boolean NULL  Default false,
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
	quota_por_conta Smallint NULL ,
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
	valor_contrato Numeric(30,6) NULL ,
	id_cobranca Smallint NOT NULL,
	status Char(2) NULL ,
	tipo_produto Char(2) NULL ,
	valor_produto Numeric(7,2) NULL ,
	num_emails Smallint NULL ,
	quota_por_conta Smallint NULL ,
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
	carteira Smallint NULL ,
	agencia Smallint NULL ,
	num_conta Smallint NULL ,
	convenio Smallint NULL ,
	cc_vencimento Varchar(5) NULL ,
	cc_numero Varchar(25) NULL ,
	cc_operadora Char(2) NULL ,
	db_banco Smallint NULL ,
	db_agencia Smallint NULL ,
	db_conta Smallint NULL ,
	vencimento Smallint NULL ,
	carencia Smallint NULL ,
	data_alt_status Date NULL ,
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
	uid Smallint NULL ,
	gid Smallint NULL ,
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
	ip_externo Inet NOT NULL,
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
	quota Smallint NULL ,
	email Varchar(255) NULL ,
 primary key (username,tipo_conta,dominio)
);

Create table  cftb_pop
(
	id_pop Smallint NOT NULL,
	nome Varchar(40) NOT NULL UNIQUE ,
	info Text NULL ,
	tipo Varchar(2) NULL ,
	id_pop_ap Smallint NULL ,
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
	id_cobranca Serial NULL ,
	cod_barra Varchar(50) NULL  UNIQUE ,
	anterior Boolean NULL  Default false,
	id_carne Smallint NOT NULL,
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
	cod_banco Smallint NULL ,
	carteira Smallint NULL ,
	agencia Smallint NULL ,
	num_conta Smallint NULL ,
	convenio Smallint NULL ,
	observacoes Text NULL ,
	pagamento Char(3) NULL ,
	path_contrato Varchar(255) NULL ,
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
	hosp_uid Smallint NULL ,
	hosp_gid Smallint NULL ,
	mail_server Inet NULL ,
	mail_uid Smallint NULL ,
	mail_gid Smallint NULL ,
	pop_host Varchar(255) NULL ,
	smtp_host Varchar(255) NULL ,
	hosp_base Varchar(255) NULL ,
 primary key (id_provedor)
);

Create table  pftb_preferencia_provedor
(
	id_provedor Smallint NOT NULL UNIQUE ,
	endereco Varchar(255) NULL ,
	localidade Varchar(255) NULL ,
	cep Varchar(20) NULL ,
	cnpj Char(25) NULL ,
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
	data_geracao Smallint NULL ,
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
Alter table cntb_conta add  foreign key (dominio) references dominio (dominio)  on update restrict  on delete restrict ;
Alter table cbtb_contrato add  foreign key (id_cobranca) references cftb_forma_pagamento (id_cobranca)  on update restrict  on delete restrict ;
Alter table adtb_usuario_privilegio add  foreign key (id_admin) references adtb_admin (id_admin)  on update restrict  on delete restrict ;
Alter table adtb_usuario_privilegio add  foreign key (id_priv) references adtb_privilegio (id_priv)  on update restrict  on delete restrict ;
Alter table cntb_conta_email add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update restrict  on delete restrict ;
Alter table cntb_conta_discado add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update restrict  on delete restrict ;
Alter table cntb_conta_hospedagem add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update restrict  on delete restrict ;
Alter table cntb_conta_bandalarga add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update restrict  on delete restrict ;
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


