<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStoreCategoryModel extends pjAppModel
{
	protected $table = 'stores_categories';
	
	protected $schema = array(
		array('name' => 'store_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'category_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjStoreCategoryModel($attr);
	}
}
?>