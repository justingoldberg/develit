<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/01/2004
# Ultima alteração: 11/01/2005
#    Alteração No.: 004
#
# Função:
#    Página principal (index) da aplicação

# Carregar Sessoes para informações de usuario
session_start();
session_register("sessLogin","sessCadastro","sessPassPhrase");

# Verifica se sessão deve ser matada
if($modulo=='logoff' && session_is_registered("sessLogin")) {
	session_destroy();
	$sessLogin[login]='';
	$sessLogin[senha]='';
}

# Verifica se é necessário zerar a sessão de cadastro
if($acao=='novo') {
	$sessCadastro='';
	$acao='adicionar';
}


# Carregar configurações
include('config/config.php');
include('config/db.php');
include('config/html.php');
include('config/isp-it.php');
include('config/ticket-it.php');

# Variáveis globais
global $configHostMySQL, $configUserMySQL, $configPasswdMySQL, $configDBMySQL, $configAppName, $configAppVersion, $corFundo, $corBorda;

# Carregar funções
include('includes/db.php');
include('includes/html.php');
include('includes/menu.php');
include('includes/data.php');

# Validação de Formulários
include('includes/valida_form.php');

# Funções de E-mail
include('includes/mail.php');

# Funções de Usuario
include('includes/usuarios.php');

# Funções de Grupos
include('includes/grupos.php');
include('includes/usuarios_grupos.php');

# Paginador
include('includes/paginador.php');

# Formulários
include('includes/form.php');

# HOME / Principal
include('includes/home.php');

# Configurações
include('includes/configuracoes.php');

# Parametros
include('includes/parametros.php');

# Máquinas
include('includes/maquinas.php');

# Programas
include('includes/programas.php');

# Users
include('includes/users.php');

# Passphrase - Validação
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


### CARRETAMENTO DE VALORES PARA SESSION - Validação
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
# Mostrar cabeçalhos
html_header($configAppName, $configAppVersion);
###########################################

$data=dataSistema();

### Javascript ###
include('includes/javascript.php');
### Javascript ###


###########################################
# Checar conexão com banco de dados
if(!$conn)
{
        aviso("Conexão com MySQL", "Erro na conexão com Banco de Dados MySQL", "#", 500);
}
if(!$db)
{
        aviso("Banco de Dados", "Erro ao selecionar banco de dados", "#", 500);
}
# fim de conexão com banco de dados
###########################################


# Montar tela -  Quadro Principal

# Mostrar menu principal
novaTabela("$configAppName - $configAppVersion - $sessLogin[login]", "center", 760, 0, 2, 1, $corFundo, $corBorda, 0);
	htmlAbreLinha($corFundo);
		htmlAbreColuna(760, 'center', $corFundo, 0, 'normal');
		
			# verificar se usuario está conectado
			#if(!$sessLogin[login] || !$sessLogin[senha] || $modulo=='login' || $modulo=='logoff') {
			if($modulo=='login' || $modulo=='logoff' || ($sessLogin && !checaLogin($sessLogin, $modulo, $sub, $acao, $registro))) {
				# Usuário não está conectado
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
					
						## Verificar se acesso é com ou sem login
						if($sessLogin) {
							# Menu de visualização resumida - principal
							verMenu($modulo, $sub, $acao, $registro, $matriz);
						}
						else {
							verMenu($modulo, $sub, $acao, $registro, $matriz);
						}
					
						htmlFechaColuna();
					htmlFechaLinha();
				fechaTabela();
				
			} #fecha verificação de login
			
			htmlFechaColuna();
	htmlFechaLinha();
fechaTabela();
# Fecha separação

htmlFechaColuna();
htmlFechaLinha();
fechaTabela();
# Fecha menu principal


###########################################
# Rodapé
html_footer($configAppName." - ".$configAppVersion);
###########################################

?>
