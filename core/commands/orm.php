<?php

class orm extends command
{

    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    public function start() {

    }

    private function genere_model() {
        $modelName = $this->get_from_name('whole');
    }

    private function genere_controller() {
        $controllerName = $this->get_from_name('whole');

        $controllerContent = "<?php

    class {$controllerName}_controller extends Controller { }";
        file_put_contents("custom/mvc/controllers/{$controllerName}_controller.php", $controllerContent);

    }

    private function genere_entity() {
        $entityName = $this->get_from_name('whole');
    }

    /**
     * génère un ensemble (whole) de model, controllers, entities en fonction d'une bdd sql ou json
     */
    public function new_whole() {
        $this->genere_controller();
        $this->genere_model();
        $this->genere_entity();
    }
}