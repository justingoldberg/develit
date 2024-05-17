<?
##################################################
#       Criado por: Rogério Ramos - Devel-it     #
#  Data de criação: 02/12/2004                   #
# Ultima alteração: 02/12/2004                   # 
#    Alteração No.: 001                          # 
#                                                #
# Função:                                        #
# Funções para relatório de Ocorrencias          #
##################################################

# linhas suprimidas por terem sido definidas em config/pdf.php
//define('FPDF_FONTPATH',$pathFPDF.'fpdf/font/');
//require($pathFPDF.'fpdf/fpdf.php');

class Ocorrencias2Pdf extends FPDF{ // classe que gera relatorio no formato PDF

	//variaveis utilizadas na classe
	var  $larg_tot, $larg_proporcional, $orientacao, $margemDir, $margemEsq, $imagem;
	var $titulo, $sub, $texto, $matLargura, $matAlinhamento, $matCabecalho, $matDetalhe, $consulta, $pop, $conn;
	//variaveis utilizadas na classe
	
	function Header(){
		
		$this->SetY(10);
		//$this->Image($this->imagem,$this->margemEsq,10,40,23);
		$this->SetFont('arial','B',16);
		$this->SetXY(50, $this->GetY());
		$this->MultiCell('',10,$this->titulo,0,'C');
		$this->SetXY(50, $this->GetY());
		$this->SetFont('arial','B',13);
		$this->MultiCell('',8,$this->sub,0,'C');
		$this->SetXY(50, $this->GetY());
		$this->SetFont('arial','B',11);
		$this->MultiCell('',5,$this->texto,0,'C');
		$this->Ln();
		// define o titulo de cada coluna do relatorio
		$posY= $this->GetY(); // pega a posicao Y (vert) atual
		$posX= $this->GetX(); // pega a posicao X (horiz) atual
		$this->Ln();
	}
	
	function Footer(){
		$this->Ln();
		$fooY=$this->GetY();
		$this->SetX(10);
		$this->Cell(40,5,date('d/m/Y'),'TB',0,'L');
		$this->SetX($this->GetX());
		$this->Cell('',5,$this->PageNo().'/{nb}','TB',0,'R');
	}
	
	function nomeCampo($result){
		$count= mysql_num_fields($result);
		for ($z=0; $z < $count; $z++)
			$nome= mysql_field_name($result, $count);
		return $nome;
	}
	
	//funcao para a geracao do relatorio
	
