<?php
/**
 * Understrap functions and definitions
 *
 * @package understrap
 */

/**
 * Initialize theme default settings
 */
require get_template_directory() . '/inc/theme-settings.php';

/**
 * Theme setup and custom theme supports.
 */
require get_template_directory() . '/inc/setup.php';

/**
 * Register widget area.
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/enqueue.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom pagination for this theme.
 */
require get_template_directory() . '/inc/pagination.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom Comments file.
 */
require get_template_directory() . '/inc/custom-comments.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load custom WordPress nav walker.
 */
require get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';

/**
 * Load WooCommerce functions.
 */
require get_template_directory() . '/inc/woocommerce.php';

/**
 * Load Editor functions.
 */
require get_template_directory() . '/inc/editor.php';



class blog {
    function blog() {
        add_action('init',array($this,'create_post_type'));
    }
    function create_post_type() {
        $labels = array(
            'name' => 'ブログ',
            'singular_name' => 'ブログ',
            'add_new' => '新規追加',
            'all_items' => 'ブログ一覧',
            'add_new_item' => '新規追加',
            'edit_item' => '修正',
            'new_item' => '新しいアイテム',
            'view_item' => '表示を確認',
            'search_items' => '探す',
            'not_found' =>  'ありません',
            'not_found_in_trash' => 'ゴミ箱にはありません',
            'parent_item_colon' => '親:',
            'menu_name' => 'ブログ'
        );
        $args = array(
            'labels' => $labels,
            'description' => "ブログです",
            'public' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 10,
            'menu_icon' => 'dashicons-flag',
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title','editor','author','thumbnail','excerpt','revisions','page-attributes','post-formats'),
            'has_archive' => true,
            'rewrite' => true,
            'query_var' => true,
            'can_export' => true
        );
        register_post_type('blog',$args);
    }
}
$blog = new blog();



// カスタムフィールド
/*-------------------------------------------------------------------------------------------*/
/* blog custom field */
/*-------------------------------------------------------------------------------------------*/
add_action('add_meta_boxes_blog', 'blog_add_custom_field');
// 投稿画面に挿入する関数
function blog_add_custom_field(){
    if(function_exists('blog_add_custom_field')){
        add_meta_box('blog_info', 'ブログ識別情報（管理用）', 'insert_blog_info', 'blog', 'normal', 'high');
    }
}

function insert_blog_info(){
    global $post;
    wp_nonce_field(wp_create_nonce(__FILE__), 'my_nonce');
    echo '<p>商品の管理IDを記入してください：<label class="hidden" for="blog_id">ブログID</label><input type="text" name="blog_id" size="10" value="'.esc_html(get_post_meta($post->ID, 'blog_id', true)).'" /></p>';
}

function my_box_save($post_id) {
    global $post;  //編集中の記事に関するデータを保存
    $my_nonce = isset($_POST['my_nonce']) ? $_POST['my_nonce'] : null; //設定したnonce を取得（CSRF対策）
    if(!wp_verify_nonce($my_nonce, wp_create_nonce(__FILE__))) {  //nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）
        return $post_id;
    }
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; } //自動保存ルーチンかどうかチェック。そうだった場合は何もしない（記事の自動保存処理として呼び出された場合の対策）
    if(!current_user_can('edit_post', $post->ID)) { return $post_id; } //ユーザーが編集権限を持っていない場合は何もしない。
    if($_POST['post_type'] == 'blog'){  //'blog' 投稿タイプの場合のみ実行
    update_post_meta($post->ID, 'blog_id', $_POST['blog_id']);
}
}
add_action('save_post', 'my_box_save');