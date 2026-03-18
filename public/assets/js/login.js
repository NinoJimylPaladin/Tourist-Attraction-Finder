// Get form elements
const form = document.querySelector("form");
const emailInput = document.querySelector('input[type="text"]');
const passwordInput = document.querySelector('input[type="password"]');
const rememberCheckbox = document.querySelector('input[type="checkbox"]');
const submitBtn = document.querySelector(".login-btn");
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

// Save token and user data using auth utility
function saveAuthData(result, remember) {
  // Save token using auth utility
  auth.saveToken(result.data.token, remember);

  // Save user data
  auth.saveUser(result.data.user);
}

form.addEventListener("submit", async function (event) {
  event.preventDefault();
  hideError();

  const email = emailInput.value.trim();
  const password = passwordInput.value.trim();
  const remember = rememberCheckbox.checked;

  // Basic validation
  if (!email || !password) {
    showError("Please enter your email and password.");
    return;
  }

  // Disable button and show loading state
  submitBtn.disabled = true;
  submitBtn.textContent = "Signing In...";
  submitBtn.style.backgroundColor = "#ccc";

  try {
    const response = await fetch("/api/auth/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: email,
        password: password,
      }),
    });

    const result = await response.json();

    if (result.success) {
      // Save token and user data
      saveAuthData(result, remember);

      // Show success message
      showError("Login successful! Redirecting...");
      if (errorMessage) {
        errorMessage.style.color = "#4CAF50";
      }

      // Redirect after short delay
      setTimeout(() => {
        window.location.href = "index.php";
      }, 1000);
    } else {
      showError(
        result.message || "Login failed. Please check your credentials.",
      );
    }
  } catch (error) {
    console.error("Login error:", error);
    showError("Network error. Please check your connection and try again.");
  } finally {
    // Re-enable button
    submitBtn.disabled = false;
    submitBtn.textContent = "Sign in";
    submitBtn.style.backgroundColor = "";
  }
});

// Check if user is already logged in
function checkAuthStatus() {
  const token =
    localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
  if (token) {
    // Token exists, redirect to dashboard
    window.location.href = "index.php";
  }
}

// Run auth check on page load
checkAuthStatus();
