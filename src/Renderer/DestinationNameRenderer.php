<?php
require_once __DIR__ . '/RendererInterface.php';

final class DestinationNameRenderer implements RendererInterface
{
    private static $VARIABLE_NAME = '[quote:destination_name]';
    /** @var DestinationRepository */
    private $destinationRepository;

    public function __construct($destinationRepository)
    {
        $this->destinationRepository = $destinationRepository;
    }


    public function needsToRender($text)
    {
        return false !== strpos($text, self::$VARIABLE_NAME);
    }

    public function render($text, $data)
    {
        /** @var Quote $quote */
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;
        if (!$quote)
        {
            return $text;
        }

        $destinationOfQuote = $this->destinationRepository->getById($quote->destinationId);

       return str_replace(self::$VARIABLE_NAME,$destinationOfQuote->countryName,$text);
    }
}