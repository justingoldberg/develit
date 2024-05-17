<?php
/*
 * Created on May 3, 2005
 *		   by louco
 */
 
 # função para form de seleção de filtros de faturamento
function formRelatorioEmailCliente($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[E-mail por Clientes]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			
			//detalhar
			getDetalharCliente($matriz);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Detalhar:</b><br>
					<span class=normal10>Exibir somente e-mails.</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					if($matriz[somenteMail]) $somenteMailOpc='checked';
					$texto="<input type=checkbox name=matriz[somenteMail] value='S' $somenteMailOpc>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			
			//Botao
			getBotoesConsRel();
			
			htmlFechaLinha();
		fechaTabela();
	}
}
function relatorioEmailCLiente($modulo, $sub, $acao, $registro, $matriz) {
	global $conn, $tb ,$corFundo, $corBorda, $sessLogin;
	
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
	
	if (!$matriz['somenteMail']){
		$matLargura=array(    '75%',      '25%');
		$matCabecalho=array( 'Cliente',  'E-mail');
		$matAlinhamento=array('left',     'left');
		$numCol=count($matCabecalho);
	}
	else{
		$matLargura=array(    '100%');
		$matCabecalho=array( 'E-mail');
		$matAlinhamento=array('left');
		$numCol=count($matCabecalho);
	}
			
	$l=0;

	while($matriz[pop][$pp]) {
//		nome do pop para exbição
		$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
		$sqlPOP="$tb[POP].id = ".$matriz[pop][$pp];
		
		$matResultado=array();
				
		$sql="
Select 
    $tb[PessoasTipos].id as id,
    $tb[Pessoas].nome,
    $tb[Emails].idDominio, 
    $tb[Emails].login, 
    $tb[Dominios].nome AS dominio, 
    $tb[Dominios].padrao  
    
FROM $tb[Pessoas] 
INNER JOIN Pop
	On($tb[Pessoas].idPOP = Pop.id)
INNER JOIN $tb[PessoasTipos]
    On($tb[Pessoas].id = $tb[PessoasTipos].idPessoa)
INNER JOIN $tb[Emails] 
    On($tb[PessoasTipos].id = $tb[Emails].idPessoaTipo)
INNER JOIN $tb[Dominios] 
    On($tb[Emails].idDominio = $tb[Dominios].id)
WHERE $sqlPOP
ORDER by $tb[Pessoas].nome";
//GROUP by $tb[PessoasTipos].id, $tb[Emails].idDominio		 count(PessoasTipos.id) AS qtde			
		$consulta=consultaSQL($sql, $conn);
	
		if( $consulta && contaconsulta($consulta) ) {		
//			exibe a gravata 
		if ($matriz[consulta]){
			echo "<br>";
			novaTabela($nomePop,"left", '100%', 0, 2, 1, $corFundo, $corBorda, $numCol);
			$cor='tabfundo0';
			htmlAbreLinha($cor);
				for ($cc=0;$cc<count($matCabecalho);$cc++) 
					itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $matLargura[$cc], $corFundo, 0, $cor);
			htmlFechaLinha();
		}				
			$matResultado=array();
			$l= 0;
			$ttUso = 0;
			$ttReservado = 0;
						
			#inicia a varredura e joga 
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$padrao=resultadoSQL($consulta, $a , 'padrao');
				$idDominio=resultadoSQL($consulta, $a , 'idDominio');
				$qtde = 0 ;//resultadoSQL($consulta, $a , 'qtde');
				
//				verifica se é uma email dentro do dominio padrao, e verifica o total que a pessoaTipo Suporta. 
//				com isto a rotina fica um tanto quanto lenta, ao fazer o calculo total das contas de email
//				porem nao existe outra maneira de fazer isto.

							
				$cc=0;
				if(!$matriz['somenteMail']){
					$campos[$cc++] = '&nbsp';
				}
				$campos[$cc++]=resultadoSQL($consulta, $a, 'login').'@'.resultadoSQL($consulta, $a , 'dominio');
					
				$ttUso += $qtde;
				$ttReservado += $total;
		
				$pessoa_atual= resultadoSQL($consulta, $a, 'nome');
				if($a>0) $pessoa_anterior=resultadoSQL($consulta, $a-1, 'nome');
				if($a+1 < contaConsulta($consulta)) $pessoa_proximo= resultadoSQL($consulta, $a+1, 'nome');	
		
//				# se for consulta exibe a linha detalhe
				if ($matriz[consulta]){
					
					if ($pessoa_atual!= $pessoa_anterior && !$matriz['somenteMail']){
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL("<b>".$pessoa_atual."</b>", 'left', 'middle', '100%', $corFundo, $numCol, 'normal9');
						htmlFechaLinha();
						$qtParcial= 1;
					}
					else
						$qtParcial++; 
					
					if ($matriz['detalhar'] == 'S' || $matriz['somenteMail']){
						htmlAbreLinha($corFundo);
						for ($cc=0; $cc<count($campos); $cc++)
							itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, "normal9");
						htmlFechaLinha();						
					}
					
					if ($pessoa_atual!= $pessoa_proximo && !$matriz['somenteMail']){
						if ($padrao == "S"){
							$idPessoaTipo=resultadoSQL($consulta, $a , 'id');
							$total = emailTotalContasDominioPadrao($idDominio, $idPessoaTipo);
						}else 
							$total=emailTotalContasDominio($idDominio);
						
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL("<b>Total</b>", 'right', 'middle', '75%', $corFundo, 0, 'normal9');
							itemLinhaTMNOURL("<b>".$qtParcial.'/'.$total."</b>", 'right', 'middle', '25%',$corFundo, 0,'normal9');
						htmlFechaLinha();
						
						$ttReservado += $total;
						$ttUso += $qtParcial;
						
						$qtParcial=0;
					}	
				}
				else{
					
					if ($pessoa_atual!= $pessoa_anterior){
						$i=0;
						$matResultado[$matCabecalho[$i++]][$l]="<b>".$pessoa_atual."</b>";
						$matResultado[$matCabecalho[$i]][$l]="&nbsp;";
						$l++;
						$qtParcial= 1;
					}	
					else	$qtParcial++;
					
					# detalhe
					if ($matriz['detalhar'] == 'S'){
						for ($cc=0; $cc<count($campos); $cc++) {
							$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
						}
						$l++;
					}
					
					if ($pessoa_atual!= $pessoa_proximo){
						if ($padrao == "S"){
							$idPessoaTipo=resultadoSQL($consulta, $a , 'id');
							$total = emailTotalContasDominioPadrao($idDominio, $idPessoaTipo);
						}else 
							$total=emailTotalContasDominio($idDominio);
						$i=0;
						$matResultado[$matCabecalho[$i++]][$l]="<b> <div align=right> Total: </div></b>";
						$matResultado[$matCabecalho[$i]][$l]="<b>".$qtParcial.'/'.$total."</b>";
						$l++;
						
						$ttReservado += $total;
						$ttUso += $qtParcial;
						
						$qtParcial=0;
					}
				}	
				
				
			}							
			
			
			if ($matriz[consulta] && !$matriz['somenteMail']){
				htmlAbreLinha($corFundo);
				itemLinhaTMNOURL('<b>Total E-mails do POP</br>', 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, $zebra);
				$cc++;
				itemLinhaTMNOURL($ttUso . "/" . $ttReservado , 'right', 'middle', $matAlinhamento[$cc++], $corFundo, 0, 'txtcheck');
				htmlFechaLinha();
				//fechaTabela();
			}
			else{
				$c=0;
				$matResultado[$matCabecalho[$c++]][$l]="<b>Total de Contas</b>";
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.$ttUso . "/" . $ttReservado .'</b>';
			
			
				$matrizRelatorio[detalhe]=$matResultado;
			
				$matrizRelatorio[header][TITULO]="Pop/E-mail ".converteData($dtFinal,'banco','formdata');
				$matrizRelatorio[header][POP]=$nomePOP;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
			

			
				# Configurações
				$matrizRelatorio[config][linhas]=38;
				$matrizRelatorio[config][layout]='portrait';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
						
				$matrizGrupo[]=$matrizRelatorio;
			}
				
		}
		fechaTabela();
		$pp++;
	}
	
	if ( ! $matriz[consulta]){
	# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','email'),'email',$matrizRelatorio[config]);
				itemTabelaNOURL("&nbsp;", 'center', $corFundo, 7, 'txtaviso');
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatorio de E-mails</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
	}	
}

}
 
 
?>
