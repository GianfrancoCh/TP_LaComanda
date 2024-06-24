<?php

include_once(__DIR__ . '/../db/AccesoDatos.php');
include_once(__DIR__ . '/.././utils/FPDF//fpdf.php');

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

	public function generarPDF() {
        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 14);

        $pdf->Cell(190, 10, 'Factura', 0, 1, 'C');

        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(30, 10, 'ID Factura:', 0, 0);
        $pdf->Cell(50, 10, $this->id, 0, 1);
        
        $pdf->Cell(30, 10, 'Fecha:', 0, 0);
        $pdf->Cell(50, 10, $this->fecha, 0, 1);
        
        $pdf->Cell(30, 10, 'Cliente:', 0, 0);
        $pdf->Cell(50, 10, $this->cliente, 0, 1);

        $pdf->Cell(30, 10, 'Forma de Pago:', 0, 0);
        $pdf->Cell(50, 10, $this->forma_pago, 0, 1);

        $pdf->Cell(30, 10, 'Importe:', 0, 0);
        $pdf->Cell(50, 10, '$' . $this->importe, 0, 1);

        $pdf->Output('I', 'factura_' . $this->id . '.pdf');

        // Para descargarlo
        // $pdf->Output('D', 'factura_' . $this->id . '.pdf');
    }

}