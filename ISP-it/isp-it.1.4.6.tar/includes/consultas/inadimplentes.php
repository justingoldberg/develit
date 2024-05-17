<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Includes de Relatórios


/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para filtros de Faturamento por POP
*/
function formRelatorioInadimplentes($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
		
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		$data=dataSistema();
		
		# Motrar tabela de busca
		novaTabela2("[Consulta de Inadimplentes]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>POP:</b><br>
				<span class=normal10>Selecione o POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[pop_todos]) $opcPOP='checked';
				$texto="<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
				itemLinhaForm(formSelectPOP($matriz[pop],'pop','multi').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano Final:</b><br>
				<span class=normal10>Informe o mês/ano final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtFinal] size=7 value='$matriz[dtFinal]'  onBlur=verificaDataMesAno2(this.value,6)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			//foi requisitado a opcao de relatar somente e EXPECIFICAMENTE os inadimplentes com dias em atraso PREDEFINIDOS, para cancelamento. gustavo 20050304
			novaLinhaTabela($corFundo, '100%');
				$texto = "Inadimplentes:";
				( $matriz[diasAtraso] == 'todos' ? $check = "CHECKED" : $check = "" );
				itemLinhaTMNOURL($texto,  'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto = "<input id=todos type=radio name=matriz[diasAtraso] value=todos $check>" .
							"<label for=todos>Todos</label>" ;
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				( $matriz[diasAtraso] == 'quarenta' ? $check = "CHECKED" : $check = "" );
				itemLinhaTMNOURL("&nbsp;",  'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto =  "<input id=quarenta type=radio name=matriz[diasAtraso] value=quarenta $check >" .
							 "<label for=quarenta> mais de 45 dias de atraso</label>" ;
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				( $matriz[diasAtraso] == 'vinte' ? $check = "CHECKED" : $check = "" );
				itemLinhaTMNOURL("&nbsp;",  'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto = "<input id=vinte type=radio name=matriz[diasAtraso] value=vinte $check >" .
						 	"<label for=vinte> mais de 20 dias e menos de 45 dias atraso</label>" ;
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			//FIM da adaptacao
		
				
			getBotoesConsRelPDF_CSV();		
		fechaTabela();
	}
	
}


function consultaInadimplentes($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;
	
	# Formatar Datas
	$data=dataSistema();
	$matriz[dtFinal]=formatarData($matriz[dtFinal]);
	if(substr($matriz[dtFinal],0,2) == $data[mes] && substr($matriz[dtFinal],2,4) == $data[ano] ) {
		$dtFinal=substr($matriz[dtFinal],2,4)."-".substr($matriz[dtFinal],0,2).'-'.$data[dia];
	}
	else {
		$dtFinal=substr($matriz[dtFinal],2,4)."-".substr($matriz[dtFinal],0,2).'-'.dataDiasMes(mktime(0,0,0,substr($matriz[dtFinal],0,2),01,substr($matriz[dtFinal],2,4)));
	}
	
	# Montar 
	if(!$matriz[pop_todos] && $matriz[pop]) {
		$i=0;
		$sqlADDPOP="$tb[POP].id in (";
		while($matriz[pop][$i]) {
			
			$sqlADDPOP.="'".$matriz[pop][$i]."'";
			
			if($matriz[pop][$i+1]) $sqlADDPOP.=",";
			$i++;
		}
		$sqlADDPOP.=")";
		
		$consultaPOP=buscaPOP($sqlADDPOP, '','custom','nome');
		
	}
	else{
		# consultar todos os POP
		$consultaPOP=buscaPOP('','','todos','nome');
	}


	# Consultar POPs
	if( $matriz['pop_todos'] || $matriz['pop'] ){

		echo "<br>";
		$dtFinalTitulo=converteData($dtFinal,'banco','formdata');
		novaTabela("[Inadimplentes até $dtFinalTitulo]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

		if($consultaPOP && contaConsulta($consultaPOP)>0) {
			
			for($x=0;$x<contaConsulta($consultaPOP);$x++) {
			
				$idPOP=resultadoSQL($consultaPOP, $x, 'id');
				$nomePOP=resultadoSQL($consultaPOP, $x, 'nome');
				$totalGeral=array();
				$matTotal=array();
				
				
				# Cabeçalho
				itemTabelaNOURL("POP: $nomePOP", 'center', $corFundo, '2', 'tabfundo0');
				
				# SQL para consulta de emails por dominios do cliente informado
				# Consultas de Pendentes
				$sql="
					SELECT 
						$tb[PessoasTipos].id idPessoaTipo, 
						$tb[Pessoas].nome nomePessoa, 
						$tb[ContasReceber].dtVencimento dtVencimento,
						SUM($tb[ContasReceber].valor) valor, 
						SUM($tb[ContasReceber].valorRecebido) recebido, 
						SUM($tb[ContasReceber].valorJuros) juros, 
						SUM($tb[ContasReceber].valorDesconto) desconto, 
						$tb[ContasReceber].status status 
					FROM 
						$tb[POP], 
						$tb[Pessoas], 
						$tb[PessoasTipos], 
						$tb[ContasReceber], 
						$tb[DocumentosGerados] 
					WHERE 
						$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
						AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
						AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
						AND $tb[Pessoas].idPOP = $tb[POP].id 
						AND $tb[ContasReceber].status = 'P'
						AND $tb[POP].id='$idPOP'" ;
					
				//adicionado para poder filtrar por quantidade de dias em atraso para eventos subjacentes. gustavo 20050304
				if($matriz[diasAtraso] == "todos")
					$sql .= "AND $tb[ContasReceber].dtVencimento  < '$dtFinal' AND $tb[ContasReceber].dtVencimento > '0000-00-00'";
				elseif($matriz[diasAtraso] == "quarenta")
					$sql .= "AND DATE_ADD($tb[ContasReceber].dtVencimento, INTERVAL 45 DAY) < '".$dtFinal."'";
				elseif ($matriz[diasAtraso] == "vinte")
					$sql .= "AND $tb[ContasReceber].dtVencimento between DATE_SUB('".$dtFinal."', INTERVAL  45 DAY) AND DATE_SUB('".$dtFinal."', INTERVAL  20 DAY)";
				//Fim
								
				$sql .= "
						GROUP BY 
						$tb[PessoasTipos].id,
						$tb[ContasReceber].dtVencimento
					ORDER BY 
						$tb[Pessoas].nome,
						$tb[ContasReceber].dtVencimento				
				";
	
				$consultaPendentes=consultaSQL($sql, $conn);
				
				if($consultaPendentes && contaConsulta($consultaPendentes)>0) {
					# Totalizar
					$matPendente=array();
					for($i=0;$i<contaConsulta($consultaPendentes);$i++) {
						$idPessoaTipo=resultadoSQL($consultaPendentes, $i, 'idPessoaTipo');
						$nomePessoa=resultadoSQL($consultaPendentes, $i, 'nomePessoa');
						$valor=resultadoSQL($consultaPendentes, $i, 'valor');
						$dtVencimento=resultadoSQL($consultaPendentes, $i, 'dtVencimento');
	
						$matDetalhe["$idPessoaTipo"][nomePessoa] = $nomePessoa;
						$matPendente["$idPessoaTipo"]["$dtVencimento"] = $valor;
						$matTotal["$idPessoaTipo"]["$dtVencimento"] += $valor;
					}
					
				}
				
				if(is_array($matTotal)) {
					
					$keys=array_keys($matTotal);
					
					# Cabeçalho
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela('Nome do Cliente', 'center', '60%', 'tabfundo0');
						itemLinhaTabela('Detalhamento', 'center', '40%', 'tabfundo0');
					fechaLinhaTabela();
					
					for($a=0;$a<count($keys);$a++) {
						
						# Buscar Telefone
						$telefone=telefonesPessoasTipos($keys[$a]);
						$tmpIDPessoaTipo=$keys[$a];
						$nomePessoa=$matDetalhe[$tmpIDPessoaTipo][nomePessoa];
						$opcoes=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico_pendente&registro=0&matriz[idPessoaTipo]=$tmpIDPessoaTipo>$nomePessoa</a>",'procurar');
	
						$meses=array_keys($matTotal[$keys[$a]]);
						
						if(is_array($meses)) {
						
							$dtVencimento=$meses[$m];
							$totalPendente=$matPendente[$tmpIDPessoaTipo][$dtVencimento];
							
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL("$opcoes<br>Telefone: $telefone", 'left','top','60%',$corFundo, 0, 'normal10');
								htmlAbreColunaForm('40%','left','top',$corFundo, 0, 'normal10');
									$subTotal=0;
									
									novaTabelaSH('left','100%',0,2,0,$corFundo, $corBorda, 2);
									
									for($m=0;$m<count($meses);$m++) {
										
										$dtVencimento=$meses[$m];
										$totalPendente=$matPendente[$tmpIDPessoaTipo][$dtVencimento];
										$subTotal+=$totalPendente;
										
										novaLinhaTabela($corFundo, '100%');
											itemLinhaTabela(converteData($dtVencimento,'banco','formdata'), 'center', '50%', 'normal10');
											itemLinhaTabela(formatarValoresForm($totalPendente), 'right', '50%', 'normal10');
										fechaLinhaTabela();
			
										$totalGeral[pendente]+=$totalPendente;
									}
									
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('Total','right','middle','70%',$corFundo, 0, 'bold10');
										itemLinhaTabela(formatarValoresForm($subTotal), 'right', '10%', 'bold10');
									fechaLinhaTabela();						
									fechaTabela();
								htmlFechaColuna();
							fechaLinhaTabela();
							
						}
						
					} #fecha laco de montagem de tabela
					
					# Totalizar
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('Totais', 'right', 'middle', '70%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL(formatarValoresForm($totalGeral[pendente]), 'right', 'middle', '10%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					
					if($x+1<contaConsulta($consultaPOP)) itemTabelaNOURL('&nbsp;','left',$corFundo,2,'normal10');
					
				}
				else {
					# Não há registros
					itemTabelaNOURL('Não foram encontrados faturamentos neste período', 'left', $corFundo, 2, 'txtaviso');
				}
			}
		}
		else {
			# Não há registros
			itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 5, 'txtaviso');
		}
		
		fechaTabela();	
		
	}
	else{
		echo "<br>";
		$msg="Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}

}


?>
