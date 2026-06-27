<?php
/**
 * @var array $productos
 * @var string $mensaje
 * @var string $termino
 */
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Pedido</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-link:hover {
            background-color: #5a6268;
        }
        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .search-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .search-btn:hover {
            background-color: #0056b3;
        }
        .clear-link {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-size: 16px;
        }
        .clear-link:hover {
            background-color: #5a6268;
        }
        .message-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .message-box.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .message-box.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .product-table th, .product-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .product-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .stock-low {
            color: #dc3545;
            font-weight: bold;
        }
        .stock-good {
            color: #28a745;
        }
        .btn-add {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-add:hover:not(:disabled) {
            background-color: #0056b3;
        }
        .btn-add:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .input-cantidad {
            width: 60px;
            padding: 6px;
            margin-right: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/dashboard.php" class="back-link">← Volver al Dashboard</a>
        <h1>Lista de Productos</h1>

        <form class="search-form" method="GET" action="">
            <input type="text" name="q" class="search-input" placeholder="Buscar producto..." value="<?= htmlspecialchars($termino ?? '') ?>">
            <button type="submit" class="search-btn">Buscar</button>
            <?php if (!empty($termino)): ?>
                <a href="?" class="clear-link">Limpiar búsqueda</a>
            <?php endif; ?>
        </form>

        <div id="message-box" class="message-box" style="display: none;"></div>

        <?php if (!empty($mensaje)): ?>
            <div class="message-box warning"><?= $mensaje ?></div>
        <?php endif; ?>

        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?= htmlspecialchars($producto['id']) ?></td>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                    <td>$<?= number_format($producto['precio'], 2) ?></td>
                    <td class="<?= $producto['stock'] == 0 ? 'stock-low' : 'stock-good' ?>">
                        <?= htmlspecialchars($producto['stock']) ?>
                    </td>
                    <td>
                        <?php if ($producto['stock'] == 0): ?>
                            <button class="btn-add" disabled>Sin stock</button>
                        <?php else: ?>
                            <form class="form-agregar" data-id-producto="<?= $producto['id'] ?>" style="display: flex; align-items: center;">
                                <input type="number" class="input-cantidad" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" required data-stock="<?= $producto['stock'] ?>">
                                <button type="submit" class="btn-add">Agregar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.querySelectorAll('.input-cantidad').forEach(input => {
            input.addEventListener('input', function() {
                this.setCustomValidity('');
            });

            input.addEventListener('invalid', function() {
                const stockDisponible = Number(this.dataset.stock);
                const cantidad = Number(this.value);

                if (cantidad > stockDisponible) {
                    this.setCustomValidity('Stock no suficiente');
                    return;
                }

                if (cantidad < 1) {
                    this.setCustomValidity('La cantidad debe ser al menos 1');
                    return;
                }

                this.setCustomValidity('Ingresa una cantidad valida');
            });
        });

        document.querySelectorAll('.form-agregar').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const idProducto = this.dataset.idProducto;
                const cantidad = this.querySelector('.input-cantidad').value;
                const messageBox = document.getElementById('message-box');

                try {
                    const response = await fetch('/pedido.php?action=agregar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_producto=' + encodeURIComponent(idProducto) + '&cantidad=' + encodeURIComponent(cantidad)
                    });

                    const data = await response.json();

                    messageBox.className = 'message-box ' + (data.success ? 'success' : 'error');
                    messageBox.textContent = data.mensaje;
                    messageBox.style.display = 'block';

                    if (data.success) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }

                } catch (error) {
                    messageBox.className = 'message-box error';
                    messageBox.textContent = 'Error al procesar la solicitud';
                    messageBox.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>
