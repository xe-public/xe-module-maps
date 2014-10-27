<?php
/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/**
 * @file	maps.admin.view.php
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	admin view class of the maps module
 * @todo	상세 작업을 추가해서 마무리(?) 해야한다.
 */
class mapsAdminView extends maps
{
	/**
	 * Initialization
	 * @return void
	 */
	public function init()
	{
	}

	public function dispMapsAdminList()
	{
		// option to get a list
		$args = new stdClass();

		// Specify a template
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('maps_list');
	}
}
/* End of file maps.admin.view.php */
/* Location: ./modules/maps/maps.admin.view.php */