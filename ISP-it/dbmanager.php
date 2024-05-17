<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 20/05/2003
# Ultima altera��o: 03/02/2004
#    Altera��o No.: 041
#
# Fun��o:
#    P�gina principal (index) da aplica��o

# Carregar Sessoes para informa��es de usuario
session_start();
session_register("sessLogin","sessCadastro");

# Verifica se sess�o deve ser matada
if($modulo=='logoff' && session_is_registered("sessLogin")) {
	session_destroy();
	$sessLogin[login]='';
	$sessSenha[senha]='';
}


# Verifica se � necess�rio zerar a sess�o de cadastro
if($acao=='novo') {
	$sessCadastro='';
	$acao='adicionar';
}


# Carregar configura��es
include('config/config.php');
include('config/db.php');
include('config/html.php');
include('config/cobranca.php');
include('config/pessoas.php');
include('config/arquivo.php');
include('config/radius.php');
include('config/manager.php');
include('config/ocorrencias.php');
include('config/pdf.php');
include('config/custom.php');

# Vari�veis globais
global $configHostMySQL, $configUserMySQL, $configPasswdMySQL, $configDBMySQL, $configAppName, $configAppVersion, $corFundo, $corBorda;

# Carregar fun��es
include('includes/db.php');
include('includes/html.php');
include('includes/data.php');
include('includes/arquivos.php');
include('includes/arquivos_layout.php');

# Valida��o de Formul�rios
include('includes/valida_form.php');
include('includes/valida_documentos.php');

# Fun��es de E-mail
include('includes/mail.php');

# Fun��es de Usuario
include('includes/usuarios.php');

# Fun��es de Grupos
include('includes/grupos.php');
include('includes/usuarios_grupos.php');

# Paginador
include('includes/paginador.php');

# Formul�rios
include('includes/form.php');

# HOME / Principal
include('includes/home.php');

# Configura��es
include('includes/configuracoes.php');
include('includes/cidades.php');
include('includes/tipo_documentos.php');
include('includes/tipo_enderecos.php');
include('includes/tipo_pessoas.php');
include('includes/tipo_cobranca.php');
include('includes/vencimentos.php');
include('includes/unidades.php');
include('includes/modulos.php');
include('includes/parametros.php');
include('includes/parametros_modulos.php');
include('includes/bancos.php');
include('includes/forma_cobranca.php');
include('includes/servicos.php');
include('includes/servicos_parametros.php');
include('includes/status_servicos.php');
include('includes/condominios.php');

# Cadastros
include('includes/cadastros.php');
include('includes/pessoas.php');
include('includes/pessoa_tipo.php');
include('includes/form_pessoas.php');
include('includes/enderecos.php');
include('includes/documentos.php');
include('includes/pop.php');
include('includes/pop_cidade.php');

# Lan�amentos
include('includes/lancamentos.php');
include('includes/planos.php');
include('includes/planos_servicos.php');
include('includes/planos_servicos_descontos.php');
include('includes/planos_servicos_adicionais.php');
include('includes/manutencao.php');

# Formulas e calculos
include('includes/planos_formulas.php');

# Faturamento
include('includes/faturamento.php');
include('includes/faturamento_clientes.php');
include('includes/faturamento_documentos_gerados.php');
include('includes/faturamento_planos_documentos_gerados.php');
include('includes/faturamento_servicos_planos_documentos_gerados.php');

# Arquivos Remessa
include('includes/arquivos_remessa.php');

# Arquivos Retorno
include('includes/arquivos_retorno.php');

# Contas a Receber
include('includes/contas_receber.php');

# Exporta��o de dados
include('includes/exportacao.php');

# Administra��o de Configura��es
include('includes/administracao.php');
include('includes/administracao_limites.php');
include('includes/administracao_modulos.php');
include('includes/administracao_radius.php');
include('includes/administracao_dominio.php');
include('includes/administracao_mail.php');

# Radius
include('includes/radius.php');
include('includes/radius_grupos.php');
include('includes/radius_usuarios.php');

