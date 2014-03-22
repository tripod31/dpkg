#!/bin/sh

rm dpkg_all.tar.gz
tar -zcvf dpkg_all.tar.gz -C .. dpkg --exclude=*.tpl.php --exclude=*.tar.gz
