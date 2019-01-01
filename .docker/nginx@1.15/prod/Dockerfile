FROM nginx:alpine

COPY config/options.conf /etc/nginx/conf.d/000-options.conf
COPY config/vhost.conf /etc/nginx/conf.d/100-default.conf

COPY ../../.. /var/www/html
