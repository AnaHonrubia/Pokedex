<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Pokédex</title>
    <style>
        /* CSS básico */
        body { font-family: Arial, sans-serif; margin: 40px; }
        .resultado { background: #f4f4f4; padding: 20px; border-radius: 8px; margin-top: 20px;}
    </style>
</head>
<body>

    <h1>Pokédex: Calculadora de Tipos</h1>

    <form method="GET" action="">
        <label for="tipo">Elige un tipo de Pokémon:</label>
        <select name="tipo" id="tipo">
            <option value="ground">Tierra</option>
            <option value="water">Agua</option>
            <option value="fire">Fuego</option>
            <option value="grass">Planta</option>
            <option value="electric">Eléctrico</option>
        </select>
        <button type="submit">Analizar Tipo</button>
    </form>

    <?php
    // Comprobamos si la URL tiene el parámetro "tipo" 
    $tipoSeleccionado = $_GET['tipo'] ?? '';

    // Si el usuario ha seleccionado un tipo, ejecutamos el código
    if ($tipoSeleccionado != '') {
        
        // Preparamos la URL de la API sumándole el tipo que eligió el usuario
        $url = "https://pokeapi.co/api/v2/type/" . $tipoSeleccionado;

        // función de PHP que va a esa URL y se trae todo el texto
        $respuestaJSON = @file_get_contents($url);

        // Si la respuesta es válida 
        if ($respuestaJSON) {
            
            // La API nos devuelve un texto en formato JSON. 
            // se convierte en un Array de PHP para que podamos leerlo.
            $datos = json_decode($respuestaJSON, true);
            
            $relaciones = $datos['damage_relations'];

            // Imprimimos el HTML con los resultados
            echo "<div class='resultado'>";
            echo "<h2>Resultados para el tipo: " . ucfirst($tipoSeleccionado) . "</h2>";

            // -- FUERTE CONTRA --
            echo "<h3> Hace doble daño a (Fuerte contra):</h3>";
            echo "<ul>";
            // Si la lista está vacía
            if (empty($relaciones['double_damage_to'])) {
                echo "<li>A ninguno en especial</li>";
            } else {
                // Si hay datos, los recorremos uno por uno con un bucle 
                foreach($relaciones['double_damage_to'] as $tipoDañado) {
                    echo "<li>" . ucfirst($tipoDañado['name']) . "</li>";
                }
            }
            echo "</ul>";

            // -- DÉBIL CONTRA --
            echo "<h3>Recibe doble daño de (Débil contra):</h3>";
            echo "<ul>";
            if (empty($relaciones['double_damage_from'])) {
                echo "<li>De ninguno en especial</li>";
            } else {
                foreach($relaciones['double_damage_from'] as $tipoAtacante) {
                    echo "<li>" . ucfirst($tipoAtacante['name']) . "</li>";
                }
            }
            echo "</ul>";

            echo "</div>"; // Cerramos el div de resultado

        } else {
            // Si la API falla
            echo "<p style='color:red;'>Error al conectar con la PokéAPI.</p>";
        }
    }
    ?>

</body>
</html>