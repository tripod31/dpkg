#!/bin/sh
if [ -f dpkg.db ]; then
  rm dpkg.db
fi
sqlite3 dpkg.db < create.sql
chmod o+w . templates_c dpkg.db
