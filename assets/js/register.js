let map =  L.map('map').setView([-34.6137, -58.3899], 7);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

let marker = null;

map.on('click', onMapClick);

function onMapClick(e) {

    if (marker) {
        map.removeLayer(marker);
    }

    marker = L.marker(e.latlng).addTo(map);

    marker.bindPopup("Ubicación seleccionada").openPopup();
}


async function onMapClick(e) {

    if (marker) {
        map.removeLayer(marker);
    }

    marker = L.marker(e.latlng).addTo(map);

    const lat = e.latlng.lat;
    const lon = e.latlng.lng;

    const response = await fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`
    );

    const data = await response.json();

    console.log(data);

    document.getElementById("ciudad").value =
        data.address.city ||
        data.address.town ||
        data.address.village ||
        "";

    document.getElementById("pais").value =
        data.address.country || "";

    marker.bindPopup(`
        <strong>${document.getElementById("ciudad").value}</strong><br>
        ${document.getElementById("pais").value}
    `).openPopup();
}
