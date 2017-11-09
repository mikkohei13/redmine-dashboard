# Build this using following commands
# See version numbers/tags at https://hub.docker.com/r/mikkohei13/redmine-dashboard/tags/
# docker build -t mikkohei13/redmine-dashboard:VERSION-NUMBER .
# docker push -t mikkohei13/redmine-dashboard:VERSION-NUMBER

FROM richarvey/nginx-php-fpm:1.3.5

# Copy PHP scripts files to image
COPY ./html/* /var/www/html/

EXPOSE 80
