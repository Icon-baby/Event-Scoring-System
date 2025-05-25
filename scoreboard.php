<?php
require_once 'includes/functions.php';

$scoreboard = getScoreboard();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard - Event Scoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="index.php">Event Scoring System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin.php">Admin Panel</a>
                <a class="nav-link" href="judge.php">Judge Portal</a>
                <a class="nav-link active" href="scoreboard.php">Scoreboard</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="display-4">Live Scoreboard</h1>
                    <div class="text-end">
                        <div class="auto-refresh-indicator">
                            <span class="badge bg-success" id="refreshStatus">Auto-refresh: ON</span>
                            <button class="btn btn-outline-secondary btn-sm ms-2" id="toggleRefresh">
                                <span id="refreshButtonText">Pause</span>
                            </button>
                        </div>
                        <div class="small text-muted mt-1">
                            Last updated: <span id="lastUpdated"><?= date('H:i:s') ?></span>
                        </div>
                    </div>
                </div>

                <div id="scoreboardContent">
                    <?php if (empty($scoreboard)): ?>
                        <div class="alert alert-info text-center">
                            <h4>No Scores Available</h4>
                            <p>Scores will appear here once judges start evaluating participants.</p>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0 scoreboard-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col" class="text-center">#</th>
                                                <th scope="col">Participant</th>
                                                <th scope="col" class="text-center">Total Score</th>
                                                <th scope="col" class="text-center">Judges Count</th>
                                                <th scope="col" class="text-center">Average</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            $prevScore = null;
                                            $actualRank = 1;
                                            
                                            foreach ($scoreboard as $index => $participant): 
                                                // Handle ties in ranking
                                                if ($prevScore !== null && $participant['total_score'] < $prevScore) {
                                                    $actualRank = $index + 1;
                                                }
                                                $prevScore = $participant['total_score'];
                                                
                                                $average = $participant['score_count'] > 0 ? 
                                                          round($participant['total_score'] / $participant['score_count'], 1) : 0;
                                                
                                                // Highlight top 3
                                                $rowClass = '';
                                                if ($actualRank === 1) $rowClass = 'table-warning'; // Gold
                                                elseif ($actualRank === 2) $rowClass = 'table-secondary'; // Silver
                                                elseif ($actualRank === 3) $rowClass = 'table-info'; // Bronze
                                            ?>
                                                <tr class="<?= $rowClass ?>">
                                                    <td class="text-center">
                                                        <strong class="rank-number"><?= $actualRank ?></strong>
                                                        <?php if ($actualRank <= 3): ?>
                                                            <div class="rank-badge">
                                                                <?php if ($actualRank === 1): ?>
                                                                    🥇
                                                                <?php elseif ($actualRank === 2): ?>
                                                                    🥈
                                                                <?php else: ?>
                                                                    🥉
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="participant-name">
                                                            <?= htmlspecialchars($participant['name']) ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary fs-6 score-badge">
                                                            <?= $participant['total_score'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <small class="text-muted">
                                                            <?= $participant['score_count'] ?> judge<?= $participant['score_count'] !== 1 ? 's' : '' ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-center">
                                                        <small class="text-muted">
                                                            <?= $average ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scoreboard.js"></script>
</body>
</html>
