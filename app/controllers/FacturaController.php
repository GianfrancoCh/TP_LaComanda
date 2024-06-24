<?php

require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/Producto.php';
require_once './models/PedidoProductos.php';
require_once './models/Mesa.php';
require_once './models/Factura.php';

class FacturaController extends Factura
{
	public function CargarUna($request, $response)
	{
		$parametros = $request->getParsedBody();
        $pedido = Pedido::obtenerPedido($parametros['id_pedido']);
		$factura = new Factura();
		$factura->fecha = $pedido->fecha;
		$factura->id_pedido = $parametros['id_pedido'];
		$factura->cliente = $parametros['cliente'];
		$factura->forma_pago = $parametros['forma_pago'];
		$factura->importe = $pedido->precio;
		$id = $factura->crearFactura();

        Pedido::modificarPedido($pedido->id, 'pagando');
		Mesa::modificarMesa($pedido->id_mesa, 'pagando');
		

        $payload = json_encode(array("mensaje" => "Se creo factura con ID: " . $id));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
	}

	public static function PagarFactura($request, $response)
	{
		$parametros = $request->getParsedBody();
		$factura = Factura::obtenerFactura($parametros['id_factura']);
		$pedido = Pedido::obtenerPedido($factura->id_pedido);

		Pedido::modificarPedido($pedido->id, 'pago');
        Mesa::modificarMesa($pedido->id_mesa, 'vacia');

		$payload = json_encode(array("mensaje" => "Pedido pago."));
		$response->getBody()->write($payload);
		return $response->withHeader('Content-Type', 'application/json');
	}

	// private static function ShowPDF($numeroRecibo)
	// {
	// 	$recibo = Recibo::TraerPorId($numeroRecibo)[0];
	// 	$objPdf = $recibo->CrearPdf();
	// 	$objPdf->Output();
	// }

	// private static function DownloadPDF($numeroRecibo)
	// {
	// 	$recibo = Recibo::TraerPorId($numeroRecibo)[0];
	// 	$objPdf = $recibo->CrearPdf();
	// 	$nombreArchivo = "recibo_" . $recibo->numero . ".pdf";
	// 	$objPdf->Output("D", $nombreArchivo);
	// }
}