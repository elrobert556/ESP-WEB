# ESP-WEB
Este repositorio te permite manejar la direccion del giro de un motor reductor controlao por un L298N desde una pagina web

Primero explicare como funciona el codigo de arduino de nombre **prueba_boton_color.ino**
``` C
//Aqui se definen las librerias para acceder a Internet dependiedo del modulo que se use para controlar los sensores
#ifdef ESP32
  #include <WiFi.h>
  #include <HTTPClient.h>
#else
  #include <ESP8266WiFi.h>
  #include <ESP8266HTTPClient.h>
  #include <WiFiClient.h>
#endif

#include <Wire.h>

//Declaramos el ssid y la contraseña de la red a la que se va conectar
const char* ssid = "nombre_de_tu_red";
const char* password = "contraseña_de_red";


//Se definen los pines del sensor, yo en mi caso use un sensor de color TCS230 
#define S0 12
#define S1 13 
#define S2 27
#define S3 14
#define sensorSalida 26

//Estos pines son del controlador de motores L298N
int IN3 = 4;
int IN4 = 2;

int estado = 0;
int estado_anterior = 0;

int Rojo_Frec = 0;
int Verde_Frec = 0;
int Azul_Frec = 0;

String color = "otro";

void setup() {
  pinMode(S0, OUTPUT);
  pinMode(S1, OUTPUT);
  pinMode(S2, OUTPUT);
  pinMode(S3, OUTPUT);
  pinMode(sensorSalida, INPUT);

  pinMode(IN3, OUTPUT);
  pinMode(IN4, OUTPUT);
  //pinMode(ledverde, OUTPUT);
  //pinMode(ledrojo, OUTPUT);
  
                           // Configura la escala de Frecuencia en 20%
  digitalWrite(S0,HIGH);
  digitalWrite(S1,LOW);
  
  Serial.begin(9600);

//Me empiezo a conectar a internet 
  WiFi.begin(ssid, password);
  Serial.println("Intentando conectar a la red");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Conexion completada, la direccion ip es: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if(WiFi.status()== WL_CONNECTED){
    WiFiClient client;
    HTTPClient http;

    // La URL de tu dominio
    http.begin("http://papelbano.000webhostapp.com/sensor_color.php");
    
    // Especificar el content type para el header del POST
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
                            // Configura el filtor ROJO para tomar lectura
    digitalWrite(S2,LOW);
    digitalWrite(S3,LOW);
    delay(100);
    Rojo_Frec= pulseIn(sensorSalida, LOW);
    Serial.print(" R= "); Serial.print(Rojo_Frec);
    delay(100);
                              // Configura el filtor VERDE para tomar lectura
    digitalWrite(S2,HIGH);
    digitalWrite(S3,HIGH);
    delay(100);
    Verde_Frec = pulseIn(sensorSalida, LOW);
    Serial.print(" V= "); Serial.print(Verde_Frec);
    delay(100);
                             // Configura el filtor AZUL para tomar lectura
    digitalWrite(S2,LOW);
    digitalWrite(S3,HIGH);
    delay(100);
    Azul_Frec = pulseIn(sensorSalida, LOW);
    Serial.print(" A= "); Serial.println(Azul_Frec);
    delay(100);

     //El sensor de color puede dar frecuencias variadas dependiendo de la cercania al objeto, la iluminacion del lugar y que tan grande o pequeño 
     //es el objeto que esta sensando pero aqui esta configurado que para cuando detecte estos valores lo tome en cuenta como blanco
    if (Rojo_Frec < 30 && Verde_Frec > 30 && Azul_Frec > 25){
    Serial.print(" . *** BLANCO **");
    int estado = 1;
    int estado_anterior = 0;
    if((estado==1) and (estado_anterior==0)){
      Serial.print(" . *** Motor Blanco **");
      //Si es blanco el motor girara 360° hacia un lado
      digitalWrite(IN3, HIGH);
      digitalWrite(IN4, LOW);
      delay(1500);
      digitalWrite(IN3, LOW);
      digitalWrite(IN4, LOW);
      estado_anterior = estado;
    }
  }
  if (Rojo_Frec > 28 && Verde_Frec > 40 && Azul_Frec < 45){
    Serial.print(" . *** AZUL **");
    int estado = 0;
    int estado_anterior = 1;
    if((estado==0) and (estado_anterior==1)){
      Serial.print(" . *** Motor azul **");
      //Si es blanco el motor girara 360° hacia el otro lado
      digitalWrite(IN3, LOW);
      digitalWrite(IN4, HIGH);
      delay(1500);
      digitalWrite(IN3, LOW);
      digitalWrite(IN4, LOW);
      estado_anterior = estado;
    }
  }

    // Preparar los datos POST a enviar
        String httpRequestData = "Rojo_Frec= " + String(Rojo_Frec) + "&Verde_Frec= " + String(Verde_Frec) + "&Azul_Frec= " + String(Azul_Frec) + "&color= " + String(color);
        delay(3000);

    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);

    // Envia los datos por POST
    int httpResponseCode = http.POST(httpRequestData);

    // Si la respuesta no dio error
    if (httpResponseCode>0) {
      Serial.print("HTTP Codigo de respuesta: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Codigo de error: ");
      Serial.println(httpResponseCode);
    }
    // Termina la conexion del POST
    http.end();


        

http.begin("http://papelbano.000webhostapp.com/Getstatus.php");
    
    // Especificar el content type para el header del POST
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpCode=http.GET();
  String payload=http.getString(); // get data from webhost continously
  Serial.println(payload);
  if(payload == "1")  // if data == 1 -> LED ON
  {
      //Si obtien un 1 encendara el sensor
      digitalWrite(S0,HIGH);
      digitalWrite(S1,LOW);
  }
   else if (payload == "0") // if data == 0 -> LED OFF
   {
      //Si obtiene un 0 apagara el sensor
      digitalWrite(S0,LOW);
      digitalWrite(S1,LOW);
    }
  delay(500);
  http.end();
 
     
     

     
  }else {
    Serial.println("Desconectado del wifi");
  }
  
} 
``` 

