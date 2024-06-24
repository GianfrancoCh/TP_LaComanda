<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];
  
        $mesa = new Mesa();
        $mesa->estado = $estado;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $mesa = Mesa::obtenerMesa($id);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $estado = $parametros['estado'];
        Mesa::modificarMesa($id,$estado);

        $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public static function SubirCsv($request, $response)
    {
      if (Mesa::SubirMesaCsv()){
        $payload = json_encode(array("mensaje" => "Los datos del archivo se subieron correctamente!"));
      }else{
        $payload = json_encode(array("mensaje" => "Hubo un problema al subir los datos del archivo."));

      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public static function DescargarCsv($request, $response)
    {
      $request->getParsedBody();

      if ($csv = Mesa::DescargaDbCsv("db/mesasDb.csv"))
        $payload = json_encode(array("mensaje" => "Se escribio el archivo exitosamente"));
      else
        $payload = json_encode(array("mensaje" => "Hubo un problema al bajar los productos."));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

}
