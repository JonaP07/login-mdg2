<?php

require_once __DIR__ . '/../config/conexion.php';

class Producto {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT id, nombre, descripcion, precio, stock FROM productos ORDER BY id");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, nombre, descripcion, precio, stock FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function verificarStock($id_producto, $cantidad) {
        $producto = $this->getById($id_producto);
        if (!$producto) {
            return false;
        }
        return $producto['stock'] >= $cantidad;
    }

    public function buscar($termino) {
        try {
            $stmt = $this->db->prepare("SELECT id, nombre, descripcion, precio, stock FROM productos WHERE nombre ILIKE ? OR descripcion ILIKE ? ORDER BY id");
            $param = "%$termino%";
            $stmt->execute([$param, $param]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ? $result : [];
        } catch (PDOException $e) {
            return [];
        }
    }
}