	/**
	* @return void
	* @param unknown $orientacao
	* @param unknown $tam_papel
	* @param unknown $margens
	* @param unknown $cabecalho
	* @param unknown $rodape
	* @desc funcao que gera  Relatorios em formato PDF utilizando a classe FPDF
	*/
	function geraImpressao($matRelatorio){
		 global $arquivo, $sessLogin;
		
		//--------------------------------- teste ---------------------------------------
		
		
		// instanciamos a classe geradora de arquivos  PDF
		//$this= new Relatorio2Pdf($orientacao,'mm','A4');
		
		//variaveis de controle de margens
		$this->margemDir=(substr($matRelatorio[config][marginright],0,1)*10);
		$this->margemEsq=(substr($matRelatorio[config][marginleft],0,1)*10);
		$this->orientacao= (strtoupper(substr($matRelatorio[config][layout],0,1)));
		$this->imagem= $matRelatorio[header][IMG_LOGO];
		$this->titulo= $matRelatorio[header][TITULO];
		$this->sub= $matRelatorio[header][SUBTITULO];
		$this->texto= $matRelatorio[header][TEXTO];
		$this->matLargura= $matRelatorio[config][largura];
		$this->matAlinhamento= $matRelatorio[config][alinhamento];
		$this->matCabecalho= $matRelatorio[header][cabecalho];
		$this->matDetalhe= $matRelatorio[detalhe];
		$this->consulta= $matRelatorio['consulta'];
		$this->pop= $matRelatorio['POP'];
		$this->conn = $matRelatorio['conn'].value;
		//$this->SetTopMargin($margens[sup]); // define a margem superior
		$this->SetLeftMargin($this->margemEsq); // define a margem esq
		$this->SetRightMargin($this->margemDir); // define a margem dir
		$this->AliasNbPages();
		$this->AddPage($this->orientacao); // Adiciona uma página para iniciar a geração do Arquivo
		$this->SetAutoPageBreak(true,10);
		
		//a partir daqui vamos carregar o corpo do relatorio
		$this->SetFont('arial','','10');
		
		/*for ($i=0; $i<=(count($this->matDetalhe[0]));$i++){
			$posY= $this->GetY(); // pega a posicao Y (vert) atual
			$posX= $this->GetX(); // pega a posicao X (horiz) atual*/
			//foreach ( $this->pop as $POP){
				//$this->SetX($this->margemEsq);
				$this->SetFont('arial', 'b', 12);
				$this->MultiCell('',6,$this->pop,0,'L');
				$cliAnt= 0;
				$ocorAnt= 0;
				for ( $i=0; $i< contaConsulta($this->consulta); $i++ ){
					//if ( ( $i -1 ) > 0 )	
						//$cliAnt= resultadoSQL($this->consulta, ($i-1), 'idPessoa');
					//if ( ( $i-1 ) > 0 )
						//$cliAnt= resultadoSQL($this->consulta, ($i-1), 'idPessoa');
					$cliAtual= resultadoSQL($this->consulta, $i, 'idPessoa');
					
					//if ( ( $i -1 ) > 0 )	
						//$ocorAnt= resultadoSQL($this->consulta, ($i-1), 'idOcorrencia');
					//if ( ( $i-1 ) > 0 )
						//$ocorAnt= resultadoSQL($this->consulta, ($i-1), 'idOcorrencia');
					$ocorAtual= resultadoSQL($this->consulta, $i, 'idOcorrencia');
					
					$idUsrOcorrencia = resultadoSQL( $this->consulta, $i, 'usrOcorrencia' );
					$idUsrComentario = resultadoSQL( $this->consulta, $i, 'usrComentario' );
					
					if ( $idUsrOcorrencia != '' && $idUsrOcorrencia == $idUsrComentario){
						$sql= "SELECT login FROM Usuarios WHERE id= ".$idUsrOcorrencia;
						$consultaUsr= @mysql_query( $sql );
						if (contaConsulta($consultaUsr)>0){
							$usrOcorrencia = resultadoSQL( $consultaUsr, 0, 'login');
							$usrComentario = $usrOcorrencia;
						} 
					} else {
						if ( !is_null( $idUsrOcorrencia ) ) {
							$sql= "SELECT login FROM Usuarios WHERE id= ".$idUsrOcorrencia;
							$consultaUsr=  @mysql_query( $sql );
							if (contaConsulta($consultaUsr)>0)
								$usrOcorrencia = resultadoSQL( $consultaUsr, 0, 'login');
						}
						if ( !is_null( $idUsrComentario ) ){
							$sql= "SELECT login FROM Usuarios WHERE id= ".$idUsrComentario;
							$consultaUsr=  @mysql_query( $sql );
							if (contaConsulta($consultaUsr)>0)
								$usrComentario = resultadoSQL( $consultaUsr, 0, 'login');
						}
					}
					
					if ( $cliAnt != $cliAtual ){
						$this->SetX(15);
						$this->SetFont('arial', 'b', 10);
						$this->MultiCell('',6,resultadoSQL($this->consulta, $i, 'nomePessoa'),0,'L');
					}
					
					if ( $ocorAnt != $ocorAtual ){
						//Nome, data e criador da Ocorrencia
						$this->SetX(20);	
						$this->SetFont('arial', 'i',9);
						$mostraOcorrencia = "Criado por: ".$usrOcorrencia." - ".converteData(resultadoSQL($this->consulta, $i, 'data'),'banco', 'form')." - ";
						$this->MultiCell('', 6, $mostraOcorrencia.resultadoSQL($this->consulta, $i, 'nomeOcorrencia'),0,'L');
					}
					
					//Descricao da Ocorrencia
					if ( $ocorAnt != $ocorAtual ) {
						$descr = '';
						if (is_null( resultadoSQL($this->consulta, $i, 'descricaoOcorrencia') ) ? $descr =' ' : $descr = resultadoSQL($this->consulta, $i, 'descricaoOcorrencia') );
						$identOcorrencia = converteData(resultadoSQL($this->consulta, $i, 'data'),'banco', 'form')." - ".$usrOcorrencia;
						$this->SetX(25);
						$this->SetFont('arial', '', 8);
						$this->MultiCell('', 6, $identOcorrencia." - ".$descr,0,'L');
					}
					
					if (!is_null(resultadoSQL($this->consulta, $i, 'textoComentario'))){
						$this->SetX(25);
						if (!is_null( resultadoSQL( $this->consulta, $i, 'usrComentario' ) ) ){
							$identificacao = converteData( resultadoSQL( $this->consulta, $i, 'dataComentario' ), 'banco', 'form' )." - ".$usrComentario; //resultadoSQL( $consultaUsr, 0, 'login' ).": ";
						}
						$this->MultiCell('', 6, $identificacao." - ".resultadoSQL($this->consulta, $i, 'textoComentario'), 0, 'L');
					}
					
					$cliAnt= $cliAtual;
					$ocorAnt= $ocorAtual;
				}
			//}
				
					
				
			//}
			/*for($j=0;$j<(count($this->matDetalhe)-1);$j++){ //tem um -1 no count matriz detalhe
				$this->SetY($posY); // posiciona o ponteiro na posicao da linha
				$this->SetX($posX); // posiciona o ponteiro na posicao da proxima coluna
				print "PosY->".$posY." PosX->".$posX;
				$this->MultiCell($this->larg_proporcional[$j],6,$this->matDetalhe[$j][$i],1,strtoupper(substr($this->matAlinhamento[$j],0,1)));	
				$posX=$posX+$this->larg_proporcional[$j];
				print "  Nova PosX->".$posX."<br>";
			}*/
			
		//}
		// fim do corpo do relatorio
		//$this->Ln(); //$this->Ln(); 
		$this->pop= str_replace(" ", "", $this->pop);
		$this->pop= str_replace(" - ", "_", $this->pop);
		$this->pop = retirarAcentos($this->pop, 1);//remove os acentos dos textos.
		//$this->pop= addslashes($this->pop);
		$nomeArquivo=$arquivo[tmpPDF].'_'.$this->pop.'_'.$sessLogin[login].'_ocorrencias2pdf.pdf';
			if(file_exists($nomeArquivo)){
				unlink($nomeArquivo); 	
			}
		$this->Output($nomeArquivo,'F');
		return $nomeArquivo;
		//$this->Output();
	}
	
}

