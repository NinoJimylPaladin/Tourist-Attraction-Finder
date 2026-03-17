// Authentication utility functions
class AuthManager {
  constructor() {
    this.tokenKey = "auth_token";
    this.userKey = "user_data";
  }

  // Get token from storage
  getToken() {
    return (
      localStorage.getItem(this.tokenKey) ||
      sessionStorage.getItem(this.tokenKey)
    );
  }

  // Save token to storage
  saveToken(token, remember = false) {
    if (remember) {
      localStorage.setItem(this.tokenKey, token);
    } else {
      sessionStorage.setItem(this.tokenKey, token);
    }
  }

  // Remove token from storage
  removeToken() {
    localStorage.removeItem(this.tokenKey);
    sessionStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.userKey);
    sessionStorage.removeItem(this.userKey);
  }

  // Check if user is authenticated
  isAuthenticated() {
    return !!this.getToken();
  }

  // Get user data from storage
  getUser() {
    const userData =
      localStorage.getItem(this.userKey) ||
      sessionStorage.getItem(this.userKey);
    return userData ? JSON.parse(userData) : null;
  }

  // Save user data to storage
  saveUser(user) {
    const storage = localStorage.getItem(this.tokenKey)
      ? localStorage
      : sessionStorage;
    storage.setItem(this.userKey, JSON.stringify(user));
  }

  // Make authenticated API request
  async makeAuthRequest(url, options = {}) {
    const token = this.getToken();

    if (!token) {
      throw new Error("No authentication token found");
    }

    const defaultOptions = {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
        ...options.headers,
      },
    };

    const mergedOptions = {
      ...defaultOptions,
      ...options,
      headers: {
        ...defaultOptions.headers,
        ...options.headers,
      },
    };

    const response = await fetch(url, mergedOptions);

    if (response.status === 401) {
      // Token expired or invalid
      this.removeToken();
      window.location.href = "login.php";
      throw new Error("Authentication required");
    }

    return response;
  }

  // Logout user
  logout() {
    this.removeToken();
    window.location.href = "index.php";
  }

  // Update navigation based on auth status
  updateNavigation() {
    const loginLink = document.querySelector(".signin-button a");
    const navLinks = document.querySelector(".search-home-container ul");

    if (this.isAuthenticated()) {
      const user = this.getUser();
      if (loginLink) {
        loginLink.textContent = user ? `Welcome, ${user.name}` : "Dashboard";
        loginLink.href = "dashboard.php";
      }

      // Add logout button if not exists
      if (!document.querySelector(".logout-btn")) {
        const logoutBtn = document.createElement("a");
        logoutBtn.href = "#";
        logoutBtn.className = "logout-btn";
        logoutBtn.textContent = "Logout";
        logoutBtn.style.marginLeft = "20px";
        logoutBtn.style.border = "#F2994A solid 2px";
        logoutBtn.style.padding = "10px 20px";
        logoutBtn.style.borderRadius = "20px";
        logoutBtn.style.transition = "all 0.3s ease";

        logoutBtn.addEventListener("mouseenter", () => {
          logoutBtn.style.backgroundColor = "#F2C94C";
          logoutBtn.style.color = "#2B662E";
        });

        logoutBtn.addEventListener("mouseleave", () => {
          logoutBtn.style.backgroundColor = "";
          logoutBtn.style.color = "";
        });

        logoutBtn.addEventListener("click", (e) => {
          e.preventDefault();
          this.logout();
        });

        if (loginLink && loginLink.parentNode) {
          loginLink.parentNode.appendChild(logoutBtn);
        }
      }
    } else {
      if (loginLink) {
        loginLink.textContent = "Sign In";
        loginLink.href = "login.php";
      }

      // Remove logout button if exists
      const logoutBtn = document.querySelector(".logout-btn");
      if (logoutBtn) {
        logoutBtn.remove();
      }
    }
  }
}

// Create global auth instance
const auth = new AuthManager();

// Export for use in other scripts
if (typeof module !== "undefined" && module.exports) {
  module.exports = AuthManager;
} else {
  window.auth = auth;
}

// Update navigation on page load
document.addEventListener("DOMContentLoaded", () => {
  auth.updateNavigation();
});
