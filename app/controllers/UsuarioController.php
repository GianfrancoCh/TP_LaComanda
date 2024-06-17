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
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];
        Usuario::modificarUsuario($id,$usuario,$clave,$rol);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function Login($request, $response, $args)
    {
      // $parametros = $request->getParsedBody();
  
      // if (isset($params['id']) && isset($params['usuario']) && isset($params['clave'])) {
      //   $usuario = Usuario::TraerPorId($params['id']);
      //   if (!empty($usuario)) {
      //     if (
      //       !strcasecmp($params['usuario'], $usuario[0]->usuario)
      //       && password_verify($params['clave'], $usuario[0]->clave)
      //     ) {
      //       if ($usuario[0]->estado == USUARIO_ACTIVO) {
      //         $payload = json_encode(array('msg' => "OK", 'rol' => $usuario[0]->rol));
  
      //         $jwt = AutentificadorJWT::CrearToken(
      //           array(
      //             'id' => $usuario[0]->id,
      //             'rol' => $usuario[0]->rol,
      //             'fecha' => date('Y-m-d'),
      //             'hora' => date('H:i:s')
      //           )
      //         );
      //         setcookie("token", $jwt, time() + 1800, '/', "localhost", false, true);
      //       } else {
      //         $payload = json_encode(array('msg' => "El usuario no se encuentra activo."));
      //       }
      //     } else {
      //       //Borra cookie existente
      //       setcookie("token", " ", time() - 3600, "/", "localhost", false, true);
      //       $payload = json_encode(array('msg' => "Los datos del usuario #{$params['id']} no coinciden."));
      //     }
      //   } else {
      //     $payload = json_encode(array('msg' => "No existe un usuario con ese id."));
      //   }
      // } else {
      //   $response->getBody()->write(json_encode(array("msg" => "Ingrese los datos para el login!")));
      // }
      $payload = "adentro login";
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

}
