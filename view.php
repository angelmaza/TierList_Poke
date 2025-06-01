<?php
class View{

    private $title;   // Título de la página.
    private $phtml;   // Almacena el contenido HTML generado.

    // Constructor que inicializa la vista con un ID y un título.
    function __construct(mixed $id = '', string $title = 'TierList'){
        $this->id = $id;
        $this->title = $title;
    }

    // Genera el encabezado HTML de la página.
    public function header(): void{
        $phtml = "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta http-equiv=\"Content-Type\" content=\"text/html;\" charset=\"utf-8\">
            <link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">
            <title>{$this->title}</title>
            <script src='javascript.js'></script>
        </head>
        <body>
        <main>
            <header>
                <h1><a href=\"index.php\">Index</a></h1>
            </header>
            <article>";
        $this->phtml = $phtml;
    }

    

    //el tier ID no necesita name, ya que no se coge con el get. Lo usara JS con getelementbyID
    public function input_index($tier_id="", $name_tier="") {
        $phtml = "<p id='error'></p>";
        $phtml .= "<form method=\"get\" action=\"index.php\">";
        $phtml .= "<label>Nombre Tier: $name_tier</label><input name='tier_name' type='text' id='tier_name' value='$name_tier'>";
        $phtml .= "<input type='hidden' value='$tier_id' id='tier_id'>";
        $phtml .= "<button type=\"submit\" name=\"action\" value=\"mostrar_tier_list\">Buscar lista</button>";
        $phtml .= "</form>";
        $this->phtml .= $phtml;
    }
    

    public function view_tier_list() {
        $phtml = "<div id='tier_list'>";
        $phtml .= "<div class='tier' id='level6' ondragover='event.preventDefault()' ondrop=\"soltar(event, 'level6')\"><p>Tier 6</p></div>";
        $phtml .= "<div class='tier' id='level5' ondragover='event.preventDefault()' ondrop=\"soltar(event, 'level5')\"><p>Tier 5</p></div>";
        $phtml .= "<div class='tier' id='level4' ondragover='event.preventDefault()' ondrop=\"soltar(event, 'level4')\"><p>Tier 4</p></div>";
        $phtml .= "<div class='tier' id='level3' ondragover='event.preventDefault()' ondrop=\"soltar(event, 'level3')\"><p>Tier 3</p></div>";
        $phtml .= "<div class='tier' id='level2' ondragover='event.preventDefault()' ondrop=\"soltar(event, 'level2')\"><p>Tier 2</p></div>";
        $phtml .= "<div class='tier' id='level1' ondragover='event.preventDefault()' ondrop=\"soltar(event, 'level1')\"><p>Tier 1</p></div>";
        
        $phtml .= "</div>"; 
        $this->phtml .= $phtml;
    }

    




    // Genera el pie de página HTML de la página.
    public function footer(): void {
        $phtml = "
            </article>
            <footer>
            
            </footer>
            </main>
            </body>
            </html>";
        $this->phtml .= $phtml;
    }

    public function error($error) {
        $this->phtml .= "Error: $error";
    }

    //funcion para obtener el HTML final
    public function getPhtml() : string {
        return $this->phtml;
    }
}
