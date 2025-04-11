<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Cotización - Electrotop Perú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .cotizacion-container {
            background-color: white;
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .header-cotizacion {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }
        .info-empresa {
            font-size: 12px;
            margin-top: 10px;
        }
        .info-cliente {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f1f8ff;
            border-radius: 5px;
        }
        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .tabla-productos th {
            background-color: #0d6efd;
            color: white;
            padding: 8px;
            text-align: left;
        }
        .tabla-productos td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .totales {
            text-align: right;
            margin-bottom: 30px;
        }
        .condiciones {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
        }
        .footer-cotizacion {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .btn-imprimir {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="cotizacion-container">
            <div class="header-cotizacion">
                <div class="logo">ELECTROTOP PERÚ S.A.C.</div>
                <div class="info-empresa">
                    DIRECCIÓN: Calle Nueva 209 Int. 201 C.C. los Portales<br>
                    MAIL: ventas@electrotopperu.com<br>
                    RUC: 20559256405<br>
                    CEI: 953760540 - 944696775
                </div>
            </div>

            <div class="info-cliente">
                <div class="row">
                    <div class="col-md-6">
                        <strong>CLIENTE:</strong> Belleza Salud y Masajes S.a.C<br>
                        <strong>RUC:</strong> 20611356561
                    </div>
                    <div class="col-md-6 text-end">
                        <strong>FECHA:</strong> <?php echo date('m/d/y'); ?><br>
                        <strong>COTIZACIÓN:</strong> CTZ<?php echo date('y'); ?>-<?php echo str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT); ?>
                    </div>
                </div>
            </div>

            <table class="tabla-productos">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>CÓDIGO</th>
                        <th>DESCRIPCIÓN</th>
                        <th>P. UND</th>
                        <th>CANT.</th>
                        <th>P. TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>ZK-ML100</td>
                        <td>Cerradura inteligente con lector de huella N° SERIE: 300009282416332</td>
                        <td>300.00</td>
                        <td>1</td>
                        <td>300.00</td>
                    </tr>
                    <!-- Filas vacías para completar -->
                    <?php for ($i = 2; $i <= 12; $i++): ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="totales">
                <p><strong>TOTAL S/ 300.00</strong></p>
            </div>

            <div class="condiciones">
                <div class="row">
                    <div class="col-md-4">
                        <strong>CONDICIONES COMERCIALES</strong><br>
                        AL CONTADO
                    </div>
                    <div class="col-md-4">
                        <strong>FORMA DE PAGO</strong><br>
                        1
                    </div>
                    <div class="col-md-4">
                        <strong>VALIDEZ DE COTIZACIÓN</strong><br>
                        NÚMERO DE CUENTA<br>
                        300-3003687630 CTA. CTE. INTERBANK
                    </div>
                </div>
            </div>

            <div class="footer-cotizacion">
                <p>Gracias por su preferencia</p>
                <p>Esta cotización es válida por 15 días</p>
            </div>

            <div class="text-center">
                <button onclick="window.print()" class="btn btn-primary btn-imprimir">
                    <i class="bi bi-printer-fill"></i> Imprimir Cotización
                </button>
                <a href="dashboard.php" class="btn btn-secondary btn-imprimir">
                    <i class="bi bi-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>