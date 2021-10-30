FROM nginx:1.21

RUN mkdir -p /var/www/public

RUN rm -f /etc/nginx/nginx.conf /etc/nginx/sites-enabled/* /etc/nginx/conf.d/*
COPY nginx.conf /etc/nginx/
COPY app.conf /etc/nginx/sites-enabled/

WORKDIR /var/www/public
