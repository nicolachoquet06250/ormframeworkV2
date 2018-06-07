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
        PhpDocParser::instence()->method()->description()->httpVerb()->params()->return()->route();

        return new Html_view(PhpDocParser::instence()->to_html('./custom/website/doc/index.html'));
	}
}