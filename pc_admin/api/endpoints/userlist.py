#!/usr/bin/env python3

# SchoolConnect Backend
# User list API endpoint
# © 2020-2021 Johannes Kreutz, Dirk Winkel

# Include dependencies
from flask import Blueprint, request, jsonify, make_response
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
    w = csv.writer(data, delimiter =';', quotechar ='"', quoting=csv.QUOTE_MINIMAL)
    w.writerow(('lastname', 'firstname', 'short', 'username', 'groups'))
    dbconn = db.database()
    dbconn.execute("SELECT id, lastname, firstname, short, username FROM people")
    for user in dbconn.fetchall():
        groups = ''
        dbconn2 = db.database()
        dbconn2.execute("select name from groups where id in(select group_id from people_has_groups where people_id='"+user['id']+"');")
        first = True
        for g in dbconn2.fetchall():
            if first:
                first = False
            else:
                groups = groups+';'
            groups=groups+g["name"]
        w.writerow((user["lastname"], user["firstname"], user["short"], user["username"], groups))
    response = make_response(data.getvalue())
    response.headers["Content-Disposition"] = "attachment; filename=userList.csv"
    response.headers["Content-Type"] = "text/csv";
    return response

@userListApi.route("/api/teacherListExport", methods=["GET"])
@login_required
def teacherListExport():
    if not es.isAuthorized("usermgmt"):
        return "ERR_ACCESS_DENIED", 403
    data = StringIO()
    w = csv.writer(data, delimiter =';', quotechar ='"', quoting=csv.QUOTE_MINIMAL)
    w.writerow(('lastname', 'firstname', 'short', 'username', 'groups'))
    dbconn = db.database()
    dbconn.execute("SELECT id, lastname, firstname, short, username FROM people")
    for user in dbconn.fetchall():
        groups = ''
        dbconn2 = db.database()
        dbconn2.execute("select name, type from groups where id in(select group_id from people_has_groups where people_id='"+user['id']+"');")
        teacher = False
        first = True
        for g in dbconn2.fetchall():
            if g['name'] == 'teachers':
                teacher = True
            if g['type'] == 3:
                if first:
                    first = False
                else:
                    groups = groups+';'
                groups=groups+g["name"]
        if teacher:
            w.writerow((user["lastname"], user["firstname"], user["short"], user["username"], groups))
    response = make_response(data.getvalue())
    response.headers["Content-Disposition"] = "attachment; filename=teacherList.csv"
    response.headers["Content-Type"] = "text/csv";
    return response

@userListApi.route("/api/studentListExport", methods=["GET"])
@login_required
def studentListExport():
    if not es.isAuthorized("usermgmt"):
        return "ERR_ACCESS_DENIED", 403
    data = StringIO()
    w = csv.writer(data, delimiter =';', quotechar ='"', quoting=csv.QUOTE_MINIMAL)
    w.writerow(('lastname', 'firstname', 'username', 'groups'))
    dbconn = db.database()
    dbconn.execute("SELECT id, lastname, firstname, username FROM people")
    for user in dbconn.fetchall():
        groups = ''
        dbconn2 = db.database()
        dbconn2.execute("select name, type from groups where id in(select group_id from people_has_groups where people_id='"+user['id']+"');")
        student = False
        first = True
        for g in dbconn2.fetchall():
            if g['name'] == 'students':
                teacher = True
            if g['type'] == 3:
                if first:
                    first = False
                else:
                    groups = groups+';'
                groups=groups+g["name"]
        if student:
            w.writerow((user["lastname"], user["firstname"], user["username"], groups))
    response = make_response(data.getvalue())
    response.headers["Content-Disposition"] = "attachment; filename=studentList.csv"
    response.headers["Content-Type"] = "text/csv";
    return response
