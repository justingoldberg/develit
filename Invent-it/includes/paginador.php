<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 19/12/2002
# Ultima altera��o: 06/06/2003
#    Altera��o No.: 003
#
# Fun��o:
#    Fun��es para paginador

// Fun��o para pagina��o
function paginador($consulta, $total, $limite, $registro, $classeCSS, $colunas, $urlADD)
{
	# Variaveis globais
	global $html, $conn, $modulo, $sub, $acao, $corFundo, $corBorda;
	
	if($acao!="listar" || $acao!="procurar") $acao='listar';
	
	if((is_numeric($registro) || !$registro) && $registro <= $total) {

		# Paginador - calculo das URLs
		# Calcular P�gina Anterior
		if( ($registro-$limite) >= 0) {
			# Pagina��o anterior, OK
			$paginador[anterior]=$registro-$limite;
		}
		# Calcular P�gina posterior
		if( $registro+$limite <= $total) {
			# Pagina��o Posterior
			$paginador[posterior]=$registro+$limite;
			if($total%$limite==0) $paginador[ultima]=((($total-$limite)/$limite)*$limite-($total%$limite));
			else $paginador[ultima]=(($total/$limite)*$limite-($total%$limite));
		}
		if($registro==0 && ($registro+$limite) < $total-1) {
			$paginador[posterior]=$registro+$limite;
			if($total%$limite==0) $paginador[ultima]=((($total-$limite)/$limite)*$limite-($total%$limite));
			else $paginador[ultima]=(($total/$limite)*$limite-($total%$limite));
		}
		
		# Caso pagina��o seja realmente feita, montar tabela do paginador
		if($paginador && $total > $limite) {
			novaLinhaTabela($corFundo, '100%');
				# Abrir linha em tabela de modulo para mostrar paginador
				htmlAbreColuna('100%', left, $corFundo, $colunas, 'normal');
		
					htmlAbreTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 3);
					# Mostrar marcadores
					htmlAbreLinha($corFundo);
					htmlAbreColuna('100', 'center', $corFundo, 0, 'tabfundo1');
					echo "<b>Navega��o</b>";
					htmlFechaColuna();
					
					# Verificar PRIMEIRA				
					if(($registro-$limite) > 0 && $registro > $limite )
					{
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$paginador[primeira]".$urlADD;
						$item.="<a href=$url><img src=".$html[imagem][setaprimeira]." border=0 alt='Primeira p�gina'>Primeira</a>";
					}
					
					# Verificar PAGINA ANTERIOR
					if($registro-$limite >= 0)
					{
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$paginador[anterior]".$urlADD;
						$item.="&nbsp;&nbsp;&nbsp;<a href=$url><img src=".$html[imagem][setaesquerda]." border=0 alt='P�gina anterior'>Anterior</a>";
				
					}
					# Verificar P�GINA POSTERIOR
					if($registro+$limite < $total)
					{
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$paginador[posterior]".$urlADD;
						$item.="&nbsp;&nbsp;&nbsp;<a href=$url>Posterior<img src=".$html[imagem][setadireita]." border=0 alt='P�gina posterior'></a>";
					}
	
					# Verificar ULTIMA
					if($registro+(2*$limite) < $total)
					{
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$paginador[ultima]".$urlADD;
						$item.="&nbsp;&nbsp;&nbsp;<a href=$url>�ltima<img src=".$html[imagem][setaultima]." border=0 alt='�ltima p�gina'></a>";
					}
	
					if($item) {
						htmlAbreColuna('130', 'center', $corFundo, 0, 'normal');
						$pgDe=intval((($registro/$limite)))+1;
						$pgAte=($total/$limite);
						# Verificar de pagina (ate) tem valor quebrado
						if(!is_integer($pgAte)){
							# Arredondar
							$pgAte=intval($pgAte)+1;
						}
						# Mostrar pagina��o
						echo "P�gina $pgDe de $pgAte";
						htmlFechaColuna();
						itemLinhaNOURL($item, 'center', $corFundo, 0, 'normal');
					}
	
			# Finaliza tabela do paginador.
				htmlFechaColuna();
			fechaLinhaTabela();
					
			htmlFechaLinha();
			fechaTabela();
		}
	}
} # fecha fun��o de paginador


