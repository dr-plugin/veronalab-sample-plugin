<?php
namespace App\Src\Admin;

defined('ABSPATH') || exit;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use WP_List_Table;
use App\src\Vsp_Plugin_Boot;

class Vsp_Book_Info_List_Table extends WP_List_Table
{

    public function prepare_items()
    {
        $this->_column_headers = [$this->get_columns(), [], [], 'ID'];
        $this->items = $this->get_book_info();
    }

    public function get_book_info()
    {
        $rows = Vsp_Plugin_Boot::db()->table('book_info')->get()->all();

        if (is_null($rows))
            return '';

        $output = [];
        foreach ($rows as $row) {
            $output[] = [
                'ID' => $row->ID,
                'post_id' => $row->post_id,
                'isbn' => $row->isbn
            ];
        }

        return $output;
    }

    public function column_default($item, $column_name)
    {
        if (isset($item[$column_name])) {
            return $item[$column_name];
        }

        return '-';
    }

    public function get_columns()
    {
        return [
            'ID'        => __('id', 'vsp-text-domain'),
            'post_id'   => __('post id', 'vsp-text-domain'),
            'isbn'      => __('isbn number', 'vsp-text-domain')
        ];
    }
}
