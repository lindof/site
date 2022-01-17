<?php

namespace Orange35\Colorpickercustom\Model;

trait HtmlElementTrait
{
    public function el(string $tag, $attributes = [], $content = '') : string
    {
        return \Spatie\HtmlElement\HtmlElement::render($tag, $attributes, $content);
    }
}
