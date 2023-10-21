ARG  platform arm64v8
FROM ${platform}/ubuntu

ENV XDEBUG_OUTPUT_DIR /tmp/xdebug
ENV XDEBUG_MODE debug
ENV XDEBUG_START yes
ENV PROJECT_NAME ""
ENV VERSION 0.0.1
LABEL version=${VERSION}
LABEL description=${PROJECT_NAME}

# avoid to ask what to do and brake the build process
ENV DEBIAN_FRONTEND=noninteractive
#install utils
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
wget nano mc vim htop procps git plocate iputils-ping lynx telnet \
net-tools nmap lsof

# install Apache
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
apache2 apache2-dev cmake

#install php
RUN apt-get update && apt-get upgrade -y && apt-get install -y \ 
php libapache2-mod-php php-mysql php-json php-gd php-intl php-pdo php-cli \
php-xml php-mbstring php-bcmath php-zip php-apcu php-opcache  php-soap \
php-tidy php-pcov php-redis php-yaml php-memcache php-memcached \
php-symfony-process php-curl composer php-xdebug

#install php tools
RUN apt-get update && apt-get upgrade -y && apt-get install -y \ 
php-dev php-pear libmcrypt-dev gcc make autoconf libc-dev \
pkg-config
RUN pecl install mcrypt

# Set up base directories
RUN mkdir -p /var/log/httpd
RUN mkdir -p /var/log/php

# UTC Timezone
RUN ln -sf /usr/share/zoneinfo/UTC /etc/localtime

#setup Apache
COPY entrypoint.sh /entrypoint.sh
COPY setrights.sh /setrights.sh
RUN mkdir ssl-certs
COPY ssl/cert.pem /ssl-certs
COPY ssl/key.pem /ssl-certs
COPY conf.d/httpd.conf /etc/apache2/conf-available
RUN ln -sf /etc/apache2/conf-available/httpd.conf /etc/apache2/conf-enabled/httpd.conf 
RUN chmod 700 /entrypoint.sh
RUN chmod 700 /setrights.sh
RUN     { \
            echo 'LogLevel warn rewrite:trace8'; \
        } | tee /etc/apache2/mods-available/rewrite-log.load; 

RUN ln -sf /etc/apache2/mods-available/expires.load /etc/apache2/mods-enabled/expires.load
RUN ln -sf /etc/apache2/mods-available/cache.load /etc/apache2/mods-enabled/cache.load
RUN ln -sf /etc/apache2/mods-available/cache_disk.load /etc/apache2/mods-enabled/cache_disk.load
RUN ln -sf /etc/apache2/mods-available/cache_socache.load /etc/apache2/mods-enabled/cache_socache.load
RUN ln -sf /etc/apache2/mods-available/ssl.load /etc/apache2/mods-enabled/ssl.load
RUN ln -sf /etc/apache2/mods-available/ssl.conf /etc/apache2/mods-enabled/ssl.conf
RUN ln -sf /etc/apache2/mods-available/socache_shmcb.load /etc/apache2/mods-enabled/socache_shmcb.load
RUN ln -sf /etc/apache2/mods-available/headers.load /etc/apache2/mods-enabled/headers.load
RUN ln -sf /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
RUN ln -sf /etc/apache2/mods-available/http2.load /etc/apache2/mods-enabled/http2.load
RUN ln -sf /etc/apache2/mods-available/socache_shmcb.load /etc/apache2/mods-enabled/socache_shmcb.load
RUN ln -sf /etc/apache2/mods-available/rewrite-log.load /etc/apache2/mods-enabled/rewrite-log.load

# setup php
RUN sed -i \
    -e 's~^;date.timezone =$~date.timezone = Europe/Madrid~g' \
    -e 's~^display_startup_errors = Off$~display_startup_errors = On~g' \
    -e 's~^display_errors = Off$~display_errors = On~g' \
    -e 's~^;user_ini.filename =$~user_ini.filename =~g' \
	-e 's~^short_open_tag = Off$~short_open_tag = On~g' \
    -e 's~^sendmail_path = /usr/sbin/sendmail -t -i$~sendmail_path = /usr/bin/msmtp -C /etc/msmtprc -t -i~g' \
    -e 's~^upload_max_filesize = 2M$~upload_max_filesize = 20M~g' \
    -e 's~^post_max_size = 8M$~post_max_size = 20M~g' \
    /etc/php/8.1/apache2/php.ini

RUN sed -i \
    -e 's~^;date.timezone =$~date.timezone = Europe/Madrid~g' \
    -e 's~^display_startup_errors = Off$~display_startup_errors = On~g' \
    -e 's~^display_errors = Off$~display_errors = On~g' \
	-e 's~^short_open_tag = Off$~short_open_tag = On~g' \
    /etc/php/8.1/cli/php.ini

# setup php xdebug
RUN     { \
            echo "zend_extension=/usr/lib/php/20210902/xdebug.so"; \
            echo "[xdebug]"; \
            echo "xdebug.mode=${XDEBUG_MODE}"; \
            echo "xdebug.start_with_request=${XDEBUG_START}"; \
            echo "xdebug.discover_client_host = 1"; \
            echo "xdebug.client_host =host.docker.internal"; \
            echo "xdebug.client_port =9003"; \
            echo "xdebug.idekey=BODAS-DOCKER" ; \
            echo "xdebug.log=/var/log/php/xdebug_remote.log"; \
			echo "xdebug.output_dir=${XDEBUG_OUTPUT_DIR}"; \
        } | tee /etc/php/8.1/mods-available/xdebug.ini; 

RUN     { \
            echo "zend_extension=/usr/lib/php/20210902/xdebug.so"; \
            echo "[xdebug]"; \
            echo "xdebug.mode=${XDEBUG_MODE}"; \
            echo "xdebug.start_with_request=${XDEBUG_START}"; \
            echo "xdebug.discover_client_host = 1"; \
            echo "xdebug.client_host =host.docker.internal"; \
            echo "xdebug.client_port =9003"; \
            echo "xdebug.idekey=BODAS-CLI-DOCKER" ; \
            echo "xdebug.log=/var/log/php/xdebug_remote.log"; \
			echo "xdebug.output_dir=${XDEBUG_OUTPUT_DIR}"; \
        } | tee /etc/php/8.1/cli/conf.d/10-xdebug.ini; 


RUN ln -sf /etc/php/8.1/mods-available/xdebug.ini /etc/php/8.1/apache2/conf.d/10-xdebug.ini        
RUN ln -sf /etc/php/8.1/mods-available/apcu.ini /etc/php/8.1/apache2/conf.d/40-acpu.ini

# set default state for Okta caching as it appears to be broken by default at the moment
RUN chmod -R 777 /var/log
RUN mkdir /tmp/xdebug
RUN chmod -R 777 /tmp/xdebug
EXPOSE 80