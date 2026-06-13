document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('hotel-map');

    if (!mapElement || typeof L === 'undefined') {
        return;
    }

    const latitudeInput = document.querySelector('input[name="latitude"]');
    const longitudeInput = document.querySelector('input[name="longitude"]');
    const cityInput = document.getElementById('city');
    const addressInput = document.getElementById('address');
    const geocodeStatus = document.getElementById('map-geocode-status');

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
    let lastGeocodeAt = 0;

    function setGeocodeStatus(message, isError) {
        if (!geocodeStatus) {
            return;
        }

        geocodeStatus.textContent = message;
        geocodeStatus.classList.toggle('text-danger', Boolean(isError));
        geocodeStatus.classList.toggle('text-muted', !isError);
    }

    function setMarker(latitude, longitude) {
        if (marker) {
            marker.setLatLng([latitude, longitude]);
        } else {
            marker = L.marker([latitude, longitude]).addTo(map);
        }

        latitudeInput.value = latitude.toFixed(7);
        longitudeInput.value = longitude.toFixed(7);
    }

    function resolveCity(address) {
        return address.city
            || address.town
            || address.village
            || address.municipality
            || address.county
            || '';
    }

    function resolveStreetAddress(address) {
        const parts = [address.road, address.house_number].filter(Boolean);

        return parts.join(' ');
    }

    async function fillAddressFromCoordinates(latitude, longitude) {
        const now = Date.now();

        if (now - lastGeocodeAt < 1000) {
            await new Promise(function (resolve) {
                window.setTimeout(resolve, 1000 - (now - lastGeocodeAt));
            });
        }

        lastGeocodeAt = Date.now();
        setGeocodeStatus('Pobieram adres…', false);

        try {
            const url = new URL('https://nominatim.openstreetmap.org/reverse');
            url.searchParams.set('format', 'json');
            url.searchParams.set('lat', String(latitude));
            url.searchParams.set('lon', String(longitude));
            url.searchParams.set('accept-language', 'pl');

            const response = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                    'Accept-Language': 'pl',
                },
            });

            if (!response.ok) {
                throw new Error('Geocoding failed');
            }

            const data = await response.json();
            const address = data.address || {};

            if (cityInput) {
                cityInput.value = resolveCity(address);
            }

            if (addressInput) {
                addressInput.value = resolveStreetAddress(address);
            }

            setGeocodeStatus('Adres uzupełniony z mapy. Możesz go poprawić ręcznie.', false);
        } catch (error) {
            setGeocodeStatus('Nie udało się pobrać adresu — współrzędne zostały zapisane.', true);
        }
    }

    if (hasExistingCoordinates) {
        setMarker(defaultLatitude, defaultLongitude);
    }

    map.on('click', function (event) {
        const latitude = event.latlng.lat;
        const longitude = event.latlng.lng;

        setMarker(latitude, longitude);
        fillAddressFromCoordinates(latitude, longitude);
    });

    window.setTimeout(function () {
        map.invalidateSize();
    }, 250);
});
