# Hydrogen Joomla Plugin Test

This repository includes a Docker Compose configuration for running a Joomla application with a MySQL database, designed to test the Hydrogen Payment Joomla Plugins.

## Description

This Docker Compose setup includes:

- Joomla 5.1 with PHP 8.1 and Apache
- MySQL 8.0.13

The Joomla application is configured to use a MySQL database and is set up with persistent data storage and custom PHP configurations.

## Getting Started

### Prerequisites

- Docker
- Docker Compose

### Pulling the Docker Image

To pull the Docker image from Docker Hub, use the following command:

```bash

docker pull yusuflawal2020/hydrogen-joomla-test

```

# Using Docker Compose

- Clone the Repository (if applicable):

```bash

git clone https://github.com/lawalyusuf/joomla-website-with-docker.git
cd your-repo

```

## Ensure Docker Compose File

Make sure you have the docker-compose.yml file in your directory.
<!-- Ensure you have the docker-compose.yml file in your working directory with the following content: -->

## Run Docker Compose

- Prepare the Docker Compose File

Ensure you have the docker-compose.yml file in your working directory with the following content:

```yaml

version: '3.8'
services:
  joomla:
    image: yusuflawal2020/hydrogen-joomla-test
    container_name: hydrogen-app
    restart: always
    ports:
      - 8081:80
    environment:
      - JOOMLA_DB_HOST=joomladb
      - JOOMLA_DB_PASSWORD=example
    depends_on:
      joomladb:
        condition: service_healthy
    volumes:
      - ./site_joomla:/var/www/html
      - ./config/php.ini:/usr/local/etc/php/conf.d/custom.ini
      - ./vmfiles:/var/www/vmfiles  # Create vmfiles outside Joomla root
    command: /bin/bash -c "chmod -R 777 /var/www/vmfiles /var/www/html/images/virtuemart /var/www/html/cache /var/www/html/administrator/logs /var/www/html/tmp && apache2-foreground"

  joomladb:
    image: mysql:8.0.13
    container_name: hydrogen-db
    restart: always
    environment:
      - MYSQL_ROOT_PASSWORD=example
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "127.0.0.1"]
      timeout: 20s
      retries: 10
    volumes:
      - ./db:/var/lib/mysql

```

## Start the Services

- Use Docker Compose to start the services in detached mode (running in the background):

```bash

docker compose up --detach

```

This command will start both the Joomla and MySQL services as defined in the docker-compose.yml file and run them in the background.

# Configuration

## Environment Variables

- JOOMLA_DB_HOST: The hostname for the MySQL database service (default is joomladb).
- JOOMLA_DB_PASSWORD: The password for the MySQL root user (default is example).
- MYSQL_ROOT_PASSWORD: The password for the MySQL root user in the joomladb service

# Volumes

- ./site_joomla maps to /var/www/html in the Joomla container for storing Joomla application files.
- ./config/php.ini maps to /usr/local/etc/php/conf.d/custom.ini for custom PHP configurations.
- ./vmfiles maps to /var/www/vmfiles for storing files outside the Joomla root directory.
- ./db maps to /var/lib/mysql for MySQL data persistence.

# Ports

- 8081 on the host maps to 80 in the Joomla container, making the Joomla application accessible at http://localhost:8081.

# Health Checks

The MySQL service includes a health check to ensure the database is ready before the Joomla service starts. It uses the mysqladmin ping command to verify the MySQL instance is up and running.

# Troubleshooting

- Port Conflicts: If port 8081 is already in use, you may need to change it to another available port in the docker-compose.yml file.
- Database Connection Issues: Ensure that the JOOMLA_DB_HOST and MYSQL_ROOT_PASSWORD environment variables match between the Joomla and MySQL services.

# Contributing

If you encounter issues or have suggestions for improvements, please create an issue or submit a pull request on the Docker Hub repository.














