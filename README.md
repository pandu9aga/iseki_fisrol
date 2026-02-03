# Iseki Fisrol - Patrol & Finding Management System

## Overview

**Iseki Fisrol** is a comprehensive management system designed to streamline security and facility patrols. It allows for scheduling patrols, managing members, tracking findings ("Temuan"), and generating detailed reports including exports to PowerPoint (PPT) and Excel.

The application features a robust role-based access control system to distinguish between administrative management and user-level patrol operations.

## Key Features

### 1. Admin Module
*   **Dashboard**: Comprehensive overview of patrol activities and key metrics.
*   **User & Member Management**:
    *   Full CRUD for system users.
    *   Member management with support for bulk import.
*   **Patrol Management**:
    *   Schedule and manage patrol shifts and routes.
    *   Assign members to specific patrols.
*   **Finding (Temuan) Management**:
    *   Review findings reported during patrols.
    *   Update status and details of findings.
    *   **Export to PPT**: Generate formatted PowerPoint presentations for findings to facilitate management reviews.
*   **Performance Monitoring**:
    *   Track scoring ("Nilai") for patrols.
    *   Calculate and view averages for patrol performance.

### 2. User Module
*   **User Dashboard**: Personalized view of assigned patrols and tasks.
*   **Patrol Participation**: Access and participate in assigned patrols.
*   **Reporting Findings**: report issues or observations directly during the patrol.
*   **Scoring**: Ability to provide scores or ratings based on patrol outcomes.

## Technology Stack

### Backend
*   **Framework**: [Laravel 12.x](https://laravel.com)
*   **Language**: PHP ^8.2
*   **Database**: SQLite (Default) / MySQL Compatible
*   **Document Generation**:
    *   `phpoffice/phppresentation`: Used for generating findings reports in PowerPoint format.
    *   `phpoffice/phpspreadsheet`: Used for Excel exports and imports.

### Frontend
*   **Build Tool**: [Vite](https://vitejs.dev)
*   **Styling**: [Tailwind CSS v4.0](https://tailwindcss.com)
*   **HTTP Client**: Axios

## Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone <repository-url>
    cd iseki_fisrol
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Setup**
    *   Copy the `.env.example` file:
        ```bash
        cp .env.example .env
        ```
    *   Configure your database and app settings in `.env`.

4.  **Database Migration & Key Generation**
    ```bash
    php artisan key:generate
    php artisan migrate
    ```

5.  **Build Assets**
    ```bash
    npm run build
    # or for development:
    npm run dev
    ```

6.  **Run the Server**
    ```bash
    php artisan serve
    ```
    Access the application at `http://localhost:8000`.

## Reporting Features

The system supports advanced reporting:
- **Temuan Export**: Admins can export findings for a specific patrol directly to a `.pptx` file.
- **Member Import**: Quick setup using Excel-based member lists.

## License

This project is proprietary.
