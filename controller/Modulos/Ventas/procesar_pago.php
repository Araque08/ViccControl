<?php
// transaccion_real.php
include '../../../conexion/conexion.php';
session_start();

// Recibe y decodifica JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// $data['productos'] ahora será un array directamente

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
    exit;
}

// Accede a los datos, ejemplo:
$nombreCliente = $data['nombre_cliente'];
$productos = $data['productos']; // array con productos

// Puedes convertir cadenas monetarias si es necesario:
$ipm = floatval(str_replace(['$', '.', ','], ['', '', '.'], $data['ipm']));
$total = floatval(str_replace(['$', '.', ','], ['', '', '.'], $data['total']));

// Implementa aquí la transacción ACID y las inserciones en la BD

// Al final:
echo json_encode(['status' => 'success', 'venta_id' => $idVentaGenerada]);
exit;



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion->begin_transaction();

    try {
        // Datos del cliente desde el formulario
        $nombre = trim($_POST['nombre_cliente']);
        $telefono = trim($_POST['telefono_cliente']);
        $direccion = trim($_POST['direccion_cliente']);
        $email = trim($_POST['email_cliente']);
        $medioPago = (int) $_POST['medio_pago'];
        $subtotal = (float) $_POST['subtotal'];
        $ipm = (float) $_POST['ipm'];
        $total = (float) $_POST['total'];
        $productos = json_decode($_POST['productos'], true);
        $idEmpleado = $_SESSION['id_empleado'];

        if (empty($nombre) || empty($telefono) || empty($productos)) {
            throw new Exception("Datos obligatorios faltantes");
        }

        // 1. Insertar cliente si no existe (puedes cambiar por verificación previa)
        $stmt = $conexion->prepare("INSERT INTO Cliente (nombre_cliente, telefono_cliente, direccion_cliente, correo_cliente) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $telefono, $direccion, $email);
        $stmt->execute();
        $idCliente = $stmt->insert_id;

        // 2. Insertar venta
        $stmt = $conexion->prepare("INSERT INTO Venta (fk_id_cliente, fk_id_empleado, fecha, total_venta) VALUES (?, ?, CURDATE(), ?)");
        $stmt->bind_param("iid", $idCliente, $idEmpleado, $total);
        $stmt->execute();
        $idVenta = $stmt->insert_id;

        // 3. Insertar medio de pago
        $stmt = $conexion->prepare("INSERT INTO Venta_MedioPago (fk_id_venta, fk_id_medio_pago, monto) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $idVenta, $medioPago, $total);
        $stmt->execute();

        // 4. Insertar factura
        $stmt = $conexion->prepare("INSERT INTO Factura (fk_id_venta, subtotal, impuestos_factura, valor_factura) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iddd", $idVenta, $subtotal, $ipm, $total);
        $stmt->execute();

        // 5. (Opcional) Guardar los productos relacionados - requiere tabla intermedia si se desea

        $conexion->commit();
        echo json_encode(['success' => true, 'mensaje' => 'Transacci\u00f3n completada exitosamente.']);

    } catch (Exception $e) {
        $conexion->rollback();  
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
