<?
################################################################################
#       Criado por: Alexandre de O. A. Cintra - alexandre@devel.it
#  Data de criação: 12/09/2006
# Ultima alteração: 12/09/2006
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastro de Pops pessoas tipos (para cadastrar 
#	 uma pessoa e um endereço)


/**
 * Gerencia a tabela PopPessoaTipo
 *
 * @param array   $matriz
 * @param unknown $tipo
 * @param unknown $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return unknown
 */
function dbPopPessoaTipo($matriz, $tipo, $subTipo='', $condicao='', $ordem = '') {
	global $conn, $tb;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	$tabelas = $tb['PopPessoaTipo'];
	$campos  = array( 'idPop', 'idPessoaTipo' );
	
	if ($tipo == 'inserir'){
		$valores = array( $matriz['idPop'], $matriz['idPessoaTipo'] );
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
	
	if ($tipo == 'consultar'){
		$retorno = $bd->seleciona($tabelas, $campos, $condicao, '', $ordem);
	}
	
	return ($retorno);
}


function PopPessoaTipoCadastro($modulo, $sub, $acao, $registro, $matriz) {
	$sessCadastro = array();
	$matriz[tipoPessoa] = 'pop';
	//se confirmou a alteração e validou inicia o processo de gravação
	if ( $matriz['bntConfirmarPessoa'] && $matriz[cnpj] && $matriz[endereco] ){

		// define a id da Pessoa
		$matriz[id]=buscaIDNovoPessoa();
		//data do cadastro
		$data=dataSistema();
		$matriz[dtCadastro]=$data[dataBanco];
		
		// se gravou a pessoa tenta gravar a PessoaTipo
		if( dbPessoa( $matriz, 'inserir' ) ) {
			
			$matriz[idPessoaTipo]=buscaIDNovoPessoaTipo();
			
			// se gravou a pessoa tipo tenta gravar o documento
			if( dbPessoaTipo($matriz, 'inserir') ) {
				
				// se gravou o documento tenta gravar o endereço
				$matrizDocumento[id]			= $matriz[id];
				$matrizDocumento[idPessoa]		= $matriz[id];
				$matrizDocumento[pessoaTipo]	= 'J';
				$matrizDocumento[tipoDocumento] = 'cnpj';
				$matrizDocumento[cnpj]			= $matriz[cnpj];
				$matrizDocumento[dtCadastro]	= $matriz[dtCadastro];
				if( dbDocumento($matrizDocumento, 'incluir') ) {
					
					// se gravou o endereço, entao grava o pop pessoa tipo
					if( dbEndereco($matriz, 'inserir') ) {
						
						if( dbPopPessoaTipo( $matriz, 'inserir' ) ){		
							$url="?modulo=$modulo&sub=$sub&acao=listar";		
							aviso('Aviso', 'Pop Pessoa Tipo cadastrado com sucesso!', $url, 400);
							echo '<br>';
						}
						else{
							# Excluir pessoa
							dbPessoa($matriz, 'excluir');
							# Excluir o documento
							dbDocumento($matrizDocumento, 'excluir');
							# E tambem a pessoa tipo
							$matrizPessoaTipo[id] = $matriz[idPessoaTipo];
							dbPessoaTipo($matrizPessoaTipo, 'excluir');
							# E também o endereço 
							dbEndereco($matrizPessoaTipo, 'excluirtodos');
							$msg="Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Pop Pessoa";
							$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[idPop]";
							aviso("Aviso", $msg, $url, 760);								
						}
					}
					else {
						# Excluir pessoa
						dbPessoa($matriz, 'excluir');
						# Excluir o documento
						dbDocumento($matrizDocumento, 'excluir');
						# E tambem a pessoa tipo
						$matrizPessoaTipo[id] = $matriz[idPessoaTipo];
						dbPessoaTipo($matrizPessoaTipo, 'excluir');
						$msg="Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Endereço";
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[idPop]";
						aviso("Aviso", $msg, $url, 760);						
					}
				}
				else {
					# Excluir pessoa
					dbPessoa($matriz, 'excluir');
					# E tambem a pessoa tipo
					$matrizPessoaTipo[id] = $matriz[idPessoaTipo];
					dbPessoaTipo($matrizPessoaTipo, 'excluir');
					$msg="Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Documento";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[idPop]";
					aviso("Aviso", $msg, $url, 760);
				}
				
			}
			else {
				# Excluir pessoa
				dbPessoa($matriz, 'excluir');							
				$msg="Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Pessoa Tipo".mysql_error();
				$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[idPop]";
				aviso("Aviso", $msg, $url, 760);
			}

		}
		// se ele retorna o aviso
		else {
			avisoNOURL('Aviso', 'Erro ao gravar os dados!', 400);
			echo '<br>';
			PopPessoaTipoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		PopPessoaTipoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	} 
	
}

function PopPessoaTipoFormulario($modulo, $sub, $acao, $registro, $matriz) {
	echo "<br>";
	verPop($modulo, $sub, 'ver', $matriz[idPop], $matriz );
	echo '<br>';
	$extraItem		= array('matriz[idPop]',	'matriz[tipoPessoa]');
	$extraConteudo	= array($matriz[idPop],		'pop');
	abreFormularioComCabecalho( $modulo, $sub, $acao, $registro, $extraItem, $extraConteudo );
	formDadosPessoaPop($modulo, $sub, $acao, $registro, $matriz);
	formPessoaEndereco($modulo, $sub, $acao, $registro, $matriz);
	getBotao('matriz[bntConfirmarPessoa]', ( $acao == 'alterar' ? 'Alterar' : 'Cadastrar' ) );
	fechaFormulario();
}

/**
 * Verifica se os dados do PopPessoaTipo estão corretos
 *
 * @param array $matriz
 * @return boolean
 */
function PopPessoaTipoValida( $matriz ) {
	$retorno = true;
	
	# verifica a razao social
	if(!$matriz[razao]) $retorno = false;
	
	# verifica se o cnpj está preenchido
	if(!$matriz[cnpj]) {
		$retorno = false;
	}
	else {
		if(!validaCNPJ($matriz[cnpj])) $retorno = true;
	}
	
	return $retorno;
}

/**
 * Exibe um menu quando tem pessoa tipo
 *
 * @param unknown_type $modulo
 * @param unknown_type $sub
 * @param unknown_type $acao
 * @param unknown_type $registro
 * @param unknown_type $matriz
 */
function exibePopPessoaTipoMenu($modulo, $sub, $acao, $registro, $matriz="", $tamanho=2) {
	global $corFundo, $tb, $conn;
	
	$opcoes = '';
	
	# Seleção de registros
	$sql = "SELECT 
				$tb[POP].*, 
				$tb[PopPessoaTipo].idPessoaTipo,
				$tb[PessoasTipos].idPessoa
			FROM $tb[POP] 
				INNER JOIN $tb[PopPessoaTipo] 
					ON ($tb[POP].id = $tb[PopPessoaTipo].idPop)
				INNER JOIN $tb[PessoasTipos]
					ON ($tb[PopPessoaTipo].idPessoaTipo = $tb[PessoasTipos].id)
			WHERE
				$tb[POP].id='$registro'		
			ORDER BY $tb[POP].nome
	";
	$consulta = consultaSQL($sql, $conn);
	#recolhe as ids
	if(contaConsulta($consulta)) {	
		$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
		$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
	
		#exibe os menus
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$idPessoaTipo>Enderecos</a>",'endereco');
		$opcoes.="&nbsp;";
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$idPessoaTipo:$idPessoa&matriz[idPop]=$id>Documentos</a>",'documento');
	}
	return $opcoes;
}

?>