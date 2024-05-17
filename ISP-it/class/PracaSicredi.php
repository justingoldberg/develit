<?

/**
 * Gerencia as Praças existentes do banco SICREDI
 *
 */
class PracaSicredi extends InterfaceBD {
	
	var $praca;
	var $cidade;
	var $estado;
	var $situacao;
	var $tipoCobranca;
	var $agencia;
	var $posto;
	
		
	function PracaSicredi( ){
		$this->campos = array( "praca", "cidade", "estado", "situacao", "tipoCobranca", "agencia", "posto" );
		$this->tabela = 'PracasSicredi';
		
		$this->InterfaceBD();
	}
	
	function parseLinha( $linha ){
		$this->praca		= substr( $linha, 1, 6 );
		$this->cidade		= rtrim( substr( $linha, 7, 25 ) );
		$this->estado		= substr( $linha, 32, 2 );
		$this->situacao		= substr( $linha, 34, 1 );
		$this->tipoCobranca	= substr( $linha, 35, 1 );
		$this->agencia		= substr( $linha, 36, 4 );
		$this->posto		= substr( $linha, 40, 2 );
	}
	

	/**
	 * Retorna o código da praça da determinada $cidade
	 *
	 * @param string $cidade
	 * @return unknown
	 */
	function getPraca( $cidade ){	
		$resul = $this->seleciona( '', '', "cidade='" . $cidade . "'" );
		return ( $resul[0]->praca );
	}
}

?>