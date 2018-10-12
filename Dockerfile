FROM alpine:3.8

# label
LABEL maintainer="eyJhb <eyjhb@gmail.com>"

# envs
ENV HTTPBIND=8080

# install packages
RUN apk add --no-cache \
    php7 \
    php7-iconv \
    php7-mbstring \
    php7-curl \
    composer

VOLUME ["/src"]


