#! /usr/bin/env python
# SIP Motortrace Viewer
# Generates a line-by-line summary of SIP messages from logs
# Works for:
#   OminPCX Enterprise (OXE) motortrace, including OXE R10 with changed date format
#   Integrated Communications Server (ICS) SIP logs
#   OmniTouch FAX Server (OFS) SIP logs (6.5)
#   Genesys SIP Server (ICE) logs
# Developed by Lars Ruoff <lars.ruoff@alcatel-lucent.com>
# Please report any errors
# Requires Python 2.7

import Tkinter
import Tix
from Tkinter import *
from Tix import *
import tkFileDialog
import tkMessageBox
import tkFont
import sys, re, getopt
import cStringIO
import os.path
from operator import itemgetter
from datetime import datetime

########################################
from version import *
from revision import *

########################################
def usage(progname):
    print "Usage: " + progname + " [-h] [-c] [-f regexp] [-r REQUEST] [tracefile]"
    print "-h : Print this help message."
    print "-c : Print summary to console and quit (no GUI)."
    print "-f : Filter with regexp on SIP message content."
    print "-r : Only show dialogs where the first seen request type is REQUEST."
    print "     (e.g. INVITE, REGISTER, OPTIONS, ...)."
    print "     May occur multiple times, e.g. '-r INVITE -r REGISTER'."
    print "Reads from [tracefile] or stdin."

########################################
class TextWithScrollbars(Frame):
  def __init__(self, parent=None, *args, **kwargs):
    Frame.__init__(self, parent)

    self.grid_rowconfigure(0, weight=1)
    self.grid_columnconfigure(0, weight=1)

    self.xscrollbar = Scrollbar(self, orient=HORIZONTAL)
    self.xscrollbar.grid(row=1, column=0, sticky=E+W)

    self.yscrollbar = Scrollbar(self)
    self.yscrollbar.grid(row=0, column=1, sticky=N+S)

    self.text = Text(self, wrap=NONE,
                xscrollcommand=self.xscrollbar.set,
                yscrollcommand=self.yscrollbar.set,
                *args, **kwargs)
    self.text.config(background="white")
    self.text.grid(row=0, column=0, sticky=N+S+E+W)

    self.xscrollbar.config(command=self.text.xview)
    self.yscrollbar.config(command=self.text.yview)
    self.pack()

  def get(self):
    return self.text.get(1.0,"%s-1c" % END)
  def set(self, astr):
    self.text.delete(1.0, END)
    self.text.insert(INSERT, astr)

########################################
class TListWithScrollbars(Frame):
  def __init__(self, parent=None, *args, **kwargs):
    Frame.__init__(self, parent)

    self.grid_rowconfigure(0, weight=1)
    self.grid_columnconfigure(0, weight=1)

    self.xscrollbar = Scrollbar(self, orient=HORIZONTAL)
    self.xscrollbar.grid(row=1, column=0, sticky=E+W)

    self.yscrollbar = Scrollbar(self)
    self.yscrollbar.grid(row=0, column=1, sticky=N+S)

    self.tlist = Tix.TList(self,
                xscrollcommand=self.xscrollbar.set,
                yscrollcommand=self.yscrollbar.set,
                *args, **kwargs)
    self.tlist.config(background="white", orient=HORIZONTAL)
    self.tlist.grid(row=0, column=0, sticky=N+S+E+W)

    self.xscrollbar.config(command=self.tlist.xview)
    self.yscrollbar.config(command=self.tlist.yview)
    self.pack()

  def get(self):
    return self.tlist.get(1.0,"%s-1c" % END)
  def set(self, astr):
    self.tlist.delete(1.0, END)
    self.tlist.insert(INSERT, astr)

########################################
def callback():
    print "called the callback!"

