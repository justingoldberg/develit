<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 12/03/2003
# Ultima altera��o: 03/10/2003
#    Altera��o No.: 010
#
# Fun��o:
#    Fun��es de autentica��o e valida��o de usuario


# Formul�rio de valida��o
function passphrase($modulo, $sub, $acao, $registro, $sessao) {
	# Carregar vari�veis de autentica��o
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessPassPhrase;
	
	$data=dataSistema();
	$parametros=carregaParametros();
	
	# Formulario de login
	if(!$sessao[passphrase]) {
		# Formul�rio de login
		
		echo "<br>";
		# Motrar tabela de busca
		novaTabela2("[Frase Secreta - Acesso � Listagem de Usu�rios]", "center", '500', 0, 2, 1, $corFundo, $corBorda, 0);
			# Opcoes Adicionais
			//menuOpcAdicional($modulo, $sub, $acao, $registro);				
			
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
				$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="&nbsp;";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<b class=bold10>Frase secreta:</b>";
				$texto.="&nbsp;<input type=password name=matValidaPassPhrase[passphrase] size=40>";
				$texto.="&nbsp;<input type=submit name=matValidaPassPhrase[bntOK] value=OK class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="&nbsp;";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha formulario
	# Conferir usuario
	elseif($sessao[passphrase]) {
		# Conferir passphrase
		if($sessao[passphrase] == $parametros[passphrase]) {
		
			if(!$sessao[passphrase_timeout]) {
				$data=dataSistema();
				$sessPassPhrase[passphrase]=$sessao[passphrase];
				$sessPassPhrase[passphrase_timeout]=$data[timestamp];
				return(1);
			}
			else {
				if($sessao[passphrase_timeout]+$parametros[passphrase_timeout] < $data[timestamp]) {
					$data=dataSistema();
					$sessPassPhrase[passphrase_timeout]='';
					$sessPassPhrase[passphrase]='';
					
					passphrase($modulo, $sub, $acao, $registro, '');
					return(0);
				}
				else {
					$sessPassPhrase[passphrase_timeout]=$data[timestamp];
					return(1);
				}
			}
		}
		else {
			$sessPassPhrase[passphrase_timeout]='';
			$sessPassPhrase[passphrase]='';
			
			passphrase($modulo, $sub, $acao, $registro, '');
			return(0);
		}
	}
	else {
		return(0);
	}
} #fecha funcao de valida��o

?>
