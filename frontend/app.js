
fetch('../api/TopDestinationAPI.php')
  .then(res => res.json())
  .then(cards => {
    const container = document.getElementById('top-destinations-cards');
    container.innerHTML = cards.map(card => `
      <div class="six-cards-border">
            <img src="../${card.image_url}">
            <div class="top-destinations-text-overlay">
                <h3>${card.location}: ${card.name}</h3>
            </div>
      </div>
    `).join('');
  })
.catch(err => console.error('Error loading cards:', err));

