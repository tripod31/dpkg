#!/bin/sh
sqlite3 dpkg.db "select name from installed where status = 'rc'" | xargs sudo apt-get remove --purge
