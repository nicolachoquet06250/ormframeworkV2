<?php
namespace ormframework\core\commands;

use \Exception;
use sql_links\factories\Request;
use sql_links\factories\RequestConnexion;


class orm extends command
{

    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    private function genere_model($name = '') {
        $modelName = $name === '' ? $this->get_from_name('whole') : $name;
        $modelContent = "<?php\n\n".
        "\tnamespace ormframework\custom\mvc\models;\n\n".
        "\tuse ormframework\core\mvc\Model;\n\n".
        "\tclass {$modelName} extends Model { }";
        file_put_contents("custom/mvc/models/{$modelName}.php", $modelContent);
    }

    private function genere_controller($name = '') {
        $controllerName = $name === '' ? $this->get_from_name('whole') : $name;
        $controllerContent = "<?php\n\n".
        "\tnamespace ormframework\custom\mvc\controllers;\n\n".
        "\tuse ormframework\core\mvc\Controller;\n\n".
        "\tclass {$controllerName} extends Controller { }";
        file_put_contents("custom/mvc/controllers/{$controllerName}.php", $controllerContent);

    }

    private function genere_entity($name = '', $properties = []) {
        $entityName = $name === '' ? $this->get_from_name('whole') : $name;
		$entityContent = "<?php\n\n".
        "\tnamespace ormframework\custom\db_context;\n\n".
        "\tuse \ormframework\core\db_context\\entity;\n\n".
		"\t/**\n";
		foreach ($properties as $property => $details) {
		    $entityContent .= "\t * @method ".(strtolower($details->type) === 'text' ? "string" : "integer")." {$property}(".(strtolower($details->type) === 'text' ? "string" : "integer")." \${$property} = null)\n";
		}
		$entityContent .= "\t **/\n";
        $entityContent .= "\tclass {$entityName} extends entity {\n";
        foreach ($properties as $property => $details) {
            $entityContent .= isset($details->default) ?
                "\t\tprotected \${$property} = ".
                (strtolower($details->type) === 'text' ? "'{$details->default}'" : $details->default)
                .";\n"
                    : "\t\tprotected \${$property};\n";
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
     * @param string $name
     */
    public function new_whole($name = '') {
        $this->genere_controller($name);
        $this->genere_model($name);
        $this->genere_entity($name);
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
		    else $cnx_array = (array)$conf;

			$request = Request::getIRequest(new RequestConnexion($cnx_array), $bdd_type);

			$tables = $request->show()->tables()->query();
            foreach ($tables as $table) {
                $filds = $request->show()->columns()->from($table)->query();
                $this->new_whole($table);
                $this->genere_entity($table, $filds);
            }
		}
	}
}