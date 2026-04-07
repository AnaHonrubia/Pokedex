<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Pokédex</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Pokédex Nacional</h1>

    <?php
    // Comprobamos si el usuario ha pinchado en un Pokémon concreto
    $pokemonSeleccionado = $_GET['pokemon'] ?? '';

    // ==========================================
    // VISTA 1: PORTADA (LISTA DE POKÉMON)
    // ==========================================
    if ($pokemonSeleccionado == '') {
        
        // Pedimos los primeros 200 Pokémon a la API
        $urlLista = "https://pokeapi.co/api/v2/pokemon?limit=200";
        $datosLista = json_decode(@file_get_contents($urlLista), true);

        if ($datosLista) {
            // Sacamos el número total de registros directamente de esta respuesta
            $totalPokemon = $datosLista['count'];
            echo "<p>Actualmente hay <strong>{$totalPokemon}</strong> registros en la base de datos (incluyendo formas alternativas).</p>";
            echo "<p>Selecciona un Pokémon para ver su información de combate (Primeros 200 Pokemon).</p>";
            
            echo "<div class='pokedex-grid'>";

            // Recorremos los 200 Pokémon para crear la cuadrícula
            foreach ($datosLista['results'] as $poke) {
                $nombre = ucfirst($poke['name']);
                
                // Truco para conseguir la foto rápido sin hacer más peticiones a la API
                $partes = explode('/', rtrim($poke['url'], '/'));
                $id = end($partes);
                $imagen = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png";

                echo "<a href='?pokemon={$poke['name']}' class='pokemon-card'>";
                echo "<img src='{$imagen}' alt='{$nombre}'>";
                echo "<h3>#{$id} {$nombre}</h3>";
                echo "</a>";
            }
        } else {
            echo "<p>Error al cargar la Pokédex.</p>";
        }
        echo "</div>";
    } 
    // ==========================================
    // VISTA 2: FICHA DEL POKÉMON
    // ==========================================
    else {
        // Pedimos todos los datos del Pokémon que hemos pinchado
        $urlPokemon = "https://pokeapi.co/api/v2/pokemon/" . $pokemonSeleccionado;
        $datosPoke = json_decode(@file_get_contents($urlPokemon), true);

        if ($datosPoke) {
            $nombre = ucfirst($datosPoke['name']);
            // Buscamos la imagen oficial, si no existe, ponemos la básica
            $imagen = $datosPoke['sprites']['other']['official-artwork']['front_default'] ?? $datosPoke['sprites']['front_default'];

            echo "<div class='ficha'>";
            echo "<a href='index.php' class='volver'>&larr; Volver a la Pokédex</a>";
            echo "<img src='{$imagen}' alt='{$nombre}'>";
            echo "<h2>{$nombre}</h2>";

            // Mostramos los TIPOS del Pokémon
            echo "<div class='tipos'>";
            foreach ($datosPoke['types'] as $tipoData) {
                echo "<span class='tipo-badge'>" . $tipoData['type']['name'] . "</span>";
            }
            echo "</div>";

            echo "<div class='estadisticas'>";
            
            // Analizamos ventajas y debilidades de su PRIMER tipo
            $urlTipo = $datosPoke['types'][0]['type']['url'];
            $datosTipo = json_decode(@file_get_contents($urlTipo), true);
            $relaciones = $datosTipo['damage_relations'];

            // FUERTE CONTRA
            echo "<div class='columna'>";
            echo "<h3 class='fuerte'>💪 Fuerte contra:</h3>";
            echo "<ul>";
            if (empty($relaciones['double_damage_to'])) {
                echo "<li>Ninguno</li>";
            } else {
                foreach ($relaciones['double_damage_to'] as $tipoFuerte) {
                    echo "<li>" . ucfirst($tipoFuerte['name']) . "</li>";
                }
            }
            echo "</ul>";
            echo "</div>";

            // DÉBIL CONTRA
            echo "<div class='columna'>";
            echo "<h3 class='debil'>💔 Débil contra:</h3>";
            echo "<ul>";
            if (empty($relaciones['double_damage_from'])) {
                echo "<li>Ninguno</li>";
            } else {
                foreach ($relaciones['double_damage_from'] as $tipoDebil) {
                    echo "<li>" . ucfirst($tipoDebil['name']) . "</li>";
                }
            }
            echo "</ul>";
            echo "</div>";

            echo "</div>"; // Cierra estadísticas
            echo "</div>"; // Cierra ficha
        } else {
            echo "<p>Error: Pokémon no encontrado.</p>";
            echo "<a href='index.php'>Volver</a>";
        }
    }
    ?>

</body>
</html>