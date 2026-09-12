<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Philippine Locations - Pinoy Address Finder</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --ph-blue: #0038a8;
    --ph-red: #ce1126;
    --ph-gold: #fcd116;
    --ph-light-blue: #e6ecf7;
    --ph-light-red: #f9e6e8;
    --ph-light-gold: #fef6e0;
    --ph-dark: #1a1a2e;
    --ph-gray: #6c757d;
    --ph-light: #f8f9fa;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--ph-light-blue) 0%, var(--ph-light-red) 50%, var(--ph-light-gold) 100%);
    min-height: 100vh;
    padding: 20px;
    color: var(--ph-dark);
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(10px);
}

.loading-content {
    text-align: center;
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 56, 168, 0.1);
}

.ph-flag-loader {
    margin-bottom: 20px;
}

.flag-loader {
    position: relative;
    width: 80px;
    height: 40px;
    margin: 0 auto;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.flag-side {
    position: absolute;
    top: 0;
    width: 50%;
    height: 100%;
}

.flag-side.blue {
    left: 0;
    background: var(--ph-blue);
}

.flag-side.red {
    right: 0;
    background: var(--ph-red);
}

.sun-loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--ph-gold);
    font-size: 1.5em;
    animation: sunRotate 2s linear infinite;
}

@keyframes sunRotate {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

.loading-content h3 {
    margin-bottom: 10px;
    color: var(--ph-blue);
    font-size: 1.5em;
}

.loading-content p {
    color: var(--ph-gray);
    margin-bottom: 20px;
}

.loading-progress {
    margin-top: 20px;
    width: 300px;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: var(--ph-light);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--ph-blue), var(--ph-red), var(--ph-gold));
    width: 0%;
    animation: progressAnimation 2s ease-in-out infinite;
}

@keyframes progressAnimation {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

/* Container */
.container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--ph-blue) 0%, var(--ph-red) 50%, var(--ph-gold) 100%);
}

/* Header */
.header {
    margin-bottom: 40px;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 20px;
    justify-content: center;
}

.ph-flag-logo {
    flex-shrink: 0;
}

.flag-logo {
    position: relative;
    width: 60px;
    height: 30px;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.blue-side {
    position: absolute;
    top: 0;
    left: 0;
    width: 50%;
    height: 100%;
    background: var(--ph-blue);
}

.red-side {
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: var(--ph-red);
}

.gold-sun {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--ph-gold);
    font-size: 0.8em;
}

.header-text {
    text-align: left;
}

.header h1 {
    color: var(--ph-blue);
    font-size: 2.2em;
    margin-bottom: 5px;
    font-weight: 700;
}

.header p {
    color: var(--ph-red);
    font-size: 1.1em;
    font-weight: 500;
    margin: 0;
}

/* Location Selector */
.location-selector {
    display: grid;
    gap: 25px;
    margin-bottom: 40px;
}

.form-group {
    display: flex;
    flex-direction: column;
    position: relative;
}

label {
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--ph-dark);
    font-size: 1.1em;
    display: flex;
    align-items: center;
    gap: 8px;
}

label i {
    color: var(--ph-blue);
    width: 20px;
}

select {
    padding: 15px;
    border: 2px solid #e1e5e9;
    border-radius: 10px;
    font-size: 1em;
    background: white;
    transition: all 0.3s ease;
    font-family: inherit;
}

select:focus {
    outline: none;
    border-color: var(--ph-blue);
    box-shadow: 0 0 0 3px rgba(0, 56, 168, 0.1);
}

select:disabled {
    background-color: var(--ph-light);
    color: var(--ph-gray);
    cursor: not-allowed;
    border-color: #dee2e6;
}

/* Loading Indicators */
.loading-indicator {
    display: none;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    font-size: 0.9em;
    color: var(--ph-blue);
    font-weight: 500;
}

.loading-indicator.show {
    display: flex;
}

.loading-indicator i {
    color: var(--ph-red);
}

/* Selected Location */
.selected-location {
    background: linear-gradient(135deg, var(--ph-light-blue) 0%, var(--ph-light-red) 100%);
    padding: 30px;
    border-radius: 15px;
    border-left: 4px solid var(--ph-blue);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.selected-location::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, transparent 50%, var(--ph-gold) 50%);
    opacity: 0.1;
}

