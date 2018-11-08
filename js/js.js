function initialize() {
  var mapOptions = {
    zoom: 12,
	center: {lat: 45.890881, lng: 13.884268}, 
	mapTypeId: 'hybrid'
  };

  map = new google.maps.Map(document.getElementById('map'),
    mapOptions);

  pointArray = new google.maps.MVCArray(burja1);
  pointArray2 = new google.maps.MVCArray(burja2);

  heatmap = new google.maps.visualization.HeatmapLayer({
    data: pointArray
  })

  heatmap.setMap(map);
  //  heatmap2.setMap(map);
}

function toggleHeatmap() {
  if (firstData) {
    heatmap.setData(pointArray2);
    firstData = false;
  } else {
    heatmap.setData(pointArray);
    firstData = true;
  }
}

function changeGradient() {
  var gradient = [
    'rgba(0, 255, 255, 0)',
    'rgba(0, 255, 255, 1)',
    'rgba(0, 191, 255, 1)',
    'rgba(0, 127, 255, 1)',
    'rgba(0, 63, 255, 1)',
    'rgba(0, 0, 255, 1)',
    'rgba(0, 0, 223, 1)',
    'rgba(0, 0, 191, 1)',
    'rgba(0, 0, 159, 1)',
    'rgba(0, 0, 127, 1)',
    'rgba(63, 0, 91, 1)',
    'rgba(127, 0, 63, 1)',
    'rgba(191, 0, 31, 1)',
    'rgba(255, 0, 0, 1)'
  ]
  heatmap.setOptions({
    gradient: heatmap.get('gradient') ? null : gradient
  });
}

function changeRadius() {
  heatmap.setOptions({
    radius: heatmap.get('radius') ? null : 20
  });
}

function changeOpacity() {
  heatmap.setOptions({
    opacity: heatmap.get('opacity') ? null : 0.2
  });
}

google.maps.event.addDomListener(window, 'load', initialize);

// Adding 500 Data Points
var map = null;
var heatmap = null;
var heatmap2 = null;
var pointArray = null;
var pointArray2 = null;
var firstData = true;
var burja1 = [
new google.maps.LatLng(45.890881, 13.884268),
new google.maps.LatLng(45.890881, 13.864268),
new google.maps.LatLng(45.890881, 13.874268),
new google.maps.LatLng(45.890881, 13.894268),
new google.maps.LatLng(45.880881, 13.894268),
new google.maps.LatLng(45.870881, 13.894268),
new google.maps.LatLng(45.860881, 13.894268)
];

var burja2 = [
new google.maps.LatLng(45.890881, 13.884268),
new google.maps.LatLng(45.890881, 13.864268),
new google.maps.LatLng(45.890881, 13.874268),
new google.maps.LatLng(45.890881, 13.894268),
new google.maps.LatLng(45.880881, 13.894268),
new google.maps.LatLng(45.870881, 13.894268),
new google.maps.LatLng(45.860881, 13.894268)
];

