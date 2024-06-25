<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->rol = $rol;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $estado = $parametros['estado'];
        Usuario::modificarUsuario($id,$estado);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function Login($request, $response)
    {
      $parametros = $request->getParsedBody();
      $id_usuario = $parametros['id'];
      $timestamp = date('Y-m-d H:i:s');

      $objAccesoDatos = AccesoDatos::obtenerInstancia();
      $consulta = $objAccesoDatos->PrepararConsulta("INSERT INTO usuarios_logs (id_usuario, timestamp) VALUES (:id_usuario, :timestamp)");
      $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
      $consulta->bindValue(':timestamp', $timestamp, PDO::PARAM_STR);
      $consulta->execute();


      return $response->withHeader('Content-Type', 'application/json');

    }


    //CONSULTAS
    public static function ObtenerLogs($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT u.usuario, l.timestamp FROM usuarios_logs l JOIN usuarios u ON l.id_usuario = u.id 
        WHERE l.timestamp BETWEEN :fecha AND :fecha2 
        ORDER BY l.timestamp DESC");
        $consulta->bindParam(':fecha', $parametros['fecha']);
        $consulta->bindParam(':fecha2', $parametros['fecha2']);
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($resultado));
      }else if(isset($parametros['fecha'])){
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT u.usuario, l.timestamp FROM usuarios_logs l JOIN usuarios u ON l.id_usuario = u.id
            WHERE DATE(l.timestamp) = :fecha
            ORDER BY l.timestamp DESC");
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

    public static function OperacionesPorSector($request, $response)
    {
      $parametros = $request->getQueryParams();
      $objAccesoDatos = AccesoDatos::obtenerInstancia();

      if(isset($parametros['fecha']) && isset($parametros['fecha2']))
      {
        $consulta = $objAccesoDatos->PrepararConsulta("SELECT p.tipo AS sector, pp.responsable, COUNT(*) AS num_operaciones
          FROM pedidos_productos pp
          JOIN productos p ON pp.id_producto = p.id
          JOIN pedidos ped ON pp.id_pedido = ped.id
          WHERE ped.fecha BETWEEN :fecha AND :fecha2
          GROUP BY p.tipo
          ORDER BY num_operaciones DESC");
          $consulta->bindParam(':fecha', $parametros['fecha']);
          $consulta->bindParam(':fecha2', $parametros['fecha2']);
          $consulta->execute();
          $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
          $response->getBody()->write(json_encode($resultado));

        }else if(isset($parametros['fecha'])){

          $consulta = $objAccesoDatos->PrepararConsulta("SELECT p.tipo AS sector, pp.responsable, COUNT(*) AS num_operaciones
          FROM pedidos_productos pp
          JOIN productos p ON pp.id_producto = p.id
          JOIN pedidos ped ON pp.id_pedido = ped.id
          WHERE ped.fecha = :fecha
          GROUP BY p.tipo
          ORDER BY num_operaciones DESC");
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