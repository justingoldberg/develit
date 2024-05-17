<?
################################################################################
#       Criado por: Devel-IT
#  Data de criação: 2005/08/02
# Ultima alteração: 2005/08/02
#    Alteração No.: 001
#
# Função:
#      Listar os clientes baseando numa faixa de valores
#

# função para form de seleção de filtros de faturamento
function formRelatorioClienteValorNominal($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $retornaHtml, $sessLogin;
	
	$data=dataSistema();
	
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[visualizar] && !$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
		
		# Motrar tabela de busca
		novaTabela2("[Clientes por Valor Nominal]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			//POP
			getPop($matriz);
			
			//valores
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor Inicial:</b><br>
				<span class=normal10>Informe o menor valor para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[valorInicial] align=right size=7 value='$matriz[valorInicial]'>&nbsp;<span class=txtaviso>,00 (Formato: 999,00)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor Final:</b><br>
				<span class=normal10>Informe o maior valor para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[valorFinal] align=right size=7 value='$matriz[valorFinal]'>&nbsp;<span class=txtaviso>,00 (Formato: 999,00)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			// Botoes Consulta Relatorio
			getBotoesConsRel();
			
		fechaTabela();
//		
//		if($matriz[bntVisualizar] || $matriz[bntGerar]) {
//			
//			if($matriz[bntVisualizar]) {
//				$matriz[acao]='visualizar';
//				$retornaHtml=0;
//				echo "<br>";
//				consultaTitulosAberto($modulo, $sub, $acao, $registro, $matriz);
//			}
//			elseif($matriz[bntRelatorio]) {
//				# Geração de Relatorio
//				relatorioTitulosAberto($modulo, $sub, $acao, $registro, $matriz);
//			}
//			$retornaHtml=0;
//		}
	}
}


