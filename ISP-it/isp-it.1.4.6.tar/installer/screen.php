<?
/**
 * Faz o cabeçalho
 *
 * @param string $modulo
 * @param array $matriz
 */
function menuComeco( $modulo, $matriz ){
	global $corBorda, $corFundo, $configAppVersion;
			# Menu Principal
			htmlAbreTabelaSH("center", 760, 0, 0, 0, $corFundo, $corBorda, 2);
					htmlAbreLinha($corFundo);
						htmlAbreColuna('100%', 'center', $corFundo, 0, 'normal');
							htmlAbreTabelaSH("center", 760, 0, 1, 0, $corFundo, $corBorda, 7);
								htmlAbreLinha($corFundo);
									
									itemLinha('&nbsp;','','center', $corFundo, 0, $classe);
								
								htmlFechaLinha();
							fechaTabela();
						htmlFechaColuna();
					htmlFechaLinha();
					
					htmlAbreLinha($corFundo);
						htmlAbreColuna('100%', 'center', $corFundo, 3, 'normal');
						
							htmlAbreTabelaSH('center', '100%', 0, 2, 1, $corFundo, $corBorda, 1);
								htmlAbreLinha($corBorda);
									itemLinhaTMNOURL("["._("INSTALADOR")."]", 'center', 'middle', '100%', $corFundo, 2, 'tabtitulo');
								htmlFechaLinha();
								itemLinhaNOURL("&nbsp;", 'right', $corFundo, 0, 'tabfundo81');
	
}

/**
 * Faz o rodapé
 *
 * @param string $modulo
 * @param array $matriz
 */
function menuFim( $modulo, $matriz ){

							fechaTabela();
		
						htmlFechaColuna();
					htmlFechaLinha();
					
					
				htmlFechaColuna();
				htmlFechaLinha();
			fechaTabela();
			# Fecha separação
	
}

/**
 * Direciona quais as etapas do instalador
 *
 * @param string $modulo
 * @param array $matriz
 */
function menuInstaller($modulo, $matriz) {
	
	menuComeco($modulo, $matriz);
	# Escolher o idioma
	if (!$modulo || $modulo == 'formFiles'){							
		infFiles( $modulo, $matriz );
	}
	# Informaçoes sobre o Banco
	elseif($modulo == 'formDB'){
		infDB( $modulo, $matriz );
	}
	# Informação do logo do Cliente
	elseif($modulo == 'formLogo'){
		infLogo( $modulo, $matriz );
	}
	# Alteração do arquivo custom.php
	elseif($modulo == 'instalarCustom'){
		# Criação do arquivo custom.php
		infCustom( $modulo, $matriz );
	}
	# Cria a Base com o usuario
	elseif($modulo == 'instalarDB'){
		infCriarDB( $modulo, $matriz );
	}
	# Cria as tabelas dessa Base
	elseif($modulo == 'instalarTb'){
		infCriarTb( $modulo, $matriz );
	}
	elseif( $modulo == 'gravarUsuario' ){
		infUsuario( $modulo, $matriz );
	}
	elseif($modulo == 'fullInstall'){
		finished();
	}
	menuFim($modulo, $matriz);

}

/**
 * Checa, verifica e  os diretorios/arquivos 
 *
 * @param string $modulo
 * @param array $matriz
 */
