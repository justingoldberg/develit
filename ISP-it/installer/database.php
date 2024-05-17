<?
/**
 * Cria a Base de Dados
 *
 * @param string $erro
 * @return int: 1 -> sucesso ; 0 -> insucesso
 */
function dbCriaBanco( &$erro ){
	global $configHostMySQL, $configDBMySQL, $sessBanco;
	
	# Conectar com banco de dados
	$conn=conectaMySQL($configHostMySQL, "root", $sessBanco[dbrootpassword] );
	$retorno=0;
	# Checar conexão com banco de dados
	if(!$conn)
	{
		$erro = _("Erro em conexão com o Base de Dados do MySQL");
	}
	else{
		$sql = "CREATE DATABASE IF NOT EXISTS $configDBMySQL";
		consultaSQL( $sql, $conn );
		$db=selecionaDB( $configDBMySQL, $conn );
		if(!$db){
	        $erro = _("Erro ao selecionar a base de dados");
		}else{
			$retorno=1;
		}
	}
	return $retorno;
}

/**
 * Cria o usuário e suas permissões
 *
 * @param string $erro
 * @return int: 1 -> sucesso ; 0 -> insucesso
 */
function dbUserDB( &$erro ){
	global $configHostMySQL, $configUserMySQL, $configPasswdMySQL, $configDBMySQL, $sessBanco;
	$retorno=0;
	# Conecta com Banco de Dados
	$conn = conectaMySQL($configHostMySQL, "root", $sessBanco[dbrootpassword] );
	if(!$conn)
	{
		$erro = _("Erro em conexão com o Base de Dados do MySQL");
	}
	else{
		$sql = "GRANT ALL ON $configDBMySQL.* TO $configUserMySQL@$configHostMySQL IDENTIFIED BY '$configPasswdMySQL'";
		if ( 1 == consultaSQL( $sql, $conn ) ){
				if ( 1 == consultaSQL( "FLUSH PRIVILEGES", $conn ) ){
					$retorno=1;
				}
				else{
					$erro = _("Erro ao executar privilegios");
				}
		}
		else{
			$erro = _("Erro ao atribuir privilegios");
		}
	}
	return $retorno;
}

/**
 * Cria as tabelas e seus registro atravês do tratamento dos arquivos sql
 *
 * @param string $erro
 * @return int: 1 -> sucesso ; 0 -> insucesso
 */
function dbCriaTabela( &$erro ){
	global $configDirSql, $configAppVersion;
	$erro = '';
	# Procedimentos:
	# 1) Validar o diretório dos arquivos do tipo sql;
	# 2) Verificar a versão do ISP;
	# 3) Localizando os arquivos do tipo sql, de acordo com o quesito 1;
	# 4) Checa cada arquivo se é compativél com a versão que pretende instalar;
	# 5) Abri cada arquivo;
	# 6) Função: Tira os comentarios feito pelo mysqldump;
	# 7) Função: Separa as instruções em sql por ; e executa-as;
	# 8) Sinalizador de sucesso de todas as operações;
	
	# 1)
	$configDirSql = trim( barra( $configDirSql ) );
	
	# 2) 
	$versaoISP = trim ( str_replace( 'Versão ', '', $configAppVersion ) );
	
	# 3)
	$updates = glob( "../$configDirSql"."*.sql" );
	$condicao = 1;
	for( $i = 0; $i < count( $updates ); $i++ ){
		# 4)
		# Depois que verificou que é incompativel, não checa os demais arquivos
		if ( $condicao == 1 && $condicao = checaVersaoUpdate( $versaoISP, $updates[$i] ) ){
			
				# 5)
				$conexaosql = fopen( $updates[$i], "r");
				if ($conexaosql){
						
					# 6)
					$conteudo = semComentarioSQL($conexaosql);
					fclose ($conexaosql);
					# 7)
					dadosSql( $conteudo, $erro );
				}
				else{
					$erro .= "Erro ao abrir o arquivo: " . $configDirSql . $configSql[$updates[$i]] . '<br>';
				}
		} #fim do if
	} #fim do for
	
	# 8)
	$retorno = ( empty( $erro) ? $retorno = 1 : $retorno = 0 );
	return $retorno;
}

/**
 * Compara a versão do aplicativo, e separa os arquivos do tipo sql para tal versão
 *
 * @param string $versaoISP
 * @param string $versaoUpdate
 * @return int: 1 -> sucesso ; 0 -> insucesso
 */

function checaVersaoUpdate( $versaoISP, $versaoUpdate ){
	$retorno = 1;
	# ver se a variavel ($versaoUpdate) é numérica, se for compara com a versão do ISP a instalar
	$find_h = strpos( $versaoUpdate, '_to_' );
	if( is_numeric($find_h) ){
		$find_h = $find_h + 4;
		$find_f = strpos( $versaoUpdate, '.sql' ) - $find_h;
		if( $num_versao_update = substr( $versaoUpdate, $find_h, $find_f ) ){
			# separa os itens da versão por pto.
			$versaoISP = explode( ".", $versaoISP );
			$num_versao_update = explode( ".", $num_versao_update );
			
			for( $i = 0; $i < count( $versaoISP ); $i++ ){
				//echo $versaoISP[$i] ." - " . $num_versao_update[$i] ."<br>";
				if ( $versaoISP[$i] > $num_versao_update[$i] ){
					break;
				}
				# verifica cada intervalo de pto OU checa o número de pto
				elseif( $versaoISP[$i] < $num_versao_update[$i] || ( (1 == count($versaoISP) - $i ) && 1 == ( count( $num_versao_update ) - count( $versaoISP ) ) ) ){
					$retorno = 0;
					break;
				}
			}
		}
	}
	return $retorno;
}

/**
 * Separador de instruçoes SQLs e as executa
 *
 * @param string $conteudo
 * @param string $erro
 */
function dadosSql( $conteudo, &$erro ){
	global $conn, $lang;
//	$retorno=1;
	# Usa o conteudo do arquivo como um array
	$dados = explode ( ";", $conteudo );
	# o ultimo elemento é sempre vazio
	$dado_retirado = array_pop( $dados );
//	$i=0;
	foreach ( $dados as $sql ){
		$consulta = consultaSQLHide( $sql, $conn );
		if ( 0 == $consulta ){
			$erro .= "Instruções SQL falhou: " . $sql . '<br>';
		}
//		$i++;
	}# fim do foreach
}


?>