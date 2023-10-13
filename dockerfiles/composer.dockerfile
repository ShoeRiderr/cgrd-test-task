FROM composer:latest
 
RUN addgroup -g 1000 cgrd_user && adduser -G cgrd_user -g cgrd_user -s /bin/sh -D cgrd_user

USER cgrd_user 

WORKDIR /var/www/html
 
ENTRYPOINT [ "composer", "--ignore-platform-reqs" ]