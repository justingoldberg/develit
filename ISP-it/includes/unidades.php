<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/06/2003
# Ultima alteração: 24/10/2006
#    Alteração No.: 008
#
# Função:
#    Painel - Funções para cadastro de tipos de documentos

/**
 * Construtor do módulo unidades
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function unidades($modulo, $sub, $acao, $registro, $matriz){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permissão do usuario
	$permissao = buscaPermissaoUsuario( $sessLogin['login'],'login','igual','login' );
	
	if( !$permissao['admin'] ) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Cadastro de Unidades]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br /><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>Unidades</b>
					<br /><span class=normal10>Cadastro de <b>unidades</b>, utilizados para parâmetros e configurações de serviços.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br />Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br />Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br />Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		echo "<br />";
		# Inclusão
		if($acao=="adicionar") {
			adicionarUnidades($modulo, $sub, $acao, $registro, $matriz);
		}
		# Alteração
		elseif($acao=="alterar") {
			alterarUnidades($modulo, $sub, $acao, $registro, $matriz);
		}
//		# Exclusão
//		elseif($acao=="excluir") {
//			excluirUnidades($modulo, $sub, $acao, $registro, $matriz);
//		}
		# Busca
		elseif($acao=="procurar") {
			procurarUnidades($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca
		# Listar
		else {
			listarUnidades($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
	}


} #fecha menu principal 

/**
 * Gerencia a gravação de dados na tabela Unidades
 *
 * @param array  $matriz
 * @param string $tipo
 * @return unknown
 */
function dbUnidade( $matriz, $tipo )
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO ".$tb['Unidades']." VALUES ( 0, '".$matriz['unidade']."', '".$matriz['descricao']."', '".$matriz['status']."', '".$matriz['estoque']."')";
	} #fecha inclusao
	elseif( $tipo=='excluir' ) {
		# Verificar se a prioridade existe
		$tmpBusca = buscaUnidades( $matriz['id'], 'id', 'igual', 'id' );
		
		# Registro já existe
		if(!$tmpBusca|| contaConsulta( $tmpBusca ) == 0 ) {
			# Mensagem de aviso
			$msg="Registro não existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM ".$tb['Unidades']." WHERE id=".$matriz['id'];
		}
	}
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql = 	"UPDATE ".$tb['Unidades']." SET\n".
				"	descricao='".$matriz['descricao']."', unidade='".$matriz['unidade']."', status='".$matriz['status']."', estoque='".$matriz['estoque']."' \n".
				"WHERE id=".$matriz['id'];
	}
	elseif($tipo=='consultar') {
		# Verificar se prioridade existe
		$sql = 	"SELECT * FROM ".$tb['Unidades']." WHERE unidade='".$matriz['unidade']."' 
		AND id <>'".$matriz['id']."'"; 
	}
	if($sql) { 
		return( consultaSQL($sql, $conn) ); 
	}
	
} # fecha função de gravação em banco de dados

/**
 * Realiza a busca de Uniaddes
 *
 * @param unknown $texto
 * @param string  $campo
 * @param string  $tipo
 * @param string  $ordem
 * @return unknown
 */
function buscaUnidades($texto, $campo, $tipo, $ordem){
	global $tb;
	return buscaRegistros( $texto, $campo, $tipo, $ordem, $tb['Unidades']);
} # fecha função de busca

