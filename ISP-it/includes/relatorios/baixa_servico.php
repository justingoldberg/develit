<?
################################################################################
#       Criado por: Hugo Ribeiro - Devel-it
#  Data de criação: 14/07/2004
# Ultima alteração: 14/07/2004
#    Alteração No.: 001
#
# Função:
# Funções para relatórios


# função para form de seleção de filtros de faturamento
function formRelatorioBaixaServico($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		$data=dataSistema();
		
		# Motrar tabela de busca
		novaTabela2("[Baixas por Serviços]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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

			// Periodo
			getPeriodoDias(6,7,$matriz);
			//Botao
			getBotoesConsRel();
			
			htmlFechaLinha();
		fechaTabela();
	}
	
}



#
# faz a consulta e o relatorio
#
function relatorioBaixaServico($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml, $sessLogin, $conn, $tb;
	
	if (is_array($matriz[pop]) || $matriz[pop_todos]) {
	
		# Formatar Datas
		if ($matriz[dtInicial]) {
			$data=formatarData($matriz[dtInicial]);
			$dtInicial=substr($data,4,4)."/".substr($data,2,2).'/'.substr($data,0,2).' 00:00:00';
		}
	
		if ($matriz[dtFinal]) {
			$data=formatarData($matriz[dtFinal]);
			$dtFinal=substr($data,4,4)."/".substr($data,2,2).'/'.substr($data,0,2).' 23:59:59';
		}
	
		// Ajusta o sql para determinar o periodo escolhido
		$sqlDT="";
		if($matriz[dtInicial] && $matriz[dtFinal]) {
			$sqlDT=" AND $tb[ContasReceber].dtBaixa between '$dtInicial' and '$dtFinal' ";
			$periodo="de ".$matriz[dtInicial]." até ".$matriz[dtFinal];
		} 
		elseif ($matriz[dtInicial]) {
			$sqlDT=" AND $tb[ContasReceber].dtBaixa >= '$dtInicial' ";
			$periodo="a partir de ".$matriz[dtInicial];
		} 
		elseif ($matriz[dtFinal])  {
			$sqlDT=" AND $tb[ContasReceber].dtBaixa <= '$dtFinal' ";
			$periodo="até ".$matriz[dtFinal];
		}
				
		// Se forem todos os pops gera a lista na matriz
		if($matriz[pop_todos]) {
			$consultaPop=buscaPOP('','','todos', 'id');
			if( $consultaPop && contaconsulta($consultaPop) ) {
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					$matriz[pop][$a]=resultadoSQL($consultaPop, $a, 'id');
				}
			}
		}
		
		// Prepara as variaveis de ajuste
		$pp=0;
		$totalGeral=array();

		
		$largura=array(       '10%',    '25%',     '10%',            '20%',      '25%');
		$matCabecalho=array(  'Baixa',  "Cliente", "Valor Recebido", "Serviço",  "Obs");
		$matAlinhamento=array("center", "left",    "right",          "left",     "left");
		$numCol=count($matCabecalho);
		
		while($matriz[pop][$pp]) {	
			
			$total=array();
			$ttRecebido = 0;
			$vazio=0;
			
			// nome do pop para exbição
			$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			$sqlPOP=" AND $tb[Pessoas].idPOP = ".$matriz[pop][$pp];
			
			$sql="SELECT distinct 	ServicosPlanos.idPlano plano, 
									ContasAReceber.dtBaixa baixa, 
									Pessoas.nome cliente, 
									Servicos.nome servico, 
									ContasAReceber.obs obs,
									ContasAReceber.valor valorPlano,
									ContasAReceber.valorRecebido recebido,
									ServicosPlanosDocumentosGerados.valor valorServico,
									ServicosPlanosDocumentosGerados.id AS idServicosPlanosDocumentosGerados
							FROM    Pessoas, PessoasTipos,  
									DocumentosGerados, 
									ContasAReceber, 
									PlanosDocumentosGerados, 
									ServicosPlanosDocumentosGerados, 
									ServicosPlanos, 
									Servicos 
							WHERE   Pessoas.id = PessoasTipos.idPessoa 
									AND PessoasTipos.id=DocumentosGerados.idPessoaTipo 
									AND DocumentosGerados.id=ContasAReceber.idDocumentosGerados 
									AND PlanosDocumentosGerados.idDocumentoGerado=DocumentosGerados.id 
									AND ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado=PlanosDocumentosGerados.id 
									AND ServicosPlanos.id=ServicosPlanosDocumentosGerados.idServicosPlanos 
									AND Servicos.id=ServicosPlanos.idServico 
									AND ContasAReceber.status='B' 
									$sqlPOP 
									$sqlDT 
						 ORDER BY 	Pessoas.idPOP, 
									ContasAReceber.dtBaixa, 
									Pessoas.nome, 
									Servicos.nome";
					
			#echo "sql: $sql";
			
			$consultaPop=consultaSQL($sql, $conn);
			
			if( $consultaPop && contaconsulta($consultaPop) ) {
				# Cabeçalho
				echo "<br>";
				novaTabela($nomePop." ".$periodo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, $numCol);
				
				# se for consulta exibe o cabecalho
				if ($matriz[consulta]) {
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
					
					#$idPlano=resultadoSQL($consultaPop, $a, 'plano');
					#$plano=dadosPlanos($idPlano);
					$baixa=converteData(resultadoSQL($consultaPop, $a, 'baixa'), 'banco', 'form');
					$valorRecebido=resultadoSQL($consultaPop, $a, 'recebido');
					
					// gustavo 20050404 -> chama a funcao para calcular o servico, com juros, proporcionalmente.
					$valorPlano = resultadoSQL($consultaPop, $a, 'valorPlano');
					$valorServico  = resultadoSQL($consultaPop, $a, 'valorServico');
									 
					$recebido = calculaProporcional($valorRecebido, $valorPlano, $valorServico);
					
					$ttRecebido += $recebido;
					
					$cc=0;
					$campos[$cc++]=substr($baixa, 0, 10);
					$campos[$cc++]=substr(resultadoSQL($consultaPop, $a, 'cliente'), 0, 35);
					$campos[$cc++]=formatarValoresForm($recebido);
					$campos[$cc++]=resultadoSQL($consultaPop, $a, 'servico');
					$campos[$cc++]=resultadoSQL($consultaPop, $a, 'obs');
					
					# se for consulta exibe a linha detalhe
					if ($matriz[consulta]) {
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
			} 
			else {
				$vazio = 1;
			}
			if (! $vazio) {
				# se for consulta exibe o total
				if ($matriz[consulta]) {
					$zebra="tabfundo0";
					$cc=0;	
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL('<b>Total do POP</br>', 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 2, $zebra);
						$cc++;
						itemLinhaTMNOURL(formatarValoresForm($ttRecebido), 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, 'txtcheck');
						itemLinhaTMNOURL("&nbsp;", 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, $zebra);
						itemLinhaTMNOURL("&nbsp;", 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, $zebra);
					htmlFechaLinha();
					
				}
				
				$c=0;
				$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$l]="<b>Total do POP $nomePop</b>";
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($ttRecebido).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
				
				$l++;
				
				$totalGeral[tgRecebido]+=$ttRecebido;
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
			}
			$pp++;
		} // while
		
		if ($matriz[consulta]) {
		//exibe o total geral
		htmlAbreLinha($corFundo);
			itemLinhaTMNOURL('<b>Total de todos POP</br>', 'right', 'middle', center, $corFundo, 2, $zebra);
			$cc++;
			itemLinhaTMNOURL(formatarValoresForm($totalGeral[tgRecebido]), 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, 'txtcheck');
			itemLinhaTMNOURL("&nbsp;", 'right', 'middle', "center", $corFundo, 2, $zebra);
		htmlFechaLinha();		
		}
		
		if(! $vazio) {
			$c=0;
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
			$matResultado[$matCabecalho[$c++]][$l]='<b>Total Geral</b>';
			$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[tgRecebido]).'</b>';
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
			
			$zebra="tabfundo0";
			$cc=0;	

			
			# Alimentar Matriz Geral
			$matrizRelatorio[detalhe]=$matResultado;
			
			# Alimentar Matriz de Header
			$matrizRelatorio[header][TITULO]="BAIXA POR SERVIÇOS";
			$matrizRelatorio[header][POP]=$nomePop.'<br>'.$periodo;
			$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
			
			# Configurações
			$matrizRelatorio[config][linhas]=25;
			$matrizRelatorio[config][layout]='landscape';
			$matrizRelatorio[config][marginleft]='1.0cm;';
			$matrizRelatorio[config][marginright]='1.0cm;';
			
			$matrizGrupo[]=$matrizRelatorio;
			
			#Se for escolhido Consulta nao gera o pdf
			if (! $matriz[consulta]) {
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_baixaservico'),'faturamento_baixaservico',$matrizRelatorio[config]);
				if ($arquivo) {
					echo "<br>";
					novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
						htmlAbreLinha($corfundo);
							itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Baixa por Serviço</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
						htmlFechaLinha();
					fechaTabela();
				}
			}
		}
	
		return(0);
	} else {
		echo "<br>";
		$msg="Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}	
	fechaTabela();
	echo "<script>location.href='#ancora';</script>";
}

