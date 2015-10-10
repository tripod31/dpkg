#!/bin/sh
if [ -f dpkg.db ]; then
  rm dpkg.db
fi
sqlite3 dpkg.db < create.sql
chmod g+w . templates_c dpkg.db
sudo chgrp www-data . templates_c dpkg.db
