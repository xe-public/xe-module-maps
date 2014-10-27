<?php
/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/**
 * @class  mapsModel
 * @author MinSoo Kim (misol.kr@gmail.com)
 * @brief model class of the maps module
 * @todo 상세 작업을 추가해서 마무리(?) 해야한다.
 */
class mapsModel extends maps
{
	/**
	 * @brief Return maps module setting
	 */
	function getMapsConfig()
	{
		$oModuleModel = getModel('module');
		$maps_default_config = $oModuleModel->getModuleConfig('maps');

		return $maps_default_config;
	}
}

/* End of file maps.model.php */
/* Location: ./modules/maps/maps.model.php */