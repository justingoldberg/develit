#!/usr/local/bin/php -q
<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 26/02/2004
# Ultima altera��o: 26/02/2004
#    Altera��o No.: 001
#
# Fun��o:
#    Autenticador auxiliar do ISP-IT  para Radius

##
# Observa��es
# Configurar no grupo do radius, a op��o 
# Exec-Program-Wait = /devel-it/isp-it/sbin/ispradius.php %i
##

# Carregar configura��es
include('../config/config.php');
include('../config/custom.php');
include('../config/db.php');
include('../config/radius.php');

# Carregar fun��es
include('../includes/db.php');
include('../includes/data.php');

# Valida��o de Formul�rios
include('../includes/valida_form.php');
include('../includes/valida_documentos.php');

# Fun��es de E-mail
include('../includes/mail.php');
include('../includes/administracao_radius.php');

# Radius
include('../includes/radius.php');
include('../includes/radius_grupos.php');
include('../includes/radius_usuarios.php');
include('../includes/radius_usuarios_telefones.php');

# Radius - Usu�rios Pessoas
include('../includes/radius_usuarios_pessoas.php');

# Servicos por Grupo - Radius
include('../includes/radius_servicos.php');

$opcoes=$argv[1];
$usuario=$argv[2];
$telefone=$argv[3];

if($opcoes && $opcoes == '--help') {
	echo "$configAppName - $configAppVersion\n";
	echo "Autentica��o Radius - Secund�ria: Falta de parametros!\n";
	echo "Sintaxe: $argv[0] <opcoes> <usuario> <telefone>\n\n";
	echo "Op��es:\n";
	echo "\tf - Necess�rio que usuario esteja cadastrado no $configAppName\n";
	echo "\tx - Necess�rio que a lista de telefones esteja cadastrada no $configAppName\n";
	echo "\tt - Validar telefone (Telefone n�o encontrado, disconecta usu�rio)\n";
	echo "\tT - Validar telefone (Telefone n�o encontrado, n�o disconecta usu�rio)\n";
	echo "\tD - Remover caracteres de DDD do inicio do telefone\n";
	echo "\td - Debug Mode\n";
	echo "\n\tUso: $argv[0] fT usuario 33242324\n";
	echo "\n";
	exit(0);
}
elseif(!$opcoes || !$usuario || !$telefone) {
	echo "$configAppName - $configAppVersion\n";
	echo "Autentica��o Radius - Secund�ria: Falta de parametros!\n";
	echo "Sintaxe: $argv[0] <opcoes> <usuario> <telefone>\n\n";
	exit(1);
}
else {

	# Verificar parametros
	for($a=0;$a<strlen($opcoes);$a++) {
		$tmpOpcao=substr($opcoes, $a, 1);
		$matOpcoes[$tmpOpcao]=$tmpOpcao;
	}


	$conn=conectaMySQL($configHostMySQL, $configUserMySQL, $configPasswdMySQL);
	if($conn) {
		selecionaDB($configDBMySQL, $conn);
	}
	else {
		if($matOpcoes[d]) {
			echo "$configAppName - DEBUG MODE\n";
			echo "Falha na conex�o com o banco de dados\n";
			exit(1);
		}
	}

	if($matOpcoes[d]) {
		echo "$configAppName - DEBUG MODE\n";
		echo "Parametros: $opcoes $usuario $telefone\n";
	}
	
	# formatar usuario
	#TODO
	
	# remover formato do telefone
	$telefone=formatarFoneNumeros($telefone);
	

	# Verificar se usu�rio deve existir no ISP
	$consultaUsuario=radiusBuscaUsuarios($usuario, 'login','igual','id');
	
	# Verificar op��o de usuario existente/inexistente
	if($matOpcoes[f]) $flagConnect=2;
	
	if($consultaUsuario && contaConsulta($consultaUsuario)>0) {
		# Verificar se existem telefones cadastrados
		if($matOpcoes[d]) echo "Usu�rio [$usuario] encontrado\n";
		
		$consultaTelefones=carregarRadiusTelefones($usuario);
		
		if(is_array($consultaTelefones)) {
			if($matOpcoes[d]) echo "Telefones encontrados: \n";
			
			if($matOpcoes[t]) $flagConnect=2;
			elseif($matOpcoes[T]) $flagConnect=1;
			
			# Validar telefones
			for($b=0;$b<count($consultaTelefones);$b++) {
				$tmpTelefone = $consultaTelefones[$b];
				
				if($matOpcoes[D] && strlen($telefone) > strlen($tmpTelefone)) {
					if($matOpcoes[D] && $matOpcoes[d]) echo "Removendo DDD: " . substr($telefone, 0, 2) . "\n";
					$telefone=substr($telefone, 2,strlen($telefone) );
				}
				
				# verificar caracteres da direita
				if( strlen($tmpTelefone) >= strlen($telefone) ) {
					if($matOpcoes[d]) echo "Verificando Telefone [$tmpTelefone] com [$telefone]";
					
					$right=(strlen($tmpTelefone) - strlen($telefone)  );
					$right=substr($tmpTelefone, $right, strlen($tmpTelefone));
					
					if($matOpcoes[d]) echo " = [$right]";
					
					if($telefone == $right )  {
						if($matOpcoes[d]) echo " encontrado!\n";
						$flagConnect=1;
						break;
					}
					else {
						if($matOpcoes[d]) echo " N�O ENCONTRADO!\n";
					}
				}
				else {
					if($matOpcoes[d]) {
						echo "Tamanho do telefone informado � superior ao do banco de dados\n";
					}
				}
			}
		}
		else {
			# N�o h� telefones cadastrados para a conta
			if($matOpcoes[d]) echo "N�o h� telefones cadastrados!\n";
			
			if($matOpcoes[x]) $flagConnect=2;
			else $flagConnect=1;
		}
	}
	else {
		if($matOpcoes[d]) echo "Usu�rio [$usuario] N�O ENCONTRADO\n";
	}
	
	if($flagConnect==1) exit(0);
	elseif($flagConnect==2) exit(1);
	else exit(0);
}
?>
