<?
################################################################################
#       Criado por: Desenvolvimento - desenvolvimento@devel-it.com.br
#  Data de criação: 15/02/2007
# Ultima alteração: 00/00/0000
#    Alteração No.: 000
#
# Função:
# Funções para consultas


/**
 * Função para formulário de seleção de filtros de faturamento
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param string $registro
 * @param array $matriz
 */
function formRelatorioListarClienteEndereco($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar] ) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
		# Motrar tabela de busca
		novaTabela2("[Consulta de Cliente/Endereço por Faturamento]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			# Exibir o Faturamento 
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Faturamento:</b><br><span class=normal10>Selecione o faturamento ativo</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm( formSelectFaturamento( $registro, 'idFaturamento', 'form_descricao'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			# Exibir o Faturamento 
			novaLinhaTabela($corFundo, '100%');
				$texto = "<input type=submit name='matriz[bntConfirmar]' value='Confirmar' class='submit'>";
				itemLinhaTMNOURL( $texto, 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
		fechaTabela();
	}
	
}

/**
 * Função para consulta de Cliente/Endereço dum Faturamento especifico
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param string $registro
 * @param array $matriz
 */
function consultaListarClienteEndereco($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml;
	
	# Procedimentos:
	# 1) Buscar as Pessoas e seus respectivos endereços de cada faturamento
	# 2) Verificou se encontrou as pessoas
	# 3) Listagem das informações
	
	
	# 1)
	# Ordenado primeiramente pela cidade e posteriormente pela cidade, MAS não retira quando os valores são zeros.
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela2("[Resultados da Consulta]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Pesquisa NÃO elimina os valores zeros
//		$sql = "SELECT 
//					Pessoas.nome nome, 
//					Enderecos.endereco logradouro, 
//					Enderecos.bairro bairro, 
//					Cidades.nome cidade, 
//					Cidades.uf uf 
//				FROM 
//					DocumentosGerados 
//					INNER JOIN PessoasTipos ON ( DocumentosGerados.idPessoaTipo = PessoasTipos.id ) 
//					INNER JOIN Pessoas ON ( PessoasTipos.idPessoa = Pessoas.id ) 
//					INNER JOIN Enderecos ON ( PessoasTipos.id = Enderecos.idPessoaTipo ) 
//					INNER JOIN Cidades ON ( Enderecos.idCidade = Cidades.id ) 
//				WHERE 
//					DocumentosGerados.idFaturamento='$matriz[idFaturamento]' 
//				ORDER BY 
//					Cidades.nome ASC, 
//					Pessoas.nome ASC ";
		# Pesquisa elimina os valores zeros
		$sql = "SELECT 
					Pessoas.nome nome, 
					Enderecos.endereco logradouro, 
					Enderecos.bairro bairro, 
					Cidades.nome cidade, 
					Cidades.uf uf 
				FROM 
					PlanosDocumentosGerados, 
					ServicosPlanosDocumentosGerados, 
					Faturamentos, 
					DocumentosGerados 
					INNER JOIN PessoasTipos ON ( DocumentosGerados.idPessoaTipo = PessoasTipos.id ) 
					INNER JOIN Pessoas ON ( PessoasTipos.idPessoa = Pessoas.id ) 
					INNER JOIN Enderecos ON ( PessoasTipos.id = Enderecos.idPessoaTipo ) 
					INNER JOIN Cidades ON ( Enderecos.idCidade = Cidades.id ) 
				WHERE 
					DocumentosGerados.id=PlanosDocumentosGerados.idDocumentoGerado 
					AND PlanosDocumentosGerados.id=ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado 
					AND Faturamentos.id=DocumentosGerados.idFaturamento 
					AND Faturamentos.id='$matriz[idFaturamento]' 
				GROUP BY 
					DocumentosGerados.id 
				ORDER BY 
					Cidades.nome, 
					Pessoas.nome ";
		$consulta = consultaSQL( $sql, $conn );
		# 2)
		if( $consulta && contaConsulta( $consulta ) > 0 ){
			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL( _("Nome"), 			'left', 'middle', '30%', $corFundo, 0, 'bold' );
				itemLinhaTMNOURL( _("Logradouro/N°"), 	'left', 'middle', '30%', $corFundo, 0, 'bold' );
				itemLinhaTMNOURL( _("Bairro"), 			'left', 'middle', '20%', $corFundo, 0, 'bold' );
				itemLinhaTMNOURL( _("Cidade"), 			'left', 'middle', '17%', $corFundo, 0, 'bold' );
				itemLinhaTMNOURL( _("UF"), 				'left', 'middle', '3%',  $corFundo, 0, 'bold' );
			htmlFechaLinha();
			
			for ( $b=0; $b<contaConsulta( $consulta ); $b++){
				
				# 3)
				$nome 		= resultadoSQL( $consulta, $b, 'nome' );
				$logradouro = resultadoSQL( $consulta, $b, 'logradouro' );
				$bairro 	= resultadoSQL( $consulta, $b, 'bairro' );
				$cidade 	= resultadoSQL( $consulta, $b, 'cidade' );
				$uf 		= resultadoSQL( $consulta, $b, 'uf' );
				
				$resto = ( $b % 2 ) + 1;
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL( _( $nome ), 			'left', 'middle', '30%', $corFundo, 0, 'tabfundo'.$resto );
					itemLinhaTMNOURL( _( $logradouro ), 	'left', 'middle', '30%', $corFundo, 0, 'tabfundo'.$resto );
					itemLinhaTMNOURL( _( $bairro ),			'left', 'middle', '20%', $corFundo, 0, 'tabfundo'.$resto );
					itemLinhaTMNOURL( _( $cidade ),			'left', 'middle', '17%', $corFundo, 0, 'tabfundo'.$resto );
					itemLinhaTMNOURL( _( $uf ),				'left', 'middle', '3%',  $corFundo, 0, 'tabfundo'.$resto );
				htmlFechaLinha();
			} # fim do for
			
			$total = $b;
			$resto = ( $b++ % 2 ) + 1;
			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL( _( "Total" ), 'left', 'middle', '30%', $corFundo, 0, 'tabfundo'.$resto );
				itemLinhaTMNOURL( 		$total,	'left', 'middle', '70%', $corFundo, 4, 'tabfundo'.$resto );
			htmlFechaLinha();
			
		} # fim da consulta
		else {
			# Não há registros
			itemTabelaNOURL('Não encontrou os registros!', 'left', $corFundo, 2, 'txtaviso');
		}
	fechaTabela();
} //fim da funcao de relatorio

?>