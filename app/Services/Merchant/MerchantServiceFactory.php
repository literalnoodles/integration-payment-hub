<?php

namespace App\Services\Merchant;

class MerchantServiceFactory
{
    const allowedMerchants = [
        'shopify',
        'cafe24'
    ];

    /**
     * return correct merchant service based on platform
     * 
     * @param string $platform
     */
    public static function create($platform)
    {
        switch ($platform)
        {
            case 'shopify':
                return new ShopifyService;
            case 'cafe24':
                return new Cafe24Service;
            default:
                return null;
        }
    }
}