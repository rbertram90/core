<?php

namespace rbwebdesigns\core\HTMx;

interface HTMXTableActionInterface
{
    public function render(array $item): string;
}
