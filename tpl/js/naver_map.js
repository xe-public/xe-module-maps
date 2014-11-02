/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/*
 * @file	tpl/js/naver_map.js
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	javascript for viewing naver map and load or save map data.
 * @see		지도 모듈의 편집용 자바스크립트 중 기초 스크립트
 */

var map_zoom = 10, map_lat = '', map_lng = '', map = '', marker = '', map_markers = new Array(), map_marker_positions = '', modi_marker_pos = '', saved_location = new Array(), result_array = new Array(), result_from = '', oIcon = '';

function map_point(i) { //검색된 위치 정보를 배열에서 로드
	center = result_array[i].geometry.location;
	map.setCenter(center);
}
function view_list() { //검색된 위치 정보를 배열에서 리스트로 뿌림
	var html = '';
	if(result_array.length == 0) 
	{
		alert(no_result);
		return;
	}
	for(var i=0;i<result_array.length;i++) {
		if(i==0) {
			html += '<ul id="view_list">';
		}
		if(result_array.length==1) { map_point('0'); }
		var format_split = result_array[i].formatted_address.split(" ");
		var list_address = result_array[i].formatted_address.substring(result_array[i].formatted_address.lastIndexOf(format_split[format_split.length-3]));  
		html += "<li class=\"result_lists\"><a href=\"javascript:map_point('"+i+"');\">"+ list_address +"</a></li>";
	}
	html += '</ul>';
	jQuery("#result_list_layer").html(html);
	window.location.href = '#view_list';
}

function showLocation(query) {
	result_from = '';
	if(!query) return;

	var params = new Array();
	params['query'] = query;

	var response_tags = new Array('error','message','results');
	exec_xml('maps', 'getMapsAdminLocation', params, function(a,b) { complete_search(a,b,query); }, response_tags);
}

function complete_search(ret_obj, response_tags, address) {
	var results = ret_obj['results'];
	if(results) results = results.item;
	else results = new Array();

	address_adder(results);
}
function address_adder(results) {
	result_array = new Array();
	if(typeof(results.length) == "undefined") results = new Array(results);

	for(var i=0;i<results.length;i++) {
		if(results[i].formatted_address || results[i].formatted_address != null) {
			result_array[i] = { from: results[i].result_from,
				formatted_address: results[i].formatted_address,
				geometry: {location : new nhn.api.map.LatLng(results[i].geometry.lat, results[i].geometry.lng) } };
		}
	}
	view_list();
}

function getMaps() {
	oIcon = new nhn.api.map.Icon('http://static.naver.com/maps2/icons/pin_spot2.png', new nhn.api.map.Size(28, 37), new nhn.api.map.Size(14, 37));

	var mapOption = {
		zoom: map_zoom,
		point: new nhn.api.map.LatLng(defaultlat, defaultlng),
		enableWheelZoom : true,
		enableDblClickZoom : true,
		mapMode : 0,
		activateTrafficMap : false,
		activateBicycleMap : false
	}
	map = new nhn.api.map.Map("map_canvas", mapOption);


	if(saved_maps_srl > 0)
	{
		var center_split = saved_map_center.split(',');
		center = new nhn.api.map.LatLng(center_split[0], center_split[1]);

		var markers_split = saved_map_markers.split(';');
		map_marker_positions = saved_map_markers.trim();
		marker = addMarker(0);

		map_zoom = parseInt(saved_map_zoom,10) - 5;
		if(!map_zoom) map_zoom = 10;
	}
	else
	{
		center = new nhn.api.map.LatLng(defaultlat, defaultlng);
	}

	map.setCenter(center);
	var center = map.getCenter();

	jQuery("#width").val('600');
	jQuery("#height").val('400');
	map.setLevel(map_zoom);

	var zoomControl = new nhn.api.map.ZoomControl();
	map.addControl(zoomControl);
	zoomControl.setPosition({ top : 10, left : 10 });
	var mapTypeControl = new nhn.api.map.MapTypeBtn();
	map.addControl(mapTypeControl);
	mapTypeControl.setPosition({ top : 10, right : 10 });

	map.attach('click', function(event) {
		var oTarget = event.target;
		if (oTarget instanceof nhn.api.map.Marker) {
			var position = event.target.getPoint();
			if(typeof(position) == "undefined") return;
			removeMarker(position);
		} else {
			var position = event.point;
			map_marker_positions += position.getLat() + ',' + position.getLng() + ';';
			addMarker(0);
		}
		return false;
	});

}

