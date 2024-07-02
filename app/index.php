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
    $group->get('/obtenerLogs', \UsuarioController::class . ':ObtenerLogs')->add(new UsuarioSocioMiddleware());
    $group->get('/operacionesPorSector', \UsuarioController::class . ':OperacionesPorSector')->add(new UsuarioSocioMiddleware());
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new CrearUsuarioRolMiddleware())->add(new UsuarioSocioMiddleware());
    $group->put('/modificarestado', \UsuarioController::class . ':ModificarUno')->add(new UsuarioSocioMiddleware());
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('/masVendido', \PedidoController::class . ':MasVendido')->add(new UsuarioSocioMiddleware());
  $group->get('/menosVendido', \PedidoController::class . ':MenosVendido')->add(new UsuarioSocioMiddleware());
  $group->get('/entregadosTarde', \PedidoController::class . ':EntregadosTarde')->add(new UsuarioSocioMiddleware());
  $group->get('/cancelados', \PedidoController::class . ':ObtenerCancelados')->add(new UsuarioSocioMiddleware());
  $group->get('/id', \PedidoController::class . ':TraerUno');
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{sector}', \PedidoController::class . ':TraerSector');
  $group->put('/tomarproductopedido', \PedidoController::class . ':TomarProductoPedido')->add(new UsuarioRolMiddleware())->add(new ProductoEnPedidoMiddleware())->add(new ProductoIdMiddleware())->add(new PedidoIdMiddleware());
  $group->put('/listoproductopedido', \PedidoController::class . ':ListoProductoPedido')->add(new UsuarioRolMiddleware())->add(new ProductoEnPedidoMiddleware())->add(new ProductoIdMiddleware())->add(new PedidoIdMiddleware());
  $group->post('/tomarfoto', \PedidoController::class . ':TomarFoto')->add(new PedidoIdMiddleware())->add(new UsuarioMozoMiddleware());
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new ProductoIdMiddleware())->add(new MesaIdMiddleware())->add(new UsuarioMozoMiddleware());
  $group->put('/servir', \PedidoController::class . ':ServirPedido')->add(new PedidoIdMiddleware());
  $group->post('/pedircuenta', \FacturaController::class . ':CargarUna')->add(new PedidoIdMiddleware())->add(new UsuarioMozoMiddleware());
  $group->get('/verfactura/{id}', \FacturaController::class . ':verFacturaId');
  $group->post('/pagarcuenta', \FacturaController::class . ':PagarFactura')->add(new FacturaIdMiddleware())->add(new UsuarioMozoMiddleware());

});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('/csv', \MesaController::class . ':SubirCsv')->add(new UsuarioSocioMiddleware());
  $group->get('/csv', \MesaController::class . ':DescargarCsv')->add(new UsuarioSocioMiddleware());
  $group->get('/masUsada', \MesaController::class . ':MasUsada')->add(new UsuarioSocioMiddleware());
  $group->get('/menosUsada', \MesaController::class . ':MenosUsada')->add(new UsuarioSocioMiddleware());
  $group->get('/masFacturo', \MesaController::class . ':MasFacturo')->add(new UsuarioSocioMiddleware());
  $group->get('/menosFacturo', \MesaController::class . ':MenosFacturo')->add(new UsuarioSocioMiddleware());
  $group->get('/mayorImporte', \MesaController::class . ':MayorImporte')->add(new UsuarioSocioMiddleware());
  $group->get('/menorImporte', \MesaController::class . ':MenorImporte')->add(new UsuarioSocioMiddleware());
  $group->get('/mejorPuntuacion', \MesaController::class . ':MejorPuntuacion')->add(new UsuarioSocioMiddleware());
  $group->get('/menorPuntuacion', \MesaController::class . ':MenorPuntuacion')->add(new UsuarioSocioMiddleware());
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{mesas}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(new UsuarioSocioMiddleware());
  $group->put('/cerrarMesa', \MesaController::class . ':ModificarUno')->add(new UsuarioSocioMiddleware());
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
