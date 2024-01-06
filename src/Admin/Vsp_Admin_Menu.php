<?php
namespace App\Src\Admin;

defined('ABSPATH') || exit;

use Rabbit\Utils\Singleton;
use App\src\Vsp_Plugin_Boot;

class Vsp_Admin_Menu extends Singleton
{

    public function __construct()
    {
        add_action("admin_menu", [$this, 'admin_menu']);
    }

    public function admin_menu()
    {
        add_menu_page(
            "books info",
            __('books info', 'vsp-text-domain'),
            "manage_options",
            "books-info",
            [$this, 'return_menu_view'],
            "dashicons-book",
            50
        );
    }


    public function return_menu_view()
    {
        $bookListTable = new Vsp_Book_Info_List_Table();
        $bookListTable->prepare_items();

        echo Vsp_Plugin_Boot::view( 'admin/books_info', ['table' => $bookListTable] );
    }
}
