<?php
/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/**
 * @class  mapsAdminModel
 * @author MinSoo Kim (misol.kr@gmail.com)
 * @brief admin model class of the maps module
 * @todo 상세 작업을 추가해서 마무리(?) 해야한다.
 */
class mapsAdminModel extends maps
{
	/**
	 * Initialization
	 * @return void
	 */
	function init()
	{
	}

	/**
	 * @brief 주소 검색 반환. 각 사이트의 API 이용.
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param string $query 검색 쿼리.
	 * @todo 자체 DB 기능을 추가할 경우 자체 DB 중 위치도 포함해서 검색.
	 */
	function getMapsAdminLocation()
	{
		$query = Context::get('query');
		if(!$query) return;
	}
}
/* End of file maps.admin.model.php */
/* Location: ./modules/maps/maps.admin.model.php */