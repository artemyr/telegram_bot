FROM nginx

ARG DOMAIN
ARG SERVER_NAME

ARG SERVER_NAME="${DOMAIN} www.${DOMAIN}"
RUN echo "${SERVER_NAME}"

COPY ./conf/nginx.conf /etc/nginx/nginx.conf
COPY ./conf/default.conf /etc/nginx/conf.d/default.conf

RUN sed -i "s/#SERVER_NAME#/$SERVER_NAME/g" /etc/nginx/conf.d/default.conf

COPY ./conf/upstream.conf /etc/nginx/conf.d/upstream.conf

EXPOSE 80
EXPOSE 443

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]