/*	$oPrint=  new Relatorio2Pdf();
	
	$cabecalho[titulo]= "Seu Titulo Aqui...";
	$cabecalho[sub]= "Seu SubTitulo Aqui...";
	$cabecalho[texto]= "Seu texto opcional aqui...";
	
	$matCabecalho= array('Código','Nome','Cidade','Estado','Fone');
	$matLargura= array('15%','40%','30%','15%','15%');
	$matAlinhamento= array('right','left','left','center','left');
	
	$matDetalhe[codigo][1]= "54632";
	$matDetalhe[codigo][2]= "84231";
	$matDetalhe[codigo][3]= "83719";

	$matDetalhe[nome][1]= "Joao";
	$matDetalhe[nome][2]= "Jose";
	$matDetalhe[nome][3]= "Mauro";

	$matDetalhe[cidade][1]= "Ourinhos";
	$matDetalhe[cidade][2]= "Marilia";
	$matDetalhe[cidade][3]= "Bauru";

	$matDetalhe[estado][1]= "SP";
	$matDetalhe[estado][2]= "SP";
	$matDetalhe[estado][3]= "SP";

	
	$margens[sup]='20';
	$margens[esq]='25';
	$margens[dir]='15';
	
	$orientacao= 'P';
	$tam_papel='A4';
	
	$oPrint->geraImpressao($margemEsq,$margemDir,$imagem,'TEste',$matCabecalho,$matLargura,$matAlinhamento,$matDetalhe);
*/
?>
