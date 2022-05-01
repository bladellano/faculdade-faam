<?php

namespace Source;

use Rain\Tpl;

class Page
{
    private $tpl;
    private $options = [];
    private $defaults = [
        "header" => true,
        "footer" => true,
        "data" => [],
    ];
    private $data = [];

    public function __construct($opts = [], $tpl_dir = "/views/", $data = [])
    {
        $this->data = $data;

        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir" => substr(chop($_SERVER["SCRIPT_FILENAME"], "index.php"), 0, -1) . $tpl_dir,
            "cache_dir" => substr(chop($_SERVER["SCRIPT_FILENAME"], "index.php"), 0, -1) . "/views-cache/",
            "debug" => false,
            "auto_escape" => false
        );

        Tpl::configure($config);
        $this->tpl = new Tpl;

        $this->setData($this->options["data"]);

        /*$head = (new Seo())->render(
            SITE,
            DESCRIPTION,
            URL_SITE,
            IMAGE
        );

        $this->tpl->assign("head", $head);*/

        if ($this->options["header"] === true) $this->tpl->draw("header");
    }

    private function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }
    public function setTpl($name, $data = array(), $returnHTML = false)
    {
        $this->setData($data);
        return $this->tpl->draw($name, $returnHTML);
    }

    public function __destruct()
    {
        if ($this->options["header"] === true) {
            if(count($this->data)):
                $this->tpl->assign("data",$this->data);
            endif;
            $this->tpl->draw("footer");
        }
            
    }
}
