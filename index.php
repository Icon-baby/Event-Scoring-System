<?php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Scoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Event Scoring System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin.php">Admin Panel</a>
                <a class="nav-link" href="judge.php">Judge Portal</a>
                <a class="nav-link" href="scoreboard.php">Scoreboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 mb-4">Welcome to Event Scoring System</h1>
                <p class="lead mb-5">A comprehensive LAMP stack solution for event management and scoring</p>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">Admin Panel</h5>
                                <p class="card-text">Manage judges and event participants</p>
                                <a href="admin.php" class="btn btn-primary">Access Admin</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">Judge Portal</h5>
                                <p class="card-text">Score participants and submit evaluations</p>
                                <a href="judge.php" class="btn btn-success">Judge Portal</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">Public Scoreboard</h5>
                                <p class="card-text">View real-time scores and rankings</p>
                                <a href="scoreboard.php" class="btn btn-info">View Scoreboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
