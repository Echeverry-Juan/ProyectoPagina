<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Header;

require __DIR__. '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->add( function ($request, $handler) {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PUT, PATCH, DELETE')
        ->withHeader('Content-Type', 'application/json')
    ;
});

    
    function enlace(){
        $enlace = mysqli_connect("db", "seminariophp", "seminariophp", "seminariophp");

        if (!$enlace) {
            echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
            echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
            echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }

        return $enlace;
    }
    
// ACÁ VAN LOS ENDPOINTS


//Establezco la zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');



//VERIFICACIONES

//Verificacion de nombre
function verificarnombre($nombre_usuario){
    if(strlen($nombre_usuario)>= 6 and strlen($nombre_usuario) < 20 and ctype_alnum($nombre_usuario)){
        return true;
    }else{
    return false;
    }
}
// verificacion de contraseña
function verificarcontraseña($contraseña){
    $regularexpresion= "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";
    if(preg_match($regularexpresion,$contraseña)){
        return true;
    }else{
    return false;
    }
}

//verificacion juego
function convertirimagen($imagen){
    return base64_encode($imagen);
}

//verificar nombre del juego
function verificarnombrejuego($nombrejuego){
    if(strlen($nombrejuego)< 45){
        return true;
    }else{
       return false;
    }
}

//verificar clasificacion de juego
function verificacionclas($clasificacion_edad){
    switch ($clasificacion_edad) {
        case 'ATP':
            return true;
            break;
        case '+13':
            return true;
            break;
        case '+18':
            return true;
            break;
    }
    return false;
}

function verificacionjuego($nombrejuego,$clasificacion_edad){
    if(verificarnombrejuego($nombrejuego)== true and verificacionclas($clasificacion_edad)== true){
        return true;
    }else{
        return false;
    }
    
}

//verificacion de estrellas para la calificacion
function verificarestrellas($estrellas){
    if($estrellas>=1 and $estrellas<=5){
    return true;
    }else{
        return false;
    }
}

//verifica si existe el dato en la base de datos
function verificarsiexiste($dato){
    if($row_count= mysqli_num_rows($dato)>=1){
        return true;
    }else{
        return false;
    }
}

//verifica si no existe el dato en la base de datos
function verificarsinoexiste($dato){
    if($row_count= mysqli_num_rows($dato)<1){
        return true;
    }else{
        return false;
    }
}

//GENERAR TOKEN
function generartoken(){
    $leng=5;
    $token='';
    $cadena='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    for($i=0 ; $i < $leng; $i++ ){
        $token.= $cadena[rand(0,35)];
    }
    return $token;
}



function horaactual(){
    return date("Y-m-d H:i:s");
}

//tiempo de duracion del token 1 hora
function generardate(){
    $datemas="";
    $datemas.=date("Y-m-d H:i:s",strtotime(horaactual().' + 1 hour'));
    return $datemas;
}


