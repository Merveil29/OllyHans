FROM richarvey/nginx-php-fpm:3.1.6

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/backend/public
ENV NGINX_DOCUMENT_ROOT=/var/www/html/backend/public

RUN apk add --no-cache nodejs npm

COPY . /var/www/html

WORKDIR /var/www/html/frontend
RUN npm ci && npm run build && cp -r dist /var/www/html/backend/public/dist

WORKDIR /var/www/html/backend
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80 443

CMD ["/start.sh"]
