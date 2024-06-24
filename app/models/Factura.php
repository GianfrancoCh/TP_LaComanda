<?php

include_once(__DIR__ . '/../db/AccesoDatos.php');

class Factura
{
	public $id;
    public $id_pedido;
	public $fecha;
	public $cliente;
	public $forma_pago;
	public $importe;

	public function crearFactura()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO facturas (id_pedido, fecha, cliente, forma_pago, importe) VALUES (:id_pedido,:fecha, :cliente, :forma_pago, :importe)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);        
        $consulta->bindValue(':forma_pago', $this->forma_pago, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

	public static function obtenerFactura($id)
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("SELECT * FROM facturas WHERE id=:id");
		$consulta->bindValue(':id', $id, PDO::PARAM_INT);
		$consulta->execute();

		return $consulta->fetchObject('Factura');
	}

	public static function obtenerTodosId()
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("SELECT id FROM facturas");
		$consulta->execute();

		return $consulta->fetchAll(PDO::FETCH_COLUMN);
	}

	// protected function CrearPdf()
	// {
	// 	$pdf = new FPDF();
	// 	$pdf->AddPage();
	// 	$pdf->SetFont('Courier', 'BU', 35);
	// 	$pdf->Cell(187.5, 30, "RECIBO", 1, 0, 'C');
	// 	$pdf->Image(__DIR__ . '..\..\..\utn-logo.png', 11.25, null, 30, 30, 'png');

	// 	$pdf->SetFont('Arial', '', 18);
	// 	$pdf->Cell(187.5, 10, "$this->fecha", 0, 1, 'R');

	// 	$pdf->SetFont('Arial', 'I', 18);
	// 	$pdf->Cell(187.5, 12, "#$this->numero", 0, 0, 'R');

	// 	$pdf->SetFont('Arial', '', 16);
	// 	$pdf->Write(10, "Id del pedido: #{$this->idPedido}\n");
	// 	$pdf->Write(10, "Cliente: {$this->cliente}\n");

	// 	$pdf->SetFillColor(255, 239, 219);
	// 	$pdf->Cell(15, 8, "ID", 1, 0, 'C', 1);
	// 	$pdf->Cell(142.5, 8, "PRODUCTO", 1, 0, 'C', 1);
	// 	$pdf->Cell(30, 8, "PRECIO", 1, 1, 'C', 1);

	// 	$idProductos = ProductoPedido::TraerProdPorPedido($this->idPedido);
	// 	foreach ($idProductos as $prodId) {
	// 		$producto = Producto::TraerPorId($prodId)[0];
	// 		$pdf->Cell(15, 8, "$producto->id", 1, 0, 'L', 1);
	// 		$pdf->Cell(142.5, 8, "$producto->descripcion", 1, 0, 'L', 1);
	// 		$pdf->Cell(30, 8, "\$$producto->precio", 1, 1, 'R', 1);
	// 	}

	// 	$pdf->SetFont('Arial', 'B', 16);
	// 	$pdf->Cell(157.5, 10, "TOTAL", 1, 0, 'L');
	// 	$pdf->Cell(30, 10, "\${$this->importe}", 1, 1, 'R');
	// 	$pdf->Write(10, "Forma de pago: {$this->formaDePago}\n");

	// 	return $pdf;
	// }

}