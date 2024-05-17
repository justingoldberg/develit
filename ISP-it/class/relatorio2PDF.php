<?
# linhas suprimidas por terem sido definidas em config/pdf.php
//define('FPDF_FONTPATH',$pathFPDF.'fpdf/font/');
//require($pathFPDF.'fpdf/fpdf.php');

class Relatorio2Pdf extends FPDF{ // classe que gera relatorio no formato PDF

	//variaveis utilizadas na classe
	var  $larg_tot, $larg_proporcional, $orientacao, $margemDir, $margemEsq, $imagem;
	var $titulo, $sub, $texto, $matLargura, $matAlinhamento, $matCabecalho, $matDetalhe;
	//variaveis utilizadas na classe
	
	function Header(){
		
		$this->larg_tot=0; $this->larg_proporcional=0;
		//determina a largura da página dependendo da sua posição!		
		if ($this->orientacao == 'P'){
			$this->larg_tot= (210-($this->margemEsq + $this->margemDir));
		}
		elseif ($this->orientacao== 'L'){
			$this->larg_tot= (297-($this->margemEsq + $this->margemDir));
		}
		//Definicao das larguras proporcionais de celulas
		//inicializa as vars...
		$soma_cols=0;
		$this->larg_proporcional=array();
		for($i=0;$i<count($this->matLargura);$i++){
			//calcula a largura total das colunas enviadas pela matriz de largura
			$soma_cols=$soma_cols+intval($this->matLargura[$i]);
		}
		//pego a diferenca da coluna opcoes(html) divido em partes iguais às colunas e concateno-a no fim cd col
		$larg_adic=((((100-$soma_cols)/count($this->matLargura))*$this->larg_tot)/100);
		//print "$this->larg_tot/$this->larg_proporcional/$this->orientacao";
		for($i=0;$i<count($this->matLargura);$i++){
			//calcula a largura proporcional da coluna
			$this->larg_proporcional[$i]= ((($this->larg_tot*intval($this->matLargura[$i])/100))+$larg_adic);
		}
		
		$this->SetY(10);
		$this->Image($this->imagem,$this->margemEsq,10,40,23);
		$this->SetFont('arial','B',16);
		$this->MultiCell('',10,$this->titulo,'T','C');
		$this->SetFont('arial','B',13);
		$this->MultiCell('',8,$this->sub,0,'C');
		$this->SetFont('arial','B',11);
		$this->MultiCell('',5,$this->texto,'B','C');
		$this->Ln();
		// define o titulo de cada coluna do relatorio
		$posY= $this->GetY(); // pega a posicao Y (vert) atual
		$posX= $this->GetX(); // pega a posicao X (horiz) atual
		for ($i=0;$i<count($this->larg_proporcional);$i++){
			//$this->SetY($posY); // posiciona o ponteiro na posicao da proxima linha
			$this->SetX($posX); // posiciona o ponteiro na posicao da proxima coluna
			$this->MultiCell($this->larg_proporcional[$i],6,$this->matCabecalho[$i],0,strtoupper(substr($this->matAlinhamento[$i],0,1)));	
			$posX=$posX+$this->larg_proporcional[$i];
			$this->SetY($posY);
		};
		$this->Ln();
	}
	
	function Footer(){
		$fooY=$this->GetY();
		$this->SetX(10);
		$this->Cell(40,5,date('d/m/Y'),'TB',0,'L');
		$this->Cell((intval($this->larg_tot)-40),5,$this->PageNo().'/{nb}','TB',0,'R');
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
		//$this->SetTopMargin($margens[sup]); // define a margem superior
		$this->SetLeftMargin($this->margemEsq); // define a margem esq
		$this->SetRightMargin($this->margemDir); // define a margem dir
		$this->AliasNbPages();
		$this->AddPage($this->orientacao); // Adiciona uma página para iniciar a geração do Arquivo
		$this->SetAutoPageBreak(true,10);
		
		//a partir daqui vamos carregar o corpo do relatorio
		$this->SetFont('arial','','10');
		
		for ($i=0; $i<=(count($this->matDetalhe[0]));$i++){
			$posY= $this->GetY(); // pega a posicao Y (vert) atual
			$posX= $this->GetX(); // pega a posicao X (horiz) atual
			
			for($j=0;$j<(count($this->matDetalhe)-1);$j++){ //tem um -1 no count matriz detalhe
				$this->SetY($posY); // posiciona o ponteiro na posicao da linha
				$this->SetX($posX); // posiciona o ponteiro na posicao da proxima coluna
				
				$this->MultiCell($this->larg_proporcional[$j],6,$this->matDetalhe[$j][$i],1,strtoupper(substr($this->matAlinhamento[$j],0,1)));	
				$posX=$posX+$this->larg_proporcional[$j];
				
			}
			
		}
		// fim do corpo do relatorio
		//$this->Ln(); //$this->Ln();
		$nomeArquivo=$arquivo[tmpPDF].$sessLogin[login].'relatorio2pdf.pdf';
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