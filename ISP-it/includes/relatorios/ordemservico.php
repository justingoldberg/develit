<?
################################################################################
#       Criado por: Hugo Ribeiro - hugo@devel-it.com.br
#  Data de criação: 04/05/2004
# Ultima alteração: 04/05/2004
#    Alteração No.: 001
#
# Função:
#      relatorio OS


function relatorioOrdemServico($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;
	
	if (!$registro) {
		
		echo "<br>";
		novaTabela("[Impressão da Ordem de Serviço]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 8);
			
		fechaTabela();
		
	} else {
		
		echo "<br>";
		novaTabela("[Impressão da Ordem de Serviço No. $registro]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 8);
			
		# Consultar OS
		$dados=dadosOrdemdeServico($registro);
		
		if($dados[id]) {
			
			# Linha detalhe da OS
			$consultaDetalhe=buscaRegistros($dados[id], 'idOrdemServico', 'igual', 'id', $tb[OrdemServicoDetalhe]); 

			if($consultaDetalhe && contaConsulta($consultaDetalhe)>0) {
				# Totalizar
				$matPendente=array();
				for($i=0;$i<contaConsulta($consultaDetalhe);$i++) {
					$id=resultadoSQL($consultaDetalhe, $i, 'id');
					$detos=dadosOrdemdeServicoDetalhe($id);
					
					$idProduto=resultadoSQL($consultaDetalhe, $i, 'idProduto');
					$idMaodeObra=resultadoSQL($consultaDetalhe, $i, 'idMaodeObra');
					if ($idProduto>0) 
						$tipo=$detos[nomeProduto];
					elseif ($idMaodeObra>0)
						$tipo=$detos[nomeMaodeObra];
						
					$quantidade=resultadoSQL($consultaDetalhe, $i, 'quantidade');
					$unitario=resultadoSQL($consultaDetalhe, $i, 'valor');
					$valor=$quantidade*$unitario;
					$aplicacao=$detos[nomeAplicacao];
					
					$matDetalhe["$id"][tipo] = $tipo;
					$matDetalhe["$id"][quantidade] = formatarValoresForm($quantidade);
					$matDetalhe["$id"][unitario] = formatarValoresForm($unitario);
					$matDetalhe["$id"][valor] = formatarValoresForm($valor);
					$matDetalhe["$id"][aplicacao] = $aplicacao;
				}
				
			}
			
			if(is_array($matDetalhe) && count($matDetalhe)>0) {
				
				$keys=array_keys($matDetalhe);
				$matResultado=array();
				$matCabecalho=array("Tipo", "Quantidade", "Unitário", "Valor", "Aplicação");
				
				$l=0;
				for($a=0;$a<count($keys);$a++) {
					
					$c=0;
					
					$matResultado[$matCabecalho[$c++]][$l]=$matDetalhe[$keys[$a]][tipo];
					$matResultado[$matCabecalho[$c++]][$l]=$matDetalhe[$keys[$a]][quantidade];
					$matResultado[$matCabecalho[$c++]][$l]=$matDetalhe[$keys[$a]][unitario];
					$matResultado[$matCabecalho[$c++]][$l]=$matDetalhe[$keys[$a]][valor];
					$matResultado[$matCabecalho[$c++]][$l]=$matDetalhe[$keys[$a]][aplicacao];
					
					$totalGeral+=$matDetalhe[$keys[$a]][valor];
					
					$l++;
					
				} #fecha laco de montagem de tabela
				
				# Cria linhas em branco para completar o relatorio
				for ($l; $l<15; $l++) {
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
				}
				
				if ($totalGeral>0) {
					# Alimentar Array de Detalhe com mais um campo - totais
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
					$matResultado[$matCabecalho[$c++]][$l]='<b>Total</b>';
					$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral).'</b>';
					$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
				}
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="ORDEM DE SERVIÇO";
				$matrizRelatorio[header][NUMERO]="Número: ".$registro;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Identificacao da OS
				$telefone=telefonesPessoasTipos($dados[idPessoaTipo]);
				$endereco=dadosEndereco($dados[idPessoaTipo]);
				
				$matrizRelatorio[header][CLIENTE]=$dados[Pessoa][nome];
				$matrizRelatorio[header][FONE]=$telefone;
				$matrizRelatorio[header][ENDERECO]=$endereco[enderecoCompleto];
				$matrizRelatorio[header][BAIRRO]=$endereco[bairro];
				$matrizRelatorio[header][CIDADE]=$endereco[cidade];
				$matrizRelatorio[header][ESTADO]=$endereco[uf];
				$matrizRelatorio[header][ACESSO]='&nbsp;';
				$matrizRelatorio[header][EMAIL]=$dados[Pessoa][email];
				$matrizRelatorio[header][REFERENCIA]=$dados[nome];
				$matrizRelatorio[header][VALOR]=$dados[valor];
				$matrizRelatorio[header][SERVICO]=$dados[nomeServico];
				$matrizRelatorio[header][PRIORIDADE]=$dados[nomePrioridade];
				
				
				# Configurações
				$matrizRelatorio[config][linhas]=20;
				$matrizRelatorio[config][layout]='portrait';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
								
				$matrizGrupo[]=$matrizRelatorio;
								
			}
			
			
			if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','ordemservico'),'ordemservico',$matrizRelatorio[config]);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Impressão de OS</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
			}
			else {
				# Não há registros
				itemTabelaNOURL('Não há registro disponível', 'left', $corFundo, 7, 'txtaviso');
			}
	
		}
		else {
			# Não há registros
			itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');
		}
			
		fechaTabela();
	}
}


?>
