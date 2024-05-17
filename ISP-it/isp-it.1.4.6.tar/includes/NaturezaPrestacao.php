<?
################################################################################
#       Criado por: Lis
#  Data de criação: 25/05/2007
# Ultima alteração: 25/05/2007
#    Alteração No.: 1
#
# Função:
#    Natureza Prestação - Funções para cadastro, alteração e inativação de natureza da prestação
################################################################################


/**
 * Construtor de módulo Natureza de Prestação
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacao( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $sessLogin, $html;

	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin['login'],'login','igual','login');
	
	if(!$permissao['admin']) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		// sistema de permissao diferente. por funcao ao inves de modulo.
		$titulo    = "<b>Natureza da Prestação</b>";
		$subtitulo = "Cadastro de Natureza da Prestação utilizada no Cadastro de Nota Fiscal de Fatura de Serviço"; 
		$itens  = Array( 'Novo', 'Procurar', 'Listar' );
		getHomeModulo( $modulo, $sub, $titulo, $subtitulo, $itens );
		echo "<br />";
		
		if( $acao == 'adicionar' ) {
			NaturezaPrestacaoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'alterar' ) {
			NaturezaPrestacaoAlterar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'procurar' ) {
			NaturezaPrestacaoProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'ativar' ) {
			NaturezaPrestacaoAtivar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'inativar' ) {
			NaturezaPrestacaoInativar( $modulo, $sub, $acao, $registro, $matriz );
		}
		else {		
			NaturezaPrestacaoListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}	
}

/**
 * Função de gerenciamento da tabela Natureza de Prestação
 *
 * @return unknown
 * @param array   $matriz
 * @param string  $tipo
 * @param string  $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 */
