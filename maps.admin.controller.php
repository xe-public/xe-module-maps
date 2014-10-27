<?php
/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/**
 * @class  mapsAdminController
 * @author MinSoo Kim (misol.kr@gmail.com)
 * @brief controller class of the maps module admin
 * @todo �� �۾��� �߰��ؼ� ������(?) �ؾ��Ѵ�.
 */
class mapsAdminController extends maps
{
	/**
	 * @brief Initialization
	 */
	function init()
	{
	}

	/**
	 * @brief ���� ��� ����(API Ű), ruleset "procMapsAdminCofig"
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param string $maps_module_map_api_key API Ű�� �Է� �޴´�. Ű ���̿� ���� ���̹�, ����, �� ���� �����Ѵ�. ruleset(procMapsAdminCofig)�� ���� ���ĺ�+���� ���ո� �Է� ����
	 * @param string $maps_module_daum_local_api_key ���� ���� API Ű�� �Է� �޴´�. ruleset(procMapsAdminCofig)�� ���� ���ĺ�+���� ���ո� �Է� ����
	 */
	function procMapsAdminCofig()
	{
		$oModuleController = getController('module');
		$config = new stdClass();

		$config->daum_local_api_key = trim(Context::get('maps_module_daum_local_api_key'));
		$config->map_api_key = trim(Context::get('maps_module_map_api_key'));
		$config->maps_api_type = '';

		// API ���� ���ϱ� ����/���̹�/����
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
	 * @brief ���� �Է� �Ǵ� ����
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param string $maps_srl �Է�/������ ���� ��ȣ�̴�. ���ڰ� �ƴ� ��� ���ο� ������ �Է�, ���ڰ� �Էµ� ��� ���� �������� Ȯ�� �� �����Ѵ�. �����ε� ���� ������ �ƴ� ��� ���ο� ������ �Է�.
	 * @param string $maps_content �Է��� ���� ������ �����Ѵ�. PHP serialize �� �迭�� Base64 ���ڵ��� ���ڿ��̴�.
	 */
	function procMapsAdminInsert()
	{
		$maps_srl = trim(Context::get('maps_srl'));
		$maps_content = trim(Context::get('maps_content'));

		
	}

	/**
	 * @brief ���� ����
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param int $maps_srl ������ ������ȣ. ������ �ִ��� Ȯ�� �� ����. ������ ���� ��� �ƹ� ��ȭ�� �Ͼ�� �ʴ´�.
	 */
	function procMapsAdminDelete()
	{
	}

	/**
	 * @brief ���̺� ����
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @see ��⿡ ����� �ڷḦ ����ϰ� �����ϰ� ������. ���� �� �������� �ǻ縦 ���.(���� �Ұ�)
	 */
	function procMapsAdminTableDrop()
	{
	}
}
/* End of file maps.admin.controller.php */
/* Location: ./modules/maps/maps.admin.controller.php */
