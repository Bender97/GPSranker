<head>

	<link rel="stylesheet" href="css/style.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script>

		var nodesList = [];
		var edgesList = [];

		var newedgeList = [];


		var danger_colors = ["#99cc33", // green
							 "#339900",	// light green
							 "#ffff80", // lightyellow
							 "#ffcc00", // gold
							 "#ff9966", // lightred
							 "#e67300", // orange
							 "#cc3300", // bloodred
							 "#802000", // dark blood red
							 "#4d1300",
							 "#330d00"]
		var default_color = "#b3ecff";


		// nodes selected for certain actions (draw edge, delete, ecc)
		var node1;
		var node2;

		// status mode variables (defined)
		const SELECT=0;
		const EDGE=1;
		const DRAWNODE = 2;

		var status = SELECT;

		var limit = 5;

		var LOADMODE = 1;
		var CLICKMODE = 0;

		$(document).ready(function() {

var node1;
		var node2;

		// status mode variables (defined)
		const SELECT=0;
		const EDGE=1;
		const DRAWNODE = 2;

		var status = SELECT;

		var LOADMODE = 1;
		var CLICKMODE = 0;

		    $("img").mousedown("click", function(event) {
		    	switch (event.which) {
		    		case 1:
				    	switch(status) {
				    		case DRAWNODE:
				    			drawNode(event, this, CLICKMODE);
				    			break;
				    		case SELECT:
				    			selectNode(event, this);
				    			break;
				    		default:
				    			console.log("click event: no mode selected");
				    	}
				    	break;
				    case 3:
				    	event.preventDefault();
				    	resetSelections();
				    	break;
		    	}
		        
		    });

		    $(document).on('keypress', function(e){


		    	switch(e.which) {
		    		case 127: 			// canc
		    			deleteElements();
		    			break;

		    		case 98: 			// b
		    			clearAll();
		    			break;
		    		case 99:  			// c
		    			if (status==SELECT) 
		    				resetSelections();
		    			break;
		    		case 100: 			// d
		    			download();
		    			break;
		    		case 101: 			// e
		    			drawEdge();
		    			break;
		    		case 104: 			// h
		    			printHelp();
		    			break;
		    		case 108:
		    			document.getElementById("files").click(); // l
		    			break;
		    		case 110: 			// n
		    			status=DRAWNODE;
		    			console.log("status: DRAWNODE");
		    			resetSelections();
		    			break;
		    		case 112: 			// p
		    			printInfo();
		    			break;
		    		case 113: 			// q
		    			removeEdge();
		    			break;
		    		case 115: 			// s
		    			status=SELECT;
		    			console.log("status: SELECT");
		    			break;
		    		case 117: 			// s
		    			console.log("updating markers collected from database");
		    			updateMarkers();
		    			break;
		    		default:
		    			console.log(e.which);
		    			break;
		    	}
		    });

		    document.getElementById('files').addEventListener('change', load, false);


		});

			function deleteLastnode() {
				elems = document.getElementsByClassName("marker");
	    		if (elems.length>0) {
	    			elem = elems[elems.length-1];
	    			var r = confirm("Rimuovere l'ultimo marker?");
					if (r == true) {
	    				elem.remove();
					}
	    		}
	    		else console.log("cannot remove: no markers");
			}

			function clearAll() {

				var r = confirm("Rimuovere tutto?");
				if (r == true) {
    				var elems = document.getElementsByClassName("marker");
		    		while(elems[0]) elems[0].parentNode.removeChild(elems[0]);
		    		nodesList = [];
		    		elems = document.getElementsByClassName("edge");
		    		while(elems[0]) elems[0].parentNode.removeChild(elems[0]);
		    		edgesList = [];
				}
			}

			function deleteParticularEdge(elems, originX, originY, targetX, targetY) {
	    		var length = Math.sqrt((targetX - originX) * (targetX - originX) 
        						+ (targetY - originY) * (targetY - originY));

		        var angle = 180 / 3.1415 * Math.acos((targetY - originY) / length);
		        if(targetX > originX)
		            angle *= -1;

				var flag = 0;

		        for (let i=0; i<elems.length; i++) {
		        	console.log("comparing: " + length.toFixed(3) + "  " + parseFloat(elems[i].style.height));
		        	if (parseFloat(elems[i].style.height)==length.toFixed(3)) {
		        		var r = confirm("Rimuovere edge selezionato?");
		        		if (r==true) {
			        		flag=1;
			        		elems[i].remove();

			        		for (let idx=0; idx<edgesList.length; idx++) {
			        			console.log("idx: " + idx + "  " + edgesList[idx]);
			        			if ( edgesList[idx][0]==targetX && edgesList[idx][1]==targetY && edgesList[idx][2]==originX && edgesList[idx][3]==originY ) {
			        				edgesList.splice(idx, 1);
			        				break;
			        			}
			        			else if ( edgesList[idx][0]==originX && edgesList[idx][1]==originY && edgesList[idx][2]==targetX && edgesList[idx][3]==targetY ) {
			        				edgesList.splice(idx, 1);
			        				break;
			        			}
			        		}



			        	}
			        	break;
		        	}
		        }
		        if (flag==0) { console.log("edge not found"); return -1;}
		        else console.log("success");
			}

			function deleteEdge() {
				elems = document.getElementsByClassName("edge");
	    		if (elems.length>0) {
					var originX = node2[0] + 4;
			        var originY = node2[1] + 4;

			        var targetX = node1[0] + 4;
			        var targetY = node1[1] + 4;

			        deleteParticularEdge(elems, originX, originY, targetX, targetY);

			        
				}
				else console.log("cannot remove: no edges");
			}

			function deleteNode() {
				elems = document.getElementsByClassName("marker");
	    		if (elems.length>0) {
	    			var flag = 0;
			        for (let i=0; i<elems.length; i++) {
			        	if (elems[i].offsetLeft==node1[0] && elems[i].offsetTop==node1[1]) {
			        		var r = confirm("Rimuovere nodo selezionato?");
			        		if (r==true) {
				        		flag=1;
				        		elems[i].remove();

				        		var stack = [];

				        		for (let s=0; s<edgesList.length; s++) {
				        			if (edgesList[s][0]-node1[0]<=limit && edgesList[s][1]-node1[1]<=limit) stack.push(edgesList[s]);
				        			if (edgesList[s][2]-node1[0]<=limit && edgesList[s][3]-node1[1]<=limit) stack.push(edgesList[s]);
				        		}

				        		for (let s=0; s<stack.length; s++) {
				        			deleteParticularEdge(document.getElementsByClassName("edge"), stack[s][0], stack[s][1], stack[s][2], stack[s][3]);
				        		}

				        		for (let s=0; s<nodesList.length; s++) {
				        			console.log("comparing: " + nodesList[s] + " and " + node1);
				        			if (nodesList[s][0] == node1[0] && nodesList[s][1]==node1[1]) {
				        				console.log("found: previous nodeslist size: " + nodesList.length);
				        				nodesList.splice(s, 1);
				        				console.log("final nodeslist size: " + nodesList.length);
				        				break;
				        			}
				        		}


				        	}
				        	break;
			        	}
			        }
			        if (flag==0) console.log("ERROR");
			        else console.log("success");
				}
				else console.log("cannot remove: no markers");
			}

			function deleteElements() {

				if (status==DRAWNODE) {
					deleteLastNode();
				}
				else if (status==SELECT) {
					if (node1!=undefined) {
						if (node2!=undefined) deleteEdge();
				    	else deleteNode();
					}
					else console.log("need to select at least a node");
		    		resetSelections();
				}

				
			}


		    function drawEdge() {
		    	var elems = document.getElementsByClassName("marker");
	    		var len = elems.length;

	    		var last;
	    		var prelast;

	    		if (status==DRAWNODE) {
		    		last = elems[len-1];
		    		prelast = elems[len-2];
	    		}
	    		else {
	    			if (node2==undefined) {
	    				console.log("cannot draw edge between nodes: no node2 selected!");
	    				return;
	    			}
	    			for (let i=0; i<elems.length; i++) {
		    			var node = elems[i];
		    			if (Math.abs(node.offsetLeft-node1[0])<=limit && Math.abs(node.offsetTop-node1[1])<=limit) {
		    				if (last==undefined) last = elems[i];
		    				else {
		    					prelast = elems[i]; break;
		    				}
		    			}
		    			else if (Math.abs(node.offsetLeft-node2[0])<=limit && Math.abs(node.offsetTop-node2[1])<=limit) {
		    				if (last==undefined) last = elems[i];
		    				else {
		    					prelast = elems[i]; break;
		    				}
		    			}
		    		}
	    		}

	    		var originX = prelast.offsetLeft;
		        var originY = prelast.offsetTop;

		        var targetX = last.offsetLeft;
		        var targetY = last.offsetTop;

		        //console.log("origin: " + originX + ", " + originY + " target: " + targetX + ", " + targetY);
		        
		        var length = Math.sqrt((targetX - originX) * (targetX - originX) 
		            + (targetY - originY) * (targetY - originY));
		    
		        var angle = 180 / 3.1415 * Math.acos((targetY - originY) / length);
		        if(targetX > originX)
		            angle *= -1;

		        //var hsv_val = Math.round(((Math.round(length) - 50)/250 * 300) / 10) * 10;

		        var hsv_val = 70;

		    	var c = originX + " " + originY + " " + targetX + " " + targetY;
	    		var linkLine = $('<div id="new-link-line" class="edge ' + c + '" ></div>').appendTo('#image');

		    	linkLine
			        .css('height', length)
		            .css('-webkit-transform', 'rotate(' + angle + 'deg)')
		            .css('-moz-transform', 'rotate(' + angle + 'deg)')
		            .css('-o-transform', 'rotate(' + angle + 'deg)')
		            .css('-ms-transform', 'rotate(' + angle + 'deg)')
		            .css('transform', 'rotate(' + angle + 'deg)')
		            .css('top', originY)
		        	.css('left', originX)
		        	//.css('background-color', 'hsl(' + hsv_val + ', 50%, 50%)');
		        	.css('background-color', default_color)
		        	.css('opacity', 0.6);


		        edgesList.push([originX, originY, targetX, targetY]);

		        if (status==SELECT) {
		        	resetSelections();
		        }

		    }

		    function computeCoords(event, click, offset) {

		        var left=click.offsetLeft;
		        var top=click.offsetTop;
		        var x = event.pageX + offset; //- left; - window.scrollX
		        var y = event.pageY + offset;// - window.scrollY;
		        var cw=click.clientWidth;
		        var ch=click.clientHeight;
		        var iw=click.naturalWidth;
		        var ih=click.naturalHeight;
		        var px=x/cw*iw;
		        var py=y/ch*ih;

		        var coords = new Object();
		    	coords.x = x;
		    	coords.y = y;

		        return coords;
		    }

		    function drawNode(event, click, mode) {

		    	var offset = 0;

		    	if (mode==CLICKMODE) offset = -5;
		    	else if (mode==LOADMODE) offset = 0;

		    	var coords = computeCoords(event, click, offset);

		        nodesList.push([coords.x, coords.y]);

		        $('#image').append( "<div class='marker' style='position:absolute; left: "+coords.x+"px; top:"+coords.y+"px; width:10px; height:10px; background-color:red;'></div>");
		    }

		    function printInfo() {
		    	console.log(nodesList);
		    	console.log(edgesList);
		    }

		    function selectNode(event, click) {

		    	if (node1!=undefined && node2!=undefined) {	// basterebbe anche solo node!=undefined, a rigor di logica
		    		console.log("no place");
		    		return;
		    	}


		    	var nodes = document.getElementsByClassName("marker");
		    	
		    	if (nodes.length==0) {
		    		console.log("cannot select anything (there are no nodes drawn)");
		    		return;
		    	}

		    	var min = 1000000000;
		    	var minnode_idx;

		    	for (let i=0; i<nodes.length; i++) {
		    		var node = nodes[i];
		    		if (node1!=undefined) {
		    			console.log("comparing node i: " + node.offsetLeft + " " + node.offsetTop + " with node1: " + node1[0] + " " + node1[1]);
		    			if (node.offsetLeft==node1[0] && node.offsetTop==node1[1]) { console.log("i:" + i + " - skipping as it is node1"); continue; }

		    			if (node2!=undefined)
		    				if (node.offsetLeft==node2[0] && node.offsetTop==node2[1]) { console.log("i:" + i + " - skipping as it is node2"); continue; }
		    		}
		    		
		    		dist = (event.pageX-node.offsetLeft)*(event.pageX-node.offsetLeft) + (event.pageY-node.offsetTop)*(event.pageY-node.offsetTop);
		    		if (min > dist) {
		    			min=dist;
		    			minnode_idx = i;
		    		}
		    	}

		    	if (node1==undefined) node1 = [nodes[minnode_idx].offsetLeft, nodes[minnode_idx].offsetTop];
		    	else node2 = [nodes[minnode_idx].offsetLeft, nodes[minnode_idx].offsetTop];

		    	nodes[minnode_idx].style.backgroundColor = "#fff";

		    }

		    function resetSelections() {
		    	var nodes = document.getElementsByClassName("marker");
		    	
		    	for (let i=0; i<nodes.length; i++) {
		    		nodes[i].style.backgroundColor = "red";
		    	}
		    	node1 = undefined;
		    	node2 = undefined;
		    }

		    // download graph as file to disk
			function download(){
			    var a = document.body.appendChild(
			        document.createElement("a")
			    );

			    var s="";
			    for (let i=0; i<nodesList.length; i++) {
			    	s+="" + nodesList[i] + "\n";
			    }

			    s+="\r";

			    for (let i=0; i<edgesList.length; i++) {
			    	s+="" + edgesList[i] + "\n";
			    }

			    a.download = "export.txt";
			    a.href = "data:text/html," + s; // Grab the HTML
			    a.click(); // Trigger a click on the element
			}

		    function printHelp() {

		    	console.log("STATUS: " + status);
		    	console.log("node1: " + node1 + " node2: " + node2);
		    	console.log("ACTIONS:\n" +
		    					   "  c - reset selections (if STATUS)\n" +
		    					   "  d - download\n" +
		    					   "  e - draw edge\n" + 
		    					   "  h - print help\n" +
		    					   "  n - enter node mode\n"+ 
		    					   "  p - print info (nodesList and edgeList)\n"+
		    					   "  s - enter status mode\n"+
		    					   "  canc - delete last node (if DRAWNODE)\n");
		    }

		    // load graph from file on disk
		    function load(evt) {

		    	var files = evt.target.files; // FileList object

		    	const file = files[0];
		    	
		    	let reader = new FileReader();

		    	
			    var click = document.getElementById("src");
  
			    reader.onload = (e) => {
			        const file = e.target.result;
			  
			        const lines = file.split(/\n/);

			        var l=0;

			        for (; l<lines.length; l++) {
			        	console.log("read node: " + lines[l]);
			        	if (lines[l][0]=="\r") {console.log("BREAK"); break;}
			        	var coords = lines[l].split(/,/)

				        var event = new Object();
				    	event.pageX = parseInt(coords[0]);
				    	event.pageY = parseInt(coords[1]);

				    	drawNode(event, click, LOADMODE);
				    	console.log("drawn node at " + coords[0] + " " + coords[1]);

			        }

			        status = SELECT;

			        for (; l<lines.length; l++) {
			        	console.log("read edge: " + lines[l]);
			        	var coords = lines[l].split(/,/)

			        	coords = [coords[0], coords[1], coords[2], coords[3]];

				        node1 = [coords[0], coords[1]];
				        node2 = [coords[2], coords[3]];

				    	drawEdge();
				    	console.log("drawn edge at [" + coords[0] + " " + coords[1] + "]  ---   [" + coords[2] + " " + coords[3] + "]");
			        }

			        //console.log(lines.join('\n'));
			  
			    };
			  
			    reader.onerror = (e) => alert(e.target.error.name);
			  
			    reader.readAsText(file);
		    }

			function drawVote(static_lat, static_long, vote) {

				var x = 7367 - (12.078956121302447 - static_lat) / 0.0000056842200597216;
				var y = 1568 + (45.610439496307514 - static_long) / 0.0000039931242747095;

				var event = new Object();
		    	event.pageX = x;
		    	event.pageY = y;

		    	var click = document.getElementById("src");

		    	var coords = computeCoords(event, click, 0);

		        $('#image').append( "<div class='vote_marker' style='position:absolute; left: "+coords.x+"px; top:"+coords.y+"px; width:10px; height:10px; background-color:green;'></div>");


		        var mindist = 10000000000;
		        var minidx = 0;

		        var elems = document.getElementsByClassName("edge");


		        for (let i=0; i< elems.length; i++) {

		        	var c = elems[i].classList;
		        	var p1x = parseInt(c[1]);
		        	var p1y = parseInt(c[2]);
		        	var p2x = parseInt(c[3]);
		        	var p2y = parseInt(c[4]);
		        	var dx = p2x - p1x;
			        var dy = p2y - p1y;

			        var lambda = (dx * (x - p1x) + dy * (y - p1y)) / (dx*dx + dy*dy);

			        var hx = p1x + lambda*dx;
			        var hy = p1y + lambda*dy;


			        if ((hx - p1x)*(hx-p2x) > 0 || (hy - p1y)*(hy-p2y)>0) continue;

			        var dist = (hx - x)*(hx - x) + (hy - y)*(hy - y);

			        if (dist < mindist) {
			        	mindist = dist;
			        	minidx = i;
			        }
		        }

		        console.log("min: " + minidx + " with dist: "  + mindist);
		        elems[minidx].style.backgroundColor = danger_colors[vote];
		        



			}

			function updateMarkers() {

			    var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			      if (this.readyState == 4 && this.status == 200) {
			    	var objJSON = JSON.parse(this.responseText);

			        for (let i=0; i<objJSON.length; i++) {
			        	var row = objJSON[i];

			        	drawVote(row[3], row[2], row[1]);
			        }
			      }

			    };

			    xmlhttp.open("GET","collectMarkers.php",true);
			    xmlhttp.send();
			}
			

			printHelp();

	</script>
</head>


<body>



	<div id="image">
		<img id = "src" src="imgs/maps.bmp" />
		<!--<img id = "src" src="hehe.png" />-->
	</div>

	<input type="file" id="files" name="files" />

	<script>
			//drawStaticNode();

	</script>



</body>