<?php
require_once __DIR__ . '/RendererInterface.php';

final class SummaryTextRenderer implements RendererInterface
{
    private static $VARIABLE_NAME = '[quote:summary]';
    private $quoteRepository;

    public function __construct($quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    public function needsToRender($text)
    {
        return false !== strpos($text, self::$VARIABLE_NAME);
    }

    public function render($text, $data)
    {
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;
        if (!$quote)
        {
            return $text;
        }

        $_quoteFromRepository = $this->quoteRepository->getById($quote->id);
        return str_replace(
            self::$VARIABLE_NAME,
            Quote::renderText($_quoteFromRepository),
            $text
        );
    }
}