/* 새로운 위치에 마커 추가. latlng = 0 인 경우, map_marker_positions 에 지정된 마커 새로 찍음 */
function addMarker(latlng) {
	if(typeof(latlng) == "undefined") return;
	var new_marker_obj;
	/* 전체 구조는 removeMarker() 와 동일*/
	// 마커 일단 다 제거
	if(typeof(map_markers) != "undefined") {
		// 마커 일단 다 제거
		for(var i = 0; i < map_markers.length; i++)
		{
			map.removeOverlay(map_markers[i]);
		}
	}

	map_markers = new Array();

	if(latlng != 0) {
		var latitude = latlng.getLat();
		var longitude = latlng.getLng();

		// 중복되는 마커는 생성되지 않도록.
		map_marker_positions = map_marker_positions.replace(latitude+','+longitude+';', '');
		map_marker_positions += latitude + ',' + longitude + ';'; /* removeMarker() 와 다른 곳 */
	}

	positions = makeLocationArray(map_marker_positions);

	// 전체 마커 다시 생성
	for(var i = 0; i < positions.length; i++)
	{
		map_markers[i] = new nhn.api.map.Marker(oIcon, {
			point: positions[i]
		});
		map.addOverlay(map_markers[i]);

		new_marker_obj = map_markers[i];
	}

	// 추가된 마커가 배열의 가장 마지막에 있을거란 가정 하에 마지막 마커 리턴
	return new_marker_obj;

}
function removeMarker(latlng) {
	if(typeof(latlng) == "undefined") return;
	// 마커 일단 다 제거
	for(var i = 0; i < map_markers.length; i++)
	{
		map.removeOverlay(map_markers[i]);
	}
	map_markers = new Array();

	var latitude = latlng.getLat();
	var longitude = latlng.getLng();

	// 마커 위치 제거
	map_marker_positions = map_marker_positions.replace(latitude+','+longitude+';', '');
	positions = makeLocationArray(map_marker_positions);

	// 전체 마커 다시 생성
	for(var i = 0; i < positions.length; i++)
	{
		map_markers[i] = new nhn.api.map.Marker(oIcon, {
			point: positions[i]
		});
		map.addOverlay(map_markers[i]);
	}

}

function positionstrRemover(obj_position, str_positions) {
	var remove_point = '';
	var arr_positions = str_positions.split(";");
	for(var i = 0; i < arr_positions.length; i++)
	{
		if(!arr_positions[i].trim()) continue;
		var position = arr_positions[i].split(",");
		var obj_base_position = new nhn.api.map.LatLng(position[0],position[1]);
		if(obj_base_position.equals(obj_position))
		{
			str_positions = str_positions.replace(arr_positions[i] + ';', '');
		}
	}
	return str_positions;
}

function makeLocationArray(str_position) {
	var arr_positons = new Array();
	var positions = str_position.split(";");
	for(var i = 0; i < positions.length; i++)
	{
		if(!positions[i].trim()) continue;
		var position = positions[i].split(",");
		arr_positons[i] = new nhn.api.map.LatLng(position[0],position[1]);
	}
	return arr_positons;
}
function makeLocationStr(arr_position) {
	var str_positons = '';

	for(var i = 0; i < arr_position.length; i++)
	{
		str_positons += arr_position[i].getLat() + ',' + arr_position[i].getLng() + ';';
	}
	return str_positons;
}

function saveMap(obj) {
	var maps_srl = jQuery("#maps_srl").val(), map_title = jQuery("#map_title").val(), map_description = jQuery("#map_description").val();

	map_zoom = map.getLevel() + 5;
	map_lat = map.getCenter().getLat();
	map_lng = map.getCenter().getLng();
	if(!maps_srl) {maps_srl = 'NEW'}

	var map_var = {
			'maps_srl':maps_srl,
			'map_center': map_lat+','+map_lng,
			'map_markers': map_marker_positions,
			'map_zoom': map_zoom,
			'map_title': map_title,
			'map_description':map_description
		};

	var response_tags = new Array('error','message','maps_srl');
	exec_xml('maps', 'procMapsAdminInsert', map_var, function(ret_obj,b) {
			var maps_srl = ret_obj['maps_srl'];
			if(maps_srl > 0)
			{
				jQuery("#maps_srl").val(maps_srl);
				alert(maps_saved);
			}

		}, response_tags);

}

jQuery(document).ready(function() { getMaps(); });
