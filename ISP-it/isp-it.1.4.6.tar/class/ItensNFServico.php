<?
################################################################################
#       Criado por: Lis
#  Data de cria��o: 30/05/2007
# Ultima altera��o: 30/05/2007
#    Altera��o No.: 001
#
# Fun��o:
#    Item Nota Fiscal Servi�o - Classe para inclusao de Itens � NF de Servi�o de comunica��o
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