<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Management Map</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        #map {
            height: 90vh;
            width: 100%;
        }
        .blink {
            animation: blink 1s infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
        .legend {
            background: white;
            padding: 10px;
            line-height: 18px;
            color: #333;
        }
        .legend i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
        }
    </style>
</head>
<body>

<?php
// dd($getAllUserData);
$data = $getAllUserData; // Ensure this array is populated
$jsonData = json_encode($data);
?>

<?php include("nav.php"); ?>
<div class="container mt-3">


<div class="card1">
        <div class="card-body">
            <div class="text-center">
                <h3>Today's Day Management</h3>
            </div>
        </div>
    </div>

    <form id="filterForm">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="userFilter">User</label>
                <select id="userFilter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="departmentFilter">Department</label>
                <select id="departmentFilter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="workFromFilter">Work From</label>
                <select id="workFromFilter" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
        </div>
    </form>
</div>

<br>

<div id="map"></div>

<br>
<br>

<?php include("footer.php"); ?>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Heatmap Plugin -->
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<!-- MarkerCluster Plugin -->
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
    const data = <?php echo $jsonData; ?>;

    // Extract unique users
    const users = [...new Set(data.map(item => item.user_name))];
    const departments = [...new Set(data.map(item => item.department_name))];
    const workFrom = [...new Set(data.map(item => item.work_from))];

    const userFilter = document.getElementById('userFilter');
    users.forEach(user => {
        const option = document.createElement('option');
        option.value = user;
        option.textContent = user;
        userFilter.appendChild(option);
    });

    const departmentFilter = document.getElementById('departmentFilter');
    departments.forEach(dept => {
        const option = document.createElement('option');
        option.value = dept;
        option.textContent = dept;
        departmentFilter.appendChild(option);
    });

    const workFromFilter = document.getElementById('workFromFilter');
    workFrom.forEach(location => {
        const option = document.createElement('option');
        option.value = location;
        option.textContent = location;
        workFromFilter.appendChild(option);
    });

    function stringToColor(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        let color = '#';
        for (let i = 0; i < 3; i++) {
            const value = (hash >> (i * 8)) & 0xFF;
            color += ('00' + value.toString(16)).substr(-2);
        }
        return color;
    }

    const map = L.map('map').setView([22.5726, 88.3639], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markerCluster = L.markerClusterGroup();
    const heatPoints = [];

    function createBlinkingMarker(lat, lng, color) {
        return L.divIcon({
            className: 'blink',
            html: `<div style="background-color: ${color}; width: 10px; height: 10px; border-radius: 50%;"></div>`,
            iconSize: [10, 10]
        });
    }

    function updateMap() {
        markerCluster.clearLayers();
        map.eachLayer(layer => {
            if (layer instanceof L.Polyline || layer instanceof L.Circle) {
                map.removeLayer(layer);
            }
        });

        const selectedUser = userFilter.value;
        const selectedDepartment = departmentFilter.value;
        const selectedWorkFrom = workFromFilter.value;

        const filteredData = data.filter(item =>
            (!selectedUser || item.user_name === selectedUser) &&
            (!selectedDepartment || item.department_name === selectedDepartment) &&
            (!selectedWorkFrom || item.work_from === selectedWorkFrom)
        );

        heatPoints.length = 0;

        filteredData.forEach(item => {
            const color = stringToColor(item.user_name);

            if (item.start_latitude && item.start_longitude) {
                // Circle zone
                L.circle([item.start_latitude, item.start_longitude], {
                    radius: 100,
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.15
                }).addTo(map);

                // Add to heatmap
                heatPoints.push([item.start_latitude, item.start_longitude, 0.5]);

                const startMarker = L.marker(
                    [item.start_latitude, item.start_longitude],
                    { icon: createBlinkingMarker(item.start_latitude, item.start_longitude, 'orange') }
                ).bindPopup(`Start: ${item.user_name}<br>${new Date(item.ustart).toLocaleDateString()}`);
                markerCluster.addLayer(startMarker);
            }

            if (item.closed_latitude && item.closed_longitude) {
                const endMarker = L.marker(
                    [item.closed_latitude, item.closed_longitude],
                    { icon: createBlinkingMarker(item.closed_latitude, item.closed_longitude, color) }
                ).bindPopup(`Closed: ${item.user_name}<br>${new Date(item.uclose).toLocaleDateString()}`);
                markerCluster.addLayer(endMarker);
            }

            // Draw line from start to closed
            if (item.start_latitude && item.start_longitude && item.closed_latitude && item.closed_longitude) {
                const path = [
                    [item.start_latitude, item.start_longitude],
                    [item.closed_latitude, item.closed_longitude]
                ];
                L.polyline(path, {
                    color: color,
                    weight: 3,
                    opacity: 0.7
                }).addTo(map);
            }
        });

        L.heatLayer(heatPoints, { radius: 25 }).addTo(map);
        map.addLayer(markerCluster);
    }

    userFilter.addEventListener('change', updateMap);
    departmentFilter.addEventListener('change', updateMap);
    workFromFilter.addEventListener('change', updateMap);

    updateMap();

    // Add legend
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function () {
        const div = L.DomUtil.create('div', 'info legend');
        div.innerHTML += `<i style="background: orange;"></i> Start Only<br>`;
        div.innerHTML += `<i style="background: green;"></i> Start to Closed<br>`;
        return div;
    };
    legend.addTo(map);
</script>


<!-- Optional: Bootstrap & jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
