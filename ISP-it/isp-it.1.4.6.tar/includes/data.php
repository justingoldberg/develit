<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 14/10/2002
# Ultima alteração: 20/03/2004
#    Alteração No.: 012
#
# Função:
#    Funções de data



# Função para buscar data do sistema
function dataSistema()
{
	# Receber data do sistema
	$dtSistema = getdate();
	$data[dia]=$dtSistema['mday'];
	$data[mes]=$dtSistema['mon'];
	$data[ano]=$dtSistema['year'];
	$data[hora]=$dtSistema['hours'];
	$data[min]=$dtSistema['minutes'];
	$data[seg]=$dtSistema['seconds'];
	
	# Verificar campos de data
	if(strlen($data[dia])==1)
	{
		$data[dia]="0".$data[dia];
	}
	if(strlen($data[mes])==1)
	{
		$data[mes]="0".$data[mes];
	}
	if(strlen($data[hora])==1)
	{
		$data[hora]="0".$data[hora];
	}
	if(strlen($data[min])==1)
	{
		$data[min]="0".$data[min];
	}
	if(strlen($data[seg])==1)
	{
		$data[seg]="0".$data[seg];
	}
	
	$data[dataBanco]=$data[ano].'-'.$data[mes].'-'.$data[dia].' '.$data[hora].':'.$data[min].':'.$data[seg];
	$data[dataNormal]=$data[dia].'-'.$data[mes].'-'.$data[ano].' '.$data[hora].':'.$data[min].':'.$data[seg];
	$data[dataNormalData]=$data[dia].'/'.$data[mes].'/'.$data[ano];
	$data[dataBancoGrapi]=$data[ano].'-'.$data[mes].'-'.$data[dia];
	$data[dataCadGrapi]=$data[ano].$data[mes].$data[dia]."000000";
	
	return($data);
	
}


// Conversão de Data
/**
 * @return array
 * @param datetime/timestamp $data
 * @param string $origem
 * @param string $destino
 * @desc Conversão/timestamp de data
 <b>data</b> Data original [date/datetime/timestamp]
 <b>origem</b> String com o formato original da data [banco/form/formdata/timestamp]
 <b>destino</b> String com o formato de destino da data [banco/form/formdata/timestamp]
*/
function converteData($data, $origem, $destino)
{
	
	$dtSistema=dataSistema();

	if(strlen(trim($data)>0)) {
		if(!$data || !$origem || !$destino)
		{
			$msg="Parâmetros insuficientes para conversão de data!";
			aviso("ERRO", $msg, '1', 'x', '200');
		}
		else {
			// converter datas
			if($origem == "banco") {
				if( $destino == "form" ) {
					// Converter de data do banco para data normal
					$dia=substr($data,8,2);
					$mes=substr($data,5,2);
					$ano=substr($data,0,4);
					$hora=substr($data,11,2);
					$min=substr($data,14,2);
					$seg=substr($data,17,2);
					
					$data="$dia/$mes/$ano $hora:$min:$seg";
					
				}
				elseif($destino == "formdata") {
					// Converter de data do banco para data normal
					$dia=substr($data,8,2);
					$mes=substr($data,5,2);
					$ano=substr($data,0,4);
					
					$data="$dia/$mes/$ano";
				}
				elseif($destino == "formhora") {
					// Converter de data do banco para data normal
					$hora=substr($data,11,2);
					$min=substr($data,14,2);
					$seg=substr($data,17,2);
					
					$data="$hora:$min:$seg";
				}
				elseif($destino == 'timestamp') {
					// converter para unix format
					$dia=substr($data,8,2);
					$mes=substr($data,5,2);
					$ano=substr($data,0,4);
					$hora=substr($data,11,2);
					$min=substr($data,14,2);
					$seg=substr($data,17,2);
					
					$data=mktime($hora,$min,$seg,$mes, $dia, $ano);
				}
				elseif($destino == "remessa") {
					// Converter de data do banco para data normal
					$dia=substr($data,8,2);
					$mes=substr($data,5,2);
					$ano=substr($data,0,4);
					
					$data="$ano$mes$dia";
				}
	
			}
			elseif($origem == "form") {
				$data=formatarData($data);
				
				if($destino == "banco") {
					// Converter de data normal para data do banco
					$dia=substr($data,0,2);
					$mes=substr($data,2,2);
					$ano=substr($data,4,4);
					$hora=substr($data,9,2);
					$min=substr($data,12,2);
					$seg=substr($data,15,2);
					
					$data="$ano-$mes-$dia $hora:$min:$seg";
				}
				elseif($destino == "bancodata") {
					// Converter de data normal para data do banco
					$dia=substr($data,0,2);
					$mes=substr($data,2,2);
					$ano=substr($data,4,4);
					
					$data="$ano-$mes-$dia";
				}
				elseif($destino == 'timestamp') {
					// converter para unix format
					$dia=substr($data,0,2);
					$mes=substr($data,2,2);
					$ano=substr($data,4,4);
					$hora=substr($data,9,2);
					$min=substr($data,12,2);
					$seg=substr($data,15,2);
					
					$data=mktime($hora,$min,$seg,$mes, $dia, $ano);
				}
			}
			elseif($origem=='timestamp') {
				if($destino=='formdata') $data=date('d/m/Y',$data);
				if($destino=='form') $data=date('d/m/Y H:i:s',$data);
				if($destino=='banco') $data=date('Y-m-d H:i:s',$data);
				if($destino=='formhora') {
					# quebrar tempo
					$matriz[horas]=intval($data/3600);
					$matriz[minutos]=intval( ($data/3600 - $matriz[horas]) * 60 );
					$matriz[segundos]=intval(((($data/3600 - $matriz[horas]) * 60) - $matriz[minutos]) * 60);
					
					if($matriz[horas]) {
						$retorno.="$matriz[horas]h, ";
					}
					
					if($matriz[minutos]) {
						$retorno.="$matriz[minutos]m, ";
					}
					
					$data=$retorno." $matriz[segundos]s";
				}
			}
			elseif($origem == "arquivo")
			{
				if( $destino == "formdata" ) {
					// Converter de data do banco para data normal
					$dia=substr($data,0,2);
					$mes=substr($data,2,2);
					$ano=substr($data,4,4);
					
					if($ano > $dtSistema[ano]) $ano="20".substr($data,4,2);
			
					$data="$dia/$mes/$ano";
				}
				elseif ( $destino == 'form' ){
					// Converter de data do banco para data normal
					$dia=substr($data,6,2);
					$mes=substr($data,4,2);
					$ano=substr($data,0,4);
			
					$data="$dia/$mes/$ano";
				}
				elseif ($destino == 'banco') {
				
					$dia=substr($data,0,2);
					$mes=substr($data,2,2);
					$ano=substr($data,4,4);
					$hora=substr($data,9,2);
					$min=substr($data,12,2);
					$seg=substr($data,15,2);
					
					if($ano > $dtSistema[ano] || $ano < 2004) $ano="20".substr($data,4,2);
					
					$data="$dia/$mes/$ano 00:00:00";
				}
			}
			elseif($origem == "retorno") {
				if( $destino == "formdata" ) {
					// Converter de data do banco para data normal
					$ano=substr($data,0,4);
					$mes=substr($data,4,2);
					$dia=substr($data,6,2);
			
					$data="$dia/$mes/$ano";
				}
				elseif( $destino == "banco" ) {
					// Converter de data do banco para data normal
					$ano=substr($data,0,4);
					$mes=substr($data,4,2);
					$dia=substr($data,6,2);
			
					$data="$ano/$mes/$dia 00:00:00";
				}
			}
			elseif($origem == "sistema") {
				
				if($destino == "remessa") {
					// Converter de data do banco para data normal
					$dia=substr($data,0,2);
					$mes=substr($data,3,2);
					$ano=substr($data,6,4);
					
					$data="$ano$mes$dia";
				}
			}
		}
		
		// Retornar data convertida
		return($data);
	}
}



