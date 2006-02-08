/*
Created		7/8/2004
Modified		6/2/2006
Project		Virtex Admin
Model		Geral
Company		Virtex ISP
Author		Andrei de Oliveira Mosman
Version		0.1
Database		PostgreSQL 7.3 (beta) 
*/




Create table  cltb_cliente
(
	id_cliente Smallint NOT NULL,
	data_cadastro Varchar(10) NULL ,
	nome_razao Varchar(50) NULL ,
	tipo_pessoa Char(1) NULL ,
	rg_inscr Char(20) NULL ,
	expedicao Char(10) NULL ,
	cpf_cnpj Char(25) NULL ,
	email Varchar(255) NULL ,
	endereco Varchar(50) NULL ,
	complemento Varchar(20) NULL ,
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
 primary key (id_cliente)
);

Create table  prtb_produto
(
	id_produto Smallint NOT NULL,
	nome Varchar(15) NULL ,
	descricao Text NULL ,
	tipo Char(2) NULL ,
	valor Numeric(7,2) NULL ,
	disponivel Boolean NULL ,
 primary key (id_produto)
);

Create table  prtb_produto_discado
(
	id_produto Smallint NOT NULL,
	franquia_horas Smallint NULL ,
	permitir_duplicidade Boolean NULL  Default false,
	valor_hora_adicional Numeric(7,2) NULL ,
	num_emails Smallint NULL ,
	quota_por_conta Smallint NULL ,
	vl_email_adicional Numeric(7,2) NULL ,
	permitir_outros_dominios Boolean NULL ,
	email_anexado Boolean NULL ,
 primary key (id_produto)
);

Create table  prtb_produto_bandalarga
(
	id_produto Smallint NOT NULL,
	banda_upload_kbps Smallint NULL ,
	banda_download_kbps Smallint NULL ,
	franquia_trafego_mensal_gb Smallint NULL ,
	valor_trafego_adicional_gb Numeric(7,2) NULL ,
	num_emails Smallint NULL ,
	quota_por_conta Smallint NULL ,
	vl_email_adicional Numeric(7,2) NULL ,
	permitir_outros_dominios Boolean NULL ,
	email_anexado Boolean NULL ,
 primary key (id_produto)
);

Create table  prtb_produto_hospedagem
(
	id_produto Smallint NOT NULL,
	dominio Boolean NULL ,
	franquia_em_mb Smallint NULL ,
	valor_mb_adicional Numeric(7,2) NULL ,
	num_emails Smallint NULL ,
	quota_por_conta Smallint NULL ,
	vl_email_adicional Numeric(7,2) NULL ,
	permitir_outros_dominios Boolean NULL ,
	email_anexado Boolean NULL ,
 primary key (id_produto)
);

Create table  cbtb_cliente_produto
(
	id_cliente_produto Smallint NOT NULL,
	id_cliente Smallint NOT NULL,
	id_produto Smallint NOT NULL,
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
 primary key (id_cliente_produto,id_cobranca)
);

Create table  cntb_conta
(
	id_cliente_produto Smallint NOT NULL,
	username Varchar(30) NOT NULL,
	dominio Varchar(255) NOT NULL,
	tipo_conta Char(2) NOT NULL,
	senha Varchar(64) NULL ,
 primary key (username,dominio,tipo_conta)
);

Create table  dominio
(
	dominio Varchar(255) NOT NULL,
	id_cliente Smallint NOT NULL,
 primary key (dominio)
);

Create table  cftb_rede
(
	rede Cidr NOT NULL,
	username_conta Varchar(30) NOT NULL,
	username Varchar(30) NOT NULL,
	dominio Varchar(255) NOT NULL,
	tipo_conta Char(2) NOT NULL,
 primary key (rede)
);

Create table  cftb_pop
(
	id_pop Smallint NOT NULL,
	nome Varchar(10) NOT NULL UNIQUE ,
	info Text NULL ,
	interface Varchar(8) NULL ,
	username_conta Varchar(30) NOT NULL,
	username Varchar(30) NOT NULL,
	dominio Varchar(255) NOT NULL,
	tipo_conta Char(2) NOT NULL,
 primary key (id_pop)
);

Create table  cftb_pppoe
(
	id_pppoe Smallint NOT NULL UNIQUE ,
	nome Varchar(15) NULL ,
	ipaddr Inet NULL ,
 primary key (id_pppoe)
);

