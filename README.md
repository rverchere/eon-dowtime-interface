# User downtime interface

## Introduction

## Run container

First, you need to build image from Dockerfile :

```bash
docker build -t eon-downtime-interface .
```

### Direct run

```
docker run -d |
      --restart=always \
      -p 8080:80 \
      -v /srv/docker-data/eon-dwt-int:/var/www/html \
      -e EON_APIKEY=<specify API key here>
      -e TZ=Europe/Paris
      eon-downtime-interface
```

### Docker Compose

You could use provided docker-compose.yml to run previously build image :

```yaml
version: '3.6'

services:
  downtime:
    image: eon-downtime-interface
    container_name: downtime-interface
    ports:
      - 8080:80
    environment:
      - EON_APIKEY=<specify API key here>
      - TZ=Europe/Paris
    volumes:
      - /srv/docker-data/eon-dwt-int:/var/www/html
    restart: always
```

To launch container, just run :

```yaml
docker-compose up -d
```

## Applications file

```yaml
---
displayname: "Template configuration"
app:
  - host: Applications_BP
    service: Template
hosts:
  - host: localhost
    services:
      - memory
      - partitions
      - processor
      - systime
      - uptime
    propagation_childs: false
  - host: test-host
    propagation_childs: false
```