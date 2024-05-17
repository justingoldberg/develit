<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 08/01/2004
# Ultima altera��o: 11/01/2005
#    Altera��o No.: 004
#
# Fun��o:
#    P�gina principal (index) da aplica��o

# Carregar Sessoes para informa��es de usuario
session_start();
session_register("sessLogin","sessCadastro","sessPassPhrase");

# Verifica se sess�o deve ser matada
if($modulo=='logoff' && session_is_registered("sessLogin")) {
	session_destroy();
	$sessLogin[login]='';
	$sessLogin[senha]='';
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
include('config/isp-it.php');
include('config/ticket-it.php');

# Vari�veis globais
global $configHostMySQL, $configUserMySQL, $configPasswdMySQL, $configDBMySQL, $configAppName, $configAppVersion, $corFundo, $corBorda;

# Carregar fun��es
include('includes/db.php');
include('includes/html.php');
include('includes/menu.php');
include('includes/data.php');

# Valida��o de Formul�rios
include('includes/valida_form.php');

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

# Parametros
include('includes/parametros.php');

# M�quinas
include('includes/maquinas.php');

# Programas
include('includes/programas.php');

# Users
include('includes/users.php');

# Passphrase - Valida��o
include('includes/passphrase.php');

# Perfil
include('includes/perfil.php');

# Empresas
include('includes/empresas.php');

# Tickets
include('includes/tickets.php');

# ISP-IT
include('includes/isp-it.php');

# Ticket-IT
include('includes/ticket-it.php');


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

### Passphrase
if($matValidaPassPhrase) {
	$sessPassPhrase=alimentaForm($matValidaPassPhrase, $sessPassPhrase);
}
### Passphrase

###########################################
# Conectar com banco de dados
$conn=conectaMySQL($configHostMySQL, $configUserMySQL, $configPasswdMySQL);
$db=selecionaDB($configDBMySQL);
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
novaTabela("$configAppName - $configAppVersion - $sessLogin[login]", "center", 760, 0, 2, 1, $corFundo, $corBorda, 0);
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
					
						## Verificar se acesso � com ou sem login
						if($sessLogin) {
							# Menu de visualiza��o resumida - principal
							verMenu($modulo, $sub, $acao, $registro, $matriz);
						}
						else {
							verMenu($modulo, $sub, $acao, $registro, $matriz);
						}
					
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
