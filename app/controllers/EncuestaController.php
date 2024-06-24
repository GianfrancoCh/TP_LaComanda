<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
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
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];
        Usuario::modificarUsuario($id,$usuario,$clave,$rol);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


}
