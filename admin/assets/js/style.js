// Login Form Handling
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    // Handle form submission
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form values
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            // Basic validation
            if (!validateForm(username, email, password)) {
                return;
            }
            
            // Simulate login process
            performLogin(username, email, password);
        });
    }
});

// Form validation function
function validateForm(username, email, password) {
    // Validate username
    if (username === '') {
        showError('username', 'Please enter your username');
        return false;
    }
    
    // Validate email
    if (email === '') {
        showError('email', 'Please enter your email');
        return false;
    }
    
    if (!isValidEmail(email)) {
        showError('email', 'Please enter a valid email address');
        return false;
    }
    
    // Validate password
    if (password === '') {
        showError('password', 'Please enter your password');
        return false;
    }
    
    if (password.length < 6) {
        showError('password', 'Password must be at least 6 characters');
        return false;
    }
    
    return true;
}

// Email validation helper
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Show error message
function showError(fieldId, message) {
    const inputGroup = document.getElementById(fieldId).parentElement;
    
    // Remove existing error
    const existingError = inputGroup.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error class
    inputGroup.style.borderColor = '#e74c3c';
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#e74c3c';
    errorDiv.style.fontSize = '12px';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    
    inputGroup.appendChild(errorDiv);
    
    // Remove error on input
    document.getElementById(fieldId).addEventListener('input', function() {
        inputGroup.style.borderColor = '#ccc';
        const error = inputGroup.querySelector('.error-message');
        if (error) {
            error.remove();
        }
    });
}

// Perform login (simulated)
function performLogin(username, email, password) {
    const signInBtn = document.querySelector('.sign-in-btn');
    const originalText = signInBtn.textContent;
    
    // Show loading state
    signInBtn.textContent = 'Signing in...';
    signInBtn.disabled = true;
    signInBtn.style.opacity = '0.7';
    
    // Simulate API call (replace with actual API endpoint)
    setTimeout(function() {
        // For demonstration purposes
        console.log('Login attempt:', { username, email, password });
        
        // Show success message (in real app, redirect to dashboard)
        alert('Login successful! Welcome, ' + username);
        
        // Reset button state
        signInBtn.textContent = originalText;
        signInBtn.disabled = false;
        signInBtn.style.opacity = '1';
        
        // Clear form
        document.getElementById('loginForm').reset();
    }, 1500);
}

// Forgot Password function
function forgotPassword() {
    const email = prompt('Please enter your email address to reset your password:');
    
    if (email) {
        if (!isValidEmail(email)) {
            alert('Please enter a valid email address.');
            return;
        }
        
        // Simulate password reset request
        alert('Password reset link has been sent to ' + email);
        
        // In a real application, you would make an API call here:
        // fetch('/api/forgot-password', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ email })
        // });
    }
}

// Add smooth focus transitions for inputs
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.boxShadow = '0 0 0 2px rgba(76, 175, 80, 0.3)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.boxShadow = 'none';
        });
    });
});

