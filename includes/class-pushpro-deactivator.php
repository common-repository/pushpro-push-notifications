<?php

/**
 * Class Pushpro_Deactivator
 * Fired during plugin deactivation
 */

class Pushpro_Deactivator
{
    public static function deactivate()
    {
        wp_cache_flush();
    }
}