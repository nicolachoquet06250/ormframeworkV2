<?php
namespace ormframework\core\commands;

use \Exception;
use sql_links\factories\Request;
use sql_links\factories\RequestConnexion;
use sql_links\requests\Json;


class orm extends command
{

    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    private function genere_model() {
        $modelName = $this->get_from_name('whole');

		$modelContent = "<?php
        
    namespace ormframework\custom\mvc\models;
    
    use ormframework\core\mvc\Model;

    class {$modelName}_controller extends Model { }";

		file_put_contents("custom/mvc/models/{$modelName}_model.php", $modelContent);
    }

    private function genere_controller() {
        $controllerName = $this->get_from_name('whole');

        $controllerContent = "<?php
        
    namespace ormframework\custom\mvc\controllers;
    
    use ormframework\core\mvc\Controller;

    class {$controllerName}_controller extends Controller { }";
        file_put_contents("custom/mvc/controllers/{$controllerName}_controller.php", $controllerContent);

    }

    private function genere_entity() {
        $entityName = $this->get_from_name('whole');

		$entityContent = "<?php

	namespace ormframework\custom\db_context;

	use \ormframework\core\db_context\\entity;

    class {$entityName} extends entity { }";

		file_put_contents("custom/entities/{$entityName}.php", $entityContent);
    }

	private function rm_entity() {
		$entityName = $this->get_from_name('whole');
		unlink("custom/entities/{$entityName}.php");
	}

	private function rm_model() {
		$modelName = $this->get_from_name('whole');
		unlink("custom/mvc/models/{$modelName}_model.php");
	}

	private function rm_controller() {
		$controllerName = $this->get_from_name('whole');
		unlink("custom/mvc/controllers/{$controllerName}_controller.php");
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function add_method_to_model() {
		$model = $this->get_from_name('model');
		$method = $this->get_from_name('method');
		if(!$model || !$method) {
			return;
		}
		$retour = $this->get_from_name('return');
		$description = $this->get_from_name('description');
		$route = $this->get_from_name('route');

		$model_content = file_get_contents("custom/mvc/models/{$model}_model.php");

		if(!$description) {
			$description = readline('donnez une rapide description : ');
		}
		if(!$retour) {
			$retour = readline('donnez le type de retour de la méthode : ');
		}
		$retour = ucfirst($retour).'_view';

		$method_content = "
	/**
	 * @model {$model}
	 ".( $description ? "* @description {$description}" : "* ")."
	 * @method {$method}
	 * @param mixed \$args
	 * @return {$retour}
	 ".( $route ? "* @route {$route}" : "* ")."
	 **/
	public function {$method}(\$args) {
		// TODO : mettez votre code ici.
		return new {$retour}([]);
	}";

		$new_model_content = str_replace("\t}\n}", "\t}\n{$method_content}\n}", $model_content);

		global $path_prefix;

		if(!strstr($model_content, "public function {$method}(")) {
			file_put_contents($path_prefix."custom/mvc/models/{$model}_model.php", $new_model_content);
			return true;
		}
		throw new \Exception("La méthode {$method} du model {$model} existe déja");
	}

    /**
     * génère un ensemble (whole) de model, controllers, entities en fonction d'une bdd sql ou json
     */
    public function new_whole() {
        $this->genere_controller();
        $this->genere_model();
        $this->genere_entity();
    }

	/**
	 * supprime un ensemble (whole) de model, controllers, entities
	 */
    public function rm_whole() {
		$this->rm_controller();
		$this->rm_model();
		$this->rm_entity();
	}

    /**
     * @throws Exception
     */
	public function start() {
        require_once 'custom/sql_links/autoload.php';
		$alias = $this->get_from_name('alias');
		$bdd_type = $this->get_from_name('bdd_type');
		if($conf = $this->get_manager('services')->conf()->get_sql_conf($bdd_type)) {
			$connexion = $conf[$alias];
			$cnx = new RequestConnexion(
			    [
			        'database' => $connexion->path_to_database.'/'.$connexion->database
                ]
            );
			$request = Request::getIRequest($cnx, 'json');
            $request->create(Json::TABLE, 'user')
                    ->set([
                        'id' => [
                            'type' 		=> 'INT',
                            'key' 		=> 'primary',
                            'increment' => 'auto',
                        ],
                        'nom' => [
                            'type'		=> 'TEXT',
                        ],
                        'prenom' => [
                            'type' 		=> 'TEXT',
                        ],
                        'age' => [
                            'type' 		=> 'INT',
                        ],
                        'ecole' => [
                            'type'		=> 'TEXT',
                            'default'	=> 'CampusID',
                        ],
                    ])->query();
			$tables = $request->show()->tables()->query();
			var_dump($tables);
		}
	}
}