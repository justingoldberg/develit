<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/12/2003
# Ultima alteração: 07/01/2004
#    Alteração No.: 005
#
# Função:
#    Manager - Funções e Integração
# 


# função para conexão com banco de dados do servidor
function conectaManager() {
	global $manager;
	
	# conectar com banco de dados do grapi e listar cidades
	$connManager=conectaMySQL($manager[host], $manager[user], $manager[passwd]);
	
	if($connManager) {
		selecionaDB($manager[db], $connManager);
		return($connManager);
	}
		
} #fecha funcao de conexão com servidor	



# Função para comunicação com Manager e ISP
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
			# Alteração de senha
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
				$descricao="ISP-IT: Restrições $matriz[restricao] $matriz[login]@$matriz[dominio]";
			}
			elseif($matriz[dominio]) {
				$comando="moduser $matriz[restricao] $matriz[dominio]";
				$descricao="ISP-IT: Restrições $matriz[restricao] $matriz[dominio]";
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
			$descricao="ISP-IT: Criar domínio $matriz[dominio] $manager[senha_padrao] $manager[quota_padrao]";
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
			$descricao="ISP-IT: Excluir domínio $matriz[dominio]";
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
			$descricao="ISP-IT: Inativar domínio $matriz[dominio]";
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
		# Aviso de problema com integração - Manager
		# Mensagem de aviso
		echo "<br>";
		$msg="ATENÇÃO: Erro ao conectar ao sistema de Integração com Servidores!";
		avisoNOURL("Erro ao executar comando", $msg, '100%');
		
		return(0);
	}
	
	
	if($sql) {
	
		$consulta=consultaSQL($sql, $connManager);
		
		return($consulta);
	}

}



# função para alteração de senha no manager
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



# função para alteração de senha no manager
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


# Função para busca de usuários em banco de dados do vpopmail
function vpopmailBuscaUsuario($dominio, $texto, $campo, $tipo, $ordem) {

	global $modulo, $sub, $vpopmail;
	
	$connManager=conectaManager();
	
	# Verifica tipo de busca em tabela
	# Tabela unica (vpopmail)
	# Trocar informação do dominio para nome de tabela
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
			$msg="Consulta não pode ser realizada por falta de parâmetros";
			$url="?modulo=$modulo&sub=$sub";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
} # fecha função para busca de usuários do vpopmail


# Função para buscar o ultimo acesso do usuário
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
			$matriz[ip]="IP Não identificado";
		}

		$matriz[data]=date("d/m/Y",resultadoSQL($query, 0, 'timestamp'));
	}
	else
	{
		$matriz[ip]="sem informações";
		$matriz[data]="sem informações";
	}
	
	return($matriz);
	
} #fecha função de busca de ultimo acesso


# Funcao para buscar tabela do dominio
function vpopmailTabelaDominio($dominio) {
	# Converter "." para "_"
	
	$tabela=str_replace('.','_',$dominio);
	$tabela=str_replace('-','_',$tabela);
	return($tabela); 
}



# Função para ignorar forwards já configurados
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
} # fecha função para ignorar forwards



function vpopmailFormatarConta($conta) {

	# Converter acentuação para maiúscula
	$matMinuscula=array('ç'
	,'â','ã','à','á','ä'
	,'é','è','ê','ë'
	,'í','ì','î','ï'
	,'ó','ò','ô','õ','ö'
	,'ú','ù','û','ü');
	
	$matMaiuscula=array('Ç'
	,'Â','Ã','Á','À','Ä'
	,'É','È','Ê','Ë'
	,'Í','Ì','Î','Ï'
	,'Ó','Ò','Õ','Ô','Ö'
	,'Ú','Ù','Û','Ü');

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
