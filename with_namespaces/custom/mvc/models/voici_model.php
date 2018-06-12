<?php

namespace ormframework\custom\mvc\models;

use \ormframework\core\annotation\PhpDocParser;
use \ormframework\custom\mvc\views\Html_view;

class voici_model extends \ormframework\core\mvc\Model {

	/**
	 * @description affichage de la documentation au format HTML
	 * @model voici
	 * @method ma_doc
	 * @param void
	 * @return Html_view
	 * @route voici/ma_doc
	 **/
	public function ma_doc() {
        PhpDocParser::instence()->method()->description()->httpVerb()->params()->return()->route();

        return new Html_view(PhpDocParser::instence()->to_html('./custom/website/doc/index.html'));
	}
}