########################################
class MainApp:
  def __init__(self):
    self.init_done = False
    self.last_filename = None
    self.last_dir = "."
    self.linenumbers = dict()
    self.call_ids = dict()
    self.call_ids_for_line = dict()
    self.filterexpression = ""
    self.tracked_requests = []
    self.console = None
    self.data = ""
    self.stringbuffer = ""
    self.appstring = "MotorTrace Viewer v" + VERSION

    self.mainwindow = Tix.Tk()
    self.mainwindow.grid()
    self.mainwindow.title(self.appstring)

    self.mainwindow.rowconfigure(0, weight=1)
    self.mainwindow.columnconfigure(0, weight=1)

    self.mainmenu = Menu(self.mainwindow)
    self.mainwindow.config(menu=self.mainmenu)

    self.filemenu = Menu(self.mainmenu, tearoff=False)
    self.mainmenu.add_cascade(label="File", menu=self.filemenu)
    self.filemenu.add_command(label="Open...", command=self.on_file_open)
    self.filemenu.add_command(label="Reload", command=self.on_file_reload)
    self.filemenu.add_separator()
    self.filemenu.add_command(label="Exit", command=self.mainwindow.destroy)

    self.editmenu = Menu(self.mainmenu, tearoff=False)
    self.mainmenu.add_cascade(label="Edit", menu=self.editmenu)
    self.editmenu.add_command(label="Copy to clipboard", command=self.on_file_export)

#    self.helpmenu = Menu(self.mainmenu, tearoff=False)
#    self.mainmenu.add_cascade(label="Help", menu=self.helpmenu)
#    self.helpmenu.add_command(label="About...", command=callback)

    self.filterframe = Frame(self.mainwindow)

    self.request_filter = StringVar()
    self.ANY_REQUEST = '(any)'
    self.combo1 = ComboBox(self.filterframe, label="First request in dialog: ", dropdown=1,
        command=self.on_apply_request_filter, editable=0, variable=self.request_filter,
        options='listbox.height 5')
    self.combo1.insert(END, self.ANY_REQUEST)
    self.combo1.insert(END, 'INVITE')
    self.combo1.insert(END, 'REGISTER')
    self.combo1.insert(END, 'OPTIONS')
    self.combo1.insert(END, 'SUBSCRIBE/NOTIFY')
    self.combo1.pack(side=LEFT)
    self.combo1.set_silent(self.ANY_REQUEST)

    self.label1 = Label(self.filterframe, text="Filter: SIP message contains regexp: ")
    self.label1.pack(side=LEFT)
    self.entry1 = Entry(self.filterframe)
    self.entry1.bind("<Return>", self.on_filter_entry_return)
    self.entry1.pack(side=LEFT, fill=X, expand=YES)
    self.button1 = Button(self.filterframe, text="Clear", command=self.on_clear_filter)
    self.button1.pack(side=LEFT)
    self.button2 = Button(self.filterframe, text="Apply", command=self.on_apply_filter)
    self.button2.pack(side=LEFT)
    self.filterframe.pack(fill=X)

    pane = Tix.PanedWindow(self.mainwindow, orientation='vertical')
    p1 = pane.add('list', min=70, size=240)
    p2 = pane.add('text', min=70, size=320)
    pane.pack(side=Tix.TOP, padx=3, pady=3, fill=Tix.BOTH, expand=1)

    self.sumlist = TListWithScrollbars(p1)
