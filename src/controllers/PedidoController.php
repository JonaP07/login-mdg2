<?php

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Pedido.php';

class PedidoController {
    private $productoModel;
    private $pedidoModel;

    public function __construct() {
        $this->productoModel = new Producto();
        $this->pedidoModel = new Pedido();
    }

    public function index() {
        $termino = $_GET['q'] ?? '';
        $mensaje = '';

        if (!empty($termino)) {
            $productos = $this->productoModel->buscar($termino);
            if (empty($productos)) {
                $mensaje = "No se encontraron productos para: " . htmlspecialchars($termino);
            }
        } else {
            $productos = $this->productoModel->getAll();
        }

        require_once __DIR__ . '/../views/pedido/index.php';
    }

    public function agregar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;

            if ($id_producto <= 0 || $cantidad <= 0) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Datos inválidos'
                ]);
                return;
            }

            $id_cliente = 1;

            echo $this->pedidoModel->crearPedido($id_cliente, $id_producto, $cantidad);
        }
    }
}
