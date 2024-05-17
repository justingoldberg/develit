<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 23/09/2003
# Ultima alteração: 05/02/2004
#    Alteração No.: 008
#
# Função:
#    Painel - Funções para configurações

# Selecionador de modulo
function moduloListarConfiguracao($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	
	$buscaModulo=dadosModulo($idModulo);
	
	if($buscaModulo[modulo]=='dial') {
		# Listar contas de dial-up do cliente
		radiusListarUsuariosPessoasTipos($idPessoaTipo, $idModulo);
	}
	elseif($buscaModulo[modulo]=='mail') {
		# Listar contas de email do cliente
		administracaoMail($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif($buscaModulo[modulo]=='suporte') {
		# Funcoes de administraco de suporte
		administracaoSuporte($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif($buscaModulo[modulo]=='maquinas') {
		# Funcoes de administraco de suporte
		administracaoMaquinas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif($buscaModulo[modulo]=='dominio') {
		# Listar contas de email do cliente
		if($acao=='config') listarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='adicionar') adicionarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='excluir') excluirDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='alterar') alterarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='inativar') inativarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='ativar') ativarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='parametros') listarDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='parametrosadicionar') adicionarDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='parametrosalterar') alterarDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='parametrosexcluir') excluirDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='importar') importarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='transferir') transferirDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='sincronizar') sincronizarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
	}
	if ($sub=='ivr') {
		exibeServicoIVR($modulo, $sub, $acao, $registro, $matriz, $_REQUEST["subacao"]);
	}
}

function exibeServicoIVR($modulo, $sub, $acao, $registro, $matriz, $subacao) {
	global $conn, $tb, $corFundo, $corBorda, $html;
	# localiza id do servico ivr do cliente
	$sql="
		SELECT 
			$tb[ServicosPlanos].id idServicoPlano,
			$tb[Servicos].id idServico 
		FROM 
			$tb[Modulos], 
			$tb[Parametros], 
			$tb[ParametrosModulos], 
			$tb[ServicosParametros], 
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[PessoasTipos], 
			$tb[Unidades], 
			$tb[Pessoas] 
		WHERE 
			$tb[Modulos].id=$tb[ParametrosModulos].idModulo 
			AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id 
			AND $tb[Parametros].idUnidade = $tb[Unidades].id 
			AND $tb[Parametros].id = $tb[ServicosParametros].idParametro 
			AND $tb[ServicosParametros].idServico = $tb[Servicos].id 
			AND $tb[Servicos].id = $tb[ServicosPlanos].idServico 
			AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
			AND $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[PessoasTipos].id = $registro
			AND $tb[PlanosPessoas].status='A'
			AND $tb[Modulos].modulo='ivr' 
		ORDER BY 
			$tb[ServicosPlanos].id
	";
	$subacao=$_REQUEST["subacao"];
	
	#verifica quais os servicos existentes pra habilitar ou nao a inclusao
	$consulta=consultaSQL($sql, $conn);
	if ($consulta && contaConsulta($consulta)>0) {
		# Busca todos os servicos planos e confere os q ja foram habilitados
		# Os q nao foram configurados joga num array dentro da matriz ServicosPlanos
		for ($a=0; $a<contaConsulta($consulta); $a++) {
			$idServicoPlano=resultadoSQL($consulta, $a, 'idServicoPlano');
			if ($idServicoPlano) {
				#listagem de todos os servicos do cliente
				$matriz[todosServicosPlanos][]=$idServicoPlano;
				#verifica o q nao esta configurado e poe na lista
				$idServicoIVR=getIDServicoIVR($idServicoPlano);
				if(! $idServicoIVR) $matriz[ServicosPlanos][]=$idServicoPlano;
			}
		}
	}
	
	# se nao há mais servicos disponiveis nao adiciona
	if (count($matriz[ServicosPlanos])<1) $matriz[naoInclui]=1;
	
	#se veio um servico plano busca o id do servico ivr 
	if ($_REQUEST["idServicoPlano"] || $matriz[idServicoPlano]) {
		#se nao veio no array veio no header (pela lista)
		if (! $matriz[idServicoPlano])
			$matriz[idServicoPlano]=$_REQUEST["idServicoPlano"];
			
		$matriz[idServicoIVR]=getIDServicoIVR($matriz[idServicoPlano]);
		
	}
		
	
	switch ($subacao) {
		case "adicionar":			
			servicosIVRAdicionar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case "alterar":
			servicosIVRAlterar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case 'listar':
			servicosIVRListar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case 'ver':
			servicosIVRVer($modulo, $sub, $acao, $registro, $matriz);
			break;
		case 'congelar':
			servicosIVRCongelar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case 'descongelar':
			servicosIVRDescongelar($modulo, $sub, $acao, $registro, $matriz);
			break;
		// Implementação do módulo de exclusão - por Felipe Assis - 24/03/2008
		case 'excluir':
			if(!$matriz['btExcluir']){
				formConfirmExcServicoIVR($modulo, $sub, $acao, $registro, $matriz);
			}
			else{
				servicosIVRExcluir($modulo, $sub, $acao, $registro, $matriz);
			}
			break;
		default:
			servicosIVRListar($modulo, $sub, $acao, $registro, $matriz);
	}
}


/*	

SELECT * FROM Modulos, Parametros,ParametrosModulos,ServicosParametros,Servicos,ServicosPlanos,PlanosPessoas,PessoasTipos,Unidades,Pessoas WHERE Modulos.id=ParametrosModulos.idModulo AND ParametrosModulos.idParametro = Parametros.id AND Parametros.idUnidade = Unidades.id AND Parametros.id = ServicosParametros.idParametro AND ServicosParametros.idServico = Servicos.id AND Servicos.id = ServicosPlanos.idServico AND ServicosPlanos.idPlano = PlanosPessoas.id AND PlanosPessoas.idPessoaTipo = PessoasTipos.id AND PessoasTipos.idPessoa = Pessoas.id AND PessoasTipos.id = 2103 AND PlanosPessoas.status='A' AND Modulos.modulo='ivr' ORDER BY ServicosPlanos.id






switch ($subacao) {
		case 'adicionar':
			servicosIVRAdicionar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case 'alterar':
			servicosIVRAlterar($modulo, $sub, $acao, $registro, $matriz, $idServicoPlano);
			break;
		case 'ver':
			servicosIVRAlterar($modulo, $sub, $acao, $registro, $matriz, $idServicoPlano);
			break;
		default:
			servicosIVR($modulo, $sub, $acao, $registro, $matriz);
			*/

?>