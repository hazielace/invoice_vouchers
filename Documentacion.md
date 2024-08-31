

##### DOCUMENTACION DE LAS NUEVAS FUNCIONALIDADES

### 1. Registro de serie, número, tipo del comprobante y moneda

Se han creado una migración para agregar las nuevas columnas en la base de datos: series, number, voucher_type  y currency.
Un script de migración adicional ha sido diseñado para extraer la información relevante del campo xml_content y actualizar las nuevas columnas en los registros existentes.

Ejecutar : php artisan migrate

### 2. Carga de comprobantes en segundo plano

Eventos y Listeners: Se ha configurado un evento que se dispara al iniciar la carga de comprobantes, y un listener que maneja el proceso en segundo plano.
Job: Se ha creado un job que gestiona la carga de comprobantes

Para probar la funcionalidad en el .ENV agregar el correo y contraseña

### 3. Endpoint de montos totales

Se ha creado un nuevo endpoint utilizando el método GET para proporcionar los montos totales acumulados.

### 4. Eliminación de comprobantes

Se ha creado un nuevo endpoint utilizando el método POST para eliminar comprobantes.

### 5. Filtro en listado de comprobantes

SeSe ha extendido el endpoint de listado existente con parámetros de búsqueda para filtrar los resultados según serie, número y rango de fechas de creación.
