// Get form and inputs
const form = document.getElementById("signupForm");
const fullnameInput = document.getElementById("fullname");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("confirm");
const agreeCheckbox = document.getElementById("agree");
const submitBtn = document.querySelector(".signup-btn");
const errorMessage = document.querySelector(".error-message");

// Show error message
function showError(message) {
  if (errorMessage) {
    errorMessage.textContent = message;
    errorMessage.style.display = "block";
  } else {
    alert(message);
  }
}

// Hide error message
function hideError() {
  if (errorMessage) {
    errorMessage.style.display = "none";
  }
}

// Validate email format
function isValidEmail(email) {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
}

// Validate password strength
function isStrongPassword(password) {
  // At least 8 characters, one uppercase, one lowercase, one number
  const passwordPattern =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/;
  return passwordPattern.test(password);
}

// Get password strength message
function getPasswordStrengthMessage(password) {
  if (password.length < 8) {
    return "Password must be at least 8 characters long";
  }
  if (!/(?=.*[a-z])/.test(password)) {
    return "Password must contain at least one lowercase letter";
  }
  if (!/(?=.*[A-Z])/.test(password)) {
    return "Password must contain at least one uppercase letter";
  }
  if (!/(?=.*\d)/.test(password)) {
    return "Password must contain at least one number";
  }
  return "Password is strong";
}

form.addEventListener("submit", async function (e) {
  e.preventDefault();
  hideError();

  const name = fullnameInput.value.trim();
  const email = emailInput.value.trim();
  const password = passwordInput.value.trim();
  const confirmPassword = confirmPasswordInput.value.trim();
  const agree = agreeCheckbox.checked;

  // Client-side validation
  if (!name || !email || !password || !confirmPassword) {
    showError("Please fill in all fields.");
    return;
  }

  if (!isValidEmail(email)) {
    showError("Please enter a valid email address.");
    return;
  }

  if (!isStrongPassword(password)) {
    showError(
      "Password must be at least 8 characters with uppercase, lowercase, and number.",
    );
    return;
  }

  if (password !== confirmPassword) {
    showError("Passwords do not match.");
    return;
  }

  if (!agree) {
    showError("You must agree to the Terms and Privacy Policy.");
    return;
  }

  // Disable button and show loading state
  submitBtn.disabled = true;
  submitBtn.textContent = "Creating Account...";
  submitBtn.style.backgroundColor = "#ccc";

  try {
    const response = await fetch("/api/auth/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        name: name,
        email: email,
        password: password,
      }),
    });

    const result = await response.json();

    if (result.success) {
      // Show success message
      showError("Account created successfully! Redirecting to login...");
      errorMessage.style.color = "#4CAF50";

      // Redirect to login after short delay
      setTimeout(() => {
        window.location.href = "../pages/login.php";
      }, 2000);
    } else {
      showError(result.message || "Registration failed. Please try again.");
    }
  } catch (error) {
    console.error("Registration error:", error);
    showError("Network error. Please check your connection and try again.");
  } finally {
    // Re-enable button
    submitBtn.disabled = false;
    submitBtn.textContent = "Sign Up";
    submitBtn.style.backgroundColor = "";
  }
});

// Real-time validation feedback
emailInput.addEventListener("blur", function () {
  if (this.value && !isValidEmail(this.value)) {
    this.style.borderColor = "#F2C94C";
    showError("Please enter a valid email address.");
  } else {
    this.style.borderColor = "";
    hideError();
  }
});

passwordInput.addEventListener("input", function () {
  const password = this.value;
  const confirmPassword = confirmPasswordInput.value;

  if (password && !isStrongPassword(password)) {
    this.style.borderColor = "#F2C94C";
  } else {
    this.style.borderColor = "";
  }

  if (confirmPassword && password !== confirmPassword) {
    confirmPasswordInput.style.borderColor = "#F2C94C";
  } else {
    confirmPasswordInput.style.borderColor = "";
  }
});

confirmPasswordInput.addEventListener("input", function () {
  const password = passwordInput.value;
  const confirmPassword = this.value;

  if (confirmPassword && password !== confirmPassword) {
    this.style.borderColor = "#F2C94C";
    showError("Passwords do not match.");
  } else {
    this.style.borderColor = "";
    hideError();
  }
});

agreeCheckbox.addEventListener("change", function () {
  if (!this.checked) {
    showError("You must agree to the Terms and Privacy Policy.");
  } else {
    hideError();
  }
});
