<?php
/**
 * Created by PhpStorm.
 * User: sharewood
 * Date: 10/03/16
 * Time: 13:58
 */

class Product extends ProductCore
{
	public $dominant_color;
	public $dominant_colors;

	public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null)
	{
		Product::$definition['fields']['dominant_color'] = [
			'type' => self::TYPE_STRING,
			'lang' => false,
			'validate' => 'isString'
		];

		Product::$definition['fields']['dominant_colors'] = [
			'type' => self::TYPE_STRING,
			'lang' => false,
			'validate' => 'isString'
		];

		parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
	}

}