function infFiles( $modulo, $matriz ){
	global $corBorda, $corFundo, $sessPath, $sessHost, $configDirSql;
	if ( !$matriz[bntNext] ){
		# Motrar tabela de busca
		novaTabela2("["._("Configuração do Sub-diretório e arquivos")."]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
				$texto = "<br>".//<img src=".$html[imagem][protocolos]." border=0 align=left >
					"<b class=bold>"._("NOTA")."</b>
					<br><span class=normal10>"._("No terminal, criar um diretório.")."<br>".
					_("A permissão correta do diretório é 757.Ex:")."<br>".
					"</span>";

				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaNOURL("&nbsp;", 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela( "&nbsp;", 'left', '35%', 'tabfundo1' );//
				$texto = "root@" . trim($sessHost[uname]) . ":/var/www# mkdir isp-it" . "<br>".
						 "root@" . trim($sessHost[uname]) . ":/var/www# chmod 757 isp-it " . "<br>";
				itemLinhaNOURL($texto, 'left', $corFundo, 0/*2*/, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela( "&nbsp;", 'right', '40%', 'tabfundo1');
				$texto="			
					<form method=post action=index.php>
					<input type=hidden name=modulo value=formFiles>
					&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 0/*2*/, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto = "<b class=bold10>"._("Sub diretório:")."</b><br>
					<span class=normal10>"._("(Sugestão: /var/www/isp-it)")."</span>";
				itemLinhaTabela($texto, 'right','40%', 'tabfundo1');
				if ( !$sessPath['path'] ){
					$matriz['path']='/var/www/isp-it';
				}
				else{
					$matriz['path']=$sessPath['path'];
				}
				$texto="<input type=text name=matriz[path] size=30 value='$matriz[path]' class=''>";
				itemLinhaNOURL($texto, 'left', $corFundo, /*2*/0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
						</form>";
				itemLinhaNOURL( $texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	elseif ( !$matriz[path] ){
		$titulo=_("Falta preenchimento");
		$mensagem="<span class=txtaviso>
			"._("Por favor, preenche os campos ")."<br>
		</span>";
		$msgFinal = _("Clique aqui =>");
		$form="<form method=post action=index.php>
				<input type=hidden name=modulo value=formFiles>
				<input type=hidden name=matriz[path] value=$matriz[path]>
				<input type='submit' name=matriz[] value="._("Voltar")." class='submit'>
				</form>";
		montarTabela( $titulo, $mensagem, '', '', '', $msgFinal, $form );
	}
	elseif ( $matriz[bntNext] ){
		if ( $matriz[path] ){
			$origem = "";
			$origem = $matriz[path];
			$valida = validacaoRaiz( $matriz[path] );
		}
		else {
			$origem = "";
			$origem = $sessPath[path];
			$valida = validacaoRaiz( $sessPath[path] );
		}
		if ( $valida == 0 ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Informar o caminho absoluto")."<br>".
				_("No terminal, verifique o comando abaixo:")."<br><br>
			</span>";
			$msgMeio1 = "root@" . trim($sessHost[uname]) . ":" .substr($origem,0,strpos($origem, dirRaiz($origem))-1). "#";
			$msgMeio2 = '<b>'."ls -l"."&nbsp;". dirRaiz( $origem ) .'</b>';
			$msgFinal = _("Depois, clique aqui =>");
			$form="	<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formFiles>
					<input type=hidden name=matriz[path] value=$matriz[path]>
					<input type='submit' name=matriz[] value="._("Voltar")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', $msgMeio1, $msgMeio2, $msgFinal, $form );
		}
		# se foi dado permissão para esse diretorio -  P E R M I S S A O - que o usuario criou
		elseif( !is_writable( $origem ) ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Falta a permissão para o sub-diretório em")."&nbsp;". $origem ."<br>".
				_("No terminal, verifique o comando abaixo:")."<br><br>
			</span>";
			$msgMeio1="root@" . trim($sessHost[uname]) . ":" . $origem . "#";
			$msgMeio2='<b>' . "chmod 757" . "&nbsp;" . ( $matriz[path] ? $matriz[path] : $sessPath[path] ) .'<b>';
			$msgFinal = _("Depois, clique aqui =>");
			$form="	<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formFiles>
					<input type=hidden name=matriz[path] value=$matriz[path]>
					<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', $msgMeio1, $msgMeio2, $msgFinal, $form );
		}
		# se foi dado permissão para esse diretorio -  P E R M I S S A O - que o usuario criou CONFIG
		elseif( !is_writable( "../config" ) ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Falta a permissão para o sub-diretório em")."&nbsp;"."$origem/config"."<br>".
				_("No terminal, verifique o comando abaixo:")."<br><br>
			</span>";
			$msgMeio1 = "root@" . trim($sessHost[uname]) . ":" . $origem . "#";
			$msgMeio2 = '<b>' . "chmod 757 config" . '<b>';
			$msgFinal = _("Depois, clique aqui =>");
			$form = "<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formFiles>
					<input type=hidden name=matriz[path] value=$matriz[path]>
					<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', $msgMeio1, $msgMeio2, $msgFinal, $form );
		}
		# se foi dado permissão para esse arquivo config/custom.php.txt
		elseif( !is_writable( "../config/custom.php.txt" ) ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Falta a permissão para o arquivo em")."&nbsp;"."config/custom.php.txt"."<br>".
				_("No terminal, verifique o comando abaixo:")."<br><br>
			</span>";
			$msgMeio1 = "root@" . trim($sessHost[uname]) . ":" . $origem . "#";
			$msgMeio2 = '<b>' . "chmod 646 config/custom.php.txt" . '<b>';
			$msgFinal = _("Depois, clique aqui =>");
			$form ="<form method=post action=index.php>
					<input type=hidden name=modulo value=formFiles>
					<input type=hidden name=matriz[path] value=$matriz[path]>
					<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', $msgMeio1, $msgMeio2, $msgFinal, $form );
		}
		# é preciso DAR permissão de execução p o diretório que fica os arquivos.sql's
		elseif( !is_executable( "../$configDirSql" ) ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$msgMeio1 = "root@" . trim($sessHost[uname]) . ":" . $origem . "#";
			$msgMeio2 = '<b>' . "chmod 755 $configDirSql" . '<b>';
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Falta a permissão para o sub-diretório em")."&nbsp;"."sql"."<br>".
				_("No terminal, verifique o comando abaixo:")."<br><br>
			</span>";
			$msgFinal = _("Depois, clique aqui =>");
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formFiles>
					<input type=hidden name=matriz[path] value=$matriz[path]>
					<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', $msgMeio1, $msgMeio2, $msgFinal, $form );
		}
		# se foi dado permissão para esse diretorio -  P E R M I S S A O - que o usuario criou
		elseif( !is_writable( "../imagens" ) ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$msgMeio1 = "root@" . trim($sessHost[uname]) . ":" . $origem . "#";
			$msgMeio2 = '<b>' . "chmod 757 imagens" . '<b>';
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Falta a permissão para o sub-diretório em")."&nbsp;"."$origem/imagens"."<br>".
				_("No terminal, verifique o comando abaixo:")."<br><br>
			</span>";
			$msgFinal = _("Depois, clique aqui =>");
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formFiles>
					<input type=hidden name=matriz[path] value=$matriz[path]>
					<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', $msgMeio1, $msgMeio2, $msgFinal, $form );
		}
		# verificar as permissões em tmp
		elseif( 0 == montarTmp( $matriz, $arquivo ) ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$msgMeio1 = "root@" . trim($sessHost[uname]) . ":" . $origem . "#";
			$msgMeio2 = '<b>' . "chmod 777 tmp/$arquivo" . '<b>';
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Falta a permissão para o arquivo em")."&nbsp;"."$origem/tmp"."<br>".
				_("No terminal, verifique o comando abaixo:")."<br><br>
			</span>";
			$msgFinal = _("Depois, clique aqui =>");
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formFiles>
					<input type=hidden name=matriz[path] value=$matriz[path]>
					<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', $msgMeio1, $msgMeio2, $msgFinal, $form );			
		}
		# Relatório: 1° verifica a existencia dos aplicativos em /usr/bin; 2° verifica a existencia dos aplicativos em $origem/usr/bin
		elseif( 1/*!( !( file_exists( "/usr/bin/html2ps" ) && file_exists( "/usr/bin/ps2pdf" ) ) xor !( file_exists( "$origem/usr/bin/html2ps" ) && file_exists( "$origem/usr/bin/ps2pdf" ) ) ) */){
			//trocar o xor por um E EXCLUSIVO. Porém não sei se o PHP TEM!
			if( !( !( file_exists( "/usr/bin/html2ps" ) && file_exists( "/usr/bin/ps2pdf" ) ) xor !( file_exists( "$origem/usr/bin/html2ps" ) && file_exists( "$origem/usr/bin/ps2pdf" ) ) ) ){
				$titulo = _("Atenção: Uma ocorrência de erro");
				$mensagem="<span class=txtaviso>
					"._("ATENÇÃO: Aplicativos não encontrados: html2ps e ps2pdf !")."<br><br>"."
				</span>";
				$msgInicial = _("Por favor, fazer 2 links simbólico em /usr/bin/html2ps e /usr/bin/ps2pdf; ou") . "<br>";
				$msgInicial.= _("os arquivos executáveis deveriam estar no diretório") . "&nbsp;" . "$origem/usr/bin/";
				$msgFinal = _("Depois, clique aqui =>");
				$form ="<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=formFiles>
						<input type=hidden name=matriz[path] value=$matriz[path]>
						<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
						</form>";
				montarTabela( $titulo, $mensagem, $msgInicial, '', '', $msgFinal, $form );
				$a=0;
			}
			else{
				$a=1;
			}
		} //Colocar o ELSE e retirar o $a tb embaixo, depois de arrumar o E exclusivo.     // descubrindo a logica E exclusiva, deixar só else, retirando o elseif
		if( $a==1 ){
			#nova tabela para mostrar informações
			$titulo = _("Próximo Estágio");
			$msgFinal = _("Agora, clique aqui =>");
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Você manipulou os diretórios/arquivos e suas permissões")."<br><br>
			</span>";
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formDB>
					<input type='submit' name=matriz[] value="._("Próximo")." class='submit'>
					</form>";
				montarTabela( $titulo, $mensagem, '', '', '', $msgFinal, $form );
		}
	}
	else{
		echo "Error<br>";
	}
}

/**
 * Formulário para informaçoes para a Base de Dados
 *
 * @param string $modulo
 * @param array $matriz
 */
function infDB( $modulo, $matriz ){
	global $corBorda, $corFundo;
	# Form de inclusao
	if( !$matriz[bntNext] ) {
		# Motrar tabela de busca
		novaTabela2("["._("Configuração para a conexão com o Base de Dados")."]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=formDB>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 1, 'tabfundo1');
					echo "<b class=bold10>"._("Host:")."</b>";
				htmlFechaColuna();
				if ( !$matriz['dbhost'] ) $matriz['dbhost']='localhost';
				$texto="<input type=text name=matriz[dbhost] size=30 value='$matriz[dbhost]' tabindex='0' class=''>";
				itemLinhaNOURL($texto, 'left', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 1, 'tabfundo1');
					echo "<b class=bold10>"._("Base de Dados:")."</b>";
				htmlFechaColuna();
				if ( !$matriz['dbdatabase'] ) $matriz['dbdatabase']='isp';
				$texto="<input type=text name=matriz[dbdatabase] size=30 value='$matriz[dbdatabase]' tabindex='1' class=''>";
				itemLinhaNOURL($texto, 'left', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 1, 'tabfundo1');
					echo "<b class=bold10>"._("Usuário:")."</b>";
				htmlFechaColuna();
				if ( !$matriz['dbuser'] ) $matriz['dbuser']='isp';
				$texto="<input type=text name=matriz[dbuser] size=30 value='$matriz[dbuser]' tabindex='2' class=''>";
				itemLinhaNOURL($texto, 'left', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 1, 'tabfundo1');
					echo "<b class=bold10>"._("Senha:")."</b>";
				htmlFechaColuna();													
				$texto="<input type=password name=matriz[dbpassword] size=30 value='$matriz[dbpassword]' tabindex='3' class=''>";
				itemLinhaNOURL($texto, 'left', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 1, 'tabfundo1');
					echo "<b class=bold10>"._("Senha de root do MySQL:")."</b>";
				htmlFechaColuna();													
				$texto="<input type=password name=matriz[dbrootpassword] size=30 value='$matriz[dbrootpassword]' tabindex='4' class=''>";
				itemLinhaNOURL($texto, 'left', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntNext] value=" . _('Próximo') . " tabindex='5' class='submit'>
						</form>";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	elseif( $matriz[bntNext] ){
		if ( empty($matriz[dbhost]) || empty($matriz[dbdatabase]) || empty($matriz[dbuser]) ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$msgFinal = _("Clique aqui =>");
			$mensagem = "<span class=txtaviso>
				"._("Por favor, preenche os campos ")."<br><br>
			</span>";
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formDB>
					<input type='submit' name=matriz[] value="._("Voltar")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', '', '', $msgFinal, $form );
		}
		else{
			$titulo = _("Próximo Estágio");
			$msgFinal = _("Agora, clique aqui =>");
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Você informou corretamente a Base de Dados")."<br><br>
			</span>";
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formLogo>
					<input type='submit' name=matriz[] value="._("Próximo")." class='submit'>
					</form>";
			montarTabela( $titulo, $mensagem, '', '', '', $msgFinal, $form );
		}
	}
}

/**
 * Formulário para o logotipo da empresa, e uma checagem do mesmo
 *
 * @param string $modulo
 * @param array $matriz
 */
function infLogo( $modulo, $matriz ){
	global $corBorda, $corFundo, $sessPath, $sessHost;
	if ( !$matriz[bntNext] ){
		# Motrar tabela de busca
		novaTabela2("["._("Escolha o Logotipo da empresa")."]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela( "&nbsp;", 'right', '40%', 'tabfundo1');
				$texto="			
					<form method=post enctype='multipart/form-data' action=index.php>
					<input type=hidden name=modulo value=formLogo>
					&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto = "<b class=bold10>"._("Logotipo da empresa:")."</b><br>
					<span class=normal10>"._("Dimensão de 104x50 pixels")."</span>";
				itemLinhaTabela( $texto, 'right', '40%', 'tabfundo1');
				$texto="<input type=file name=arquivo size=30 value='$arquivo' class=''>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela( "&nbsp;", 'right', '40%', 'tabfundo1');
				$texto="<input type='submit' name=matriz[bntNext] value="._("Próximo")." class='submit'>
						</form>";
				itemLinhaNOURL( $texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	# verifica erro ao transmitir
	else/*if ( $_FILES[arquivo][error] >= 3 )*/{
		# carregar o nome da imagem do arquivo				  
		$matriz[nomeArquivo]=$_FILES[arquivo][name];
		$logotipo = logo( $matriz );
		#nova tabela para mostrar informações
		if (  0 == $logotipo ){
			#nova tabela para mostrar informações
			$titulo = _("Atenção: Uma ocorrência de erro");
			$mensagem = "<span class=txtaviso>
						"._("ATENÇÃO: O arquivo não pode ser usado")."<br>".
						_("Por favor, envie um arquivo novamente")."<br><br>
						</span>";
			$msgFinal = _("Depois, clique aqui =>");
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=formLogo>
					<input type='submit' name=matriz[] value="._("Voltar")." class='submit'>
					</form>";
		}
		else{
			$titulo = _("Próximo Estágio");
			$mensagem="<span class=txtaviso>
				"._("ATENÇÃO: Você enviou corretamente o logotipo da empresa")."<br><br>
			</span>";
			$msgFinal = _("Agora, clique aqui =>");
			$form ="<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=instalarCustom>
					<input type='submit' name=matriz[] value="._("Próximo")." class='submit'>
					</form>";
		}
		montarTabela( $titulo, $mensagem, '', '', '', $msgFinal, $form );
	}
}

/**
 * Cria o arquivo custom.php
 *
 * @param string $modulo
 * @param array $matriz
 */
function infCustom( $modulo, $matriz ){
	# Criação do arq. custom.php
	if ( 0 == file_custom( $modulo, $matriz, $erro ) ){
		$titulo = _("Atenção: Uma ocorrência de erro");
		$msgFinal = _("Clique aqui =>");
		$mensagem = "<span class=txtaviso>
			"._("Por favor, entre em contato com o seu administrador ")."<br><br>
		</span>";
		$msgInicial = $erro;
	}
	else{
		$titulo = _("Próximo Estágio");
		$mensagem="<span class=txtaviso>
			"._("ATENÇÃO: Você executou a criação do arquivo: config/custom.php")."<br><br>
		</span>";
		$msgInicial = '';
		$msgFinal = _("Agora, clique aqui =>");
		$form ="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=instalarDB>
				<input type='submit' name=matriz[] value="._("Próximo")." class='submit'>
				</form>";
	}
	montarTabela($titulo, $mensagem, $msgInicial, '', '', $msgFinal, $form );
}

/**
 * Cria o Base de Dados
 *
 * @param string $modulo
 * @param array $matriz
 */
function infCriarDB( $modulo, $matriz ){
	# Conexão com o Banco e criação do BANCO	
	if ( 1 == dbCriaBanco( $erro ) ){
		$userDB = dbUserDB( $erro );
	}
	if( $userDB ){
		$titulo = _("Próximo Estágio");
		$mensagem="<span class=txtaviso>
			"._("ATENÇÃO: Você executou a criação da Base de Dados")."<br><br>
		</span>";
		$msgInicial = '';
		$msgFinal = _("Agora, clique aqui =>");
		$form ="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=instalarTb>
				<input type='submit' name=matriz[] value="._("Próximo")." class='submit'>
				</form>";
	}
	else{
		$titulo = _("Conexão com o MySQL");
		$mensagem="<span class=txtaviso>".
			_("ATENÇÃO: A criação da Base de dados não foi executada")."<br>".
			_("Por favor, entre em contato com o seu administrador ")."<br><br>
		</span>";
		$msgInicial = $erro;
		$msgFinal = '';//_("Depois, clique aqui =>");
	}
	montarTabela($titulo, $mensagem, $msgInicial, '', '', $msgFinal, $form );
}

/**
 * Cria as tabelas para a Base
 *
 * @param string $modulo
 * @param array $matriz
 */
function infCriarTb( $modulo, $matriz ){
	if( 1 == dbCriaTabela( $erro ) ){
		$titulo = _("Próximo Estágio");
		$msgInicial = '';
		$mensagem="<span class=txtaviso>
			"._("ATENÇÃO: Você executou a criação das Tabelas")."<br><br>
		</span>";
		$msgFinal = _("Agora, clique aqui =>");
		$form ="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=gravarUsuario>
				<input type='submit' name=matriz[] value="._("Próximo")." class='submit'>
				</form>";
	}
	else{
		$titulo = _("Atenção: Uma ocorrência de erro");
		$mensagem="<span class=txtaviso>".
			_("ATENÇÃO: A criação tabelas/registros houve problemas")."<br>".
			_("Por favor, entre em contato com o seu administrador ")."<br><br>
		</span>";
		$msgInicial = $erro;
		$msgFinal = '';		
	}
	montarTabela( $titulo, $mensagem, $msgInicial, '', '', $msgFinal, $form );
}

function infUsuario( $modulo, $matriz ){
	global $corBorda, $corFundo, $sessHost, $sessPath;
	# Form de inclusao
	if( !$matriz[bntNext] ) {
		# Motrar tabela de busca
		novaTabela2("["._("Cadastro de Usuário Administrador")."]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
				$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=gravarUsuario>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Login: </b><br>
					<span class=normal10>Login de acesso do usuário</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[login] value='$matriz[login]' size=20>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Senha: </b><br>
					<span class=normal10>Senha de acesso do usuário</span>";
				htmlFechaColuna();
				$texto="<input type=password name=matriz[senha] size=20>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Confirmação de Senha: </b><br>
					<span class=normal10>Confirmação de de Senha de acesso do usuário</span>";
				htmlFechaColuna();
				$texto="<input type=password name=matriz[confirma_senha] size=20>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntNext] value='Próximo' class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	elseif( $matriz[bntNext] ){
		if ( empty( $matriz[login] ) || empty( $matriz[senha] ) || empty( $matriz[confirma_senha] ) || ( $matriz[confirma_senha] != $matriz[confirma_senha] ) ){
			$titulo = _("Atenção: Uma ocorrência de erro");
			$mensagem="<span class=txtaviso>
				"._("Preencha os campos, e deixe as senhas iguais")."<br><br>
			</span>";
			$msgFinal = _("Clique aqui =>");
			$form="	<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=gravarUsuario>
					<input type=hidden name=matriz[login] value=$matriz[login]>
					<input type=hidden name=matriz[senha] value=$matriz[senha]>
					<input type=hidden name=matriz[confirma_senha] value=$matriz[confirma_senha]>
					<input type='submit' name=matriz[] value="._("Voltar")." class='submit'>
					</form>";
		}
		else{
			# Gravar o Usuário na tab. Usuarios
			dbUsuario( $matriz, 'incluir' );
			# Buscar o id do usuario que gravou
			$matriz['usuario'] = resultadoSQL( buscaUsuarios( $matriz[login], 'login', 'igual', 'id' ), 0, 'id' );
			# Gravar o Usuario_Grupo na tab. UsuariosGrupos que pertença ao grupo administrativo.
			$matriz[grupo] = '1';
			dbUsuarioGrupo( $matriz, 'incluir' );
			$titulo = _("Próximo Estágio");
			$mensagem="<span class=txtaviso>
						"._("ATENÇÃO: Você cadastrou corretamente")."<br><br>
						</span>";
			$msgFinal = _("Clique aqui =>");
			$form="	<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=fullInstall>
					<input type='submit' name=matriz[] value="._("Finalizado")." class='submit'>
					</form>";
		}
		montarTabela( $titulo, $mensagem, '', '', '', $msgFinal, $form );
	}
}

function finished(){
	$url = str_replace( "installer/", "", $_SERVER['HTTP_REFERER'] );
	$titulo = _("Pronto para rodar o ISP");
	$mensagem =	"<span class=txtaviso>
				"._("ATENÇÃO: Você completou a instalação!")."<br><br>
				</span>";
	$msgFinal = _("Clique aqui =>");
	$form = "<b><a href=$url>"._("ISP")."</a></b>";
	montarTabela( $titulo, $mensagem, $msgInicial, '', '', $msgFinal, $form );
}

function montarTabela( $titulo, $mensagem='', $msgInicial='', $msgMeio1='', $msgMeio2='', $msgFinal, $fechaForm='' ){
	global $corFundo, $corBorda;
	#nova tabela para mostrar informações
	novaTabela2( $titulo, 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL( "&nbsp;", 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL($mensagem, 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		if ( $msgInicial != '' ){
			novaLinhaTabela($corFundo, '100%');
				itemLinhaNOURL( $msgInicial, 'center', $corFundo, 2, 'tabfundo1');			
			fechaLinhaTabela();			
		}
		if ( $msgMeio1 != ''){
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela( $msgMeio1, 'right', '50%', 'tabfundo1');
				itemLinhaNOURL( $msgMeio2, 'left', $corFundo, 0, 'tabfundo1');			
			fechaLinhaTabela();
		}
		if ( $msgMeio1 != '' || $msgInicial != '' ){
			novaLinhaTabela($corFundo, '100%');
				itemLinhaNOURL( "&nbsp;", 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		}
		if ( $fechaForm != '' ){
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela( $msgFinal, 'right', '50%', 'tabfundo1');
				itemLinhaNOURL( $fechaForm, 'left', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
	fechaTabela();	
	# fim da tabela	
}

?>
