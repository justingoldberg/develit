<?

class BoletoSicoob extends BoletoModelo {
	
	var $logo_banco = 'logosicoob.png';
	
	var $codigo_banco = '756';
	
	var $codigo_banco_com_dv;
	
	var $carteira = "1";
	
	var $agencia_codigo;
	/**
	 * @var string
	 */
	var $inicio_nosso_numero = '9';
	/**
	 * @var string
	 */
	var $nosso_numero_completo;
	
	var $num_moeda = '9';
	
	var $conta_cedente;
	
	var $conta_cedente_dg;
	
	var $digitoAgencia;
	
	var $parcela = "001";
	
	var $cooperativa;
	
	var $modalidade;
	
	var $cliente;
	
	var $digitoVerificador;
	
	var $convenio;

	
	function BoletoSicoob() {
		parent::BoletoModelo();
	}
	
	function modulo_10($num) {
		$numtotal10 = 0;
		$fator = 2;
 
		for ($i = strlen($num); $i > 0; $i--) {
			$numeros[$i] = substr($num,$i-1,1);
			$parcial10[$i] = $numeros[$i] * $fator;
			$numtotal10 .= $parcial10[$i];
			if ($fator == 2) {
				$fator = 1;
			}
			else {
				$fator = 2; 
			}
		}
	
		$soma = 0;
		for ($i = strlen($numtotal10); $i > 0; $i--) {
			$numeros[$i] = substr($numtotal10,$i-1,1);
			$soma += $numeros[$i]; 
		}
		$resto = $soma % 10;
		$digito = 10 - $resto;
		if ($resto == 0) {
			$digito = 0;
		}

		return $digito;
	}
	
