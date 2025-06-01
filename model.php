<?php


class Model {
    private $con;  // Variable para almacenar la conexión a la base de datos.

    // Constructor que inicializa la conexión a la base de datos.
    function __construct(string $db = "pokemon", string $host = "localhost", string $user = "root", string $pass = "") {
        $this->con = new mysqli($host, $user, $pass, $db);  // Crea una conexión utilizando MySQLi.
        if ($this->con->connect_error) {
            // Si ocurre un error de conexión, detiene la ejecución con un mensaje de error.
            die('(' . $this->con->connect_errno . ') ' . $this->con->connect_error);
        }
    }

    function closeConnection() {
        $this->con->close();
    }


    //Id del tier a partir del nombre
    public function get_tier_id(string $nombre_tier): array {
        $query = "SELECT id FROM tiers
                  WHERE name = ?;";
        return $this->exe_sentencia_preparada($query, "s", [$nombre_tier]);
    }

    public function crear_tier(string $nombre_tier) {
        $query = "INSERT INTO tiers (name) VALUES (?)";
        return $this->exe_sentencia_preparada($query, "s", [$nombre_tier]);
    }

    public function insert_pok_level1(int $tier_id, int $pokemon_id) {
        $query = "INSERT INTO tiers_pokemon (tier_id, pokemon_species_id, level) VALUES (?, ?, 1)";
        $parametros = [$tier_id, $pokemon_id];
        return $this->exe_sentencia_preparada($query, "ii", $parametros);
    }

    public function get_pokids_y_tiers_tierlist(int $tier_id): array {
        $query = "SELECT pokemon_species_id, level level FROM tiers_pokemon
                  WHERE tier_id = ?;";
        return $this->exe_sentencia_preparada($query, "i", [$tier_id]);
    }

    //devuelve el nombre valido para los png, y el nombre "bonito"
    function consultarImgName($idpok) {
        $consulta = 
            "SELECT pf.identifier, psn.name
            FROM pokemon_species_names psn
            LEFT JOIN pokemon_forms pf
            ON psn.pokemon_species_id = pf.id
            WHERE psn.local_language_id = 7
            and psn.pokemon_species_id = ?;";
        $prepares = 'i';
        $parametros = [$idpok]; 
        return $this->exe_sentencia_preparada($consulta, $prepares, $parametros); 
    }

    public function actualizar_level_pok(array $datos_update): ?array {
        $query = "
            UPDATE tiers_pokemon 
            SET level = ? 
            WHERE tier_id = ? AND pokemon_species_id = ?;";
            return $this->exe_sentencia_preparada($query, "iii", $datos_update);
    }
    
    private function exe_consulta(string $query): array {
        $result = $this->con->query($query);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    //recoge 10 pokemons validos (<10000), y los ordena de forma aleatoria para asi obtener nuestros 10 pok aleatorios
    public function get_id_pok_randoms() : array {
        $querry = "SELECT id
        FROM pokemon
        WHERE id < 10000
        ORDER BY RAND()
        LIMIT 10";
        return $this->exe_consulta($querry);
    }

    private function exe_sentencia_preparada(string $query, string $types, array $values, bool $single_result = false): ?array {
        $stmt = $this->con->stmt_init();  // Inicializa un objeto `stmt` para preparar la consulta.
        $stmt->prepare($query);  // Prepara la consulta SQL.
        $status = $stmt->bind_param($types, ...$values);  // Vincula los parámetros.
        if($status == false)
            return null;
        $status = $stmt->execute();  // Ejecuta la consulta.
        if($status == false) {
            $stmt->close();  // Si hay error, cierra el `stmt` y retorna `null`.
            return null;
        }
        $item_set = $stmt->get_result();  // Obtiene los resultados de la consulta.
        $stmt->close();  // Cierra el `stmt`.

        if($item_set == false)
            return null;

        // Determina si debe retornar un solo resultado o todos los resultados.
        if($single_result == true)
            $item = $item_set->fetch_assoc();  // Retorna una fila como un array asociativo.
        else
            $item = $item_set->fetch_all(MYSQLI_ASSOC);  // Retorna todas las filas.
        $item_set->free_result();  // Libera los resultados.
        return $item;
    }

}
?>