Motview - OmniPCX Enterprise SIP Motortrace Viewer
============================================================
Generates a browsable line-by-line summary of SIP messages from log files.
Works for:
* OminPCX Enterprise (OXE) motortrace, including OXE R10 with changed date format
* Integrated Communications Server (ICS) SIP logs
* OmniTouch FAX Server (OFS) SIP logs (6.5)
* Genesys SIP Server (ICE) logs

Copyright (c) 2009-2011 Alcatel-Lucent
Author: Lars Ruoff <lars.ruoff@alcatel-lucent.fr>
See LICENSE.txt for terms of redistribution.

IMPORTANT NOTE:
The application comes in form of a Python script.
It requires Python 2.7 or above, but below 3.0.
Download Python 2.7 for Windows here:
http://www.python.org/ftp/python/2.7.2/python-2.7.2.msi
or download for another platform:
http://www.python.org/download/

Tip for Windows users:
Create a link on your desktop with the following Target:
"C:\Python27\pythonw.exe C:\Programs\motview.py"
(Replace with whereever you installed Python and the script)
This link can be used to launch the application without the console window and to drag&drop motortrace files into it or integrate it to the "Send To" menu.

------------------------------------------------------------
User Interface Help:

The UI is made up of the following elements:
- Main menu: Use File/Open to open a motortrace file.
- Filter bar: A filter expression can be entered and applied. See below.
- Message summary pane: One line is displayed per SIP message in the trace.
- Details pane: Original trace file is shown.

Double-click on a line in the summary pane to jump to the corresponding lines in the detail pane.

Right-click on a line in the summary pane > "Filter on this Dialog" will filter to keep only those lines that belong to the same dialog (Call-ID) as the currently selected one.

------------------------------------------------------------
Filtering:

A regular expression can be entered into the Filter text box.
When applied, the summary pane will only show the messages where at least one line of the detailed SIP message text matches the filter expression.

Examples for filter expressions:
"^(From|f).*XXX" - filter on messages where the "From" line contains "XXX"
"^(To|t).*XXX" - filter on messages where the "To" line contains "XXX"
"^(Call-ID|i).*XXX" - filter on messages where the "Call-ID" contains "XXX"

------------------------------------------------------------
Motview can be run with the folowing arguments:
>motview [-c] [-f regexp] [tracefile]
-c : Print summary to console and quit (no GUI).
     If no [tracefile] given, read from stdin.
-f : Filter with regexp on SIP message content.

------------------------------------------------------------
Report bugs and feature requests to <Lars.Ruoff@alcatel-lucent.com>
