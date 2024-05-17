<?
################################################################################
#       Criado por: Josй Roberto Kerne - joseroberto@kerne.org
#  Data de criaзгo: 08/01/2004
# Ultima alteraзгo: 11/01/2005
#    Alteraзгo No.: 003
#
# Funзгo:
#    Estrutura SQL da aplicaзгo

# Usuбrios
$tb[Usuarios]="usuarios";

# Grupos
$tb[Grupos]="grupos";

# Usuбrios dos grupos
$tb[UsuariosGrupos]="usuarios_grupos";

# Maquinas
$tb[Maquinas]="Maquinas";

# Categorias
$tb[Categorias]="categorias";

# CategoriasGrupos
$tb[CategoriasGrupos]="categorias_grupos";

# Prioridades
$tb[Prioridades]="prioridades";

# Status
$tb[Status]="status";

# Ticket
$tb[Ticket]="ticket";

# Ticket Detalhes
$tb[TicketDetalhes]="ticket_detalhes";

# Processos Ticket
$tb[ProcessosTicket]="processos_ticket";

# Comentбrios de Tickets
$tb[ComentariosTicket]="ticket_comentario";

# Perfil
$tb[Perfil]="perfil";

# Evento
$tb[Evento]="evento";

# Agenda
$tb[Agenda]="agenda";

# Parametros
$tb[Parametros]="Parametros";

# Empresas
$tb[Empresas]="Empresas";

# Usuбrios Empresas
$tb[EmpresasUsuarios]="EmpresasUsuarios";

# UsuпїЅrios Empresas (tabela associativa)
$tb[UsuariosEmpresas]="UsuariosEmpresas";



# Tickets
$tb[Tickets]="ticket_empresa";

# Tickets
$tb[TicketEmpresaUsuarios]="ticket_empresa_usuarios";


# Tickets
$tb[ComentarioEmpresaUsuarios]="comentario_empresa_usuarios";

# Finalizacoes
$tb[TicketFinalizacoes]="ticket_tempo";

# Ticket Chat
$tb[TicketChat]="ticket_chat";

# Ticket Chat Conteudo
$tb[TicketChatConteudo]="ticket_chat_conteudo";

# Ticket Feedback
$tb[TicketFeedback]="ticket_feedback";



#tabelas do isp-it
include('isp-it.php');

#ISP Maquinas Suporte
$tb[MaquinasSuporte] = $isp['db'].'.MaquinasSuporte';

#ISP Suportes
$tb[Suporte] = $isp['db'].'.Suporte';

#ISP Pessoas Tipos
$tb[PessoasTipos] = $isp['db'].'.PessoasTipos';


?>