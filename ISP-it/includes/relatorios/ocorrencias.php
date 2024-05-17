<?
##################################################
#       Criado por: Rogério Ramos - Devel-it     #
#  Data de criação: 02/12/2004                   #
# Ultima alteração: 02/12/2004                   # 
#    Alteração No.: 001                          # 
#                                                #
# Função:                                        #
# Funções para relatório de Ocorrencias          #
##################################################

# função para form de seleção de filtros de faturamento
function formRelatorioOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[Ocorrências - Clientes]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			//Periodo de Ocorrencias (dtIni e dtFim)
			getPeriodoDias(6,7, $matriz);
			//situacao ocorrencia (novo, aberta, cancelada, etc)
			getStatusOcorrencias();
			//tipo ocorrencia (geral, ordem servico, ticket)
			
			//prioridade
			
			//Botao
			getBotoesConsRel();
						
		fechaTabela();
	}
	
}

#
# faz a consulta e o relatorio
#
function relatorioOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

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
		
		// aplica a clausula where para o status da Ocorrencia
		if ( ( $matriz[status] ) || ( $matriz[status_todos] ) ){
			$sqlStatus='';
			if ( $matriz[status] ){
					$sqlStatus= "($tb[Ocorrencias].status= '".implode("' OR $tb[Ocorrencias].status='",$matriz[status])."')";
			}
		}
		
		//aplica a clausula where para o periodo inicial e final das Ocorrencias, se nao for especificado buscas tds as datas
		if ( $matriz[dtInicial] || $matriz[dtFinal] ){
			if ( !$matriz[dtFinal] )
				$sqlPeriodo= "$tb[Ocorrencias].data BETWEEN '".converteData($matriz[dtInicial]." 00:00:00",'form','banco')."' AND '".date('Y-m-d H:i:s')."'";
			elseif ( !$matriz[dtInicial] )
				$sqlPeriodo= "$tb[Ocorrencias].data <= '".date('Y-m-d H:i:s')."'";
			else
				$sqlPeriodo="$tb[Ocorrencias].data BETWEEN '".converteData($matriz[dtInicial]." 00:00:00",'form','banco')."' AND '".converteData($matriz[dtFinal]." 23:59:59",'form','banco')."'";
		}
		
		
		// Prepara as variaveis de ajuste
		$pp=0;
		
		$matLargura=array(    '10%',      '10%',   '10%',      '70%');
		$matCabecalho=array();
		$matAlinhamento=array('left',     'left', 'left',    'left');
		$numCol=count($matLargura);
			
		$l=0;
		
		if ( $matriz[bntConfirmar] )
			echo "<br>";
			
		while($matriz[pop][$pp]) {	
			// nome do pop para exbição
			$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			$sqlPOP="$tb[POP].id = ".$matriz[pop][$pp];
			
			$sql="SELECT $tb[Pessoas].id idPessoa, $tb[Pessoas].nome nomePessoa, $tb[Ocorrencias].id idOcorrencia,  $tb[Ocorrencias].data,
						 $tb[Ocorrencias].nome nomeOcorrencia, $tb[Ocorrencias].descricao descricaoOcorrencia, $tb[Ocorrencias].idUsuario usrOcorrencia,
						 $tb[Ocorrencias].status status, $tb[OcorrenciasComentarios].texto textoComentario, 
						 $tb[OcorrenciasComentarios].idUsuario usrComentario, $tb[OcorrenciasComentarios].data dataComentario
			
				FROM $tb[OcorrenciasComentarios] RIGHT JOIN $tb[Ocorrencias] 
					ON ($tb[OcorrenciasComentarios].idOcorrencia = $tb[Ocorrencias].id)
				INNER JOIN $tb[PessoasTipos]
					ON ($tb[Ocorrencias].idPessoaTipo = $tb[PessoasTipos].id)
				INNER JOIN $tb[Pessoas]
					ON ($tb[PessoasTipos].idPessoa = $tb[Pessoas].id)
				INNER JOIN $tb[POP]
					ON ($tb[Pessoas].idPop = $tb[POP].id)";
			
				$sql.=" WHERE ".$sqlPOP;
				if ( $sqlPeriodo && !empty($sqlPeriodo) ) $sql.= " AND ".$sqlPeriodo;
				if ( $sqlStatus && !empty($sqlStatus) ) $sql.= " AND ".$sqlStatus;
					
				$sql.=" ORDER BY $tb[Pessoas].nome, $tb[Ocorrencias].data";
					
			#echo "sql: $sql";
			
			$consulta=consultaSQL($sql, $conn);
			$consSQL= $consulta;
			
			if( $consulta && contaconsulta($consulta) ) {
				
				if ($matriz[bntConfirmar]) { //consulta
					novaTabela($nomePop,"left", '100%', 0, 2, 1, $corFundo, $corBorda, $numCol);
				}
				$nomeLink[]= $nomePop;				
				
				$matResultado=array();
				$l= 0;
				
				$cliAnt= 0;
				$ocorAnt= 0;
				#inicia a varredura e joga 
				for($a=0;$a<contaConsulta($consulta);$a++) {
					
					$cliAtual= resultadoSQL($consulta, $a, 'idPessoa');
					
					$ocorAtual= resultadoSQL($consulta, $a, 'idOcorrencia');
					
					$idUsrOcorrencia = resultadoSQL( $consulta, $a, 'usrOcorrencia' );
					$idUsrComentario = resultadoSQL( $consulta, $a, 'usrComentario' );
					
					$status = resultadoSQL( $consulta, $a, 'status' );
					
					#define as opçes do relatorio de ocorrencias
					$def = "<a href=?modulo=ocorrencias&sub=&registro=$ocorAtual";
					$opcoes=htmlMontaOpcao( $def."&acao=alterar>Alterar</a>",'alterar' );
					$opcoes.=htmlMontaOpcao( $def."&acao=excluir>Excluir</a>",'excluir');
					
					# Verificar status
					if($status=='N') $opcoes.=htmlMontaOpcao( $def."&acao=abrir>Abrir</a>",'abrir');
					else {
						if($status=='A' || $status=='R') {
							$opcoes.=htmlMontaOpcao( $def."&acao=fechar>Fechar</a>",'fechar');
							$opcoes.=htmlMontaOpcao( $def."&acao=cancelar>Cancelar</a>",'cancelar');
							//$opcoes.="<br>";
							$opcoes.=htmlMontaOpcao( $def."&acao=comentar>Comentar</a>",'comentar');
						}
						elseif($status=='F') $opcoes.=htmlMontaOpcao( $def."&acao=reabrir>Re-Abrir</a>",'abrir');
						elseif($status=='P') $opcoes.=htmlMontaOpcao( $def."&acao=fechar>Fechar</a>",'fechar');
					}
					//$opcoes.=htmlMontaOpcao( $def."&acao=historico>Historico</a>",'historico');
					
					if ( $idUsrOcorrencia != '' && $idUsrOcorrencia == $idUsrComentario){
						$sql= "SELECT login FROM Usuarios WHERE id= ".$idUsrOcorrencia;
						$consultaUsr= consultaSQL( $sql, $conn );
						if (contaConsulta($consultaUsr)>0){
							$usrOcorrencia = resultadoSQL( $consultaUsr, 0, 'login');
							$usrComentario = $usrOcorrencia;
							}
					} else {
						if ( !is_null( $idUsrOcorrencia ) ) {
							$sql= "SELECT login FROM Usuarios WHERE id= ".$idUsrOcorrencia;
							$consultaUsr= consultaSQL( $sql, $conn );
							if (contaConsulta($consultaUsr)>0)
								$usrOcorrencia = resultadoSQL( $consultaUsr, 0, 'login');
						}
						if ( !is_null( $idUsrComentario ) ){
							$sql= "SELECT login FROM Usuarios WHERE id= ".$idUsrComentario;
							$consultaUsr= consultaSQL( $sql, $conn );
							if (contaConsulta($consultaUsr)>0)		
								$usrComentario = resultadoSQL( $consultaUsr, 0, 'login');
						}
					}
					
					# se for consulta exibe a linha detalhe
					if ($matriz[bntConfirmar]) {
						// monta a linha com o nome dos clientes
						if ( $cliAnt != $cliAtual ){
							$cor='tabfundo0';
							htmlAbreLinha($cor);
									itemLinhaTMNOURL('&nbsp;', $matAlinhamento[$cc], 'middle', '10%', $corFundo, 0, $cor);
									itemLinhaTMNOURL(resultadoSQL($consulta,$a, 'nomePessoa'), $matAlinhamento[$cc], 'middle', '70%', $corFundo, 2, $cor);
									itemLinhaTMNOURL('Opções', $matAlinhamento[$cc], 'middle', '20%', $corFundo, 1, $cor);
							htmlFechaLinha();			
						// fim exibição do nome do cliente
						}
						
						//monta a linha com o nome e data da Ocorrencia			
						if ($ocorAtual != $ocorAnt){
							htmlAbreLinha($corFundo);
								//print "Entrou Nome e Data<br>";
								$mostraOcorrencia= $usrOcorrencia." - ".converteData(resultadoSQL($consulta, $a, 'data'),'banco', 'form')." - ".resultadoSQL($consulta, $a, 'nomeOcorrencia');
								itemLinhaTMNOURL('Criado por: ', 'right', 'middle', '20%', $corFundo, 2, 'normal10');
								itemLinhaTMNOURL("<b>".$mostraOcorrencia."</b>", 'left', 'middle', '80%', $corFundo, 2, 'normal10');
							htmlFechaLinha();
						//fim da exibição do nome da Ocorrencia
						}
						
						//monta a linha com a descrição da Ocorrencia
						if ($ocorAtual != $ocorAnt){	
							$descr = resultadoSQL($consulta, $a, 'descricaoOcorrencia');
							if (is_null( $descr )  ? $descr ='&nbsp;' : $descr = resultadoSQL( $consulta, $a, 'descricaoOcorrencia' ) );
						    //print "$descr";
							$descr = '';
							if (is_null( resultadoSQL($consulta, $a, 'descricaoOcorrencia') ) ? $descr ='&nbsp;' : $descr = resultadoSQL($consulta, $a, 'descricaoOcorrencia') );
							//print "Entrou Descricao $descr<br>";
							htmlAbreLinha($corFundo);
								$identificacaoOcor = converteData(resultadoSQL($consulta, $a, 'data'),'banco', 'form')." - ".$usrOcorrencia;
								itemLinhaTMNOURL($identificacaoOcor, $matAlinhamento[$cc], 'middle', '30%', $corFundo, 2, 'normal9');
								itemLinhaTMNOURL("<b>$descr</b>", 'left', 'middle', '50%', $corFundo, 1, 'normal9');
								itemLinhaTMNOURL($opcoes, 'left', 'middle', '20%', $corFundo, 1, 'normal9');
							htmlFechaLinha();
							unset( $descr );
						//fim exibição da descricao da Ocorrencia
						}

						//mostra os comentarios das Ocorrencias
						htmlAbreLinha($corFundo);
							if (!is_null(resultadoSQL($consulta, $a, 'textoComentario'))){
								//print "Entrou Comentario Nao Nulo<br>";
								$identificacao= converteData( resultadoSQL( $consulta, $a, 'dataComentario' ), 'banco', 'form' )." - ".$usrComentario;
								itemLinhaTMNOURL($identificacao, $matAlinhamento[0], 'middle', '30%', $corFundo, 2, "normal9");
								itemLinhaTMNOURL(resultadoSQL($consulta, $a, 'textoComentario'), $matAlinhamento[0], 'middle', '70%', $corFundo, 2, "normal9");
								
							}
														
						//fim da exibiçao dos comentarios das Ocorrencias
						htmlFechaLinha();
					}
					else{
						if ($serv_atual!= $serv_anterior){
								$i=0;
								$matResultado[$matCabecalho[$i++]][$l]="<b>".$serv_atual."</b>";
								$matResultado[$matCabecalho[$i++]][$l]="&nbsp;";
								$matResultado[$matCabecalho[$i++]][$l]="&nbsp;";
								$matResultado[$matCabecalho[$i]][$l]="&nbsp;";
							$l++;
							$ttParcial= $campos[$cc];
							$qtParcial++;
						}
						else{
							$ttParcial+= $campos[$cc];							
							$qtParcial++;
						}
						
						# detalhe
						for ($cc=0; $cc<count($campos); $cc++) {
							$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
						}
						$l++;						
						
						if ($serv_atual!= $serv_proximo){
							
							$i=0;
							$matResultado[$matCabecalho[$i++]][$l]="<b>Total</b>";
							$matResultado[$matCabecalho[$i++]][$l]="<b>".$qtParcial."</b>";
							$matResultado[$matCabecalho[$i++]][$l]="<b>".number_format($campos[3],2,",",".")."</b>";
							$matResultado[$matCabecalho[$i++]][$l]="<b>".number_format(($qtParcial*$campos[3]),2,",",".")."</b>";
							$l++;
							$ttParcial= 0;
							$qtParcial=0;
						}						
						

					}

					$cliAnt= $cliAtual;
					$ocorAnt= $ocorAtual;
				}

				$matrizRelatorio[detalhe]=$matDetalhe;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="Relatório de Ocorrências por Cliente";
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				//$matrizRelatorio[header][cabecalho]=$matCabecalho;
				
				# Configurações
				$matrizRelatorio[config][layout]='portrait';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
				$matrizRelatorio[config][alinhamento]=$matAlinhamento;
				$matrizRelatorio[config][largura]=$matLargura;
				$matrizRelatorio['consulta']= $consSQL;
				$matrizRelatorio['POP']= $nomePop;
				$matrizRelatorio['conn']= $conn; 
				
				//gera o PDF
				//instancio a classe de relatorios com o parametro da orientacao da pagina!
				$relatorio= new Ocorrencias2Pdf();
				//utilizando a classe chamamos o  metodo geraImpressao
				$nomeArquivo=$relatorio->geraImpressao($matrizRelatorio);
				
				$links[]= $nomeArquivo;				
			} 
			else {
				$vazio = 1;
			}
			if (! $vazio) {

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
				$matrizRelatorio[header][TITULO]="CLIENTES POR SERVIÇOS";
				$matrizRelatorio[header][POP]=$nomePop;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=25;
				$matrizRelatorio[config][layout]='portrait';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
			
				$matrizGrupo[]=$matrizRelatorio;
			}
			$pp++;
			
			
			
			
			
		} // while
		
		
		/*htmlAbreLinha($corFundo);
			itemLinhaNOURL('&nbsp;', 'left', $corFundo, 7, 'corfundo1');
		htmlFechaLinha();*/
		
		fechaTabela(); // fecha a tabela de demonmstracao de ocorrencias em tela
		
		if ( $matriz['bntRelatorio'] ){
			if ( !is_null( $links ) ){
				novaTabela('[Arquivo(s) Gerado(s)]<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
					htmlAbreLinha($corFundo);
					$i= 0;
					foreach ($links as $link){
						itemTabelaNOURL(htmlMontaOpcao("<a href=$link>Pop ".$nomeLink[$i++]."</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
					}
					htmlFechaLinha();
				fechaTabela();
			}
		}
		if(! $vazio) {
			
			# Alimentar Matriz Geral
			#### codigo para geracao de relatorio via FPDF
			
			/*$matrizRelatorio[detalhe]=$matDetalhe;
			
			# Alimentar Matriz de Header
			$matrizRelatorio[header][TITULO]="Relatório de Ocorrências por Cliente";
			$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
			//$matrizRelatorio[header][cabecalho]=$matCabecalho;
			
			# Configurações
			$matrizRelatorio[config][layout]='portrait';
			$matrizRelatorio[config][marginleft]='1.0cm;';
			$matrizRelatorio[config][marginright]='1.0cm;';
			$matrizRelatorio[config][alinhamento]=$matAlinhamento;
			$matrizRelatorio[config][largura]=$matLargura;
			$matrizRelatorio['consulta']= $consSQL;
			$matrizRelatorio['POP']= $nomePop;
			
			//gera o PDF
			//instancio a classe de relatorios com o parametro da orientacao da pagina!
			$relatorio= new Ocorrencias2Pdf();
			//utilizando a classe chamamos o  metodo geraImpressao
			$nomeArquivo=$relatorio->geraImpressao($matrizRelatorio);
			
			novaTabela('[Arquivo Gerado]<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$nomeArquivo>TEste seu PDF aqui</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
				htmlFechaLinha();
			fechaTabela();
			//exibeLinkPdf("Relatório de Clientes por Serviços",$nomeArquivo);*/
			
			//FIM DO CODIGO PARA GERACAO DE  PDF VIA FPDF
			
			/*$c=0;
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
			$matResultado[$matCabecalho[$c++]][$l]='<b>Total Geral</b>';
			$matResultado[$matCabecalho[$c++]][$l]='<b>'.$ttRecebido.'</b>';
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
			$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';*/
			
			
			#Se for escolhido Consulta nao gera o pdf
			/*if (! $matriz[bntConfirmar]) {
				# Converter para PDF:
				$nome= "ocorrencias";
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
			}*/
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