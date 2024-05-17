<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 20/05/2003
# Ultima alteração: 15/04/2004
#    Alteração No.: 047
#
# Função:
#      Includes de Relatórios

include('titulos_em_aberto.php');
include('faturamento_pop.php');
include('faturamento_grupo_servico.php');
include('recebimento_grupo_servico.php');
include('clientes_pop.php');
include('clientesgruposservicos.php');
include('inadimplentes.php');
include('ordemservico.php');
include('inadimplentes_gruposservicos.php');
include('faturamento_pop_baixa.php');
include('servicosadicionais.php');
include('baixa_servico.php');
include('clientes_valor.php');
include('clientes.php');
include('cliente_servico.php');
include('cliente_plano_especial.php');
include('ocorrencias.php');
include('email_cliente.php');
include('nota_fiscal.php');
include('clientes_valor_nominal.php');
include('contas_a_pagar_plano_de_contas.php');
include('contas_a_pagar_fornecedor.php');
include('contas_a_pagar_centro_de_custo.php');
include('FluxoDeCaixa.php');
include('OrdemServicoCliente.php');
include('ProdutosInventario.php');
include('Retorno.php');
include('SaidaProdutos.php');
include('cliente_quantidade_servico.php');
include('inadimplentes_recebidos.php');

# Menu principal de relatórios
function relatorios($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin, $modulos;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
		
	if(!$permissao[admin] && !$permissao[visualizar] ) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 400);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Relatórios]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][relatorios]." border=0 align=left><b class=bold>Relatórios</b>
					<br><span class=normal10>O módulo de <b>relatórios</b> permite a extração de informações cruciais
					para o departamento financeiro e faturamento, além de permitir a visualização rápida de 
					informações agrupadas.</span>";
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
							novaTabela2("[Relatórios Gerais]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
								$texto=htmlMontaOpcao("Clientes por POP", 'pessoa');
								itemTabela($texto, "?modulo=$modulo&sub=cliente_pop", 'left', $corFundo, 0, 'normal');
								#
								$texto=htmlMontaOpcao("Detalhamento de Clientes", 'procurar');
								itemTabela($texto, "?modulo=$modulo&sub=cliente_detalhe", 'left', $corFundo, 0, 'normal');
								#
								$texto= htmlMontaOpcao("E-mails por Cliente",'mail');
								itemTabela($texto, "?modulo=$modulo&sub=email_cliente",'left',$corFundo,0,'normal');
								#
								$texto=htmlMontaOpcao("Clientes - Customizado", 'pessoa');
								itemTabela($texto, "?modulo=$modulo&sub=clientes", 'left', $corFundo, 0, 'normal');
								#comentado por estar com falhas
								#$texto=htmlMontaOpcao("Clientes - por Grupo de Serviço", 'grupo');
								#itemTabela($texto, "?modulo=$modulo&sub=clientesgruposervico", 'left', $corFundo, 0, 'normal');
								#
								#$texto=htmlMontaOpcao("Ordem de Serviço", 'procurar');
								#itemTabela($texto, "?modulo=$modulo&sub=ordemservico", 'left', $corFundo, 0, 'normal');
								#
								$texto= htmlMontaOpcao("Clientes por Serviço",'configuracoesgerais');
								itemTabela($texto, "?modulo=$modulo&sub=clienteservico",'left',$corFundo,0,'normal');								
								#
								$texto= htmlMontaOpcao("Clientes por Plano Especial",'configuracoesgerais');
								itemTabela($texto, "?modulo=$modulo&sub=cliente_plano_especial",'left',$corFundo,0,'normal');								
								# Quantidade/Cliente por Serviço
								$texto= htmlMontaOpcao("Quantidade de Clientes por Serviço",'configuracoesgerais');
								itemTabela($texto, "?modulo=$modulo&sub=cliente_quantidade_servico",'left',$corFundo,0,'normal');
								#
								$texto= htmlMontaOpcao("Ocorrências - Clientes",'ocorrencia');
								itemTabela($texto, "?modulo=$modulo&sub=ocorrencias",'left',$corFundo,0,'normal');								
							fechaTabela();
							if( $modulos['controleEstoque'] ){
								echo "<br />";
								novaTabela2("[Relatórios de Controle de Estoque]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
									$texto=htmlMontaOpcao("Inventário", 'procurar');
									itemTabela($texto, "?modulo=$modulo&sub=produtosInventario", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Ordem de Serviço", 'configuracoesgerais');
									itemTabela($texto, "?modulo=$modulo&sub=ordemServicoCliente", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Retorno de Produtos", 'estoque_entrada');
									itemTabela($texto, "?modulo=$modulo&sub=retornoProdutos", 'left', $corFundo, 0, 'normal');
									$texto=htmlMontaOpcao("Saída de Produtos", 'estoque_saida');
									itemTabela($texto, "?modulo=$modulo&sub=saidaProdutos", 'left', $corFundo, 0, 'normal');								
								fechaTabela();
							}
							if( $permissao['admin'] ) {
								echo "<br>";
								novaTabela2("[Relatórios de Contas à Pagar]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
									$texto=htmlMontaOpcao("Por Plano De Contas", 'desconto');
									itemTabela($texto, "?modulo=$modulo&sub=contas_pagar_plano_contas", 'left', $corFundo, 0, 'normal');
								
									$texto=htmlMontaOpcao("Por Fornecedor", 'desconto');
									itemTabela($texto, "?modulo=$modulo&sub=contas_pagar_fornecedor", 'left', $corFundo, 0, 'normal');
	
									$texto=htmlMontaOpcao("Por Previsão de Centro de Custo", 'desconto');
									itemTabela($texto, "?modulo=$modulo&sub=contas_pagar_centro_custo", 'left', $corFundo, 0, 'normal');
								fechaTabela();
							}
						htmlFechaColuna();
						
						
						itemLinhaTMNOURL('&nbsp;&nbsp;&nbsp;', 'center', 'middle', '1', $corFundo, 0, 'normal10');
						htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
							novaTabela2("[Relatórios de Faturamento]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
								$texto=htmlMontaOpcao("Faturamento Total por POP/Cliente", 'faturamento');
								itemTabela($texto, "?modulo=$modulo&sub=faturamento_pop", 'left', $corFundo, 0, 'normal');							
								
								#comentado aguardando correcao
								$texto=htmlMontaOpcao("Faturamento por Grupos de Serviços", 'faturamento');
								itemTabela($texto, "?modulo=$modulo&sub=gruposervico", 'left', $corFundo, 0, 'normal');				
								
								$texto=htmlMontaOpcao("Baixas por POP/Cliente", 'financeiro');
								itemTabela($texto, "?modulo=$modulo&sub=baixa_pop", 'left', $corFundo, 0, 'normal');							
								
								$texto=htmlMontaOpcao("Baixas por Serviço", 'financeiro');
								itemTabela($texto, "?modulo=$modulo&sub=baixa_servico", 'left', $corFundo, 0, 'normal');
								
								$texto=htmlMontaOpcao("Baixas por Grupos de Serviços", 'financeiro');
								itemTabela($texto, "?modulo=$modulo&sub=baixagruposervico", 'left', $corFundo, 0, 'normal');
								
								#comentado aguardando por correcao
								$texto=htmlMontaOpcao("Inadimplentes por Grupos de Serviços", 'devedores');
								itemTabela($texto, "?modulo=$modulo&sub=inadimplentesgruposervico", 'left', $corFundo, 0, 'normal');
								
								$texto=htmlMontaOpcao("Titulos em Aberto", 'devedores');
								itemTabela($texto, "?modulo=$modulo&sub=titulosemaberto", 'left', $corFundo, 0, 'normal');				
								
								$texto=htmlMontaOpcao("Inadimplentes", 'devedores');
								itemTabela($texto, "?modulo=$modulo&sub=inadimplentes", 'left', $corFundo, 0, 'normal');
								
								$texto=htmlMontaOpcao("Inadimplentes Recebidos", 'devedores');
								itemTabela($texto, "?modulo=$modulo&sub=inadimplentes_recebidos", 'left', $corFundo, 0, 'normal'); 
								
								$texto=htmlMontaOpcao("Serviços Adicionais", 'servicos');
								itemTabela($texto, "?modulo=$modulo&sub=servicosadicionais", 'left', $corFundo, 0, 'normal');
								
								$texto=htmlMontaOpcao("Clientes por Valor", 'pessoa');
								itemTabela($texto, "?modulo=$modulo&sub=clientesvalor", 'left', $corFundo, 0, 'normal');
							
								$texto=htmlMontaOpcao("Clientes por Valor Nominal", 'pessoa');
								itemTabela($texto, "?modulo=$modulo&sub=clientesvalornominal", 'left', $corFundo, 0, 'normal');
																
							fechaTabela();
						htmlFechaColuna();
					fechaLinhaTabela();
				fechaTabela();
			htmlFechaColuna();
			fechaLinhaTabela();
		}
		elseif($sub=='gruposervico') {
			# Pedir dados para geração de faturamento
			echo "<br>";
			
			formRelatorioFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz);
			
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				$matriz[consulta]=1;
				consultaFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz);
			}else if($matriz[bntRelatorio]) {
				# Prosseguir com consulta
				$matriz[consulta]=0;
				consultaFaturamentoGrupo($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='baixagruposervico') {
			# Pedir dados para geração de faturamento
			echo "<br>";
			
			formRelatorioBaixaGrupoServico($modulo, $sub, $acao, $registro, $matriz);
			
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				$matriz[consulta]=1;
				relatorioBaixaGrupoServico($modulo, $sub, $acao, $registro, $matriz);
			}else if($matriz[bntRelatorio]) {
				# Prosseguir com consulta
				$matriz[consulta]=0;
				relatorioBaixaGrupoServico($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='baixa_servico') {
			echo "<br>";
			formRelatorioBaixaServico($modulo, $sub, $acao, $registro, $matriz);
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				$matriz[consulta]=1;
				relatorioBaixaServico($modulo, $sub, $acao, $registro, $matriz);
			}else if($matriz[bntRelatorio]) {
				# Prosseguir com consulta
				$matriz[consulta]=0;
				relatorioBaixaServico($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='inadimplentesgruposervico') {
			# Pedir dados para geração de faturamento
			echo "<br>";
			
			formRelatorioInadimplenteGrupoServico($modulo, $sub, $acao, $registro, $matriz);
			
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				$matriz[consulta]=1;
				relatorioInadimplenteGrupoServico($modulo, $sub, $acao, $registro, $matriz);
			}else if($matriz[bntRelatorio]) {
				# Prosseguir com consulta
				$matriz[consulta]=0;
				relatorioInadimplenteGrupoServico($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='titulosemaberto') {
	
			echo "<br>";
			
			formRelatorioTitulosAberto($modulo, $sub, $acao, $registro, $matriz);
			
			if($matriz[bntConfirmar] && $matriz[pop]) {
				# Prosseguir com consulta
				consultaTitulosAberto($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($matriz[bntRelatorio] && ($matriz[pop] || $matriz[pop_todos]))  {
				# Enviar dados para geração de relatorio
				relatorioTitulosAberto($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='inadimplentes') {
	
			echo "<br>";
			
			formRelatorioInadimplentes($modulo, $sub, $acao, $registro, $matriz);
			
			if($matriz['bntConfirmar']) {
				# Prosseguir com consulta
				consultaInadimplentes($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($matriz['bntRelatorioPDF'])  {
				# Enviar dados para geração de relatorio
				relatorioInadimplentes($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($matriz['bntRelatorioCSV']){
				InadimplentesRelatorioCSV( $matriz );				
			}
		}
		elseif ( $sub == 'inadimplentes_recebidos' ) {
			echo '<br>';

			formInadimplentesRecebidos( $modulo, $sub, $acao, $registro, $matriz );
			
			if ( $matriz['bntConfirmar'] || $matriz['bntRelatorioPDF'] ) {
				$rel = InadimplentesRecebidosPreparaRel( $matriz );
				if( $matriz['bntConfirmar'] ) {
						InadimplentesRecebidosExibir( $rel, $matriz );
				}
				else{
					InadimplentesRecebidosRelatorioPDF( $rel, $matriz );
				}
			}
			elseif( $matriz['bntRelatorioCSV'] ){
				InadimplentesRecebidosRelatorioCSV( $matriz );
			}

		}				
		elseif($sub=='faturamento_pop') {
			echo "<br>";
			
			formFaturamentoPOP($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				if($matriz[detalhar] == 'S') 
					consultaFaturamentoClientesPOP($modulo, $sub, $acao, $registro, $matriz);
				else 
					if ($matriz[baixa]=='baixa')
						relatorioFaturamentoPOPBaixa($modulo, $sub, $acao, $registro, $matriz);
					else
						consultaFaturamentoPOP($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($matriz[bntRelatorio]) {
				# Processar relatório
				if($matriz[detalhar] == 'S') 
					relatorioFaturamentoClientesPOP($modulo, $sub, $acao, $registro, $matriz);
				else 
					consultaFaturamentoPOP($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='ordemservico') {
			echo "<br>";
			relatorioOrdemServico($modulo, $sub, $acao, $regitro, $matriz);

		}
		elseif($sub=='cliente_pop') {
			echo "<br>";
			formRelatorioClientesPOP($modulo, $sub, $acao, $regitro, $matriz);
			if($matriz[bntRelatorio] && ($matriz[pop] || $matriz[pop_todos]) ) {
				consultaClientesPOP($modulo, $sub, $acao, $registro, $matriz);
			}

		}
		elseif($sub=='baixa_pop') {
			echo "<br>";
			
			formBaixaPOP($modulo, $sub, $acao, $registro, $matriz);
			
			# relizar consulta
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				if($matriz[detalhar] == 'S') 
					relatorioBaixaClientesPOP($modulo, $sub, $acao, $registro, $matriz);
				else 
					relatorioFaturamentoPOPBaixa($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		elseif($sub=='servicosadicionais') {
			echo "<br>";
			formServicoAdicional($modulo, $sub, $acao, $regitro, $matriz);
			if($matriz[bntRelatorio] || $matriz[bntConfirmar]) {
				relatorioServicoAdicional($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		elseif($sub=='clientesvalor') {
			echo "<br>";
			formRelatorioClienteValor($modulo, $sub, $acao, $registro, $matriz);
			if($matriz['bntConfirmar']) {
				$matriz['consulta'] = 1;
				relatorioClienteValor($modulo, $sub, $acao, $registro, $matriz);
			}
			else if($matriz['bntRelatorio']) {
				$matriz['consulta'] = 0;
				relatorioClienteValor($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		elseif($sub=='clientesvalornominal') {
			echo "<br>";
			formRelatorioClienteValorNominal($modulo, $sub, $acao, $registro, $matriz);
			if($matriz[bntConfirmar]) {
				$matriz[consulta]=1;
				relatorioClienteValorNominal($modulo, $sub, $acao, $registro, $matriz);
			}else if($matriz[bntRelatorio]) {
				$matriz[consulta]=0;
				relatorioClienteValorNominal($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		elseif($sub=='clientes') {
			echo "<br>";
			if ($matriz[incluirFiltro])
				$matriz = filtroAdicionar($matriz);
			
			if ($matriz[excluirFiltro])
				$matriz = filtroExcluir($matriz);
				
			formRelatorioClientes($modulo, $sub, $acao, $regitro, $matriz);
			if($matriz[bntRelatorio] || $matriz[bntConfirmar]) {
				relatorioClientes($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		elseif($sub=='clientesgruposervico') {
			echo "<br>";
			formRelatorioClientesGruposServicos($modulo, $sub, $acao, $regitro, $matriz);
			if($matriz[bntRelatorio] || $matriz[bntConfirmar]) {
				relatorioClientesGruposServicos($modulo, $sub, $acao, $registro, $matriz);
			}

		}
		
		elseif ( $sub== 'clienteservico'){
			echo "<br>";
			formRelatorioClienteServico($modulo, $sub, $acao, $registro, $matriz);
			if ( $matriz[bntRelatorio] || $matriz[bntConfirmar] ){
				relatorioClienteServico($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif ( $sub== 'cliente_plano_especial'){
			echo "<br>";
			formRelatorioClientePlanoEspecial($modulo, $sub, $acao, $registro, $matriz);
			if ( $matriz[bntRelatorio] || $matriz[bntConfirmar] ){
				relatorioClientePlanoEspecial($modulo, $sub, $acao, $registro, $matriz);
			}
		}		
		elseif ( $sub== 'ocorrencias'){
			echo "<br>";	
			formRelatorioOcorrencias($modulo, $sub, $acao, $registro, $matriz);
			if ( $matriz[bntRelatorio] || $matriz[bntConfirmar] ){
				relatorioOcorrencias($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif( $sub == 'email_cliente'){
			echo "<br>";
			formRelatorioEmailCliente($modulo, $sub, $acao, $registro, $matriz);
			if($matriz[bntConfirmar]) {
				# Prosseguir com consulta
				$matriz[consulta]=1;
				relatorioEmailCLiente($modulo, $sub, $acao, $registro, $matriz);
			}
			else{
				$matriz[consulta]=0;
				relatorioEmailCLiente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif ($sub == 'cliente_quantidade_servico' ){
			echo "<br>";	
			formRelatorioClienteQuantidadeServico($modulo, $sub, $acao, $registro, $matriz);
			if ( $matriz[bntRelatorio] || $matriz[bntConfirmar] ){
				relatorioClienteQuantidadeServico($modulo, $sub, $acao, $registro, $matriz);
			}			
		}
		elseif( $sub == 'nota_fiscal'){
			echo '<br>';
			formRelatorioNotaFiscal($modulo, $sub, $acao, $registro, $matriz);
			if ($matriz[bntConfirmar] || $matriz[bntRelatorio]  )
				relatorioNotaFiscal($modulo, $sub, $acao, $registro, $matriz);
		}
		
		elseif( $sub == 'contas_pagar_plano_contas'){
			echo '<br>';
			
			if( !$permissao['admin'] ) {
				# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
				$msg="ATENÇÃO: Você não tem permissão para executar esta função";
				$url="?modulo=$modulo";
				aviso("Acesso Negado", $msg, $url, 760);		
			}
			else {
				formContasAPagarPlanoDeContas( $modulo, $sub, $acao, $registro, $matriz );
				
				if ( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ){
					echo "<br>";
					$rel = contasAPagarPlanoDeContasPreparaRel( $matriz );
					
					if( $matriz['bntConfirmar'] ) contasAPagarPlanoDeContasExibr( $rel );
					else contasAPagarPlanoDeContasGeraPdf( $rel );
		
				}
			}
			
		}
		
		elseif ( $sub == 'contas_pagar_fornecedor' ) {
			echo '<br>';
			if( !$permissao['admin'] ) {
				# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
				$msg="ATENÇÃO: Você não tem permissão para executar esta função";
				$url="?modulo=$modulo";
				aviso("Acesso Negado", $msg, $url, 760);		
			}
			else {
				formContasAPagarFornecedor( $modulo, $sub, $acao, $registro, $matriz );
				
				if ( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) {
					$rel = contasAPagarFornecedorPreparaRel( $matriz );
					
					if( $matriz['bntConfirmar'] ) {
						contasAPagarFornecedorExibr( $rel, $matriz );
					}
					else {
						contasAPagarFornecedorRelatorio( $rel, $matriz );
					}
					
				}
			}
		}
		# Contas a pagar por centro de custo
		elseif ( $sub == 'contas_pagar_centro_custo' ) {
			echo '<br>';
			if( !$permissao['admin'] ) {
				# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
				$msg="ATENÇÃO: Você não tem permissão para executar esta função";
				$url="?modulo=$modulo";
				aviso("Acesso Negado", $msg, $url, 760);		
			}
			else {
				formContasAPagarCentroDeCusto( $modulo, $sub, $acao, $registro, $matriz );
				
				if ( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) {
	
					if( $rel = ContasAPagarCentroDeCustoPreparaRel( $matriz ) ) {
				
						if( $matriz['bntConfirmar'] ) {
							contasAPagarCentroDeCustoExibir( $rel );
						}
						else {
							contasAPagarCentroDeCustoRelatorio( $rel );
						}
	
					}
					
				}
			}
		}
		elseif ( $sub == 'ordemServicoCliente' ) {
			echo '<br>';
			if( !$permissao['admin'] ) {
				# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
				$msg="ATENÇÃO: Você não tem permissão para executar esta função";
				$url="?modulo=$modulo";
				aviso("Acesso Negado", $msg, $url, 760);		
			}
			else {
				formOrdemServico( $modulo, $sub, $acao, $registro, $matriz );
				
				if ( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) {
					$rel = OrdemServicoPreparaRel( $matriz );
					if( $matriz['bntConfirmar'] ) {
 						OrdemServicoExibir( $rel, $matriz );
					}
					else {
						OrdemServicoRelatorio( $rel, $matriz );
					}
					
				}
			}
		}
		elseif ( $sub == 'retornoProdutos' ) {
			echo '<br>';
			if( !$permissao['admin'] ) {
				# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
				$msg="ATENÇÃO: Você não tem permissão para executar esta função";
				$url="?modulo=$modulo";
				aviso("Acesso Negado", $msg, $url, 760);		
			}
			else {
				formRetornoProdutos( $modulo, $sub, $acao, $registro, $matriz );
				if ( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) {
					$rel = RetornoProdutosPreparaRel( $matriz );
					if( $matriz['bntConfirmar'] ) {
 						RetornoProdutosExibir( $rel, $matriz );
					}
					else {
						RetornoProdutosRelatorio( $rel, $matriz );
					}
					
				}
			}
		}
		elseif ( $sub == 'produtosInventario' ) {
			echo '<br>';
			if( !$permissao['admin'] ) {
				# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
				$msg="ATENÇÃO: Você não tem permissão para executar esta função";
				$url="?modulo=$modulo";
				aviso("Acesso Negado", $msg, $url, 760);		
			}
			else {
				formProdutosInventario( $modulo, $sub, $acao, $registro, $matriz );
				
				if ( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) {
					$rel = ProdutosInventarioPreparaRel( $matriz );
					if( $matriz['bntConfirmar'] ) {
 						ProdutosInventarioExibir( $rel, $matriz );
					}
					else {
						ProdutosInventarioRelatorio( $rel, $matriz );
					}
					
				}
			}
		}
		elseif ( $sub == 'saidaProdutos' ) {
			echo '<br>';
			if( !$permissao['admin'] ) {
				# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
				$msg="ATENÇÃO: Você não tem permissão para executar esta função";
				$url="?modulo=$modulo";
				aviso("Acesso Negado", $msg, $url, 760);		
			}
			else {
				formSaidaProdutos( $modulo, $sub, $acao, $registro, $matriz );
				
				if ( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) {
					$rel = SaidaProdutosPreparaRel( $matriz );
					if( $matriz['bntConfirmar'] ) {
 						SaidaProdutosExibir( $rel, $matriz );
					}
					else {
						SaidaProdutosRelatorio( $rel, $matriz );
					}
					
				}
			}
		}
	}
}
?>