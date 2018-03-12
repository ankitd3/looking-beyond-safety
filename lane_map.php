
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <!-- custom css-->
    <link href="css/slider.css" rel="stylesheet">
    <link href="css/form.css" rel="stylesheet">
    <style type="text/css">
    	:focus{
		  outline:none;
		}
		.radio{
		  -webkit-appearance:button;
		  -moz-appearance:button;
		  appearance:button;
		  border:4px solid #ccc;
		  border-top-color:#bbb;
		  border-left-color:#bbb;
		  background:#fff;
		  width:50px;
		  height:50px;
		  border-radius:50%;
		}
		.radio:checked{
		  border:20px solid #4099ff;
		}
    </style>
  
    <title>Looking Beyond Syllabus</title>

    <?php
    SESSION_start();
    if(isset($_POST['speed']) && isset($_POST['lat']) && isset($_POST['long']))
    {
      $_SESSION['speed']=$_POST["speed"];
      $_SESSION['lat']=$_POST["lat"];
      $_SESSION['long']=$_POST["long"];
      $lat=$_SESSION['lat'];
      $long=$_SESSION['long'];
      $speed=$_SESSION['speed'];
    }
    else 
    {
      $_SESSION['speed']=0;$_SESSION['lat']=19.019474137261;$lat=19.019474137261;$long=73.01705417305;$_SESSION['long']=73.01705417305;
    }
    $var='';
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sprdh";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if($lat > 19.00882 && $long >73.008835){
      $area=1;
    }
    else{
      $area=2;
    }

    $sql2 = "SELECT sl,Lanes FROM speed WHERE area=$area";
    $result2 = $conn->query($sql2);

      if ($result2->num_rows > 0) {
          // output data of each row
          while($row = $result2->fetch_assoc()) {
              $sl=$row["sl"];
              $lane=$row["Lanes"];
          }
      }
    if($lane>1){

      $myfile = fopen("l.txt", "r") or die("Unable to open file!");
      $l = fgets($myfile);
      fclose($myfile);

      $image='/out_images/1.jpg';
      /*if(isset($_POST['lane']))
      {
       $l_f=$_POST['lane'];
       if($l!=$l_f)
       {
        $l=$l_f;
       }
      }
      else
      {
        $l=0;
      }*/
      
      $l1='';
      $l2='';
      $l3='';

      if($l==1)
      {
        $l1='checked';
        $sl=$sl-5;
      }
      if($l==2)
      {
        $l2='checked';
      }
      if($l==3)
      {
        $sl=$sl+5;
        $l3='checked';
      }
      $var='<script>  discl();</script>';
      
    }
      
    $warn="";
    if ($sl-$_SESSION['speed'] < 10) {
      $warn="WARNING";
    }
    if ($sl-$_SESSION['speed']<=0) {
      $warn="OVERSPEEDING";
      $sql = "INSERT INTO over (speed,longi,lat) VALUES ('$speed','$long','$lat')";
      if ($conn->query($sql) === TRUE) {
        $warn .= "Recorded";}
      //} else {
      //  $warn = "Error: " . $sql . "<br>" . $conn->error;
      //}

     }
    ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyDza9f8W8IVQsOjqrShmYG6yeroepHlIkQ"></script>

<script type='text/javascript'>//<![CDATA[
        window.onload=function(){
          var map;
          function initialize() {
              var lat = '<?php echo $lat ?>';
              var long = '<?php echo $long ?>';
              var myLatlng = new google.maps.LatLng(lat, long);

              var myOptions = {
                  zoom: 16,
                  center: myLatlng,
                  mapTypeId: google.maps.MapTypeId.ROADMAP
              };
              map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

              //var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
              
              var marker = new google.maps.Marker({
                  draggable: true,
                  position: myLatlng,
                  map: map,
                  title: "Your location",
                  icon: 'car1.png'
              });

              google.maps.event.addListener(marker, 'dragend', function (event) {
                  document.getElementById("lat").value = event.latLng.lat();
                  document.getElementById("long").value = event.latLng.lng();
                  infoWindow.open(map, marker);
              });
          }
          google.maps.event.addDomListener(window, "load", initialize());
        }//]]> 
    </script>  

