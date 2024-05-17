<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 27/02/2003
# Ultima alteração: 10/12/2003
#    Alteração No.: 004
#
# Função:
#    Configurações utilizadas pela aplicação - Integração com Servidor de Mail


# função para conexão com banco de dados do servidor
function conectaMailServer() {
	global $mailserver;
	
	# conectar com banco de dados do grapi e listar cidades
	$conn=conectaMySQL($mailserver[host], $mailserver[user], $mailserver[passwd]);
	
	if($conn) return($conn);
		
} #fecha funcao de conexão com servidor





# Função para busca de emails
function mailBuscaConta($conta, $dominio) {
	global $mailserver;

	$conn=conectaMailserver();
	
	if(!$conn) {
		# Aviso de erro de conexão
		$msg="Sistema de cadastramento não está disponivel para uso neste momento";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indisponívei no momento", $msg, $url, '100%');
	}
	else {

		$dominioTAB=str_replace('.','_',$dominio);
		
		$sql="SELECT pw_name FROM $mailserver[db].$dominioTAB where pw_name='$conta'";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) return(resultadoSQL($consulta, 0, 'pw_name'));	
	}
} #fecha  busca 



# Função para validação de contas de e-mail
function mailValidaConta($conta) {

	if(strstr($conta, "@")) {
		$tmpConta=explode("@",$conta);
		$conta=$tmpConta[0];
	}

	$conta=strtolower($conta);

	$matCaracInvalido=array(' ','!','@','#','$','%','¨','"','&','*','\(','\)','+','='
	,'^','ç','~','\]','\[','\{','\}','/','?',':',';',',','<','>','\`','\'','\'','\\','|','\"'
	,'â','ã','à','á','ä'
	,'é','è','ê','ë'
	,'í','ì','î','ï'
	,'ó','ò','ô','õ','ö'
	,'ú','ù','û','ü'
	,'Â','Ã','Á','À','Ä'
	,'É','È','Ê','Ë'
	,'Í','Ì','Î','Ï'
	,'Ó','Ò','Õ','Ô','Ö'
	,'Ú','Ù','Û','Ü'	
	,'tdkom','teste','test','root','postmaster','qmvc','qmail','vpopmail','admin','administrator','administrador'
	,'sys','proc','mailer-daemon','undisclosed','microsoft','linux','anti','virus','novo','new'
	,'tedeka','abuse','security','secure','gerencia','contato','vendas','comercial','dominio','dominios'
	,'sysop','warning','aviso','alerta','alert','intranet','extranet','clientes','web','mail','server','conectiva'
	,'redhat','suse','frebsd','bsd','open','close','mysql','php','apache','procmail','sendmail','exim','javascript'
	,'rpm','firewall','network','uol','terra','usuario');
	
	
	# Deixar apenas letras, numeros e símbolos ("." ou "_" ou "-")
	$conta=ereg_replace("[^_\.a-zA-Z0-9\-]+", "", $conta);
	
	# Remover caracteres inválidos (começo e final da string)
	$conta=ereg_replace("(\.|_|\-)+$", "", $conta);
	$conta=ereg_replace("^(\.|_|\-)+", "", $conta);
	
	
	# Verificação final
	for($i=0;$i<count($matCaracInvalido);$i++) {
		$conta=str_replace($matCaracInvalido[$i],'',$conta);
	}
	

	# Verifica a string - eliminar caracteres invalidos
	$matRejeitar=array('.','_','-');
	$retorno='';
	$flagRejeitar=0;
	for($i=0;$i<strlen($conta);$i++) {
		# Zerar Flags
		$flagEncontrado=0;
		
		# Verificar String
		for($x=0;$x<count($matRejeitar);$x++) {
			if(substr($conta, $i, 1)==$matRejeitar[$x]) {
				$flagEncontrado++;
				$flagRejeitar++;
			}
		}
		
		if($flagEncontrado==0) $flagRejeitar=0;
		
		# Verificar se deve ou nao abribuir o caracter para string final
		if($flagEncontrado<=1 && $flagRejeitar<=1) $retorno.=substr($conta, $i, 1);		
	}
	
	return($retorno);

	
} # fecha validação de conta de mail





# Função para Criação de Conta
function mailCriaConta($conta, $dominio, $senha) {
	global $manager;

	$conn=conectaManager();
	
	if(!$conn) {
		# Aviso de erro de conexão
		$msg="Sistema de cadastramento não está disponivel para uso neste momento";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indisponívei no momento", $msg, $url, '100%');
	}
	else {
		$descricao="Cadastro DIAL: Incluir conta $conta@$dominio $senha";
		$comando="adduser $conta@$dominio $senha";
		
		$data=dataSistema();

		$sql="INSERT INTO $manager[db].$manager[tabelafila] VALUES (0, '$descricao','$data[dataBanco]','','','$comando','mail','N','','','')";
		$consulta=consultaSQL($sql, $conn);
				
	}
} #fecha criação





# funcao para envio de email
function mailEnviar($origem, $destino, $assunto, $texto) {

	global $configMail;

	if(!$origem && !$destino) {
		$headerADD="From: ".$configMail[from]."\nReply-To: ".$configMail[to]."\nX-Mailer: PHP-Mail (by Kerne.org)";
	}
	elseif(!$origem) {
		$headerADD="From: ".$configMail[from]."\nReply-To: ".$destino."\nX-Mailer: PHP-Mail (by Kerne.org)";
	}
	
	mail($configMail[to], $assunto, $texto, $headerADD);
} # fecha função de envio de mail





# Função para validação de conta de mail
function mailValidaMail($email) {
} #fecha validação de conta de mail




?>
