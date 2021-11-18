
<?php
include_once("secureinit.php");
?>

<!doctype html>
<html>
	<head>	
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="OpenLayers-2.13.1/OpenLayers.js"></script>
		 <script>
		 	var lat;
		 	var long;
		 	var map;
		 	var markers;
		 	var mapnik;
			var fromProjection;
			var toProjection;
			var position;
			var zoom;

			var flag = true;

			var icon_paths = ["imgs/marker.png",
							  "imgs/marker-blue.png",
							  "imgs/marker-green.png",
							  "imgs/marker-orange.png",
							  "imgs/marker-pink.png",
							  "imgs/marker-gold.png"];//,
							  //"imgs/marker-orange.png",
							  //"imgs/marker-orange.png",
							  //"imgs/marker-orange.png"]

		 	function getCookie(cookieName) {
			  let cookie = {};
			  document.cookie.split(';').forEach(function(el) {
			    let [key,value] = el.split('=');
			    cookie[key.trim()] = value;
			  })
			  return cookie[cookieName];
			}

			function showUser(vote) {
			    var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			      if (this.readyState == 4 && this.status == 200) {
						updateMarkers();			        
			      }
			    };

			    xmlhttp.open("GET","insertpin.php?id="+<?php echo '"'.$_SESSION['name'].'"'; ?>+"&vote="+vote+"&GPSlatitude="+lat+"&GPSlongitude="+long,true);
			    xmlhttp.send();
			  
			}
		 	

			//var x = document.getElementById("demo");

			function getLocation(callback, args) {
				if (navigator.geolocation) {
					
				    navigator.geolocation.getCurrentPosition(
				    	function(position) {
					    	lat = position.coords.latitude;
					    	long = position.coords.longitude;
							if (args==undefined) callback();
							else {
								if (callback!=undefined)callback(args);
							}
							init();
					    },
					    function(error){
					         alert(error.message);
					    }, {
					         enableHighAccuracy: true
					              ,timeout : 5000
					    });
				} else {
				    console.log("Geolocation is not supported by this browser.");
				}

			}

			function init() {
				document.getElementById("demoMap").innerHTML="";				
				map = new OpenLayers.Map("demoMap");
				mapnik         = new OpenLayers.Layer.OSM();
				fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
				toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
				position       = new OpenLayers.LonLat(long, lat).transform( fromProjection, toProjection);
				zoom           = 15; 

				

				map.addLayer(mapnik);

				updateMarkers();

				map.setCenter(position, zoom );
			}

			function handleCellClick(val) {
				if (confirm(val)) {
					getLocation(showUser, val);
				}
			}



			function undoLastClick(val) {
				if (confirm(val)) {
					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				      if (this.readyState == 4 && this.status == 200) {
				    		updateMarkers();
				      }
				    };

				    xmlhttp.open("GET","undoLastClick.php?username="+<?php echo '"'.$_SESSION['name'].'"'; ?>,true);
				    xmlhttp.send();
				}
			}

			function updateMarkers() {


				if (markers!=undefined)
					markers.destroy();
				markers = new OpenLayers.Layer.Markers( "Markers" );
			    map.addLayer(markers);
			    //markers.addMarker(new OpenLayers.Marker(position));

			    var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			      if (this.readyState == 4 && this.status == 200) {
			    	var objJSON = JSON.parse(this.responseText);
			    	var icon_idx = -1;
			    	var pairs_user_icon = {};
			    	var icon_path;

			        for (let i=0; i<objJSON.length; i++) {
			        	var row = objJSON[i];

			        	if (!(row[0] in pairs_user_icon)) {
			        		icon_idx = (icon_idx + 1) % icon_paths.length;
			        		pairs_user_icon[row[0]] = icon_paths[icon_idx];
			        	}

			        	icon_path = pairs_user_icon[row[0]];

			        	marker = new OpenLayers.Marker(new OpenLayers.LonLat(row[3], row[2]).transform( fromProjection, toProjection));
			        	marker.icon = new OpenLayers.Icon(icon_path,
                               {w: 21, h: 25}, {x: -10.5, y: -25});
			        	markers.addMarker(marker);
			        }
			      }

			    };

			    xmlhttp.open("GET","collectMarkers.php",true);
			    xmlhttp.send();
			}


    	</script>

    	<link rel="stylesheet" href="css/home_style.css">
		
	</head>
	<body>

		<?php include_once("menu.php"); ?>

		<div id="demoMap"></div>

		<script>
			getLocation(init, undefined);
			
			window.setInterval(function(){
			  updateMarkers()
			}, 5000);
		</script>

		<table id="voting_table">
			<tr>
				<td><div id="cell" onclick="undoLastClick()">UNDO</div></td>
				<td><div id="cell" onclick='handleCellClick(10);'>10</div></td>
				<td><div id="cell"></div></td>
			</tr>
			<tr>
				<td><div id="cell" onclick='handleCellClick(7);'>7</div></td>
				<td><div id="cell" onclick='handleCellClick(8);'>8</div></td>
				<td><div id="cell" onclick='handleCellClick(9);'>9</div></td>
			</tr>
			<tr>
				<td><div id="cell" onclick='handleCellClick(4);'>4</div></td>
				<td><div id="cell" onclick='handleCellClick(5);'>5</div></td>
				<td><div id="cell" onclick='handleCellClick(6);'>6</div></td>
			</tr>
			<tr>
				<td><div id="cell" onclick='handleCellClick(1);'>1</div></td>
				<td><div id="cell" onclick='handleCellClick(2);'>2</div></td>
				<td><div id="cell" onclick='handleCellClick(3);'>3</div></td>
			</tr>
		</table>

	</body>
</html>