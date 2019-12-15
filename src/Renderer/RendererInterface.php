<?php


interface RendererInterface
{
    /**
     * Return if a text have to be renderered
     *
     * @param string $text
     * @return bool
     */
    public function needsToRender($text);

    /**
     * @param string $text
     * @param array $data
     * @return string
     */
    public function render($text, $data);
}