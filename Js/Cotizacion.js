let itemCount = 0;
let total = 0;

// Mostrar modal para agregar producto
document.getElementById('btnAddProduct').addEventListener('click', function () {
    var modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
});

// Calcular total del producto en tiempo real
document.getElementById('producto_precio').addEventListener('input', calculateProductTotal);
document.getElementById('producto_cantidad').addEventListener('input', calculateProductTotal);

function calculateProductTotal() {
    const precio = parseFloat(document.getElementById('producto_precio').value) || 0;
    const cantidad = parseInt(document.getElementById('producto_cantidad').value) || 0;
    const totalProducto = precio * cantidad;
    document.getElementById('producto_total').value = totalProducto.toFixed(2);
}

// Guardar producto
document.getElementById('btnSaveProduct').addEventListener('click', function () {
    const codigo = document.getElementById('producto_codigo').value;
    const descripcion = document.getElementById('producto_descripcion').value;
    const precio = parseFloat(document.getElementById('producto_precio').value);
    const cantidad = parseInt(document.getElementById('producto_cantidad').value);
    const totalProducto = precio * cantidad;

    if (!codigo || !descripcion || isNaN(precio) || isNaN(cantidad)) {
        alert('Por favor complete todos los campos correctamente');
        return;
    }

    itemCount++;
    total += totalProducto;

    // Actualizar tabla
    const tbody = document.getElementById('productosBody');
    if (tbody.firstChild && tbody.firstChild.textContent.includes('No hay productos')) {
        tbody.innerHTML = '';
    }

    const newRow = document.createElement('tr');
    newRow.innerHTML = `
                <td>${itemCount}</td>
                <td>${codigo}</td>
                <td class="product-description">${descripcion}</td>
                <td>${precio.toFixed(2)}</td>
                <td>${cantidad}</td>
                <td>${totalProducto.toFixed(2)}</td>
                <td><button class="btn btn-sm btn-danger remove-product" title="Eliminar producto"><i class="bi bi-trash"></i></button></td>
            `;
    tbody.appendChild(newRow);

    // Actualizar total
    document.getElementById('total').value = total.toFixed(2);

    // Cerrar modal y limpiar campos
    var modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
    modal.hide();
    document.getElementById('producto_codigo').value = '';
    document.getElementById('producto_descripcion').value = '';
    document.getElementById('producto_precio').value = '';
    document.getElementById('producto_cantidad').value = '1';
    document.getElementById('producto_total').value = '0.00';
});

// Eliminar producto
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-product') || e.target.closest('.remove-product')) {
        if (confirm('¿Está seguro que desea eliminar este producto?')) {
            const row = e.target.closest('tr');
            const precioTotal = parseFloat(row.cells[5].textContent);
            total -= precioTotal;
            document.getElementById('total').value = total.toFixed(2);
            row.remove();
            itemCount--;

            // Renumerar items
            const rows = document.querySelectorAll('#productosBody tr');
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            });

            // Mostrar mensaje si no hay productos
            if (itemCount === 0) {
                const tbody = document.getElementById('productosBody');
                tbody.innerHTML = '<tr class="text-center text-muted"><td colspan="7">No hay productos agregados</td></tr>';
            }
        }
    }
});
