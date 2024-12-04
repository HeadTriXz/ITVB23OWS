FROM php:8.3-apache

WORKDIR /app
COPY . .

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
