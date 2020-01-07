# Prueba SunMedia
#### Instalación 

Añadir al /etc/host la siguiente linea:
```sh
127.0.0.1	prueba.localhost
```

Dentro de la carpeta del proyecto levantamos el contenedor docker:
```sh 
docker-composer build
docker-composer up
```


#### Endpoints creados:
* [GET] http://prueba.localhost/communications -> Obtener comunicationes de un numero.
```
{
	"number" : "6111111111"
}
```
Respuesta: se ha hecho una proyección acorde a como queremos mostrar la información al usuario
 ```

{
    "telephoneNumber": "6111111111",
    "communication": [
        {
            "contactNumber": "600999888",
            "smss": [],
            "calls": [
                {
                    "incoming": true,
                    "name": "Pepe                    ",
                    "date": "01012016205203",
                    "duration": "000142"
                }
            ]
        },
        {
            "contactNumber": "700111222",
            "smss": [
                {
                    "incoming": false,
                    "name": "Movistar                ",
                    "date": "02012016180130"
                }
            ],
            "calls": []
        }
    ]
}
```