/**
 * @return unknown
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Enter description here...
*/
function relatorioClienteValorNominal($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml;

	$titulo = "Clientes por Valor Nominal";
	$data=dataSistema();
	
	
	// Faixa de valores
	if ($matriz[valorInicial]) $valorInicial=$matriz[valorInicial];
	else $valorInicial=0;
	
	if ($matriz[valorFinal]) $valorFinal=$matriz[valorFinal];
	else $valorFinal=999999999;
	
	// Se forem todos os pops gera a lista na matriz
	if($matriz[pop_todos]) {
		$consultaPop=buscaPOP('','','todos', 'id');
		if( $consultaPop && contaConsulta($consultaPop) ) {
			for($a=0;$a<contaConsulta($consultaPop);$a++) {
				$matriz[pop][]=resultadoSQL($consultaPop, $a, 'id');
			}
		}
	}
	
	$pp=0;	
	$totalGeral = 0;
	$largura=array(       '40%',     '30%',     '10%',    '15%');
	$matCabecalho=array(  "Cliente", "Telefone", "Valor", "Opções");
	$matAlinhamento=array("left",    "left",     "right", "left");
	$linhaRelatorio=0;

	if( $matriz['pop_todos'] || $matriz['pop'] ){
	
		# tabela principal
		echo "<br>";
		novaTabela("[$titulo]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
		htmlAbreLinha($corFundo);
		htmlAbreColuna('100%', 'center', $corFundo, 0, 'normal9');
		
		while($matriz[pop][$pp]) {
			
			$sqlPOP=" AND $tb[Pessoas].idPOP = ".$matriz[pop][$pp];
			
			$sql="SELECT 	Pessoas.idPOP pop, 
							trim(Pessoas.nome) cliente, 
							concat(Enderecos.fone1, ' / ', Enderecos.fone2) fone, 
							PessoasTipos.id idPessoaTipo 
				FROM 		PessoasTipos, 
							Pessoas, 
							Enderecos 
				WHERE 		PessoasTipos.idPessoa=Pessoas.id 
							AND Enderecos.idPessoaTipo=PessoasTipos.id 
							$sqlPOP
				GROUP BY	PessoasTipos.id 
				ORDER BY 	trim(Pessoas.nome)";
			
			#echo $sql;
			
			if($sql) $consultaPop=consultaSQL($sql, $conn);
		
			if( $consultaPop && contaconsulta($consultaPop) > 0 ) {
				
				$formas = array('anual', 'mensal');
				
				foreach ($formas as $forma) {
		
				$pop=resultadoSQL($consultaPop, 0, "pop");
				$matResultado=array();
				
				if ($matriz[consulta]) {
					echo "<br>";
					$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
					novaTabela($nomePop, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
					$cor='tabfundo0';
					htmlAbreLinha($cor);
						for ($cc=0;$cc<count($matCabecalho);$cc++) 
							itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, $cor);					
					htmlFechaLinha();
				}
				
				$anterior=resultadoSQL($consultaPop, 0, 'cliente');
				$valor=0;
				
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
				
					$idPessoaTipo=resultadoSQL($consultaPop, $a, 'idPessoaTipo');
					$cliente=resultadoSQL($consultaPop, $a, 'cliente');
					
					$consultaPlano=buscaPlanos("idPessoaTipo=$idPessoaTipo AND (status='A' OR status='I' OR status='N' OR status='T')", '','custom','dtCadastro');
					
					#soma o valor dos planos
					$valor=0;	
					for($i=0;$i<contaConsulta($consultaPlano);$i++) {
						$id=resultadoSQL($consultaPlano, $i, 'id');
						$idVencimento=resultadoSQL($consultaPlano, $i, 'idVencimento');
						$dtCadastro=resultadoSQL($consultaPlano, $i, 'dtCadastro');
						$especial=resultadoSQL($consultaPlano, $i, 'especial');
						
						$valor+=valorPlanoGrosso($id, $especial, $idVencimento, '', $data["mes"], $data["ano"], $parametro, $forma);
					}
					
					if ($valor>=$valorInicial && $valor<=$valorFinal) {
						$opcoes=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$idPessoaTipo target='_blank'>Planos</a>",'planos');
						
						$c=0;
						$campos[$c++]=$cliente;
						$campos[$c++]=resultadoSQL($consultaPop, $a, 'fone');
						$campos[$c++]=formatarValoresForm($valor);
						$campos[$c++]=$opcoes;
							
						# alteracao de 10/02/2005
						$vlTotal += $valor;
						$totCliente++;
						#exibe a linha detalhe
						if ($matriz[consulta]) {
							if ($matriz[consulta]) {
								htmlAbreLinha($corFundo);
									for ($cc=0; $cc<count($campos); $cc++) {
										itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $lagura[$cc], $corFundo, 0, "normal9");
									}
								htmlFechaLinha();
							}
						}
						
						$c=0;
						$matResultado[$matCabecalho[$c]][$linhaRelatorio]=$campos[$c++];
						$matResultado[$matCabecalho[$c]][$linhaRelatorio]=$campos[$c++];
						$matResultado[$matCabecalho[$c]][$linhaRelatorio++]=$campos[$c++];
					}
					
				} #fecha laco de montagem de tabela
				
				# alteracao de 10/02/2005
				# monta linha de total
				if ( !$matriz[bntRelatorio] ){
					$i = 0;
					htmlAbreLinha($corFundo);
					itemLinhaTMNOURL( "<b>Total de Clientes:&nbsp;&nbsp;</b>".$totCliente, 'left', 'middle', $lagura[$i++], $corFundo, 0, 'tabfundo0' );
					itemLinhaTMNOURL( "<b>Valor Total:</b>", 'right', 'middle', $lagura[$i++], $corFundo, 0, 'tabfundo0' );
					itemLinhaTMNOURL( number_format( $vlTotal, 2,',','.' ), 'right', 'middle', $largura[$i++], $corFundo, 0, 'tabfundo0' );
					itemLinhaTMNOURL( "&nbsp;", 'right', 'middle', $largura[$i++], $corFundo, 0, 'tabfundo0' );
					htmlFechaLinha();
					fechaTabela();
				}
				$i = 0;
				$matResultado[$matCabecalho[$i++]][$linhaRelatorio] = "<b>Total de Clientes:&nbsp;&nbsp;".$totCliente."</b>";
				$matResultado[$matCabecalho[$i++]][$linhaRelatorio] = "<b>Valor Total:</b>";
				$matResultado[$matCabecalho[$i++]][$linhaRelatorio] = "<b>".number_format( $vlTotal, 2, ',', '.' )."</b>";
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="RELATÓRIO DE $titulo<br>";
				$matrizRelatorio[header][POP]=$pop;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Alimentar Matriz de configurações
				$matrizRelatorio[config][linhas]=40;
				$matrizRelatorio[config][layout]='portrait';
				
				$matrizGrupo[]=$matrizRelatorio;
				}	

			} //sql
			
			$pp++;
		}
		
		#fecha a tabela principal
		htmlFechaColuna();
		htmlFechaLinha();
		fechaTabela();	
		
		if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
			if (! $matriz[consulta]) {
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','inadip'),'inadip', $matrizRelatorio[config]);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Clientes por Valor Nominal</a>",'pdf'), 'center', $corFundo, 3, 'txtaviso');
			}
		}
		else {
			# Não há registros
			itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');	
		}
	


	}
	else {
		echo "<br>";
		$msg="Você esqueceu de selecionar o POP.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}
	
	return(0);
	
}
?>