#    self.sumlist.tlist.config(height=25, command=self.on_select_line)
    self.sumlist.tlist.config(height=25, browsecmd=self.on_select_line)
    self.sumlist.tlist.bind("<Button-3>", self.on_right_click)
    self.sumlist.pack(fill=BOTH, expand=YES)

    self.font1 = tkFont.Font(family="Courier", size=10, weight=tkFont.BOLD)

    self.style = [
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#FFCCCC", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#CCFFCC", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#CCCCFF", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#FFFFCC", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#FFCCFF", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#CCFFFF", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#FF8888", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#88FF88", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#8888FF", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#FFFF88", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#FF88FF", font=self.font1),
      Tix.DisplayStyle(refwindow=self.mainwindow, itemtype=Tix.TEXT, foreground="black", background="#88FFFF", font=self.font1),
    ]

    self.btext = TextWithScrollbars(p2)
    self.btext.pack(fill=BOTH, expand=YES)
    self.init_done = True

  #-------------------------------------
  def output_to_tlist(self, text, call_id_num):
    self.sumlist.tlist.insert(END, itemtype=Tix.TEXT, text=text,
      style=self.style[call_id_num % len(self.style)])

  #-------------------------------------
  def output_to_stdout(self, text, call_id_num):
    sys.stdout.write(text+'\n');

  #-------------------------------------
  def output_to_string(self, text, call_id_num):
    self.stringbuffer += text+'\n';

  #-------------------------------------
  def refresh_summary(self, inputstream):
    self.sumlist.tlist.delete(0, END)
    self.write_summary(inputstream, self.output_to_tlist)

  #-------------------------------------
  def write_summary(self, inputstream, outputmethod):
    print "Scanning file " + self.last_filename + " ..."
    starttime = datetime.now()

    mcount = 0
    next_call_id_num = 1
    self.call_ids.clear()
    call_ids_displayed = []
    self.call_ids_for_line.clear()
    self.linenumbers.clear()

    direction = "????"
    remotesocket = ""
    protocol = "???"
    break_on_2x_empty_line = True

    regex_sip_end_1 = re.compile(".*-+$")

    logformats = []

    class LogFormat():
        def __init__(self, regex,
            regex_group_timestamp=0,
            regex_group_direction_raw=0,
            regex_group_remotesocket=0,
            regex_group_protocol=0,
            direction_send_id="",
            break_on_2x_empty_line=False):
            self.regex = regex
            self.regex_group_timestamp = regex_group_timestamp
            self.regex_group_direction_raw = regex_group_direction_raw
            self.regex_group_remotesocket = regex_group_remotesocket
            self.regex_group_protocol = regex_group_protocol
            self.direction_send_id = direction_send_id
            self.break_on_2x_empty_line = break_on_2x_empty_line

    # For OXE R10+:
    logformat_oxe_r10 = LogFormat(
        regex = re.compile(".*(\d\d:\d\d:\d\d).*(SEND|RECEIVE).*NETWORK.*\(([\d\.:]*).*(UDP|TCP)"),
        regex_group_timestamp = 1,
        regex_group_direction_raw = 2,
        regex_group_remotesocket = 3,
        regex_group_protocol = 4,
        direction_send_id = "SEND",
        break_on_2x_empty_line = False,
    )
    logformats.append(logformat_oxe_r10)

    # For OXE and ICS:
    logformat_oxe = LogFormat(
        regex = re.compile("([\d\.:]*).*(SEND|RECEIVE).*NETWORK.*\(([\d\.:]*).*(UDP|TCP)"),
        regex_group_timestamp = 1,
        regex_group_direction_raw = 2,
        regex_group_remotesocket = 3,
        regex_group_protocol = 4,
        direction_send_id = "SEND",
        break_on_2x_empty_line = False,
    )
    logformats.append(logformat_oxe)

    # For Genesys SIP server (ICE):
    logformat_ice = LogFormat(
        regex = re.compile("(\d\d:\d\d:\d\d\.\d\d\d).*(Sending|Received).*(UDP|TCP).* ([\d\.:]*) (>|<)"),
        regex_group_timestamp = 1,
        regex_group_direction_raw = 2,
        regex_group_remotesocket = 4,
        regex_group_protocol = 3,
        direction_send_id = "Sending",
        break_on_2x_empty_line = True,
    )
    logformats.append(logformat_ice)

    # For OmniTouch FAX server:
    logformat_ofs = LogFormat(
        regex = re.compile(".*(\d\d:\d\d:\d\d\.\d\d\d).*(Sent|Received) Message"),
        regex_group_timestamp = 1,
        regex_group_direction_raw = 2,
        direction_send_id = "Sent",
        break_on_2x_empty_line = False,
    )
    logformats.append(logformat_ofs)

    # Add other formats here...

    regex_rtpmap = re.compile("a=rtpmap\\:([0-9]+) ([^/]*)/.*")
    rtpmap = list()
    regex_filter = re.compile(self.filterexpression)
    regex_call_id = re.compile("^(Call-ID|i)\\:", re.IGNORECASE)
    regex_request_line = re.compile("(\S+)\s+\S+\s+SIP/2.0")

    mode = 0  #0=outside SIP message, 1=entering SIP message, 2=inside SIP message
    lcount = 1
    line = inputstream.readline()
    # read line by line
    while line != "":
        if mode==0:
            for logformat in logformats:
                match_trigger = logformat.regex.match(line)
                if match_trigger:
                    timestamp = match_trigger.group(logformat.regex_group_timestamp)
                    direction_raw = match_trigger.group(logformat.regex_group_direction_raw)
                    if logformat.regex_group_remotesocket > 0:
                      remotesocket = match_trigger.group(logformat.regex_group_remotesocket)
                    if logformat.regex_group_protocol > 0:
                      protocol = match_trigger.group(logformat.regex_group_protocol)
                    if direction_raw==logformat.direction_send_id:
                      direction = "---->"
                    else:
                      direction = "<----"
                    break_on_2x_empty_line = logformat.break_on_2x_empty_line
                    mode = 1
                    break
        if mode==1:  # Did we just enter a SIP message?
            # Then do the necessary initialization
            thislcount = lcount-1
            new_line_count = 0
            matched_filter = False
            matched_dialog = False
            message_summary = ""
            sip_call_id = ""
            ptlist = list()
            mode = 2
        elif mode==2:  # Are we inside a SIP message?
            while line:
                if re.search("SIP/2.0", line):
                    request = ""
                    match_trigger = regex_request_line.match(line)
                    if match_trigger:
                        request = match_trigger.group(1)
                    break
                line = inputstream.readline()
                lcount = lcount + 1
            message_summary = line.replace("SIP/2.0", "").strip() + "  "
            if regex_filter == None or regex_filter.search(line):
              matched_filter = True
            while line:
                line = inputstream.readline()
                lcount = lcount + 1
                # End of SIP message?
                if line=="\n":
                  new_line_count += 1
                  if new_line_count==2 and break_on_2x_empty_line:
                    # The SIP message is complete when two empty lines have been read (RFC3261)
                    break
                # Special rule for OXE:
                if regex_sip_end_1.match(line):
                   break

                if regex_filter == None or regex_filter.search(line):
                  matched_filter = True
                if regex_call_id.search(line):
                    sip_call_id = line.split(":",1)[1].strip()
                elif re.search("^m=", line):
                  #m=audio 16410 RTP/AVP 0 8 4 18 101
                  alist = list()
                  alist = line.split()
                  if len(alist)>0:
                    alist.pop(0)
                  if len(alist)>0:
                    alist.pop(0)
                  if len(alist)>0:
                    alist.pop(0)
                  ptlist = ptlist + alist
                elif re.search("sendonly", line):
                  message_summary = message_summary + "/sendonly"
                elif re.search("recvonly", line):
                  message_summary = message_summary + "/recvonly"
                elif re.search("inactive", line):
                  message_summary = message_summary + "/inactive"
                elif regex_rtpmap.search(line):
                  rtpmap.append( (regex_rtpmap.search(line).group(1), regex_rtpmap.search(line).group(2)) )

            # add media type list
            for pt in ptlist:
              found=False
              for entry in rtpmap:
                if entry[0]==pt:
                  message_summary = message_summary + "/" + entry[1]
                  rtpmap.remove(entry)
                  found=True
                  break
              if not found:
                message_summary = message_summary + "/" + pt

            # update call ids
            if not sip_call_id in self.call_ids:
                self.call_ids[sip_call_id] = next_call_id_num
                next_call_id_num = next_call_id_num + 1
                if request<>"" and request in self.tracked_requests:
                    call_ids_displayed.append(sip_call_id)

            if matched_filter and (len(self.tracked_requests)==0 or sip_call_id in call_ids_displayed):
                summary="{0:10}: {1:6}{2:22}: {3:4} {4:120}".format(
                  timestamp, direction, remotesocket,
                  "D"+str(self.call_ids[sip_call_id])+":",
                  message_summary)

                outputmethod(summary, (self.call_ids[sip_call_id]-1))

                self.call_ids_for_line[mcount] = sip_call_id
                self.linenumbers[mcount] = thislcount
                mcount = mcount + 1

            mode = 0

        line = inputstream.readline()
        lcount = lcount + 1
    print "Finished (" + str((datetime.now()-starttime)) + "s)."

  #-------------------------------------
  def open_file(self, afilename):
    try:
      afile = open(afilename)

      # reads the entire file in one go:
      self.data = afile.read()

      afile.seek(0)
      self.btext.text["state"] = "normal"
      self.btext.text.delete(1.0, END)
      self.btext.set(afile.read())
      self.btext.text["state"] = "disabled"
      afile.close()
      self.refresh_summary(cStringIO.StringIO(self.data))

      self.mainwindow.title(os.path.basename(afilename) + " - " + self.appstring)

    except Exception, e:
      tkMessageBox.showwarning("File Problem",
         "Could not read file '%s': %s" % (afilename, str(e)))

  #-------------------------------------
  def on_file_open(self):
    afilename = tkFileDialog.askopenfilename(initialdir=self.last_dir)
    if afilename:
      self.last_filename = afilename
      self.last_dir = os.path.basename(afilename)
      self.open_file(afilename)

  #-------------------------------------
  def on_file_reload(self):
    if self.last_filename:
      self.open_file(self.last_filename)

  #-------------------------------------
  def on_file_export(self):
    self.stringbuffer = ""
    self.write_summary(cStringIO.StringIO(self.data), self.output_to_string)
    self.mainwindow.clipboard_clear()
    self.mainwindow.clipboard_append(self.stringbuffer, type='STRING')

  #-------------------------------------
  def on_select_line(self, index):
    self.btext.text.yview(self.linenumbers[int(index)])

  #-------------------------------------
  def on_filter_entry_return(self, event):
    self.on_apply_filter()

  #-------------------------------------
  def on_apply_filter(self):
    self.filterexpression = self.entry1.get()
    self.refresh_summary(cStringIO.StringIO(self.data))

  #-------------------------------------
  def on_clear_filter(self):
    self.entry1.delete(0, END)
    self.filterexpression = ""
    self.refresh_summary(cStringIO.StringIO(self.data))

  #-------------------------------------
  def set_filter_from_line(self):
    self.entry1.delete(0, END)
    self.filterexpression = "^(Call-ID|i).*" + self.call_ids_for_line[int(self.sumlist.tlist.info_selection()[0])]
    self.entry1.insert(0, self.filterexpression)
    self.refresh_summary(cStringIO.StringIO(self.data))

  #-------------------------------------
  def on_right_click(self, event):
    self.sumlist.tlist.selection_clear()
    self.sumlist.tlist.selection_set(self.sumlist.tlist.nearest(20, event.y_root - self.sumlist.tlist.winfo_rooty()))
    rmenu = Menu(None, tearoff=0, takefocus=0)
    rmenu.add_command(label="Filter on this Dialog", command=self.set_filter_from_line)
    rmenu.tk_popup(event.x_root-3, event.y_root+3, entry="0")

  #-------------------------------------
  def on_apply_request_filter(self, event=None):
    if self.init_done:
        self.tracked_requests = []
        if self.request_filter.get() != self.ANY_REQUEST:
            self.tracked_requests.extend(self.request_filter.get().split('/'))
        self.refresh_summary(cStringIO.StringIO(self.data))

