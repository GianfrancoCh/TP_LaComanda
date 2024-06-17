<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;


require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';
require_once './utils/AutentificadorJWT.php';

require_once './middlewares/RolUsuariosMiddleware.php';
require_once './middlewares/AuthMiddleware.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new UsuarioRolMiddleware());
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{sector}', \PedidoController::class . ':TraerSector');
  $group->post('[/]', \PedidoController::class . ':CargarUno');
});


$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{mesas}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');

});

$app->group('/auth', function (RouteCollectorProxy $group) {

  $group->post('/login', \UsuarioController::class . ':Login')->add(new UsuarioLoginMiddleware());

});
// $app->group('/auth', function (RouteCollectorProxy $group) {

//   $group->post('/login', function (Request $request, Response $response) {    
//     $parametros = $request->getParsedBody();

//     $usuario = $parametros['usuario'];
//     $contrasena = $parametros['contrasena'];
//     $rol = $parametros['rol'];

//     if($usuario == 'gian' && $contrasena == '1234'){ 
//       $datos = array('usuario' => $usuario, 'rol' => $rol);

//       $token = AutentificadorJWT::CrearToken($datos);
//       $payload = json_encode(array('jwt' => $token));
//     } else {
//       $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
//     }

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });

// });





$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Bienvenido!"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
