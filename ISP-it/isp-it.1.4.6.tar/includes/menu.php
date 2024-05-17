<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 20/05/2003
# Ultima alteração: 14/04/2004
#    Alteração No.: 049
#
# Função:
#    Menus da aplicação


# Função para verificação de menus da aplicação
function menuPrincipal($tipo) {

	global $corFundo, $corBorda, $html;
	
	# Receber ID do Grupo do usuario
	if($grupoUsuario && contaConsulta($grupoUsuario)>0) {
		$idGrupo=resultadoSQL($grupoUsuario, 0, 'idGrupo');	
		
		# Buscar informações do grupo
		# receber informações do grupo
		$infoGrupo=buscaInfoGrupo($idGrupo);
	}
	
	if($tipo=='usuario') {
		htmlAbreTabelaSH("center", 760, 0, 1, 0, $corFundo, $corBorda, 7);
			htmlAbreLinha($corFundo);
				itemLinha('<img src="'.$html[imagem][home].'" border="0" />PRINCIPAL', "?modulo=home", 'center', $corFundo, 0, 'titulo8');
				itemLinha('<img src="'.$html[imagem][cadastros].'" border="0" />CADASTROS', "?modulo=cadastros", 'center', $corFundo, 0, 'titulo8');
				itemLinha('<img src="'.$html[imagem][menulancamento].'" border="0" />LANÇAMENTOS', "?modulo=lancamentos", 'center', $corFundo, 0, 'titulo8');
				itemLinha('<img src="'.$html[imagem][financeiro].'" border="0" />FATURAMENTO', "?modulo=faturamento", 'center', $corFundo, 0, 'titulo8');
				itemLinha('<img src="'.$html[imagem][consultas].'" border="0" />CONSULTAS', "?modulo=consultas", 'center', $corFundo, 0, 'titulo8');
				itemLinha('<img src="'.$html[imagem][relatorio].'" border="0" />RELATORIOS', "?modulo=relatorios", 'center', $corFundo, 0, 'titulo8');
				itemLinha('<img src="'.$html[imagem][config_sistema].'" border="0" />CONFIGURAÇÕES', "?modulo=configuracoes", 'center', $corFundo, 0, 'titulo8');
				itemLinha('<img src="'.$html[imagem][desativar].'" border="0" />SAIR', "?modulo=logoff", 'center', $corFundo, 0, 'titulo8');
			htmlFechaLinha();
		fechaTabela();
	}


} # fecha visualizacao de menu



