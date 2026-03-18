// Fetch top-rated attractions for the landing page
async function loadTopAttractions() {
  try {
    const response = await fetch("/api/attractions/top-rated?limit=6");
    const result = await response.json();

    if (result.success && result.data) {
      const attractions = result.data;
      renderTopDestinations(attractions);
      renderThreeCards(attractions.slice(0, 3));
    } else {
      console.error("Failed to load attractions:", result.message);
      showErrorMessage("Failed to load attractions. Please try again later.");
    }
  } catch (error) {
    console.error("Error loading attractions:", error);
    showErrorMessage("Network error. Please check your connection.");
  }
}

function renderTopDestinations(attractions) {
  const container = document.getElementById("top-destinations-cards");
  if (!container) return;

  container.innerHTML = attractions
    .map(
      (attraction) => `
        <div class="six-cards-border">
            <img src="/${attraction.image_url}" alt="${attraction.name}" onerror="this.onerror=null; this.src='/assets/img/falls.png'; this.style.opacity='0.5';">
            <div class="top-destinations-text-overlay">
                <h3>${attraction.location}: ${attraction.name}</h3>
            </div>
        </div>
    `,
    )
    .join("");
}

function renderThreeCards(attractions) {
  const container = document.getElementById("three-cards");
  if (!container) return;

  container.innerHTML = attractions
    .map(
      (attraction) => `
        <div class="three-cards-border">
            <img src="/${attraction.image_url}" alt="${attraction.name}" onerror="this.onerror=null; this.src='/assets/img/falls.png'; this.style.opacity='0.5';">
        </div>
    `,
    )
    .join("");
}

function showErrorMessage(message) {
  // Create error message element
  const errorDiv = document.createElement("div");
  errorDiv.className = "error-message";
  errorDiv.innerHTML = `
        <div style="text-align: center; padding: 20px; color: #F2C94C; background: rgba(0,0,0,0.8); border: 2px solid #F2C94C; border-radius: 10px; margin: 20px;">
            <h3>⚠️ ${message}</h3>
            <p style="margin-top: 10px; opacity: 0.8;">The page will continue to work with placeholder content.</p>
        </div>
    `;

  // Insert after the top destinations section
  const topDestinationsSection = document.querySelector(
    ".top-destinations-container",
  );
  if (topDestinationsSection) {
    topDestinationsSection.parentNode.insertBefore(
      errorDiv,
      topDestinationsSection.nextSibling,
    );
  }
}

// Load attractions when the page loads
document.addEventListener("DOMContentLoaded", loadTopAttractions);
