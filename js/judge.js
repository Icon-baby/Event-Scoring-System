class JudgeManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupFormValidation();
        this.setupEventListeners();
    }

    setupFormValidation() {
        const scoreForms = document.querySelectorAll('.score-form');
        scoreForms.forEach(form => {
            form.addEventListener('submit', (e) => this.validateScoreForm(e));
        });
    }

    setupEventListeners() {
        // Real-time validation for score inputs
        const scoreInputs = document.querySelectorAll('input[name="score"]');
        scoreInputs.forEach(input => {
            input.addEventListener('input', (e) => this.validateScoreInput(e.target));
            input.addEventListener('blur', (e) => this.validateScoreInput(e.target));
        });

        // Judge selection change
        const judgeSelect = document.querySelector('select[name="judge_id"]');
        if (judgeSelect) {
            judgeSelect.addEventListener('change', () => this.onJudgeChange());
        }
    }

    validateScoreForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const scoreInput = form.querySelector('input[name="score"]');
        const score = parseInt(scoreInput.value);
        
        let isValid = true;
        let errors = [];

        // Validate score
        if (isNaN(score)) {
            errors.push('Score must be a number');
            isValid = false;
        } else if (score < 1 || score > 100) {
            errors.push('Score must be between 1 and 100');
            isValid = false;
        }

        if (isValid) {
            this.submitScore(form);
        } else {
            this.showValidationErrors(errors, form);
            scoreInput.focus();
        }
    }

    validateScoreInput(input) {
        const value = parseInt(input.value);
        let feedback = '';
        let isValid = true;

        if (input.value) {
            if (isNaN(value)) {
                feedback = 'Must be a number';
                isValid = false;
            } else if (value < 1 || value > 100) {
                feedback = 'Must be between 1-100';
                isValid = false;
            } else {
                feedback = 'Valid score';
            }
        }

        this.updateFieldFeedback(input, feedback, isValid);
        return isValid;
    }

    updateFieldFeedback(input, message, isValid) {
        // Remove existing feedback
        const existingFeedback = input.parentNode.querySelector('.score-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Add new feedback if there's a message
        if (message) {
            const feedback = document.createElement('div');
            feedback.className = `score-feedback small ${isValid ? 'text-success' : 'text-danger'}`;
            feedback.textContent = message;
            input.parentNode.appendChild(feedback);
        }

        // Update input styling
        input.classList.remove('is-valid', 'is-invalid');
        if (input.value.trim()) {
            input.classList.add(isValid ? 'is-valid' : 'is-invalid');
        }
    }

    submitScore(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';
        
        // Submit the form
        form.submit();
    }

    async submitScoreAjax(formData) {
        try {
            const response = await fetch('api/add_score.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            
            if (result.success) {
                this.showSuccessMessage('Score submitted successfully!');
                this.updateScoreDisplay(formData);
            } else {
                this.showErrorMessage(result.error || 'Failed to submit score');
            }
        } catch (error) {
            console.error('Error submitting score:', error);
            this.showErrorMessage('Network error. Please try again.');
        }
    }

    updateScoreDisplay(formData) {
        // Update the current score badge for the user
        const userRow = document.querySelector(`input[name="user_id"][value="${formData.user_id}"]`).closest('tr');
        const currentScoreCell = userRow.querySelector('td:nth-child(2)');
        
        currentScoreCell.innerHTML = `<span class="badge bg-info">${formData.score}</span>`;
    }

    onJudgeChange() {
        // Clear any existing validation states when judge changes
        const scoreInputs = document.querySelectorAll('input[name="score"]');
        scoreInputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
            const feedback = input.parentNode.querySelector('.score-feedback');
            if (feedback) {
                feedback.remove();
            }
        });
    }

    showValidationErrors(errors, form) {
        // Remove existing error alerts for this form
        const existingAlert = form.querySelector('.validation-error-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create new error alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-sm validation-error-alert mt-2';
        
        let errorHTML = '<strong>Error:</strong> ';
        errorHTML += errors.join(', ');
        
        alertDiv.innerHTML = errorHTML;

        // Insert after the form
        form.parentNode.insertBefore(alertDiv, form.nextSibling);

        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3000);
    }

    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    showErrorMessage(message) {
        this.showMessage(message, 'danger');
    }

    showMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.container');
        const firstChild = container.firstElementChild;
        container.insertBefore(alertDiv, firstChild.nextSibling);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Helper method to get current judge scores
    async refreshJudgeScores(judgeId) {
        try {
            const response = await fetch(`api/get_judge_scores.php?judge_id=${encodeURIComponent(judgeId)}`);
            const result = await response.json();
            
            if (result.success) {
                this.updateJudgeScoresDisplay(result.data);
            }
        } catch (error) {
            console.error('Error refreshing judge scores:', error);
        }
    }

    updateJudgeScoresDisplay(scores) {
        scores.forEach(score => {
            const userRow = document.querySelector(`input[name="user_id"][value="${score.user_id}"]`).closest('tr');
            if (userRow) {
                const currentScoreCell = userRow.querySelector('td:nth-child(2)');
                const scoreInput = userRow.querySelector('input[name="score"]');
                
                if (score.score !== null) {
                    currentScoreCell.innerHTML = `<span class="badge bg-info">${score.score}</span>`;
                    scoreInput.value = score.score;
                } else {
                    currentScoreCell.innerHTML = '<span class="text-muted">Not scored</span>';
                    scoreInput.value = '';
                }
            }
        });
    }
}

// Initialize judge manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new JudgeManager();
});
