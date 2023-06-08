# TRAMITE-DOCUMENTARIO-UNAAT
Sistema de tramite documentarlo para la universidad de Tarma 

#requerimientos 

node.js v16 
php v8

tener instalado composer y mysql

#comandos de instalación

npm install
composer install

#configuracion

se tiene que crear una base de datos en mysql

quitar el .example del archivo.env
y colocar en el apartado de NAME_DATABSE= nombre de tu base de datos

ejecutar 
php artisan migrate:fresh --seed

#comandos de ejecucion

npm run watch
php artisan serve


#credenciales iniciales

admin@gmail.com
password