# Radius - Usu�rios Pessoas
include('includes/radius_usuarios_pessoas.php');

# Servicos por Grupo - Radius
include('includes/radius_servicos.php');

# Dominios
include('includes/dominios.php');
include('includes/dominios_servicos_planos.php');
include('includes/dominios_servicos_parametros.php');

# Emails
include('includes/email.php');
include('includes/email_config.php');
include('includes/email_dominios.php');
include('includes/email_alias.php');
include('includes/email_forward.php');
include('includes/email_autoreply.php');

# Ocorr�ncias
include('includes/ocorrencias.php');
include('includes/ocorrencias_comentarios.php');
include('includes/prioridades.php');

# Manager
include('includes/manager.php');

# DBManager
include('includes/dbmanager/dbmanager.php');
include('includes/dbmanager/importacao.php');


### CARRETAMENTO DE VALORES PARA SESSION - Valida��o
if($matValida) {
	$sessLogin=usuariosValidaForm($matValida);
}
###

### CARRETAMENTO DE VALORES PARA SESSSION
if($modulo=='cadastros') {
	if(!$matriz[bntConfirmar]) {
		$matriz[bntConfirmar]='';
	}
	$sessCadastro=alimentaForm($matriz, $sessCadastro);
	$matriz=$sessCadastro;
}
###

###########################################
# Conectar com banco de dados
$conn=conectaMySQL($configHostMySQL, $configUserMySQL, $configPasswdMySQL);
$db=selecionaDB($configDBMySQL, $conn);
###########################################


###########################################
# Mostrar cabe�alhos
html_header($configAppName, $configAppVersion);
###########################################

$data=dataSistema();

### Javascript ###
include('includes/javascript.php');
### Javascript ###


###########################################
# Checar conex�o com banco de dados
if(!$conn)
{
        aviso("Conex�o com MySQL", "Erro na conex�o com Banco de Dados MySQL", "#", 500);
}
if(!$db)
{
        aviso("Banco de Dados", "Erro ao selecionar banco de dados", "#", 500);
}
# fim de conex�o com banco de dados
###########################################


# Montar tela -  Quadro Principal

# Mostrar menu principal
novaTabela("ISP-IT DBManager - $sessLogin[login]", "center", 760, 0, 2, 1, $corFundo, $corBorda, 0);
	htmlAbreLinha($corFundo);
		htmlAbreColuna(760, 'center', $corFundo, 0, 'normal');
		
			# verificar se usuario est� conectado
			#if(!$sessLogin[login] || !$sessLogin[senha] || $modulo=='login' || $modulo=='logoff') {
			if($modulo=='login' || $modulo=='logoff' || ($sessLogin && !checaLogin($sessLogin, $modulo, $sub, $acao, $registro))) {
				# Usu�rio n�o est� conectado
				validacao($sessLogin, $modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Menu Principal
				htmlAbreTabelaSH("center", 760, 0, 0, 0, $corFundo, $corBorda, 2);
					htmlAbreLinha($corFundo);
						htmlAbreColuna('100%', 'center', $corFundo, 0, 'normal');
						if($sessLogin && checaLogin($sessLogin, $modulo, $sub, $acao, $registro)) menuPrincipal('usuario');
						else menuPrincipal('anonimo');
						htmlFechaColuna();
					htmlFechaLinha();
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 3, 'normal');
					
					htmlAbreLinha($corFundo);
						htmlAbreColuna('100%', 'center', $corFundo, 3, 'normal');
							verMenu($modulo, $sub, $acao, $registro, $matriz);
						htmlFechaColuna();
					htmlFechaLinha();
				fechaTabela();
				
			} #fecha verifica��o de login
			
			htmlFechaColuna();
	htmlFechaLinha();
fechaTabela();
# Fecha separa��o

htmlFechaColuna();
htmlFechaLinha();
fechaTabela();
# Fecha menu principal


###########################################
# Rodap�
html_footer($configAppName." - ".$configAppVersion);
###########################################

?>
