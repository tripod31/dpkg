#!/usr/bin/env python3

import sqlite3

from flask import (Flask, g, jsonify, make_response, render_template,
                   request)

app = Flask(__name__)
app.secret_key = b'random string...'

# get Database Object.
def get_db():
    if 'db' not in g:
        g.db = sqlite3.connect('dpkg.db')
    return g.db

# close Dataabse Object.
def close_db(e=None):
    db = g.pop('db', None)
    if db is not None:
        db.close()

# routes
@app.route('/', methods=['GET'])
def index():
    return render_template(
        'index.html',
        )

@app.route('/ajax', methods=['POST'])
def ajax():
    db = get_db()
    
    #クエリー
    sql = request.form.get('sql')
    pkg_name =  request.form.get('pkg_name')
    hide_lib = request.form.get('hide_lib')
    hide_rc = request.form.get('hide_rc')
    cur = db.execute(sql)
    colnames = [desc[0] for desc in cur.description]
    colname_idx = {desc[0]:i for i,desc in enumerate(cur.description)} #{列名:列番号}
    rows_org = cur.fetchall()
    #クエリ結果をフィルタ
    rows =[]
    for row in rows_org:
        if len(pkg_name)>0 and pkg_name not in row[colname_idx["name"]]:
            continue
        if hide_lib and "lib" in row[colname_idx["name"]]:
            continue
        if hide_rc and row[colname_idx["status"]]=="rc":
            continue

        rows.append(row)

    cur = db.execute("SELECT * FROM info")
    info = {row[0]:row[1] for row in cur.fetchall()}  #{name:info}

    #print(info)
    data = {'colnames':colnames,'rows':rows,'info':info}
    resp = make_response(jsonify(data))
    resp.headers['Access-Control-Allow-Origin'] = '*'   #同一ドメイン以外でもアクセス可能にする
    return resp

if __name__ == '__main__':
    app.debug = True
    app.run(host='0.0.0.0')
