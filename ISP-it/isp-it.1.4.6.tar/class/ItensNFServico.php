<?
################################################################################
#       Criado por: Lis
#  Data de criaчуo: 30/05/2007
# Ultima alteraчуo: 30/05/2007
#    Alteraчуo No.: 001
#
# Funчуo:
#    Item Nota Fiscal Serviчo - Classe para inclusao de Itens р NF de Serviчo de comunicaчуo
################################################################################

Class ItensNFServico extends InterfaceBD{ // inicio da Classe

	var $tabela = 'ItensNFServico';
	var $campos = array( "id", "idNFS", "discriminacao", "valor", "isento" );

	// Att da classe
	var $id;
	var $idNFS;
	var $discriminacao;
	var $valor;
	var $isento;
	
	function ItensNFServico(){
		$this->InterfaceBD();
	}
	
	function getId(){
		return $this->id;
	}
		
	function setId( $_id){
		$this->id =	$_id;
	}
	
	function getidNFS(){
		return $this->idNFS;
	}
		
	function setIdNFS( $_idNFS){
		$this->idNFS = 	$_idNFS;
	}
	
	function getDiscriminacao(){
		return $this->discriminacao;
	}
		
	function setDiscriminacao( $_discr){
		$this->discriminacao = 	$_discr;
	}
	
	function getValor(){
		return $this->valor;
	}
		
	function setValor( $_valor){
		$this->valor = 	$_valor;
	}
	
	function getIsento(){
		return $this->isento;
	}
	
	function setIsento( $_isento ){
		$this->isento = $_isento;
	}
	
} // fim da classe

?>