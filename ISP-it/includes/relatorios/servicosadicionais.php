<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 18/06/4004
# Ultima alteração: 18/06/2004
#    Alteração No.: 001
#
# Função:
#      Relatorio de Servicos Adicionais 
#	   
#	   	Escolhe o POP, o servico adicional e a data.
#		Relaciona o nome do cliente, mes, nome do servico e o valor 



/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para filtros de Faturamento por POP
*/
function formServicoAdicional($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[Relatório de Serviços Adicionais]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			
			#exibe os pop
			getPop($matriz);
			
			#Servico Adicional
			getServicoAdicional($matriz);
			
			#exibe os campos pra entrada de datas
//			getPeriodo(7, 8, $matriz);
			getPeriodo(8, 9, $matriz);
			
			#Detalhar
			getDetalharCliente($matriz);
			
			#botao
			getBotaoConfirmar($matriz);
			
		fechaTabela();
	}
	
}


/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int $registro
 * @param array $matriz
 * @desc Consulta de Faturamento por POP (Total, Recebido, Inadimplente)
 Ja gera o relatorio automaticamente
*/
function relatorioServicoAdicional($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;
	
	
	# Formatar Datas
	if ($matriz[dtInicial]) {
		$matriz[dtInicial]=formatarData($matriz[dtInicial]);
		$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/01 00:00:00';
		$matriz[dtInicial]=substr($matriz[dtInicial],0,2)."/".substr($matriz[dtInicial],2,4);
	}
	
	if ($matriz[dtFinal]) {
		$matriz[dtFinal]=formatarData($matriz[dtFinal]);
		$dia=dataDiasMes(substr($matriz[dtFinal],0,2));
		$dtFinal=substr($matriz[dtFinal],2,4)."/".substr($matriz[dtFinal],0,2).'/'.$dia.' 23:59:59';
		$matriz[dtFinal]=substr($matriz[dtFinal],0,2)."/".substr($matriz[dtFinal],2,4);
	}
	
	// Ajusta o sql para determinar o periodo escolhido
	$sqlDT="";
	if($matriz[dtInicial] && $matriz[dtFinal]) {
		$sqlDT=" AND $tb[ServicosAdicionais].dtVencimento between '$dtInicial' and '$dtFinal' ";
		$periodo="de ".$matriz[dtInicial]." até ".$matriz[dtFinal];
	} 
	elseif ($matriz[dtInicial]) {
		$sqlDT=" AND $tb[ServicosAdicionais].dtVencimento >= '$dtInicial' ";
		$periodo="a partir de ".$matriz[dtInicial];
	} 
	elseif ($matriz[dtFinal])  {
		$sqlDT=" AND $tb[ServicosAdicionais].dtVencimento <= '$dtFinal' ";
		$periodo="até ".$matriz[dtFinal];
	}
	
	
	$titulo = "Serviços Adicionais";
	
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
	elseif($matriz[pop_todos]) {
		# consultar todos os POP
		$consultaPOP=buscaPOP('','','todos','nome');
	}

	if( $matriz['pop_todos'] || $matriz['pop'] ){
	
		#Tipo do Servico Adicional
		if (!$matriz[sa_todos] && $matriz[idTipoServicoAdicional]) {
			$idSA = "AND $tb[ServicosAdicionais].idTipoServicoAdicional = ".$matriz[idTipoServicoAdicional];
		}
		
		# Cabeçalho
		$gravata=array('Cliente', 'Mês',   'Serviço', 'Valor');
		$largura=array('40%',     '10%',   '40%',     '10%');
		$alinhar=array('left',    'center','left',    'right');
		$numColunas=4;
		
		# Consultar POPs
		if($consultaPOP && contaConsulta($consultaPOP)>0) {
			
			for($x=0;$x<contaConsulta($consultaPOP);$x++) {
			
				$idPOP=resultadoSQL($consultaPOP, $x, 'id');
				$nomePOP=resultadoSQL($consultaPOP, $x, 'nome');
				
				$totalGeral=array();
				$total=array();
				$totalValor=0;
				
				# Cabeçalho
				$matResultado=array();
				$matCabecalho=$gravata;
				
				echo "<br>";
				novaTabela("[ $titulo - $nomePOP ]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, $numColunas);
				
				$cc=0;			
				novaLinhaTabela($corFundo, '100%');
					foreach ($gravata as $grv)
						itemLinhaTabela($grv, $alinhar[$cc], $largura[$cc++], 'tabfundo0');
				fechaLinhaTabela();
	
				# SQL para consulta de emails por dominios do cliente informado
				# Consultas de Pendentes
				#$sql="SELECT ServicosAdicionais.id idServico, left(Pessoas.nome, 15) cliente,left(ServicosAdicionais.nome, 15) servico,ServicosAdicionais.dtVencimento vencimento,ServicosAdicionais.status status,ServicosAdicionais.valor FROM    Pop,Pessoas,PessoasTipos,TipoPessoa, PlanosPessoas, ServicosAdicionais WHERE ServicosAdicionais.idPlano=PlanosPessoas.id AND PessoasTipos.id=PlanosPessoas.idPessoaTipo AND Pessoas.id = PessoasTipos.idPessoa AND Pop.id = Pessoas.idPOP AND PessoasTipos.idTipo = TipoPessoa.id AND TipoPessoa.valor='cli' AND ServicosAdicionais.status <> 'C' AND Pop.id = 1 AND ServicosAdicionais.idTipoServicoAdicional = 1 AND ServicosAdicionais.dtVencimento between '2004/04/01 00:00:00' and '2004/04/31 23:59:59' order by Pessoas.nome, ServicosAdicionais.dtVencimento, ServicosAdicionais.nome";
				
				$sql = "SELECT 	$tb[ServicosAdicionais].id idServico, 
							$tb[Pessoas].nome cliente, 
							$tb[ServicosAdicionais].nome servico,
							$tb[ServicosAdicionais].dtVencimento vencimento, 
							$tb[ServicosAdicionais].status status, 
							$tb[ServicosAdicionais].valor 
					FROM 	$tb[POP], 
							$tb[ServicosAdicionais], 
							$tb[Pessoas], 
							$tb[PessoasTipos], 
							$tb[TipoPessoas],
							PlanosPessoas
					WHERE 	ServicosAdicionais.idPlano=PlanosPessoas.id 
			   		   		AND PessoasTipos.id=PlanosPessoas.idPessoaTipo 
			   		   		AND Pessoas.id = PessoasTipos.idPessoa 
			   		   		AND Pop.id = Pessoas.idPOP 
			   		   		AND PessoasTipos.idTipo = TipoPessoa.id 
			   		   		AND TipoPessoa.valor='cli' 
			   		   		AND ServicosAdicionais.status <> 'C' 
					   		AND ServicosAdicionais.valor > 0 
							AND $tb[POP].id = $idPOP
							$idSA
							$sqlDT
				ORDER BY   $tb[Pessoas].nome, 
						   $tb[ServicosAdicionais].dtVencimento,
						   $tb[ServicosAdicionais].nome
				";
				
				$consulta=consultaSQL($sql, $conn);
				if($consulta && contaConsulta($consulta)>0) {
					# Totalizar
					for($i=0;$i<contaConsulta($consulta);$i++) {
						
						$idServico=resultadoSQL($consulta, $i, 'idServico');
						$cliente=resultadoSQL($consulta, $i, 'cliente');
						$servico=resultadoSQL($consulta, $i, 'servico');
						$vencimento=resultadoSQL($consulta, $i, 'vencimento');
						$status=resultadoSQL($consulta, $i, 'status');
						$valor=resultadoSQL($consulta, $i, 'valor');
					 
						$ano=substr($vencimento,0,4);
						$mesFormatado=$configMeses[intval(substr($vencimento, 5, 2))]."/".$ano;
						
						$totalValor+=$valor;
						$totalGeral[valor]+=$valor;
						
						$valorf=formatarValoresForm($valor);
						
						#matrizes para o relatorio
						$c=0;
						$matResultado[$matCabecalho[$c++]][$i]=$cliente;
						$matResultado[$matCabecalho[$c++]][$i]=$vencimento;
						$matResultado[$matCabecalho[$c++]][$i]=$servico;
						$matResultado[$matCabecalho[$c++]][$i]=$valorf;
						
						$c=0;
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTabela($cliente, $alinhar[$c], $largura[$c++], 'normal10');
							itemLinhaTabela($mesFormatado, $alinhar[$c], $largura[$c++], 'normal10');
							itemLinhaTabela(wordwrap($servico, 40, "<br />\n", 1 ), $alinhar[$c], $largura[$c++], 'normal10');
							itemLinhaTabela($valorf, $alinhar[$c], $largura[$c++], 'normal10');
						fechaLinhaTabela();
	
					} #fecha laco de montagem de tabela
					
					# Totalizar
					$cor='tabfundo0';
					$c=0;
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela("&nbsp;", $alinhar[$c], $largura[$c++], $cor);
						itemLinhaTabela("&nbsp;", $alinhar[$c], $largura[$c++], $cor);
						itemLinhaTabela("Total", $alinhar[$c], $largura[$c++], $cor);
						itemLinhaTabela(formatarValoresForm($totalValor), $alinhar[$c], $largura[$c++], $cor);
					fechaLinhaTabela();
					
//					#pula linha entre as tabelas de pop
//					if($x+1<contaConsulta($consultaPOP)) itemTabelaNOURL('&nbsp;','left',$corFundo,$numColunas,'normal10');
					
					# Alimentar Array de Detalhe com mais um campo - totais
					$c=0;
					$matResultado[$matCabecalho[$c++]][$a]="&nbsp;";
					$matResultado[$matCabecalho[$c++]][$a]="&nbsp;";
					$matResultado[$matCabecalho[$c++]][$a]='<b>Total</b>';
					$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalValor).'</b>';
					
					# Alimentar Matriz Geral
					$matrizRelatorio[detalhe]=$matResultado;
					
					# Alimentar Matriz de Header
					$matrizRelatorio[header][TITULO]=strtoupper("Total de $titulo")."<br>".converteData($dtInicial,'banco','formdata')." até ".converteData($dtFinal,'banco','formdata');;
					$matrizRelatorio[header][POP]=$nomePOP;
					$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
					
					# Configurações
					$matrizRelatorio[config][linhas]=20;
					$matrizRelatorio[config][layout]='landscape';
									
					$matrizGrupo[]=$matrizRelatorio;
					
				}
				else {
					# Não há registros
					itemTabelaNOURL('Não foram encontrados faturamentos neste período', 'left', $corFundo, $numColunas, 'txtaviso');
				}
				
				fechaTabela();
				
			} // fim do for POP
			
			
			if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
				itemTabelaNOURL('&nbsp;','left',$corFundo,$numColunas,'normal10');
				novaTabela("Arquivos Gerados<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, $numColunas);
					# Converter para PDF:
					$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','servicoAdicional'),'servicoAdicional',$matrizRelatorio[config]);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório $titulo</a>",'pdf'), 'center', $corFundo, $numColunas, 'txtaviso');
				fechaTabela();
			}
			
		}
		else {
			# Não há registros
			itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, $numColunas, 'txtaviso');
		}
			
	}
	else {
		echo "<br>";
		$msg="Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}

}
/*
+---------------------------------+--------+------------------------------------------------+---------------------+---------+--------------------------------------------------------+------+---------------------------------+
| table                           | type   | possible_keys                                  | key                 | key_len | ref                                                    | rows | Extra                           |
+---------------------------------+--------+------------------------------------------------+---------------------+---------+--------------------------------------------------------+------+---------------------------------+
| Pop                             | const  | PRIMARY                                        | PRIMARY             |       4 | const                                                  |    1 | Using temporary; Using filesort |
| ServicosAdicionais              | ALL    | K_For_FK_ServicosAdicionais_Servicos           | NULL                |    NULL | NULL                                                   | 1097 | Using where                     |
| ServicosPlanosDocumentosGerados | ref    | idPlanoDocumentoGerado,idServicosPlanos        | idServicosPlanos    |       5 | ServicosAdicionais.idServicoPlano                      |   11 | Using where                     |
| PlanosDocumentosGerados         | eq_ref | PRIMARY,idDocumentoGerado                      | PRIMARY             |       4 | ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado |    1 |                                 |
| DocumentosGerados               | eq_ref | PRIMARY,idPessoaTipo                           | PRIMARY             |       4 | PlanosDocumentosGerados.idDocumentoGerado              |    1 |                                 |
| ContasAReceber                  | ref    | idDocumentosGerados                            | idDocumentosGerados |       5 | DocumentosGerados.id                                   |    1 | Using where                     |
| PessoasTipos                    | eq_ref | PRIMARY,K_For_FK_PessoasTipos_Pessoas,idPessoa | PRIMARY             |       4 | DocumentosGerados.idPessoaTipo                         |    1 |                                 |
| TipoPessoa                      | eq_ref | PRIMARY                                        | PRIMARY             |       4 | PessoasTipos.idTipo                                    |    1 | Using where                     |
| Pessoas                         | eq_ref | PRIMARY                                        | PRIMARY             |       4 | PessoasTipos.idPessoa                                  |    1 | Using where                     |
+---------------------------------+--------+------------------------------------------------+---------------------+---------+--------------------------------------------------------+------+---------------------------------+

SELECT distinct ServicosAdicionais.id idServico,Pessoas.nome cliente,ServicosAdicionais.nome servico,ServicosAdicionais.dtVencimento vencimento,ContasAReceber.dtBaixa baixa,ContasAReceber.status status,ServicosAdicionais.valor FROM    Pop,ServicosAdicionais,Pessoas,PessoasTipos,TipoPessoa,ContasAReceber,DocumentosGerados,PlanosDocumentosGerados,ServicosPlanosDocumentosGerados WHERE   PessoasTipos.idTipo = TipoPessoa.id AND TipoPessoa.valor='cli' AND Pop.id = Pessoas.idPOP AND Pessoas.id = PessoasTipos.idPessoa AND DocumentosGerados.idPessoaTipo = PessoasTipos.id AND ContasAReceber.idDocumentosGerados = DocumentosGerados.id AND PlanosDocumentosGerados.idDocumentoGerado=DocumentosGerados.id AND ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado=PlanosDocumentosGerados.id AND ServicosAdicionais.idServicoPlano = ServicosPlanosDocumentosGerados.idServicosPlanos AND ContasAReceber.status <> 'C' AND Pop.id = 1 AND ServicosAdicionais.idTipoServicoAdicional = 1 AND ServicosAdicionais.dtVencimento between '2004/04/01 00:00:00' and '2004/04/31 23:59:59' ORDER BY   Pessoas.nome,ContasAReceber.dtVencimento,ServicosAdicionais.nome

SELECT ServicosAdicionais.id idServico, left(Pessoas.nome, 15) cliente,left(ServicosAdicionais.nome, 15) servico,ServicosAdicionais.dtVencimento vencimento,ServicosAdicionais.status status,ServicosAdicionais.valor FROM    Pop,Pessoas,PessoasTipos,TipoPessoa, PlanosPessoas, ServicosAdicionais
WHERE
	ServicosAdcionais.   
    PessoasTipos.idTipo = TipoPessoa.id 
AND TipoPessoa.valor='cli' 
AND Pop.id = Pessoas.idPOP 
AND Pessoas.id = PessoasTipos.idPessoa 
AND DocumentosGerados.idPessoaTipo = PessoasTipos.id 
AND ContasAReceber.idDocumentosGerados = DocumentosGerados.id 
AND PlanosDocumentosGerados.idDocumentoGerado=DocumentosGerados.id 
AND ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado=PlanosDocumentosGerados.id 
AND ServicosAdicionais.idServicoPlano = ServicosPlanosDocumentosGerados.idServicosPlanos 
AND ContasAReceber.status <> 'C' AND Pop.id = 1 AND ServicosAdicionais.idTipoServicoAdicional = 1 
AND ServicosAdicionais.dtVencimento between '2004/04/01 00:00:00' and '2004/04/31 23:59:59';
*/
?>