// Login, verificar credenciales y retornar token
$app->post('/login', function(Request $request, Response $response, $args){
    $conexion= enlace();
    $datos= $request->getParsedBody();
    if(isset($datos['nombre_usuario'])){
        $nombre_usuario= $datos['nombre_usuario'];
    }else{
        $nombre_usuario=null;
    }
    if(isset($datos['clave'])){
        $clave= $datos['clave'];
    }else{
        $clave=null;
    }
    if($nombre_usuario!=null && $clave!=null){
    $verificacion= mysqli_query($conexion, "SELECT * FROM usuario WHERE nombre_usuario = '$nombre_usuario' AND clave = '$clave'");
    if(verificarsiexiste($verificacion)){
        $token= '';
        $token= generartoken();//generar token
        $horamas1= generardate(); //genero el tiempo que va a durar el token
        mysqli_query($conexion,"UPDATE usuario SET token = '$token', vencimiento_token = '$horamas1'  WHERE usuario.nombre_usuario = '$nombre_usuario' ");
        $payload = json_encode($token);
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }else{
        $payload = json_encode('El nombre de usuario o clave son incorrectos');
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
    }else{
        $payload = json_encode('Debe Ingresar el nombre de usuario y clave');
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
    return $response;
});

// Agregar nuevo usuario con su nombre de usuario y clave
$app->post('/register', function(Request $request, Response $response, $args){
    $conexion= enlace();
    $datos= $request->getParsedBody();
    if(isset($datos['nombre_usuario'])){
        $nombre_usuario= $datos['nombre_usuario'];
    }else{
        $nombre_usuario=null;
    }
    if(isset($datos['clave'])){
        $clave= $datos['clave'];
    }else{
        $clave=null;
    }
    if($nombre_usuario!=null && $clave!=null){
    $verificacion= mysqli_query($conexion, "SELECT * FROM usuario WHERE nombre_usuario LIKE '$nombre_usuario'");
    if(verificarsinoexiste($verificacion)){
        if(verificarnombre($nombre_usuario) == true && verificarcontraseña($clave)== true){      
            mysqli_query($conexion,"INSERT INTO usuario (nombre_usuario, clave, token, vencimiento_token, es_admin) VALUES ('$nombre_usuario', '$clave', NULL, NULL, 0)");
            $payload = json_encode('Se ha registrado con los datos ingresados');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }else{
            $payload = json_encode("Su nombre de usuario o clave no cumplen con los requisitos pedidos");
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }else{
        $response->getBody()->write('El nombre de usuario ya existe');
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
    }else{
        $response->getBody()->write('Debe ingresar ambos datos');
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
    return $response;
});

//Crear Usuario
$app->post('/usuario', function(Request $request, Response $response, $args){
    $conexion= enlace();
    $datos= $request->getParsedBody();
    if(isset($datos['nombre_usuario'])){
        $nombre_usuario= $datos['nombre_usuario'];
    }else{
        $nombre_usuario=null;
    }
    if(isset($datos['clave'])){
        $clave= $datos['clave'];
    }else{
        $clave=null;
    }
    if($nombre_usuario!=null && $clave!=null){
    $verificacion= mysqli_query($conexion, "SELECT * FROM usuario WHERE nombre_usuario LIKE '$nombre_usuario'");
    if(verificarsinoexiste($verificacion)){
        if(verificarnombre($nombre_usuario) == true && verificarcontraseña($clave)== true){      
            mysqli_query($conexion,"INSERT INTO usuario (nombre_usuario, clave, token, vencimiento_token, es_admin) VALUES ('$nombre_usuario', '$clave', NULL, NULL, 0)");
            $payload = json_encode('Se ha creado un usuario con los datos ingresados');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }else{
            $payload = json_encode("Su contraseña o clave no cumplen con los requisitos pedidos");
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }else{
        $response->getBody()->write('El nombre de usuario ya existe');
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
    }else{
        $response->getBody()->write('Debe ingresar ambos datos');
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
    return $response;
});


//Editar un usuario existente (se necesita saber si el usuario se logueo)
$app->put('/usuario/{id}', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $idact= $request->getAttribute('id');
    $datos= $request->getParsedBody();
    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $updatedatos="";
    if(isset($datos['nombre_usuario'])){
        $nombre_usuario= $datos['nombre_usuario'];
            if(strlen($updatedatos)<1){
            $updatedatos.= "UPDATE usuario SET nombre_usuario = '$nombre_usuario'";
            }else{
            $updatedatos.= ", nombre_usuario = '$nombre_usuario'";
            } 
    }else{
        $nombre_usuario=null;
    }
    if(isset($datos['clave'])){
        $clave= $datos['clave'];
            if(strlen($updatedatos)<1){
                $updatedatos.= "UPDATE usuario SET clave = '$clave'";
            }else{
                $updatedatos.= ", clave = '$clave'";
            }
    }else{
        $clave=null;
    }
    $hora=horaactual();
    if($token!=null){
        $verificacion= mysqli_query($conexion,"SELECT * FROM usuario WHERE id = '$idact' AND token= '$token' AND vencimiento_token > '$hora'");  
        if(verificarsiexiste($verificacion)){
            if(strlen($updatedatos)>0){
                    if($nombre_usuario!=null and $clave!=null){
                        $verificacionnombre= mysqli_query($conexion,"SELECT * FROM usuario WHERE nombre_usuario= '$nombre_usuario'");
                        if(verificarnombre($nombre_usuario)== true and verificarcontraseña($clave)==true and verificarsinoexiste($verificacionnombre)== true){
                            mysqli_query($conexion, "$updatedatos WHERE id = '$idact'");
                            $payload = json_encode('Se edito el usuario');
                            $response->getBody()->write($payload);
                            return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
                
                        }else{
                            $payload = json_encode('el nombre de usuario o contrasenia es incorrecto o ya existe un usuario con ese nombre');
                            $response->getBody()->write($payload);
                            return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus(400);
                        }
                    }else{
                        if($nombre_usuario!=null){
                            $verificacionnombre= mysqli_query($conexion,"SELECT * FROM usuario WHERE nombre_usuario= '$nombre_usuario'");
                            if(verificarnombre($nombre_usuario)==true and verificarsinoexiste($verificacionnombre)== true){
                                mysqli_query($conexion, "$updatedatos WHERE id = '$idact'");
                                $payload = json_encode('Se edito el usuario');
                                $response->getBody()->write($payload);
                                return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus(200);
                
                            }else{
                                $payload = json_encode('El nombre de usuario es incorrecto o ya existe');
                                $response->getBody()->write($payload);
                                return $response
                                    ->withHeader('Content-Type', 'application/json')
                                    ->withStatus(400);
                            }
                        }else{
                            if($clave!=null){
                                if(verificarcontraseña($clave)== true){
                                mysqli_query($conexion, "$updatedatos WHERE id = '$idact'");
                                $payload = json_encode('Se edito el usuario');
                                $response->getBody()->write($payload);
                                return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus(200);
                
                                }else{
                                    $payload = json_encode('La clave es incorrecta');
                                    $response->getBody()->write($payload);
                                    return $response
                                        ->withHeader('Content-Type', 'application/json')
                                        ->withStatus(400);
                                }
                            }
                        }
                    }
                }else{
                    $payload = json_encode('Debe ingresar los datos a editar');
                    $response->getBody()->write($payload);
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
                }           
        }else{
                $payload = json_encode('El usuario no esta logeado o el token es incorrecto');
                    $response->getBody()->write($payload);
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
        }
    }else{
            $payload = json_encode('Debe ingresar el token');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
    }
    
    return $response;
});


//eliminar un usuario    (se necesita saber si el usuario se logueo)
$app->delete('/usuario/{id}', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $idact= $request->getAttribute('id');
    $datos= $request->getParsedBody('token');
    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $hora=horaactual();
    if($token!=null){
    $verificacion= mysqli_query($conexion, "SELECT * FROM usuario WHERE id = '$idact' AND token = '$token' AND vencimiento_token > '$hora' ");
    if(verificarsiexiste($verificacion)){
        $tienecalificaciones= mysqli_query($conexion,"SELECT * FROM calificacion WHERE usuario_id = '$idact'");
        if(verificarsinoexiste($tienecalificaciones)==true){
            mysqli_query($conexion,"DELETE FROM usuario WHERE usuario.id = '$idact'" );
            $payload = json_encode('Se ha eliminado el usuario');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }else{
            $payload = json_encode('El usuario posee calificaciones');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409);
        }
    }else{
        $payload = json_encode('El usuario no esta logeado o el token es incorrecto');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
    }
    }else{
        $payload = json_encode('Debe ingresar el token');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
    return $response;
});

//obtener informacion de un usuario especifico (se necesita saber si el usuario se logueo)
$app->get('/usuario/{id}', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $idact= $request->getAttribute('id');
    $datos= $request->getParsedBody('token');
    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $hora=horaactual();
    if($token!=null){
    $verificacion= mysqli_query($conexion,"SELECT * FROM usuario WHERE id = '$idact' AND token= '$token' AND vencimiento_token > '$hora'");
    if(verificarsiexiste($verificacion)){
            $result= mysqli_query($conexion,"SELECT nombre_usuario,clave,token,vencimiento_token FROM usuario WHERE id = $idact" );
            $datos= mysqli_fetch_all($result, MYSQLI_ASSOC);
            $payload = json_encode($datos);
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }else{
            $payload = json_encode('El usuario no esta logeado o el token es incorrecto');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }else{
        $payload = json_encode('Debe ingresar el token');
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
    return $response;
});
// Fin Usuario

// JUEGOS

//Listar los juegos de la página según los parámetros de búsqueda incluyendo la puntuación promedio del juego.
$app->get('/juegos', function (Request $request, Response $response, $args){
    $conexion= enlace(); 
    $datos = $request->getQueryParams();

    //j.nombre,j.descripcion,j.clasificacion_edad,j.imagen
    $pagina1="LIMIT 0,5";
    $consulta="SELECT j.id,j.nombre,j.descripcion,j.clasificacion_edad,j.imagen,p.nombre AS plataforma, AVG(c.estrellas) AS puntuacion_promedio FROM juego j LEFT JOIN calificacion c ON j.id = c.juego_id LEFT JOIN soporte s ON j.id = s.juego_id LEFT JOIN plataforma p ON s.plataforma_id = p.id";
    if($datos == null){
        $juegos= mysqli_query($conexion,"$consulta group by j.id, p.nombre order by puntuacion_promedio,j.nombre,j.id $pagina1"); 
        if(verificarsiexiste($juegos)){
        $datos= mysqli_fetch_all($juegos, MYSQLI_ASSOC);
        $payload = json_encode($datos);
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }else{
            $payload = json_encode('No hay juegos');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }else{
        $atributos="";
        if(isset($datos['plataforma'])){
            $plataforma=$datos['plataforma'];
            if(strlen($atributos > 0)){
                $atributos .= "AND p.nombre= '$plataforma' ";
            }else{
            $atributos .= " WHERE p.nombre= '$plataforma'";
            }
        }else{
            $plataforma=null;
        }
        
        if(isset($datos['clasificacion'])){
            $clasificacion=$datos['clasificacion'];
            if(strlen($atributos) > 0){
                if($clasificacion= "ATP"){
                    $atributos .= "AND j.clasificacion_edad = '$clasificacion'";
                    
                }else{
                    if($clasificacion= "13"){
                        $atributos .= "AND j.clasificacion_edad = '+13' OR  j.clasificacion_edad = 'ATP' ";
                        
                    }else{
                        if($clasificacion= "18"){
                            $atributos .= "AND j.clasificacion_edad = '+18' OR j.clasificacion_edad = 'ATP' OR j.clasificacion_edad = '+13'";
                            
                        }
                    }
                }
            }else{
                $atributos .=" WHERE j.clasificacion_edad = '$clasificacion'";
            }
        }else{
            $clasificacion=null;
        }
        if(isset($datos['texto'])){
            $texto=$datos['texto'];
            if(strlen($atributos) > 0){
                $atributos .= " AND j.nombre LIKE  '%$texto%' ";
            }else{
                $atributos .=" WHERE j.nombre LIKE '%$texto%'";
            }
        }else{
            $texto=null;
        }
        if(isset($datos['pagina'])){
            $pagina=$datos['pagina'];
            $registros= 5;
            if(is_numeric($pagina)){
                $inicio= (($pagina-1)*$registros);
                $paginacion = "LIMIT $inicio,$registros";
            }
        }else{
            $paginacion="";
            $pagina=null;
        }
        $consulta.=$atributos;
            if(strlen($paginacion)>0 && ctype_digit($pagina)){
                $juegos= mysqli_query($conexion, " $consulta group by j.id, p.nombre order by puntuacion_promedio,j.nombre,j.id $paginacion ");
            }else{
                $juegos= mysqli_query($conexion, " $consulta group by j.id, p.nombre order by puntuacion_promedio,j.nombre,j.id $pagina1 ");
            }
            if(verificarsiexiste($juegos)){
            $data= mysqli_fetch_all($juegos, MYSQLI_ASSOC);
            $payload = json_encode($data);
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
            }else{
                $payload = json_encode('No existe el juego');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
        } 
    return $response;
});



//Obtener juego por id *
$app->get('/juegos/{id}', function (Request $request, Response $response, $args) {
    $conexion = enlace();
    $idact = $request->getAttribute('id');
    $result = mysqli_query($conexion, "SELECT j.id as id, j.nombre, j.descripcion, j.clasificacion_edad, j.imagen, p.nombre AS plataforma, AVG(c.estrellas) AS puntuacion_promedio FROM juego j LEFT JOIN calificacion c ON j.id = c.juego_id LEFT JOIN soporte s ON j.id = s.juego_id LEFT JOIN plataforma p ON s.plataforma_id = p.id WHERE j.id = '$idact' GROUP BY j.id, p.nombre ORDER BY puntuacion_promedio, j.nombre, j.id");
    $result2 = mysqli_query($conexion, "SELECT c.estrellas, c.usuario_id as usuario, c.id as idcalificacion FROM calificacion c WHERE juego_id = '$idact'");

    if (verificarsinoexiste($result2)) {
        if (verificarsiexiste($result)) {
            $datosJuego = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $response->getBody()->write(json_encode(['juego' => $datosJuego[0], 'calificaciones' => []]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'No existe el juego']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    } else {
        if (verificarsiexiste($result)) {
            $datosJuego = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $datosCalificaciones = mysqli_fetch_all($result2, MYSQLI_ASSOC);

            // Responder con los datos estructurados
            $response->getBody()->write(json_encode(['juego' => $datosJuego[0], 'calificaciones' => $datosCalificaciones]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'No existe el juego']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }
});


//Agregar juego nuevo (el usuario debe ser administrador)
$app->post('/juego', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $datos= $request->getParsedBody();
    if(isset($datos['nombre'])){
        $nombrejuego= $datos['nombre'];
    }else{
        $nombrejuego=null;
    }
    if(isset($datos['descripcion'])){
        $descripcion= $datos['descripcion'];
    }else{
        $descripcion=null;
    }
    if(isset($datos['imagen'])){
        $imagen= convertirimagen($datos['imagen']);
    }else{
        $imagen=null;
    }
    if(isset($datos['clasificacion'])){
        $clasificacion_edad= $datos['clasificacion'];
    }else{
        $clasificacion_edad=null;
    }

    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $hora=horaactual();
    if($token!=null){
        $verificacion= mysqli_query($conexion, "SELECT * FROM usuario WHERE  token = '$token' AND vencimiento_token > '$hora' AND es_admin = 1");    
        if(verificarsiexiste($verificacion)){
         if($nombrejuego!=null && $descripcion!=null && $imagen!=null && $clasificacion_edad!=null){
            $existeeljuego= mysqli_query($conexion, "SELECT * FROM juego WHERE nombre = '$nombrejuego'");
            if(verificarsinoexiste($existeeljuego) and verificacionjuego($nombrejuego,$clasificacion_edad)== true){
                mysqli_query($conexion, "INSERT INTO juego(nombre, descripcion, imagen, clasificacion_edad) VALUES ('$nombrejuego', '$descripcion', '$imagen', '$clasificacion_edad')");
                $payload = json_encode('Se creo un nuevo juego');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }else{
                $payload = json_encode('El juego ya existe o el nombre u La clasificacion del juego es incorrecto');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
                
            }
        }else{
            $payload = json_encode('Debe Ingresar todos los datos sobre el juego');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }else{
        $payload = json_encode('El usuario no esta logeado , no es administrador o el token es incorrecto');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
    }
    }else{
        $payload = json_encode('Debe ingresar el token');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
    return $response;
});

//editar juego
$app->put('/juego/{id}', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $datos= $request->getParsedBody();
    $idjuego= $request->getAttribute('id');
    $updatedatos="";
    if(isset($datos['nombre'])){
        $nombrejuego= $datos['nombre'];
        if(strlen($updatedatos)<1){
            $updatedatos.="UPDATE juego set nombre= '$nombrejuego'";
        }else{
            $updatedatos.=" , nombre = '$nombrejuego'";
        }
    }else{
        $nombrejuego=null;
    }
    if(isset($datos['descripcion'])){
        $descripcion= $datos['descripcion'];
        if(strlen($updatedatos)<1){
            $updatedatos.="UPDATE juego set descripcion = '$descripcion'";
        }else{
            $updatedatos.=" , descripcion = '$descripcion'";
        }
    }else{
        $descripcion=null;
    }
    if(isset($datos['imagen'])){
        $imagen= convertirimagen($datos['imagen']);
        if(strlen($updatedatos)<1){
            $updatedatos.="UPDATE juego set imagen = '$imagen'";
        }else{
            $updatedatos.=" , imagen = '$imagen'";
        }
    }else{
        $imagen=null;
    }
    if(isset($datos['clasificacion'])){
        $clasificacion_edad= $datos['clasificacion'];
        if(strlen($updatedatos)<1){
            $updatedatos.="UPDATE juego set clasificacion_edad = '$clasificacion_edad'";
        }else{
            $updatedatos.=" , clasificacion_edad = '$clasificacion_edad'";
        }
    }else{
        $clasificacion_edad=null;
    }
    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $hora=horaactual();
    if($token!=null){
        $verificacion= mysqli_query($conexion, "SELECT * FROM usuario WHERE  token = '$token' AND vencimiento_token > '$hora' AND es_admin = 1");    
    if(verificarsiexiste($verificacion)){
            $existeeljuego= mysqli_query($conexion, "SELECT * FROM juego WHERE id = '$idjuego'");
            if(strlen($updatedatos)>0){
                if(verificarsiexiste($existeeljuego)){
                    if($nombrejuego!=null and $clasificacion_edad!=null){
                        $existeelnombre= mysqli_query($conexion, "SELECT * FROM juego WHERE nombre = '$nombrejuego'");
                        if(verificarsinoexiste($existeelnombre)== true && verificacionjuego($nombrejuego,$clasificacion_edad)== true){
                            mysqli_query($conexion, "$updatedatos WHERE juego.id = '$idjuego'");
                            $payload = json_encode('Se edito el juego');
                            $response->getBody()->write($payload);
                            return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
                
                        }else{
                            $payload = json_encode('El nombre o La clasificacion del juego es incorrecto o ya existe un juego con este nombre');
                            $response->getBody()->write($payload);
                            return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus(400);
                        }
                    }else{
                        if($nombrejuego!=null){
                            $existeelnombre= mysqli_query($conexion, "SELECT * FROM juego WHERE nombre = '$nombrejuego'");
                            if(verificarsinoexiste($existeelnombre)== true && verificarnombrejuego($nombrejuego)== true){
                                mysqli_query($conexion, "$updatedatos WHERE juego.id = '$idjuego'");
                                $payload = json_encode('Se edito el juego');
                                $response->getBody()->write($payload);
                                return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus(200);
                
                            }else{
                                $payload = json_encode('El nombre del juego es incorrecto o ya existe un juego con este nombre');
                                $response->getBody()->write($payload);
                                return $response
                                    ->withHeader('Content-Type', 'application/json')
                                    ->withStatus(400);
                            }
                        }else{
                            if($clasificacion_edad!=null){
                                if(verificacionclas($clasificacion_edad)== true){
                                mysqli_query($conexion, "$updatedatos WHERE juego.id = '$idjuego'");
                                $payload = json_encode('Se edito el juego');
                                $response->getBody()->write($payload);
                                return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus(200);
                
                                }else{
                                    $payload = json_encode('La clasificacion del juego es incorrecta');
                                    $response->getBody()->write($payload);
                                    return $response
                                        ->withHeader('Content-Type', 'application/json')
                                        ->withStatus(400);
                                }
                            }
                        }
                    }
                    mysqli_query($conexion, "$updatedatos WHERE juego.id = '$idjuego'");
                        $payload = json_encode('Se edito el juego');
                        $response->getBody()->write($payload);
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
                
                }else{
                    $payload = json_encode('EL juego no existe');
                    $response->getBody()->write($payload);
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
                
                }
            }else{
                $payload = json_encode('Debe ingresar los datos que se desea actualizar');
                    $response->getBody()->write($payload);
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
            }
        }else{
            $payload = json_encode('El usuario no esta logeado o no es administrador');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }else{
        $payload = json_encode('Debe ingresar el token');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
    return $response;
});


//borrar juego
$app->delete('/juego/{id}', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $datos= $request->getParsedBody();
    $idjuego= $request->getAttribute('id');
    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $hora=horaactual();
    if($token!=null){
    $verificacion= mysqli_query($conexion, "SELECT * FROM usuario WHERE  token = '$token' AND vencimiento_token > '$hora' AND es_admin = 1");
    if(verificarsiexiste($verificacion)){
            $existeeljuego= mysqli_query($conexion, "SELECT * FROM juego WHERE id = '$idjuego'");
            $tienecalificaciones= mysqli_query($conexion, "SELECT * FROM calificacion WHERE juego_id = '$idjuego'");
            if(verificarsiexiste($existeeljuego) and verificarsinoexiste($tienecalificaciones)){
                mysqli_query($conexion, "DELETE FROM juego WHERE id = '$idjuego'");
                $payload = json_encode('Se elimino el juego');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }else{
                $payload = json_encode('El juego no existe o tiene calificaciones');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(409);
            }
        }else{
            $payload = json_encode('El usuario no esta logeado o no es administrador');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }else{
        $payload = json_encode('Debe ingresar el token');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
    return $response;
});

// FIN JUEGOS


//Calificaciones


$app->post('/calificacion', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $datos= $request->getParsedBody();
    if(isset($datos['estrellas'])){
    $estrellas= $datos['estrellas'];
    }else{
        $estrellas=null;
    }
    if(isset($datos['idusuario'])){
        $idus= $datos['idusuario'];
    }else{
        $idus=null;
    }
    if(isset($datos['idjuego'])){
        $idjuego= $datos['idjuego'];
    }else{
        $idjuego=null;
    }
    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $hora=horaactual();
    if($token!=null){
    $verificacion= mysqli_query($conexion, "SELECT * FROM usuario WHERE id = '$idus' AND token = '$token' AND vencimiento_token > '$hora'");
    if(verificarsiexiste($verificacion)){
        if($estrellas!=null && $idus!=null && $idjuego!=null){
            $existeeljuego= mysqli_query($conexion, "SELECT * FROM juego WHERE id = '$idjuego'");
            $masdeuna=mysqli_query($conexion,"SELECT * FROM calificacion WHERE juego_id = '$idjuego' AND usuario_id = '$idus'");
            if(verificarestrellas($estrellas)== true && verificarsiexiste($existeeljuego) && verificarsinoexiste($masdeuna)== true){
                mysqli_query($conexion, "INSERT INTO calificacion (estrellas, usuario_id, juego_id) VALUES ('$estrellas', '$idus', '$idjuego')");
                $payload = json_encode('Se creo una nueva calificacion');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }else{
                $payload = json_encode('La calificacion es incorrecta , el id de juego es incorrecto o ya existe una calificacion para este juego con el mismo usuario');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
                
            }
        }else{
            $payload = json_encode('Debe ingresar todos los datos de la calificacion');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }else{
        $payload = json_encode('El usuario no esta logeado o el token es incorrecto');
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
    }else{
        $payload = json_encode('Debe ingresar el token');
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
    return $response;
});

//Editar Calificacion


$app->put('/calificacion/{id}', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $datos= $request->getParsedBody();
    $id= $request->getAttribute('id');
    if(isset($datos['estrellas'])){
        $estrellas= $datos['estrellas'];
    }else{
        $estrellas=null;
    }
    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $hora=horaactual();
    if($token!=null){
    $verificacion= mysqli_query($conexion, "SELECT * FROM calificacion c LEFT JOIN usuario u ON u.id = c.usuario_id WHERE c.id = '$id'  AND u.token = '$token' AND u.vencimiento_token > '$hora'");
    if(verificarsiexiste($verificacion)){
            if(verificarestrellas($estrellas)== true and $estrellas!=null){
                mysqli_query($conexion, "UPDATE calificacion SET estrellas = '$estrellas' WHERE calificacion.id = '$id'");
                $payload = json_encode('Se edito la calificacion');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }else{
                $payload = json_encode('Debe ingresar numero valido de estrellas');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(401);
                
            }
    }else{
        $payload = json_encode('La calificacion no existe, el token no coincide, o el usuario no esta logeado');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
    }
    }else{
        $payload = json_encode('Debe ingresar el token');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
    }
    return $response;
});


//Borrar calificacion
$app->delete('/calificacion/{id}', function (Request $request, Response $response, $args) {
    $conexion= enlace();
    $datos= $request->getParsedBody();
    $id= $request->getAttribute('id');
    if(isset($datos['token'])){
        $token= $datos['token'];
    }else{
        $token=null;
    }
    $hora=horaactual();
    if($token!=null){
        $verificacion= mysqli_query($conexion, "SELECT * FROM calificacion c LEFT JOIN usuario u ON u.id = c.usuario_id WHERE c.id = '$id'  AND u.token = '$token' AND u.vencimiento_token > '$hora'");
        if(verificarsiexiste($verificacion)){
                mysqli_query($conexion, "DELETE FROM calificacion WHERE id = '$id'");
                $payload = json_encode('Se elimino la calificacion');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
        }else{
            $payload = json_encode('El usuario no esta logeado, la calificacion no existe o el token es incorrecto');
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }else{
        $payload = json_encode('Debe ingresar el token');
                $response->getBody()->write($payload);
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
    }
    return $response;
});

//Fin calificaciones
    
$app->run();