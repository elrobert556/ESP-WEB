#ifdef ESP32
  #include <WiFi.h>
  #include <HTTPClient.h>
#else
  #include <ESP8266WiFi.h>
  #include <ESP8266HTTPClient.h>
  #include <WiFiClient.h>
#endif

#include <Wire.h>

const char* ssid = "iPhone de Joel";
const char* password = "tlvm5873";

#define S0 12
#define S1 13 
#define S2 27
#define S3 14
#define sensorSalida 26

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


    if (Rojo_Frec < 30 && Verde_Frec > 30 && Azul_Frec > 25){
    Serial.print(" . *** BLANCO **");
    int estado = 1;
    int estado_anterior = 0;
    if((estado==1) and (estado_anterior==0)){
      Serial.print(" . *** Motor Blanco **");
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


        

http.begin("http://papelbano.000webhostapp.com/apagar_sensor/Getstatus.php");
    
    // Especificar el content type para el header del POST
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpCode=http.GET();
  String payload=http.getString(); // get data from webhost continously
  Serial.println(payload);
  if(payload == "1")  // if data == 1 -> LED ON
  {
      /*digitalWrite(IN3, HIGH);
      digitalWrite(IN4, LOW);*/
      digitalWrite(S0,HIGH);
      digitalWrite(S1,LOW);
  }
   else if (payload == "0") // if data == 0 -> LED OFF
   {
      /*digitalWrite(IN3, LOW);
      digitalWrite(IN4, HIGH);*/
      digitalWrite(S0,LOW);
      digitalWrite(S1,LOW);
    }
  delay(500);
  http.end();
 
     
     

     
  }else {
    Serial.println("Desconectado del wifi");
  }
  
}
