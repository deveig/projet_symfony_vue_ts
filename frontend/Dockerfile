FROM node:latest
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY ./ .
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/app.key -out /etc/ssl/certs/app.crt -subj "/CN=localhost"
RUN export NODE_EXTRA_CA_CERTS=/etc/ssl/certs/app.crt
RUN npm run build
CMD ["npm", "run", "preview"]