# Função para calculo da quantidade de dias do mes
/**
 * @return string
 * @param int $mes
 * @desc Retorna o ultimo dia do mes
*/
function dataDiasMes($mes) {

	return(date('t',$mes));
}



# Calcular Dias de Diferenca entre 2 datas
function calculaDiasDiferenca($dtInicial, $dtFinal) {

	# converter datas para timestamp
	$dtInicial=converteData($dtInicial, 'banco','timestamp');
	$dtFinal=converteData($dtFinal, 'banco','timestamp');
	
	# Dias Antecipados
	if($dtInicial < $dtFinal) {
		$resultado=($dtFinal - $dtInicial);
		
		$dias=intval(($resultado/60/60/24));
		
		$retorno[texto]="<span class=txtok>antecipado em $dias dias</span>";
		$retorno[tipo]='antecipado';
	}
	
	# Dias em atraso
	elseif($dtInicial > $dtFinal) {
		$resultado=($dtInicial - $dtFinal);
		
		$dias=intval(($resultado/60/60/24));
		
		$retorno[texto]="<span class=txtaviso>atrasado em $dias dias</span>";
		$retorno[tipo]='atrasado';
	}
	
	# Em Dia
	elseif($dtFinal == $dtInicial) {
		$retorno[texto]="<span class=txtok>em dia</span>";
		$retorno[tipo]='emdia';
	}
	
	return($retorno);
	
}


/**
 * @return void
 * @param unknown $data <BR>
<B>Formato:</B> AAAA/MM/DD
 * @desc Adicionar 1 mes inteiro a data de vencimento
*/
function dataSomaMes($data, $meses) {

	# Quebrar Data
	$dia=substr($data,8,2);
	$mes=substr($data,5,2);
	$ano=substr($data,0,4);
	
	$data=mktime(0, 0, 0, $mes+($meses), $dia, $ano);
	
	return(converteData($data, 'timestamp','banco'));
	
}

function dataSubtraiMeses( $ano='', $mes='', $dia=1, $qtd=1 ){
	
	if( !$ano || !$mes ){
		$data = dataSistema();
		
		$ano = $data['ano'];
		$mes = $data['mes'];
		$dia = ( $dia ? $dia : $data['dia'] );
	}
	
	$novaData = mktime(0, 0, 0, $mes-$qtd, $dia, $ano);
	
	return array( 	"ano" => date('Y', $novaData), 
					"mes" => date('m', $novaData), 
					"dia" => date('d', $novaData) );
}


?>
