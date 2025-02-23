<?php

namespace rbwebdesigns\core\HTMx;

class HTMxTableDialogAction implements HTMXTableActionInterface
{
    public function __construct(public $url, public string $label) {}

    public function render(array|object $item): string
    {
        $url = $this->url;
        if (is_callable($this->url)) {
            $url = ($this->url)($item);
        }

        return '<a hx-get="'.$url.'" hx-target="#action-dialog">'.$this->label.'</a>';
    }

}
