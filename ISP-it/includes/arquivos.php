<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 03/02/2004
# Ultima alteração: 11/03/2004
#    Alteração No.: 002
#
# Função:
#    Funções para leitura de arquivos

# Função para ler matriz de dados de
function arquivoLerDados($origem, $tipo, $linha, $coluna, $tamanho) {
	
	if($tipo=='matriz') {
		$coluna--;
		return(substr($origem[$linha],$coluna,$tamanho));
	}
}

/**
 * Baseado no caminho esficificado em $arquivo, esta função abre arquivo com no $modo. Caso não 
 * consiga, ele retorna false. Do contrário, é retornado um array contendo:
 *     handler => conteudo do arquivo
 *     size => tamanho do arquivo (em bytes)
 *
 * @param unknown_type $arquivo
 * @param unknown_type $modo
 * @return unknown
 */
function k_fileOpen( $arquivo, $modo ) {

	$handler = @fopen( $arquivo, $modo );
	
	if( $handler ) {
		$retorno['handler']	= $handler;
		$retorno['size']	= @filesize($arquivo);
	}
	else {
		$retorno = false;
	}
	
	return( $retorno );
}


function k_fileRead($handler) {

	if(is_array($handler)) {
		$retorno=fread($handler[handler], $handler[size]);
	}
	
	return($retorno);
}


