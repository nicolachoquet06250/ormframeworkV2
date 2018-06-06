<?php

class voici_model extends Model {

	/**
	 * @description affichage de la documentation au format HTML
	 * @model voici
	 * @method ma_doc
	 * @param void
	 * @return Html_view
	 * @route voici/ma_doc
	 **/
	public function ma_doc() {
		PhpDocParser::instence('custom/mvc/models', true);//->model();
		PhpDocParser::instence()->method();
		PhpDocParser::instence()->description();
		PhpDocParser::instence()->httpVerb();
		PhpDocParser::instence()->params();
		PhpDocParser::instence()->return();
		PhpDocParser::instence()->route();
		PhpDocParser::instence()->to_html('./custom/website/doc/index.html');
		$response = file_get_contents('./custom/website/doc/index.html');
		return new Html_view($response);
	}
}