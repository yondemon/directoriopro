Instalación de directoriopro
============================

Configurar servidor local
-------------------------

* [Primeros pasos con symfony2 usando MAMP en mac](http://miquelcamps.com/symfony/primeros-pasos-con-symfony2-usando-mamp-en-mac/)
* [Subiendo symfony2 a producción](http://miquelcamps.com/symfony/subiendo-symfony2-a-produccion/)

Instalación
-----------

### Copia la ultima copia del repositorio

    git clone git://github.com/miquelcamps/directoriopro.git
    cd directoriopro

### Crea el fichero de configuración

renombra app/config/parameters.ini.dist a parameters.ini
introduce los datos de tu base de datos

### Instalar vendors de symfony

    php bin/vendors install

### Configurar BD

    php app/console doctrine:schema:update
    php app/console doctrine:schema:update --force

### Reinicia symfony

    php app/console cache:clear

### Entra en la web
http://localhost/dir/web/app_dev.php


Actualizar a la última copia
----------------------------

	git pull
	php app/console cache:clear

