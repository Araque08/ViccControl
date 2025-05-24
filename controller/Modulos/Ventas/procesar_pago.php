<?php
// 🔧 Configuración de cabecera y errores
ob_clean();
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('html_errors', 0);
error_reporting(E_ALL);

// 📦 Iniciar sesión y conexión
session_start();

$conexionPath = realpath('../../../conexionBD/conexion.php');
if (!file_exists($conexionPath)) {
    echo json_encode(['status' => 'error', 'message' => 'No se encontró el archivo de conexión']);
    exit;
}
include $conexionPath;

// 📥 Recibir JSON y decodificar
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
    exit;
}

// 🧍‍♂️ Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión de empleado no definida']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion->begin_transaction();

    try {
        // 📋 Datos recibidos
        $nombre      = $data['nombre_cliente'];
        $telefono    = $data['telefono_cliente'];
        $direccion   = $data['direccion_cliente'] ?? '';
        $email       = $data['email_cliente'] ?? '';
        $medioPago   = $data['medio_pago'];
        $subtotal    = floatval($data['subtotal'] ?? 0);
        $ipm         = floatval($data['ipm'] ?? 0);
        $total       = floatval($data['total'] ?? 0);
        $productos   = $data['productos'] ?? [];
        $idEmpleado  = $_SESSION['id_usuario'];

        if (empty($nombre) || empty($telefono) || empty($productos)) {
            throw new Exception("Faltan datos obligatorios");
        }

        // 1️⃣ Insertar cliente
        $stmt = $conexion->prepare("INSERT INTO Cliente (nombre_cliente, telefono_cliente, direccion_cliente, email_cliente) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $telefono, $direccion, $email);
        $stmt->execute();
        $idCliente = $stmt->insert_id;
        $stmt->close();

        // 2️⃣ Insertar venta
        $stmt = $conexion->prepare("INSERT INTO Venta (fk_id_cliente, fk_id_empleado, fecha, total_venta) VALUES (?, ?, CURDATE(), ?)");
        $stmt->bind_param("iid", $idCliente, $idEmpleado, $total);
        $stmt->execute();
        $idVenta = $stmt->insert_id;
        $stmt->close();

        // 3️⃣ Insertar método de pago
        $stmt = $conexion->prepare("INSERT INTO Venta_MedioPago (fk_id_venta, fk_id_medio_pago, monto) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $idVenta, $medioPago, $total);
        $stmt->execute();
        $stmt->close();

        // 4️⃣ Insertar factura
        $stmt = $conexion->prepare("INSERT INTO Factura (fk_id_venta, subtotal, impuestos_factura, valor_factura) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iddd", $idVenta, $subtotal, $ipm, $total);
        $stmt->execute();
        $stmt->close();

        // 🔁 5️⃣ (Opcional) Insertar productos si tienes tabla intermedia
        // foreach ($productos as $producto) {
        //     $nombreProducto = $producto['nombre'];
        //     $cantidad = intval($producto['cantidad']);
        //     $totalProd = floatval(str_replace(['$', '.', ','], ['', '', '.'], $producto['total']));
        //     ...
        // }

        // ✅ Confirmar
        $conexion->commit();
        echo json_encode(['status' => 'success', 'venta_id' => $idVenta]);
        exit;

    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}
?>
