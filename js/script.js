// js/script.js
document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('search-form');
    const addFavoriteForm = document.getElementById('add-favorite-form');
    const weatherData = document.getElementById('weather-data');
    const favoritesContainer = document.getElementById('favorites-container');

    // Validation du formulaire de recherche
    if (searchForm) {
        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const locationInput = document.getElementById('location');
            if (!locationInput.value.trim()) {
                showError(locationInput, 'This field is required');
                return;
            }
            await fetchWeatherData(locationInput.value);
        });
    }

    // Validation du formulaire d'ajout de favoris
    if (addFavoriteForm) {
        addFavoriteForm.addEventListener('submit', (e) => {
            const locationInput = document.getElementById('favorite-location');
            if (!locationInput.value.trim()) {
                e.preventDefault();
                showError(locationInput, 'This field is required');
            }
        });
    }

    // Gestion des boutons "Voir" des favoris
    if (favoritesContainer) {
        favoritesContainer.addEventListener('click', async (e) => {
            if (e.target.classList.contains('view-btn')) {
                const listItem = e.target.closest('li');
                const location = listItem.getAttribute('data-location');
                if (location) {
                    await fetchWeatherData(location);
                }
            }
        });
    }

    // Afficher les erreurs de validation
    function showError(inputElement, message) {
        const existingError = inputElement.parentElement.querySelector('.input-error');
        if (existingError) existingError.remove();

        const errorElement = document.createElement('div');
        errorElement.className = 'input-error';
        errorElement.style.color = 'var(--error-color)';
        errorElement.style.fontSize = '0.9rem';
        errorElement.style.marginTop = '8px';
        errorElement.textContent = message;
        inputElement.parentElement.appendChild(errorElement);
        inputElement.style.border = '1px solid var(--error-color)';

        inputElement.addEventListener('input', function handler() {
            if (this.value.trim()) {
                const error = this.parentElement.querySelector('.input-error');
                if (error) error.remove();
                this.style.border = '';
            }
            this.removeEventListener('input', handler);
        }, { once: true });
    }

    // Récupérer les données météo via Fetch
    async function fetchWeatherData(location) {
        weatherData.innerHTML = `
            <div class="loading" role="status">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-spin">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                </svg>
                <span>Loading...</span>
            </div>
        `;

        try {
            const response = await fetch(`search.php?location=${encodeURIComponent(location)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            if (data.success) {
                displayWeatherData(data.data);
            } else {
                weatherData.innerHTML = `<p class="error">${data.message}</p>`;
            }
        } catch (error) {
            weatherData.innerHTML = `<p class="error">Error fetching weather data: ${error.message}</p>`;
            console.error('Fetch error:', error);
        }
    }

    // Afficher les données météo
    function displayWeatherData(data) {
        const weatherIcons = {
            sunny: '☀️',
            cloudy: '☁️',
            rainy: '🌧️',
            snowy: '❄️',
            windy: '💨'
        };

        const html = `
            <div class="weather-display">
                <h3>${data.location}</h3>
                <div class="weather-icon">${weatherIcons[data.weather_type] || '🌍'}</div>
                <div class="temperature">${data.temperature}°C</div>
                <div class="details">
                    <div class="detail-item">
                        <p><strong>Humidity</strong></p>
                        <p>${data.humidity}%</p>
                    </div>
                    <div class="detail-item">
                        <p><strong>Wind</strong></p>
                        <p>${data.wind_speed} km/h</p>
                    </div>
                    <div class="detail-item">
                        <p><strong>Condition</strong></p>
                        <p>${data.weather_type}</p>
                    </div>
                </div>
                <form id="add-to-favorites" action="favorites.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="location" value="${data.location}">
                    <input type="hidden" name="csrf_token" value="${document.querySelector('input[name="csrf_token"]').value}">
                    <button type="submit">Add to Favorites</button>
                </form>
            </div>
        `;

        weatherData.innerHTML = html;
    }
});