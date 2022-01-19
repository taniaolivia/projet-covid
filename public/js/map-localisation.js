let myMap;

let goldIcon = new L.Icon({
    iconUrl: '../img/marker-icon-2x-gold.png',
    shadowUrl: '../img/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

let redIcon = new L.Icon({
    iconUrl: '../img/marker-icon-2x-red.png',
    shadowUrl: '../img/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

let positions = {
    lat : document.getElementById('lat'),
    long : document.getElementById('long')
};

function allow(position)
{
    positions.lat.value = position.coords.latitude;
    positions.long.value = position.coords.longitude;

    console.log(positions.lat.value + ", " + positions.long.value);

    L.marker([positions.lat.value, positions.long.value],{icon:goldIcon}).addTo(myMap);
}

function reject(error)
{
    let message = "";
    switch(error.code)
    {
        case 1:
            message = "Permission denied";
            break;
        case 2:
            message = "Position indisponible";
            break;
        case 3:
            message = "Timeout";
            break;
        case 4:
            message = "Error not found";
            break;
    }
    console.log(message);
}

function initMap()
{
    myMap = L.map('map').setView([46, 2], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            minZoom: 1,
            maxZoom: 20
    }).addTo(myMap);
}

function geolocalisation(event)
{
    navigator.geolocation.getCurrentPosition(allow, reject);
}


let geo = document.getElementById("geo");
let all = document.getElementById('infected');

initMap();

if(geo)
{
    geo.addEventListener("click", function() {
        geolocalisation();
    });
}

if(all)
{
    all.addEventListener("click", function()
    {
        let lati = document.querySelectorAll('#lati');
        let longi = document.querySelectorAll('#longi');
        let uname = document.querySelectorAll('#username');

        for(let i = 0; i < lati.length; i++)
        {
            L.marker([lati[i].value, longi[i].value],{icon:redIcon}).addTo(myMap).bindPopup(uname[i].value);
        }
    });
}

