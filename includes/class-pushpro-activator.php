<?php

/**
 * Class Pushpro_Activator
 *
 * Fired during plugin activation
 *
 */
class Pushpro_Activator
{
    public static function activate()
    {
        wp_cache_flush();
    }
}