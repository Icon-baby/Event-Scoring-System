<?php
require_once 'includes/functions.php';

$selectedJudge = $_GET['judge_id'] ?? '';
$message = '';
$messageType = '';

// Handle score submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judge_id = sanitizeInput($_POST['judge_id'] ?? '');
    $user_id = intval($_POST['user_id'] ?? 0);
    $score = intval($_POST['score'] ?? 0);
    
    if (empty($judge_id) || $user_id <= 0 || $score < 1 || $score > 100) {
        $message = 'Invalid input. Score must be between 1-100.';
        $messageType = 'danger';
    } elseif (!judgeExists($judge_id)) {
        $message = 'Invalid judge ID: ' . $judge_id . '. Please make sure the judge exists in the system.';
        $messageType = 'danger';
    } else {
        if (addScore($judge_id, $user_id, $score)) {
            $message = 'Score submitted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to submit score. Please try again.';
            $messageType = 'danger';
        }
    }
}

$judges = getAllJudges();
$users = getAllUsers();
$judgeScores = [];

if ($selectedJudge && judgeExists($selectedJudge)) {
    $judgeScores = getJudgeScores($selectedJudge);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Portal - Event Scoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="index.php">Event Scoring System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin.php">Admin Panel</a>
                <a class="nav-link active" href="judge.php">Judge Portal</a>
                <a class="nav-link" href="scoreboard.php">Scoreboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Judge Portal</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($judges)): ?>
            <div class="alert alert-warning">
                <h5>No Judges Available</h5>
                <p>Please contact the administrator to add judges to the system.</p>
                <a href="admin.php" class="btn btn-primary">Go to Admin Panel</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Select Judge</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <div class="mb-3">
                                    <select class="form-select" name="judge_id" onchange="this.form.submit()">
                                        <option value="">Choose Judge...</option>
                                        <?php foreach ($judges as $judge): ?>
                                            <option value="<?= htmlspecialchars($judge['judge_id']) ?>" 
                                                    <?= $selectedJudge === $judge['judge_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($judge['display_name']) ?> (<?= htmlspecialchars($judge['judge_id']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <?php if ($selectedJudge): ?>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Score Participants</h5>
                                <small class="text-muted">Scoring as: <?= htmlspecialchars($selectedJudge) ?></small>
                            </div>
                            <div class="card-body">
                                <?php if (empty($users)): ?>
                                    <p class="text-muted">No participants to score.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Participant</th>
                                                    <th>Current Score</th>
                                                    <th>New Score (1-100)</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $scoreMap = [];
                                                foreach ($judgeScores as $judgeScore) {
                                                    $scoreMap[$judgeScore['id']] = $judgeScore['score'];
                                                }
                                                
                                                foreach ($users as $user): 
                                                    $currentScore = $scoreMap[$user['id']] ?? null;
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= htmlspecialchars($user['name']) ?></strong><br>
                                                            <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                                        </td>
                                                        <td>
                                                            <?php if ($currentScore !== null): ?>
                                                                <span class="badge bg-info"><?= $currentScore ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted">Not scored</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <form method="POST" class="d-flex score-form">
                                                                <input type="hidden" name="judge_id" value="<?= htmlspecialchars($selectedJudge) ?>">
                                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                                <input type="number" class="form-control form-control-sm me-2" 
                                                                       name="score" min="1" max="100" 
                                                                       value="<?= $currentScore ?>" 
                                                                       style="width: 100px;" required>
                                                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/judge.js"></script>
</body>
</html>
