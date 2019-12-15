<?php

class TemplateManager
{
    /** @var RendererInterface[] */
    private $renderers = [];

    /**
     * @param RendererInterface $renderer
     * @return TemplateManager
     */
    public function registerRenderer($renderer)
    {
        $this->renderers[] = $renderer;
        return $this;
    }

    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        foreach ($this->renderers as $renderer)
        {
            /** @var RendererInterface $renderer */
            if ($renderer->needsToRender($text))
            {
                $text = $renderer->render($text, $data);
            }
        }

        return $text;
    }
}
