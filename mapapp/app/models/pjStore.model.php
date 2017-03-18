<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStoreModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'stores';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'website', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'country_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'address_state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'address_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'address_content', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'address_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'opening_times', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'image_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'image_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'lat', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'lng', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'F')
	);
	
	public static function factory($attr=array())
	{
		return new pjStoreModel($attr);
	}
}
?>