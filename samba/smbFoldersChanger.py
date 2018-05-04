#!/bin/python3

# test it with something like this:
# curl -i -X POST -H 'Content-Type: application/json' -d '{"key1": "value1", "key2": "value2"}' http://localhost:8000

import http.server
import socketserver
from http import HTTPStatus
import json
import os

conffile = '/etc/samba/smb.conf'
bakfile= '/home/FolderConf.txt'

def includeBackup():
    # delete old stuff:
    old = open(conffile, 'r')
    lines = old.readlines()
    old.close()
    new = open(conffile, 'w')
    found = False
    for line in lines:
        if line == "# begin ExchangeFolders; DON'T CHANGE MANUALLY!\n":
            found = True
        if not found:
            new.write(line)
        if line == "# end ExchangeFolders; DON'T CHANGE MANUALLY!\n":
            found = False
    # write backup file to config:
    newconf = open(bakfile, 'r')
    new.writelines(newconf)
    newconf.close()
    new.close()
    os.system('service smbd reload')

class Handler(http.server.SimpleHTTPRequestHandler):
    ''' Handler to apply samba-config-changes that are received as POST-data '''
    def do_GET(self):
        ''' give feedback to GET-request for debugging '''
        self.send_response(HTTPStatus.OK)
        self.end_headers()
        self.wfile.write(b"I'm here!")

    def do_POST(self):
        ''' get the POST-Data and geve feedback if they are fine to work with '''
        self.send_response(HTTPStatus.OK)
        self.end_headers()
        content_length = int(self.headers['Content-Length'])
        post_data = self.rfile.read(content_length)
        try:
            self.configSamba(str(post_data, 'utf-8'))
            self.wfile.write(b'Thanks, it worked\n')
        except InputError as err:
            self.wfile.write(b'InputError!\n')
            print(err.args)

    def configSamba(self, data):
        ''' edit the samba config and restart it if changed '''
        # create new config block:
        newconf = ['']
        newconf.append("# begin ExchangeFolders; DON'T CHANGE MANUALLY!\n")
        j = json.loads(data)
        for d in j:
            newconf.append('['+d['name']+']\n')
            newconf.append('    path = '+d['path']+'\n')
            error = False
            if d['writeable']:
                newconf.append('    writeable = yes\n')
            elif not d['writeable']:
                newconf.append('    writeable = no\n')
            else:
                error = True
            if d['teachers'] and d['students']:
                # no new line needed, but config is valid
                pass
            elif d['teachers'] and not d['students']:
                newconf.append('    valid users = @teachers\n')
            elif not (d['teachers']) and (d['students']):
                newconf.append('    valid users = @students\n')
            else:
                error = True
            if error:
                raise InputError('not all values are declared, please reconfigure samba-folders (might be in inconsistent state at the moment)')
        newconf.append("# end ExchangeFolders; DON'T CHANGE MANUALLY!\n")
        # delete old config:
        old = open(conffile, 'r')
        lines = old.readlines()
        old.close()
        new = open(conffile, 'w')
        found = False
        for line in lines:
            if line == "# begin ExchangeFolders; DON'T CHANGE MANUALLY!\n":
                found = True
            if not found:
                new.write(line)
            if line == "# end ExchangeFolders; DON'T CHANGE MANUALLY!\n":
                found = False
        # write new stuff:
        new.writelines(newconf)
        new.close()
        # write to bakfile:
        bak = open(bakfile, 'w')
        bak.truncate()
        bak.writelines(newconf)
        bak.close()
        # reload samba:
        os.system('service smbd reload')
        #os.system('service nmbd force-reload')

if os.path.isfile(bakfile):
    includeBackup()
socketserver.TCPServer.allow_reuse_address = True
httpd = socketserver.TCPServer(('', 8000), Handler)
httpd.serve_forever()
