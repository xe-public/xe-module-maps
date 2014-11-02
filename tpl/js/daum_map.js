/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/*
 * @file	tpl/js/daum_map.js
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	javascript for viewing daum map and load or save map data.
 * @see		지도 모듈의 편집용 자바스크립트 중 기초 스크립트
 */

var map_zoom = 5, map_lat = '', map_lng = '', map = '', marker = '', map_markers = new Array(), map_marker_positions = '', modi_marker_pos = '', saved_location = new Array(), result_array = new Array(), result_from = '';

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
				geometry: {location : new daum.maps.LatLng(results[i].geometry.lat, results[i].geometry.lng) } };
		}
	}
	view_list();
}

function getMaps() {
	var mapOption = {
		level: map_zoom,
		center: new daum.maps.LatLng(defaultlat, defaultlng)
	}
	map = new daum.maps.Map(document.getElementById("map_canvas"), mapOption);

	if(saved_maps_srl > 0)
	{
		var center_split = saved_map_center.split(',');
		center = new daum.maps.LatLng(center_split[0], center_split[1]);

		var markers_split = saved_map_markers.split(';');
		map_marker_positions = saved_map_markers.trim();
		marker = addMarker(0);

		map_zoom = 20 - parseInt(saved_map_zoom,10);
		if(!map_zoom) map_zoom = 5;
	}
	else
	{
		center = new daum.maps.LatLng(defaultlat, defaultlng);
	}

	map.setCenter(center);
	var center = map.getCenter();

	jQuery("#width").val('600');
	jQuery("#height").val('400');
	map.setLevel(map_zoom);

	var zoomControl = new daum.maps.ZoomControl();
	map.addControl(zoomControl, daum.maps.ControlPosition.LEFT);
	var mapTypeControl = new daum.maps.MapTypeControl();
	map.addControl(mapTypeControl, daum.maps.ControlPosition.TOPRIGHT);

	daum.maps.event.addListener(map, 'click', function(MouseEvent) {
		latlng = MouseEvent.latLng;
		addMarker(latlng);
	});

}

/* 새로운 위치에 마커 추가. latlng = 0 인 경우, map_marker_positions 에 지정된 마커 새로 찍음 */
function addMarker(latlng) {
	var new_marker_obj;
	/* 전체 구조는 removeMarker() 와 동일*/
	// 마커 일단 다 제거
	if(typeof(map_markers) != "undefined") {
		for(var i = 0; i < map_markers.length; i++)
		{
			map_markers[i].setMap(null);
		}
	}

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
		map_markers[i] = new daum.maps.Marker({
			position: positions[i]
		});
		map_markers[i].setMap(map);
		map_markers[i].setDraggable(true);
		map_markers[i].soo_position = positions[i];
		new_marker_obj = map_markers[i];

		// 이벤트 등록 드래그 시작과 끝은 전후 관계로 연결 되어있음
		daum.maps.event.addListener(map_markers[i], "dragstart", function() {
			var position = this.soo_position;
			map_marker_positions = map_marker_positions.replace(position.getLat() + ',' + position.getLng() + ';', '');
		});
		daum.maps.event.addListener(map_markers[i], "dragend", function() {
			var position = this.getPosition();
			// 중복되는 마커는 생성되지 않도록.
			map_marker_positions = map_marker_positions.replace(position.getLat() + ',' + position.getLng() + ';', '');
			map_marker_positions += position.getLat() + ',' + position.getLng() + ';';
			addMarker(0);
		});
		daum.maps.event.addListener(map_markers[i], "click", function() {
			var position = this.soo_position;
			removeMarker(position);
		});
	}

	// 추가된 마커가 배열의 가장 마지막에 있을거란 가정 하에 마지막 마커 리턴
	return new_marker_obj;

}
function removeMarker(latlng) {
/* 전체 구조는 removeMarker() 와 동일*/
	// 마커 일단 다 제거
	for(var i = 0; i < map_markers.length; i++)
	{
		map_markers[i].setMap(null);
	}

	var latitude = latlng.getLat();
	var longitude = latlng.getLng();

	// 마커 위치 제거
	map_marker_positions = map_marker_positions.replace(latitude+','+longitude+';', '');
	positions = makeLocationArray(map_marker_positions);

	// 전체 마커 다시 생성
	for(var i = 0; i < positions.length; i++)
	{
		map_markers[i] = new daum.maps.Marker({
			position: positions[i]
		});
		map_markers[i].setMap(map);
		map_markers[i].setDraggable(true);
		map_markers[i].soo_position = positions[i];
		new_marker_obj = map_markers[i];

		// 이벤트 등록 드래그 시작과 끝은 전후 관계로 연결 되어있음
		daum.maps.event.addListener(map_markers[i], "dragstart", function() {
			var position = this.soo_position;
			map_marker_positions = map_marker_positions.replace(position.getLat() + ',' + position.getLng() + ';', '');
		});
		daum.maps.event.addListener(map_markers[i], "dragend", function() {
			var position = this.getPosition();
			map_marker_positions = map_marker_positions.replace(position.getLat() + ',' + position.getLng() + ';', '');
			map_marker_positions += position.getLat() + ',' + position.getLng() + ';';
			addMarker(0);
		});
		daum.maps.event.addListener(map_markers[i], "click", function() {
			var position = this.soo_position;
			removeMarker(position);
		});
	}

}

function makeLocationArray(str_position) {
	var arr_positons = new Array();
	var positions = str_position.split(";");
	for(var i = 0; i < positions.length; i++)
	{
		if(!positions[i].trim()) continue;
		var position = positions[i].split(",");
		arr_positons[i] = new daum.maps.LatLng(position[0],position[1]);
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

	map_zoom = 20 - map.getLevel();
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
