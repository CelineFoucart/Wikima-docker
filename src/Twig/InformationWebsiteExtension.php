<?php

namespace App\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class InformationWebsiteExtension
 * 
 * InformationWebsiteExtension handles website informations for a twig view.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class InformationWebsiteExtension extends AbstractExtension
{
    /**
     * @var string website title
     */
    private string $websiteName;

    /**
     * @var string description for meta description tag
     */
    private string $websiteDescription;

    public function __construct(string $websiteName, string $websiteDescription)
    {
        $this->websiteName = $websiteName;
        $this->websiteDescription = $websiteDescription;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('website_name', [$this, 'getWebsiteName']),
            new TwigFunction('website_description', [$this, 'getWebsiteDescription']),
        ];
    }

    /**
     * Get the value of websiteName.
     */
    public function getWebsiteName(): string
    {
        return $this->websiteName;
    }

    /**
     * Get the value of websiteDescription.
     */
    public function getWebsiteDescription(): string
    {
        return $this->websiteDescription;
    }
}
