#!/bin/bash

VHOST_DIR="/www/p.confabulator.net"
DB="pastebin.db"
DB_PATH="$VHOST_DIR/db/$DB"
TIMESTAMP=`date +%s`
BACKUP_DIR="$VHOST_DIR/backups"
BACKUP_PATH="$BACKUP_DIR/pastebin-$TIMESTAMP.db"

sqlite3 $DB_PATH ".backup $BACKUP_PATH"
ln -nfs $BACKUP_PATH $BACKUP_DIR/pastebin-latest.db