/**
 * Adiciona nova unidade
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function adicionarUnidades($modulo, $sub, $acao, $registro, $matriz) {
	global $corFundo, $corBorda, $html, $sessCadastro;

	if( $matriz['bntConfirmar'] ) {
		$erro = false;
		if( $sessCadastro[$modulo.$sub.$acao] || unidadesValida( $matriz, $acao ) ) {
			if( $sessCadastro[$modulo.$sub.$acao] || dbUnidade( $matriz, 'incluir' ) ) {
				$sessCadastro[$modulo.$sub.$acao] = "gravado";
				# acusar falta de parametros
				# Mensagem de aviso
				avisoNOURL("Aviso", "Registro Gravado com Sucesso!", 400);
				echo '<br />';
				listarUnidades( $modulo, $sub, 'listar', '', $matriz );
			}
			else {
				$erro = true;
			}
		}
		else {
			$erro = true;
		}
		// se ocorreu erro
		if( $erro ) {
			$msg =	"Não foi possível gravar os dados. Certifique-se se todos os dados foram preenchidos ".
					"corretamente ou se esta unidade já foi cadastrada!";
			avisoNOURL("Aviso:", $msg, 600);
			echo '<br />';
			unidadesForm( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		unidadesForm( $modulo, $sub, $acao, $registro, $matriz );
	}

} # fecha funcao de inclusao

/**
 * Exibe a listagem de unidades
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function listarUnidades($modulo, $sub, $acao, $registro, $matriz){
	global $corFundo, $corBorda, $html, $limite;

	# Cabeçalho
	$largura 				= array( '50%',			'10%',		'10%',		'10%',				'20%' );
	$gravata['cabecalho']   = array( 'Descrição',	'Unidade',	'Status',	'Un. de Estoque',	'Opções' );
	$gravata['alinhamento'] = array( 'left', 		'left',		 'center', 	'center',			'center' );
	
	$qtdColunas = count( $largura );
	
	# Motrar tabela de busca
	novaTabela( ( ( $acao == 'procurar' ) ? '[Procurar]' : '[Listagem de Unidades]' ), "center", '100%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
		menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, $qtdColunas );
		
	# Seleção de registros
	switch ( $acao ) {
		case 'listarativo':
			$consulta = buscaUnidades('A', 'status', 'igual', 'descricao');
			break;
		case 'listarinativo':
			$consulta = buscaUnidades('I', 'status', 'igual', 'descricao');
			break;
		case 'listarEstoque':
			$consulta = buscaUnidades('S', 'estoque', 'igual', 'descricao');
			break;
		case 'procurar':
			$consulta = buscaUnidades( "upper(descricao) like '%".$matriz['nome']."%' OR upper(unidade) like '%".$matriz['nome']."'", 
										$campo, 'custom','descricao' );
			break;
		default:
			$consulta = buscaUnidades($texto, $campo, 'todos','descricao');
			break;
	}
	
	$unidades = getArrayObjetos( $consulta );
	$totalUnidades = count( $unidades );
	
	if( $totalUnidades ) {
		if($acao == 'procurar' ) {
			itemTabelaNOURL('Registros encontrados procurando por "'.$matriz['nome'].'": '.$totalUnidades.' registro(s)', 
							'left', $corFundo, $qtdColunas, 'txtaviso' );	
		}
		paginador( $consulta, $totalUnidades, $limite['lista']['unidades'], $registro, 'normal10', $qtdColunas, '' );
		htmlAbreLinha( $corFundo );
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], 'center', 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
		htmlFechaLinha();

		# Setar registro inicial
		if( !$registro ) {
			$j = 0;
		}
		elseif( $registro && is_numeric($registro) ) {
			$j = $registro;
		}
		else {
			$j = 0;
		}
		
		$limite = $j + $limite['lista']['unidades'];
		
		while( ( $j < $totalUnidades ) && ( $j < $limite ) ) {
			// default da url
			$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $unidades[$j]->id;
			$opcoes = htmlMontaOpcao( $default . "&acao=alterar\">Alterar</a>", 'alterar' );
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $unidades[$j]->descricao, $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $unidades[$j]->unidade ,  $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formSelectStatusAtivoInativo( $unidades[$j]->status, "status", "check" ) , $gravata['alinhamento'][$i], 
								  'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formSelectSimNao( $unidades[$j]->estoque, "", "check" ), $gravata['alinhamento'][$i], 
								  'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
		
	}
	else {
		# Não há registros
		$mensagem = '<span class="txtaviso"><i>' . ( ( $acao == 'procurar' )
						? 'Não foram encontrados registros com a pesquisa especificada.' : 'Não há registros cadastrados.' ) . '</i></span>';
		htmlAbreLinha($corFundo);
			itemLinhaTMNOURL( $mensagem, 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
		htmlFechaLinha();

	}
	
} # fecha função de listagem

/**
 * Função para procura de serviço
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function procurarUnidades($modulo, $sub, $acao, $registro, $matriz){
	if( !$matriz['bntProcurar'] ){
		$matriz['nome'] = '';
	}
	getFormProcurar( $modulo, $sub, $acao, $matriz, "Unidades" );
	
	if( $matriz['nome'] && $matriz['bntProcurar'] ){
		listarUnidades( $modulo, $sub, $acao, $registro, $matriz );
	}

} #fecha funcao de  procurar 


/**
 * Realiza a ateração de unidades
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function alterarUnidades($modulo, $sub, $acao, $registro, $matriz){
	global $corFundo, $corBorda, $html;

	if( $matriz['bntConfirmar'] ) {
		$erro = false;
		$matriz['id'] = $registro;
		if( unidadesValida( $matriz, $acao ) ) {
			$matriz['id'] = $registro; // adiciona o $registro a matriz para que o mesmo possa ter alterado
			if( dbUnidade( $matriz, 'alterar' ) ) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				echo '<br />';
				listarUnidades( $modulo, $sub, 'listar', '', $matriz );
			}
			else {
				$erro = true;
			}
		}
		else {
			$erro = true;
		}
		// se ocorreu erro
		if( $erro ) {
			$msg =	"Não foi possível gravar os dados. Certifique-se se todos os dados foram preenchidos corretemente, ou se a unidade já está cadastrada";
			avisoNOURL("Aviso:", $msg, 600);
			echo '<br />';
			unidadesForm( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		// busca os dados da unidade cadastrado
		$conta = getArrayObjetos( buscaUnidades( $registro, 'id', 'igual', 'id' ) );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$dados = get_object_vars( $conta[0] );
			unidadesForm( $modulo, $sub, $acao, $registro, $dados );
		}
		else {
			avisoNOURL("Erro", "Não foi possível localizar a Unidade!", 400);
			echo "<br />";
			listarUnidades( $modulo, $sub, 'listar', '', $matriz );
		}
	}
	
} # fecha funcao de alteração


# Exclusão de servicos
function excluirUnidades($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro não foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaUnidades($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$id=resultadoSQL($consulta, 0, 'id');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$unidade=resultadoSQL($consulta, 0, 'unidade');
			
			# Motrar tabela de busca
			novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Descrição: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Unidade: </b>";
					htmlFechaColuna();
					itemLinhaForm($unidade, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
	
				# Botão de confirmação
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Alteração - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbUnidade($matriz, 'excluir');
				
		# Verificar inclusão de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro excluído com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 

/**
 * Exibe um select combo de unidades
 *
 * @param string  $unidade
 * @param unknown $campo
 * @param string  $tipo
 * @return string
 */