function dbNaturezaPrestacao( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
		
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['NaturezaPrestacao'];
	$campos  = array( 	'id',		'descricao',			'codigo', 			'status' );
	$valores = array(	'NULL', 	$matriz['descricao'], 	$matriz['codigo'],	$matriz['status'] );
	if( $tipo == 'inserir' ){
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
	if( $tipo == 'alterar'){ 
		array_shift( $campos ); //retira o campo id da lista de campos
		array_shift( $valores ); //retira o elemento NULL da lista de valores
		if( $subTipo == 'status' ){
			$campos = array( 'status' );
			$valores = array( $matriz['status'] );
		}
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}
	if( $tipo == 'consultar' ){
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	return $retorno;
}

/**
 * Cadastra Natureza de Prestação para ser utilizada no cadastro de Nota 
 * Fiscal de Fatura Serviço
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacaoAdicionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro;
	
	if( $matriz['bntConfirmar'] ) {
		if( $sessCadastro[$modulo.$sub.$acao] || NaturezaPrestacaoValida( $matriz, $acao ) ){
			$matriz['status'] = 'A';
			if( $sessCadastro[$modulo.$sub.$acao] || dbNaturezaPrestacao( $matriz, 'inserir' ) ){ 
				$sessCadastro[$modulo.$sub.$acao] = "gravado";
				avisoNOURL("Aviso", "Natureza da Prestação adicionada com sucesso!", 400);
				echo "<br />";
				NaturezaPrestacaoListar( $modulo, $sub, 'listar', $registro, $matriz );
			}
			else {
				avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
				echo "<br />";
				NaturezaPrestacaoFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.  
						Ou verifique se código já foi cadastrado.", 420);
			echo "<br />";
			NaturezaPrestacaoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		NaturezaPrestacaoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Função que permite realizar a alteração de Natureza da Prestação cadastrada
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacaoAlterar( $modulo, $sub, $acao, $registro, $matriz ) {
	
	if( $matriz["bntConfirmar"] ){ 
	// se clicou no botão confirmar e os dados são validos
		if( NaturezaPrestacaoValida( $matriz, $acao ) ) {
			// grava os dados atualizados
			$gravar = dbNaturezaPrestacao( $matriz, 'alterar', "", "id='" . $registro . "'" ); 
			if( $gravar ) { // se gravou avisa que gravou com sucesso e exibe a listagem
				avisoNOURL( "Aviso", "Natureza da Prestação alterada com sucesso!", 400 );
				echo "<br>";
				NaturezaPrestacaoListar( $modulo, $sub, 'listar', '', $matriz );
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível gravar dados!", 400 );
				echo "<br>";
				NaturezaPrestacaoFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
			echo "<br />";
			NaturezaPrestacaoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}

	}
	else{
		// busca os dados da natureza da prestação cadastrada
		$conta = dbNaturezaPrestacao( '', "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz['descricao']  = $conta[0]->descricao;
			$matriz['codigo'] 	  = $conta[0]->codigo;
			$matriz['status']     = $conta[0]->status;
			$matriz['id']         = $conta[0]->id;
			NaturezaPrestacaoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar a Natureza da Prestação!", 400);
			echo "<br>";
			NaturezaPrestacaoListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}

/**
 * Permite a realizar consulta da Natureza da Prestação cadastrada 
 * pelo campo descrição
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacaoProcurar( $modulo, $sub, $acao, $registro, $matriz ) {
	if( !$matriz['bntProcurar'] ){
		$matriz['nome']='';
	}
	getFormProcurar( $modulo, $sub, $acao, $matriz, "Natureza da Prestação de Serviço" );

	if( $matriz['nome'] && $matriz['bntProcurar'] ){
		NaturezaPrestacaoListar( $modulo, $sub, $acao, $registro, $matriz );
	}	
}

/**
 * Exibe listagem das Naturezas de Prestação cadastradas
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacaoListar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $sessLogin, $limite, $tb;

	$largura 				= array( '42%',			'18%',		'15%',		'25%'   );
	$gravata['cabecalho']   = array( 'Descrição', 	'Código', 	'Status',	'Opções');
	$gravata['alinhamento'] = array( 'left', 		'center', 	'center',	'center');

	$qtdColunas = count( $largura );
	novaTabela("[Listagem de Natureza da Prestação]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
	
		menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, $qtdColunas);
	
		if( $acao == 'procurar' ) {
			$condicao = "{$tb['NaturezaPrestacao']}.descricao LIKE '%{$matriz['nome']}%'";
		}
		elseif( $acao == 'listar_ativos' ) {
			$condicao = "{$tb['NaturezaPrestacao']}.status = 'A'";
		}
		elseif( $acao == 'listar_inativos' ) {
			$condicao = "{$tb['NaturezaPrestacao']}.status = 'I'";
		}
		else {
			$condicao = '';
		}
		
		$naturezaPrestacao = dbNaturezaPrestacao( "", "consultar", "", $condicao, "{$tb['NaturezaPrestacao']}.descricao" );
	    $totalNaturezaPrestacao = count($naturezaPrestacao);
		
		if( $totalNaturezaPrestacao ){
			
			paginador( '', $totalNaturezaPrestacao, $limite['lista']['naturezaPrestacao'], $registro, 'normal10', $qtdColunas, '' );

			htmlAbreLinha($corFundo);
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
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

			$limite = $j + $limite['lista']['naturezaPrestacao'];

			while( ( $j < $totalNaturezaPrestacao ) && ( $j < $limite ) ) {

				$default = '<a style="font-size:11px" href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $naturezaPrestacao[$j]->id;
				$opcoes = htmlMontaOpcao( $default . "&acao=alterar\">Alterar</a>", 'alterar' );
				
				// realiza as opções conforme os status 
				if( $naturezaPrestacao[$j]->status == 'A' ) {
					$opcoes.= htmlMontaOpcao( $default . "&acao=inativar&matriz[idNaturezaPrestacao]=" . $naturezaPrestacao[$j]->id . 
						"\">Inativar</a>", 'desativar' );
				}
				else {
					$opcoes .= htmlMontaOpcao( $default . "&acao=ativar&matriz[idNaturezaPrestacao]=" . $naturezaPrestacao[$j]->id . 
						"\">Ativar</a>", 'ativar' );
				}
				
				$i = 0;
				htmlAbreLinha( $corFundo );
					itemLinhaTMNOURL( $naturezaPrestacao[$j]->descricao ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
					itemLinhaTMNOURL( $naturezaPrestacao[$j]->codigo ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
					itemLinhaTMNOURL( formSelectStatusAtivoInativo( $naturezaPrestacao[$j]->status, "status", "check" ), $gravata['alinhamento'][$i],'middle', $largura[$i++], $corFundo, 0, 'txtaviso' );
					itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				htmlFechaLinha();
				$j++;
			}
		}
		else {
			htmlAbreLinha($corFundo);
			itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhuma Natureza da Prestação encontrada!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
			htmlFechaLinha();
		}
	fechaTabela();	
}

/**
 * Exibe formulário para cadastro ou alteração da Natureza da Prestação
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacaoFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	novaTabela2( '['.( $acao == 'adicionar' ? 'Adicionar' : 'Alterar' ).' Natureza da Prestação]<a name="ancora"></a>', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			$extraItem 		= array( 'matriz[id]', 'matriz[status]' );
			$extraConteudo	= array( $matriz['id'], $matriz['status'] );
			getCamposOcultos( $extraItem, $extraConteudo );
			getCampo('', '', '', '&nbsp;');
			#primeiro filtra o produto
			getCampo( 'text', _('Descrição'), 'matriz[descricao]',  $matriz['descricao'],'onblur="verificaTamanho(this.name, this.value, 21)"','',25,'','','Máx. 21 caracteres.' );
			getCampo( 'text',  _('Código'),	  'matriz[codigo]', $matriz['codigo'], 
					'', '', 10 ) ;
			getBotao( 'matriz[bntConfirmar]', 'Confirmar' );
		fechaFormulario();
	fechaTabela();
}

/**
 * Permite a ativação da Natureza da Prestação cadastrada
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacaoAtivar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if( $matriz["bntAtivar"] ){ 
	// se clicou no botão ativar
	// ativar a Natureza da Prestação
		$matriz['status'] = 'A';
		if( dbNaturezaPrestacao( $matriz, 'alterar', 'status', "id='" . $registro . "'" ) ) { // se gravou avisa que gravou com sucesso e exibe a listagem
			avisoNOURL( "Aviso", "Natureza da Prestação ativada com sucesso!", 400 );
			echo "<br>";
			$sessCadastro = '';
			NaturezaPrestacaoListar( $modulo, $sub, 'listar', '', array() );
		}
		else { // senão avisa que teve um erro e exibe o formulario de exclusão
			avisoNOURL( "Erro", "Não foi possível ativar a Natureza da Prestação!", 400 );
			echo "<br>";
			NaturezaPrestacaoVer( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
	// busca os dados do produto cadastrado
		$conta = dbNaturezaPrestacao( '', "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz['descricao']= $conta[0]->descricao;
			$matriz['codigo']		= $conta[0]->codigo;
			$matriz['status'] 	= $conta[0]->status;
			NaturezaPrestacaoVer( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar a Natureza da Prestação!", 400);
			echo "<br>";
			NaturezaPrestacaoListar( $modulo, $sub, 'listar', '', $matriz );
		}
	}
}

/**
 * Permite a inativação da Natureza da Prestação cadastrada
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacaoInativar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if( $matriz["bntInativar"] ){ 
	// se clicou no botão ativar
	// ativar a Natureza da Prestação
		$matriz['status'] = 'I';
		if( dbNaturezaPrestacao( $matriz, 'alterar', 'status', "id='" . $registro . "'" ) ) { // se gravou avisa que gravou com sucesso e exibe a listagem
			avisoNOURL( "Aviso", "Natureza da Prestação inativada com sucesso!", 400 );
			echo "<br>";
			$sessCadastro = '';
			NaturezaPrestacaoListar( $modulo, $sub, 'listar', '', array() );
		}
		else { // senão avisa que teve um erro e exibe o formulario de exclusão
			avisoNOURL( "Erro", "Não foi possível inativar a Natureza da Prestação!", 400 );
			echo "<br>";
			NaturezaPrestacaoVer( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
	// busca os dados do produto cadastrado
		$conta = dbNaturezaPrestacao( '', "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz['descricao']= $conta[0]->descricao;
			$matriz['codigo']		= $conta[0]->codigo;
			$matriz['status'] 	= $conta[0]->status;
			NaturezaPrestacaoVer( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar a Natureza da Prestação!", 400);
			echo "<br>";
			NaturezaPrestacaoListar( $modulo, $sub, 'listar', '', $matriz );
		}
	}
}

/**
 * Permite a visualização dos dados da Natureza da Prestação cadastrada
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NaturezaPrestacaoVer( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	novaTabela2("[" . ( $acao  == "ativar" ? "Ativar" : "Inativar" ) . " Natureza da Prestação]" . "<a name='ancora'></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		menuOpcAdicional( $modulo, $sub, 'ver', $registro, $matriz, 2 );
		getCampo('', '', '', '&nbsp;');
		getCampo( 'combo', _('Descrição'), '', 	$matriz['descricao'] );
		getCampo( 'combo', _('Código'), '', 	$matriz['codigo'] );
		getCampo( 'combo', _("Status"), "", formSelectStatusAtivoInativo( $matriz['status'], "status", "check") );
		if ( $acao == 'ativar' ){
			getBotao('matriz[bntAtivar]', 'Ativar', 'submit','button', 'onclick="window.location=\'?modulo='.$modulo.
				     '&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[bntAtivar]=ativar\'"');
		}
		else{
			getBotao('matriz[bntInativar]', 'Inativar', 'submit','button', 'onclick="window.location=\'?modulo='.$modulo.
				     '&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[bntInativar]=Inativar\'"');
		}
	fechaTabela();	
}

/**
 * Verifica os dados preenchidos para cadastro ou alteração da Natureza da Prestação
 *
 * @param array $matriz
 * @param string $acao
 * @return boolean
 */
function NaturezaPrestacaoValida( $matriz, $acao ) {
	$retorno = true;
	//verifica se este codigo da prestacao já não existe cadastrada
	$consulta = dbNaturezaPrestacao( '', 'consultar', '', "codigo='". intval( $matriz['codigo'] )."' AND id <>'". intval( $matriz['id'] )."'" );
	$retorno = verificaRegistroDuplicado( $consulta, $acao );
	if( empty($matriz['codigo']) || !is_numeric( formatarValores( $matriz['codigo']) ) ){
		$retorno = false;
	}
	if( !$matriz['descricao'] ){
		$retorno = false;
	}
	return $retorno;
}

# Formulário de seleção de Tipo de Endereço
function formSelectNatPrestacao($id, $campo, $tipo, $adic='', $evento='') {

	global $tb, $conn;
	
	if( $tipo=='form' ){

		$sql="SELECT * FROM $tb[NaturezaPrestacao] WHERE status='A' ORDER BY descricao";
		$consulta=consultaSQL($sql, $conn);
		
		if( $consulta && contaConsulta($consulta)>0 ){

			$retorno ="<select name=\"matriz[$campo]\" style=\"width:300px\" $evento>";
			$retorno.= $adic;
			for( $a=0; $a<contaConsulta($consulta); $a++ ){
				$tmpID = resultadoSQL($consulta, $a, 'id');
				$tmpNome = resultadoSQL($consulta, $a, 'descricao');
				$tmpCodigo = resultadoSQL($consulta, $a, 'codigo');
			
				if( $id && $tmpID==$id ){
					$opcSelect=' selected="selected"';
				}
				else{
					$opcSelect='';
				}
				
				$retorno.="<option value=\"$tmpID\" $opcSelect>$tmpNome : $tmpCodigo</option>\n";
			}
	
			$retorno.="</select>";
		}
	}
	elseif($tipo=='multi') {

		$sql="SELECT * FROM $tb[NaturezaPrestacao] WHERE status='A' ORDER BY descricao";
		$consulta=consultaSQL($sql, $conn);
		
		if( $consulta && contaConsulta($consulta)>0 ){

			$retorno="<select multiple size=6 name=matriz[$campo][]>";
			
			for( $a=0; $a<contaConsulta($consulta); $a++){
				$tmpID=resultadoSQL($consulta, $a, 'id');
				$tmpNome = resultadoSQL($consulta, $a, 'descricao');
				$tmpCodigo = resultadoSQL($consulta, $a, 'codigo');
			
				if( $id ){
					if( array_search($tmpID, $id) ){
						$opcSelect='selected';
					}
					else{
						$opcSelect='';
					}
				}
				
				$retorno.="<option value=$tmpID>$tmpNome : $tmpCodigo</option>\n";
			}
	
			$retorno.="</select>";
		}
	}
	elseif( $tipo=='check'){
	
		$consulta = dbNaturezaPrestacao( $matriz, 'consultar','',"id = $id");
		if( $consulta && count($consulta)>0 ){
			$retorno = $consulta[0]->descricao."<b class=bold10>  Cód.: </b>".$consulta[0]->codigo ;
		}
		else{
			$retorno='Natureza da Prestação inválida!';
		}
	}

	return $retorno;
	
}


?>