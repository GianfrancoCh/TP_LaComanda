<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
        $puntuacionMozo = $parametros['puntuacionMozo'];
        $puntuacionRestaurante = $parametros['puntuacionRestaurante'];
        $puntuacionMesa = $parametros['puntuacionMesa'];
        $puntuacionCocinero = $parametros['puntuacionCocinero'];
        $comentarios = $parametros['comentarios'];


        $encuesta = new Encuesta();
        $encuesta->id_pedido = $id_pedido;
        $encuesta->puntuacionMozo = $puntuacionMozo;
        $encuesta->puntuacionRestaurante = $puntuacionRestaurante;
        $encuesta->puntuacionMesa = $puntuacionMesa;
        $encuesta->puntuacionCocinero = $puntuacionCocinero;
        $encuesta->comentarios = $comentarios;

        $encuesta->crearEncuesta();

        $payload = json_encode(array("mensaje" => "Encuesta creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $nombre = $args['nombre'];
        $producto = Producto::obtenerProducto($nombre);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Encuesta::TraerTodasLasEncuestas();
        $payload = json_encode(array("encuestas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    

    public static function MejoresComentarios($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      
      $consulta = $objAccesoDatos->PrepararConsulta("SELECT *, (puntuacionMesa + puntuacionMozo + puntuacionRestaurante + puntuacionCocinero) AS puntuacionTotal
          FROM encuestas
          ORDER BY puntuacionTotal DESC
          LIMIT 1");

      $consulta->execute();
      $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
      $response->getBody()->write(json_encode($resultado));

      
      return $response->withHeader('Content-Type', 'application/json');
    }


}