function formSelectUnidades($unidade, $campo, $tipo, $estoque=false ) {
	if($tipo=='form') {
		# mostrar formulário
		$condicao = "status='A'";
		$consulta = getArrayObjetos( ( $estoque 
						? buscaUnidades( "estoque='S' AND ".$condicao, '', 'custom', 'unidade') : buscaUnidades($condicao, '', 'custom', 'unidade') ) );

		if( count( $consulta ) ) {
			$retorno = "<select name=\"matriz[$campo]\">\n";
			foreach( $consulta as $un ) { # listar
				$retorno .= '<option value="'.$un->id.'"'.( $un->id == $unidade  ? ' selected="selected"' : '' ).'>' . 
							$un->unidade.' - '.$un->descricao."</option>\n";
			}
			$retorno .= "</select>";
		}
	}
	# checagem
	elseif($tipo=='check') {
		# Mostrar descricao
		$consulta=buscaUnidades($unidade, 'id','igual','id');
		
		$retorno = ( ( $consulta && contaConsulta($consulta) > 0 ) ? resultadoSQL($consulta, 0, 'unidade') : '&nbsp;' );
	}
	return( $retorno );
}

/**
 * Exibe o formulario de cadastro de Unidades
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function unidadesForm( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html;
	
	novaTabela2( "[".( $acao == "alterar" ? "Alterar" : "Adicionar" ).' Unidade]<a name="ancora"></a>', 
				 "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2 );
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		getCampo( '', '', '', '&nbsp;' );
		getCampo( 'text', 'Descrição', 'matriz[descricao]', $matriz['descricao'],'','',40, '', false, 'Descrição da unidade' );
		getCampo( 'text',  'Unidade',	'matriz[unidade]',  $matriz['unidade'], '', '', 10 );
		getCampo( 'combo', 'Status', "", 		formSelectStatusAtivoInativo($matriz['status'], "status", "form") );
		getCampo( 'combo', "Unidade de Estoque", '', formSelectSimNao( $matriz['estoque'], 'estoque', 'form' ) );
		getBotao( 'matriz[bntConfirmar]', 'Confirmar' );
		fechaFormulario();
	fechaTabela();
}

/**
 * Verifica se os dados da unidade estão validos
 *
 * @param array  $matriz
 * @param string $acao
 * @return boolean
 */
function unidadesValida( &$matriz, $acao ){
	$retorno = true;
	// verifca se unidade esta preenchida
	if( empty( $matriz['unidade'] ) ) {
		$retorno = false;
	}
	else {
		//verifica se esta unidadeo já não existe cadastrada
		( $acao == 'alterar' ? 
			$consulta = getArrayObjetos( dbUnidade($matriz, 'consultar') ):
			$consulta = getArrayObjetos( buscaUnidades( $matriz['unidade'], 'unidade', 'igual','id' ) ) );
		$retorno = verificaRegistroDuplicado( $consulta, $acao );
	}
	// verifica se a descricao não foi preenchida
	if( empty( $matriz['descricao'] ) ) {
		$retorno = false;
	}
	return $retorno;
}

?>