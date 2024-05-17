<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Consulta de Clientes por POP


function consultaClientesPOP($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites;
	
	# Formatar Datas
	
	if($matriz[dtInicial] && $matriz[dtFinal]) {
		$dtInicial=formatarData($matriz[dtInicial]);
		$dtInicial=substr($dtInicial,2,4)."/".substr($dtInicial,0,2).'/01 00:00:00';
		$dtFinal=formatarData($matriz[dtFinal]);
		$dtFinal=substr($dtFinal,2,4)."/".substr($dtFinal,0,2).'/31 00:00:00';
		
		$sqlDT=" AND $tb[Pessoas].dtCadastro between '$dtInicial' and '$dtFinal' ";
		$periodo="de ".$matriz[dtInicial]." até ".$matriz[dtFinal];
	} 
	elseif ($matriz[dtInicial]) {
		$dtInicial=formatarData($matriz[dtInicial]);
		$dtInicial=substr($dtInicial,2,4)."/".substr($dtInicial,0,2).'/01 00:00:00';
		$sqlDT=" AND $tb[Pessoas].dtCadastro  >= '$dtInicial' ";
			$periodo="a partir de ".$matriz[dtInicial];
	} 
	elseif ($matriz[dtFinal])  {
		$dtFinal=formatarData($matriz[dtFinal]);
		$dtFinal=substr($dtFinal,2,4)."/".substr($dtFinal,0,2).'/31 00:00:00';
		$sqlDT=" AND $tb[Pessoas].dtCadastro <= '$dtFinal' ";
			$periodo="até ".$matriz[dtFinal];
	}
	
	$matCabecalho=array("Nome do Cliente", "Tipo", "Data do Cadastro");
	
	# Consultar POPs
	if(!$matriz[pop_todos] && $matriz[pop]) {
		
		$sqlADDPOP='';
		$i=0;
		while($matriz[pop][$i]) {
			$sqlADDPOP.=$matriz[pop][$i];
			
			if($i+1 < count($matriz[pop])) $sqlADDPOP.=",";
			
			$i++;
		}
		
		$sqlPOP="
			SELECT
				$tb[POP].id,
				$tb[POP].nome
			FROM
				$tb[POP]
			WHERE 
				$tb[POP].id IN ($sqlADDPOP)
			ORDER BY
				$tb[POP].nome
		";
		
		$consultaPOP=consultaSQL($sqlPOP, $conn);
	}
	else {
		$consultaPOP=buscaPOP('','','todos','nome');
	}	
	
	# Cabeçalho
	itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
	# Mostrar Cliente
	htmlAbreLinha($corFundo);
		htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
			novaTabela("[Resultados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			
			if($consultaPOP && contaConsulta($consultaPOP)>0) {
				
				for($a=0;$a<contaConsulta($consultaPOP);$a++) {
					
					$l=0;
					$matrizRelatorio=array();
					
					$idPOP=resultadoSQL($consultaPOP, $a, 'id');
					$nomePOP=resultadoSQL($consultaPOP, $a, 'nome');
		
					# SQL para consulta de emails por dominios do cliente informado
					$sql="
						SELECT
							$tb[POP].nome, 
							$tb[Pessoas].id idPessoa, 
							$tb[Pessoas].idPOP idPOP, 
							$tb[Pessoas].dtCadastro dtCadastro, 
							$tb[Pessoas].nome nomePessoa, 
							$tb[Pessoas].razao razaoSocial, 
							$tb[Pessoas].tipoPessoa tipoPessoa, 
							$tb[PessoasTipos].id idPessoaTipo
						FROM 
							$tb[Pessoas], 
							$tb[PessoasTipos], 
							$tb[POP],
							$tb[TipoPessoas]
						WHERE 
							$tb[PessoasTipos].idTipo = $tb[TipoPessoas].id
							AND $tb[TipoPessoas].valor='cli'
							AND $tb[POP].id = $tb[Pessoas].idPOP
							AND $tb[Pessoas].id = $tb[PessoasTipos].idPessoa
							AND $tb[POP].id = '$idPOP'
							$sqlDT
						ORDER BY
							$tb[Pessoas].nome
					";
					
					$consulta=consultaSQL($sql, $conn);
					
					# Cabecalho do POP
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL('POP: '.$nomePOP ." ". $periodo , 'center', 'middle', '100%', $corFundo, 4, 'tabfundo0');
					htmlFechaLinha();
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL('Nome do Clente', 'center', 'middle', '40%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Tipo', 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('DataCadastro', 'center nowrap ', 'middle', '15%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Opções', 'center', 'middle', '35%', $corFundo, 0, 'tabfundo0');
					htmlFechaLinha();
					
					$totalFisica=0;
					$totalJuridica=0;
					for($b=0;$b<contaConsulta($consulta);$b++) {
						
						$id=resultadoSQL($consulta, $b, 'idPessoa');
						$idPessoaTipo=resultadoSQL($consulta, $b, 'idPessoaTipo');
						$dtCadastro=resultadoSQL($consulta, $b, 'dtCadastro');
						$tipoPessoa=resultadoSQL($consulta, $b, 'tipoPessoa');
						$nomePessoa=resultadoSQL($consulta, $b, 'nomePessoa');
						$razaoSocial=resultadoSQL($consulta, $b, 'razaoSocial');
						
						if($tipoPessoa=='F') {
							$nome=$nomePessoa;
							$totalFisica++;
						} else {
							$nome=$razaoSocial;
							$totalJuridica++;
						}
						
						$opcoes=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=enderecos&registro=$idPessoaTipo:$id>Endereços</a>",'endereco');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$idPessoaTipo>Planos</a>",'planos');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$idPessoaTipo>Financeiro</a>",'financeiro');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=limites&registro=$idPessoaTipo>Administração</a>",'config');
						$opcoes.="<br>";
						$opcoes.=htmlMontaOpcao("<a href=?modulo=ocorrencias&sub&registro=$idPessoaTipo>Ocorrências</a>",'ocorrencia');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=ver&registro=$idPessoaTipo:$id>Cadastro</a>",'ver');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=documentos&registro=$idPessoaTipo:$id>Documentos</a>",'documento');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&acao=listar&registro=$idPessoaTipo:$id>Contratos</a>",'contrato');
						
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL($nome, 'left', 'middle', '40%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL(formSelectPessoaTipo($tipoPessoa,'','check'), 'center', 'middle', '10%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL(converteData($dtCadastro,'banco','formdata'), 'center', 'middle', '15%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($opcoes, 'left nowrap', 'middle', '35%', $corFundo, 0, 'normal8');
						htmlFechaLinha();
						
						$c=0;
						$matResultado[$matCabecalho[$c++]][$l]=$nome;
						$matResultado[$matCabecalho[$c++]][$l]=formSelectPessoaTipo($tipoPessoa,'','check');
						$matResultado[$matCabecalho[$c++]][$l]=converteData($dtCadastro,'banco','formdata');
						$l++;
						
					}
					
					# totalizar
					itemTabelaNOURL("<b>Total de Clientes:</b> $b - <B>Pessoa Jurídica:</b> $totalJuridica - <B>Pessoa Física:</B> $totalFisica", 'left', $corFundo, 4, 'normal10');
					
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]="<b>Total de Clientes:</b>";
					$matResultado[$matCabecalho[$c++]][$l]=$b;
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$l++;
					
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]="<B>Pessoa Jurídica:</b>";
					$matResultado[$matCabecalho[$c++]][$l]=$totalJuridica;
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$l++;
					
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]="<B>Pessoa Física:</B>";
					$matResultado[$matCabecalho[$c++]][$l]=$totalFisica;
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$l++;
					
					# saltar linha
					//if($a+1 < contaConsulta($consultaPOP)) itemTabelaNOURL('&nbsp;', 'left', $corFundo, 4, 'normal10');
					
					# Alimentar Matriz Geral
					$matrizRelatorio[detalhe]=$matResultado;
					
					# Alimentar Matriz de Header
					$matrizRelatorio[header][TITULO]="'POP: '.$nomePOP<br>".converteData($dtInicial,'banco','formdata');
					$matrizRelatorio[header][POP]=$nomePOP;
					$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
					
					# Configurações
					$matrizRelatorio[config][linhas]=30;
					$matrizRelatorio[config][layout]='portrait';
					
					$matrizGrupo[]=$matrizRelatorio;

				}

				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','clientes_pop'),'clientes_pop',$matrizRelatorio[config]);
				# coloca o botão
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Clientes por POP</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
			}
					
			fechaTabela();
			
		htmlFechaColuna();
	htmlFechaLinha();		
}


?>