	function modulo_11($num, $base=9, $r=0) {
		$soma = 0;
		$fator = 2;
		for ($i = strlen($num); $i > 0; $i--) {
			$numeros[$i] = substr($num,$i-1,1);
			$parcial[$i] = $numeros[$i] * $fator;
			$soma += $parcial[$i];
			if ($fator == $base) {
				$fator = 1;
			}
			$fator++;
		}
		
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;

			//corrigido
			if ($digito == 10) {
				$digito = "X";
			}

			/*
			alterado por mim, Daniel Schultz

			Vamos explicar:

			O m�dulo 11 s� gera os digitos verificadores do nossonumero,
			agencia, conta e digito verificador com codigo de barras (aquele que fica sozinho e triste na linha digit�vel)
			s� que � foi um rolo...pq ele nao podia resultar em 0, e o pessoal do phpboleto se esqueceu disso...

			No BB, os d�gitos verificadores podem ser X ou 0 (zero) para agencia, conta e nosso numero,
			mas nunca pode ser X ou 0 (zero) para a linha digit�vel, justamente por ser totalmente num�rica.

			Quando passamos os dados para a fun��o, fica assim:

			Agencia = sempre 4 digitos
			Conta = at� 8 d�gitos
			Nosso n�mero = de 1 a 17 digitos

			A unica vari�vel que passa 17 digitos � a da linha digitada, justamente por ter 43 caracteres

			Entao vamos definir ai embaixo o seguinte...

			se (strlen($num) == 43) { n�o deixar dar digito X ou 0 }
			*/

			if (strlen($num) == "43") {
				//ent�o estamos checando a linha digit�vel
				if ($digito == "0" or $digito == "X" or $digito > 9) {
					$digito = 1;
				}
			}
			return $digito;
		}
		elseif ($r == 1){
			$resto = $soma % 11;
			return $resto;
		}
	}

	function carregaDadosBoleto() {

		$this->codigo_banco_com_dv = $this->geraCodigoBanco();
		/* Caso exista o digito da Ag�ncia, concatena o mesmo com o agencia/codigo do cedente exibido no boleto, ex: 3008-5/52703-3 */
		$this->agencia_codigo = $this->agencia . (!empty($this->digitoAgencia) ? "-$this->digitoAgencia" : "") . '/'.$this->conta . (empty($this->digito_conta) ? "" : "-$this->digito_conta");
		$this->set_nosso_numero_completo();
		$this->setCodigoBarras();
		$this->monta_linha_digitavel();
	}	
	
	/* Modificado para atender o padrão do banco Sicoob */
	function digitoVerificador_nossonumero() {
		/* Monta um Array */
		$numero = $this->agencia . $this->formata_numero($this->conta.$this->digito_conta, 10, 0) . $this->formata_numero($this->nosso_numero, 7, 0);
		
		$arraySize = strlen($numero);
		$soma = 0;
		$fator = array();
		
		/* Monta um Array com o fator de multiplicacão */
		for ($i = 0; $i < $arraySize; $i += 4) {
			$fator[$i]   = 3;
			$fator[$i+1] = 1;
			$fator[$i+2] = 9;
			$fator[$i+3] = 7;
		}
		
		/* Multiplica cada índice do Array de acordo com o fator de multiplicacão definido acima */
		$soma = 0;
		for ($i = 0; $i < $arraySize; $i += 4) {
			$soma += $numero[$i] * $fator[$i];
			$soma += $numero[$i+1] * $fator[$i+1];
			$soma += $numero[$i+2] * $fator[$i+2];
			$soma += $numero[$i+3] * $fator[$i+3];
		}
		
		$resto = $soma % 11;
		$dv = 11 - $resto;
		/* Sempre que o DV for maior do que 9, então ele deve assumir como valor o número 0 */
		if ($dv > 9) {
			$dv = 0;
		}
		
		return $dv;
	}
	
	/**
	 * M�todo sobreescrito
	 *
	 * @param string $numero
	 * @return string
	 */
	function digitoVerificador_barra() {
		$numero = $this->codigo_banco . $this->num_moeda . $this->fator_vencimento($this->data_vencimento) . $this->formata_numero($this->valor, 10, 0, "valor") . $this->carteira . $this->agencia . $this->modalidade . $this->formata_numero($this->conta . $this->digito_conta, 7, 0) . $this->nosso_numero_completo . $this->parcela;
		
		$resto = $this->modulo_11($numero, 9, 1);
		if ($resto == 0 || $resto == 1 || $resto == 10) {
			$dv = 1;
		} else {
			$dv = 11 - $resto;
		}
		
		return $dv;
	}
	
	function set_nosso_numero_completo() {
		$this->nosso_numero = $this->nosso_numero . $this->digitoVerificador_nossonumero();
		$this->nosso_numero_completo = $this->formata_numero($this->nosso_numero, 8, 0, "geral");
	}
	
	function setCodigoBarras() {
		$this->codigo_barras = $this->codigo_banco . 
							   $this->num_moeda . 
							   $this->digitoVerificador_barra() . 
							   $this->fator_vencimento($this->data_vencimento) . 
							   $this->formata_numero($this->valor, 10, 0, 'valor') . 
							   $this->carteira . 
							   $this->agencia . 
							   $this->modalidade . 
							   $this->formata_numero($this->conta . $this->digito_conta, 7, 0) . 
							   $this->nosso_numero_completo . 
							   $this->parcela;
	}
	
	function setLinhaDigitavel() {
		$this->linha_digitavel = $this->codigo_banco .
								 $this->num_moeda .
								 $this->carteira .
								 $this->agencia .
								 $this->modalidade .
								 $this->formata_numero($this->conta . $this->digito_conta, 7, 0) .
								 $this->nosso_numero_completo .
								 $this->parcela;
	}
	
	function monta_linha_digitavel() {
		$this->setLinhaDigitavel();
		
		$p1  = substr($this->linha_digitavel, 0, 5);
		$p2  = substr($this->linha_digitavel, 5, 4);
		$p2 .= $this->modulo_10("$p1$p2"); // Digito Verificador Primeiro grupo

		$campo1 = "$p1.$p2";
		
		$p1  = substr($this->linha_digitavel, 9, 5);
		$p2  = substr($this->linha_digitavel, 14, 5);
		$p2 .= $this->modulo_10("$p1$p2"); // Digito verificador Segundo grupo
		
		$campo2 = "$p1.$p2";
		
		$completaCampo = $this->formata_numero(substr($this->linha_digitavel, 19, 10), 10, 0, "convenio");
		$p1  = substr($completaCampo, 0, 5);
		$p2  = substr($completaCampo, 5, 10);
		$p2 .= $this->modulo_10("$p1$p2"); // Digito verificador Terceiro grupo

		$campo3 = "$p1.$p2";
		
		$campo4  = " " . $this->digitoVerificador_barra() . " "; //Digito Verificador do C�digo de Barras

		$campo5  = $this->fator_vencimento($this->data_vencimento);
		$campo5 .= $this->formata_numero($this->valor, 10, 0, 'valor');
		
		$this->linha_digitavel = "$campo1 $campo2 $campo3 $campo4 $campo5";
	}
		
}

?>