<?php
/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/**
 * @file	maps.admin.model.php
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	admin model class of the maps module
 * @todo	상세 작업을 추가해서 마무리(?) 해야한다.
 */
class mapsAdminModel extends maps
{
	/**
	 * Initialization
	 * @return void
	 */
	public function init()
	{
	}

	/**
	 * @brief 주소 검색 반환. 각 사이트의 API 이용.
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param string $query 검색 쿼리.
	 * @todo 자체 DB 기능을 추가할 경우 자체 DB 중 위치도 포함해서 검색.
	 */
	public function getMapsAdminLocation()
	{
		$query = Context::get('query');
		if(!$query) return;

		// API 종류 정하기 다음/네이버/구글
		$oMapsModel = getModel('maps');
		$maps_config = $oMapsModel->getMapsConfig();

		// 언어 값 설정
		$langtype = str_replace($this->xe_langtype, $this->google_langtype, strtolower(Context::getLangType()));

		$uri = sprintf('http://maps.googleapis.com/maps/api/geocode/xml?address=%s&sensor=false&language=%s',urlencode($query),urlencode($langtype));
		$xml_doc = $oMapsModel->getApiXmlObject($uri);

		$item = $xml_doc->geocoderesponse->result;
		if(!is_array($item)) $item = array($item);
		$item_count = count($item);

		if($item_count > 0) {
			for($i=0;$i<$item_count;$i++) {
				$input_obj = '';
				$input_obj = $item[$i];
				if(!$input_obj->formatted_address->body) continue;
				$result[$i]->formatted_address = $input_obj->formatted_address->body;
				$result[$i]->geometry->lng = $input_obj->geometry->location->lng->body;
				$result[$i]->geometry->lat = $input_obj->geometry->location->lat->body;
				$result[$i]->result_from = 'Google';
			}

		}

		if($maps_config->maps_api_type == 'naver') {
			$uri = sprintf('http://map.naver.com/api/geocode.php?key=%s&encoding=utf-8&coord=latlng&query=%s',$maps_config->map_api_key,urlencode($query));
			$xml_doc = $oMapsModel->getApiXmlObject($uri);

			$item = $xml_doc->geocode->item;
			if(!is_array($item)) $item = array($item);
			$item_count = count($item);

			if($item_count > 0) {
				$result_orgin_count = count($result);
				for($i=$result_orgin_count;($i-$result_orgin_count)<$item_count;$i++) {
					$input_obj = '';
					$j = $i-$result_orgin_count;
					$input_obj = $item[$j];
					if(!$input_obj->address->body) continue;
					$result[$i]->formatted_address = $input_obj->address->body;
					$result[$i]->geometry->lng = $input_obj->point->x->body;
					$result[$i]->geometry->lat = $input_obj->point->y->body;
					$result[$i]->result_from = 'Naver';
				}

			}
		}

		if($maps_config->daum_local_api_key) {
			$uri = sprintf('http://apis.daum.net/local/geo/addr2coord?apikey=%s&q=%s&output=xml',$maps_config->daum_local_api_key,urlencode($query));
			$xml_doc = $oMapsModel->getApiXmlObject($uri);

			$item = $xml_doc->channel->item;
			if(!is_array($item)) $item = array($item);
			$item_count = count($item);

			if($item_count > 0) {
				$result_orgin_count = count($result);
				for($i=$result_orgin_count;($i-$result_orgin_count)<$item_count;$i++) {
					$input_obj = '';
					$j = $i-$result_orgin_count;
					$input_obj = $item[$j];
					if(!$input_obj->title->body) continue;
					$result[$i]->formatted_address = $input_obj->title->body;
					$result[$i]->geometry->lng = $input_obj->lng->body;
					$result[$i]->geometry->lat = $input_obj->lat->body;
					$result[$i]->result_from = 'Daum';
				}

			}
		}
		$this->add("results", $result);

	}

	/**
	 * @brief 주소 검색 반환. 각 사이트의 API 이용.
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param string $query 검색 쿼리.
	 * @todo 자체 DB 기능을 추가할 경우 자체 DB 중 위치도 포함해서 검색.
	 */
	public function getMapsAdminList($args)
	{
		$output = executeQuery('maps.getMapsAdminList', $args);
		return $output;
	}

}
/* End of file maps.admin.model.php */
/* Location: ./modules/maps/maps.admin.model.php */