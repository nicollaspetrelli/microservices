FROM node:16-alpine

# Base app
WORKDIR /app

# Installing tcpdump
RUN apk --no-cache add tcpdump

# Taking advantage of layers caching
COPY package*.json ./
RUN npm install

# Copying the rest of files
COPY . .

# Starting server
EXPOSE 50051
CMD [ "npm", "start" ]
