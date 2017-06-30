<?php
/**
 * Register Custom Post Type for Authors and Author's items
 * @author Avinash
 **/
if (!defined('WPINC')) {
    die;
}


class CP_Author
{

    private $nonce = 'cp_author_nonce';
    
    public function __construct()
    {
        add_action( 'init', array( $this, 'register_posts_tax' ) );
        add_action( 'cp_authors_add_form_fields', array( $this, 'cp_authors_add_field' ) );
        add_action( 'cp_authors_edit_form_fields',  array( $this, 'cp_authors_edit_field' ) );
        add_filter( 'manage_edit-cp_authors_columns',  array( $this, 'cp_authors_columns' ) );
        add_filter( 'manage_cp_authors_custom_column',  array( $this, 'cp_authors_column' ), 10, 3 );
        add_action( 'edit_term',  array( $this, 'save_cp_authors' ) );
        add_action( 'create_term', array( $this, 'save_cp_authors' ) );
    }

    public function register_posts_tax()
    {
        $labels = array(
            'name'               => __( 'Items' ),
            'singular_name'      => __( 'Item'),
            'menu_name'          => __( 'Items'),
            'name_admin_bar'     => __( 'Items' ),
            'add_new'            => __( 'Add' ),
            'add_new_item'       => __( 'Add New' ),
            'new_item'           => __( 'New' ),
            'edit_item'          => __( 'Edit' ),
            'view_item'          => __( 'View' ),
            'all_items'          => __( 'All' ),
            'search_items'       => __( 'Search' ),
            'parent_item'        => __( 'Parent' ),
            'parent_item_colon'  => __( 'Parent:' ),
            'not_found'          => __( 'Nothing found.' ),
            'not_found_in_trash' => __( 'Nothing found in Trash.' )
            );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'cp-items' ),
            'taxonomies' => array('author'), 
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor',  'thumbnail' ),
            );
        register_post_type( 'cp_items', $args );

        $labels = array(
            'name'              => __( 'Authors' ),
            'singular_name'     => __( 'Author' ),
            'search_items'      => __( 'Search Author' ),
            'all_items'         => __( 'All' ),
            'parent_item'       => __( 'Parent' ),
            'parent_item_colon' => __( 'Parent:' ),
            'edit_item'         => __( 'Edit' ),
            'update_item'       => __( 'Update' ),
            'add_new_item'      => __( 'Add New' ),
            'new_item_name'     => __( 'New' ),
            'menu_name'         => __( 'Author' ),
            'not_found'         => __( 'Nothing found.' ),
            );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'cp-author' ),
            );

        register_taxonomy( 'cp_authors', array( 'cp_items' ), $args );
    }

    
    /*******Taxonmy Fields*************/
   
    public function cp_authors_add_field()
    {        

    }

    public function cp_authors_edit_field($taxonomy)
    {

    }
    
    public function akt_authors_columns( $columns )
    {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        unset( $columns['cb'] );
        return array_merge( $new_columns, $columns );
    }
    
    public function akt_authors_column( $columns, $column, $id )
    {
        return $columns;
    }
    
    public function save_cp_authors($term_id)
    {
    }

}

return new CP_Author();
