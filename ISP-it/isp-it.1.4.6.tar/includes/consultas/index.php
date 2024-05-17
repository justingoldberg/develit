<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Includes de Consultas

include('faturamento_pop.php');
include('titulos_em_aberto.php');
include('simulacao_faturamento.php');
include('geral_faturamento.php');
include('simulacao_faturamento_cliente.php');
include('detalhamento_cliente.php');
include('servicos_clientes.php');
include('geral_clientes.php');
include('descontos_concedidos.php');
include('ocorrencias_clientes.php');
include('contratos_clientes.php');
include('dial_clientes.php');
include('email_clientes.php');
include('dominios_cliente.php');
include('clientes_pop.php');
include('inadimplentes.php');
include('dominios_parceiro.php');
include('simulacao_faturamento_grupo_servico.php');
include( 'listar_cliente_endereco.php' );

# Consultas
function consultas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Consultas]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][consulta]." border=0 align=left><b class=bold>Consultas</b>
					<br><span class=normal10>O módulo de <b>consultas</b> permite a rápida visualização dos resultados
					obtidos, bem como acompanhamento das movimentações e lançamentos.</span>";
				htmlFechaColuna();
			fechaLinhaTabela();
		fechaTabela();
		
		# Menus de seleção
		if(!$sub) {
			# Mostrar menu principal
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					novaTabela2SH("center", '100%', 0, 0, 0, $corFundo, $corBorda, 3);
						novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
								novaTabela2("[Consultas Gerais]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
									$texto=htmlMontaOpcao("Detalhamento de Clientes", 'cadastros');
									itemTabela($texto, "?modulo=$modulo&sub=clientes", 'left', $corFundo, 0, 'normal');
									#comentado por necessitar de correção
									#$texto=htmlMontaOpcao("Servicos por Cliente", 'servicos');
									#itemTabela($texto, "?modulo=$modulo&sub=servicos_clientes", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Consulta Geral de Clientes", 'usuario');
									itemTabela($texto, "?modulo=$modulo&sub=geral_clientes", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Ocorrências por Cliente", 'ocorrencia');
									itemTabela($texto, "?modulo=$modulo&sub=ocorrencias_cliente", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Contas de Dial-UP por Cliente", 'dial');
									itemTabela($texto, "?modulo=$modulo&sub=dial_cliente", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Contas de E-mails por Cliente", 'mail');
									itemTabela($texto, "?modulo=$modulo&sub=email_cliente", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Domínios por Cliente", 'dominio');
									itemTabela($texto, "?modulo=$modulo&sub=dominio_cliente", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Contratos por Cliente", 'contrato');
									itemTabela($texto, "?modulo=$modulo&sub=contrato_cliente", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Domínios por Parceiro", 'contrato');
									itemTabela($texto, "?modulo=$modulo&sub=dominio_parceiro", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Listagem de Cliente/Endereço por Faturamento", 'faturamento');
									itemTabela($texto, "?modulo=$modulo&sub=listar_cliente_endereco", 'left', $corFundo, 0, 'normal');
								fechaTabela();
							htmlFechaColuna();
							itemLinhaTMNOURL('&nbsp;&nbsp;&nbsp;', 'center', 'middle', '1', $corFundo, 0, 'normal10');
							htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
								novaTabela2("[Consultas de Movimentação]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
									$texto=htmlMontaOpcao("Simulação Geral de Faturamento", 'consultas');
									itemTabela($texto, "?modulo=$modulo&sub=faturamento", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Simulação de Faturamento por Cliente", 'consultas');
									itemTabela($texto, "?modulo=$modulo&sub=faturamento_cliente", 'left', $corFundo, 0, 'normal');
									#comentado para correção
									$texto=htmlMontaOpcao("Simulação de Faturamento por Grupo Serviços", 'consultas');
									itemTabela($texto, "?modulo=$modulo&sub=faturamento_grupo_servico", 'left', $corFundo, 0, 'normal');
								fechaTabela();
								echo "<br>";
								novaTabela2("[Consultas Financeiras]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
									$texto=htmlMontaOpcao("Faturamento por Cliente", 'lancamento');
									itemTabela($texto, "?modulo=$modulo&sub=clientes", 'left', $corFundo, 0, 'normal');							
									$texto=htmlMontaOpcao("Serviços por Cliente", 'lancamento');
									itemTabela($texto, "?modulo=$modulo&sub=servicos_clientes", 'left', $corFundo, 0, 'normal');							
									$texto=htmlMontaOpcao("Consulta Geral de Faturamento", 'lancamento');
									itemTabela($texto, "?modulo=$modulo&sub=faturamento_geral", 'left', $corFundo, 0, 'normal');							
									$texto=htmlMontaOpcao("Descontos Concedidos", 'desconto');
									itemTabela($texto, "?modulo=$modulo&sub=descontos", 'left', $corFundo, 0, 'normal');							
								fechaTabela();
							htmlFechaColuna();
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
		}
		elseif($sub=='faturamento') {
			# Pedir dados para geração de faturamento
			echo "<br>";
			
			formSimulacaoFaturamento($modulo, $sub, $acao, $registro, $matriz);
			
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaSimulacaoFaturamento($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='faturamento_geral') {
			# Pedir dados para geração de faturamento
			echo "<br>";
			
			formGeralFaturamento($modulo, $sub, $acao, $registro, $matriz);
			
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaGeralFaturamento($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='faturamento_cliente') {
			# selecionar o cliente
			echo "<br>";
			
			formSimulacaoFaturamentoCliente($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar] || $matriz[bntGerarCobranca]) {
				# Prosseguir com consulta
				consultaSimulacaoFaturamentoCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='clientes') {
			# selecionar o cliente
			echo "<br>";
			
			formDetalhamentoCliente($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaDetalhamentoCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='servicos_clientes') {
			# selecionar o cliente
			echo "<br>";
			
			formServicosClientes($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaServicosClientes($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='geral_clientes') {
			# selecionar o cliente
			echo "<br>";
			
			formConsultaGeralClientes($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaGeralClientes($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='descontos') {
			echo "<br>";
			
			formDescontosConcedidos($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaDescontosConcedidos($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='ocorrencias_cliente') {
			echo "<br>";
			
			formOcorrenciasCliente($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaOcorrenciasCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='dial_cliente') {
			echo "<br>";
			
			formDialCliente($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaDialCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='email_cliente') {
			echo "<br>";
			
			formConsultaEmailCliente($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaEmailsCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='dominio_cliente') {
			echo "<br>";
			
			formConsultaDominiosCliente($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaDominiosCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='contrato_cliente') {
			echo "<br>";
			
			formContratosCliente($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				consultaContratosCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		elseif($sub=='dominio_parceiro') {
			echo "<br>";
			
			formConsultaDominioParceiro($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar] || $matriz[bntRelatorio]) {
				# Prosseguir com consulta
				consultaDominioParceiro($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		elseif ( $sub == 'faturamento_grupo_servico' ){
			echo "<br>";
			
			formRelatorioSimulacaoFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz);
			
			#realizar consulta
			if ( $matriz[bntConfirmar] || $matriz[bntRelatorio] ){
				#prosseguir com consulta
				consultaSimulacaoFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		elseif ( $sub == 'listar_cliente_endereco' ){
			echo "<br>";
			
			if ( !$matriz[idFaturamento] ){
				formRelatorioListarClienteEndereco($modulo, $sub, $acao, $registro, $matriz);
			}
			
			#realizar consulta
			elseif ( $matriz[idFaturamento] || $matriz[bntConfirmar] ){
				$registro = $matriz[idFaturamento];
				verFaturamento( $modulo, $sub, $acao, $registro, $matriz );
				
				echo "<br>";

				#prosseguir com consulta
				consultaListarClienteEndereco($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		//echo "<script> if(document.forms[0]) { location.href='#ancora'; } </script>";
	}
}

?>