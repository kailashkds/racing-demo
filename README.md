# Racing Demo

Symfony project 5.4
-------------------

# Installation
### Prerequisites
- Ubuntu 16.04 LTS or newer (https://ubuntu.com/download)
- Git version control: `$ sudo apt-get install git`
- Docker (Installation: https://docs.docker.com/engine/install/ubuntu/)
- Docker Compose (Installation: https://docs.docker.com/compose/install/)

* **1) Git clone**
``` bash
$ git clone https://github.com/kailashkds/racing-demo.git
```
* **2) Inside the docker folder**
``` bash
$ cd racing-demo/
```

* **3) Build Docker**
``` bash
$ docker-compose build
```

* **4) Start the Docker**
``` bash
$ docker-compose up -d
```
* **5) Add domian in your host file **
``` bash
$ 127.0.0.1 demoweb.local
```

* **6) Docker Stop**
``` bash
$ docker-compose stop
```
## For running php unit test:

* **1) For running tests**
``` bash
$ docker exec -it demo_php bash
```

* **2) For running tests**
``` bash
$ docker exec -it demo_php bash
```
