document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('hotel-map');

    if (!mapElement || typeof L === 'undefined') {
        return;
    }

    const latitudeInput = document.querySelector('input[name="latitude"]');
    const longitudeInput = document.querySelector('input[name="longitude"]');

    if (!latitudeInput || !longitudeInput) {
        return;
    }

    const defaultLatitude = parseFloat(mapElement.dataset.defaultLat || latitudeInput.value || 52.0);
    const defaultLongitude = parseFloat(mapElement.dataset.defaultLng || longitudeInput.value || 19.0);
    const hasExistingCoordinates = latitudeInput.value !== '' && longitudeInput.value !== '';

    const map = L.map('hotel-map').setView(
        [defaultLatitude, defaultLongitude],
        hasExistingCoordinates ? 13 : 6
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    let marker = null;

    function setMarker(latitude, longitude) {
        if (marker) {
            marker.setLatLng([latitude, longitude]);
        } else {
            marker = L.marker([latitude, longitude]).addTo(map);
        }

        latitudeInput.value = latitude.toFixed(7);
        longitudeInput.value = longitude.toFixed(7);
    }

    if (hasExistingCoordinates) {
        setMarker(defaultLatitude, defaultLongitude);
    }

    map.on('click', function (event) {
        setMarker(event.latlng.lat, event.latlng.lng);
    });

    window.setTimeout(function () {
        map.invalidateSize();
    }, 250);
});
