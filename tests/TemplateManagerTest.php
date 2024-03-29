<?php

require_once __DIR__ . '/../src/Entity/Destination.php';
require_once __DIR__ . '/../src/Entity/Quote.php';
require_once __DIR__ . '/../src/Entity/Site.php';
require_once __DIR__ . '/../src/Entity/Template.php';
require_once __DIR__ . '/../src/Entity/User.php';
require_once __DIR__ . '/../src/Helper/SingletonTrait.php';
require_once __DIR__ . '/../src/Context/ApplicationContext.php';
require_once __DIR__ . '/../src/Repository/Repository.php';
require_once __DIR__ . '/../src/Repository/DestinationRepository.php';
require_once __DIR__ . '/../src/Repository/QuoteRepository.php';
require_once __DIR__ . '/../src/Repository/SiteRepository.php';
require_once __DIR__ . '/../src/Renderer/RendererInterface.php';
require_once __DIR__ . '/../src/Renderer/SummaryHtmlRenderer.php';
require_once __DIR__ . '/../src/Renderer/SummaryTextRenderer.php';
require_once __DIR__ . '/../src/Renderer/DestinationNameRenderer.php';
require_once __DIR__ . '/../src/Renderer/DestinationLinkRenderer.php';
require_once __DIR__ . '/../src/Renderer/UserFirstnameRenderer.php';
require_once __DIR__ . '/../src/TemplateManager.php';

class TemplateManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Init the mocks
     */
    public function setUp()
    {
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function testWithDestinationLink()
    {
        $faker = \Faker\Factory::create();

        $expectedDestination = DestinationRepository::getInstance()->getById($faker->randomNumber());
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();

        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->date());
        $expectedQuoteSummary = $quote->id;

        $siteFromRepository = SiteRepository::getInstance()->getById($quote->siteId);
        $expectedDestinationLink = $siteFromRepository->url . '/' . $expectedDestination->countryName . '/quote/' . $quote->id;

        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "
Bonjour [user:first_name],

Summary HTML='[quote:summary_html]'
Summary Text='[quote:summary]'
Destination link='[quote:destination_link]'

Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name].

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
");
        $templateManager = new TemplateManager();

        $templateManager
            ->registerRenderer(
            new SummaryHtmlRenderer(QuoteRepository::getInstance())
            )
            ->registerRenderer(
                new SummaryTextRenderer(QuoteRepository::getInstance())
            )
            ->registerRenderer(new DestinationNameRenderer(DestinationRepository::getInstance()))
            ->registerRenderer(new DestinationLinkRenderer(
                SiteRepository::getInstance(),
                DestinationRepository::getInstance(),
                QuoteRepository::getInstance()
            ))
            ->registerRenderer(new UserFirstnameRenderer(ApplicationContext::getInstance()))
        ;

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $expectedDestination->countryName, $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

Summary HTML='<p>" . $expectedQuoteSummary . "</p>'
Summary Text='" . $expectedQuoteSummary . "'
Destination link='" . $expectedDestinationLink . "'

Merci d'avoir contacté un agent local pour votre voyage " . $expectedDestination->countryName . ".

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
", $message->content);
    }

    public function testWithoutDestinationLink()
    {
        $faker = \Faker\Factory::create();

        $expectedDestination = DestinationRepository::getInstance()->getById($faker->randomNumber());
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();

        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->date());
        $expectedQuoteSummary = $quote->id;

        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "
Bonjour [user:first_name],

Summary HTML='[quote:summary_html]'
Summary Text='[quote:summary]'

Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name].

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
");
        $templateManager = new TemplateManager();

        $templateManager
            ->registerRenderer(
                new SummaryHtmlRenderer(QuoteRepository::getInstance())
            )
            ->registerRenderer(
                new SummaryTextRenderer(QuoteRepository::getInstance())
            )
            ->registerRenderer(new DestinationNameRenderer(DestinationRepository::getInstance()))
            ->registerRenderer(new DestinationLinkRenderer(
                SiteRepository::getInstance(),
                DestinationRepository::getInstance(),
                QuoteRepository::getInstance()
            ))
            ->registerRenderer(new UserFirstnameRenderer(ApplicationContext::getInstance()))
        ;

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $expectedDestination->countryName, $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

Summary HTML='<p>" . $expectedQuoteSummary . "</p>'
Summary Text='" . $expectedQuoteSummary . "'

Merci d'avoir contacté un agent local pour votre voyage " . $expectedDestination->countryName . ".

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
", $message->content);
    }
}
