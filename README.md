# Ekahal Assignment

This repository contains the Ekahal Assignment. Below are the steps to clone, set up, and run the application.

## Installation Flow

### 1. Clone the Repository
```bash
git clone https://github.com/PeranoXx/Ekahal-Assignment.git
cd Ekahal-Assignment
```

### 2. Setup and Run the Application

#### Option A: Running with Docker (Recommended)
Ensure Docker is installed and running before executing the setup script.

Run the automated setup script:
```bash
./setup.sh
```
This script handles the environment configuration, PHP/Composer dependency installation, database migrations, and asset compilation within Docker.

Once the script completes, you can access the application at:
**[http://localhost/](http://localhost/)**

#### Option B: Running without Docker
If you prefer not to use Docker, switch to the dedicated branch for a direct host installation:
```bash
git checkout feature/epic-without-docker
```

Once switched, run the following commands to manually set up and start the application:
```bash
cp .env.example .env
composer install
php artisan key:generate --ansi --no-interaction
php artisan migrate --fresh
php artisan db:seed --force
npm install
npm run dev
php artisan serve
```
