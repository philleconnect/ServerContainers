#!/bin/python3

# test it with something like this:
# curl -i -X POST -H 'Content-Type: application/json' -d '{"key1": "value1", "key2": "value2"}' http://localhost:8000

import http.server
import socketserver
from http import HTTPStatus
import json
import os

conffile ='/etc/samba/smb.conf'

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
        old = open(conffile, 'r')
        lines = old.readlines()
        old.close()
        new = open(conffile, 'w')
        found = False
        # delete old config:
        for line in lines:
            if line == "# begin ExchangeFolders; DON'T CHANGE MANUALLY!\n":
                found = True
            if not found:
                new.write(line)
            if line == "# end ExchangeFolders; DON'T CHANGE MANUALLY!\n":
                found = False
        # Write new Config
        new.write("# begin ExchangeFolders; DON'T CHANGE MANUALLY!\n")
        j = json.loads(data)
        for d in j:
            new.write('['+d['name']+']\n')
            new.write('    path = '+d['path']+'\n')
            if d['teachers'] and d['students']:
                new.write('    writable = yes\n')
            elif d['teachers'] and not d['students']:
                new.write('    writable = yes\n')
                new.write('    valid users = @teachers\n')
            elif not (d['teachers']) and not (d['students']):
                new.write('    writable = no\n')
                new.write('    valid users = @teachers\n')
            else:
                raise InputError('not all values are declared, please reconfigure samba-folders (might be in inconsistent state at the moment)')
        new.write("# end ExchangeFolders; DON'T CHANGE MANUALLY!\n")
        new.close()
        os.system('service smbd reload')
        #os.system('service nmbd force-reload')

socketserver.TCPServer.allow_reuse_address = True
httpd = socketserver.TCPServer(('', 8000), Handler)
httpd.serve_forever()
