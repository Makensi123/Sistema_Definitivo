// Variables globales
let productosCotizacion = [];
let itemCounter = 1;
let currentEditIndex = null;

// Inicialización
document.addEventListener('DOMContentLoaded', function () {
    // Configurar eventos
    setupEventListeners();

    // Actualizar tipo de documento visual
    document.getElementById('tipo_operacion').addEventListener('change', function () {
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
    document.getElementById('incrementQty')?.addEventListener('click', function () {
        const input = document.getElementById('producto_cantidad');
        input.value = parseInt(input.value) + 1;
        calcularTotalProducto();
    });

    document.getElementById('decrementQty')?.addEventListener('click', function () {
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

    // Generar PDF
    document.getElementById('btnPreviewPDF').addEventListener('click', generatePDFPreview);
    document.getElementById('btnDownloadPDF').addEventListener('click', downloadPDF);
    document.getElementById('btnDownloadFromPreview').addEventListener('click', downloadPDF);

    // Autoajuste de textareas
    document.querySelectorAll('.auto-expand').forEach(textarea => {
        textarea.addEventListener('input', autoExpandTextarea);
        // Disparar el evento input para ajustar inicialmente
        const event = new Event('input');
        textarea.dispatchEvent(event);
    });

    // Validación antes de enviar
    document.getElementById('cotizacionForm').addEventListener('submit', function (e) {
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

        // Crear input oculto con los productos en formato JSON
        const productosInput = document.createElement('input');
        productosInput.type = 'hidden';
        productosInput.name = 'productos_json';
        productosInput.value = JSON.stringify(productosCotizacion);
        this.appendChild(productosInput);
    });
}

function showProductModal() {
    // Limpiar búsqueda previa
    document.getElementById('searchProduct').value = '';
    document.getElementById('productSearchResults').querySelector('tbody').innerHTML =
        '<tr class="no-results"><td colspan="5"><div class="text-center py-4"><i class="bi bi-search" style="font-size: 2rem; color: #6c757d;"></i><p class="mt-2">Ingrese un término de búsqueda para encontrar productos</p></div></td></tr>';
    document.getElementById('selectedProductDetails').style.display = 'none';
    document.getElementById('btnAddToQuote').disabled = true;
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
                    <td class="text-end">S/ ${producto.precio}</td>
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
                btn.addEventListener('click', function () {
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
    document.getElementById('producto_precio').value = precio;
    document.getElementById('producto_cantidad').value = 1;
    document.getElementById('producto_total').value = precio;

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
    const producto = {
        item: currentEditIndex !== null ? productosCotizacion[currentEditIndex].item : itemCounter++,
        id: document.getElementById('producto_id').value,
        codigo: document.getElementById('producto_codigo').value,
        descripcion: document.getElementById('producto_descripcion').value,
        precio: parseFloat(document.getElementById('producto_precio').value).toFixed(2),
        cantidad: parseInt(document.getElementById('producto_cantidad').value),
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
            btn.addEventListener('click', function () {
                editProduct(parseInt(this.getAttribute('data-index')));
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function () {
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

// Función corregida para generar vista previa del PDF
function generatePDFPreview() {
    if (productosCotizacion.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Cotización vacía',
            text: 'Debe agregar al menos un producto para generar el PDF',
            confirmButtonColor: '#1a4b8c'
        });
        return;
    }

    // Mostrar carga
    const loadingSwal = Swal.fire({
        title: 'Generando PDF',
        html: 'Por favor espere mientras se genera la vista previa...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Clonar el elemento para evitar problemas con el DOM
    const element = document.getElementById('cotizacionContent');
    const clone = element.cloneNode(true);

    // Asegurar que las imágenes se carguen correctamente
    const images = clone.querySelectorAll('img');
    let imagesToLoad = images.length;

    if (imagesToLoad === 0) {
        generatePDFFromClone(clone, loadingSwal);
        return;
    }

    // Función para verificar si todas las imágenes están cargadas
    const checkImagesLoaded = () => {
        if (imagesToLoad === 0) {
            generatePDFFromClone(clone, loadingSwal);
        }
    };

    images.forEach(img => {
        // Reemplazar con URL absoluta si es relativa
        if (!img.src.startsWith('http') && !img.src.startsWith('data:')) {
            img.src = window.location.origin + (img.src.startsWith('/') ? img.src : '/' + img.src);
        }

        // Si la imagen ya está cargada
        if (img.complete && img.naturalWidth !== 0) {
            imagesToLoad--;
            checkImagesLoaded();
            return;
        }

        img.onload = () => {
            imagesToLoad--;
            checkImagesLoaded();
        };

        img.onerror = () => {
            console.warn('Error cargando imagen:', img.src);
            // Ocultar imagen si no se puede cargar
            img.style.display = 'none';
            imagesToLoad--;
            checkImagesLoaded();
        };
    });
}

function generatePDFFromClone(clone, loadingSwal) {
    // Reemplazar imágenes con problemas por un div de fallback
    clone.querySelectorAll('img').forEach(img => {
        if (!img.complete || img.naturalWidth === 0) {
            const fallback = document.createElement('div');
            fallback.className = 'img-fallback';
            fallback.textContent = 'Logo no disponible';
            img.parentNode.replaceChild(fallback, img);
        }
    });

    // Opciones de configuración mejoradas
    const opt = {
        margin: 10,
        filename: `Cotizacion_Electrotop_${Date.now()}.pdf`,
        image: {
            type: 'jpeg',
            quality: 0.98
        },
        html2canvas: {
            scale: 1.5, // Reducido para mayor compatibilidad
            ignoreElements: (el) => el.tagName === 'IMG',  // Ignorar todas las imágenes
            logging: true,
            useCORS: true,
            allowTaint: false,
            ignoreElements: (element) => {
                return element.tagName === 'IFRAME';
            },
            onclone: (clonedDoc) => {
                // Asegurar que las imágenes problemáticas no se muestren
                clonedDoc.querySelectorAll('img').forEach(img => {
                    if (!img.complete || img.naturalWidth === 0) {
                        img.style.display = 'none';
                    }
                });
            },
            backgroundColor: '#FFFFFF' // Fondo blanco para el PDF
        },
        jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait',
            compress: true
        }
    };

    // Generar PDF con manejo de errores mejorado
    html2pdf()
        .set(opt)
        .from(clone)
        .toPdf()
        .get('pdf')
        .then(function (pdf) {
            const pdfBlob = pdf.output('blob');
            const pdfUrl = URL.createObjectURL(pdfBlob);

            const iframe = document.getElementById('pdfPreview');
            iframe.onload = function () {
                URL.revokeObjectURL(pdfUrl);
                loadingSwal.close();

                const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
                previewModal.show();
            };
            iframe.src = pdfUrl;
        })
        .catch(function (error) {
            console.error('Error detallado:', error);
            let errorMessage = 'Ocurrió un error al generar el PDF';

            if (error.message.includes('Unsupported image type')) {
                errorMessage = 'Error: Formato de imagen no soportado. Por favor use imágenes JPG o PNG.';
            }

            Swal.fire({
                icon: 'error',
                title: 'Error al generar PDF',
                text: errorMessage,
                confirmButtonColor: '#1a4b8c'
            });
        })
        .finally(() => {
            // Asegurarse de eliminar el clon
            if (document.body.contains(clone)) {
                document.body.removeChild(clone);
            }
            loadingSwal.close();
        });
}

// Función optimizada para descargar PDF
function downloadPDF() {
    if (productosCotizacion.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Cotización vacía',
            text: 'Debe agregar al menos un producto para generar el PDF',
            confirmButtonColor: '#1a4b8c'
        });
        return;
    }

    const loadingSwal = Swal.fire({
        title: 'Generando PDF',
        html: 'Por favor espere mientras se genera el documento...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Clonar el elemento
    const element = document.getElementById('cotizacionContent');
    const clone = element.cloneNode(true);

    // Aplicar estilos para PDF
    clone.style.width = '210mm';
    clone.style.margin = '0 auto';
    clone.style.padding = '15mm';
    clone.style.boxShadow = 'none';
    clone.style.backgroundColor = 'white';
    clone.classList.add('pdf-export'); // Clase adicional para estilos específicos

    // Ocultar elementos no deseados
    const elementsToHide = clone.querySelectorAll('.no-print, .action-buttons, .btn');
    elementsToHide.forEach(el => {
        el.style.display = 'none';
    });

    // Agregar temporalmente al documento
    clone.style.position = 'absolute';
    clone.style.left = '-9999px';
    document.body.appendChild(clone);

    // Nombre del archivo
    const cliente = document.getElementById('cliente').value.substring(0, 20).replace(/[^a-zA-Z0-9]/g, '_');
    const fecha = document.getElementById('fecha').value;
    const filename = `Cotizacion_${cliente}_${fecha}.pdf`;

    // Opciones de configuración optimizadas
    const opt = {
        margin: [10, 10, 10, 10],
        filename: filename,
        image: {
            type: 'jpeg',
            quality: 0.95 // Calidad ligeramente reducida para mayor compatibilidad
        },
        html2canvas: {
            scale: 1.5, // Escala reducida
            logging: true,
            useCORS: true,
            allowTaint: false, // Importante para seguridad
            scrollX: 0,
            scrollY: 0,
            windowWidth: clone.scrollWidth,
            windowHeight: clone.scrollHeight,
            backgroundColor: '#FFFFFF'
        },
        jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait',
            compress: true
        }
    };

    // Generar y descargar PDF
    html2pdf()
        .set(opt)
        .from(clone)
        .save()
        .catch(error => {
            console.error('Error al descargar PDF:', error);
            let errorMsg = 'Error al generar PDF para descarga';

            if (error.message.includes('Unsupported image type')) {
                errorMsg = 'Error: Formato de imagen no soportado en el PDF. Verifique las imágenes (use JPG/PNG).';
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
                confirmButtonColor: '#1a4b8c'
            });
        })
        .finally(() => {
            // Asegurarse de eliminar el clon
            if (document.body.contains(clone)) {
                document.body.removeChild(clone);
            }
            loadingSwal.close();
        });
    function verifyImagesBeforePDF() {
        const images = document.querySelectorAll('#cotizacionContent img');
        let problematicImages = [];

        images.forEach(img => {
            if (!img.complete || img.naturalWidth === 0) {
                problematicImages.push(img.src);
            }
        });

        if (problematicImages.length > 0) {
            console.warn('Imágenes con problemas:', problematicImages);
            return false;
        }
        return true;
    }

    // Modificar el inicio de generatePDFPreview:
    if (!verifyImagesBeforePDF()) {
        Swal.fire({
            icon: 'warning',
            title: 'Problema con imágenes',
            text: 'Algunas imágenes no se cargaron correctamente. El PDF podría generarse sin ellas.',
            confirmButtonColor: '#1a4b8c'
        });
    }
}