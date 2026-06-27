<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/Producto.php';

class Pedido {
    private $db;
    private $productoModel;

    public function __construct() {
        global $db;
        $this->db = $db;
        $this->productoModel = new Producto();
    }

    public function crearPedido($id_cliente, $id_producto, $cantidad) {
        try {
            if (!$this->productoModel->verificarStock($id_producto, $cantidad)) {
                return json_encode([
                    'success' => false,
                    'mensaje' => 'Stock no suficiente'
                ]);
            }

            $producto = $this->productoModel->getById($id_producto);
            if (!$producto) {
                return json_encode([
                    'success' => false,
                    'mensaje' => 'Producto no encontrado'
                ]);
            }

            $this->db->beginTransaction();

            $total = $producto['precio'] * $cantidad;
            $stmt = $this->db->prepare("INSERT INTO pedidos (id_cliente, total) VALUES (?, ?)");
            $stmt->execute([$id_cliente, $total]);
            $id_pedido = $this->db->lastInsertId();

            $stmt = $this->db->prepare("INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_pedido, $id_producto, $cantidad, $producto['precio']]);

            $stmt = $this->db->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$cantidad, $id_producto]);

            $this->db->commit();

            return json_encode([
                'success' => true,
                'mensaje' => 'Producto agregado al pedido correctamente'
            ]);

        } catch (PDOException $e) {
            $this->db->rollBack();
            return json_encode([
                'success' => false,
                'mensaje' => 'Error al crear pedido: ' . $e->getMessage()
            ]);
        }
    }
}
