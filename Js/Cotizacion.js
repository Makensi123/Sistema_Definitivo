// Variables globales
        let productosCotizacion = [];
        let itemCounter = 1;
        let currentEditIndex = null;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar eventos
            setupEventListeners();

            // Actualizar tipo de documento visual
            document.getElementById('tipo_operacion').addEventListener('change', function() {
                document.getElementById('documentTypeBadge').textContent = this.value;
            });
        });

        function setupEventListeners() {
            // Botón para agregar primer producto
            document.getElementById('btnAddFirstProduct')?.addEventListener('click', showProductModal);

            // Botón para agregar producto
            document.getElementById('btnAddProduct').addEventListener('click', showProductModal);

            // Búsqueda de productos
            document.getElementById('searchProduct').addEventListener('input', searchProducts);
            document.getElementById('btnSearchProduct').addEventListener('click', searchProducts);

            // Control de cantidad
            document.getElementById('incrementQty')?.addEventListener('click', function() {
                const input = document.getElementById('producto_cantidad');
                input.value = parseInt(input.value) + 1;
                calcularTotalProducto();
            });

            document.getElementById('decrementQty')?.addEventListener('click', function() {
                const input = document.getElementById('producto_cantidad');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    calcularTotalProducto();
                }
            });

            // Cálculos de producto
            document.getElementById('producto_cantidad').addEventListener('input', calcularTotalProducto);
            document.getElementById('producto_precio').addEventListener('input', calcularTotalProducto);

            // Agregar producto a cotización
            document.getElementById('btnAddToQuote').addEventListener('click', addProductToQuote);

            // Autoajuste de textareas
            document.querySelectorAll('.auto-expand').forEach(textarea => {
                textarea.addEventListener('input', autoExpandTextarea);
                // Disparar el evento input para ajustar inicialmente
                const event = new Event('input');
                textarea.dispatchEvent(event);
            });

            // Validación antes de enviar
            document.getElementById('cotizacionForm').addEventListener('submit', function(e) {
                if (productosCotizacion.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cotización vacía',
                        text: 'Debe agregar al menos un producto para guardar la cotización',
                        confirmButtonColor: '#1a4b8c'
                    });
                    return;
                }

                // Validar campos obligatorios
                const cliente = document.getElementById('cliente').value.trim();
                const numeroDocumento = document.getElementById('numero_documento').value.trim();

                if (cliente === '' || numeroDocumento === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos incompletos',
                        text: 'Por favor complete todos los campos obligatorios',
                        confirmButtonColor: '#1a4b8c'
                    });
                    return;
                }

                // Crear input oculto con los productos en formato JSON
                const productosInput = document.createElement('input');
                productosInput.type = 'hidden';
                productosInput.name = 'productos_json';
                productosInput.value = JSON.stringify(productosCotizacion);
                this.appendChild(productosInput);
            });
        }

        function autoExpandTextarea() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        }

        function showProductModal() {
            // Limpiar búsqueda previa
            document.getElementById('searchProduct').value = '';
            document.getElementById('productSearchResults').querySelector('tbody').innerHTML =
                '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-search" style="font-size: 2rem; color: #6c757d;"></i><p class="mt-2">Ingrese un término de búsqueda para encontrar productos</p></div></td></tr>';
            document.getElementById('selectedProductDetails').style.display = 'none';
            document.getElementById('btnAddToQuote').disabled = true;
            document.getElementById('btnAddToQuote').innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Agregar a Cotización';
            currentEditIndex = null;

            var modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        }

        function searchProducts() {
            const searchTerm = this.value?.trim() || document.getElementById('searchProduct').value.trim();
            const tbody = document.getElementById('productSearchResults').querySelector('tbody');

            if (searchTerm.length === 0) {
                tbody.innerHTML = '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-search" style="font-size: 2rem; color: #6c757d;"></i><p class="mt-2">Ingrese un término de búsqueda para encontrar productos</p></div></td></tr>';
                return;
            }

            // Mostrar carga
            tbody.innerHTML = '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Buscando productos...</p></div></td></tr>';

            fetch(`../Controllers/buscarProductos.php?termino=${encodeURIComponent(searchTerm)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Error en la red');
                    return response.json();
                })
                .then(data => {
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-exclamation-circle" style="font-size: 2rem; color: #6c757d;"></i><p class="mt-2">No se encontraron productos</p></div></td></tr>';
                        return;
                    }

                    data.forEach(producto => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                        <td>${producto.codigo}</td>
                        <td>${producto.descripcion}</td>
                        <td class="text-end">S/ ${parseFloat(producto.precio).toFixed(2)}</td>
                        <td class="text-center">${producto.stock || 'N/A'}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary btn-select-product"
                                data-id="${producto.id}"
                                data-codigo="${producto.codigo}"
                                data-descripcion="${producto.descripcion}"
                                data-precio="${producto.precio}">
                                <i class="bi bi-plus"></i> Seleccionar
                            </button>
                        </td>
                    `;
                        tbody.appendChild(tr);
                    });

                    // Agregar eventos a los botones de selección
                    document.querySelectorAll('.btn-select-product').forEach(btn => {
                        btn.addEventListener('click', function() {
                            selectProduct(
                                this.dataset.id,
                                this.dataset.codigo,
                                this.dataset.descripcion,
                                this.dataset.precio
                            );
                        });
                    });
                })
                .catch(error => {
                    tbody.innerHTML = '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-exclamation-triangle" style="font-size: 2rem; color: #dc3545;"></i><p class="mt-2">Error al buscar productos</p></div></td></tr>';
                    console.error('Error:', error);
                });
        }

        function selectProduct(id, codigo, descripcion, precio) {
            // Llena los campos del modal con el producto seleccionado
            document.getElementById('producto_id').value = id;
            document.getElementById('producto_codigo').value = codigo;
            document.getElementById('producto_descripcion').value = descripcion;
            document.getElementById('producto_precio').value = parseFloat(precio).toFixed(2);
            document.getElementById('producto_cantidad').value = 1;
            document.getElementById('producto_total').value = parseFloat(precio).toFixed(2);

            document.getElementById('selectedProductDetails').style.display = 'block';
            document.getElementById('btnAddToQuote').disabled = false;

            // Enfocar el campo de cantidad
            document.getElementById('producto_cantidad').focus();
        }

        function calcularTotalProducto() {
            const cantidad = parseInt(document.getElementById('producto_cantidad').value) || 0;
            const precio = parseFloat(document.getElementById('producto_precio').value) || 0;
            const total = cantidad * precio;
            document.getElementById('producto_total').value = total.toFixed(2);
        }

        function addProductToQuote() {
            const cantidad = parseInt(document.getElementById('producto_cantidad').value);
            const precio = parseFloat(document.getElementById('producto_precio').value);

            if (isNaN(cantidad) || cantidad < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cantidad inválida',
                    text: 'Por favor ingrese una cantidad válida (mínimo 1)',
                    confirmButtonColor: '#1a4b8c'
                });
                return;
            }

            if (isNaN(precio) || precio <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Precio inválido',
                    text: 'Por favor ingrese un precio válido (mayor que 0)',
                    confirmButtonColor: '#1a4b8c'
                });
                return;
            }

            const producto = {
                item: currentEditIndex !== null ? productosCotizacion[currentEditIndex].item : itemCounter++,
                id: document.getElementById('producto_id').value,
                codigo: document.getElementById('producto_codigo').value,
                descripcion: document.getElementById('producto_descripcion').value,
                precio: parseFloat(document.getElementById('producto_precio').value).toFixed(2),
                cantidad: cantidad,
                total: parseFloat(document.getElementById('producto_total').value).toFixed(2),
                notas: document.getElementById('producto_notas').value
            };

            if (currentEditIndex !== null) {
                // Editar producto existente
                productosCotizacion[currentEditIndex] = producto;
            } else {
                // Agregar nuevo producto
                productosCotizacion.push(producto);
            }

            actualizarTablaProductos();
            calcularTotalCotizacion();

            // Mostrar notificación
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: currentEditIndex !== null ? 'Producto actualizado' : 'Producto agregado',
                showConfirmButton: false,
                timer: 1500,
                toast: true
            });

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        }

        function actualizarTablaProductos() {
            const tbody = document.getElementById('productosBody');

            if (productosCotizacion.length > 0) {
                tbody.innerHTML = '';

                productosCotizacion.forEach((producto, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td class="text-center">${producto.item}</td>
                    <td>${producto.codigo}</td>
                    <td>
                        ${producto.descripcion}
                        ${producto.notas ? `<div class="product-notes"><small><i class="bi bi-info-circle"></i> ${producto.notas}</small></div>` : ''}
                    </td>
                    <td class="text-end">S/ ${producto.precio}</td>
                    <td class="text-center">${producto.cantidad}</td>
                    <td class="text-end">S/ ${producto.total}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-edit me-1" data-index="${index}" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-delete" data-index="${index}" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                    tbody.appendChild(row);
                });

                // Agregar eventos a los botones de editar y eliminar
                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', function() {
                        editProduct(parseInt(this.getAttribute('data-index')));
                    });
                });

                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', function() {
                        deleteProduct(parseInt(this.getAttribute('data-index')));
                    });
                });
            } else {
                tbody.innerHTML = `
                <tr class="empty-row">
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-box-seam"></i>
                            <p>No hay productos agregados</p>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddFirstProduct">
                                <i class="bi bi-plus-circle me-1"></i> Agregar primer producto
                            </button>
                        </div>
                    </td>
                </tr>
            `;

                // Agregar evento al botón de agregar primer producto
                document.getElementById('btnAddFirstProduct')?.addEventListener('click', showProductModal);
            }
        }

        function editProduct(index) {
            const producto = productosCotizacion[index];
            currentEditIndex = index;

            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();

            // Llenar datos del producto
            document.getElementById('producto_id').value = producto.id;
            document.getElementById('producto_codigo').value = producto.codigo;
            document.getElementById('producto_descripcion').value = producto.descripcion;
            document.getElementById('producto_precio').value = producto.precio;
            document.getElementById('producto_cantidad').value = producto.cantidad;
            document.getElementById('producto_total').value = producto.total;
            document.getElementById('producto_notas').value = producto.notas || '';

            document.getElementById('selectedProductDetails').style.display = 'block';
            document.getElementById('btnAddToQuote').disabled = false;

            // Cambiar texto del botón
            document.getElementById('btnAddToQuote').innerHTML = '<i class="bi bi-save-fill me-1"></i> Actualizar Producto';
        }

        function deleteProduct(index) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    productosCotizacion.splice(index, 1);

                    // Reorganizar los números de item
                    itemCounter = 1;
                    productosCotizacion.forEach(producto => {
                        producto.item = itemCounter++;
                    });

                    actualizarTablaProductos();
                    calcularTotalCotizacion();

                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Producto eliminado',
                        showConfirmButton: false,
                        timer: 1500,
                        toast: true
                    });
                }
            });
        }

        function calcularTotalCotizacion() {
            const total = productosCotizacion.reduce((sum, producto) => sum + parseFloat(producto.total), 0);
            document.getElementById('total').value = total.toFixed(2);
        }