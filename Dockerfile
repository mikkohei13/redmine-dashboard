# Build this using
# docker build -t mikkohei13/redmine-dashboard:VERSION-NUMBER .

FROM richarvey/nginx-php-fpm:1.3.5

# Copy PHP scripts files to image
COPY ./html/* /var/www/html/

EXPOSE 80