.location-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.location-header i {
    color: var(--ph-red);
    font-size: 1.5em;
}

.location-header h3 {
    color: var(--ph-dark);
    margin: 0;
    font-weight: 600;
}

.location-content #location-display {
    font-size: 1.3em;
    color: var(--ph-blue);
    font-weight: 600;
    margin-bottom: 15px;
    min-height: 1.5em;
}

.location-details {
    display: grid;
    gap: 10px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(0, 56, 168, 0.1);
}

.detail-label {
    font-weight: 600;
    color: var(--ph-dark);
}

.detail-item span:last-child {
    color: var(--ph-blue);
    font-weight: 500;
}

/* Philippine Info Cards */
.ph-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.info-card {
    text-align: center;
    padding: 25px 20px;
    background: white;
    border-radius: 12px;
    border: 1px solid #e1e5e9;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.info-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--ph-blue), var(--ph-red), var(--ph-gold));
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.info-card i {
    font-size: 2em;
    color: var(--ph-blue);
    margin-bottom: 15px;
}

.info-card h4 {
    color: var(--ph-dark);
    margin-bottom: 8px;
    font-size: 1.1em;
    font-weight: 600;
}

.info-card p {
    color: var(--ph-gray);
    font-size: 0.9em;
    margin: 0;
    line-height: 1.4;
}

/* Responsive Design */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }

    .container {
        padding: 25px;
        margin: 10px;
    }

    .logo-section {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }

    .header-text {
        text-align: center;
    }

    .header h1 {
        font-size: 1.8em;
    }

    .ph-info {
        grid-template-columns: 1fr;
    }

    .location-details {
        grid-template-columns: 1fr;
    }

    .detail-item {
        flex-direction: column;
        gap: 5px;
    }

    .loading-content {
        padding: 30px 20px;
        margin: 20px;
    }

    .loading-progress {
        width: 250px;
    }
}

/* Animation for updates */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.selected-location {
    animation: fadeIn 0.5s ease-in-out;
}

/* Hover effects */
select:hover:not(:disabled) {
    border-color: var(--ph-blue);
}

.info-card:hover i {
    color: var(--ph-red);
    transition: color 0.3s ease;
}

/* Focus states for accessibility */
select:focus {
    border-color: var(--ph-blue);
    box-shadow: 0 0 0 3px rgba(0, 56, 168, 0.1);
}
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="ph-flag-loader">
                <div class="flag-loader">
                    <div class="flag-side blue"></div>
                    <div class="flag-side red"></div>
                    <div class="sun-loader">
                        <i class="fas fa-sun"></i>
                    </div>
                </div>
            </div>
            <h3>Loading Philippine Locations</h3>
            <p>Fetching the latest data from official sources...</p>
            <div class="loading-progress">
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="logo-section">
                <div class="ph-flag-logo">
                    <div class="flag-logo">
                        <div class="blue-side"></div>
                        <div class="red-side"></div>
                        <div class="gold-sun">
                            <i class="fas fa-sun"></i>
                        </div>
                    </div>
                </div>
                <div class="header-text">
                    <h1>🇵🇭 Pinoy Address Finder</h1>
                    <p>Select your complete Philippine address</p>
                </div>
            </div>
        </div>

        <div class="location-selector">
            <div class="form-group">
                <label for="province">
                    <i class="fas fa-map-marker-alt"></i> Lalawigan (Province)
                </label>
                <select id="province" name="province">
                    <option value="">Pumili ng Lalawigan...</option>
                </select>
                <div class="loading-indicator" id="province-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading provinces...
                </div>
            </div>

            <div class="form-group">
                <label for="municipal">
                    <i class="fas fa-city"></i> Lungsod/Munisipyo (City/Municipality)
                </label>
                <select id="municipal" name="municipal" disabled>
                    <option value="">Pumili ng Lungsod/Munisipyo...</option>
                </select>
                <div class="loading-indicator" id="municipal-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading cities/municipalities...
                </div>
            </div>

            <div class="form-group">
                <label for="barangay">
                    <i class="fas fa-home"></i> Barangay
                </label>
                <select id="barangay" name="barangay" disabled>
                    <option value="">Pumili ng Barangay...</option>
                </select>
                <div class="loading-indicator" id="barangay-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading barangays...
                </div>
            </div>
        </div>

        <div class="selected-location">
            <div class="location-header">
                <i class="fas fa-map-pin"></i>
                <h3>Iyong Napiling Address</h3>
            </div>
            <div class="location-content">
                <p id="location-display">Mangyaring pumili ng kumpletong address</p>
                <div class="location-details">
                    <div class="detail-item">
                        <span class="detail-label">Lalawigan:</span>
                        <span id="detail-province">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lungsod/Munisipyo:</span>
                        <span id="detail-municipal">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Barangay:</span>
                        <span id="detail-barangay">-</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="ph-info">
            <div class="info-card">
                <i class="fas fa-landmark"></i>
                <h4>Official PSGC Data</h4>
                <p>Powered by Philippine Statistics Authority</p>
            </div>
            <div class="info-card">
                <i class="fas fa-sync"></i>
                <h4>Real-time Updates</h4>
                <p>Always up-to-date location information</p>
            </div>
            <div class="info-card">
                <i class="fas fa-map"></i>
                <h4>Complete Coverage</h4>
                <p>All 81 provinces, 1,600+ cities/municipalities</p>
            </div>
        </div>
    </div>

    <script>
        // Base path for our own Laravel API endpoints (see routes/api.php).
        window.LOCATIONS_API_BASE = "{{ url('/api/locations') }}";

