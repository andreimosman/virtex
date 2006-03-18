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
	id_cobranca Smallint NOT NULL,
	data_contratacao Date NULL ,
	vigencia Smallint NULL ,
	data_renovacao Date NULL ,
	valor_contrato Numeric(30,6) NULL ,
 primary key (id_cliente_produto,id_cobranca)
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

Create table  cbtb_cobranca
(
	id_cobranca Smallint NOT NULL,
	nome_cobranca Varchar(20) NULL ,
	tipo_cobranca Varchar(3) NULL ,
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
 primary key (username,tipo_conta,dominio)
);

Create table  cntb_conta_discado
(
	username Varchar(30) NOT NULL,
	tipo_conta Varchar(2) NOT NULL,
	dominio Varchar(255) NOT NULL,
	foneinfo Varchar(15) NULL ,
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
Alter table cntb_conta add  foreign key (dominio) references dominio (dominio)  on update restrict  on delete restrict ;
Alter table cbtb_contrato add  foreign key (id_cobranca) references cbtb_cobranca (id_cobranca)  on update restrict  on delete restrict ;
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


