<?php
/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/**
 * @file	maps.admin.controller.php
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	admin controller class of the maps module
 * @todo	상세 작업을 추가해서 마무리(?) 해야한다.
 */
class mapsAdminController extends maps
{
	/**
	 * @brief Initialization
	 */
	public function init()
	{
	}

	/**
	 * @brief 지도 모듈 설정(API 키), ruleset "procMapsAdminCofig"
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param string $maps_module_map_api_key API 키를 입력 받는다. 키 길이에 따라 네이버, 다음, 빙 인지 구분한다. ruleset(procMapsAdminCofig)에 의해 알파벳+숫자 조합만 입력 가능
	 * @param string $maps_module_daum_local_api_key 다음 지역 API 키를 입력 받는다. ruleset(procMapsAdminCofig)에 의해 알파벳+숫자 조합만 입력 가능
	 */
	public function procMapsAdminCofig()
	{
		$oModuleController = getController('module');
		$config = new stdClass();

		$config->daum_local_api_key = trim(Context::get('maps_module_daum_local_api_key'));
		$config->map_api_key = trim(Context::get('maps_module_map_api_key'));
		$config->maps_api_type = '';

		// API 종류 정하기 다음/네이버/구글
		if(trim($config->map_api_key))
		{
			if(strlen($config->map_api_key) == 40)
			{
				$config->maps_api_type = 'daum'; /* Daum maps */
			}
			elseif(strlen($config->map_api_key) == 32)
			{
				$config->maps_api_type = 'naver'; /* NAVER maps */
			}
			elseif(strlen($config->map_api_key) == 64)
			{
				$config->maps_api_type = 'microsoft'; /* bing maps */
			}
			else
			{
				$config->maps_api_type = 'google'; /* Google maps */
			}
		}
		else
		{
			$this->setMessage('maps_module_fail_to_set');
			return;
		}

		$oModuleController->insertModuleConfig('maps', $config);
		$this->setRedirectUrl(Context::get('error_return_url'));
	}

	/**
	 * @brief 지도 입력 또는 수정
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param string $maps_srl 입력/수정할 지도 번호이다. 숫자가 아닐 경우 새로운 지도로 입력, 숫자가 입력될 경우 기존 지도인지 확인 후 수정한다. 숫자인데 기존 지도가 아닐 경우 새로운 지도로 입력.
	 * @param string $maps_content 입력할 지도 정보를 포함한다. PHP serialize 된 배열이 Base64 인코딩된 문자열이다.
	 */
	public function procMapsAdminInsert()
	{
		$action = ''; //insert or update
		$args = new stdClass();
		$args->maps_srl = intval(trim(Context::get('maps_srl'))); // 정수형이 아닐 경우 제거
		$args->maps_content = trim(Context::get('maps_content'));
		$args->title = trim(Context::get('title'));
		$args->content = trim(Context::get('content'));
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];

		// 정수형이고, 값이 존재할 경우 실제 존재하는 지도인지 확인(업데이트 날짜가 존재하는지 확인)
		if($args->maps_srl > 0)
		{
			$output = executeQuery('maps.getMapUpdate', $args);
		}

		// 존재하는 지도일 경우, 업데이트 진행
		if($output->update)
		{
			$output = executeQuery('maps.updateMapsContent', $args);
		}
		else // 존재하지 않는 지도일 경우 지도 생성. 시퀀스 번호 생성, 지도 입력
		{
			$args->maps_srl = getNextSequence();//다음 시쿼스 번호
			$output = executeQuery('maps.insertMapsContent', $args);
		}

		return;
	}

	/**
	 * @brief 지도 삭제
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param int $maps_srl 삭제할 지도번호. 지도가 있는지 확인 후 삭제. 지도가 없을 경우 아무 변화도 일어나지 않는다.
	 */
	public function procMapsAdminDelete()
	{
		$args = new stdClass();
		$args->maps_srl = intval(trim(Context::get('maps_srl'))); // 정수형이 아닐 경우 제거

		// 정수형이고, 값이 존재할 경우 실제 존재하는 지도인지 확인(업데이트 날짜가 존재하는지 확인)
		if($maps_srl > 0)
		{
			$output = executeQuery('maps.getMapUpdate', $args);
			//todo 존재하는 지도일 경우, 삭제 진행
		}

		// 존재하는 지도일 경우, 삭제 진행
		if($output->update)
		{
			$output = executeQuery('maps.deleteMapContent', $args);
		}

	}

	/**
	 * @brief 테이블 삭제
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @see 모듈에 저장된 자료를 깔끔하게 삭제하고 싶을때. 삭제 전 진심인지 의사를 물어봄.(복구 불가)
	 */
	public function procMapsAdminTableDrop()
	{
		DB::dropTable('maps_contents');
	}
}
/* End of file maps.admin.controller.php */
/* Location: ./modules/maps/maps.admin.controller.php */
