<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 09/03/2004
# Ultima alteração: 20/03/2004
#    Alteração No.: 004
#
# Função:
#    Manutenção de Contratos de Pessoas Tipos

# Adminitração Geral de Contratos
# Função de configurações
function contratosPessoasTipos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
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
			# listar Serviços e contratos
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

	# Listar Contratos disponíveis
	listarContratosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
}

?>
