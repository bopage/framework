<?php
namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PriceExtension extends AbstractExtension
{
    private $currency;

    public function __construct(string $currency = '€')
    {
        $this->currency = $currency;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('vat', [$this, 'getVat']),
            new TwigFunction('vat_only', [$this, 'getVatOnly'])
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price_format', [$this, 'priceFormat'])
        ];
    }
    
    /**
     * priceFormat
     *
     * @param  float $price
     * @param  string $currency
     * @return void
     */
    public function priceFormat(float $price, ?string $currency = null)
    {
        return number_format($price, 2, ',', ' '). ' ' .($currency ?: $this->currency);
    }
    
    /**
     * getVat donne le prix TTC
     *
     * @param  float $price
     * @param  float $vat
     * @return float
     */
    public function getVat(float $price, ?float $vat): float
    {
        return $price + $this->getVatOnly($price, $vat);
    }

    /**
     * getVat donne le prix de Tva
     *
     * @param  float $price
     * @param  float $vat
     * @return float
     */
    public function getVatOnly(float $price, ?float $vat): float
    {
        if ($vat === null) {
            return 0;
        }
        return $price * $vat / 100;
    }
}
