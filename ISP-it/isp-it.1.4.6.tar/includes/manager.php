<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 10/12/2003
# Ultima altera��o: 07/01/2004
#    Altera��o No.: 005
#
# Fun��o:
#    Manager - Fun��es e Integra��o
# 


# fun��o para conex�o com banco de dados do servidor
function conectaManager() {
	global $manager;
	
	# conectar com banco de dados do grapi e listar cidades
	$connManager=conectaMySQL($manager[host], $manager[user], $manager[passwd]);
	
	if($connManager) {
		selecionaDB($manager[db], $connManager);
		return($connManager);
	}
		
} #fecha funcao de conex�o com servidor	



# Fun��o para comunica��o com Manager e ISP
function managerComando($matriz, $acao) {

	global $manager;

	$connManager=conectaManager();
	
	$data=dataSistema();
	
	if($connManager) {
		
		if($acao=='emailadicionar') {
			# Adicionar conta de email
			$comando="adduser $matriz[login]@$matriz[dominio] $matriz[senha_conta]";
			$descricao="ISP-IT: Criar conta $matriz[login]@$matriz[dominio] $matriz[senha_conta]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
			
		}
		elseif($acao=='emailremover') {
			# Remover conta de email
			$comando="deluser $matriz[login]@$matriz[dominio]";
			$descricao="ISP-IT: Excluir conta $matriz[login]@$matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailsenha') {
			# Altera��o de senha
			$comando="passwd $matriz[login]@$matriz[dominio] $matriz[senha_conta]";
			$descricao="ISP-IT: Trocar senha conta $matriz[login]@$matriz[dominio] $matriz[senha_conta]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailaliasadicionar') {
			# Alias - Adicionar
			$comando="alias modificar $matriz[dominio] $matriz[alias] $matriz[login]@$matriz[dominio]";
			$descricao="ISP-IT: Criar alias $matriz[dominio] $matriz[alias] $matriz[login]@$matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailaliasremover') {
			# Alias - Adicionar
			$comando="alias remover $matriz[dominio] $matriz[alias] $matriz[login]@$matriz[dominio]";
			$descricao="ISP-IT: Excluir alias $matriz[dominio] $matriz[alias] $matriz[login]@$matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailforwardadicionar') {
			# Alias - Adicionar
			$comando="forward modificar $matriz[login]@$matriz[dominio] $matriz[forward] $matriz[copia]";
			$descricao="ISP-IT: Adicionar forward $matriz[login]@$matriz[dominio] $matriz[forward] $matriz[copia]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailforwardremover') {
			# Alias - Adicionar
			$comando="forward remover $matriz[login]@$matriz[dominio] a N";
			$descricao="ISP-IT: Excluir forward $matriz[login]@$matriz[dominio] a N";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailquota') {
			# Alias - Adicionar
			$comando="setuserquota modificar $matriz[login]@$matriz[dominio] $matriz[quota]000000";
			$descricao="ISP-IT: Configurar quota $matriz[login]@$matriz[dominio] $matriz[quota]000000";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailautoreplyadicionar') {
			# Alias - Adicionar
			$comando="autoreply modificar $matriz[login]@$matriz[dominio] $matriz[texto]";
			$descricao="ISP-IT: Adicionar Auto-Resposta $matriz[login]@$matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailautoreplyremover') {
			# Alias - Adicionar
			$comando="autoreply remover $matriz[login]@$matriz[dominio] a";
			$descricao="ISP-IT: Excluir Auto-Resposta $matriz[login]@$matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailantivirusadicionar') {
			# Alias - Adicionar
			$comando="antivirus modificar $matriz[login]@$matriz[dominio]";
			$descricao="ISP-IT: Ativar AntiVirus $matriz[login]@$matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='emailantivirusremover') {
			# Alias - Adicionar
			$comando="antivirus remover $matriz[login]@$matriz[dominio]";
			$descricao="ISP-IT: Excluir AntiVirus $matriz[login]@$matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		elseif($acao=='configuracoes') {
			# Alias - Adicionar
			if($matriz[login]) {
				$comando="moduser $matriz[restricao] $matriz[login]@$matriz[dominio]";
				$descricao="ISP-IT: Restri��es $matriz[restricao] $matriz[login]@$matriz[dominio]";
			}
			elseif($matriz[dominio]) {
				$comando="moduser $matriz[restricao] $matriz[dominio]";
				$descricao="ISP-IT: Restri��es $matriz[restricao] $matriz[dominio]";
			}
			
			if($comando) {
				$sql="
					INSERT INTO
						manager_queue
					VALUES (
						0, 
						'$descricao', 
						'$data[dataBanco]',
						'',
						'',
						'$comando',
						'mail',
						'N',
						'',
						'',
						''
					)
				";
			}
		}
		if($acao=='dominioadicionar') {
			# Adicionar conta de email
			$comando="adddomain $matriz[dominio] $manager[senha_padrao] $manager[quota_padrao]";
			$descricao="ISP-IT: Criar dom�nio $matriz[dominio] $manager[senha_padrao] $manager[quota_padrao]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		if($acao=='dominioexcluir') {
			# Adicionar conta de email
			$comando="deldomain $matriz[dominio]";
			$descricao="ISP-IT: Excluir dom�nio $matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
		if($acao=='dominioinativar') {
			# Adicionar conta de email
			$comando="moduser pop3,relay,passwd,bounce,imap $matriz[dominio]";
			$descricao="ISP-IT: Inativar dom�nio $matriz[dominio]";
			$sql="
				INSERT INTO
					manager_queue
				VALUES (
					0, 
					'$descricao', 
					'$data[dataBanco]',
					'',
					'',
					'$comando',
					'mail',
					'N',
					'',
					'',
					''
				)
			";
		}
	}
	else {
		# Aviso de problema com integra��o - Manager
		# Mensagem de aviso
		echo "<br>";
		$msg="ATEN��O: Erro ao conectar ao sistema de Integra��o com Servidores!";
		avisoNOURL("Erro ao executar comando", $msg, '100%');
		
		return(0);
	}
	
	
	if($sql) {
	
		$consulta=consultaSQL($sql, $connManager);
		
		return($consulta);
	}

}



# fun��o para altera��o de senha no manager
function managerCriarConta($usuario, $dominio, $senha) {
	global $manager;
	
	$conn=conectaManager();
	
	$data=dataSistema();
	$descricao="ISP-IT: Criar Conta $usuario $dominio $senha";
	$comando="adduser $usuario@$dominio $senha";
	
	$sql="INSERT INTO $manager[queue] VALUES (0, '$descricao','$data[dataBanco]','','','$comando','mail','N','','','')";
	$consulta=consultaSQL($sql, $conn);
	
	return($consulta);
	
}



# fun��o para altera��o de senha no manager
function managerAlterarSenha($usuario, $dominio, $senha) {
	global $manager;
	
	$conn=conectaManager();
	
	$data=dataSistema();
	$descricao="ISP-IT: Alterar Senha $usuario $dominio $senha";
	$comando="passwd $usuario@$dominio $senha";
	
	$sql="INSERT INTO $manager[queue] VALUES (0, '$descricao','$data[dataBanco]','','','$comando','mail','N','','','')";
	$consulta=consultaSQL($sql, $conn);
	
	return($consulta);
	
}


# Fun��o para busca de usu�rios em banco de dados do vpopmail
function vpopmailBuscaUsuario($dominio, $texto, $campo, $tipo, $ordem) {

	global $modulo, $sub, $vpopmail;
	
	$connManager=conectaManager();
	
	# Verifica tipo de busca em tabela
	# Tabela unica (vpopmail)
	# Trocar informa��o do dominio para nome de tabela
	$tabela=vpopmailTabelaDominio($dominio);
	
	if($connManager) {
	
		if($tipo=='todos') {
			$sql="SELECT * FROM $vpopmail[db].$tabela ORDER BY $ordem";
		}
		elseif($tipo=='contem') {
			$sql="SELECT * from $vpopmail[db].$tabela WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
		}
		elseif($tipo=='igual') {
			$sql="SELECT * from $vpopmail[db].$tabela WHERE $campo='$texto' ORDER BY $ordem";
		}
		elseif($tipo=='custom') {
			$sql="SELECT * from $vpopmail[db].$tabela WHERE $texto ORDER BY $ordem";
		}
		
		# Verifica consulta
		if($sql){
			$consulta=consultaSQLHide($sql, $connManager);
			# Retornvar consulta
			return($consulta);
		}
		else {	
			# Mensagem de aviso
			$msg="Consulta n�o pode ser realizada por falta de par�metros";
			$url="?modulo=$modulo&sub=$sub";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}
} # fecha fun��o para busca de usu�rios do vpopmail


# Fun��o para buscar o ultimo acesso do usu�rio
function vpopmailUltimoAcesso($conta, $dominio)
{
	global $vpopmail, $conn;

	$sql="SELECT * FROM $vpopmail[db].lastauth WHERE domain='$dominio' AND user='$conta'";
	$query=consultaSQL($sql, $conn);
	
	if($query)
	{
		$matriz[ip]=resultadoSQL($query, 0, 'remote_ip');
		
		# Substituir imap por webmail
		if($matriz[ip]=="imap")
		{
			$matriz[ip]="TDKOM Webmail";
		}
		elseif($matriz[ip]=="0.0.0.0")
		{
			$matriz[ip]="IP N�o identificado";
		}

		$matriz[data]=date("d/m/Y",resultadoSQL($query, 0, 'timestamp'));
	}
	else
	{
		$matriz[ip]="sem informa��es";
		$matriz[data]="sem informa��es";
	}
	
	return($matriz);
	
} #fecha fun��o de busca de ultimo acesso


# Funcao para buscar tabela do dominio
function vpopmailTabelaDominio($dominio) {
	# Converter "." para "_"
	
	$tabela=str_replace('.','_',$dominio);
	$tabela=str_replace('-','_',$tabela);
	return($tabela); 
}



# Fun��o para ignorar forwards j� configurados
function vpopmailFormataForward($forward, $tipo)
{
	# Ler arquivo
	$forward=trim($forward);
	$forward=str_replace(' ','',$forward);
	$forward=str_replace('\n','',$forward);
	$forward=str_replace('\\','',$forward);
	$matOrigem=explode("\n", $forward);

	if($tipo=='texto') {
		for($a=0;$a<count($matOrigem);$a++) {
		
			if( strlen(trim($matOrigem[$a]))>0 && strstr($matOrigem[$a],'@') && strstr($matOrigem[$a],'.') ) {
				$retorno.=trim($matOrigem[$a]);
				if( ($a+1) < count($matOrigem)) $retorno.=',';
			}
		}
	}
	elseif($tipo=='form') {
		for($a=0;$a<count($matOrigem);$a++) {
		
			if(strlen(trim($matOrigem[$a]))>0 && strstr($matOrigem[$a],'@') && strstr($matOrigem[$a],'.') ) {
				$retorno.=trim($matOrigem[$a]);
				if( ($a+1) < count($matOrigem)) $retorno.="\n";
			}
		}
	}

	elseif($tipo=='matriz') {
		# Remover forwards antigos
		$matForward=array();
		$iNovo=0;
		
		for($i=0;$i<count($matOrigem);$i++) {
			if(strlen($matOrigem[$i])>0) {
				$retorno[$iNovo]=$matOrigem[$i];
				$iNovo++;
			}
		}
	}
	
	# Retornar matriz
	return($retorno);
} # fecha fun��o para ignorar forwards



function vpopmailFormatarConta($conta) {

	# Converter acentua��o para mai�scula
	$matMinuscula=array('�'
	,'�','�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�','�'
	,'�','�','�','�');
	
	$matMaiuscula=array('�'
	,'�','�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�','�'
	,'�','�','�','�');

	$matSimbolos=array(':',',',';','/',']','[','{','}','`','"','\'','\\','|','+','=',')','(','*','&','"','%','$','#','!');

	for($a=0;$a<count($matMinuscula);$a++ ) {
		$conta=str_replace($matMinuscula[$a],'',$conta);
		$conta=str_replace($matMaiuscula[$a],'',$conta);
	}
	
	for($a=0;$a<count($matSimbolos);$a++) {
		$conta=str_replace($matSimbolos[$a],'',$conta);
	}
	
	$conta=trim($conta);
	
	return($conta);
}


?>
