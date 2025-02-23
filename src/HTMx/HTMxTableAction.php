<?php

namespace rbwebdesigns\core\HTMx;

/**
 * A dead simple link.
 */
class HTMxTableAction implements HTMXTableActionInterface
{
    /**
     * @var callable
     */
    protected $urlCallback;

    /**
     * Create a new table action.
     * @param string|Callable(array|object) $url Url string, or callback for the link href.
     * @param string $label Text of the link.
     */
    public function __construct(public $url, public string $label) {}

    public function render(array|object $item): string
    {
        $url = $this->url;
        if (is_callable($this->url)) {
            $url = ($this->url)($item);
        }

        return '<a href="'.$url.'">'.$this->label.'</a>';
    }

}