/*
SELECT Pessoas.idPOP pop, GruposServicos.nome grupo, GruposServicos.id idGrupo, Pessoas.nome cliente, ContasAReceber.valor valor, ContasAReceber.valorRecebido recebido, ContasAReceber.status status, ContasAReceber.dtVencimento vencto, ServicosGrupos.idServico idServ, DocumentosGerados.id idDocumentoGerado FROM    Pessoas, PessoasTipos,  DocumentosGerados, ContasAReceber, PlanosDocumentosGerados, ServicosPlanosDocumentosGerados, ServicosPlanos, ServicosGrupos, GruposServicos WHERE   Pessoas.id = PessoasTipos.idPessoa AND PessoasTipos.id=DocumentosGerados.idPessoaTipo AND DocumentosGerados.id=ContasAReceber.idDocumentosGerados AND PlanosDocumentosGerados.idDocumentoGerado=DocumentosGerados.id AND ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado=PlanosDocumentosGerados.id AND ServicosPlanos.id=ServicosPlanosDocumentosGerados.idServicosPlanos AND ServicosGrupos.idServico=ServicosPlanos.idServico AND GruposServicos.id=ServicosGrupos.idGrupos  AND Pessoas.idPOP = 1 AND ServicosGrupos.idGrupos in (2)  AND ContasAReceber.dtVencimento between '2004/01/01 00:00:00' and '2004/01/31 23:59:59'  and ContasAReceber.status<>'C' GROUP BY Pessoas.id, GruposServicos.id ORDER BY GruposServicos.nome, Pessoas.nome 
*/
?>

