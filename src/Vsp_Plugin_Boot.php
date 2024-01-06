<?php
namespace App\Src;

defined('ABSPATH') || exit;

use Rabbit\Utils\Singleton;
use App\Src\Admin\Vsp_Add_Meta_Box;
use App\Src\Admin\Vsp_Admin_Menu;

class Vsp_Plugin_Boot extends Singleton{

    protected static $continer;

    public function __construct( $continer )
    {
        self::$continer = $continer ;
        
        if (is_admin()) {
            Vsp_Admin_Menu::get();
            Vsp_Add_Meta_Box::get();
        }
        
        add_action('init', function () {
            Vsp_Register_Book::get();
        });
    }

    public static function db()
    {
        if (self::$continer->has('database')) {
            return self::$continer->get('database');
        }

        return false;
    }

    public static function view( $file_name, $val = [] )
    {
        if (self::$continer->has('template')) {
            return self::$continer->template( $file_name, $val );
        }
        return 'template service provider not loaded';
    }
}
