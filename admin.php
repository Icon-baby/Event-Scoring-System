<?php
require_once 'includes/functions.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judge_id = sanitizeInput($_POST['judge_id'] ?? '');
    $display_name = sanitizeInput($_POST['display_name'] ?? '');
    
    if (empty($judge_id) || empty($display_name)) {
        $message = 'Both Judge ID and Display Name are required.';
        $messageType = 'danger';
    } elseif (judgeExists($judge_id)) {
        $message = 'A judge with this ID already exists.';
        $messageType = 'danger';
    } else {
        if (addJudge($judge_id, $display_name)) {
            $message = 'Judge added successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to add judge. Please try again.';
            $messageType = 'danger';
        }
    }
}

$judges = getAllJudges();
$users = getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Event Scoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Event Scoring System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="admin.php">Admin Panel</a>
                <a class="nav-link" href="judge.php">Judge Portal</a>
                <a class="nav-link" href="scoreboard.php">Scoreboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Admin Panel</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add New Judge</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="addJudgeForm">
                            <div class="mb-3">
                                <label for="judge_id" class="form-label">Judge ID *</label>
                                <input type="text" class="form-control" id="judge_id" name="judge_id" required 
                                       placeholder="e.g., judge001">
                                <div class="form-text">Must be unique identifier</div>
                            </div>
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name *</label>
                                <input type="text" class="form-control" id="display_name" name="display_name" required
                                       placeholder="e.g., John Smith">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Judge</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Current Judges (<?= count($judges) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($judges)): ?>
                            <p class="text-muted">No judges added yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Judge ID</th>
                                            <th>Display Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($judges as $judge): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($judge['judge_id']) ?></td>
                                                <td><?= htmlspecialchars($judge['display_name']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Registered Participants (<?= count($users) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($users)): ?>
                            <p class="text-muted">No participants registered yet. Sample participants will be created automatically.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['id']) ?></td>
                                                <td><?= htmlspecialchars($user['name']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>
