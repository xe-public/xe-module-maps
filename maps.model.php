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
	public function getMapsConfig()
	{
		$oModuleModel = getModel('module');
		$maps_config = $oModuleModel->getModuleConfig('maps');
		if(!is_object($maps_config))
		{
			$maps_config = new stdClass();
		}

		//default settings
		if(!$maps_config->maps_api_type)
		{
			$maps_config->maps_api_type = 'google';
		}

		return $maps_config;
	}

	public function getApiXmlObject($uri, $headers = null) {
		$xml = '';
		$xml = FileHandler::getRemoteResource($uri, null, 3, 'GET', 'application/xml', $headers);

		$xml = preg_replace("/<\?xml([.^>]*)\?>/i", "", $xml);

		$oXmlParser = new XmlParser();
		$xml_doc = $oXmlParser->parse($xml);

		return $xml_doc;
	}

}

/* End of file maps.model.php */
/* Location: ./modules/maps/maps.model.php */