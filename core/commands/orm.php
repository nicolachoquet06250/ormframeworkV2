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

    class {$modelName} extends Model { }";

		file_put_contents("custom/mvc/models/{$modelName}.php", $modelContent);
    }

    private function genere_controller() {
        $controllerName = $this->get_from_name('whole');

        $controllerContent = "<?php
        
    namespace ormframework\custom\mvc\controllers;
    
    use ormframework\core\mvc\Controller;

    class {$controllerName} extends Controller { }";
        file_put_contents("custom/mvc/controllers/{$controllerName}.php", $controllerContent);

    }

    private function genere_entity($name = '', $properties = []) {
        $entityName = $name === '' ? $this->get_from_name('whole') : $name;

		$entityContent = "<?php\n\n".

        "\tnamespace ormframework\custom\db_context;\n\n".

        "\tuse \ormframework\core\db_context\\entity;\n\n".

        "\tclass {$entityName} extends entity {\n";

        foreach ($properties as $property => $details) {
            if(isset($details->default)) {
                $entityContent .= "\t\tprivate \${$property} = ".(strtolower($details->type) === 'text' ? "'{$details->default}'" : $details->default).";\n\n";
            }
            else {
                $entityContent .= "\t\tprivate \${$property};\n\n";
            }
        }

        $entityContent .= "\t}";

		file_put_contents("custom/entities/{$entityName}.php", $entityContent);
    }

	private function rm_entity() {
		$entityName = $this->get_from_name('whole');
		unlink("custom/entities/{$entityName}.php");
	}

	private function rm_model() {
		$modelName = $this->get_from_name('whole');
		unlink("custom/mvc/models/{$modelName}.php");
	}

	private function rm_controller() {
		$controllerName = $this->get_from_name('whole');
		unlink("custom/mvc/controllers/{$controllerName}.php");
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

		$model_content = file_get_contents("custom/mvc/models/{$model}.php");

		if(!$description) {
			$description = readline('donnez une rapide description : ');
		}
		if(!$retour) {
			$retour = readline('donnez le type de retour de la méthode : ');
		}
		$retour = ucfirst($retour);

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
			file_put_contents($path_prefix."custom/mvc/models/{$model}.php", $new_model_content);
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
		if($conf = $this->get_manager('services')->conf()->get_sql_conf($bdd_type)[$alias]) {
		    if($bdd_type !== 'mysql' && $bdd_type !== 'pgsql')
                $cnx_array = [
                    'database' => $conf->path_to_database.'/'.$conf->database,
                ];
		    else
		        $cnx_array = (array)$conf;

			$request = Request::getIRequest(new RequestConnexion($cnx_array), $bdd_type);

			$tables = $request->show()->tables()->query();
            foreach ($tables as $table) {
                $filds = $request->show()->columns()->from($table)->query();
                $this->genere_entity($table, $filds);
            }
		}
	}
}