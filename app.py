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
    sql = request.form.get('sql')
    #print(sql)
    cur = db.execute(sql)
    colnames = [desc[0] for desc in cur.description]
    rows = cur.fetchall()
    
    cur = db.execute("SELECT * FROM info")
    info = {row[0]:row[1] for row in cur.fetchall()}  #{name:info}

    #print(info)
    data = {'colnames':colnames,'rows':rows,'info':info}
    resp = make_response(jsonify(data))
    resp.headers['Access-Control-Allow-Origin'] = '*'   #同一ドメイン以外でもアクセス可能にする
    return resp

if __name__ == '__main__':
    app.debug = True
    app.run(host='localhost')
