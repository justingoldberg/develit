<?
/**
 * No arquivo php.ini, erifica o conteúdo de register_globals
 *
 * @param string $modulo
 * @return int: 1 -> register_globals = on; e 0 -> default
 */
function phpIni( $modulo ){
	# Verificar a diretiva do php.ini
	$retorno=1;
	if ( !$modulo ){
		$register_globals = ini_get('register_globals');
		if ( $register_globals != 1 ){
			$retorno=0;
			echo "<br>";
			$titulo = _("Atenção: Uma ocorrência de erro");
			$mensagem = "<span class=txtaviso>
						"._("ATENÇÃO: Edite o arquivo php.ini")."</span><br>";
			$msgInicial = 	_("Setar a linha register_globals para On")."<br>".
							_("E logo após, reinicie o servidor apache")."<br><br>";
			$msgFinal = _("Depois, clique aqui =>");
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=''>
					<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, $msgInicial, '', '', $msgFinal, $form );
		}
	}
	return $retorno;
}

//function phpApache(){
//	$retorno=1;
//	# Pego os charset's setados
//	$apache = apache_getenv("HTTP_ACCEPT_CHARSET");
//	$variavel = split( "[,;]", $apache );
//	if ( !in_array("ISO-8859-1", $variavel)) {
//		$retorno=0;
//		echo "<br>";
//		$titulo = _("Warning: An error has been ocurred");
//		$mensagem = "<span class=txtaviso>
//					"._("ATTENTION: Edit the Charset")."<br></span>";
//		$msgInicial =	_("Edit the charset to ISO")."<br>".
//						"<br>";
//		$msgFinal = _("So, click here =>");
//		$form ="<form method=post name=matriz action=index.php>
//				<input type=hidden name=modulo value=''>
//				<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
//				</form>";
//		montarTabela( $titulo, $mensagem, $msgInicial, '', '', $msgFinal, $form );
//	}
//	return $retorno;
//}

/**
 * Validar o logotipo da empresa
 *
 * @param array $matriz
 * @return int 
 */
function logo( $matriz ){
	global $corBorda, $corFundo, $_FILES;
	$retorno=0;
	# analisa: extensão, dimensão, copiar p/ imagens, config/html.php e custom.php
	$info = getimagesize( $_FILES[arquivo][tmp_name] );
	if (ereg("[][><}{}():;,!?*%&#@]", $_FILES['arquivo']['name'])){
		$erro = _("O arquivo tem caracteres inválidos")."<br>";
	}
	else{
		if (!$info = getimagesize( $_FILES[arquivo][tmp_name] ) ){
			$erro = _("Não é um arquivo de imagem ou o campo está vazio")."<br>";
		}
		if ($info['2']>3){ // Aceitando apenas jpeg, gif e png
			$erro .= _("O arquivo não está no formato: PNG, GIF or JPG")."<br>";
		}
		if  ( !( $info[0]==104 && $info[1]==50 ) && !empty($info) ){
			$erro.= _("O arquivo não tem a dimensão de 104x50 pixels")."<br>";
		}
		if ( empty( $erro ) ){
			if ( !copy( $_FILES['arquivo']['tmp_name'], "../imagens/".$matriz['nomeArquivo'] ) ){
				$erro.= _("Ao copiar o arquivo houve um erro");
			}
		}
	}
	if ( empty($erro) ){
		# gravar em (html e custom).php
		$retorno = file_logo_custom( $matriz, $erro );
	}
	return $retorno;
}

/**
 * No arquivo custom.php.txt é adicionado na variavel $html[imagem][logoRelatorio], o nome do logotipo da empresa
 *
 * @param array $matriz
 * @param string $erro
 * @return int
 */
function file_logo_custom($matriz, &$erro ){
	# Adicionando uma linha no arquivo custom.php.txt
	$retorno = 0;
	$arquivotxt="custom.php.txt";
	$find = "logoRelatorio";//"\$html";
	$replace=$matriz[nomeArquivo];
	$totalBuffer='';
	$conexaotxt = fopen( "../config/".$arquivotxt, "r" );
	while ( !feof( $conexaotxt ) ){
		# verifica linha por linha.
		$buffer = rtrim( fgets( $conexaotxt, 4096 ) );
		# prepara p/ procurar pela linha do que tem o conteudo de find[0] (no buffer)
		# a ocorrencia de $find, está na posiçao 0 (zero), o mesmo pode ser interpretado como false, então pergunto se trata-se..
		#de um valor numerico
		if ( is_numeric( strpos( $buffer, $find ) ) ){
			$buffer = "\$html[imagem][logoRelatorio]=\$htmlDirImagem.\"/$replace\";";
		}
		$totalBuffer.=$buffer."\n";
	}# fim do while
	fclose($conexaotxt);

	# Abro o arquivo pra sobreescrever
	$conexaotxt = fopen( "../config/".$arquivotxt, "w");
	if ( !fwrite( $conexaotxt, $totalBuffer ) ){
		$erro .= "Não gravou em config/$arquivotxt entre em contato com o seu administrador !";
	}
	else{
		$retorno = 1;
	}
	fclose($conexaotxt);
	return $retorno;
}

/**
 * Verifica permissão 777 em todos os sub-diretorios de tmp
 *
 * @param array $matriz
 * @param string $arquivo
 * @return int
 */
