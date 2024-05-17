<?
################################################################################
# Fun��o:
#    P�gina principal (index) da aplica��o

# Carregar Sessoes para informa��es de usuario
session_start();
# sessMsg -> ir acumulando as msg de OK!
session_register( "sessNavegacao", "sessPath", "sessBanco", "sessMsg", "sessHost" );

# Verifica se sess�o deve ser matada
if($modulo=='fullInstall' && session_is_registered( "sessBanco" )) {
	session_destroy();
	$sessBanco[dbhost]='';
	$sessBanco[dbuser]='';
	$sessBanco[dbpassword]='';
	$sessBanco[dbdatabase]='';
	$sessBanco[dbrootpassword]='';
}

# Carregar um DIRET�RIO ACIMA
# Carregar configura��es
include('../config/config.php');
include('../config/db.php');
# j� foi criado o custom.php
if( $sessBanco[customphp] == 1 || $modulo== 'instalarDB' ){
	include('../config/custom.php');
}
//include('../config/db.php');
include('../config/html.php');

# Vari�veis globais
//global $configHostMySQL, $configUserMySQL, $configPasswdMySQL, $configDBMySQL, $configAppName, $configAppVersion, $corFundo, $corBorda;

# Carregar fun��es
include('../includes/db.php');
include('../includes/html.php');

# Fun��es de Usuario
include('../includes/usuarios.php');
# Fun��es de Grupos
include('../includes/grupos.php');
# Fun��es de UsuariosGrupos
include( '../includes/usuarios_grupos.php' );

# Carregar o conte�do necess�rio para o INSTALADOR

# Tela
include('screen.php');
# Arquivos/diret�rios
include('files.php');
# Banco
include('database.php');

###########################################
# Conectar com banco de dados
if( $sessBanco[customphp] == 1 ){
	$conn=conectaMySQL($configHostMySQL, $configUserMySQL, $configPasswdMySQL);
	$db = selecionaDB( $configDBMySQL, $conn );
	if(!$conn)
	{
        aviso(_("Connection with MySQL"), _("Error in the connection with MySQL Database"), "#", 500);
	}
	if(!$db)
	{
        aviso(_("Database"), _("Error when selecting database"), "#", 500);
	}
}
###########################################

###########################################
# Mostrar cabe�alhos
html_header($configAppName, $configAppVersion);
###########################################

### Javascript ###
include('../includes/javascript.php');
### Fim do Javascript ###

	# Montar tela -  Quadro Principal
	# Mostrar menu principal
	
	novaTabela("$configAppName - $configAppVersion", "center", 760, 0, 2, 1, $corFundo, $corBorda, 0);

	htmlAbreLinha($corFundo);
	htmlAbreColuna(760, 'center', $corFundo, 0, 'normal');
	
		###########################################
		# Verificar a diretiva do php.ini
		if ( phpIni( $modulo ) == 1 ){
			# Verificar o Charset do Apache
//			if ( phpApache() == 1 ){
				menuInstaller($modulo, $matriz);
//			}
		}
		###########################################
	
	htmlFechaColuna();
	htmlFechaLinha();
	fechaTabela();
	# Fecha menu principal

###########################################
# Rodap�
html_footer($configAppName." - ".$configAppVersion);
###########################################

if($modulo) 				$sessNavegacao[modulo]=	$modulo;
if($matriz[path])			$sessPath[path]=		$matriz[path];
if($matriz[dbhost])			$sessBanco[dbhost]=		$matriz[dbhost];
if($matriz[dbdatabase])		$sessBanco[dbdatabase]=	$matriz[dbdatabase];
if($matriz[dbuser])			$sessBanco[dbuser]=		$matriz[dbuser];
if($matriz[dbpassword])		$sessBanco[dbpassword]=	$matriz[dbpassword];
if($matriz[dbrootpassword])	$sessBanco[dbrootpassword]=	$matriz[dbrootpassword];
if($modulo == 'instalarDB' )$sessBanco[customphp]=	1;
if( empty( $sessHost ) )	$sessHost[uname]=		shell_exec("uname -n");
//echo "Matriz:<br>";
//echo "<br>";
//print_r($matriz);
//echo "<br><br><br>";
//echo "Banco:<br>";
//print_r($sessBanco);
?>
