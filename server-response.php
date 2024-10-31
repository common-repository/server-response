<?php
/*
Plugin Name: Server Response
Plugin URI: http://seo-river.ru/server-response/
Description: Корректировка заголовков ответа сервера и отключение REST API.
Version: 1.1
Author: seoriver
Author URI: http://seo-river.ru/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// создаем меню управления настройками плагина
add_action('admin_menu', 'seoriver_create_menu');
add_action( 'init', 'seoriver_init' );		

function seoriver_create_menu() {

	//создаем меню верх. уровня
	add_options_page('seoriver Settings', 'Server Response', 'administrator', __FILE__, 'seoriver_settings_page');

	//настройки
	add_action( 'admin_init', 'register_seoriver_settings' );
}


	function seoriver_init() {

		// отключаем REST API
		if(get_option( 'remove_wp_json' ) == true ){
		add_filter('rest_enabled', '_return_false');
		}

		// убираем ссылку на REST API из ответа сервера
		if(get_option( 'remove_rest_output_link_header' ) == true ){
		remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
		}
		
		// убираем rel=shortlink из ответа сервера
		if(get_option( 'remove_wp_shortlink_header' ) == true ){
		remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 );
		}
		
		// меняем (создаем) заголовки Expires и Last-Modified в ответе сервера с актуальными датами
		if(get_option( 'create_expires' ) == true ){
		if ($_SERVER['REQUEST_URI']=="/") {
			 header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60*60*24*5)) . ' GMT');
			 header("Last-Modified: " . gmdate("D, d M Y H:i:s", time() - (60*60*24*5)) . " GMT");
		}else{
			 header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60*60*24*15)) . ' GMT');
			 header("Last-Modified: " . gmdate("D, d M Y H:i:s", time() - (60*60*24*15)) . " GMT");
		}
		}
}		
function register_seoriver_settings() {
	//регистрируем наши настройки
	register_setting( 'seoriver-settings-group', 'remove_wp_json' );
	register_setting( 'seoriver-settings-group', 'remove_rest_output_link_header' );
	register_setting( 'seoriver-settings-group', 'remove_wp_shortlink_header' );
	register_setting( 'seoriver-settings-group', 'create_expires' );
}


function seoriver_settings_page() {
?>
<div class="wrap">
<h2>Server Response</h2>
<h4>Выберите функции, которые вы хотите отключить:</h4>

<form method="post" action="options.php">
    <?php settings_fields( 'seoriver-settings-group' ); ?>
    
    <table class="form-table">		
		<tr valign="top">
        <th scope="row">Отключить REST API</th>
        <td><input type="checkbox" name="remove_wp_json" value="1" <?php if (get_option('remove_wp_json')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Убрать ссылку на REST API из ответа сервера</th>
        <td><input type="checkbox" name="remove_rest_output_link_header" value="1" <?php if (get_option('remove_rest_output_link_header')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Актуализировать даты заголовков Expires и Last-Modified в ответе сервера</th>
        <td><input type="checkbox" name="create_expires" value="1" <?php if (get_option('create_expires')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Убрать rel=shortlink из ответа сервера</th>
        <td><input type="checkbox" name="remove_wp_shortlink_header" value="1" <?php if (get_option('remove_wp_shortlink_header')==true) echo 'checked="checked" '; ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
<h4>Проверка (все ссылки открываются в новом окне):</h4>
<p><a target="_blank" href="/wp-json/">REST API</a> для сайта с ЧПУ или <a target="_blank" href="/?rest_route=/">REST API</a> для любого сайта, в том числе без ЧПУ</p>
<p><a target="_blank" href="https://webmaster.yandex.ru/tools/server-response/">Ответ сервера (Яндекс Вебмастер)</a></p>
<p><a target="_blank" href="http://mainspy.ru/otvet_servera">Ответ сервера (mainspy)</a></p>
<h4>Все изменения, инициируемые данным плагином, абсолютно безопасны и могут быть отменены в любой момент.</h4>
</div>

<?php } ?>