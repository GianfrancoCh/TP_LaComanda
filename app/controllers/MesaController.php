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


    public static function MasUsada($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, COUNT(*) AS cantidad
            FROM pedidos ped
            WHERE ped.fecha BETWEEN :fecha AND :fecha2
            GROUP BY ped.id_mesa
            ORDER BY cantidad DESC
            LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else if(isset($parametros['fecha'])){

        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, COUNT(*) AS cantidad
            FROM pedidos ped
            WHERE ped.fecha = :fecha
            GROUP BY ped.id_mesa
            ORDER BY cantidad DESC
            LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else{
          $mensaje = "Faltan campos";
          $payload = json_encode(array("mensaje" => $mensaje));
          $response->getBody()->write($payload);
      }

      return $response->withHeader('Content-Type', 'application/json');
    }

    public static function MenosUsada($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, COUNT(*) AS cantidad
            FROM pedidos ped
            WHERE ped.fecha BETWEEN :fecha AND :fecha2
            GROUP BY ped.id_mesa
            ORDER BY cantidad ASC
            LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else if(isset($parametros['fecha'])){

        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, COUNT(*) AS cantidad
            FROM pedidos ped
            WHERE ped.fecha = :fecha
            GROUP BY ped.id_mesa
            ORDER BY cantidad ASC
            LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else{
          $mensaje = "Faltan campos";
          $payload = json_encode(array("mensaje" => $mensaje));
          $response->getBody()->write($payload);
      }

      return $response->withHeader('Content-Type', 'application/json');
    }

    public static function MasFacturo($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, SUM(ped.precio) AS total
          FROM pedidos ped
          WHERE ped.fecha BETWEEN :fecha AND :fecha2
          GROUP BY ped.id_mesa
          ORDER BY total DESC
          LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else if(isset($parametros['fecha'])){

        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, SUM(ped.precio) AS total
          FROM pedidos ped
          WHERE ped.fecha = :fecha
          GROUP BY ped.id_mesa
          ORDER BY total DESC
          LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else{
          $mensaje = "Faltan campos";
          $payload = json_encode(array("mensaje" => $mensaje));
          $response->getBody()->write($payload);
      }

      return $response->withHeader('Content-Type', 'application/json');
    }

    public static function MenosFacturo($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, SUM(ped.precio) AS total
          FROM pedidos ped
          WHERE ped.fecha BETWEEN :fecha AND :fecha2
          GROUP BY ped.id_mesa
          ORDER BY total ASC
          LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else if(isset($parametros['fecha'])){

        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, SUM(ped.precio) AS total
          FROM pedidos ped
          WHERE ped.fecha = :fecha
          GROUP BY ped.id_mesa
          ORDER BY total ASC
          LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else{
          $mensaje = "Faltan campos";
          $payload = json_encode(array("mensaje" => $mensaje));
          $response->getBody()->write($payload);
      }

      return $response->withHeader('Content-Type', 'application/json');
    }

    public static function MayorImporte($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT id_mesa, precio AS mayor_importe
        FROM pedidos
        WHERE fecha BETWEEN :fecha_ini AND :fecha_fin
        AND precio = (SELECT MAX(precio) FROM pedidos WHERE fecha BETWEEN :fecha_ini2 AND :fecha_fin2)");
        $consulta->bindParam(':fecha_ini', $parametros['fecha']);
        $consulta->bindParam(':fecha_fin', $parametros['fecha2']);
        $consulta->bindParam(':fecha_ini2', $parametros['fecha']);
        $consulta->bindParam(':fecha_fin2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else if(isset($parametros['fecha'])){

        $consulta = $objAccesoDatos->PrepararConsulta("SELECT id_mesa, precio AS mayor_importe FROM pedidos
          WHERE fecha = :fecha
          AND precio = (SELECT MAX(precio) FROM pedidos WHERE fecha = :fecha2)");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else{
          $mensaje = "Faltan campos";
          $payload = json_encode(array("mensaje" => $mensaje));
          $response->getBody()->write($payload);
      }

      return $response->withHeader('Content-Type', 'application/json');
    }

    public static function MenorImporte($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT id_mesa, precio AS mayor_importe
        FROM pedidos
        WHERE fecha BETWEEN :fecha_ini AND :fecha_fin
        AND precio = (SELECT MIN(precio) FROM pedidos WHERE fecha BETWEEN :fecha_ini2 AND :fecha_fin2)");
        $consulta->bindParam(':fecha_ini', $parametros['fecha']);
        $consulta->bindParam(':fecha_fin', $parametros['fecha2']);
        $consulta->bindParam(':fecha_ini2', $parametros['fecha']);
        $consulta->bindParam(':fecha_fin2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else if(isset($parametros['fecha'])){

        $consulta = $objAccesoDatos->PrepararConsulta("SELECT id_mesa, precio AS mayor_importe FROM pedidos
          WHERE fecha = :fecha
          AND precio = (SELECT MIN(precio) FROM pedidos WHERE fecha = :fecha2)");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else{
          $mensaje = "Faltan campos";
          $payload = json_encode(array("mensaje" => $mensaje));
          $response->getBody()->write($payload);
      }

      return $response->withHeader('Content-Type', 'application/json');
    }


    public static function MejorPuntuacion($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, AVG(enc.puntuacionMesa) AS promedio_puntuacion
          FROM encuestas enc
          JOIN pedidos ped ON enc.id_pedido = ped.id
          WHERE ped.fecha BETWEEN :fecha AND :fecha2
          GROUP BY ped.id_mesa
          ORDER BY promedio_puntuacion DESC
          LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else if(isset($parametros['fecha'])){

        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, AVG(enc.puntuacionMesa) AS promedio_puntuacion
          FROM encuestas enc
          JOIN pedidos ped ON enc.id_pedido = ped.id
          WHERE ped.fecha = :fecha
          GROUP BY ped.id_mesa
          ORDER BY promedio_puntuacion DESC
          LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else{
          $mensaje = "Faltan campos";
          $payload = json_encode(array("mensaje" => $mensaje));
          $response->getBody()->write($payload);
      }

      return $response->withHeader('Content-Type', 'application/json');
    }

    public static function MenorPuntuacion($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, AVG(enc.puntuacionMesa) AS promedio_puntuacion
          FROM encuestas enc
          JOIN pedidos ped ON enc.id_pedido = ped.id
          WHERE ped.fecha BETWEEN :fecha AND :fecha2
          GROUP BY ped.id_mesa
          ORDER BY promedio_puntuacion ASC
          LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else if(isset($parametros['fecha'])){

        $consulta = $objAccesoDatos->PrepararConsulta("SELECT ped.id_mesa, AVG(enc.puntuacionMesa) AS promedio_puntuacion
          FROM encuestas enc
          JOIN pedidos ped ON enc.id_pedido = ped.id
          WHERE ped.fecha = :fecha
          GROUP BY ped.id_mesa
          ORDER BY promedio_puntuacion ASC
          LIMIT 1");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));

      }else{
          $mensaje = "Faltan campos";
          $payload = json_encode(array("mensaje" => $mensaje));
          $response->getBody()->write($payload);
      }

      return $response->withHeader('Content-Type', 'application/json');
    }

}
