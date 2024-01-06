<?php
namespace App\Src\Admin;

defined('ABSPATH') || exit;

use App\Src\Vsp_Plugin_Boot;
use Rabbit\Utils\Singleton;

class Vsp_Add_Meta_Box extends Singleton
{

    public function __construct()
    {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_box']);
        add_action("save_post_book", [__CLASS__, 'save_book_isbn']);
    }

    public static function add_meta_box( $post_type )
    {
        if ($post_type === 'book') {
            add_meta_box(
                'isbn-number',      // Unique ID
                __('isbn number', 'vsp-text-domain'),
                [__CLASS__, 'showView'],  // Callback function
            );
        }
    }

    public static function showView( $post )
    {
        $db = Vsp_Plugin_Boot::db();
        $row = $db->table('book_info')->where('post_id', $post->ID)->first();

        if (!isset($row->isbn)) {
            $row = '';
        } else {
            $row = $row->isbn;
        }

        echo Vsp_Plugin_Boot::view('admin/book_metabox', ['data' => $row]);
    }

    public static function save_book_isbn( $post_id )
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['book-isbn'])) {
            $isbn = sanitize_text_field($_POST['book-isbn']);

            $db = Vsp_Plugin_Boot::db();       
            $db->table('book_info')->updateOrInsert(
                ['post_id' => $post_id], //conditions
                ['post_id' => $post_id, 'isbn' => $isbn]
            );
        }
    }
}