</head>

<body>
    <!-- Page Content -->
  <div class = "container text-center"> 
   <h1 style="color: #b30000;"><?php echo $warn;?> <span style="color: #d2691e"> Speed Limit: <?php echo $sl; ?> </span></h1>
  </div>
	<form id="one" class="form-inline" action="lane_map.php" method="post">
           	<div class="col-md-2 text-center">
                 <div class="form-group">
                 	<br><br><br>
                 	<h2><span class="label label-default">Latitude:</span></h2>
                    <br><br>
                      <input id="lat" type="text" name="lat" value="<?php echo $_SESSION['lat'] ?>" class="form-control form-control-lg" readonly>
                 </div>
                 <br><br><br><br>
                 <div class="form-group">
                 	<h2><span class="label label-default">Longitude:</span></h2>
                 	<br><br>
                      <input id="long" type="text" name="long" value="<?php echo $_SESSION['long'] ?>" class="form-control form-control-lg" readonly>
                 </div>
            </div>
    <div class="col-md-8">
            <div id="map_canvas" class="col-md-12" style=" width: 100%; height: 500px; float: center;"></div>
    </div>
                <!-- form starts here to display the co-ordinates-->
 
                <div id="lane_img" style="display: none;">
                	<div class="col-md-2 text-right" style="font-size: 200%;">
                		<br><br><br>
                		<h2><span class="label label-default">Lane Number:</span></h2><br>

                      Slowest : <input type="radio" name="lane" id="a" value="1" <?php echo $l1;?> class="radio"> 1<br>
                      <input type="radio" name="lane" id="b" value="2" <?php echo $l2?> class="radio"> 2<br>
                      Fastest : <input type="radio" name="lane" id="c" value="3" <?php echo $l3;?> class="radio"> 3 
                    </div>
                    <div class="text-center"> 
                      <img src="out_images/1.jpg" style="width:500px;height:250px;">
                      <img src="<?php if($l==1)
                                        echo "lane1.jpg";
                                      if($l==2)
                                        echo "lane2.jpg";
                                      if($l==3)
                                        echo "lane3.jpg"; ?>" alt="3LANESPIC" usemap="#lanemap">

                      <map name="lanemap">
                        <area shape="rect" coords="0,0,157,322" alt="lane1" onClick="document.getElementById('a').checked = true;">
                        <area shape="rect" coords="160,0,265,322" alt="lane2" onClick="document.getElementById('b').checked = true;">
                        <area shape="rect" coords="268,0,440,322" alt="lane3" onClick="document.getElementById('c').checked = true;">
                      </map>
                 	</div>
                 </div>
                <div class="container">
                    <div id="slidecontainer" class="text-center container-fluid">
                    	<h2><span class="label label-default">CURRENT SPEED:</span></h2>
                    	<input id="anuj1" type="text" name="speed" class="form-control" readonly>
                    	<input type="range" min="1" max="100" value="<?php echo $_SESSION['speed'] ?>" class="slider" id="myRange">
                    </div>
                    <!--p><button type="submit">Submit</button></p-->
                </div>
             </form>
            </div>
        </div>
    </div>
  
    <!-- /.container -->

    <script>
      var myVar = setInterval(subform, 4000);
      var slider = document.getElementById("myRange");
      //var output = document.getElementById("anuj");
      document.getElementById("anuj1").defaultValue = slider.value;
      //output.innerHTML = slider.value;
      slider.oninput = function() {
        //output.innerHTML = this.value;
        document.getElementById("anuj1").defaultValue = this.value;
      }

      function subform(){
          document.forms["one"].submit();
      }

      function discl(){
      	var x = document.getElementById("lane_img");
    	if (x.style.display === "none") {
        	x.style.display = "block";
    	} else {
        	x.style.display = "none";
    	}
      }

    </script>
    <?php echo $var;?>



    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>