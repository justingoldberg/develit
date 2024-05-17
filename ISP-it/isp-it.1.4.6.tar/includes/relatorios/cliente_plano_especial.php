<?
##################################################
#       Criado por: Rogério Ramos - Devel-it     #
#  Data de criação: 30/11/2004                   #
# Ultima alteração: 30/11/2004                   # 
#    Alteração No.: 001                          # 
#                                                #
# Função:                                        #########
# Funções para relatório de Clientes por Plano  Especial #
##########################################################

# função para form de seleção de filtros de faturamento
function formRelatorioClientePlanoEspecial($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[Clientes com Plano Especial]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			//Botao
			getBotoesConsRel();
			
			htmlFechaLinha();
		fechaTabela();
	}
	
}

#
# faz a consulta e o relatorio
#
function relatorioClientePlanoEspecial($modulo, $sub, $acao, $registro, $matriz) {

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
		
		// Prepara as variaveis de ajuste
		$pp=0;
		$totalGeral=array();
		$total=array();
		
		$matLargura=array(    '45%',      '40%',     '15%');
		$matCabecalho=array(  'Cliente',  'Serviços','Valor Plano');
		$matAlinhamento=array('left',     'left',    'right');
		$numCol=count($matCabecalho);
			
		$ttRecebido= 0;
		$l=0;
		
		while($matriz[pop][$pp]) {	
			// nome do pop para exbição
			$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			$sqlPOP="$tb[POP].id = ".$matriz[pop][$pp];
			
			// esta consulta retorna o id dos status dos servicos dos planos que devem sair no relatorio de clientes com plano especial
			$sql="SELECT id FROM StatusServicosPlanos where status= 'A'";
			$consulta= consultaSQL($sql, $conn);
			$idStatus= array();
			if( $consulta && contaConsulta($consulta)>0){
				for ( $i=0; $i<contaConsulta($consulta);$i++ )
					$idStatus[]=resultadoSQL($consulta,$i,'id');
			}
			
			
			$sql="SELECT Pop.nome pop, Pessoas.id idPessoa, Pessoas.nome cliente, Servicos.nome servico, Sum(ServicosPlanos.valor) valor 
							FROM (Pop 
							INNER JOIN (Pessoas 
							INNER JOIN ( PessoasTipos 
							INNER JOIN ( PlanosPessoas 
							INNER JOIN ( Servicos INNER JOIN ServicosPlanos 
			
							ON Servicos.id = ServicosPlanos.idServico)
							ON PlanosPessoas.id = ServicosPlanos.idPlano)
							ON PessoasTipos.id = PlanosPessoas.idPessoaTipo)
							ON Pessoas.id = PessoasTipos.idPessoa)
							ON Pop.id = Pessoas.idPop) ";
							$sql.="WHERE ".$sqlPOP;
							$sql.=" 
							AND UPPER(PlanosPessoas.especial)= 'S'
							AND UPPER(PlanosPessoas.status)= 'A' 
							AND ServicosPlanos.idStatus in (".implode( ",", $idStatus ).") 
							
							GROUP BY Pop.id,Pessoas.id, Servicos.id 
			
							ORDER BY Pop.nome, Pessoas.nome, Servicos.Nome";
					
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
				$valorPlano=0;
				$strServico=array();
				$idPessoa=0;
				$previousId=0;
				for($a=0;$a<contaConsulta($consulta);$a++) {
					
					$idPessoa= resultadoSQL($consulta, $a, 'idPessoa');
					
					if ( $idPessoa != $previousId ){
						
						$valorPlano+=resultadoSQL($consulta, $a, 'valor');
						$strServico[]=resultadoSQL($consulta, $a, 'servico');
						
						if ( $valorPlano > 0){
							$cc=0;
							$campos[$cc++]=resultadoSQL($consulta, $a, 'cliente');
							$campos[$cc++]= implode("; ", $strServico);
							$campos[$cc++]= number_format($valorPlano,2,",",".");
							
							
							$ttRecebido+=$campos[--$cc];
							
							# se for consulta exibe a linha detalhe
							if ($matriz[bntConfirmar]) {
								htmlAbreLinha($corFundo);
									for ($cc=0; $cc<count($campos); $cc++) {
										itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, "normal9");
									}
								htmlFechaLinha();
							}
							# soma na matriz
							for ($cc=0; $cc<count($campos); $cc++) {
								$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
							}
							$l++;
							$strServico=array();
							$valorPlano=0;
						} // fim if valor > 0
						else{
							$strServico=array();
							$valorPlano=0;
						}
					} // fim do if idPessoa != nextId
					else{
						$valorPlano+=resultadoSQL($consulta, $a, 'valor');
						$strServico[]=resultadoSQL($consulta, $a, 'servico');
					}
					
					$previousId= $idPessoa; //resultadoSQL($consulta, $a, 'idPessoa');
					
				}
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
						itemLinhaTMNOURL('<b>Total Clientes do POP</br>', 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 2, $zebra);
						$cc++;
						itemLinhaTMNOURL(number_format($ttRecebido,2,',','.'), 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, 'txtcheck');
					htmlFechaLinha();
					fechaTabela();
					$ttRecebido= 0;
				}

				$c=0;
				$matResultado[$matCabecalho[$c++]][$l]="<b>Total do POP $nomePop</b>";
				$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.number_format($ttRecebido,2,',','.').'</b>';
				
				$l++;
				
				$totalGeral[tgRecebido]+=$ttRecebido;
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="CLIENTES COM PLANO ESPECIAL";
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
		
		if(! $vazio) {
			
			# Alimentar Matriz Geral
			#### codigo para geracao de relatorio via FPDF
			/*
			$matrizRelatorio[detalhe]=$matDetalhe;
			
			# Alimentar Matriz de Header
			$matrizRelatorio[header][TITULO]="Relatório de Clientes com Plano Especial";
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
			exibeLinkPdf("Relatório de Clientes com Plano Especial",$nomeArquivo);
			
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
				$nome= "cliente_plano_especial";
				criaTemplates($nome,$matCabecalho,$matAlinhamento, $matLargura);
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html',$nome),$nome,$matrizRelatorio[config]);
				if ($arquivo) {
					echo "<br>";
					novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
						htmlAbreLinha($corfundo);
							itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Clientes com Plano Especial</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
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