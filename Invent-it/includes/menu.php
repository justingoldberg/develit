<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/01/2004
# Ultima alteração: 11/01/2005
#    Alteração No.: 003
#
# Função:
#    Menus da aplicação


# Função para verificação de menus da aplicação
function menuPrincipal($tipo)
{
	global $corFundo, $corBorda, $html;
	
	
	# Receber ID do Grupo do usuario
	if($grupoUsuario && contaConsulta($grupoUsuario)>0) {
		$idGrupo=resultadoSQL($grupoUsuario, 0, 'idGrupo');	
		
		# Buscar informações do grupo
		# receber informações do grupo
		$infoGrupo=buscaInfoGrupo($idGrupo);
	}
	
	if($tipo=='usuario') {
		htmlAbreTabelaSH("center", 760, 0, 1, 0, $corFundo, $corBorda, 6);
			htmlAbreLinha($corFundo);
				itemLinha("<img src=".$html[imagem][home]." border=0>PRINCIPAL", "?modulo=home", 'center', $corFundo, 0, 'titulo9');
				itemLinha("<img src=".$html[imagem][ver]." border=0>INVENTÁRIO", "?modulo=maquina&acao=listar", 'center', $corFundo, 0, 'titulo9');
				itemLinha("<img src=".$html[imagem][cadastros]." border=0>EMPRESAS", "?modulo=empresas", 'center', $corFundo, 0, 'titulo9');
				itemLinha("<img src=".$html[imagem][perfil]." border=0>PERFIL", "?modulo=perfil", 'center', $corFundo, 0, 'titulo9');
				itemLinha("<img src=".$html[imagem][config_sistema]." border=0>CONFIGURAÇÕES", "?modulo=configuracoes", 'center', $corFundo, 0, 'titulo9');
				itemLinha("<img src=".$html[imagem][fechar]." border=0>SAIR", "?modulo=logoff", 'center', $corFundo, 0, 'titulo9');
			htmlFechaLinha();
		fechaTabela();
	}


} # fecha visualizacao de menu



# Função para verificação de menus da aplicação
function verMenu($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	### Menu principal - usuarios logados apenas
	if($modulo=='login' || !$sessLogin)
	{
		validacao($sessLogin, $modulo, $sub, $acao, $registro);
	}

	
	### MODULOS QUE REQUEREM AUTENTICAÇÃO
	else {
		if(checaLogin($sessLogin, $modulo, $sub, $acao, $registro) ) {
			## PRINCIPAL / HOME
			if(!$modulo || $modulo=='home') {
				home($modulo, $sub, $acao, $registro, $matriz);
			}
			## ACESSO
			elseif($modulo=='acesso') {
				acesso($modulo, $sub, $acao, $registro, $matriz);
			}
			## CONFIGURACOES
			elseif($modulo=='configuracoes') {
				config($modulo, $sub, $acao, $registro, $matriz);
			}
			## MÁQUINAS
			elseif($modulo=='maquina') {
				maquinas($modulo, $sub, $acao, $registro, $matriz);
			}
			## PERFIL
			elseif($modulo=='perfil') {
				perfil($modulo, $sub, $acao, $registro, $matriz);
			}
			## EMPRESAS
			elseif($modulo=='empresas') {
				empresas($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
	}
} # fecha visualizacao de menu




# Visualização de menu adicional em cadastros
function menuOpcAdicional($modulo, $sub, $acao, $registro)
{
	global $corFundo, $moduloApp;

	## ACESSO
	if($modulo=='acesso') {
		# USUARIOS
		if($sub=='usuarios') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=grupos&acao=listar>Selecionar Grupo</a>",'usuario');
			}
			elseif($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
		elseif($sub=='grupos') {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			if($acao=='usuariosadicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=adicionar>Novo usuário</a>",'usuario');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=usuarios&registro=$registro>Listar</a>",'listar');
			}
			if($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			if($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
		}
	}
	## ACESSO
	if($modulo=='maquina') {
	
		if(!$sub) {
			if($acao=='adicionar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=procurar>Procurar</a>",'procurar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
			}
			elseif($acao=='alterar') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=listar&registro=$registro>Programas</a>",'cadastros');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=tickets&acao=listar&registro=$registro>Tickets</a>",'ticket');
			}
			elseif($acao=='excluir') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=listar&registro=$registro>Programas</a>",'cadastros');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=tickets&acao=listar&registro=$registro>Tickets</a>",'ticket');
			}
			elseif($acao=='detalhes') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=adicionar>Adicionar</a>",'incluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=alterar&registro=$registro>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=excluir&registro=$registro>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=listar&registro=$registro>Programas</a>",'cadastros');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=listar&registro=$registro>Usuários</a>",'chave');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=tickets&acao=listar&registro=$registro>Tickets</a>",'ticket');
			}
		}
		elseif($sub=='programas') {
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=alterar&registro=$registro>Alterar</a>",'alterar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=excluir&registro=$registro>Excluir</a>",'excluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=listar&registro=$registro>Programas</a>",'cadastros');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=listar&registro=$registro>Usuários</a>",'chave');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=tickets&acao=listar&registro=$registro>Tickets</a>",'ticket');
		}
		elseif($sub=='usuarios') {
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=alterar&registro=$registro>Alterar</a>",'alterar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=excluir&registro=$registro>Excluir</a>",'excluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=listar&registro=$registro>Programas</a>",'cadastros');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=listar&registro=$registro>Usuários</a>",'chave');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=tickets&acao=listar&registro=$registro>Tickets</a>",'ticket');
		}
		elseif($sub=='tickets') {
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=adicionar>Adicionar</a>",'incluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=alterar&registro=$registro>Alterar</a>",'alterar');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=excluir&registro=$registro>Excluir</a>",'excluir');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=listar&registro=$registro>Programas</a>",'cadastros');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=listar&registro=$registro>Usuários</a>",'chave');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=tickets&acao=listar&registro=$registro>Tickets</a>",'ticket');
		}
	}
	
	if($opcoes) {
		# Mostrar opção adicional
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL($opcoes, 'right', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	}
	
}
# fecha menu adicional



?>
