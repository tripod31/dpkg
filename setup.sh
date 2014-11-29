#!/bin/sh
if [ -f dpkg.db ]; then
  rm dpkg.db
fi
sqlite3 dpkg.db < create.sql
sudo chgrp apache . templates_c dpkg.db
chmod g+w . templates_c dpkg.db
