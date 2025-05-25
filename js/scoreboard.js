class ScoreboardManager {
    constructor() {
        this.autoRefresh = true;
        this.refreshInterval = null;
        this.refreshRate = 10000; // 10 seconds
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startAutoRefresh();
        this.updateTimestamp();
    }

    setupEventListeners() {
        const toggleButton = document.getElementById('toggleRefresh');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => this.toggleAutoRefresh());
        }
    }

    startAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }

        this.refreshInterval = setInterval(() => {
            if (this.autoRefresh) {
                this.refreshScoreboard();
            }
        }, this.refreshRate);
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    toggleAutoRefresh() {
        this.autoRefresh = !this.autoRefresh;
        this.updateRefreshUI();
        
        if (this.autoRefresh) {
            this.startAutoRefresh();
            this.refreshScoreboard();
        } else {
            this.stopAutoRefresh();
        }
    }

    updateRefreshUI() {
        const statusElement = document.getElementById('refreshStatus');
        const buttonTextElement = document.getElementById('refreshButtonText');
        
        if (statusElement) {
            statusElement.textContent = `Auto-refresh: ${this.autoRefresh ? 'ON' : 'OFF'}`;
            statusElement.className = `badge ${this.autoRefresh ? 'bg-success' : 'bg-secondary'}`;
        }
        
        if (buttonTextElement) {
            buttonTextElement.textContent = this.autoRefresh ? 'Pause' : 'Resume';
        }
    }

    async refreshScoreboard() {
        try {
            const content = document.getElementById('scoreboardContent');
            if (content) {
                content.classList.add('loading');
            }

            const response = await fetch('api/get_scores.php');
            if (!response.ok) {
                throw new Error('Failed to fetch scores');
            }

            const data = await response.json();
            if (data.success) {
                this.updateScoreboardContent(data.data);
                this.updateTimestamp();
            }
        } catch (error) {
            console.error('Error refreshing scoreboard:', error);
            this.showError('Failed to refresh scoreboard. Please check your connection.');
        } finally {
            const content = document.getElementById('scoreboardContent');
            if (content) {
                content.classList.remove('loading');
            }
        }
    }

    updateScoreboardContent(scoreboardData) {
        const content = document.getElementById('scoreboardContent');
        if (!content) return;

        if (scoreboardData.length === 0) {
            content.innerHTML = `
                <div class="alert alert-info text-center">
                    <h4>No Scores Available</h4>
                    <p>Scores will appear here once judges start evaluating participants.</p>
                </div>
            `;
            return;
        }

        let tableHTML = `
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
        `;

        let prevScore = null;
        let actualRank = 1;

        scoreboardData.forEach((participant, index) => {
            // Handle ties in ranking
            if (prevScore !== null && participant.total_score < prevScore) {
                actualRank = index + 1;
            }
            prevScore = participant.total_score;

            const average = participant.score_count > 0 ? 
                          (participant.total_score / participant.score_count).toFixed(1) : '0.0';

            // Highlight top 3
            let rowClass = '';
            if (actualRank === 1) rowClass = 'table-warning'; // Gold
            else if (actualRank === 2) rowClass = 'table-secondary'; // Silver
            else if (actualRank === 3) rowClass = 'table-info'; // Bronze

            let rankBadge = '';
            if (actualRank <= 3) {
                const medals = ['🥇', '🥈', '🥉'];
                rankBadge = `<div class="rank-badge">${medals[actualRank - 1]}</div>`;
            }

            tableHTML += `
                <tr class="${rowClass}">
                    <td class="text-center">
                        <strong class="rank-number">${actualRank}</strong>
                        ${rankBadge}
                    </td>
                    <td>
                        <div class="participant-name">
                            ${this.escapeHtml(participant.name)}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary fs-6 score-badge">
                            ${participant.total_score}
                        </span>
                    </td>
                    <td class="text-center">
                        <small class="text-muted">
                            ${participant.score_count} judge${participant.score_count !== 1 ? 's' : ''}
                        </small>
                    </td>
                    <td class="text-center">
                        <small class="text-muted">
                            ${average}
                        </small>
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;

        content.innerHTML = tableHTML;
    }

    updateTimestamp() {
        const timestampElement = document.getElementById('lastUpdated');
        if (timestampElement) {
            const now = new Date();
            timestampElement.textContent = now.toLocaleTimeString();
        }
    }

    showError(message) {
        const content = document.getElementById('scoreboardContent');
        if (content) {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <h5>Error</h5>
                    <p>${message}</p>
                    <button class="btn btn-primary" onclick="location.reload()">Reload Page</button>
                </div>
            `;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize scoreboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ScoreboardManager();
});

// Handle visibility change to pause refresh when tab is not visible
document.addEventListener('visibilitychange', () => {
    if (window.scoreboardManager) {
        if (document.hidden) {
            window.scoreboardManager.autoRefresh = false;
            window.scoreboardManager.updateRefreshUI();
        } else {
            window.scoreboardManager.autoRefresh = true;
            window.scoreboardManager.updateRefreshUI();
            window.scoreboardManager.refreshScoreboard();
        }
    }
});
