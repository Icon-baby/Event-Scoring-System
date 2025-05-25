class AdminManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupFormValidation();
        this.setupEventListeners();
    }

    setupFormValidation() {
        const form = document.getElementById('addJudgeForm');
        if (form) {
            form.addEventListener('submit', (e) => this.validateForm(e));
        }
    }

    setupEventListeners() {
        // Real-time validation
        const judgeIdInput = document.getElementById('judge_id');
        const displayNameInput = document.getElementById('display_name');

        if (judgeIdInput) {
            judgeIdInput.addEventListener('input', (e) => this.validateJudgeId(e.target));
        }

        if (displayNameInput) {
            displayNameInput.addEventListener('input', (e) => this.validateDisplayName(e.target));
        }
    }

    validateForm(event) {
        event.preventDefault();
        
        const judgeId = document.getElementById('judge_id').value.trim();
        const displayName = document.getElementById('display_name').value.trim();
        
        let isValid = true;
        let errors = [];

        // Validate Judge ID
        if (!judgeId) {
            errors.push('Judge ID is required');
            isValid = false;
        } else if (judgeId.length < 3) {
            errors.push('Judge ID must be at least 3 characters long');
            isValid = false;
        } else if (!/^[a-zA-Z0-9_-]+$/.test(judgeId)) {
            errors.push('Judge ID can only contain letters, numbers, hyphens, and underscores');
            isValid = false;
        }

        // Validate Display Name
        if (!displayName) {
            errors.push('Display Name is required');
            isValid = false;
        } else if (displayName.length < 2) {
            errors.push('Display Name must be at least 2 characters long');
            isValid = false;
        }

        if (isValid) {
            event.target.submit();
        } else {
            this.showValidationErrors(errors);
        }
    }

    validateJudgeId(input) {
        const value = input.value.trim();
        let feedback = '';
        let isValid = true;

        if (value) {
            if (value.length < 3) {
                feedback = 'Must be at least 3 characters long';
                isValid = false;
            } else if (!/^[a-zA-Z0-9_-]+$/.test(value)) {
                feedback = 'Only letters, numbers, hyphens, and underscores allowed';
                isValid = false;
            } else {
                feedback = 'Valid judge ID';
            }
        }

        this.updateFieldFeedback(input, feedback, isValid);
        return isValid;
    }

    validateDisplayName(input) {
        const value = input.value.trim();
        let feedback = '';
        let isValid = true;

        if (value) {
            if (value.length < 2) {
                feedback = 'Must be at least 2 characters long';
                isValid = false;
            } else {
                feedback = 'Valid display name';
            }
        }

        this.updateFieldFeedback(input, feedback, isValid);
        return isValid;
    }

    updateFieldFeedback(input, message, isValid) {
        // Remove existing feedback
        const existingFeedback = input.parentNode.querySelector('.field-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Add new feedback if there's a message
        if (message) {
            const feedback = document.createElement('div');
            feedback.className = `field-feedback small ${isValid ? 'text-success' : 'text-danger'}`;
            feedback.textContent = message;
            input.parentNode.appendChild(feedback);
        }

        // Update input styling
        input.classList.remove('is-valid', 'is-invalid');
        if (input.value.trim()) {
            input.classList.add(isValid ? 'is-valid' : 'is-invalid');
        }
    }

    showValidationErrors(errors) {
        // Remove existing error alerts
        const existingAlerts = document.querySelectorAll('.validation-error-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new error alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show validation-error-alert';
        
        let errorHTML = '<strong>Please fix the following errors:</strong><ul class="mb-0 mt-2">';
        errors.forEach(error => {
            errorHTML += `<li>${error}</li>`;
        });
        errorHTML += '</ul>';
        errorHTML += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        
        alertDiv.innerHTML = errorHTML;

        // Insert before the form
        const form = document.getElementById('addJudgeForm');
        form.parentNode.insertBefore(alertDiv, form);

        // Scroll to the error
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Method to add judge via AJAX (for future enhancement)
    async addJudgeAjax(judgeData) {
        try {
            const response = await fetch('api/add_judge.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(judgeData)
            });

            const result = await response.json();
            
            if (result.success) {
                this.showSuccessMessage('Judge added successfully!');
                this.resetForm();
                this.refreshJudgesList();
            } else {
                this.showErrorMessage(result.error || 'Failed to add judge');
            }
        } catch (error) {
            console.error('Error adding judge:', error);
            this.showErrorMessage('Network error. Please try again.');
        }
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

    resetForm() {
        const form = document.getElementById('addJudgeForm');
        if (form) {
            form.reset();
            
            // Remove validation classes
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });

            // Remove feedback messages
            const feedbacks = form.querySelectorAll('.field-feedback');
            feedbacks.forEach(feedback => feedback.remove());
        }
    }

    async refreshJudgesList() {
        // This would reload the judges list via AJAX in a full implementation
        // For now, we'll just reload the page
        location.reload();
    }
}

// Initialize admin manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AdminManager();
});
