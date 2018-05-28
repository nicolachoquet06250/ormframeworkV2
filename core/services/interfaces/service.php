<?php

interface service {
	public function __call($name, $arguments);
}