#
# Pull base image.
ARG  platform arm64v8
FROM --platform=$platform traefik:latest
ENV PROJECT_NAME ""
ENV VERSION 0.0.1
LABEL version=${VERSION}
LABEL description=${PROJECT_NAME}


EXPOSE 80
EXPOSE 443
EXPOSE 8080