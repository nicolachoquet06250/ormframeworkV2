<?php

class httpVerb implements annotation_interface
{

    public $comments;

    public function __construct(array $comments)
    {
        $this->comments = $comments;
    }

    public function get()
    {
        $httpVerb = ['get' => [], 'post' => [], 'put' => [], 'delete' => [], 'command' => []];
        foreach ($this->comments as $comment) {
            foreach ($comment as $item => $value) {
                if (isset($value['@httpVerb'])) {
                    $httpVerb[$value['@httpVerb']][] = $value['@model'] . '/' . $value['@method'] . '/@args';
                }
            }
        }
        return $httpVerb;
    }
}