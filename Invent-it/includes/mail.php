<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 27/02/2003
# Ultima altera��o: 10/12/2003
#    Altera��o No.: 004
#
# Fun��o:
#    Configura��es utilizadas pela aplica��o - Integra��o com Servidor de Mail


# fun��o para conex�o com banco de dados do servidor
function conectaMailServer() {
	global $mailserver;
	
	# conectar com banco de dados do grapi e listar cidades
	$conn=conectaMySQL($mailserver[host], $mailserver[user], $mailserver[passwd]);
	
	if($conn) return($conn);
		
} #fecha funcao de conex�o com servidor





# Fun��o para busca de emails
function mailBuscaConta($conta, $dominio) {
	global $mailserver;

	$conn=conectaMailserver();
	
	if(!$conn) {
		# Aviso de erro de conex�o
		$msg="Sistema de cadastramento n�o est� disponivel para uso neste momento";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indispon�vei no momento", $msg, $url, '100%');
	}
	else {

		$dominioTAB=str_replace('.','_',$dominio);
		
		$sql="SELECT pw_name FROM $mailserver[db].$dominioTAB where pw_name='$conta'";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) return(resultadoSQL($consulta, 0, 'pw_name'));	
	}
} #fecha  busca 



# Fun��o para valida��o de contas de e-mail
function mailValidaConta($conta) {

	if(strstr($conta, "@")) {
		$tmpConta=explode("@",$conta);
		$conta=$tmpConta[0];
	}

	$conta=strtolower($conta);

	$matCaracInvalido=array(' ','!','@','#','$','%','�','"','&','*','\(','\)','+','='
	,'^','�','~','\]','\[','\{','\}','/','?',':',';',',','<','>','\`','\'','\'','\\','|','\"'
	,'�','�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�','�'
	,'�','�','�','�'	
	,'tdkom','teste','test','root','postmaster','qmvc','qmail','vpopmail','admin','administrator','administrador'
	,'sys','proc','mailer-daemon','undisclosed','microsoft','linux','anti','virus','novo','new'
	,'tedeka','abuse','security','secure','gerencia','contato','vendas','comercial','dominio','dominios'
	,'sysop','warning','aviso','alerta','alert','intranet','extranet','clientes','web','mail','server','conectiva'
	,'redhat','suse','frebsd','bsd','open','close','mysql','php','apache','procmail','sendmail','exim','javascript'
	,'rpm','firewall','network','uol','terra','usuario');
	
	
	# Deixar apenas letras, numeros e s�mbolos ("." ou "_" ou "-")
	$conta=ereg_replace("[^_\.a-zA-Z0-9\-]+", "", $conta);
	
	# Remover caracteres inv�lidos (come�o e final da string)
	$conta=ereg_replace("(\.|_|\-)+$", "", $conta);
	$conta=ereg_replace("^(\.|_|\-)+", "", $conta);
	
	
	# Verifica��o final
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

	
} # fecha valida��o de conta de mail





# Fun��o para Cria��o de Conta
function mailCriaConta($conta, $dominio, $senha) {
	global $manager;

	$conn=conectaManager();
	
	if(!$conn) {
		# Aviso de erro de conex�o
		$msg="Sistema de cadastramento n�o est� disponivel para uso neste momento";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Sistema indispon�vei no momento", $msg, $url, '100%');
	}
	else {
		$descricao="Cadastro DIAL: Incluir conta $conta@$dominio $senha";
		$comando="adduser $conta@$dominio $senha";
		
		$data=dataSistema();

		$sql="INSERT INTO $manager[db].$manager[tabelafila] VALUES (0, '$descricao','$data[dataBanco]','','','$comando','mail','N','','','')";
		$consulta=consultaSQL($sql, $conn);
				
	}
} #fecha cria��o





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
} # fecha fun��o de envio de mail





# Fun��o para valida��o de conta de mail
function mailValidaMail($email) {
} #fecha valida��o de conta de mail




?>
