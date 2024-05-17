<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 09/03/2004
# Ultima altera��o: 20/03/2004
#    Altera��o No.: 004
#
# Fun��o:
#    Manuten��o de Contratos de Pessoas Tipos

# Adminitra��o Geral de Contratos
# Fun��o de configura��es
function contratosPessoasTipos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro;

	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=home";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
			
		# Quebrar registro
		$tmpValor=explode(":",$registro);
		
		$registro=$tmpValor[0];
		$matriz[id]=$tmpValor[1];
		$matriz[idServicosContratos]=$tmpValor[2];
		
		$sessCadastro[idPessoaTipo]=$registro;
		
		verPessoas('cadastros', 'clientes', 'ver', $registro, $matriz);
		echo "<br>";
		
		if(!$acao || $acao=='listar' || $acao=='listartodos' || $acao=='listarcancelados') {
			# listar Servi�os e contratos
			listarContratosPessoasTipos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='contratos') {
			# Listar Contratos do ServicoPlano
			listarContratosServicosPlanosServico($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='gerar') {
			# Visualizar documento em HTML
			gerarContratoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='cancelar') {
			# Cancelamento de contratos
			cancelarContratoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='renovar') {
			# Cancelamento de contratos
			renovarContratoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		}

	}
}



# Contratos Pessoas Tipos
function listarContratosPessoasTipos($modulo, $sub, $acao, $registro, $matriz) {

	# Listar Contratos Gerados
	listarGeradosContratosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
	echo "<br>";

	# Listar Contratos dispon�veis
	listarContratosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
}

?>
