#!/bin/bash

current_time=$(date +%s)

echo "START LARAVEL BACKUP"
rm -rf $DDPC_HOME/backup
mkdir $DDPC_HOME/backup
cp /etc/httpd/conf.d/laravel.conf $DDPC_HOME/backup
mkdir $DDPC_HOME/backup/ssl
cp -R --dereference /etc/letsencrypt/live/vps.bltn.cc/ $DDPC_HOME/backup/ssl
mkdir $DDPC_HOME/backup/ssl-apache-conf
cp /etc/letsencrypt/options-ssl-apache.conf $DDPC_HOME/backup/ssl-apache-conf
cp $DDPC_HOME/.env $DDPC_HOME/backup/
cp -R --dereference $DDPC_HOME/storage/app $DDPC_HOME/backup
cp -R --dereference $DDPC_HOME/storage/logs $DDPC_HOME/backup
cp -R --dereference /etc/supervisord.d/laravel-worker.ini $DDPC_HOME/backup
php artisan db:backup
mkdir $DDPC_HOME/backups
tar -czvf "$DDPC_HOME/backups/backup_$current_time.gz" $DDPC_HOME/backup
