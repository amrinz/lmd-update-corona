<?php
/*
Plugin Name: LombokMedia Update Corona Widget
Plugin URI: https://lombokmedia.web.id/
GitHub Plugin URI: https://github.com/amrinz/lmd-carousel
Description: Simple carousel
Version: 1
Author: Amrin Zulkarnain
Author URI: http://amrinz.wordpress.com/
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: lmd-carousel
Domain Path: /languages
*/

function kc_get_option() {
	
	$kc_url = 'http://api.kawalcorona.com/indonesia';

	$lastdate = date('d-m-Y', strtotime(get_option('kc_update_time')) );
	$curdate = date('d-m-Y');
	
	if (FALSE === get_option('kc_update_time') ) {

		$json = @file_get_contents($kc_url);

		if ( !$json === FALSE ) {

			$data = json_decode($json);

			$lokasi = $data[0]->name;
			$positif = $data[0]->positif;
			$sembuh = $data[0]->sembuh;
			$meninggal = $data[0]->meninggal;
			$dirawat = $data[0]->dirawat;

			add_option('kc_update_time', date('d-m-Y H:i:s') );
			add_option('kc_lokasi', $lokasi );
			add_option('kc_positif', $positif );
			add_option('kc_sembuh', $sembuh );
			add_option('kc_meninggal', $meninggal );
			add_option('kc_dirawat', $dirawat );

		}
		
	}
	
	//jika ada, tapi tanggalnya kemarin
	if ( $lastdate < $curdate ) {

			$json = @file_get_contents($kc_url);

			if ( !$json === FALSE ) {

				$data = json_decode($json);

				$lokasi = $data[0]->name;
				$positif = $data[0]->positif;
				$sembuh = $data[0]->sembuh;
				$meninggal = $data[0]->meninggal;
				$dirawat = $data[0]->dirawat;

				update_option('kc_update_time', date('d-m-Y H:i:s') );
				update_option('kc_lokasi', $lokasi );
				update_option('kc_positif', $positif );
				update_option('kc_sembuh', $sembuh );
				update_option('kc_meninggal', $meninggal );
				update_option('kc_dirawat', $dirawat );

			}

		}

}

function lmd_regwidget_update_corona() {
	register_widget( 'lmd_update_corona' );
}
add_action( 'widgets_init', 'lmd_regwidget_update_corona' );

class lmd_update_corona extends WP_Widget {
	function __construct() {
		parent::__construct(
		
		// widget ID
		'lmd_update_corona',
		
		// widget name
		__('LMD Update Corona', 'lombokmedia'),
		
		// widget description
		array( 'description' => __( 'Menampilkan widget kawal corona', 'lombokmedia' ), )
		);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		echo $args['before_widget'];
		
		//if title is present
		if ( ! empty( $title ) )
		
		echo $args['before_title'] . $title . $args['after_title'];

		kc_get_option();

		//update_option('kc_update_time', '20-1-2020 00:00:01');

		?>

		<div class="kc-wrapper">
			<div class="kc-title kc-lokasi">
				<h4>Update Covid-19 <?php echo get_option('kc_lokasi'); ?></h4>
				<p>Diperbaharui <i><?php echo get_option('kc_update_time'); ?></i></p>
			</div>

			<div class="kc-row">
				<div class="kc-col kc-positif">
					<h4><?php echo get_option('kc_positif'); ?></h4>
					<div class="kc-data">Positif</div>
				</div>
				<div class="kc-col kc-dirawat">
					<h4><?php echo get_option('kc_dirawat'); ?></h4>
					<div class="kc-data">Dirawat</div>
				</div>
			</div>

			<div class="kc-row">
				<div class="kc-col kc-sembuh">
					<h4><?php echo get_option('kc_sembuh'); ?></h4>
					<div class="kc-data">Sembuh</div>
				</div>
				<div class="kc-col kc-meninggal">
					<h4><?php echo get_option('kc_meninggal'); ?></h4>
					<div class="kc-data">Meninggal</div>
				</div>
			</div>

			<p class="kc-credits">Pasang widget dari <a href="https://lombokmedia.web.id" target="_blank">LombokMedia</a></p>
			<?php //DILARANG KERAS MENGHAPUS KREDIT INI ?>
		</div>
		
		<?php

		echo $args['after_widget'];
	}
	
	public function form( $instance ) {
		
		if ( isset( $instance[ 'title' ] ) )
		
			$title = $instance[ 'title' ];
		
		else
		
			$title = __( 'Update Corona', 'lombokmedia' );

		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

	<?php
	}
		public function update( $new_instance, $old_instance ) {
		
		$instance = array();
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		
		return $instance;
	}
}

function lmd_kc_scripts() {
	$plugin_url = plugin_dir_url( __FILE__ );

	wp_enqueue_style( 'style',  $plugin_url . '/kc_style.css');
}

add_action('wp_enqueue_scripts', 'lmd_kc_scripts' );
//IN-ADMIN add_action( 'admin_print_styles', 'lmd_kc_scripts' );