El siguiente codigo seria el de **Getstatus.php**
``` php
<?php
//En este caso no uso el archivo de conexion.php sino que dentro de este mismo archivo me conecto a la base de datos y le mando las instrucciones sql. 
//El orden de las conexiones seria: host, usuario, contraseña y nombre de la base de datos
$con = new mysqli("localhost","id19945353_joel","ABCabc123***","id19945353_espwifi");
/*En la base de datos tengo una tabla de nombre 'status' con solo dos campos, uno es el id de atributo 'auto_increment' y el otro
es status de tipo 'tyniint'*/
//En la siguiente linea voy a seleccionar el ultimo registro de la tabla ordenandola de forma descendiente 
$sql = "SELECT status from status ORDER BY id DESC LIMIT 1";
$res = $con->query($sql);
	
		while($row = $res->fetch_assoc()) 
		{
    //Con este ciclo while imprimo unicamente el status. Este mensaje es lo que va a leer el esp32
			echo $row["status"];
		}
?>
```

El siguiente archivo es el que recibe los datos del esp para insertarlos en la tabla. Este archivo es el que tiene por nombre **sensor_color.php**
```php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);

require 'conexion.php';
$obj = new BD_PDO();

//Recibo las variables desde el esp
$Rojo_Frec = $_POST['Rojo_Frec'];
$Verde_Frec = $_POST['Verde_Frec'];
$Azul_Frec = $_POST['Azul_Frec'];
/*date_default_timezone_set('america/matamoros');
$hora = date("Y-m-d H:i:s");*/
//Dependiendo la frecuencia de los colores la instruccion sql cambia el campo color
if ($Rojo_Frec < 30 && $Verde_Frec > 30 && $Azul_Frec > 25){
    $obj->Ejecutar_Instruccion("Insert into sensor_color (Rojo_Frec,Verde_Frec,Azul_Frec,color) VALUES ('$Rojo_Frec','$Verde_Frec','$Azul_Frec','Blanco')");
}

elseif ($Rojo_Frec > 28 && $Verde_Frec > 40 && $Azul_Frec < 45){
    $obj->Ejecutar_Instruccion("Insert into sensor_color (Rojo_Frec,Verde_Frec,Azul_Frec,color) VALUES ('$Rojo_Frec','$Verde_Frec','$Azul_Frec','Azul')");
}

//Cuando apague el sensor las frecuecias seran de 0 asi que con este if hago que ya no inserte nada en la base de datos
elseif ($Rojo_Frec == 0 && $Verde_Frec == 0 && $Azul_Frec == 0){
    
}

//Si no es ningun color que este configurado que en el campo color inserte 'otro'
else{
    $obj->Ejecutar_Instruccion("Insert into sensor_color (Rojo_Frec,Verde_Frec,Azul_Frec,color) VALUES ('$Rojo_Frec','$Verde_Frec','$Azul_Frec','otro')");
}



?>
```