// Philippine Location Selector
document.addEventListener("DOMContentLoaded", function() {
    const provinceSelect = document.getElementById("province");
    const municipalSelect = document.getElementById("municipal");
    const barangaySelect = document.getElementById("barangay");
    const locationDisplay = document.getElementById("location-display");
    const loadingOverlay = document.getElementById("loadingOverlay");

    // Loading indicators
    const provinceLoading = document.getElementById("province-loading");
    const municipalLoading = document.getElementById("municipal-loading");
    const barangayLoading = document.getElementById("barangay-loading");

    // Our own Laravel API (see routes/api.php + LocationController).
    // The PSGC/Buonzz fallback logic now runs server-side, so the
    // browser only ever talks to this app.
    const API_BASE = window.LOCATIONS_API_BASE || "/api/locations";

    // Helper functions
    function showLoading(selectElement, loadingElement) {
        selectElement.disabled = true;
        loadingElement.classList.add('show');
    }

    function hideLoading(selectElement, loadingElement) {
        selectElement.disabled = false;
        loadingElement.classList.remove('show');
    }

    function clearOptions(selectElement, placeholder) {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
    }

    function showError(selectElement, loadingElement, message) {
        clearOptions(selectElement, message);
        hideLoading(selectElement, loadingElement);
    }

    function sortByName(a, b) {
        return a.name.localeCompare(b.name);
    }

    function updateLocationDisplay() {
        const province = provinceSelect.value;
        const municipal = municipalSelect.value;
        const barangay = barangaySelect.value;

        // Update main display
        if (province && municipal && barangay) {
            locationDisplay.textContent = `${barangay}, ${municipal}, ${province}`;
            locationDisplay.style.color = '#0038a8';
        } else {
            locationDisplay.textContent = "Mangyaring pumili ng kumpletong address";
            locationDisplay.style.color = '#666';
        }

        // Update detail items
        document.getElementById('detail-province').textContent = province || '-';
        document.getElementById('detail-municipal').textContent = municipal || '-';
        document.getElementById('detail-barangay').textContent = barangay || '-';
    }

    // Hide initial loading overlay
    function hideMainLoading() {
        loadingOverlay.style.display = 'none';
    }

    // API Fetch Function - talks only to our own Laravel backend now.
    async function fetchData(url) {
        try {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error(`Failed to fetch ${url}:`, error);
            throw error;
        }
    }

    // Data Loading Functions
    async function loadProvinces() {
        showLoading(provinceSelect, provinceLoading);

        try {
            const provinces = await fetchData(`${API_BASE}/provinces`);

            clearOptions(provinceSelect, "Pumili ng Lalawigan...");
            provinces.sort(sortByName).forEach(province => {
                const option = document.createElement("option");
                option.value = province.name;
                option.dataset.code = province.code || province.id;
                option.dataset.isRegion = province.isRegion || province.name.includes("NCR");
                option.text = province.name;
                provinceSelect.appendChild(option);
            });

            hideLoading(provinceSelect, provinceLoading);
            hideMainLoading();
        } catch (error) {
            showError(provinceSelect, provinceLoading, "Error loading provinces");
            console.error("Failed to load provinces:", error);
            hideMainLoading();
        }
    }

    async function loadMunicipalities(provinceData) {
        showLoading(municipalSelect, municipalLoading);
        clearOptions(barangaySelect, "Pumili ng Barangay...");
        barangaySelect.disabled = true;

        try {
            const isRegion = provinceData.isRegion ? '1' : '0';
            const url = `${API_BASE}/provinces/${provinceData.code}/cities?is_region=${isRegion}`;
            const municipalities = await fetchData(url);

            clearOptions(municipalSelect, "Pumili ng Lungsod/Munisipyo...");
            municipalities.sort(sortByName).forEach(municipality => {
                const option = document.createElement("option");
                option.value = municipality.name;
                option.dataset.code = municipality.code || municipality.id;
                option.text = municipality.name;
                municipalSelect.appendChild(option);
            });

            hideLoading(municipalSelect, municipalLoading);
        } catch (error) {
            showError(municipalSelect, municipalLoading, "Error loading cities/municipalities");
            console.error("Failed to load municipalities:", error);
        }
    }

    async function loadBarangays(cityCode) {
        showLoading(barangaySelect, barangayLoading);

        try {
            const barangays = await fetchData(`${API_BASE}/cities/${cityCode}/barangays`);

            clearOptions(barangaySelect, "Pumili ng Barangay...");
            barangays.sort((a, b) => (a.name || a.brgy_name).localeCompare(b.name || b.brgy_name)).forEach(barangay => {
                const option = document.createElement("option");
                option.value = barangay.name || barangay.brgy_name;
                option.text = barangay.name || barangay.brgy_name;
                barangaySelect.appendChild(option);
            });

            hideLoading(barangaySelect, barangayLoading);
        } catch (error) {
            showError(barangaySelect, barangayLoading, "Error loading barangays");
            console.error("Failed to load barangays:", error);
        }
    }

    // Event Handlers
    function handleProvinceChange() {
        const selectedValue = this.value;
        clearOptions(municipalSelect, "Pumili ng Lungsod/Munisipyo...");
        clearOptions(barangaySelect, "Pumili ng Barangay...");
        municipalSelect.disabled = true;
        barangaySelect.disabled = true;
        updateLocationDisplay();

        if (!selectedValue) return;

        const selectedOption = this.options[this.selectedIndex];
        loadMunicipalities({
            code: selectedOption.dataset.code,
            name: selectedValue,
            isRegion: selectedOption.dataset.isRegion === "true"
        });
    }

    function handleMunicipalChange() {
        const selectedValue = this.value;
        clearOptions(barangaySelect, "Pumili ng Barangay...");
        barangaySelect.disabled = true;
        updateLocationDisplay();

        if (!selectedValue) return;

        const selectedOption = this.options[this.selectedIndex];
        loadBarangays(selectedOption.dataset.code);
    }

    function handleBarangayChange() {
        updateLocationDisplay();
    }

    // Initialize
    function init() {
        if (!provinceSelect || !municipalSelect || !barangaySelect) return;

        municipalSelect.disabled = true;
        barangaySelect.disabled = true;

        // Set up event listeners
        provinceSelect.addEventListener("change", handleProvinceChange);
        municipalSelect.addEventListener("change", handleMunicipalChange);
        barangaySelect.addEventListener("change", handleBarangayChange);

        // Initial load
        loadProvinces();
    }

    // Start the application
    init();
});

    </script>
</body>
</html>
