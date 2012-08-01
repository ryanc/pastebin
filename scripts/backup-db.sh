#!/bin/bash

VHOST_PATH="/www/paste.confabulator.net"
DB="pastebin.db"
DB_PATH="$VHOST_PATH/db/$DB"
TIMESTAMP=`date +%s`
BACKUP_PATH="$VHOST_PATH/backups/pastebin-$TIMESTAMP.db"

sqlite3 $DB_PATH ".backup $BACKUP_PATH"
