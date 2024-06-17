<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Gestión de Patio</title>
    <style>
        /* Estilos CSS existentes */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .chart-container {
            width: 30%;
            height: 200px;
            margin: 10px;
        }
        .charts {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 100px;
        }
        canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
    <!-- Incluir jQuery desde un CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Incluir Chart.js desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            function cargarDashboard() {
                $.ajax({
                    url: 'datos_dashboard.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Procesar los datos para el gráfico de barras
                        var labelsBarra = response.barra.map(function(item) { return item.alquiladora; });
                        var dataBarra = response.barra.map(function(item) { return item.cantidad_camiones; });

                        var datosGraficoBarra = {
                            labels: labelsBarra,
                            datasets: [{
                                label: 'Cantidad de Camiones',
                                backgroundColor: ['#ffcd56', '#36a2eb', '#ff6384', '#fd6b19'],
                                data: dataBarra
                            }]
                        };

                        var opcionesGraficoBarra = {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        };

                        var ctxBarra = document.getElementById('grafico-barras').getContext('2d');
                        new Chart(ctxBarra, {
                            type: 'bar',
                            data: datosGraficoBarra,
                            options: opcionesGraficoBarra
                        });

                        // Procesar los datos para el gráfico de pastel
                        var labelsPastel = response.pastel.map(function(item) { return item.estado; });
                        var dataPastel = response.pastel.map(function(item) { return item.cantidad; });

                        var datosGraficoPastel = {
                            labels: labelsPastel,
                            datasets: [{
                                label: 'Estado de Camiones',
                                backgroundColor: ['#ffcd56', '#36a2eb', '#ff6384', '#fd6b19'],
                                data: dataPastel
                            }]
                        };

                        var opcionesGraficoPastel = {
                            responsive: true,
                            maintainAspectRatio: false
                        };

                        var ctxPastel = document.getElementById('grafico-pastel').getContext('2d');
                        new Chart(ctxPastel, {
                            type: 'pie',
                            data: datosGraficoPastel,
                            options: opcionesGraficoPastel
                        });

                        // Cargar datos para gráfico de líneas y tabla
                        cargarDatosTotalesEstadoAlquiladora();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar datos:', error);
                    }
                });
            }

            function cargarDatosTotalesEstadoAlquiladora() {
                $.ajax({
                    url: 'totales_estado_alquiladora.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var alquiladoras = Object.keys(response);
                        var estados = Object.keys(response[alquiladoras[0]]);

                        var datasetsLineas = alquiladoras.map(function(alquiladora) {
                            return {
                                label: alquiladora,
                                data: estados.map(function(estado) { return response[alquiladora][estado] || 0; }),
                                fill: false,
                                borderColor: '#' + Math.floor(Math.random()*16777215).toString(16),
                                tension: 0.1
                            };
                        });

                        var opcionesGraficoLineas = {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        };

                        var ctxLineas = document.getElementById('grafico-lineas').getContext('2d');
                        new Chart(ctxLineas, {
                            type: 'line',
                            data: {
                                labels: estados,
                                datasets: datasetsLineas
                            },
                            options: opcionesGraficoLineas
                        });

                        var tablaHTML = '<table><tr><th>Alquiladora</th>';
                        estados.forEach(function(estado) {
                            tablaHTML += '<th>' + estado + '</th>';
                        });

                        alquiladoras.forEach(function(alquiladora) {
                            var totalDisponibles = 0;
                            var totalNoDisponibles = 0;
                            tablaHTML += '<tr><td>' + alquiladora + '</td>';
                            estados.forEach(function(estado) {
                                var cantidad = response[alquiladora][estado] || 0;
                                tablaHTML += '<td>' + cantidad + '</td>';
                                if (estado.toLowerCase() === 'disponible') {
                                    totalDisponibles += cantidad;
                                } else {
                                    totalNoDisponibles += cantidad;
                                }
                            });
                        });

                        tablaHTML += '</table>';
                        $('#totales-estado-alquiladora').html(tablaHTML);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar datos de totales de estado y alquiladora:', error);
                    }
                });
            }

            function cargarDatos() {
                $.ajax({
                    url: 'total_camiones.php',
                    type: 'GET',
                    success: function(response) {
                        var data = JSON.parse(response);

                        // Actualizar el título del dashboard con los totales
                        $('#dashboard-title').text(
                            'Dashboard de Gestión de Patio - Total de Camiones: ' + data.total_camiones
                        );

                        // Mostrar total de camiones
                        $('#total-camiones').text(data.total_camiones);
                        // Mostrar total de camiones no disponibles
                        $('#total-camiones-no-disponibles').text(data.total_camiones_no_disponibles);
                        // Mostrar total de camiones por línea
                        var camionesPorLineaHTML = '<table><tr><th>Línea</th><th>Total Camiones';
                        data.camiones_por_linea.forEach(function(item) {
                            camionesPorLineaHTML += '<tr><td>' + item.Linea + '</td><td>' + item.Total_Camiones ;
                        });
                        camionesPorLineaHTML += '</table>';
                        $('#camiones-por-linea').html(camionesPorLineaHTML);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar datos:', error);
                    }
                });
            }

            cargarDatos();
            cargarDashboard();
            
            setInterval(cargarDashboard, 60000); // Actualizar cada 60 segundos
        });
    </script>
</head>
<body>
    <h1 id="dashboard-title">Dashboard de Gestión de Patio</h1>

    <div class="charts">
        <div class="chart-container">
            <h2>Gráfico de Barras - Cantidad de Camiones por Alquiladora</h2>
            <canvas id="grafico-barras"></canvas>
        </div>

        <div class="chart-container">
            <h2>Gráfico de Pastel - Estado de los Camiones</h2>
            <canvas id="grafico-pastel"></canvas>
        </div>

        <div class="chart-container">
            <h2>Gráfico de Líneas - Totales por Estado y Alquiladora</h2>
            <canvas id="grafico-lineas"></canvas>
        </div>
    </div>

    <div id="totales-estado-alquiladora" style="margin-top: 20px;"></div>

    <div>
        <h2>Total de Camiones: <span id="total-camiones"></span></h2>
        <h2>Total de Camiones No Disponibles: <span id="total-camiones-no-disponibles"></span></h2>
    </div>

    <div id="camiones-por-linea">
       <!-- Aquí se insertará la tabla con el total de camiones por línea -->
       </div>


</body>
</html>
