#!/bin/bash
set -e
source /etc/apache2/envvars
./setrights.sh
/usr/sbin/apache2 -E /dev/stdout -DFOREGROUND
