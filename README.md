# Event Scoring System - LAMP Stack Implementation

A comprehensive event scoring system built with the LAMP stack (Linux, Apache, MySQL, PHP) that provides three main interfaces: an admin panel for judge management, a judge portal for scoring participants, and a public scoreboard with real-time updates.
⚡ Designed as part of the CTFroom technical challenge with extensibility and clarity in mind.

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

Follow these steps to install and run the **Event Scoring System** judge scoring app locally on a LAMP stack using XAMPP, WAMP, or native Apache + MySQL.

---

## ✅ Requirements

- PHP 7.x or higher
- MySQL (5.x or 8.x)
- Apache Web Server
- [XAMPP](https://www.apachefriends.org/) for easy local dev
- (Optional) [Ngrok](https://ngrok.com/) for public access

---

## 📂 Step 1: Clone or Move the Project

Place the `LAMPWebBuilder` project folder into your Apache root directory.

**For XAMPP on Windows:**
```bash
C:\xampp\htdocs\LAMPWebBuilder\


**For Linux Apache:**
```bash
/var/www/html/LAMPWebBuilder/ 