// Fun��o para pagina��o
function paginador2($consulta, $total, $limite, $matriz, $classeCSS, $colunas, $urlADD)
{
	# Variaveis globais
	global $html, $conn, $modulo, $sub, $acao, $corFundo, $corBorda;
	
	if((is_numeric($matriz[pagina]) || !$matriz[pagina]) && $matriz[matriz] <= $total) {

		# Paginador - calculo das URLs
		# Calcular P�gina Anterior
		if( ($matriz[pagina]-$limite) >= 0) {
			# Pagina��o anterior, OK
			$paginador[anterior]=$matriz[pagina]-$limite;
		}
		# Calcular P�gina posterior
		if( $registro+$limite <= $total) {
			# Pagina��o Posterior
			$paginador[posterior]=$matriz[pagina]+$limite;
			if($total%$limite==0) $paginador[ultima]=((($total-$limite)/$limite)*$limite-($total%$limite));
			else $paginador[ultima]=(($total/$limite)*$limite-($total%$limite));
		}
		if($registro==0 && ($registro+$limite) < $total-1) {
			$paginador[posterior]=$matriz[pagina]+$limite;
			if($total%$limite==0) $paginador[ultima]=((($total-$limite)/$limite)*$limite-($total%$limite));
			else $paginador[ultima]=(($total/$limite)*$limite-($total%$limite));
		}
		
		# Caso pagina��o seja realmente feita, montar tabela do paginador
		if($paginador && $total > $limite) {
			novaLinhaTabela($corFundo, '100%');
				# Abrir linha em tabela de modulo para mostrar paginador
				htmlAbreColuna('100%', left, $corFundo, $colunas, 'normal');
		
					htmlAbreTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 3);
					# Mostrar marcadores
					htmlAbreLinha($corFundo);
					htmlAbreColuna('100', 'center', $corFundo, 0, 'tabfundo1');
					echo "<b>Navega��o</b>";
					htmlFechaColuna();
					
					# Verificar PRIMEIRA				
					if(($matriz[pagina]-$limite) > 0 && $matriz[pagina] > $limite )
					{
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[registro]&matriz[pagina]=$paginador[primeira]".$urlADD;
						$item.="<a href=$url><img src=".$html[imagem][setaprimeira]." border=0 alt='Primeira p�gina'>Primeira</a>";
					}
					
					# Verificar PAGINA ANTERIOR
					if($matriz[pagina]-$limite >= 0)
					{
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[registro]&matriz[pagina]=$paginador[anterior]".$urlADD;
						$item.="&nbsp;&nbsp;&nbsp;<a href=$url><img src=".$html[imagem][setaesquerda]." border=0 alt='P�gina anterior'>Anterior</a>";
				
					}
					# Verificar P�GINA POSTERIOR
					if($matriz[pagina]+$limite < $total)
					{
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[registro]&matriz[pagina]=$paginador[posterior]".$urlADD;
						$item.="&nbsp;&nbsp;&nbsp;<a href=$url>Posterior<img src=".$html[imagem][setadireita]." border=0 alt='P�gina posterior'></a>";
					}
	
					# Verificar ULTIMA
					if($matriz[pagina]+(2*$limite) < $total)
					{
						$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[registro]&matriz[pagina]=$paginador[ultima]".$urlADD;
						$item.="&nbsp;&nbsp;&nbsp;<a href=$url>�ltima<img src=".$html[imagem][setaultima]." border=0 alt='�ltima p�gina'></a>";
					}
	
					if($item) {
						htmlAbreColuna('130', 'center', $corFundo, 0, $classeCSS);
						$pgDe=intval((($matriz[pagina]/$limite)))+1;
						$pgAte=($total/$limite);
						# Verificar de pagina (ate) tem valor quebrado
						if(!is_integer($pgAte)){
							# Arredondar
							$pgAte=intval($pgAte)+1;
						}
						# Mostrar pagina��o
						echo "P�gina $pgDe de $pgAte";
						htmlFechaColuna();
						itemLinhaNOURL($item, 'center', $corFundo, 0, $classeCSS);
					}
	
			# Finaliza tabela do paginador.
				htmlFechaColuna();
			fechaLinhaTabela();
					
			htmlFechaLinha();
			fechaTabela();
		}
	}
} # fecha fun��o de paginador


?>
