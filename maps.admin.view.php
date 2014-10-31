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
		// 한국 지도가 기본값(언어에 따라 다른 위치 표시될 수도 있음(세계지도 표시가능한 경우만)
		$maps_lat = 37.57;
		$maps_lng = 126.98;

		// API 종류 정하기 다음/네이버/구글
		$oMapsModel = getModel('maps');
		$maps_config = $oMapsModel->getMapsConfig();

		Context::get('maps_srl');

		// 다음과 네이버는 국내 지도만 사용가능. 구글은 세계지도.
		if($maps_config->maps_api_type == 'daum')
		{
			$map_comp_header_script = '<script src="https://apis.daum.net/maps/maps3.js?apikey='.$maps_config->map_api_key.'"></script>';
			$map_comp_header_script .= '<script>'.
				sprintf(
					'var defaultlat="%s";'.
					'var defaultlng="%s";'
					,$maps_lat,$maps_lng).
				'</script>';
			Context::set('maps_langcode', 'ko');
		}
		elseif($maps_config->maps_api_type == 'naver')
		{
			$map_comp_header_script = '<script src="http://openapi.map.naver.com/openapi/naverMap.naver?ver=2.0&amp;key='.$maps_config->map_api_key.'"></script>';
			$map_comp_header_script .= '<script>'.
				sprintf(
					'var defaultlat="%s";'.
					'var defaultlng="%s";'
					,$maps_lat,$maps_lng).
				'</script>';
			Context::set('maps_langcode', 'ko');
		}
		else
		{
			// 언어 값 설정
			$langtype = str_replace($this->xe_langtype, $this->google_langtype, strtolower(Context::getLangType()));

			// 세계지도 표시 가능한 경우는 외국이 표시될 수 있음
			if(Context::getLangType() == 'zh-CN' || Context::getLangType() == 'zh-TW') // Beijing
			{
				$maps_lat = 39.55;
				$maps_lng = 116.23;
			}
			elseif(Context::getLangType() != 'ko') // United States
			{
				$maps_lat = 38;
				$maps_lng = -97;
			}

			$map_comp_header_script = '<script src="https://maps-api-ssl.google.com/maps/api/js?sensor=false&amp;language='.$langtype.'"></script>';
			$map_comp_header_script .= '<script>'.
				sprintf(
					'var defaultlat="%s";'.
					'var defaultlng="%s";'
					,$maps_lat,$maps_lng).
				'</script>';
			Context::set('maps_langcode',$langtype);
		}

		Context::set('maps_api_type', $maps_config->maps_api_type);

		Context::addHtmlHeader($map_comp_header_script);

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