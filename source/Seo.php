<?php

namespace Source;

use CoffeeCode\Optimizer\Optimizer;

class Seo
{
    protected $optmizer;

    public function __construct(string $schema="website")
    {
        $this->optmizer = new Optimizer();
        
        $this->optmizer->openGraph(
            SITE,
            "pt_BR",
            $schema
        )->publisher(
            FB_PAGE,
            FB_AUTHOR
        )->facebook(
            APP_ID
        );
    }

    public function render( string $title, string $description, string $url, string $image, bool $follow = true):string
    {
        $seo = $this->optmizer->optimize(
            $title,
            $description,
            $url,
            $image,
            $follow
        );
        return $seo->render();
    }
}