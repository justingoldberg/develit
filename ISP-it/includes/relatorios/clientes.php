<?
################################################################################
#       Criado por: Hugo Ribeiro - Devel-it
#  Data de criação: 12/08/2004
# Ultima alteração: 12/08/2004
#    Alteração No.: 001
#
# Função:
# 		Funções para relatórios personalizado de clientes
#

function formRelatorioClientes($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	$data=dataSistema();
	
	# Motrar tabela de busca
	novaTabela2("[Clientes - Relatório Customizado]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="&nbsp;			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>";
		if (is_array($matriz[filtrocampos])){
			$chaves = array_keys($matriz[filtrocampos]);
			foreach ($chaves as $chave){
				$texto .="
					<input type=hidden name=matriz[filtrocampos][$chave] value='".$matriz[filtrocampos][$chave]."'> 
					<input type=hidden name=matriz[filtrocondicao][$chave] value='".$matriz[filtrocondicao][$chave]."'> 
					<input type=hidden name=matriz[filtrovalor][$chave] value='". $matriz[filtrovalor][$chave]."'> 
				";
			}
		}
//		for ($i=0; $i<count($matriz[filtrocampos]); $i++)
//			$texto .="
//				<input type=hidden name=matriz[filtrocampos][$i] value=".$matriz[filtrocampos][$i]."> 
//				<input type=hidden name=matriz[filtrocondicao][$i] value=".$matriz[filtrocondicao][$i]."> 
//				<input type=hidden name=matriz[filtrovalor][$i] value=". $matriz[filtrovalor][$i]."> 
//			";

			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		//POP
		getPop($matriz);
		// Periodo
		getComboCamposClientes();
		// filtro
		filtroClientes();
		
		filtroClientesListar($matriz);
		
		//Botao
		getBotoesConsRel();
		
		htmlFechaLinha();
	fechaTabela();
	
}

function filtroAdicionar($matriz){

	$matriz[filtrocampos][]		= $matriz[_filtrocampos];
	$matriz[filtrocondicao][]	= $matriz[_filtrocondicao];
	$matriz[filtrovalor][]		= $matriz[_filtrovalor];
	
	return($matriz);
}

function filtroExcluir($matriz){

	$itens = array_keys($matriz[excluirFiltro]);
	foreach ($itens as $delItem){
		unset ($matriz[filtrocampos][$delItem]);
		unset ($matriz[filtrocondicao][$delItem]);
		unset ($matriz[filtrovalor][$delItem]);
	}
	return($matriz);
}

function filtroClientes() {
	
	global $corBorda;


	$campos = Array('Pessoas.tipoPessoa=Tipo_Pessoa');
	$campos = array_merge($campos, getCamposClientes());

	$select = getSelectNovo($campos, 'matriz[_filtrocampos]', 0, 0);


	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Filtros:</b><br>
		<span class=normal10>Itens para filtragem da consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		# monta  uma tabela
		$texto ="<table width='100%' border=0 cellspacing=2 bgcolor=$corBorda";
		$texto.="<tr class=tabfundo0><td>Campo</td><td>Condição</td><td>Valor</td><td>opção</td></tr>";
		$texto.="<tr class=tabfundo2><td>".$select."</td>";
		$texto.="<td>".getComboCondicao('matriz[_filtrocondicao]')."</td>";
		$texto.="<td><input text name=matriz[_filtrovalor]></td>";
		$texto.="<td align=center><input type=submit name=matriz[incluirFiltro] value='Incluir'></td></tr>";
		$texto.="</table>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
}

function filtroClientesListar ($matriz) {
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Filtros:</b><br>
		<span class=normal10>itens adicionados:</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		# monta  uma tabela
		$texto ="<table width='100%' border=0 cellspacing=2 bgcolor=$corBorda";
		$texto.="<tr class=tabfundo0><td>Campo</td><td>Condição</td><td>Valor</td><td>opção</td></tr>";
		
		if (is_array($matriz[filtrocampos])){
			$chaves = array_keys($matriz[filtrocampos]);
			foreach ($chaves as $chave){
				$texto.="<tr class=tabfundo2><td>".$matriz[filtrocampos][$chave]."</td>";
				$texto.="<td>".$matriz[filtrocondicao][$chave]."</td>";
				$texto.="<td>".$matriz[filtrovalor][$chave]."</td>";
				$texto.="<td align=center><input type=submit name=matriz[excluirFiltro][$chave] value='Excluir'></td></tr>";
			}
		}
		$texto.="</table>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
}
	
function filtroGerarSQL ($matriz) {
	
	if (is_array($matriz[filtrocampos])){
		$chaves = array_keys($matriz[filtrocampos]);
		
		foreach ($chaves as $chave)
			if ($matriz[filtrocondicao][$chave] == 'contem' ) 
				$sql .= ' AND '.$matriz[filtrocampos][$chave]." like '%".$matriz[filtrovalor][$chave]."%' " ;
			else
				$sql .= ' AND '.$matriz[filtrocampos][$chave].' '.$matriz[filtrocondicao][$chave]." '".$matriz[filtrovalor][$chave]."' " ;
	}
	
	return ($sql);
}

#
# faz a consulta e o relatorio
#
function relatorioClientes($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml, $sessLogin, $conn, $tb;
	
	if (is_array($matriz['pop'])|| $matriz['pop_todos'] ) {
			
		// Se forem todos os campos gera a lista na matriz
		$campos=($matriz['campos_todos'] ? $campos=getCamposClientes() : $campos=$matriz['campos']);
		
			
		// Se forem todos os pops gera a lista na matriz
		if($matriz['pop_todos']) {
			$consultaPop=buscaPOP('','','todos', 'id');
			if( $consultaPop && contaconsulta($consultaPop) ) {
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					$matriz['pop'][$a]=resultadoSQL($consultaPop, $a, 'id');
				}
			}
		}
		
		$sqlFiltro = filtroGerarSQL($matriz);
				
		$matriz['campos']="";
		$select=array();
		$largura=array();
		$matCabecalho=array();
		$matAlinhamento=array();
		$pos=0;
		
		if(count($campos) > 0) {
			foreach ($campos as $coluna) {
				$partes=explode("=", $coluna);
				$matriz[campos][]=stripslashes($partes[0])." ".$partes[1];
				$matCabecalho[]=$partes[1];
				if(!$pos++) $order=$partes[0];
			}

		
			// Prepara as variaveis de ajuste
			$pp=0;
			
	
			$select=implode(", ", $matriz['campos']);
			$numCol=count($matCabecalho);
			
			while($matriz['pop'][$pp]) {	
				// nome do pop para exbição
				$nomePop=resultadoSQL(buscaPOP($matriz['pop'][$pp], 'id', igual, 'nome'), 0, 'nome');
				$sqlPOP=" AND $tb[Pessoas].idPOP = ".$matriz['pop'][$pp];
				
				$sql="SELECT DISTINCT ".$select.
						" FROM 	Pessoas, 
								PessoasTipos, 
								TipoPessoa, 
								Enderecos, 
								Cidades 
						WHERE 	Pessoas.id=PessoasTipos.idPessoa 
								AND PessoasTipos.idTipo=TipoPessoa.id 
								AND TipoPessoa.valor='cli' 
								AND Enderecos.idPessoaTipo=PessoasTipos.id 
								AND Cidades.id=Enderecos.idCidade
								$sqlPOP 
								$sqlFiltro
						order by $order
					";
	
	//			$sql =  "SELECT DISTINCT ".$select.
	//					"FROM" .
	//					"	Pessoas," .
	//					"	INNER JOIN PessoasTipos," .
	//					"		On (Pessoas.id = PessoasTipos.idPessoa)".
	//					"	INNER JOIN TipoPessoa," .
	//					"		On (PessoasTipos.idTipo = TipoPessoa.id)" .
	//					"	INNER JOIN Enderecos" .
	//					"		On (PessoasTipos.id = Enderecos.idPessoaTipo)" .
	//					"	INNER JOIN Cidades" .
	//					"		On (Enderecos.idCidade = Cidade.id)".
	// 					$sqlPOP . 
	// 					$sqlFiltro . 
	// 					"order  by " . $order
				
				#echo "sql: $sql";
				
				$consultaPop=consultaSQL($sql, $conn);
				
				if( $consultaPop && contaconsulta($consultaPop) ) {
					# Cabeçalho
					echo "<br>";
					novaTabela($nomePop." ".$periodo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, $numCol);
					
					# se for consulta exibe o cabecalho
					if ($matriz[bntConfirmar]) {
						$cor='tabfundo0';
						htmlAbreLinha($cor);
							for ($cc=0;$cc<count($matCabecalho);$cc++) 
								itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, $cor);					
						htmlFechaLinha();
					}
					
					$matResultado=array();
					$l=0;
					
					#inicia a varredura e joga 
					for($a=0;$a<contaConsulta($consultaPop);$a++) {
						
						$cc=0;
						foreach ($matCabecalho as $mc)
							$campos[$cc++]=resultadoSQL($consultaPop, $a, $mc);
						
						# se for consulta exibe a linha detalhe
						if ($matriz[bntConfirmar]) {
							htmlAbreLinha($corFundo);
								for ($cc=0; $cc<count($campos); $cc++) {
									itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $lagura[$cc], $corFundo, 0, "normal9");
								}
							htmlFechaLinha();
						}
						# soma na matriz
						for ($cc=0; $cc<count($campos); $cc++) {
							$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
						}
						$l++;
					}
					fechaTabela();
				}
				else {
					$vazio = 1;
				}
				if (! $vazio) {
					# Alimentar Matriz Geral
					$matrizRelatorio[detalhe]=$matResultado;
					
					# Alimentar Matriz de Header
					$matrizRelatorio[header][TITULO]="Clientes - Customizado";
					$matrizRelatorio[header][POP]=$nomePop.'<br>'.$periodo;
					$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
					
					# Configurações
					$matrizRelatorio[config][linhas]=25;
					$matrizRelatorio[config][layout]='landscape';
					$matrizRelatorio[config][marginleft]='1.0cm;';
					$matrizRelatorio[config][marginright]='1.0cm;';
					
					$matrizGrupo[]=$matrizRelatorio;
				}
				$pp++;
				
				
				
			} // while
			
			if(! $vazio) {
				
				#Se for escolhido Consulta nao gera o pdf
				if (! $matriz[bntConfirmar]) {
					#nome do arquivo
					$nome="clientescustom";
					criaTemplates($nome, $matCabecalho);			
					# Converter para PDF:
					$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html',$nome),$nome,$matrizRelatorio[config]);
					if ($arquivo) {
						echo "<br>";
						novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
							htmlAbreLinha($corfundo);
								itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Clientes - Customizado</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
							htmlFechaLinha();
						fechaTabela();
					}
				}
			}
			return(0);
		}
		else {
			echo "<br>";
			$msg="É necessário selecionar algum campo.";
			avisoNOURL("Aviso: Relatório<a name=ancora></a>", $msg, 400);			
		}
	} else {
		echo "<br>";
		$msg="Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Relatório<a name=ancora></a>", $msg, 400);
	}	
	
	echo "<script>location.href='#ancora';</script>";
}


?>

