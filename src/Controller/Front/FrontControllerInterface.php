<?php                                      
                                                     
namespace App\Controller\Front;

interface FrontControllerInterface
{
    /**
     * Long cache period.
     */
    public const CACHE_MAX_AGE_LONG = 3600;

    /**
     * Medium cache period.
     */
    public const CACHE_MAX_AGE_MEDIUM = 1800;

    /**
     * Short cache period.
     */
    public const CACHE_MAX_AGE_SHORT = 600;

    /**
     * No cache.
     */
    public const NO_CACHE = 0;
}
