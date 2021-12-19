*Herramientas 

PHP >= 7.4
Mysql >= 8.0
XAMPP >= 3.3.0
Javascript, HTML y CSS.
Boostrap >= 5.1.3

Crear virtualhost de apache (system32/drivers)
xammp/apache/httpd-vhosts.conf (ruta para crear el linux, backend)
Ejecutar el sql execute (creada e instalada la base de datos)

http://localhost:666/frontend/index.html
http://api.ensolvers.local/

*Instalacion de Linux

MySQL:

#!/bin/bash

mysql -h root -p < Backend/sql/execute.sql

php -S localhost:666 -t .

#the url for the webpage is set to : http://localhost:666/Frontend/index.html 

sudo chmod +x deploy.sh

PHP:

php -S localhost:666 -t /directory

Para el funcionamiento de XAMMP
                                                      ##PREREQUISITES
if [[ ! -f /tmp/lampp-startstop ]] ; then             # if temp file doesn't exist
 echo 0 > /tmp/lampp-startstop 2>&1                   # create it and write 0 in it


                                                      ##IF NOT RUNNING
if [ "cat /tmp/lampp-startstop" == "0" ] ; then     # if temp file contains 0
 sudo /opt/lampp/lampp start                          # start lampp
 echo 1 > /tmp/lampp-startstop 2>&1                   # write 1 in the temp file
 notify-send "Lampp" "Program started." -i xampp      # send a notification
 exit 0                                               # and exit


                                                      ##IF RUNNING
if [ "cat /tmp/lampp-startstop" == "1" ] ; then     # if temp file contains 1
 sudo /opt/lampp/lampp stop                           # stop lampp
 echo 0 > /tmp/lampp-startstop 2>&1                   # write 0 in the temp file
 notify-send "Lampp" "Program stopped." -i xampp      # send a notification
 exit 0                                               # and exit
