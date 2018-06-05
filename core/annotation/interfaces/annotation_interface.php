<?php

interface annotation_interface
{
    public function __construct(array $comments);

    public function get();

    public function to_html(int $id);
}