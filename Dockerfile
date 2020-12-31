FROM ubuntu:latest
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ="Europe/Paris"
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y php-fpm composer php-dom php-mysqli nginx php-mbstring php-zip php-curl php-intl mariadb-server wget
RUN mkdir -p /app/public && \
    wget -O /tmp/wordpress.tar.gz https://fr.wordpress.org/latest-fr_FR.tar.gz && \
    cd /tmp && \
    tar -zxvf wordpress.tar.gz && \
    mv /tmp/wordpress/* /app/
RUN echo "upstream php {" > /etc/nginx/sites-available/default && \
    echo "        server unix:/run/php/php7.4-fpm.sock;" >> /etc/nginx/sites-available/default && \
    echo "        server 127.0.0.1:9000;" >> /etc/nginx/sites-available/default && \
    echo "}" >> /etc/nginx/sites-available/default && \
    echo "server {" >> /etc/nginx/sites-available/default && \
    echo "        server_name domain.tld;" >> /etc/nginx/sites-available/default && \
    echo "        root /app;" >> /etc/nginx/sites-available/default && \
    echo "        index index.php;" >> /etc/nginx/sites-available/default && \
    echo "        location / {" >> /etc/nginx/sites-available/default && \
    echo "                try_files \$uri \$uri/ /index.php?\$args;" >> /etc/nginx/sites-available/default && \
    echo "        }" >> /etc/nginx/sites-available/default && \
    echo "        location ~ \.php\$ {" >> /etc/nginx/sites-available/default && \
    echo "                include fastcgi_params;" >> /etc/nginx/sites-available/default && \
    echo "                fastcgi_intercept_errors on;" >> /etc/nginx/sites-available/default && \
    echo "                fastcgi_pass php;" >> /etc/nginx/sites-available/default && \
    echo "                fastcgi_param  SCRIPT_FILENAME \$document_root\$fastcgi_script_name;" >> /etc/nginx/sites-available/default && \
    echo "        }" >> /etc/nginx/sites-available/default && \
    echo "        location ~* \\.(js|css|png|jpg|jpeg|gif|ico)\$ {" >> /etc/nginx/sites-available/default && \
    echo "                expires max;" >> /etc/nginx/sites-available/default && \
    echo "                log_not_found off;" >> /etc/nginx/sites-available/default && \
    echo "        }" >> /etc/nginx/sites-available/default && \
    echo "}" >> /etc/nginx/sites-available/default && \
    echo "#!/bin/sh" > /run.sh && \
    echo "nginx" >> /run.sh && \
    echo "service php7.4-fpm start" >> /run.sh && \
    echo "service mysql start" >> /run.sh && \
    echo "chown www-data:www-data -R /app" >> /run.sh && \
    echo "sleep 10" >> /run.sh && \
    echo "mysql -u root -e \"CREATE DATABASE wordpress\"" >> /run.sh && \
    echo "mysql -u root -e \"CREATE USER 'wordpress'@'localhost' IDENTIFIED BY 'wordpress'\"" >> /run.sh && \
    echo "mysql -u root -e \"GRANT ALL PRIVILEGES ON *.* TO 'wordpress'@'localhost'\"" >> /run.sh && \
    echo "mysql -u root -e \"FLUSH PRIVILEGES\"" >> /run.sh && \
    echo "tail -f /var/log/nginx/*" >> /run.sh && \
    chmod +x /run.sh
CMD ["/run.sh"]
