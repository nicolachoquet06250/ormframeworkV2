<?php

namespace ormframework\core\commands;



class sql extends command {
	public function __construct(array $args = [])
	{
		$this->argv = $args;
	}

	public function new_alias() {
		$bdd_type = $this->get_from_name('bdd_type');
		$database = $this->get_from_name('database');
		$alias = $this->get_from_name('alias');

		$new_conf = new \stdClass();
		$new_conf->database = $database;
		$new_conf->bdd_type = $bdd_type;

		switch ($bdd_type) {
			case 'json':
				$path_to_database = 'custom/'.$this->get_from_name('path_to_database');
				$new_conf->path_to_database = $path_to_database;
				break;
			default:
				$host = $this->get_from_name('host');
				$user = $this->get_from_name('user');
				$pw = $this->get_from_name('password');
				$port = ($bdd_type === 'mysql') ?
					($this->get_from_name('port') ?
						$this->get_from_name('port') : 3306) :
					($this->get_from_name('port') ?
						$this->get_from_name('port') : 5632 /* a verifier */);

				$new_conf->host = $host;
				$new_conf->user = $user;
				$new_conf->pw = $pw;
				$new_conf->port = $port;
				break;
		}

		$this->get_manager('services')->conf()->add_sql_conf($alias, $new_conf);
	}

	public function rm_alias() {
		$alias = $this->get_from_name('alias');
		$bdd_type = $this->get_from_name('bdd_type');

		$this->get_manager('services')->conf()->remove_sql_conf($alias, $bdd_type);
	}

	public function update_alias() {
		$alias = $this->get_from_name('alias');
		$bdd_type = $this->get_from_name('bdd_type');

		$new_conf = new \stdClass();
		$new_conf->bdd_type = $bdd_type;
		$new_conf->database = $this->get_from_name('database');

		if($bdd_type === 'json') {
			if($path = $this->get_from_name('path_to_database')) {
				$new_conf->path_to_database = 'custom/'.$path;
			}
		}
		else {
			if($host = $this->get_from_name('host')) {
				$new_conf->host = $host;
			}
			if($user = $this->get_from_name('user')) {
				$new_conf->user = $user;
			}
			if($pw = $this->get_from_name('password')) {
				$new_conf->password = $pw;
			}
			if($port = ($bdd_type === 'mysql') ?
				($this->get_from_name('port') ?
					$this->get_from_name('port') : 3306) :
				($this->get_from_name('port') ?
					$this->get_from_name('port') : 5632 /* a verifier */)) {
				$new_conf->port = $port;
			}
		}
		$this->get_manager('services')->conf()->update_sql_conf($alias, $new_conf);
	}
}