function arquivoQuebrarColunas($linhas, $matriz) {
	
	global $tb, $conn;
	
	$data=dataSistema();
	
	if(is_array($linhas)) {
		
		$linha=0;
		
		for($a=0;$a<count($linhas);$a++) {
			# Quebrar a linha em colunas
			$tmpLinha=$linhas[$a];
			
			$tmpColunasLinha=explode($matriz[separador],$tmpLinha);

			if(is_array($tmpColunasLinha) && count($tmpColunasLinha)>1) {
				if($matriz[debug]=='S') echo "Gravando linha $a<br>";

				# Identificar colunas
				for($b=0;$b<count($tmpColunasLinha);$b++) {
					
					if($a==0) {
						# titulos
						$titulo[]=$tmpColunasLinha[$b];

						
						if($matriz[debug]=='S') echo "$b: $tmpColunasLinha[$b]<br>";
					}
					else {
						$conteudo[$linha][$titulo[$b]]=$tmpColunasLinha[$b];
						
						if($matriz[debug]=='S') echo "$titulo[$b]: $tmpColunasLinha[$b]<br>";
					}
				}

				if($matriz[debug]=='S') echo "<hr>";
				$linha++;
			}
		}
	}

	# Verificar se foi confirmada a gravação dos dados
	if($matriz[importar]=='S') {

		/* Colunas
					/* Colunas
			0: tipoPessoa
			1: nome
			2: email
			3: dtNascimento
			4: contato
			 : valorTipoPessoa  (char)
			 : tipoEndereco (char)
			5: cidade
			6: uf
			7: endereco
			8: bairro
			9: cep
			  : caixa_postal
			10: fone1
			11: fone2
			12: fax
		//	13: rg
			14: documento
		//	15: ie
			  : vencimento
\			  : nomePlano
			  : especial
			  : statusPlano
			16: nomeServico
			17: valorServico
			18: dtCadastro
			19: dtAtivacao
			  : dtInativacao
			  : dtCancelamento
			
			$coluna[pessoaTipo]=0
			$coluna[nome]=1
			$coluna[razao]
			$coluna[site]
			$coluna[email]=2
			$coluna[dtNascimento]=3
			$coluna[dtCadastro]=$data[dataBanco]
			$coluna[pop]=verificar pop
			$coluna[contato]=1
		*/
			
		$contadorImportados=0;

		for($a=1;$a<=count($conteudo);$a++) {
			$conteudo[$a][nomePop] = 'SCM';
			$conteudo[$a][valorTipoPessoa] = 'cli';
			
			$dadosGravar[id]=buscaIDNovoPessoa();
			//$dadosGravar[pessoaTipo]=DBManagerImportarTipoPessoa($conteudo[$a][$titulo[0]]);
			$dadosGravar[pessoaTipo]=$conteudo[$a][tipoPessoa];
			$dadosGravar[nome]=$conteudo[$a][nome];
			if($dadosGravar[pessoaTipo]=='J') $dadosGravar[razao]=$conteudo[$a][nome];
			else $dadosGravar[razao]='';
			$dadosGravar[email]=$conteudo[$a][mail];
			$dadosGravar[dtNascimento]=converteData($conteudo[$a][dtNascimento],'banco','formdata');
			$dadosGravar[dtCadastro]=$data[dataBanco];
			$dadosGravar[contato]=$conteudo[$a][contato];
			
			$dadosPop = buscaPOP($conteudo[$a][nomePop], 'nome', 'igual', 'id');			
			$dadosGravar[pop]=resultadoSQL($dadosPop, 0, 'id');
			
			
			// Buscar pessoa para identificar se já não foi importada
			$buscaPessoa=buscaPessoas("upper(nome) like  '%$dadosGravar[nome]%' AND idPOP = $dadosGravar[pop]", '','custom','id');
			if(contaConsulta($buscaPessoa)==0) {

				// Adicionar Pessoa
				dbPessoa($dadosGravar, 'incluir');
			
				// Adicionar PessoaTipo
				$dadosGravar[idPessoaTipo]=buscaIDNovoPessoaTipo();
				$dadosGravar[id]=$dadosGravar[id];
				$dadosGravar[dtCadastro]=$data[dataBanco];
				$dadosGravar[tipoPessoa]=$conteudo[$a][valorTipoPessoa];  //meu deus... 
				dbPessoaTipo($dadosGravar, 'incluir');
			}
			// caso ja tenha o cabloco cadastrado... so pega o id do bixo...
			else {
				$dadosGravar[idPessoaTipo] = resultadoSQL($buscaPessoa, 0, 'idPessoaTipo');
				$dadosGravar[idPessoa] = resultadoSQL($buscaPessoa, 0, 'id');
				avisoNOURL("ops", "Usuario ". $dadosGravar[nome]. "ja cadastrado", '100%' );
			}
			
###############################################################################################################			
			
			// Adicionar Endereço para pessoa
			$dadosGravar[tipoEndereco]=$conteudo[$a][tipoEndereco];
			$dadosGravar[cidade]=buscaIDCidade($conteudo[$a][cidade], $conteudo[$a][uf]);
			$dadosGravar[endereco]=$conteudo[$a][endereco]. ','. $conteudo[$a][numero];
			$dadosGravar[complemento]='';
			$dadosGravar[bairro]=$conteudo[$a][bairro];
			$dadosGravar[cep]=$conteudo[$a][cep];
			$dadosGravar[pais]="Brasil";
			$dadosGravar[caixaPostal]=$conteudo[$a][caixa_postal];
				
			# Verificação de Fones
			$dadosGravar[fone1]=$conteudo[$a][fone1];
			$dadosGravar[ddd_fone1]=$conteudo[$a][ddd_fone1];
			$dadosGravar[fone2]=$conteudo[$a][fone2];
			$dadosGravar[ddd_fone2]=$conteudo[$a][ddd_fone2];
			$dadosGravar[fax]=$conteudo[$a][fax];
			$dadosGravar=DBManagerImportarFones($dadosGravar);
			
			//verificar se endereco ja nao foi cadastrado
			$condicao = "endereco like '%" . $conteudo[$a][endereco].",". $conteudo[$a][numero]. "%' and idPessoaTipo = $dadosGravar[idPessoaTipo]"; 
			$buscaEndereco = buscaEnderecosPessoas($condicao, '','custom','idPessoaTipo');
			if(contaConsulta($buscaEndereco) == 0 ){
				dbEndereco($dadosGravar, 'incluir');
			}	else
				avisoNOURL("ops", "Endereco do Usuario ". $dadosGravar[nome]. "ja cadastrado", '100%' );
			
######################################################################################################################			
			
			// Adicionar Documento
			//$dadosGravar[rg]=$conteudo[$a][$titulo[13]];
			
			$dadosGravar[documento]=$conteudo[$a][documento];
			$dadosGravar=DBManagerImportarDocumentos($dadosGravar);
			
			//verifica se já não foi inserido tal documento
			$buscaDocumento=buscaDocumentosPessoas("documento = '$dadosGravar[documento]' and idPessoa = '$dadosGravar[id]' ", '', 'custom', 'dtCadastro');
			if (contaConsulta($buscaDocumento)==0)
				dbDocumento($dadosGravar, 'incluir');
			else
				avisoNOURL("ops", "Documento do Usuario ". $dadosGravar[nome]. "ja cadastrado", '100%' );
				
###################################################################################################			
			
			// Adicionar Planos e Serviços
			//idvencimento
			$dtVencimento = buscaVencimentos($conteudo[$a][diaVencimento], 'diaVencimento', 'igual', 'id');
			if (contaConsulta($dtVencimento)>0)
				$idVencimento = resultadoSQL($dtVencimento, 0, 'id');
			else{
				$dtVencimento = buscaVencimentos('', '', 'todos', 'id');
				$idVencimento = resultadoSQL($dtVencimento, 0, 'id');
			}
			
	
			//idFormaCobranca
			$formaCobranca = buscaFormaCobranca($conteudo[$a][descricaoFormaCobranca], 'descricao', 'igual', 'id');
			if (contaConsulta($formaCobranca)>0)
				$idFormaCobranca = resultadoSQL($formaCobranca, 0, 'id');
			else{
				$formaCobranca = buscaFormaCobranca('', '', 'todos', 'id');
				$idFormaCobranca = resultadoSQL($formaCobranca, 0, 'id');
			}
			
			$dadosGravar[idPlano]=buscaNovoID($tb[PlanosPessoas]);
			$dadosGravar[vencimento]=$idVencimento;
			$dadosGravar[forma_cobranca]=$idFormaCobranca;
			$dadosGravar[dtCadastro]=$conteudo[$a][dtCadastroPlanosPessoas];
			$dadosGravar[dtCancelamento]=$conteudo[$a][dtCancelamentoPlanosPessoas];
			$dadosGravar[nome]=$conteudo[$a][nomePlano];
			$dadosGravar[especial]=$conteudo[$a][especial];
			$dadosGravar[status]=$conteudo[$a][statusPlano];
			
			//verifica se ja nao foi inserido o plano.
			$condicao = "idPessoaTipo = '$dadosGravar[idPessoaTipo]' AND nome='$dadosGravar[nome]'";
			$buscaPlano = buscaPlanos($condicao, '', 'custom', 'id');
			if (contaConsulta($buscaPlano)==0)
				dbPlano($dadosGravar, 'incluirplano');
			else{
				$dadosGravar[idPlano] = resultadoSQL($buscaPlano, 0, 'id');
				avisoNOURL("ops", "Plano do Usuario ". $dadosGravar[nome]. "ja cadastrado", '100%' );
			}	
			
			// Serviços ao Plano - ID 24 //pegando id do nome do servico que esta  na matriz
			$servicos = buscaServicos($conteudo[$a][nomeServico], 'nome', 'igual', 'id');
			$idServico = resultadoSQL($servicos, 0, 'id');
			
			$dadosGravar[idServico]= $idServico;
			$dadosGravar[valor]=$conteudo[$a][valorServico];
			$dadosGravar[dtCadastro]=$conteudo[$a][dtCadastroServicosPlanos];
			$dadosGravar[dtAtivacao]=$conteudo[$a][dtAtivacaoServicosPlanos];
			$dadosGravar[dtInativacao]=$conteudo[$a][dtInativacaoServicosPlanos];
			# Verificar Status
//			if($dadosGravar[status]=='A')  {
//					$iAtivos++;
//					$totalAtivos+=$dadosGravar[valor];
//					$dadosGravar[status]=4;
//			}
//			else {
//				$iInativos++;
//				$totalInativos+=$dadosGravar[valor];
//				$dadosGravar[status]=6;
//			}

			if ( $conteudo[$a][idStatusServicosPlanos] == 2)
				$dadosGravar[status] = 4;
			else
				$dadosGravar[status] = 7;

			$dadosGravar[trial]=0;
			dbServicosPlano($dadosGravar, 'incluir');

			$contadorImportados++;
		}
			
		// Zerar variável
		$dadosGravar=array();
			
		
		
		$linha-=2;
		
		echo "<br>";
		avisoNOURL("Importação", "Dados Importados com sucesso<br>
		Total de cadastros lidos: $linha cadastros<br>
		Total de cadastros importados: $contadorImportados cadastros<br>
		Ativos: $iAtivos - Valor: $totalAtivos<br>
		Inativos: $iInativos - Valor: $totalInativos", 500); 
		
	}
	
}

?>
