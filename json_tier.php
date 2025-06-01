<?php 
include_once "model.php";
// http://localhost/angel/tema6_async/proyecto/json_tier.php?&actionapi=updatecarga&tier_id=26&idpok=142&lvlpok=5
// http://localhost/angel/tema6_async/proyecto/json_tier.php?&actionapi=cargarinicio&tier_id=26

class tier_api {
    private $error = "";

    public function __construct() {}

    //segun la accion que se mande desde el JS, hara una funcion u otra. Si $error existe, devolvera solo el error como json
    public function main_api() {
        try {
            if (empty($_GET["actionapi"])) {
                $resultado["error"] = "Accion no encontrada";
            } else {
                $action = $_GET["actionapi"];
                switch ($action) {
                    case "cargarinicio":
                        $resultado = $this->cargar_lista_inicio();
                        break;
                    case "updatecarga":
                        $resultado = $this->insert_y_cargar_lista();
                        break;
                    default:
                        $this->error .= "Accion '$action' no valida";
                        break;
                }
            }
        } catch (Throwable $throwable) {
            $this->error .= $throwable->getMessage();
        }
        if($this->error) {
            header('HTTP/1.1 500 Internal Server Error');
            return json_encode(["error" => "-Error: $this->error"]);
        } 
        return json_encode($resultado);
    }

    //En caso de que se cargue la lista sin updatear nada, hace esto. Si hubiese errores, devuelve vacion
    private function cargar_lista_inicio() {
        $array_info_poks_completo = [];
        if (empty($_GET['tier_id'])) {
            $this->error .= 'Falta el idtier';
        } else if (is_numeric($_GET['tier_id']) && $_GET['tier_id'] > 0) {
            $tier_id = (int) $_GET['tier_id'];
        } else {
            $this->error .= '--El tier_id no es valido';
        }
        if(!$this->error) {
            try {
                $m = new Model();
                $listado_ids_lvls = $m->get_pokids_y_tiers_tierlist($tier_id);
        
                foreach($listado_ids_lvls as $pokemon) {
                    $dato_pok = $m->consultarImgName($pokemon['pokemon_species_id']);
                
                    $array_info_poks_completo[] = [
                        'pngname' => $dato_pok[0]['identifier'], 
                        'nombre' => $dato_pok[0]['name'], 
                        'pok_id' => $pokemon['pokemon_species_id'],
                        'level' => $pokemon['level']
                    ]; 
                }
                $m->closeConnection();
            } catch (Throwable $t) {
                $this->error .= $t->getMessage();
            }
        }
        return $array_info_poks_completo;
    }

    //similar a la anterior, pero recogiendo y editando el id y el level de un pokemon
    private function insert_y_cargar_lista() {
        $array_info_poks_completo = [];
        $lvlpok = $_GET['lvlpok'];
        $idpok = $_GET['idpok'];

        if (empty($_GET['tier_id'])) {
            $this->error .= '--Falta el idtier';
        } else if (is_numeric($_GET['tier_id']) && $_GET['tier_id'] > 0) {
            $tier_id = (int) $_GET['tier_id'];
        } else {
            $this->error .= '--El tier_id no es valido';
        }
    
        //Validacion de datos
        if (!is_numeric($lvlpok) || !is_numeric($idpok) || $lvlpok <= 0 || $lvlpok > 6 || $idpok <= 0) {
            $this->error .= "--Los datos introducidos como lvlpok o idpok no son validos";
        } else {
            if (!$this->error) {
                $status = false; //se usara para ver que el update salio bien (devuelve true o false)
                try {
                    $m = new Model();
                    $status = $m->actualizar_level_pok([$lvlpok, $tier_id, $idpok]);
                    $listado_ids_lvls = $m->get_pokids_y_tiers_tierlist($tier_id);
                    $array_info_poks_completo = [];
            
                    foreach($listado_ids_lvls as $pokemon) {
                        $dato_pok = $m->consultarImgName($pokemon['pokemon_species_id']);
                    
                        $array_info_poks_completo[] = [
                            'pngname' => $dato_pok[0]['identifier'], 
                            'nombre' => $dato_pok[0]['name'], 
                            'pok_id' => $pokemon['pokemon_species_id'],
                            'level' => $pokemon['level']
                        ]; 
                    }
                    
                    $m->closeConnection();
                } catch (Throwable $t) {
                    $this->error .= $t->getMessage();
                }
            }
            if(isset($status)) { //que es mejor, comprobar que esta, o crear una variable a inicio funcion....
                if($status === false) {
                    $this->error .= '--Fallo al hacer update en el tier';
                }
            }
        }
        return $array_info_poks_completo;
    }
}

//Lanzamiento
$api = new tier_api();
echo $api->main_api();








