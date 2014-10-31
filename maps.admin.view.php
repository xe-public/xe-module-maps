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
		$args->page = intval(Context::get('page'));
		$args->order_type = 'desc';// asc | desc
		$oMapsAdminModel = getAdminModel('maps');
		$maps_list = $oMapsAdminModel->getMapsAdminList($args);

		//Pre. page
		if($maps_list->page_navigation->first_page <= $maps_list->page_navigation->cur_page - 1)
		{
			$maps_list->page_navigation->prev_page = $maps_list->page_navigation->cur_page - 1;
		}
		else
		{
			$maps_list->page_navigation->prev_page = $maps_list->page_navigation->cur_page;
		}



		Context::set('total_count', $maps_list->total_count);
		Context::set('total_page', $maps_list->total_page);
		Context::set('total_page', $maps_list->total_page);
		Context::set('page', $maps_list->page);
		Context::set('page_navigation', $maps_list->page_navigation);
		Context::set('maps_list', $maps_list->data);

		// Specify a template
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('maps_list');
	}

	public function dispMapsAdminWrite()
	{
		Context::get('maps_srl');

		// Specify a template
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('maps_write');
	}

	public function dispMapsAdminConfig()
	{
		// Specify a template
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('maps_config');
	}
}
/* End of file maps.admin.view.php */
/* Location: ./modules/maps/maps.admin.view.php */