<?php
/**
 * Created by PhpStorm.
 * User: sharewood
 * Date: 10/03/16
 * Time: 11:09
 */

if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/lib/ColorThief/ColorThief.php';

class Colorfinder extends module {
	const INSTALL_SQL_FILE = 'sql/install.sql';
	const UNINSTALL_SQL_FILE = 'sql/uninstall.sql';

	public function __construct()
	{
		$this->name                   = 'colorfinder';
		$this->tab                    = 'front_office_features';
		$this->version                = '1.0.0';
		$this->author                 = 'Nicolas Coutureau';
		$this->need_instance          = 0;
		$this->ps_versions_compliancy = array( 'min' => '1.6', 'max' => _PS_VERSION_ );
		$this->bootstrap              = true;

		parent::__construct();

		$this->displayName = $this->l( 'Color Finder' );
		$this->description = $this->l( 'Trouve la couleur dominante d\'un produit' );

		$this->confirmUninstall = $this->l( 'Êtes vous sûr de vouloir désactiver le module colorfinder?' );
	}


	public function install()
	{
		return
			parent::install() &&
			$this->registerHook( 'displayAdminProductsExtra' ) &&
			$this->registerHook( 'actionProductSave' ) &&
			$this->installDb();

	}

	public function uninstall()
	{
		return $this->uninstallDB()
		       && parent::uninstall();
	}

	private function installDb()
	{
		$logInstall = dirname( __FILE__ ) . '/log/install.log';

		$sql = file_get_contents( dirname( __FILE__ ) . '/' . self::INSTALL_SQL_FILE );
		$sql = str_replace( array( 'PREFIX_', 'ENGINE_TYPE', '_ID_LANG_' ), array(
				_DB_PREFIX_,
				_MYSQL_ENGINE_,
				$this->context->language->id
			), $sql );
		$sql = preg_split( "/;\s*[\r\n]+/", trim( $sql ) );

		foreach ( $sql AS $query ) {
			if ( $query ) {
				if ( ! Db::getInstance()->Execute( trim( $query ) ) ) {
					file_put_contents( $logInstall, "The following query cannot be executed on your DB " . $query . "<br/>\n", FILE_APPEND );

					return false;
				}
			}
		}

		return true;
	}

	private function uninstallDB()
	{
		$logUninstall = dirname( __FILE__ ) . '/log/uninstall.log';

		$sql = file_get_contents( dirname( __FILE__ ) . '/' . self::UNINSTALL_SQL_FILE );
		$sql = str_replace( array( 'PREFIX_', 'ENGINE_TYPE', '_ID_LANG_' ), array(
				_DB_PREFIX_,
				_MYSQL_ENGINE_,
				$this->context->language->id
			), $sql );
		$sql = preg_split( "/;\s*[\r\n]+/", trim( $sql ) );

		foreach ( $sql AS $query ) {
			if ( ! Db::getInstance()->Execute( trim( $query ) ) ) {
				file_put_contents( $logUninstall, "The following query cannot be executed on your DB " . $query . "<br/>\n", FILE_APPEND );

				return false;
			}
		}

		return true;
	}

	public function hookDisplayAdminProductsExtra( $params )
	{
		$id_product = Tools::getValue( 'id_product' );
		$product    = new Product( $id_product );

		$image = Image::getCover( $id_product );

		if ( ! $image ) {
			$image_link = null;
		} else {
			$image_link = ( new Link )->getImageLink( $product->link_rewrite[1], $image['id_image'], 'home_default' );
		}

		$this->context->smarty->assign( array(
			'product'    => $product,
			'image_link' => $image_link,
			'base_uri'     => __PS_BASE_URI__
		) );

		return $this->display( __FILE__, 'views/admin/colorfinder.tpl' );
	}

	/**
	 * Lorsque le produit est mis à jour ou créé
	 *
	 * @param $params
	 */
	public function hookActionProductSave( $params )
	{
		$id_product = $params['id_product'];

		if ( ! Image::getCover( $id_product ) ) {
			return;
		}

		$dominant_colors = $this->getDominantColors( $id_product );

		$product = new Product($id_product);
		if($product->dominant_color != null){
			$fields = [
				'dominant_colors' => serialize( $dominant_colors )
			];
		}else{
			$fields = [
				'dominant_colors' => serialize( $dominant_colors ),
				'dominant_color'  => $dominant_colors[0]
			];
		}

		Db::getInstance()->update( 'product', $fields, 'id_product=' . $id_product );
	}

	private function getCoverImagePath( $id_product )
	{
		$image = Image::getCover( $id_product );
		$image = new Image( $image['id_image'] );

		return _PS_PROD_IMG_DIR_ . $image->getExistingImgPath() . '.jpg';
	}

	private function getDominantColors( $id_product )
	{
		$image_path = $this->getCoverImagePath( $id_product );

		$palette = ColorThief::getPalette( $image_path );

		return array_map( function ( $color ) {
			return $this->rgb2hex( $color );
		}, $palette );
	}

	private function rgb2hex( $rgb )
	{
		$hex = "#";
		$hex .= str_pad( dechex( $rgb[0] ), 2, "0", STR_PAD_LEFT );
		$hex .= str_pad( dechex( $rgb[1] ), 2, "0", STR_PAD_LEFT );
		$hex .= str_pad( dechex( $rgb[2] ), 2, "0", STR_PAD_LEFT );

		return $hex; // returns the hex value including the number sign (#)
	}

}
