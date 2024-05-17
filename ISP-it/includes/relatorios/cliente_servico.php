<?
##################################################
#       Criado por: Rogério Ramos - Devel-it     #
#  Data de criação: 19/11/2004                   #
# Ultima alteração: 19/11/2004                   # 
#    Alteração No.: 001                          # 
#                                                #
# Função:                                        # 
# Funções para relatório de Clientes por Serviço #
##################################################



# função para form de seleção de filtros de faturamento
function formRelatorioClienteServico($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[visualizar] && !$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		$data=dataSistema();
		
		# Motrar tabela de busca
		novaTabela2("[Clientes por Serviços]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			
			//tipo data
			$campos=array('dtCadastro=Data de Cadastro', 'dtCancelamento=Data de Cancelamento', 'dtAtivacao=Data de Ativação');
			getCampo('combo', 'Filtrar Por', '', getSelectNovo($campos, 'matriz[tipoData]', 0, 0, $matriz['tipoData']), 0, 0);
			
			//periodo
			getPeriodoDias(7, 8, $matriz, '');
		
			//status	
			getCampo('combo', 'Exibir Somente:<br>Não selecione nenhum para buscar por todos status', '', implode('<br>', getStatusCheckList($matriz)) );
			
			//Botao
			getBotoesConsRel();
			
			htmlFechaLinha();
		fechaTabela();
	}
	
}

