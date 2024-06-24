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
require_once './controllers/EncuestaController.php';
require_once './controllers/MesaController.php';
require_once './controllers/FacturaController.php';
require_once './utils/AutentificadorJWT.php';

require_once './middlewares/UsuarioMiddleware.php';
require_once './middlewares/AuthMiddleware.php';
require_once './middlewares/ProductoMiddleware.php';
require_once './middlewares/MesaMiddleware.php';
require_once './middlewares/PedidoMiddleware.php';

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
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new CrearUsuarioRolMiddleware())->add(new UsuarioSocioMiddleware());
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{sector}', \PedidoController::class . ':TraerSector');
  $group->put('/tomarproductopedido', \PedidoController::class . ':TomarProductoPedido')->add(new UsuarioRolMiddleware())->add(new ProductoEnPedidoMiddleware())->add(new ProductoIdMiddleware())->add(new PedidoIdMiddleware());
  $group->put('/listoproductopedido', \PedidoController::class . ':ListoProductoPedido')->add(new UsuarioRolMiddleware())->add(new ProductoEnPedidoMiddleware())->add(new ProductoIdMiddleware())->add(new PedidoIdMiddleware());
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new ProductoIdMiddleware())->add(new MesaIdMiddleware())->add(new UsuarioMozoMiddleware());
  $group->post('/pedircuenta', \FacturaController::class . ':CargarUna')->add(new PedidoIdMiddleware())->add(new UsuarioMozoMiddleware());
  $group->post('/pagarcuenta', \FacturaController::class . ':PagarFactura')->add(new FacturaIdMiddleware())->add(new UsuarioMozoMiddleware());

});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('/csv', \MesaController::class . ':SubirCsv')->add(new UsuarioSocioMiddleware());
  $group->get('/csv', \MesaController::class . ':DescargarCsv')->add(new UsuarioSocioMiddleware());
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{mesas}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(new UsuarioSocioMiddleware());
  
});

$app->group('/encuesta', function (RouteCollectorProxy $group) {
	$group->post('[/]', \EncuestaController::class . ':CargarUno')->add(new EncuestaMiddleware())->add(new PedidoIdMiddleware());;
	$group->get('[/]', \EncuestaController::class . ':TraerTodos');
});

$app->group('/auth', function (RouteCollectorProxy $group) {

  $group->post('/login', \UsuarioController::class . ':Login')->add(new UsuarioLoginMiddleware());

});


$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Bienvenido!"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
