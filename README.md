# Ekahal Assignment

This repository contains the Ekahal Assignment. Below are the steps to clone, set up, and run the application.

> [!NOTE]
> **UI Design**
>
> The user interface for this project was generated using Google Stitch, an AI-powered UI design tool that helps create modern and structured application interfaces.
>
> * **Google Stitch**: [https://stitch.withgoogle.com/](https://stitch.withgoogle.com/?utm_source=chatgpt.com)
> * **Ekahal Assignment UI Design**: [View the Ekahal Assignment UI on Google Stitch](https://stitch.withgoogle.com/projects/14963736868981363540?utm_source=chatgpt.com)
>
> The Google Stitch design was used as the visual foundation for the project, helping define the overall layout, user interface structure, and design direction before implementing the frontend.

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

#### Default Credentials

**Admin User:**
- **Email:** admin@example.com
- **Password:** password

**Standard User:**
- **Email:** test@example.com
- **Password:** password

#### Option B: Running without Docker
If you prefer not to use Docker, switch to the dedicated branch for a direct host installation:
```bash
git checkout feature/epic-without-docker
```

Once switched, execute the following commands to manually configure, install, and run the application:

1. **Configure Environment File**
   Copy the example environment configuration file to create your environment configuration:
   ```bash
   cp .env.example .env
   ```

2. **Install PHP Dependencies**
   Install project dependencies using Composer:
   ```bash
   composer install
   ```

3. **Generate Application Key**
   Generate a unique secure application key:
   ```bash
   php artisan key:generate --ansi --no-interaction
   ```

4. **Run Database Migrations and Seed**
   Freshly migrate the database schema and populate it with seed data:
   ```bash
   php artisan migrate:fresh
   php artisan db:seed --force
   ```

5. **Build and Serve Frontend Assets**
   Install node dependencies and launch the Vite development server:
   ```bash
   npm install
   npm run build
   ```

6. **Launch local server**
   Start the PHP development server in a separate terminal:
   ```bash
   php artisan serve
   ```
