<html>
<head> 
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
h1 {
  color: green;
   font-size: 70px;
}
</style>
</head>
 <style>
 .button {
  background-color: gray; /* Green */
  border: none;
  color: white;
  padding: 16px 40px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 100px;
   margin: 20px 2px;
   cursor: pointer;
   outline: none;
   border-radius: 15px;
   box-shadow: 0 9px #999;
}
.button:hover {background-color: #3e8e41}
.button:active {
  background-color: #3e8e41;
  box-shadow: 0 5px #666;
  transform: translateY(15px);
}
.wrapper {
    text-align: center;
}

.btnon {padding: 50px 202px;}
.btnoff {padding: 50px 178px;}
 </style>
<?php 

require 'conexion.php';
$obj = new BD_PDO();

$tabla = $obj->Ejecutar_Instruccion("SELECT *from status ");


if(isset($_POST['ON']))	{
    $obj->Ejecutar_Instruccion("INSERT INTO status (status) VALUES(1)");
}

if(isset($_POST['OFF'])){
    $obj->Ejecutar_Instruccion("INSERT INTO status (status) VALUES(0)");
}

?>
 
<body>

  <form action="led.php" method="POST">

    <div class="wrapper">
    <h1>Practica 2 AIOT</h1>
    <button class="button btnon" type="submit" name = "ON" id="ON">ON</button><br>

    <button class="button btnoff"  type="submit" name = "OFF" id="OFF">OFF</button>
  </form>

  </div>

  <div id="curve_chart"></div> 

</body>
<script>
google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart_temps_bd);
      google.charts.setOnLoadCallback(drawChart_temps);
    
 function drawChart_temps_bd() 
 {
        var data = google.visualization.arrayToDataTable([['Hora','ESTADO'],

       <?php foreach ($tabla as $renglon) 
        {             ?>        
            ['<?php echo $renglon[0]; ?>',parseFloat('<?php echo $renglon[1]; ?>')],
<?php } ?>
            ]);    

        var options = {
          title: 'Estado del motor',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
</script>
	
</html>