Create table  cftb_ip
(
	ipaddr Inet NOT NULL,
	id_pppoe Smallint NOT NULL,
	username_conta Varchar(30) NOT NULL,
	username Varchar(30) NOT NULL,
	dominio Varchar(255) NOT NULL,
	tipo_conta Char(2) NOT NULL,
 primary key (ipaddr)
);

Create table  cbtb_cobranca
(
	id_cobranca Smallint NOT NULL,
	nome_cobranca Varchar(20) NULL ,
	tipo_cobranca Varchar(3) NULL ,
 primary key (id_cobranca)
);

Create table  contas
(
	username_conta Varchar(30) NOT NULL,
	id_pop Smallint NOT NULL,
	quota Smallint NULL ,
	foneinfo Varchar(15) NULL ,
	ipaddr Inet NULL ,
	rede Cidr NULL ,
	tipo_bandalarga Char(1) NULL ,
	upload_kbps Smallint NULL ,
	download_kbps Smallint NULL ,
	status Char(1) NULL ,
	username Varchar(30) NOT NULL,
	dominio Varchar(255) NOT NULL,
	tipo_conta Char(2) NOT NULL,
 primary key (username_conta,username,dominio,tipo_conta)
);





















Create index clix_cliente_nome_razao on cltb_cliente using btree( nome_razao );
Create index clix_cliente_rg_inscr on cltb_cliente using btree( rg_inscr );
Create index clix_cliente_cpf_cnpj on cltb_cliente using btree( cpf_cnpj );
Create index clix_cliente_dia_pagto_nome_raz on cltb_cliente using btree( dia_pagamento,nome_razao );
Create index prix_produto_nome on prtb_produto using btree( nome );
Create index cbix_cliente_produto_idc_idp on cbtb_cliente_produto using btree( id_cliente,id_produto );
Create index cnix_conta_user_dom on cntb_conta using btree( username,dominio );



Alter table cbtb_cliente_produto add  foreign key (id_cliente) references cltb_cliente (id_cliente)  on update restrict  on delete restrict ;
Alter table dominio add  foreign key (id_cliente) references cltb_cliente (id_cliente)  on update restrict  on delete restrict ;
Alter table prtb_produto_discado add  foreign key (id_produto) references prtb_produto (id_produto)  on update restrict  on delete restrict ;
Alter table prtb_produto_bandalarga add  foreign key (id_produto) references prtb_produto (id_produto)  on update restrict  on delete restrict ;
Alter table prtb_produto_hospedagem add  foreign key (id_produto) references prtb_produto (id_produto)  on update restrict  on delete restrict ;
Alter table cbtb_cliente_produto add  foreign key (id_produto) references prtb_produto (id_produto)  on update restrict  on delete restrict ;
Alter table cbtb_contrato add  foreign key (id_cliente_produto) references cbtb_cliente_produto (id_cliente_produto)  on update restrict  on delete restrict ;
Alter table cntb_conta add  foreign key (id_cliente_produto) references cbtb_cliente_produto (id_cliente_produto)  on update restrict  on delete restrict ;
Alter table contas add  foreign key (username,dominio,tipo_conta) references cntb_conta (username,dominio,tipo_conta)  on update restrict  on delete restrict ;
Alter table cntb_conta add  foreign key (dominio) references dominio (dominio)  on update restrict  on delete restrict ;
Alter table cftb_ip add  foreign key (id_pppoe) references cftb_pppoe (id_pppoe)  on update restrict  on delete restrict ;
Alter table cbtb_contrato add  foreign key (id_cobranca) references cbtb_cobranca (id_cobranca)  on update restrict  on delete restrict ;
Alter table cftb_pop add  foreign key (username_conta,username,dominio,tipo_conta) references contas (username_conta,username,dominio,tipo_conta)  on update restrict  on delete restrict ;
Alter table cftb_rede add  foreign key (username_conta,username,dominio,tipo_conta) references contas (username_conta,username,dominio,tipo_conta)  on update restrict  on delete restrict ;
Alter table cftb_ip add  foreign key (username_conta,username,dominio,tipo_conta) references contas (username_conta,username,dominio,tipo_conta)  on update restrict  on delete restrict ;