function montarTmp( $matriz, &$arquivo ){
//	global $sessPath;
	$retorno=0;
	if (file_exists("../tmp")){
		if ( 777 != substr( sprintf( "%o", fileperms( "../tmp/html" ) ), -3 ) ){
			$arquivo = "html";
		}
		elseif ( 777 != substr( sprintf( "%o", fileperms( "../tmp/nota" ) ), -3 ) ){
			$arquivo = "nota";
		}
		elseif ( 777 != substr( sprintf( "%o", fileperms( "../tmp/pdf" ) ), -3 ) ){
			$arquivo = "pdf";
		}
		elseif ( 777 != substr( sprintf( "%o", fileperms( "../tmp/remessa" ) ), -3 ) ){
			$arquivo = "remessa";
		}
		else{
			$retorno=1;
		}
	}
	return $retorno;
}

/**
 * Cria o arquivo custom.php através de custom.php.txt, agregando as variáveis de configuração para a conexão com o Banco de Dados
 *
 * @param unknown_type $modulo
 * @param array $matriz
 * @param string $erro
 * @return int
 */
function file_custom( $modulo, $matriz, &$erro ){
	global $sessBanco, $sessPath;
	$retorno = 0;
	$arquivophp="custom.php";
	$arquivotxt="custom.php.txt";
	$dirPath = dirRaiz( $sessPath[path] );
	$conexaotxt = fopen( "../config/".$arquivotxt, "r");
	$find = array ( "configHostMySQL"	,
					"configUserMySQL"	,
					"configPasswdMySQL"	,
					"configDBMySQL"
					);
	$replace=array( "dbhost"	,
					"dbuser"	,
					"dbpassword",
					"dbdatabase"
					);
	$i=0;
	# Criando o arquivo.php
	$conexaophp = fopen( "../config/".$arquivophp, "w" );
	if ( !file_exists( "../config/".$arquivophp ) ){
		$erro = _("Provavelmente problema de permissão.")."&nbsp;"._("Não foi criado o arquivo: config/$arquivophp");
	}
	else{
		$totalBuffer='';
		while ( !feof( $conexaotxt ) ){
			# verifica linha por linha.
			$buffer = rtrim( fgets( $conexaotxt, 4096 ) );
			if ( !empty( $totalBuffer ) ) $totalBuffer.="\n";
			# prepara p/ procurar pela linha do que tem o conteudo de find[0] (no buffer)
			# a ocorrencia de $find, está na posiçao 0 (zero), o mesmo pode ser interpretado como false, então pergunto se trata-se..
			#de um valor numerico
			if ( is_numeric( strpos( $buffer, $find[$i] ) ) ){
				#procurou por " (aspa dupla);
				$length=strpos($buffer,"\"");
				$buffer=substr( $buffer, 0, $length + 1) . $sessBanco[$replace[$i]] . "\";";
				$i++;
			}
			# Acumulando o buffer para gravar;
			$totalBuffer.=$buffer;
		}# fim do while - varrendo o custom.php.txt
		if ( fwrite( $conexaophp, $totalBuffer ) ){
			$retorno = 1;
		}
		else{
			$erro = _("Ao escrever no arquivo houve um erro em: config/") . $arquivophp;
		}
		fclose($conexaophp);
	}
	fclose ($conexaotxt);
	return $retorno;
}

/**
 * Lendo os arquivos SQL, e retirando os comentarios que o mysqldump inclui
 *
 * @param string $conexaosql
 * @return string
 */
function semComentarioSQL($conexaosql){
	$conteudo='';
	while ( !feof( $conexaosql ) ){
		
		# verifica linha por linha.
		$buffer = fgets( $conexaosql, 4096 );
		# ver se começa com -- ou /* ou # que significa comentario em linguagem SQL. E retira-os.
		if ( is_numeric( strpos( $buffer, "--" ) ) || is_numeric( strpos( $buffer, "/*" ) ) || ( "#" == substr( $buffer, 0, 1 ) ) ){ //		if ( ereg( "^(--|\/\*)")){
			$buffer = "";
		}
		# Acumulando o buffer;
		$conteudo.=$buffer;
	}# fim do while
	return $conteudo;
}

/**
 * Enter description here...
 *
 * @param unknown_type $raiz
 * @return unknown
 */
function validacaoRaiz( $raiz ){
	$retorno = 0;
	$raiz = barra($raiz);
	$compTotal=strlen($raiz);
	if ( "/" == substr($raiz, 0, 1) ){
		for($i=1; $i<=$compTotal; $i++ ){
			if( "/" == ( substr($raiz, $i, 1 ) ) ){				
				$nome = substr($raiz, 0, $i);
				$retorno = is_dir( $nome);
				if ( $retorno == 0 ){
					break;
				}
			}
		}
	}
	return $retorno;
}

/**
 * Enter description here...
 *
 * @param unknown_type $raiz
 * @return unknown
 */
function dirRaiz( $raiz ){
	$raiz = barra($raiz);
	$compTotal=strlen($raiz);
	$x=0;
	for($i=1; $i<=$compTotal; $i++ ){
		if( "/" == ( substr($raiz, $i, 1 ) ) ){
			$nome = substr($raiz, $x+1, $i-$x-1);
			$x=$i;
		}
	}
	return $nome;
}

/**
 * Adiciona a barra caso na tenha
 *
 * @param string $raiz
 * @return string
 */
function barra($raiz){
	if ( "/" != substr( $raiz, strlen($raiz) -1 , 1) ){
		$raiz.="/";
	}
	return $raiz;
}
?>