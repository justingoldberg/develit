<?php
################################################################################
#       Criado por: JosÚ Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 02/10/2003
# Ultima alteração: 01/04/2004
#    Alteração No.: 002
#
# Função:
#    Funções JavaScript

### Javascript ###
$funcoesJs = "
<script type=\"text/javascript\" language=\"JavaScript\">
<!--
function novaJanela(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function voltar() {
	document.history.back();
}

function verificaData(data, campo) {

	var er = /\//g;
	data=data.replace(er,'');
	
	if(data && !isNaN(data)) {
		
		dia=data.substr(0,2);
		mes=data.substr(2,2);
		ano=data.substr(4,4);
		
		dataFormat=dia+'/'+mes+'/'+ano;
		
		if( !dia || !mes || !ano || (dia <= 0 || dia > 31) || (mes <=0 || mes > 12) || (ano < 1900) ) {
			alert('Data Inválida');
			
			nomecampo=document.forms[0].elements[campo].name;
			
			document.forms[0].elements[campo].value='';
			document.forms[0].elements[campo].focus();
			return false;

		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
	else if(data && isNaN(data)) {
		alert('Data Inválida');
		
		document.forms[0].elements[campo].value='';
		document.forms[0].elements[campo].focus();
	}
}


function verificaDataSuperior(data, campo) {
	
	var er = /\//g;
	data=data.replace(er,'');
	
	if(data) {
		dia=data.substr(0,2);
		mes=data.substr(2,2);
		ano=data.substr(4,4);
		
		dataFormat=dia+'/'+mes+'/'+ano;
		
		if( !dia || !mes || !ano || (dia <= 0 || dia > 31) 
			|| (mes <=0 || mes > 12) 
			|| (ano < 1900) 
			|| ( (dia <= $data[dia]) && mes <= $data[mes] && ano <= $data[ano]) 
			|| (mes < $data[mes] && ano <= $data[ano]) 
			|| ano < $data[ano] ) {
			alert('Data Inválida');
			document.forms[0].elements[campo].value='';
			document.forms[0].elements[campo].focus();
		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
}


function verificaDataMesAno(data, campo) {
	
	var er = /\//g;
	data=data.replace(er,'');
	
	if(data) {
		mes=data.substr(0,2);
		ano=data.substr(2,4);
		
		dataFormat=mes+'/'+ano;
		
		if( !mes || !ano || 
			(mes < 1 || mes > 12) 
			|| ( mes < $data[mes] && ano <= $data[ano] )  
			|| (ano < $data[ano]) 
			|| ( mes > $data[mes] && ano > $data[ano]+1 ) 
		) {
		
			if ( mes > $data[mes] && ano > $data[ano]+1 ) alert('Data de desconto possui ANO inválido ou superior ao ANO Atual +1');
			else alert('Data Inválida');
			
			document.forms[0].elements[campo].value='';
			document.forms[0].elements[campo].focus();
		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
}


function verificaDataMesAno2(data, campo) {
	
	var er = /\//g;
	data=data.replace(er,'');
	
	if(data) {
		mes=data.substr(0,2);
		ano=data.substr(2,4);
		
		dataFormat=mes+'/'+ano;
		
		if( !mes || !ano || 
		(mes < 1 || mes > 12) 
		) {
		
			alert('Data Inválida');
			document.forms[0].elements[campo].value='';
			document.forms[0].elements[campo].focus();
		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
}


function verificaMesAnoRT(data, campo) {
	
	var er = /\//g;
	data=data.replace(er,'');

	if( data.length < 6 ) {

		if(data.length >2) {
			mes=data.substr(0,2);
			ano=data.substr(2,4);
			
			dataFormat=mes+'/'+ano;

		}
		else {
			mes=data.substr(0,2);
			dataFormat=mes;
		}

		document.forms[0].elements[campo].value=dataFormat;
		document.forms[0].elements[campo].focus();

	}
	else {
	
		if(data) {
			mes=data.substr(0,2);
			ano=data.substr(2,4);
			
			dataFormat=mes+'/'+ano;
			
			if( !mes || !ano || 
			(mes < 1 || mes > 12) 
			) {
				alert('Data Inválida');
				document.forms[0].elements[campo].value='';
				document.forms[0].elements[campo].focus();
			}
			else {
				document.forms[0].elements[campo].value=dataFormat;
			}
		}
	}
}

function verificaDataMesAnoPagamento(data, campo) {
	
	var er = /\//g;
	data=data.replace(er,'');
	
	if(data) {
		dia=data.substr(0,2);
		mes=data.substr(2,2);
		ano=data.substr(4,4);
		
		dataFormat=dia+'/'+mes+'/'+ano;
		
		if( !dia || !mes || !ano || (mes < 1 || mes > 12) || (dia < 1 || dia > 31) ) {
		
			alert('Data Inválida');
			
			document.forms[0].elements[campo].value='';
			document.forms[0].elements[campo].focus();
		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
}


function verificaCEP(cep,campo) {
	
	var er = /[^0-9]/g;
	cep=cep.replace(er,'');
	
	if(cep) {
		prefixo=cep.substr(0,5);
		sufixo=cep.substr(5,3);
		
		cepFormat=prefixo+'-'+sufixo;
		
		if( !prefixo || !sufixo ) {
			alert('CEP Inválido');
			document.forms[0].elements[campo].value='';
			document.forms[0].elements[campo].focus();
		}
		else {
			document.forms[0].elements[campo].value=cepFormat;
		}
	}
}


function verificaCNPJ(cnpj,campo) {
	
	var er = /[^0-9]/g;
	cnpj=cnpj.replace(er,'');
	
	if(cnpj) {
		prefixo=cnpj.substr(0,8);
		sufixo=cnpj.substr(8,4);
		digito=cnpj.substr(12,2);
		
		prefixo1=prefixo.substr(0,2);
		prefixo2=prefixo.substr(2,3);
		prefixo3=prefixo.substr(5,3);
		
		cnpjFormat=prefixo1 + '.' + prefixo2 + '.' + prefixo3 + '/' + sufixo + '-' + digito;
		
		if( !prefixo || !sufixo || !digito) {
			alert('CNPJ Inválido');
			document.forms[0].elements[campo].value='';
			document.forms[0].elements[campo].focus();
		}
		else {
			document.forms[0].elements[campo].value=cnpjFormat;
		}
	}
}

function verificaURL(url,campo) {

	if(url) {
		if(url.substr(0,4) != 'http') {
			url='http://'+url;
		}
		
		document.forms[0].elements[campo].value=url;
	}
}


function formataValor(valor,campo) {

	var valorFormat = getValorFormatado(valor);
	document.forms[0].elements[campo].value=valorFormat;
}


function verificarValor(original,novo) {

	if(original > novo) {
		alert(\"ATENÇÃO: Valor informato é INFERIOR ao valor original!\");
	}
	
}

function calculaDesconto(valor,desconto,index) {
	total=(valor - ((valor * desconto)/100));
	total=number_format(total);
	formataValor(total,index);
}


function calculaDescontoAplicado(valor,desconto,index) {
	total=((valor * desconto)/100);
	total=number_format(total);
	formataValor(total,index);
}


function number_format(num) {

	num = num.toString().replace(/\$|\,/g,'');
	
	if(isNaN(num))
		num = '0';
		
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	
	if(cents<10)
		cents = '0' + cents;
	
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+','+num.substring(num.length-(4*i+3));
	
	return (num + cents);
}



function verificaIP(ip, campo) {

	var er = /[^0-9]/g;
	var ok = 0;
	
	ip=ip.replace(er,'');
	//alert('virou '+ip);
	if (ip.length > 12) {
		alert('IP Inválido - Muitos Caracteres '+ip);
    	return (false);
	}
	
	if(ip && !isNaN(ip)) {
		
		p1=ip.substr(0,3);
		p2=ip.substr(3,3);
		p3=ip.substr(6,3);
		p4=ip.substr(9,3);

		ipFormat=p1+'.'+p2+'.'+p3+'.'+p4;

		if( !p1 || !p2 || !p3 || !p4 || (p1 < 0 || p1 > 255) || (p2 < 0 || p2 > 255) || (p3 < 0 || p3 > 255) || (p4 < 0 || p4 > 255)) {
			alert('IP Inválido (255.255.255.255): '+ipFormat);
			nomecampo=document.forms[0].elements[campo].name;
			document.forms[0].elements[campo].value='';
			document.forms[0].elements[campo].focus();
			return false;
		}
		else {
			document.forms[0].elements[campo].value=ipFormat;
		}
	}
	else if(ip && isNaN(ip)) {
		alert('IP Inválido ' + ip);
		document.forms[0].elements[campo].value='';
		document.forms[0].elements[campo].focus();
	}
	else {
		document.forms[0].elements[campo].value='';
	}
}

function validaIp( ip ) {
	var er=/^(0{0,2}[1-9]|0?[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-4])\.(0{0,2}[0-9]|0?[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.(0{0,2}[0-9]|0?[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.(0{0,2}[1-9]|0?[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-4])$/;
	
	if( ip.value && !er.exec( ip.value ) ){
      	alert('Ip inválido!');
		ip.value = '';
		ip.focus();
        return false;
    }
}

function validaIpMask( ip ) {
	var er=/^(0{0,2}[0-9]|0?[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.(0{0,2}[0-9]|0?[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.(0{0,2}[0-9]|0?[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.(0{0,2}[0-9]|0?[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])$/;

	if( ip.value && !er.exec( ip.value ) ){
    	alert('Mascára de Ip inválida!');
		ip.value = '';
        ip.focus();
        return false;
	}
}


function isNum(caractere) {
 	var strValidos = '0123456789.,';
 	if ( strValidos.indexOf( caractere ) == -1 )
		return false;
	return true;
}
 
function validaTecla(campo, event) {
	var BACKSPACE=8;
	var TAB=11;
	var key;
	var tecla;
	CheckTAB=true;

	if(navigator.appName.indexOf('Netscape')!= -1)
		tecla= event.which;
	else
		tecla= event.keyCode;
 
	key = String.fromCharCode(tecla);
	alert( 'key: ' + tecla + ' -> campo: ' + campo.value);
 
	if ( tecla == 13 )
		return false; 
	if ( tecla == BACKSPACE )
		return true;
	if (tecla == TAB)
		return true;

	return (isNum(key));
}
 
 
function FormataCNPJ( el ) { 
	vr = el.value;
	tam = vr.length;
 
	if ( vr.indexOf(".") == -1 ) {
		if ( tam <= 2 )
			el.value = vr;

		if ( (tam > 2) && (tam <= 6) )
			el.value = vr.substr( 0, 2 ) + '.' + vr.substr( 2, tam );

		if ( (tam >= 7) && (tam <= 10) )
			el.value = vr.substr( 0, 2 ) + '.' + vr.substr( 2, 3 ) + '.' + vr.substr( 5, 3 ) + '/';
 
		if ( (tam >= 11) && (tam <= 18) )
			el.value = vr.substr( 0, 2 ) + '.' + vr.substr( 2, 3 ) + '.' + vr.substr( 5, 3 ) + '/' + vr.substr( 8, 4 ) + '-' + vr.substr( 12, 2 ) ;
	}
 
	return true;

} 


function calculaValorTotal( campo1, campo2, resultado ){
	var total;
	var er = /[^0-9]/g;
	campo1 = campo1.replace( er, '' );
	campo2 = campo2.replace( er, '' );
	total = ( campo1*campo2 );
	formataValor( total, resultado );
}

function retornaInteiro( valor, campo ){
	var er = /[^0-9]/g;
	
	valor = valor.replace( er, '' );

	document.forms[0].elements[campo].value = valor;
}

function setPrevisao(valor, idCentro, idMes){
	var nome = '';
	
	for( i = idMes; i <= 12; i++) {
		nome = 'matriz[CC]['+idCentro+']['+i+']';
		
		document.forms[0].elements[nome].value = valor;
	}
}


function checkboxDinamico( id ) {
	var campo = document.getElementById(id);
	if( campo.checked == false ) {
		campo.value = 'N';
	}
	alert('valor: '+campo.value);
	document.forms[0].submit();
}

// transfere o texto da opção selecionada em um combo select
function recolheNomeOpcaoSelect(select, destino) {
	var x=document.forms[0].elements[select];
	document.forms[0].elements[destino].value = x.options[x.selectedIndex].text;
}

// Calcula o valor do produto quando altera a quantidade
function calculaValor(qtd, vl_unit, resultado) {
	var total = Number(qtd.replace(/,/, \".\")) * Number(vl_unit.replace(/,/, \".\"));
	var valor = total.toString();
	if (valor.search(/\./)== -1) {
	   total= valor+\",00\"
	} else {
    	var divisao=valor.split('.');
	    var reais=divisao[0];
    	var centavos=divisao[1]+\"0\";
	    var formato_cent=centavos.substring(0,2);
    	total = reais + \",\" + formato_cent
	}

	formataValor( total, resultado );
}

//Formata número padrão duas casas decimais
function formataNumero(valor,campo,form) {

	var valorFormat = getValorFormatado(valor);
	document.forms[form].elements[campo].value=valorFormat;
}

function getValorFormatado(valor) {
	var er = /[^0-9]/g;
	valor=valor.toString().replace(er,'');
	
	if(valor) {
		
		if(valor.length >1) {
			centavos=valor.substr((valor.length-2),2);
			inteiro=valor.substr(0,(valor.length-2));
		}
		else {
			centavos='00'; //valor.substr((valor.length-2),2);
			inteiro=valor; //valor.substr(0,(valor.length-2));
		}

		valorFormat=inteiro+','+centavos;
		
	} else {
		valorFormat=0;
	}
	return valorFormat;
}

//Compara valores e verifica se valor digitado é superior ao valor permitido
function verificarValorPermitido( permitido, valor, campo ) {
	var er;
	er =  /\,/g;	

	valor1 = parseInt( permitido.replace(er,'') );
	valor2 = parseInt( valor.replace(er,'') );

	if( valor2 > valor1 ) {
		alert(\"ATENÇÃO: Valor informado é SUPERIOR ao valor permitido!\");
		document.forms[0].elements[campo].value='';
	}
}

//verifica se o checkbox foi checado, se foi habilita campo
function checaproduto(checa) {
	var campo = 'campo'+checa; 

	if( document.getElementById(checa).checked == true ) {
		document.getElementById(campo).disabled = false;
		document.getElementById(campo).focus();
		//document.getElementById(campo).value = '';
	}
	else {
		document.getElementById(campo).disabled = true;	
		document.getElementById(campo).value = '';	
	}
}

function exibeLayerExcluirParcela( id , url){
	var botaoe = \"<input type=\\\"button\\\" name=\\\"matriz[bntConfirmar]\\\" value=\\\"Confirmar\\\" class=\\\"submit\\\" onclick=\\\"window.location='\"+url+\"'>\";
	document.getElementById('janela').style.display = 'inline';
	document.getElementById('dataLabel').innerHTML = document.getElementById('data'+id).innerHTML;
	document.getElementById('valorLabel').innerHTML = document.getElementById('valor'+id).innerHTML;
	document.getElementById('botao').innerHTML = botaoe;

}

function ocultaLayerExcluirParcela(){
	document.getElementById('janela').style.display = 'none';
}

//Começa aqui

var ids=new Array();
function flipdiv(id){	
	
	if ( ids[id] == 1 ) {
		ids[id]=0;
		hidediv(id);		
	}
	else {
		ids[id]=1;
		showdiv(id);
	}
}

function hidediv(id) {
	//safe function to hide an element with a specified id
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'none';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'none';
		}
		else { // IE 4
			document.all.id.style.display = 'none';
		}
	}
}

function showdiv(id) {
	//safe function to show an element with a specified id
		  
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'block';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'block';
		}
		else { // IE 4
			document.all.id.style.display = 'block';
		}
	}
}

function preencheCampo( campo, valor, opcao) {
	var vlcampo='';
	var opc=''
	
	if(opcao==1){
		opc = 'servico'+valor; 
	}
	else{
		if(opcao==2){
			opc = 'descricao'+valor;
		}
		else{
			opc = 'valor'+valor;
		}
	}

	if(document.getElementById) {
		vlcampo =  document.getElementById(opc).value;
		document.getElementById(campo).value = vlcampo;
	}

}
//Termina aqui

function verificaTamanho(campo, valor, tamanho){
    if( valor.length > tamanho ) {
    	alert(\"ATENÇÃO: Número de caracteres maior que o permitido!\");
    	document.getElementById(campo).value = '';
    }
}

//Função para selecionar todos os elementos checkbox de um formulário
function selecionarTodos(retorno){
	var formulario = document.matriz;
	if(retorno == true){
		for(i = 0; i < formulario.length; i ++){
			if(formulario.elements[i].type == 'checkbox' && formulario.elements[i].name != 'todas'){
				if(formulario.elements[i].checked == false){
					formulario.elements[i].checked = true;
				}
			}
		}
	}
	else{
		for(i = 0; i < formulario.length; i ++){
			if(formulario.elements[i].type == 'checkbox' && formulario.elements[i].name != 'todas'){
				if(formulario.elements[i].checked == true){
					formulario.elements[i].checked = false;
				}
			}
		}
	}
} //Termina aqui

//-->
</script>
";
### Fim do Javascript ###
?>