########################################
def main():
  try:
      opts, args = getopt.getopt(sys.argv[1:], "hcr:f:")
  except getopt.GetoptError:
      usage(sys.argv[0])
      sys.exit(2)

  # help requested?
  if ("-h", "") in opts:
      usage(sys.argv[0])
      sys.exit(2)

  print "Motview v" + VERSION + " - Alcatel-Lucent Enterprise SIP Trace Viewer"
  print "Use -h for help."

  theApp = MainApp()

  # parse command line options
  for o, a in opts:
    # filter given?
    if o == "-f":
      theApp.filterexpression = a
    # REQUEST filter?
    if o == "-r":
      theApp.tracked_requests.append(a)
    # console output only?
    if o == "-c":
      theApp.console = True

  if len(args)>0:
    theApp.last_filename = args[0]
  else:
    theApp.last_filename = "stdin"
  if theApp.console:
    if len(args)>0:
      theApp.write_summary(open(theApp.last_filename), theApp.output_to_stdout)
    else:
      theApp.write_summary(sys.stdin, theApp.output_to_stdout)
  else:
    if len(args)>0:
      theApp.open_file(theApp.last_filename)
    theApp.entry1.insert(0, theApp.filterexpression)
    theApp.mainwindow.mainloop()

########################################
if __name__ == "__main__":
  main()
