<?php
namespace ormframework\core\commands;

use \Exception;
use ormframework\core\db_context\entity;
use sql_links\factories\Request;
use sql_links\factories\RequestConnexion;


class orm extends command
{

    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    private function genere_model() {

        $modelName = $this->get_from_name('whole');

        $methods = [
            [
                "annotations" => [
                    "model" => $modelName,
                    "description" => "récupère tous les {$modelName}",
                    "method" => "get",
                    "param" => "mixed \$args",
                    "return" => "Json",
                    "route" => $modelName.'/get',
                    "throws" => "\\Exception"
                ],
                "name" => "get",
                "content" => [
                    "if(\$conf = \$this->get_manager('services')->conf()->get_sql_conf('{$this->get_from_name('bdd_type')}')['{$this->get_from_name('alias')}']) {\n",
                    "\t\$request = Request::getIRequest(new RequestConnexion((array)\$conf, '{$this->get_from_name('bdd_type')}'), '{$this->get_from_name('bdd_type')}');\n",
                    "\t\$retour = \$request->select()->from('{$modelName}')->query()->get(\$this->get_from_name('id', \$args));\n",
                    "}\n",
                    "else {\n",
                    "\t\$retour = [];\n",
                    "}\n",
                    "return new Json(\$retour);\n"
                ]
            ],
            [
                "annotations" => [
                    "model" => $modelName,
                    "description" => "ajoute un {$modelName}",
                    "method" => "add",
                    "param" => "mixed \$args",
                    "return" => "Json",
                    "route" => $modelName.'/add',
                    "throws" => "\\Exception"
                ],
                "name" => "add",
                "content" => [
                    "if(\$conf = \$this->get_manager('services')->conf()->get_sql_conf('{$this->get_from_name('bdd_type')}')['{$this->get_from_name('alias')}']) {\n",
                    "\t\$request = Request::getIRequest(new RequestConnexion((array)\$conf, '{$this->get_from_name('bdd_type')}'), '{$this->get_from_name('bdd_type')}');\n",
                    "\t\${$modelName} = new \ormframework\custom\db_context\\".$modelName."(\$request, false, [{params_array}]);\n",
                    "\t\${$modelName}->add();\n",
                    "\t\$retour = \$request->select()->from('{$modelName}')->query()->get();\n",
                    "}\n",
                    "else {\n",
                    "\t\$retour = [];\n",
                    "}\n",
                    "return new Json(\$retour);\n"
                ]
            ],
            [
                "annotations" => [
                    "model" => $modelName,
                    "description" => "supprime un {$modelName}",
                    "method" => "delete",
                    "param" => "mixed \$args",
                    "return" => "Json",
                    "route" => $modelName.'/delete',
                    "throws" => "\\Exception"
                ],
                "name" => "delete",
                "content" => [
                    "if(\$conf = \$this->get_manager('services')->conf()->get_sql_conf('{$this->get_from_name('bdd_type')}')['{$this->get_from_name('alias')}']) {\n",
                    "\t\$request = Request::getIRequest(new RequestConnexion((array)\$conf, '{$this->get_from_name('bdd_type')}'), '{$this->get_from_name('bdd_type')}');\n",
                    "\t\${$modelName} = new \ormframework\custom\db_context\\".$modelName."(\$request, false, [['id' => \$this->get_from_name('id', \$args)]]);\n",
                    "\t\${$modelName}->remove();\n",
                    "\t\$retour = \$request->select()->from('{$modelName}')->query()->get();\n",
                    "}\n",
                    "else {\n",
                    "\t\$retour = [];\n",
                    "}\n",
                    "return new Json(\$retour);\n"
                ]
            ],
            [
                "annotations" => [
                    "model" => $modelName,
                    "description" => "modifie certains champs d'un {$modelName}",
                    "method" => "update",
                    "param" => "mixed \$args",
                    "return" => "Json",
                    "route" => $modelName.'/update',
                    "throws" => "\\Exception"
                ],
                "name" => "update",
                "content" => [
                    "if(\$conf = \$this->get_manager('services')->conf()->get_sql_conf('{$this->get_from_name('bdd_type')}')['{$this->get_from_name('alias')}']) {\n",
                    "\t\$request = Request::getIRequest(new RequestConnexion((array)\$conf, '{$this->get_from_name('bdd_type')}'), '{$this->get_from_name('bdd_type')}');\n",
                    "\t/**\n",
                    "\t * @var \ormframework\core\db_context\\entity \${$modelName}\n",
                    "\t */\n",
                    "\t\${$modelName} = \$request->select()->from('{$modelName}')->where(['id' => \$this->get_from_name('id', \$args)])->query()->get(0);\n",
                    "\tforeach(\${$modelName}->get_not_null_props() as \$prop) {\n",
                    "\t\tif(\$this->get_from_name(\$prop, \$args) !== null) {\n",
                    "\t\t\t\${$modelName}->\$prop(\$this->get_from_name(\$prop, \$args));\n",
                    "\t\t}\n",
                    "\t}\n",
                    "\t\$retour = \$request->select()->from('{$modelName}')->query();\n",
                    "}\n",
                    "else {\n",
                    "\t\$retour = [];\n",
                    "}\n",
                    "return new Json(\$retour);\n"
                ]
            ],
        ];
        $modelContent = "<?php\n\n".
        "\tnamespace ormframework\custom\mvc\models;\n\n".
        "\tuse ormframework\core\mvc\Model;\n".
        "\tuse \ormframework\custom\setup\utils;\n".
        "\tuse sql_links\\factories\Request;\n".
        "\tuse sql_links\\factories\RequestConnexion;\n".
        "\tuse \ormframework\custom\mvc\\views\Json;\n\n".
        "\tclass {$modelName} extends Model {\n";
        $modelContent .= "\t\tprivate \$my_utils;\n".
        "\t\tpublic function __construct(\$is_assoc)\n".
        "\t\t{\n".
        "\t\t\tparent::__construct(\$is_assoc);\n".
        "\t\t\t\$this->my_utils = new utils();\n".
        "\t\t}\n\n";
        $entity = "\\ormframework\\custom\\db_context\\".$methods[0]['annotations']['model'];
        require_once $this->get_manager('services')->conf()->get_modules_conf()->modules->dbcontext->location['custom'].'/'.$methods[0]['annotations']['model'].'.php';
        /**
         * @var entity $entity
         */
        $entity = new $entity();
        $props = array_keys($entity->get_props());
        foreach ($props as $id => $prop) {
            if($prop === 'id') {
                unset($props[$id]);
            }
        }
        $params_array = [];
        foreach ($props as $prop) {
            $params_array[] = "'{$prop}' => \$this->get_from_name('{$prop}', \$args)";
        }
        foreach ($methods as $method) {
            $modelContent .= "\t\t/**\n";
            foreach ($method['annotations'] as $annotation => $annotation_value) {
                $modelContent .= "\t\t * @{$annotation} {$annotation_value}\n";
            }
            $modelContent .= "\t\t **/\n";

            $modelContent .= "\t\tpublic function {$method['name']}(\$args = []) {\n";

            foreach ($method['content'] as $i => $content) {
                $method['content'][$i] = str_replace('{params_array}', implode(', ', $params_array), $content);
            }

            $modelContent .= "\t\t\t".(gettype($method['content']) === 'array' ? implode("\t\t\t", $method['content']) : $method['content'])."\n";

            $modelContent .= "\t\t}\n\n";
        }

        $modelContent.= "\t}";
        file_put_contents("custom/mvc/models/{$modelName}.php", $modelContent);
    }

    private function genere_controller() {
        $controllerName = $this->get_from_name('whole');
        $controllerContent = "<?php\n\n".
        "\tnamespace ormframework\custom\mvc\controllers;\n\n".
        "\tuse ormframework\core\mvc\Controller;\n\n".
        "\tclass {$controllerName} extends Controller { }";
        file_put_contents("custom/mvc/controllers/{$controllerName}.php", $controllerContent);

    }

    private function genere_entity($name = '', $properties = []) {
        $entityName = $this->get_from_name('whole');
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
    public function new_whole() {
        $this->genere_controller();
        $this->genere_entity();
        $this->genere_model();
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
			$request = Request::getIRequest(new RequestConnexion((array)$conf, $bdd_type), $bdd_type);

			$tables = $request->show()->tables()->query();
            foreach ($tables as $table) {
                $filds = $request->show()->columns()->from($table)->query();
                $this->new_whole($table);
                $this->genere_entity($table, $filds);
            }

            //var_dump($request->select()->from('user')->where(['id' => 0])->query());
		}
	}
}