# Função para verificação de menus da aplicação
function verMenu($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	### Menu principal - usuarios logados apenas
	if($modulo=='login' || !$sessLogin) {
		validacao($sessLogin, $modulo, $sub, $acao, $registro);
	}

	
	### MODULOS QUE REQUEREM AUTENTICAÇÃO
	else {
		if(checaLogin($sessLogin, $modulo, $sub, $acao, $registro) ) {
			## PRINCIPAL / HOME
			if(!$modulo || $modulo=='home') {
				home($modulo, $sub, $acao, $registro, $matriz);
			}
			## ACESSO
			elseif($modulo=='acesso') {
				acesso($modulo, $sub, $acao, $registro, $matriz);
			}
			## CONFIGURACOES
			elseif($modulo=='configuracoes') {
				config($modulo, $sub, $acao, $registro, $matriz);
			}
			## ADMINISTRAÇÃO DE CONFIGURAÇÕES
			elseif($modulo=='administracao') {
				administracao($modulo, $sub, $acao, $registro, $matriz);
			}
			### CADASTROS
			elseif($modulo=='cadastros') {
				cadastros($modulo, $sub, $acao, $registro, $matriz);
			}
			## LANÇAMENTOS
			elseif($modulo=='lancamentos') {
				lancamentos($modulo, $sub, $acao, $registro, $matriz);
			}
			## CONSULTAS
			elseif($modulo=='consultas') {
				consultas($modulo, $sub, $acao, $registro, $matriz);
			}
			## RELATÓRIOS
			elseif($modulo=='relatorios') {
				relatorios($modulo, $sub, $acao, $registro, $matriz);
			}
			## FATURAMENTO
			elseif($modulo=='faturamento') {
				faturamento($modulo, $sub, $acao, $registro, $matriz);
			}
			## RADIUS
			elseif($modulo=='radius') {
				administracaoRadius($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## OCORRÊNCIAS
			elseif($modulo=='ocorrencias') {
				ocorrencias($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## CONTRATOS
			elseif($modulo=='contratos') {
				contratosPessoasTipos($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## Ordem de Servico
			elseif($modulo=='ordemdeservico') {
				ordemdeservico($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## Aplicações da Ordem de Servico
			elseif($modulo=='aplicacao') {
				aplicacao($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## Mao de Obra para Ordem de Serviço
			elseif($modulo=='maodeobra') {
				maodeobra($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## Produto
			elseif($modulo=='produto') {
				produto($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## Equipamento
			elseif($modulo=='equipamento') {
				equipamento($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## Equipamento Tipo
			elseif($modulo=='equiptoTipo') {
				equiptoTipo($modulo, $sub, $acao, $registro, $matriz);
			}
			
			## Equipamento Caracteristica
			elseif($modulo=='equiptoCaracteristica') {
				equiptoCaracteristica($modulo, $sub, $acao, $registro, $matriz);
			}
			
			elseif ( $modulo == 'notafiscal' ){
				notaFiscal( $modulo, $sub, $acao, $registro, $matriz );
			}
			
			elseif ( $modulo == "itensnotafiscal" ){
				itensNotaFiscal( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $modulo == 'descontosnotafiscal' ){
				descontosNotaFiscal( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $modulo == 'pagamento_avulso' ){
				pagamentoAvulso( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $modulo == 'planos_contas' ){
				planoDeContas( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $modulo == 'contas_a_pagar' ){
				contasAPagar( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $modulo == 'centro_custo' ){
				centroDeCusto( $modulo, $sub, $acao, $registro, $matriz );	
			}
			elseif ( $modulo == 'fluxo_caixa' ){
				fluxoDeCaixa( $modulo, $sub, $acao, $registro, $matriz );	
			}
			# movimento de estoque
			elseif ( $modulo == 'movimentoEstoque' ) {
				MovimentoEstoque( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif( $modulo == 'nf_faturaservico') {
				NotaFiscalServico( $modulo, $sub, $acao, $registro, $matriz );
			}
		}
		
	}
} # fecha visualizacao de menu




# Visualização de menu adicional em cadastros
function menuOpcAdicional($modulo, $sub, $acao, $registro, $matriz="", $tamanho=2) {
	
	global $corFundo, $moduloApp;

	## ACESSO
	if($modulo=='acesso') {
		# USUARIOS
		if($sub=='usuarios') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=grupos&acao=listar>Selecionar Grupo</a>",'usuario');
			}
			elseif($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
		elseif($sub=='grupos') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			if($acao=='usuariosadicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=adicionar>Novo usuário</a>",'usuario');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=usuarios&registro=$registro>Listar</a>",'listar');
			}
			if($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			if($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
	}
	
	
	## CONFIGURAÇÕES
	elseif($modulo=='configuracoes') {
		# CATEGORIAS
		if($sub=='modulos') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$registro>Parametros</a>",'parametros');
			}
			elseif($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$registro>Parametros</a>",'parametros');
			}
			elseif($acao=='parametrosadicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=parametros&acao=adicionar>Novo parâmetro</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$registro>Listar</a>",'listar');
			}
		}
		elseif($sub=='grupos') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			if($acao=='usuariosadicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=adicionar>Novo usuário</a>",'usuario');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=usuarios&registro=$registro>Listar</a>",'listar');
			}
			if($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			if($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
		elseif($sub=='prioridades' || $sub=='status') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			if($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			if($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
		elseif($sub=='bancos') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			else {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
		elseif($sub=='servicos') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$registro>Parametros</a>",'parametros');
			}
			elseif($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$registro>Parametros</a>",'parametros');
			}
			elseif(strstr($acao, 'parametros')) {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametrosadicionar&registro=$registro>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$registro>Listar</a>",'listar');
			}
			elseif(strstr($acao, 'contratos')) {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contratosadicionar&registro=$registro>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contratos&registro=$registro>Listar</a>",'listar');
			}
			else {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
		if($sub=='status_servicos') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='listar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
			}
		}
		elseif($sub=='forma_cobranca') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='alterar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='excluir') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			else {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
		elseif($sub=='contratos') {
			if($acao=='adicionar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='alterar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='excluir') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='paginasadicionar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginas&registro=$registro>Listar Páginas</a>",'paginas');
			}
			elseif($acao=='paginasexcluir') {
				$tmpOpcao=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasadicionar&registro=$tmpOpcao[0]>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasalterar&registro=$tmpOpcao[1]>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasver&registro=$tmpOpcao[1]>Visualizar</a>",'ver');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginas&registro=$tmpOpcao[0]>Listar Páginas</a>",'paginas');
			}
			elseif($acao=='paginasalterar') {
				$tmpOpcao=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasadicionar&registro=$tmpOpcao[0]>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasexcluir&registro=$tmpOpcao[1]>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasver&registro=$tmpOpcao[1]>Visualizar</a>",'ver');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginas&registro=$tmpOpcao[0]>Listar Páginas</a>",'paginas');
			}
			elseif($acao=='paginasver') {
				$tmpOpcao=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasadicionar&registro=$tmpOpcao[0]>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasexcluir&registro=$tmpOpcao[1]>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasalterar&registro=$tmpOpcao[1]>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginas&registro=$tmpOpcao[0]>Listar Páginas</a>",'paginas');
			}
			else {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar&registro=$registro>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
		elseif($sub=='unidades') {
			if( substr($acao, 0, 6) == 'listar' || $acao == 'procurar' ) {
				$opcoes  = htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar\">Listar Todos</a>",'listar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listarinativo\">Listar Inativos</a>",'desativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listarativo\">Listar Ativos</a>",'ativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listarEstoque\">Listar Unidades de Estoque</a>",'estoque' );
			}
		}
	}
	elseif( $modulo == 'movimentoEstoque' ){
		if( $sub == 'entrada_nf' ) {
			if( $acao == 'baixar' || $acao == 'ver' ) {
				$opcoes = htmlMontaOpcao( '<a href="?modulo='.$modulo.'&sub='.$sub.'&acao=adicionar_item&registro='.$registro.
										  '&matriz[idNFE]='.$matriz['idNFE'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											'">Ver/Adicionar Itens</a>','ver' );
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=baixar&registro=".$registro.
											'&matriz[idNFE]='.$matriz['idNFE'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											"\">Lançar em Estoque</a>", 'baixar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=cancelar&registro=".$registro.
											'&matriz[idNFE]='.$matriz['idNFE'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											"\">Cancelar</a>", 'cancelar');
			}
			if( substr( $acao, 0, 6 ) == 'listar' || $acao == 'procurar' || !$acao ) {
				$opcoes  = htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar\">Todos</a>",'listar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_pendentes\">Pendente</a>",'desativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_baixados\">Baixado</a>",'ativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_cancelados\">Cancelado</a>",'cancelar');
			}
		}
		if( $sub == 'requisicao' ) {
			if( $acao == 'baixar' || $acao == 'ver' ) {
				$opcoes = htmlMontaOpcao( '<a href="?modulo='.$modulo.'&sub='.$sub.'&acao=adicionar_item&registro='.$registro.
										  '&matriz[idRequisicao]='.$matriz['idRequisicao'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											'">Ver/Adicionar Itens</a>','ver' );
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=baixar&registro=".$registro.
											'&matriz[idRequisicao]='.$matriz['idRequisicao'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											"\">Baixar</a>", 'baixar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=cancelar&registro=".$registro.
											'&matriz[idRequisicao]='.$matriz['idRequisicao'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											"\">Cancelar</a>", 'cancelar');
			}
			if( substr($acao, 0, 6 ) == 'listar' || !$acao ) {
				$opcoes  = htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar\">Todos</a>",'listar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_entrada\">Retornos</a>", 'estoque_entrada');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_saida\">Requisições</a>", 'estoque_saida');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_pendentes\">Pendentes</a>", 'desativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_baixados\">Baixados</a>", 'ativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_cancelados\">Cancelados</a>", 'cancelar');
			}
		}
		if( $sub == 'ordemServico' ) {
			if( $acao == 'baixar' || $acao == 'ver' ) {
				$opcoes = htmlMontaOpcao( "<a href=\"?modulo=$modulo&sub=$sub&acao=adicionar_item&registro=".$registro.
										  	'&matriz[idOrdemServico]='.$matriz['idOrdemServico'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											"\">Ver/Adicionar Itens</a>",'ver' );
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=baixar&registro=".$registro.
											'&matriz[idOrdemServico]='.$matriz['idOrdemServico'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											"\">Baixar</a>", 'baixar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=cancelar&registro=".$registro.
											'&matriz[idOrdemServico]='.$matriz['idOrdemServico'].'&matriz[idMovimentoEstoque]='.$matriz['idMovimentoEstoque'].
											"\">Cancelar</a>", 'cancelar');
			}
			if( substr($acao, 0, 6 ) == 'listar' || !$acao ) {
				$opcoes  = htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar\">Todos</a>",'listar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_pendentes\">Pendentes</a>", 'desativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_baixados\">Baixados</a>", 'ativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_cancelados\">Cancelados</a>", 'cancelar');
			}
		}
	}
	
	
	## CADASTROS
	elseif($modulo=='cadastros') {
		# CLIENTES
		$plano=explode(":",$registro);
		if($sub == 'clientes') {
			if($acao=='adicionar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=novo>Novo</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
			}
			elseif($acao=='excluir') {
				$plano=explode(":",$registro);
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$plano[0]:$plano[1]>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$plano[0]>Planos</a>",'planos');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro>Documentos</a>",'documento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$plano[0]>Financeiro</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=impostosPessoas&registro=$plano[0]:$plano[1]>Impostos</a>",'parametros');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&sub=&acao=listar&registro=$registro>Contratos</a>",'contrato');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub$sub&acao=$acao&registro=$plano[0]>Administração</a>",'config');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$plano[0]>Ocorrências</a>",'ocorrencia');				
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
			}
			elseif($acao=='alterar') {
				$plano=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=novo>Novo Cliente</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$plano[0]>Planos</a>",'planos');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro>Documentos</a>",'documento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$plano[0]>Financeiro</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=impostosPessoas&registro=$plano[0]:$plano[1]>Impostos</a>",'parametros');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&sub=&acao=listar&registro=$registro>Contratos</a>",'contrato');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$plano[0]>Ocorrências</a>",'ocorrencia');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
			}
			elseif($acao=='ver') {
				$plano=explode(":",$registro);
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$plano[0]:$plano[1]>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$plano[0]>Planos</a>",'planos');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro>Documentos</a>",'documento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$plano[0]>Financeiro</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=impostosPessoas&registro=$plano[0]:$plano[1]>Impostos</a>",'parametros');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&sub=&acao=listar&registro=$registro>Contratos</a>",'contrato');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub$sub&acao=$acao&registro=$plano[0]>Administração</a>",'config');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$plano[0]>Ocorrências</a>",'ocorrencia');				
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');//
				//opção para o bloquear Empresas no Ticket by Felipe Assis (13/11/2007)
					$parametros = carregaParametrosConfig();
					// Verifica se o ISP está configurado para se integrar ao Ticket-IT
					if (strtoupper($parametros['integrarTicket']) == 'S') {
						/*A consulta abaixo retorna o id do módulo, caso o mesmo
						 esteja vinculado à um serviço
						*/
						
						/*
						 * registro = idPessoaTipo:idPessoa
						 * recuperando idPessoaTipo e idPessoa
						*/
						
						$registro = explode(":", $registro);
						$idPessoaTipo = $registro[0];
						$idPessoa = $registro[1]; 
						
						$conModulos = buscaModulos($idPessoaTipo, '', 'buscarModuloCliente', '');
						$resultados = mysql_num_rows($conModulos);
						if($resultados > 0){ // se o cliente tem módulos vinculados
							// selecionado possuem parâmetros 
							$opcoes .= opcaoBloquearCliente('administracao', $sub, $acao, $matriz, $idPessoaTipo, $idPessoa);
						}
						
					}
			}
			elseif(strstr($acao, 'enderecos') || strstr($acao, 'documentos') || strstr($acao, 'impostosPessoas')) {
				$plano=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$plano[0]:$plano[1]>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$plano[0]>Planos</a>",'planos');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=ver&registro=$registro>Cadastro</a>",'ver');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro>Documentos</a>",'documento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$plano[0]>Financeiro</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=impostosPessoas&registro=$plano[0]:$plano[1]>Impostos</a>",'parametros');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&sub=&acao=listar&registro=$registro>Contratos</a>",'contrato');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub$sub&acao=$acao&registro=$plano[0]>Administração</a>",'config');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$plano[0]>Ocorrências</a>",'ocorrencia');				
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				
			}
		}
		elseif($sub == 'bancos' || $sub=='fornecedores' || $sub=='condominios') {
			if($acao=='adicionar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=novo>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro>Documentos</a>",'documento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
			}
			elseif($acao=='alterar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=novo>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro>Documentos</a>",'documento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
			}
			elseif($acao=='ver') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=novo>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro>Documentos</a>",'documento');
			}
			elseif(strstr($acao, 'enderecos') || strstr($acao, 'documentos') ) {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$registro>Documentos</a>",'documento');
			}
		}
		elseif($sub == 'pop') {
			if($acao=='cidadesadicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cidades&registro=$registro>Cidades</a>",'cidades');
			}
			elseif($acao=='cidadesexcluir') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cidades>Cidades</a>",'cidades');
			}
			elseif($acao=='cidadesalterar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cidades>Cidades</a>",'cidades');
			}
			elseif($acao == 'alterar' || $acao == 'documentos' || $acao == 'enderecos' ) {
				$opcoes = exibePopPessoaTipoMenu($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif( $sub == 'produtos' ) {
			if( substr($acao, 0, 6 ) == 'listar' || !$acao ) {
				$opcoes  = htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar\">Todos</a>",'listar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_ativos\">Ativos</a>", 'ativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_inativos\">Inativos</a>", 'desativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_fracionados\">Fracionados</a>", 'modulo');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_nfracionados\">Não-Fracionados</a>", 'quota');
			}
			if( $acao == 'ver' ) {
				$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $registro;
				$opcoes  = htmlMontaOpcao( $default . "&acao=ver\">Ver</a>", 'ver' );
				$opcoes .= htmlMontaOpcao( $default . "&acao=alterar\">Alterar</a>", 'alterar' );
				$opcoes .= htmlMontaOpcao( $default . "&acao=novo_item\">Adicionar Item</a>", 'incluir' );
			}
		}
		elseif( $sub == 'produtoComposto' ) {
			if( substr($acao, 0, 6 ) == 'listar' || !$acao ) {
				$opcoes  = htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar\">Todos</a>",'listar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_ativos\">Ativos</a>", 'ativar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_inativos\">Inativos</a>", 'desativar');
			}
			if( $acao == 'ver' ) {
				$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $registro;
				$opcoes  = htmlMontaOpcao( $default . "&acao=ver\">Ver</a>", 'ver' );
				$opcoes .= htmlMontaOpcao( $default . "&acao=alterar\">Alterar</a>", 'alterar' );
				$opcoes .= htmlMontaOpcao( $default . "&acao=excluir\">Excluir</a>", 'excluir' );
				$opcoes .= htmlMontaOpcao( $default . "&acao=novo_item\">Adicionar Item</a>", 'incluir' );
			}
		}
		elseif( $sub == 'produtosEstoque' ) {
			if( substr($acao, 0, 6 ) == 'listar' || !$acao ) {
				$opcoes  = htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar\">Todos</a>",'listar');
				$opcoes .= htmlMontaOpcao("<a href=\"?modulo=$modulo&sub=$sub&acao=listar_emfalta\">Abaixo do Mínimo</a>",'abaixo');
			}
		}
		
	}
	
	## LANÇAMENTOS
	elseif($modulo=='lancamentos') {
		# PLANOS
		if($sub=='planos') {
			if($acao=='adicionar') {
				$plano=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=novo>Novo</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar&registro=$plano[0]>Planos</a>",'planos');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=documentos&registro=$registro>Documentos</a>",'documento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$plano[0]>Financeiro</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&sub=&acao=listar&registro=$registro>Contratos</a>",'contrato');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=&acao&registro=$plano[0]>Administração</a>",'config');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$plano[0]>Ocorrências</a>",'ocorrencia');
				
			}
			elseif($acao=='listar' || $acao=='listartodos' || $acao=='ver') {
				$plano=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=novo>Novo</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=ver&registro=$registro>Cadastro</a>",'ver');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=enderecos&registro=$registro>Endereços</a>",'endereco');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=documentos&registro=$registro>Documentos</a>",'documento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$plano[0]>Financeiro</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&sub=&acao=listar&registro=$registro>Contratos</a>",'contrato');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=&acao&registro=$plano[0]>Administração</a>",'config');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$plano[0]>Ocorrências</a>",'ocorrencia');
			}
			elseif($acao=='abrir') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar Plano</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Listar Serviços</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$registro>Cancelar</a>",'cancelar');
			}
			elseif($acao=='cancelar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar Plano</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Abrir Plano</a>",'abrir');
			}
			elseif($acao=='alterar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$registro>Cancelar</a>",'cancelar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Abrir Plano</a>",'abrir');
			}
			elseif($acao=='adicionarservico') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$registro>Cancelar</a>",'cancelar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Abrir Plano</a>",'abrir');
			}
			elseif($acao=='alterarservico' || $acao=='ativarservico' || $acao=='desativarservico') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar Plano</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Abrir Plano</a>",'abrir');
			}
			elseif($acao=='verservico') {
				$plano=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$plano[0]>Alterar Plano</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$plano[0]>Listar Serviços</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$plano[0]>Abrir Plano</a>",'abrir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$plano[1]>Alterar Serviço</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$plano[1]>Cancelar Serviço</a>",'cancelar');
			}
			elseif($acao=='cancelarservico') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar Plano</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Abrir Plano</a>",'abrir');
			}
			elseif($acao=='desativarservico') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Listar Serviços</a>",'listar');
			}
			
			# DESCONTOS
			elseif($acao=='descontosservico' || $acao=='descontosservicotodos') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Listar Serviços</a>",'listar');
			}
			elseif($acao=='adicionardesconto') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$registro>Descontos Ativos</a>",'desconto');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservicotodos&registro=$registro>Todos os Descontos</a>",'desconto');
			}
			elseif($acao=='alterardesconto') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$registro>Descontos Ativos</a>",'desconto');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservicotodos&registro=$registro>Todos os Descontos</a>",'desconto');
			}
			elseif($acao=='verdesconto') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$registro>Descontos Ativos</a>",'desconto');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservicotodos&registro=$registro>Todos os Descontos</a>",'desconto');
			}
			elseif($acao=='desativardesconto') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$registro>Listar Descontos</a>",'desconto');
			}
			elseif($acao=='ativardesconto') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$registro>Listar Descontos</a>",'desconto');
			}
			
			# SERVICOS ADICIONAIS
			elseif($acao=='servicosadicionais' || $acao=='servicosadicionaistodos') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Listar Serviços</a>",'listar');
			}
			elseif($acao=='adicionarservicoadicional') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$registro>Servicos Adicionais</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionarservicoadicional&registro=$registro>Novo Serviço Adicional</a>",'incluir');
			}
			elseif($acao=='ativarservicoadicional') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionarservicoadicional&registro=$registro>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$registro>Servicos Adicionais</a>",'lancamento');
			}
			elseif($acao=='desativarservicoadicional') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionarservicoadicional&registro=$registro>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$registro>Servicos Adicionais</a>",'lancamento');
			}
			elseif($acao=='cancelarservicoadicional') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionarservicoadicional&registro=$registro>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$registro>Servicos Adicionais</a>",'lancamento');
			}
			elseif($acao=='verservicoadicional') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionarservicoadicional&registro=$registro>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$registro>Servicos Adicionais</a>",'lancamento');
			}
			/*contra partidas*/
			elseif ( substr($acao, 0, 19) == 'contrapartidalistar'){
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contrapartidaadicionar&registro=$registro>Adicionar</a>",'incluir');
			}
			elseif( substr($acao, 0, 13) == 'contrapartida'){
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contrapartidalistar&registro=$registro> Listar</a>",'listar');
			}
		}
		
		
		### MANUTENÇÃO
		elseif($sub=='manutencao') {
			if($acao=='aplicardescontos') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=aplicardescontos&registro=$registro>Aplicar Descontos</a>",'desconto');
			}
		}
	}
	
	### FATURAMENTO
	elseif($modulo=='faturamento') {
		# Geração de faturamento
		if($sub=='geracao') {
			# Geração
			if($acao=='gerar' || $acao=='detalhes') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=gerar>Gerar novo faturamento</a>",'faturamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Faturamentos Inativos</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarativos>Faturamentos Ativos</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarcancelados>Faturamentos Cancelados</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listartodos>Todos</a>",'listar');		
			}
		}
		
		# Histórico de faturamento por cliente
		elseif($sub=='clientes') {
			# Histórico
			if($acao=='historico' || $acao=='historico_pendente' || $acao=='historico_pago' || $acao=='dados_cobranca' || $acao=='baixar' || $acao=='cancelar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=historico&registro=0&matriz[idPessoaTipo]=$registro>Resumo</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=historico_pendente&registro=0&matriz[idPessoaTipo]=$registro>Pagamentos Pendentes</a>",'desconto');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=historico_pago&registro=0&matriz[idPessoaTipo]=$registro>Pagamentos Efetuados</a>",'lancamento');
			}
		}
		elseif($sub== 'debitoAutomatico'){
			# Imprimir
			if($acao== 'listar'){
					$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=imprimirLista>Imprimir Lista</a>",'imprimir');
			}
		}
		# Arquivos Remessa
		elseif($sub=='arquivoremessa') {
			if($acao=='gerar' || $acao=='download') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Arquivos pendentes</a>",'arquivo');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listargerados>Arquivos gerados</a>",'arquivo');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listartodos>Todos</a>",'arquivo');			
			}
		}
	}
	
	### RADIUS
	elseif($modulo=='radius') {
		if($sub=='grupos' || $sub=='usuarios') {
			# Grupos
			if($acao=='adicionar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='ver') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='alterar') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='excluir') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
	}
	
	
	
	### ADMINISTRACAO
	elseif($modulo=='administracao') {
		if($sub=='dominio') {
			if($acao=='parametrosadicionar' || $acao=='parametrosexcluir' || $acao=='parametrosalterar') {
				$matTmp=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$matTmp[0]:$matTmp[1]>Listar Parametros</a>&nbsp;&nbsp;",'config');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=config&registro=$matTmp[0]>Listar Dominios</a>",'web');
			}
		}
		if($sub=='dial') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=alterar&registro=$matTmp[0]:$matTmp[1]>Senha</a>",'senha');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=excluir&registro=$matTmp[0]:$matTmp[1]>Excluir</a>",'excluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=extrato&registro=$matTmp[0]:$matTmp[1]>Extrato</a>",'extrato');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=telefones&registro=$matTmp[0]:$matTmp[1]>Telefones</a>",'fone');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=dial&acao=config&registro=$matTmp[0]>Listar Contas</a>",'listar');
		}
		if($sub=='mail') {
			if($acao=='adicionar' || $acao=='alterar') {
				$matTmp=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar&registro=$matTmp[0]:$matTmp[1]>Contas</a>",'mail');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=config&registro=$matTmp[0]>Listar Dominios</a>",'config');
			}
			elseif($acao=='excluir') {
				$matTmp=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar&registro=$matTmp[0]:$matTmp[1]>Contas</a>",'mail');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=config&registro=$matTmp[0]>Listar Dominios</a>",'config');
			
			}
			elseif(strstr($acao, 'forward')) {
				$matTmp=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar&registro=$matTmp[0]:$matTmp[1]>Contas</a>",'mail');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=config&registro=$matTmp[0]>Listar Dominios</a>",'config');
			}
			elseif($acao=='emailconfig') {
				$matTmp=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar&registro=$matTmp[0]:$matTmp[1]>Contas</a>",'mail');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=config&registro=$matTmp[0]>Listar Dominios</a>",'config');
			}
			elseif($acao=='autoreply') {
				$matTmp=explode(":",$registro);
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar&registro=$matTmp[0]:$matTmp[1]>Contas</a>",'mail');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=config&registro=$matTmp[0]>Listar Dominios</a>",'config');
			}
		}
	}
	
	### OCORRÊNCIAS
	elseif($modulo=='ocorrencias') {
		if($acao=='procurar' || $acao=='listar' || $acao=='listartodos' || !$acao) {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$matTmp[0]>Planos</a>",'planos');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar>Ocorrências</a>",'ocorrencia');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$matTmp[0]>Financeiro</a>",'lancamento');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=&acao&registro=$matTmp[0]>Administração</a>",'config');
		}
		elseif($acao=='adm') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=adicionar>Adicionar Ocorrência</a>",'incluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=listar>Ocorrências em Aberto</a>",'listar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=listartodos>Todas as Ocorrências</a>",'listar');
		}
		elseif($acao=='historico') {
			$matTmp=explode(":",$registro);
			
			$ocorrencia=dadosOcorrencia($matTmp[0]);
			if($ocorrencia[status]!='F' && $ocorrencia[status]!='N' && $ocorrencia[status]!='C') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=comentar&registro=$registro>Adicionar Comentario</a>",'comentar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=fechar&registro=$registro>Fechar</a>",'fechar');
			}
			elseif($ocorrencia[status]=='F') {
				# Re-abrir
				$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=reabrir&registro=$registro>Re-Abrir</a>",'abrir');
			}
			elseif($ocorrencia[status]=='N') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=abrir&registro=$registro>Abrir Ocorrência</a>",'abrir');
			}
			
			# Ignorar canceladas
			if($ocorrencia[status]!='C' && $ocorrencia[status]!='F') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=cancelar&registro=$registro>Cancelar</a>",'cancelar');
			}
			else {
				$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=adicionar>Adicionar Ocorrência</a>",'incluir');
			}
		}
		elseif($acao=='comentar') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=&acao=historico&registro=$registro>Histórico</a>",'historico');
		}
		elseif($acao=='adicionar') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$matTmp[0]>Listar Ocorrências</a>",'listar');
		}
		elseif($acao=='alterar') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=excluir&registro=$matTmp[1]>Excluir</a>",'excluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=abrir&registro=$matTmp[1]>Abrir</a>",'abrir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$matTmp[0]>Listar Ocorrências</a>",'listar');
		}
		elseif($acao=='excluir') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=alterar&registro=$matTmp[1]>Alterar</a>",'alterar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$matTmp[0]>Listar Ocorrências</a>",'listar');
		}
		elseif($acao=='ver') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=alterar&registro=$matTmp[1]>Alterar</a>",'alterar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=excluir&registro=$matTmp[1]>Excluir</a>",'excluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=abrir&registro=$matTmp[1]>Abrir</a>",'abrir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$matTmp[0]>Listar Ocorrências</a>",'listar');
		}
		elseif($acao=='abrir') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=alterar&registro=$matTmp[1]>Alterar</a>",'alterar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=excluir&registro=$matTmp[1]>Excluir</a>",'excluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub=$sub&acao=listar&registro=$matTmp[0]>Listar Ocorrências</a>",'listar');
		}
	}
	
	### OCORRÊNCIAS
	elseif($modulo=='contratos') {
		if($acao=='contratos' || $acao=='listar' || !$acao) {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=contratos&sub=planos&acao=listar&registro=$matTmp[0]>Listar Contratos</a>",'contrato');
		}
		elseif($acao=='gerar' || $acao=='cancelar' || $acao=='renovar') {
			$matTmp=explode(":",$registro);
			$opcoes=htmlMontaOpcao("<a href=?modulo=contratos&sub=planos&acao=listar&registro=$matTmp[0]>Listar Contratos</a>",'contrato');
		}
	}
	
	### Ordem de Servico
	elseif($modulo=='ordemdeservico') {
		
		$def="<a href=?modulo=$modulo&sub=ordemdeservico&registro=$registro&";
		
		if($acao=='ver') {
			$opcoes=htmlMontaOpcao($def."acao=detalhar>Detalhar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=OrdemServico&registro=$registro&acao=adicionarDetalhe>Incluir Detalhe</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=imprimir>Imprimir</a>",'imprimir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=alterar>Alterar</a>",'alterar');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=fechar>Fechar</a>",'fechar');
		} 
		elseif($acao=='alterar') {
			$opcoes=htmlMontaOpcao($def."acao=ver>Ver</a>",'ver');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=detalhar>Detalhar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=fechar>Fechar</a>",'fechar');
		}
		elseif(substr($acao, 0, 6)=='listar') {
			$opcoes=htmlMontaOpcao($def."acao=listarAtivas>Listar Ativas</a>",'listar');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=listarInativas>Listar Fechadas</a>",'listar');
		}
	}
	
	### Ordem de Servico
	elseif($modulo=='aplicacao') {
		
		$def="<a href=?modulo=$modulo&sub=aplicacao&";
		
		if($acao=='ver') {
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=alterar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=alterar>Alterar</a>",'alterar');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=excluir>Excluir</a>",'excluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
		if($acao=='alterar') {
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=alterar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=ver>Ver</a>",'ver');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=excluir>Excluir</a>",'excluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
		if($acao=='excluir') {
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=ver>Ver</a>",'ver');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=alterar>Alterar</a>",'alterar');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
	}
	
	#======================================================
	### Ordem de Servico
	elseif($modulo=='maodeobra') {
		
		$def="<a href=?modulo=$modulo&sub=maodeobra&";
		
		if($acao=='ver') {
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=alterar>Alterar</a>",'alterar');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=excluir>Excluir</a>",'excluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
		if($acao=='alterar') {
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=ver>Ver</a>",'ver');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=excluir>Excluir</a>",'excluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
		if($acao=='excluir') {
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=ver>Ver</a>",'ver');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=alterar>Alterar</a>",'alterar');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
	}
	#======================================================
	### Servicos IVR
	elseif($modulo=='servicoIVR') {
		
		$def="<a href=?modulo=administracao&sub=ivr&acao=config&registro=$registro&idServicoPlano=$matriz[idServicoPlano]&";
		
		if($acao=='alterar') {
			$opcoes=htmlMontaOpcao($def."subacao=alterar>Alterar</a>",'alterar');
		} 
		elseif($acao=='ver') {
			$opcoes=htmlMontaOpcao($def."subacao=ver>Ver</a>",'ver');
		} 
		elseif($acao=='adicionar') {
			$opcoes=htmlMontaOpcao($def."subacao=adicionar>Adicionar</a>",'incluir');
		}
		$opcoes.="&nbsp;&nbsp;";
		$opcoes.=htmlMontaOpcao($def."subacao=listar>Listar</a>",'listar');
	} // fim das opcoes p/ servico IVR
	
	elseif ($modulo== "produto"){
		$def="<a href=?modulo=$modulo&sub=produto&";	
		if($acao== 'ver'){
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=alterar>Alterar</a>",'alterar');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=excluir>Excluir</a>",'excluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
		if($acao=='alterar') {
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=ver>Ver</a>",'ver');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=excluir>Excluir</a>",'excluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
		if($acao=='excluir') {
			$opcoes=htmlMontaOpcao($def."acao=listar>Listar</a>",'consultas');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=ver>Ver</a>",'ver');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=alterar>Alterar</a>",'alterar');
			$opcoes.="&nbsp;&nbsp;";
			$opcoes.=htmlMontaOpcao($def."registro=$registro&acao=procurar>Procurar</a>",'procurar');
		}
	} // Fim das opcoes adicionais de produto
	
	//Opções para a Nota Fiscal
	elseif ( $modulo == "notafiscal" ||  $modulo == 'nf_faturaservico' ){
		$def = "<a href=?modulo=$modulo&sub=$sub&";
		
		if ( $acao == "adicionar" ){
			$opcoes = htmlMontaOpcao($def."acao=adicionar>Adicionar</a>",'incluir');
		}
		elseif ( substr( $acao, 0, 6 ) == "listar" ){
			$opcoes = htmlMontaOpcao($def."acao=listar>Listar</a>",'listar');
			$opcoes .= htmlMontaOpcao($def."acao=listarImpressa>Listar Impressas</a>",'listar');
			$opcoes .= htmlMontaOpcao($def."acao=listarCancelada>Listar Canceladas</a>",'listar');
			$opcoes .= htmlMontaOpcao($def."acao=listarTodas>Listar Todas</a>",'listar');
		}
		elseif ( $acao == "ver" ){
			if ( strtoupper( $_REQUEST['status'] ) == "A" ){
				$opcoes .= htmlMontaOpcao( $def."registro=$registro&acao=excluir>Excluir</a>",'excluir');
				$opcoes .= htmlMontaOpcao( $def."registro=$registro&acao=alterarDtEmissao>Imprimir</a>", 'imprimir');
			}
			elseif ( strtoupper( $_REQUEST['status'] == "I" ) ){
				$opcoes = htmlMontaOpcao( $def."registro=$registro&acao=cancelar>Cancelar</a>",'cancelar');
			} 
		}
		elseif ( $acao == "excluir" ){
			$opcoes = htmlMontaOpcao( $def."registro=$registro&acao=listar>Listar</a>", "listar" )	;
		}
	}
	
	// Opcoes para pagamento avulso
	elseif ( $modulo == "pagamento_avulso" ){
		$def = "<a href=?modulo=$modulo&sub=$sub&";
		if ( substr( $acao, 0, 6 ) == "listar" || !$acao){
			$opcoes = htmlMontaOpcao($def."acao=listar_baixadas>Listar Baixadas</a>",'lancamento');
			$opcoes .= htmlMontaOpcao($def."acao=listar_pendentes>Listar Pendentes</a>",'desconto');
			$opcoes .= htmlMontaOpcao($def."acao=listar>Listar Todas</a>",'listar');
		}
	}
	
	// Opcoes para contas a pagar
	elseif ( $modulo == "contas_a_pagar" ){
		$def = "<a href=?modulo=$modulo&sub=$sub&";
		if ( substr( $acao, 0, 6 ) == "listar" || !$acao){
			$opcoes  = htmlMontaOpcao($def."acao=listar_semana>Listar Semana</a>",'lancamento');
			$opcoes .= htmlMontaOpcao($def."acao=listar_mes>Listar Mês</a>",'lancamento');
			$opcoes .= htmlMontaOpcao($def."acao=listar_baixadas>Listar Baixadas</a>",'lancamento');
			$opcoes .= htmlMontaOpcao($def."acao=listar_pendentes>Listar Pendentes</a>",'desconto');
			$opcoes .= htmlMontaOpcao($def."acao=listar_todas>Listar Todas</a>",'listar');
		}
	}
	
	// Opcoes para Centro de Custo
	elseif ( $modulo == "centro_custo" ){
			$def = "<a href=?modulo=".$modulo."&sub=previsao&";
			
			$opcoes = htmlMontaOpcao($def."acao=adicionar>Lançar Previsões</a>",'lancamento');
			$opcoes .= htmlMontaOpcao($def."acao=listar_previsao>Listar Previsões</a>",'listar');	
	}
	
	if($opcoes) {
		# Mostrar opção adicional
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL("<span class=normal8>$opcoes</span>", 'right', $corFundo, $tamanho, 'tabfundo1');
		fechaLinhaTabela();
	}

} // fim do menu adicional
# fecha menu adicional

?>