<?

class BoletoCEF extends BoletoModelo {
	
	var $logo_banco = 'logocaixa.jpg';
	
	var $codigo_banco = '104';
	
	var $codigo_banco_com_dv;
	
	var $agencia_codigo;
	/**
	 * Inicio do Nosso numero - Pode ser 80 ou 81 ou 82 (Confirmar com gerente qual usar)
	 *
	 * @var string
	 */
	var $inicio_nosso_numero = '80';
	/**
	 * nosso número (sem dv) é 10 digitos
	 *
	 * @var string
	 */
	var $nosso_numero_completo;
	
	var $num_moeda = '9';
	
	var $conta_cedente = '';
	
	var $conta_cedente_dg;

	
	function BoletoCEF(){
		parent::BoletoModelo();
	}

	function carregaDadosBoleto(){

		$this->codigo_banco_com_dv = $this->geraCodigoBanco();
		$this->agencia_codigo = $this->agencia.'/'.$this->conta_cedente;
		$this->set_nosso_numero_completo();
		$this->setCodigoBarras();
		$this->monta_linha_digitavel();
	}	
	
	function digitoVerificador_nossonumero($numero) {
		$resto2 = $this->modulo_11($numero, 9, 1);
		$digito = 11 - $resto2;
		if ($digito == 10 || $digito == 11) {
			$dv = 0;
		} else {
			$dv = $digito;
		}
		return $dv;
	}
	
	/**
	 * Método sobreescrito
	 *
	 * @param string $numero
	 * @return string
	 */
	function digitoVerificador_barra() {
		$numero = 	$this->codigo_banco . $this->num_moeda . $this->fator_vencimento($this->data_vencimento) . $this->formata_numero($this->valor, 10, 0, 'valor') .
					$this->nosso_numero_completo . $this->agencia . $this->conta_cedente;
		$resto2 = $this->modulo_11($numero, 9, 1);
		if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
			$dv = 1;
		} else {
			$dv = 11 - $resto2;
		}
		return $dv;
	}
	
	function set_nosso_numero_completo() {
		$this->nosso_numero_completo = $this->inicio_nosso_numero . $this->formata_numero($this->nosso_numero,8,0);
	}
	
	function setCodigoBarras(){
		$this->codigo_barras =	$this->codigo_banco . $this->num_moeda . $this->digitoVerificador_barra().$this->fator_vencimento($this->data_vencimento) . $this->formata_numero($this->valor, 10, 0, 'valor') .
						$this->nosso_numero_completo . $this->agencia . $this->conta_cedente;

	}
	
	function monta_linha_digitavel() {
		$codigo = $this->codigo_barras;
		// Posição 	Conteúdo
		// 1 a 3    Número do banco
		// 4        Código da Moeda - 9 para Real
		// 5        Digito verificador do Código de Barras
		// 6 a 9   Fator de Vencimento
		// 10 a 19 Valor (8 inteiros e 2 decimais)
		// 20 a 44 Campo Livre definido por cada banco (25 caracteres)
	
		// 1. Campo - composto pelo código do banco, código da moéda, as cinco primeiras posições
		// do campo livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 0, 4);
		$p2 = substr($codigo, 19, 5);
		$p3 = $this->modulo_10("$p1$p2");
		$p4 = "$p1$p2$p3";
		$p5 = substr($p4, 0, 5);
		$p6 = substr($p4, 5);
		$campo1 = "$p5.$p6";
	
		// 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
		// e livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 24, 10);
		$p2 = $this->modulo_10($p1);
		$p3 = "$p1$p2";
		$p4 = substr($p3, 0, 5);
		$p5 = substr($p3, 5);
		$campo2 = "$p4.$p5";
	
		// 3. Campo composto pelas posicoes 16 a 25 do campo livre
		// e livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 34, 10);
		$p2 = $this->modulo_10($p1);
		$p3 = "$p1$p2";
		$p4 = substr($p3, 0, 5);
		$p5 = substr($p3, 5);
		$campo3 = "$p4.$p5";
	
		// 4. Campo - digito verificador do codigo de barras
		$campo4 = substr($codigo, 4, 1);
	
		// 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
		// indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
		// tratar de valor zerado, a representacao deve ser 000 (tres zeros).
		$p1 = substr($codigo, 5, 4);
		$p2 = substr($codigo, 9, 10);
		$campo5 = "$p1$p2";
	
		$this->linha_digitavel = "$campo1 $campo2 $campo3 $campo4 $campo5";
	}
		
}

?>