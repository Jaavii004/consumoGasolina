<?php
// Configuración de la base de datos
$servername = "192.168.5.8:33006";
$username = "root";
$password = "dbrootpass";
$dbname = "gasolinera";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// URL de la API
$url = "https://sedeaplicaciones.minetur.gob.es/ServiciosRESTCarburantes/PreciosCarburantes/EstacionesTerrestres/";

// Inicializar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Ejecutar la solicitud
$response = curl_exec($ch);
curl_close($ch);

// Verificar si la respuesta está vacía o hay un error en cURL
if ($response === false) {
    die("Error en la solicitud cURL: " . curl_error($ch));
}

// Decodificar la respuesta JSON
$data = json_decode($response, true);

// Verificar la estructura de la respuesta
if ($data === null) {
    die("Error al decodificar JSON: " . json_last_error_msg());
}

// Verificar la estructura de la respuesta
if (isset($data['ListaEESSPrecio'])) {
    foreach ($data['ListaEESSPrecio'] as $item) {
        // Filtrar por municipio, en este caso Valencia
	    $gasolinera = $conn->real_escape_string($item['Rótulo']);
	    $direccion = $conn->real_escape_string($item['Dirección']);
	    $municipio = $conn->real_escape_string($item['Municipio']);
	    $precio_gasolina95 = isset($item['Precio Gasolina 95 E5']) ? floatval(str_replace(',', '.', $item['Precio Gasolina 95 E5'])) : null;
	    $precio_gasolina98 = isset($item['Precio Gasolina 98 E5']) ? floatval(str_replace(',', '.', $item['Precio Gasolina 98 E5'])) : null;
	    $precio_diesel = isset($item['Precio Gasoleo A']) ? floatval(str_replace(',', '.', $item['Precio Gasoleo A'])) : null;
	    $fecha_actualizacion = date("Y-m-d H:i:s");

	    // Preparar la declaración SQL para insertar los datos en la base de datos
	    $sql = "INSERT INTO precios_gasolineras (gasolinera, direccion, municipio, precio_gasolina95, precio_gasolina98, precio_diesel, fecha_actualizacion)
		    VALUES ('$gasolinera', '$direccion', '$municipio', '$precio_gasolina95', '$precio_gasolina98', '$precio_diesel', '$fecha_actualizacion')";

	    // Ejecutar la consulta
	    if ($conn->query($sql) === TRUE) {

	    } else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	    }
    }
} else {
    echo "No se pudieron obtener los datos de las gasolineras.";
}

// Cerrar la conexión
$conn->close();
?>