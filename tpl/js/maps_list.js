/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/*
 * @file	tpl/js/maps_list.js
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	javascript for maps list.
 * @see		지도 모듈의 지도 리스트에서 사용되는 스크립트
 */

function deleteMap(maps_srl)
{
	if(!maps_srl) return;

	var params = new Array();
	params['maps_srl'] = maps_srl;
	params['error_return_url'] = current_url;

	var response_tags = new Array('error','message','results');
	exec_xml('maps', 'procMapsAdminDelete', params, function(a,b) { location.reload(true); }, response_tags);
}