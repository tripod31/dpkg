#!/bin/sh

rm dpkg.tar.gz
tar -zcvf dpkg.tar.gz -C .. dpkg --exclude=*.tpl.php --exclude=old --exclude=.* --exclude=make*.sh --exclude=purge.sh --exclude=nbproject --exclude=*.tar.gz
