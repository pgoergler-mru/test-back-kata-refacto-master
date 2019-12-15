<?php
require_once __DIR__ . '/RendererInterface.php';

final class DestinationLinkRenderer implements RendererInterface
{
    private static $VARIABLE_NAME = '[quote:destination_link]';
    /** @var SiteRepository */
    private $siteRepository;
    /** @var DestinationRepository */
    private $destinationRepository;
    /** @var QuoteRepository */
    private $quoteRepository;

    public function __construct($siteRepository, $destinationRepository, $quoteRepository)
    {
        $this->siteRepository = $siteRepository;
        $this->destinationRepository = $destinationRepository;
        $this->quoteRepository = $quoteRepository;
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

        $siteFromRepository = $this->siteRepository->getById($quote->siteId);
        $destination = $this->destinationRepository->getById($quote->destinationId);
        $_quoteFromRepository = $this->quoteRepository->getById($quote->id);
        return str_replace(
            '[quote:destination_link]',
            $siteFromRepository->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id,
            $text
        );
    }
}