#
# faz a consulta e o relatorio
#
function relatorioClienteServico($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml, $sessLogin, $conn, $tb;
	
	
	if ( ( $matriz[pop] ) || ( $matriz[pop_todos] ) ) {
				
		// Se forem todos os pops gera a lista na matriz
		if($matriz[pop_todos]) {
			$consultaPop=buscaPOP("status='A'",'','custom', 'id');
			if( $consultaPop && contaconsulta($consultaPop) ) {
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					$matriz[pop][$a]=resultadoSQL($consultaPop, $a, 'id');
				}
			}
		}

		# Formatar Datas
		if ($matriz[dtInicial]) {
			$data=formatarData($matriz[dtInicial]);
			$dtInicial=substr($data,4,4)."-".substr($data,2,2).'-'.substr($data,0,2).' 00:00:00';
		}
	
		if ($matriz[dtFinal]) {
			$data=formatarData($matriz[dtFinal]);
			$dtFinal=substr($data,4,4)."-".substr($data,2,2).'-'.substr($data,0,2).' 23:59:59';
		}
	
		// Ajusta o sql para determinar o periodo escolhido
		if (!$matriz[tipoData]) $tipoData='dtCadastro';
		else $tipoData=$matriz[tipoData];
			
		
		$sqlDT="";
		if($matriz[dtInicial] && $matriz[dtFinal]) {
			$sqlDT=" AND $tb[ServicosPlanos].$tipoData between '$dtInicial' and '$dtFinal' ";
			$periodo="de ".$matriz[dtInicial]." até ".$matriz[dtFinal];
		} 
		elseif ($matriz[dtInicial]) {
			$sqlDT=" AND $tb[ServicosPlanos].$tipoData >= '$dtInicial' ";
			$periodo="a partir de ".$matriz[dtInicial];
		} 
		elseif ($matriz[dtFinal])  {
			$sqlDT=" AND $tb[ServicosPlanos].$tipoData <= '$dtFinal' ";
			$periodo="até ".$matriz[dtFinal];
		}


		//status dos servicos planos	
		if(count($matriz['status']) > 0)
			$sqlStatus = " AND ServicosPlanos.idStatus in (".implode( ",", $matriz['status'] ).")  " ;

		
		// Prepara as variaveis de ajuste
		$pp=0;
		$totalGeral=array();
		$total=array();
		
		$matLargura=array(    '55%',      '15%',   '15%',      '15%');
		$matCabecalho=array(  'Serviço',  'Quantidade',  'Valor Unitário', 'Valor Total');
		$matAlinhamento=array('left',     'right', 'right',    'right');
		$numCol=count($matCabecalho);
			
		$ttRecebido= 0;
		$l=0;
	
		//Loop dos Pops
		while($matriz[pop][$pp]) {	
			// nome do pop para exbição
			$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			$sqlPOP="$tb[POP].id = ".$matriz[pop][$pp];
			
		
			$sql="SELECT Pop.nome pop, Servicos.nome servico, Servicos.valor as valor, ServicosPlanos.valor as valorEspecial, PlanosPessoas.especial, Pessoas.nome cliente, Count(Servicos.id) total
							FROM Pop 
							INNER JOIN Pessoas
								ON (Pop.id = Pessoas.idPop)
							INNER JOIN PessoasTipos
								ON (Pessoas.id = PessoasTipos.idPessoa) 
							INNER JOIN  PlanosPessoas 
								ON (PessoasTipos.id = PlanosPessoas.idPessoaTipo)
							INNER JOIN ServicosPlanos 	
								ON (PlanosPessoas.id = ServicosPlanos.idPlano)
							INNER JOIN  Servicos 
								ON (Servicos.id = ServicosPlanos.idServico)";
							$sql.="WHERE ".$sqlPOP;
							$sql.=
//" 
//							AND UPPER(PlanosPessoas.especial)!= 'S'
//							AND UPPER(PlanosPessoas.status)= 'A' "
							$sqlStatus
							. $sqlDT . "
							
							GROUP BY Pop.id,Servicos.id, Pessoas.id 
			
							ORDER BY Pop.nome, Servicos.Nome, Pessoas.nome";
					
			#echo "sql: $sql";
			
			$consulta=consultaSQL($sql, $conn);
			
			
			if( $consulta && contaconsulta($consulta) ) {
				
				
				# se for consulta exibe o cabecalho
				if ($matriz[bntConfirmar]) { //consulta
					# Cabeçalho
					echo "<br>";
					novaTabela($nomePop,"left", '100%', 0, 2, 1, $corFundo, $corBorda, $numCol);
					$cor='tabfundo0';
					htmlAbreLinha($cor);
						for ($cc=0;$cc<count($matCabecalho);$cc++) {
							itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $matLargura[$cc], $corFundo, 0, $cor);
						}
					htmlFechaLinha();
				}
				
				$matResultado=array();
				$l= 0;
				$ttRecebido= 0;
				
				#inicia a varredura e joga 
				for($a=0;$a<contaConsulta($consulta);$a++) {
					
					$cc=0;
					$campos[$cc++]=resultadoSQL($consulta, $a, 'cliente');
					$campos[$cc++]=resultadoSQL($consulta, $a , 'total');
					
					$campos[$cc++]=formatarValoresForm(resultadoSQL($consulta, $a , 'especial') == 'S' ? resultadoSQL($consulta, $a , 'valorEspecial') : resultadoSQL($consulta, $a , 'valor') );
//					$campos[$cc++]=resultadoSQL($consulta, $a , 'valor');
					$campos[$cc]=formatarValoresForm(resultadoSQL($consulta, $a , 'total') * formatarValores( $campos[2] ));
					
					$ttRecebido+=$campos[$cc];

					$serv_atual= resultadoSQL($consulta, $a, 'servico');
					if($a>0) $serv_anterior=resultadoSQL($consulta, $a-1, 'servico');
					$serv_proximo = ( ($a+1 < contaConsulta($consulta)) ? resultadoSQL($consulta, $a+1, 'servico') : "" );
					
					
					# se for consulta exibe a linha detalhe
					if ($matriz[bntConfirmar]) {
						
						if ($serv_atual!= $serv_anterior){
							htmlAbreLinha($corFundo);
								itemLinhaTMNOURL("<b>".$serv_atual."</b>", 'left', 'middle', '100%', $corFundo, $numCol, 'normal9');
							htmlFechaLinha();
							$ttParcial= formatarValores( $campos[3] ); //$ttParcial= $campos[$cc];
							$qtParcial= $campos[1]; //$qtParcial++;
						}
						else{
							$ttParcial+= formatarValores( $campos[3] ); //$ttParcial+= $campos[$cc];
							$qtParcial+= $campos[1]; //$qtParcial++;
						}
						
						htmlAbreLinha($corFundo);
							for ($cc=0; $cc<count($campos); $cc++) {
								itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, "normal9");
							}
						htmlFechaLinha();
						
						if ($serv_atual != $serv_proximo){
							htmlAbreLinha($corFundo);
								itemLinhaTMNOURL("<b>Total</b>", 'right', 'middle', '100%', $corFundo, 0, 'normal9');
								itemLinhaTMNOURL("<b>".$qtParcial."</b>", 'right', 'middle', '100%',$corFundo, 0,'normal9');
								//itemLinhaTMNOURL($campos[--$cc], 'right', 'middle', '100%', $corFundo, 0, 'normal9');
								itemLinhaTMNOURL("<b>".$campos[2]."</b>", 'right', 'middle', '100%', $corFundo, 0, 'normal9');
								itemLinhaTMNOURL("<b>".number_format($ttParcial,2,",",".")."</b>", 'right', 'middle', '100%', $corFundo, 0, 'normal9');
							htmlFechaLinha();
							
							$ttParcial= 0;
							$qtParcial=0;
						}						
						
					}
					# Se for relatorio guarda na matriz
					else{
						# se for serviço novo guarda linha do serviço
						if ($serv_atual!= $serv_anterior){
							$i=0;
							$matResultado[$matCabecalho[$i++]][$l]="<b>".$serv_atual."</b>";
							$matResultado[$matCabecalho[$i++]][$l]="&nbsp;";
							$matResultado[$matCabecalho[$i++]][$l]="&nbsp;";
							$matResultado[$matCabecalho[$i]][$l]="&nbsp;";
							$l++;
							$ttParcial= $campos[3]; //$ttParcial= $campos[$cc];
							$qtParcial= $campos[1]; //$qtParcial++;
						}
						# senão faz a contagem dos valores parciais
						else{
							$ttParcial+= $campos[3]; //$ttParcial+= $campos[$cc];
							$qtParcial+= $campos[1]; //$qtParcial++;
						}
						
						# detalhe
						for ($cc=0; $cc<count($campos); $cc++) {
							$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
						}
						$l++;						
						#verifica se é o final do serviço para mostrar total parcial
						if ($serv_atual!= $serv_proximo){
							
							$i=0;
							$matResultado[$matCabecalho[$i++]][$l]="<b>Total</b>";
							$matResultado[$matCabecalho[$i++]][$l]="<b>".$qtParcial."</b>";
							//$matResultado[$matCabecalho[$i++]][$l]="<b>".number_format($campos[3],2,",",".")."</b>";
							$matResultado[$matCabecalho[$i++]][$l]="<b>".number_format($campos[2],2,",",".")."</b>";
							//$matResultado[$matCabecalho[$i++]][$l]="<b>".number_format(($qtParcial*$campos[3]),2,",",".")."</b>";
							$matResultado[$matCabecalho[$i++]][$l]="<b>".number_format($ttParcial,2,",",".")."</b>";
							$l++;
							$ttParcial= 0;
							$qtParcial=0;
						}						
						

					}

				} // fim do loop for
				$vazio = 0;
			}
			else {
				$vazio = 1;
			}
			if (! $vazio) {
				# se for consulta exibe o total
				if ($matriz[bntConfirmar]) {
					$zebra="tabfundo0";
					$cc=0;	
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL('<b>Total Serviços do POP</br>', 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 3, $zebra);
						$cc++;
						itemLinhaTMNOURL(number_format($ttRecebido,2,',','.'), 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, 'txtcheck');
					htmlFechaLinha();
					fechaTabela();
					$ttRecebido= 0;
				}
				else {
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]="<b>Total do POP $nomePop</b>";
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$matResultado[$matCabecalho[$c++]][$l]='<b>'.number_format($ttRecebido,2,',','.').'</b>';
					
					$l++;
					
					$totalGeral[tgRecebido]+=$ttRecebido;
					
								# Alimentar Matriz Geral
					$matrizRelatorio[detalhe]=$matResultado;
					
					# Alimentar Matriz de Header
					$matrizRelatorio[header][TITULO]="CLIENTES POR SERVIÇOS - ";
					$matrizRelatorio[header][POP]=$nomePop;
					$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
					
					# Configurações
					$matrizRelatorio[config][linhas]=35; //25
					$matrizRelatorio[config][layout]='portrait';
					$matrizRelatorio[config][marginleft]='1.0cm;';
					$matrizRelatorio[config][marginright]='1.0cm;';
				
					$matrizGrupo[]=$matrizRelatorio;
				}
			}
			$pp++;
			
		} // while do pop
		
		if( count( $matResultado ) ) {
			

			
			# Alimentar Matriz Geral
			#### codigo para geracao de relatorio via FPDF
			/*
			$matrizRelatorio[detalhe]=$matDetalhe;
			
			# Alimentar Matriz de Header
			$matrizRelatorio[header][TITULO]="Relatório de Clientes por Serviços";
			$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
			$matrizRelatorio[header][cabecalho]=$matCabecalho;
			
			# Configurações
			$matrizRelatorio[config][layout]='portrait';
			$matrizRelatorio[config][marginleft]='1.0cm;';
			$matrizRelatorio[config][marginright]='1.0cm;';
			$matrizRelatorio[config][alinhamento]=$matAlinhamento;
			$matrizRelatorio[config][largura]=$matLargura;
			
			//gera o PDF
			//instancio a classe de relatorios com o parametro da orientacao da pagina!
			$relatorio= new Relatorio2Pdf();
			//utilizando a classe chamamos o  metodo geraImpressao
			$nomeArquivo=$relatorio->geraImpressao($matrizRelatorio);
			exibeLinkPdf("Relatório de Clientes por Serviços",$nomeArquivo);
			
			//FIM DO CODIGO PARA GERACAO DE  PDF VIA FPDF
			*/
			
			/*$c=0;
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
			$matResultado[$matCabecalho[$c++]][$l]='<b>Total Geral</b>';
			$matResultado[$matCabecalho[$c++]][$l]='<b>'.$ttRecebido.'</b>';
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';*/
			
			
			#Se for escolhido Consulta nao gera o pdf
			if (! $matriz[bntConfirmar]) {
				# Converter para PDF:
				$nome= "cliente_servico";
				criaTemplates($nome,$matCabecalho,$matAlinhamento);
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html',$nome),$nome,$matrizRelatorio[config]);
				if ($arquivo) {
					echo "<br>";
					novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
						htmlAbreLinha($corfundo);
							itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Clientes por Serviço</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
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
	
	echo "<script>location.href='#ancora';</script>";
}

/*
SELECT Pessoas.idPOP pop, GruposServicos.nome grupo, GruposServicos.id idGrupo, Pessoas.nome cliente, ContasAReceber.valor valor, ContasAReceber.valorRecebido recebido, ContasAReceber.status status, ContasAReceber.dtVencimento vencto, ServicosGrupos.idServico idServ, DocumentosGerados.id idDocumentoGerado FROM    Pessoas, PessoasTipos,  DocumentosGerados, ContasAReceber, PlanosDocumentosGerados, ServicosPlanosDocumentosGerados, ServicosPlanos, ServicosGrupos, GruposServicos WHERE   Pessoas.id = PessoasTipos.idPessoa AND PessoasTipos.id=DocumentosGerados.idPessoaTipo AND DocumentosGerados.id=ContasAReceber.idDocumentosGerados AND PlanosDocumentosGerados.idDocumentoGerado=DocumentosGerados.id AND ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado=PlanosDocumentosGerados.id AND ServicosPlanos.id=ServicosPlanosDocumentosGerados.idServicosPlanos AND ServicosGrupos.idServico=ServicosPlanos.idServico AND GruposServicos.id=ServicosGrupos.idGrupos  AND Pessoas.idPOP = 1 AND ServicosGrupos.idGrupos in (2)  AND ContasAReceber.dtVencimento between '2004/01/01 00:00:00' and '2004/01/31 23:59:59'  and ContasAReceber.status<>'C' GROUP BY Pessoas.id, GruposServicos.id ORDER BY GruposServicos.nome, Pessoas.nome 
*/

?>