
<?php
include_once "model.php"; 
include_once "view.php";

class Controller {
    

    public function __construct() {}

    public function main() {
        $vista = new View();
        $vista->header();
        $vista->input_index();
        $vista->footer();

        return $vista->getPhtml();

    }

    //solo muestra la cuadricula, e inserta los poks si no los hubiese en la tabla, el resto lo hace el JS
    public function mostrar_tier_list() { 
        $vista = new View();
        $vista->header();

        if(isset($_GET['tier_name'])) {
            $tier_name = $_GET['tier_name'];
            $model = new Model;
            $tier_id = $model->get_tier_id($tier_name);  //ES ARRAY

            if(!$tier_id) { //si no hay lista con ese nombre y devuelve vacio:
                $model->crear_tier($tier_name);
                $tier_id = $model->get_tier_id($tier_name); 

                $ids_pok_random = $model->get_id_pok_randoms();
                foreach($ids_pok_random as $id_pok) {
                    $model->insert_pok_level1($tier_id[0]['id'], $id_pok['id']);
                    
                }

            }
            $vista->input_index($tier_id[0]['id'], $tier_name);
            $vista->view_tier_list();
        }
        else {
            $vista->error("Fallo con el tier name");
            $vista->footer();
        }
        
        return $vista->getPhtml();

    }

    public function showMethodError() {
        $action = $_GET['action'] ?? 'Falta accion';
        $error = "Fallo con el texto '$action' en la url";
        $v = new View();
        $v->header();
        $v->error($error);
        $v->footer();
        return $v->getPhtml();
    }
    

}


?>