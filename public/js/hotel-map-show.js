document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('hotel-map-show');

    if (!mapElement || typeof L === 'undefined') {
        return;
    }

    const latitude = parseFloat(mapElement.dataset.lat);
    const longitude = parseFloat(mapElement.dataset.lng);
    const address = mapElement.dataset.address || '';

    if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
        return;
    }

    const map = L.map('hotel-map-show').setView([latitude, longitude], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    const marker = L.marker([latitude, longitude]).addTo(map);

    if (address !== '') {
        marker.bindPopup(address).openPopup();
    }

    window.setTimeout(function () {
        map.invalidateSize();
    }, 250);
});
