# Event Scoring System - LAMP Stack Implementation

A comprehensive event scoring system built with the LAMP stack (Linux, Apache, MySQL, PHP) that provides three main interfaces: an admin panel for judge management, a judge portal for scoring participants, and a public scoreboard with real-time updates.

## Features

### 🔧 Admin Panel
- Add new judges with unique IDs and display names
- View all registered judges
- View all event participants
- Form validation and error handling

### ⚖️ Judge Portal
- Select judge identity from dropdown
- View all participants
- Assign scores (1-100) to participants
- Update existing scores
- Real-time validation

### 📊 Public Scoreboard
- Real-time display of participant rankings
- Auto-refresh functionality (every 10 seconds)
- Total scores and averages
- Ranking with tie handling
- Medal indicators for top 3 positions
- Responsive design

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **CSS Framework**: Bootstrap 5.1.3
- **Web Server**: Apache 2.4+

## Installation & Setup

### Prerequisites
- LAMP stack installed (Linux, Apache, MySQL, PHP)
- PHP extensions: PDO, pdo_mysql
- MySQL/MariaDB database server

### Step 1: Clone/Download Files
```bash
# Place all files in your web root directory (e.g., /var/www/html/)
# Ensure proper file permissions
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
