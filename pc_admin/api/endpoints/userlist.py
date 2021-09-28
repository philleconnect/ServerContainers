#!/usr/bin/env python3

# SchoolConnect Backend
# User list API endpoint
# © 2020-2021 Johannes Kreutz, Dirk Winkel

# Include dependencies
from flask import Blueprint, request, jsonify
from flask_login import login_required

from io import StringIO
import csv

# Include modules
import helpers.essentials as es
import modules.database as db

# Endpoint definition
userListApi = Blueprint("userListApi", __name__)
@userListApi.route("/api/users", methods=["GET"])
@login_required
def listUsers():
    if not es.isAuthorized("usermgmt"):
        return "ERR_ACCESS_DENIED", 403
    dbconn = db.database()
    users = []
    dbconn.execute("SELECT id, firstname, lastname, username, email, DATE_FORMAT(birthdate, '%Y-%m-%d') AS birthdate, persistant FROM people")
    for user in dbconn.fetchall():
        ids = []
        dbconn.execute("SELECT G.id AS id FROM groups G INNER JOIN people_has_groups PHG ON PHG.group_id = G.id INNER JOIN people P ON P.id = PHG.people_id WHERE P.id = %s", (user["id"],))
        for group in dbconn.fetchall():
            ids.append(group["id"])
        user["groups"] = ids
        users.append(user)
    return jsonify(users), 200

@userListApi.route("/api/usercount", methods=["GET"])
@login_required
def userCount():
    if not es.isAuthorized("usermgmt"):
        return "ERR_ACCESS_DENIED", 403
    dbconn = db.database()
    dbconn.execute("SELECT COUNT(*) AS c FROM people")
    return jsonify({"count": dbconn.fetchone()["c"]}), 200

@userListApi.route("/api/userListExport", methods=["GET"])
@login_required
def userListExport():
    if not es.isAuthorized("usermgmt"):
        return "ERR_ACCESS_DENIED", 403
    data = StringIO()
    w = csv.writer(data)
    dbconn = db.database()
    dbconn.execute("SELECT firstname, lastname, short, username FROM people")
    w.writerow(('id', 'firstname', 'lastname', 'short', 'username', 'groups'))
    yield data.getvalue()
    data.seek(0)
    data.truncate(0)
    for user in dbconn.fetchall():
        uid = user[0]
        fn = user[1]
        ln = user[2]
        so = user[3]
        un = user[4]
        groups = '"'
        dbconn2 = db.database()
        dbconn2.execute("select name from groups where id in(select group_id from people_has_groups where people_id='"+uid+"' and type='3');")
        for gname in dbconn2.fetchall():
            groups=groups+';'+gname
        groups=groups+'"'
        w.writerow((fn, ln, so, un, groups))
        yield data.getvalue()
        data.seek(0)
        data.truncate(0)
    response = Response(generate(), mimetype='text/csv')
    response.headers.set("Content-Disposition", "attachment", filename="userList.csv")
    return response
