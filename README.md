# Ontwikkelstraten (ITV23BOWS)

This repository contains the development environment and application code for the Ontwikkelstraten course at Hanze University of Applied Sciences. It includes a PHP-based web application for the board game Hive, which was developed as part of the course, alongside an AI service and a MySQL database that were provided.

## Table of Contents
- [Project Structure](#project-structure)
- [Usage](#usage)
    - [1. Running Manually (Local PHP Server)](#1-running-manually-local-php-server)
    - [2. Running in Separate Containers](#2-running-in-separate-containers)
    - [3. Running with Docker Compose (Recommended)](#3-running-with-docker-compose-recommended)
- [Configuration](#configuration)
- [CI/CD and Quality Assurance](#cicd-and-quality-assurance)
- [Unit Testing](#unit-testing)
- [License](#license)

## Project Structure
```
.
├── apps/
│   ├── ai/       # AI service
│   └── hive/     # Main PHP application
└── db/           # MySQL database setup
```

## Usage
You can run the application in multiple ways:

### 1. Running Manually (Local PHP Server)
Ensure you have PHP 8.3 installed and that the database is properly set up before running the application. Then, navigate to the `apps/hive` directory:
```sh
cd apps/hive
```
After that, run:
```sh
php -S localhost:8000 -t public/
```
The application will be accessible at `http://localhost:8000/`.

### 2. Running in Separate Containers
Each component can be run independently using Docker:

- **AI Service**:
  ```sh
  docker build -t hive-ai ./apps/ai
  docker run -p 5000:5000 hive-ai
  ```
- **Database (MySQL)**:
  ```sh
  docker build -t hive-db ./db
  docker run -p 3306:3306 -e MYSQL_ROOT_PASSWORD=password -e MYSQL_USER=hive -e MYSQL_PASSWORD=password -e MYSQL_DATABASE=hive hive-db
  ```
- **Hive App (PHP)**:
  ```sh
  docker build -t hive-app ./apps/hive
  docker run -p 8000:8000 --env-file .env hive-app
  ```

### 3. Running with Docker Compose (Recommended)
This method orchestrates all services together.
```sh
docker-compose up --build -d
```
The application will be accessible at `http://localhost:8000/`.

To shut down all containers:
```sh
docker-compose down
```

## Configuration
The application uses environment variables for configuration. Create a `.env` file in the project root (or rename `.env.example`):
```
AI_URL=http://localhost:5000
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=hive
DB_PASSWORD=password
DB_DATABASE=hive
```

## CI/CD and Quality Assurance
This project implements modern development pipelines using GitHub Actions, automated testing, and static analysis tools.

- **Version Control**: Managed with GitHub
- **CI/CD**: Automated workflows using GitHub Actions
    - Unit tests run on every push
    - Docker images are built and published to [GitHub Packages](https://github.com/HeadTriXz/ITVB23OWS/pkgs/container/ITVB23OWS). To pull the latest image:
        ```sh
        docker pull ghcr.io/HeadTriXz/ITVB23OWS:main
        ```
- **Code Quality**:
    - **Qodana** for static analysis
    - **PSR-1 & PSR-12** compliance for PHP code standards

## Unit Testing
The `hive` application includes unit tests to validate functionality. To run the tests, first move to the `apps/hive` directory:
```sh
cd apps/hive
```
Then, execute PHPUnit:
```sh
php vendor/bin/phpunit
```
Alternatively, they are automatically executed in the GitHub Actions pipeline.

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
