/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/*
 * @file	tpl/js/google_map.js
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	javascript for viewing google map and load or save map data.
 * @see		지도 모듈의 편집용 자바스크립트 중 기초 스크립트
 */
var map_zoom = 13, map_lat = '', map_lng = '', map = '', marker = '', map_markers = new Array(), map_marker_positions = '', modi_marker_pos = '', saved_location = new Array(), result_array = new Array(), result_from = '';

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
}

function showLocation(query) {
	result_from = '';
	if(!query) return;

	var params = new Array();
	params['query'] = query;

	var response_tags = new Array('error','message','results');
	exec_xml('maps', 'getMapsAdminLocation', params, function(a,b) { complete_search(a,b,query); }, response_tags);
}

function complete_search(ret_obj, response_tags, query) {
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
				geometry: {location : new google.maps.LatLng(results[i].geometry.lat, results[i].geometry.lng) } };
		}
	}
	view_list();
}

function getMaps() {
	var mapOption = {
		zoom: map_zoom,
		center: new google.maps.LatLng(defaultlat, defaultlng),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControl: true,
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
			position: google.maps.ControlPosition.TOP_RIGHT
		},
		panControl: false,
		zoomControl: true,
		zoomControlOptions: {
			style: google.maps.ZoomControlStyle.LARGE,
			position: google.maps.ControlPosition.LEFT_CENTER
		},
		scaleControl: false,
		streetViewControl: false,
		disableDoubleClickZoom: true
	}
	map = new google.maps.Map(document.getElementById("map_canvas"), mapOption);

	if(saved_maps_srl > 0)
	{
		var center_split = saved_map_center.split(',');
		center = new google.maps.LatLng(center_split[0], center_split[1]);

		var markers_split = saved_map_markers.split(';');
		map_marker_positions = saved_map_markers.trim();
		marker = addMarker(0);

		map_zoom = parseInt(saved_map_zoom,10);
		if(!map_zoom) map_zoom = 13;
	}
	else
	{
		center = new google.maps.LatLng(defaultlat, defaultlng);
	}

	map.setCenter(center);
	var center = map.getCenter();

	map.setZoom(map_zoom);

	google.maps.event.addListener(map, 'click', function(MouseEvent) {
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
	map_markers = new Array();

	if(latlng != 0) {
		// 중복되는 마커는 생성되지 않도록.
		map_marker_positions = positionstrRemover(latlng, map_marker_positions);
		map_marker_positions += latlng.lat() + ',' + latlng.lng() + ';'; /* removeMarker() 와 다른 곳 */
	}

	positions = makeLocationArray(map_marker_positions);

	// 전체 마커 다시 생성
	for(var i = 0; i < positions.length; i++)
	{
		map_markers[i] = new google.maps.Marker({
			position: positions[i]
		});
		map_markers[i].setMap(map);
		map_markers[i].setDraggable(true);
		map_markers[i].soo_position = positions[i];
		new_marker_obj = map_markers[i];

		// 이벤트 등록 드래그 시작과 끝은 전후 관계로 연결 되어있음
		google.maps.event.addListener(map_markers[i], "dragstart", function() {
			var position = this.soo_position;
			map_marker_positions = positionstrRemover(position, map_marker_positions);
		});
		google.maps.event.addListener(map_markers[i], "dragend", function() {
			var position = this.getPosition();
			map_marker_positions = positionstrRemover(position, map_marker_positions);
			map_marker_positions += position.lat() + ',' + position.lng() + ';';
			addMarker(0);
		});
		google.maps.event.addListener(map_markers[i], "click", function() {
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
	map_markers = new Array();

	// 마커 위치 제거
	map_marker_positions = positionstrRemover(latlng, map_marker_positions);
	positions = makeLocationArray(map_marker_positions);

	// 전체 마커 다시 생성
	for(var i = 0; i < positions.length; i++)
	{
		map_markers[i] = new google.maps.Marker({
			position: positions[i]
		});
		map_markers[i].setMap(map);
		map_markers[i].setDraggable(true);
		map_markers[i].soo_position = positions[i];
		new_marker_obj = map_markers[i];

		// 이벤트 등록 드래그 시작과 끝은 전후 관계로 연결 되어있음
		google.maps.event.addListener(map_markers[i], "dragstart", function() {
			var position = this.soo_position;
			map_marker_positions = positionstrRemover(position, map_marker_positions);
		});
		google.maps.event.addListener(map_markers[i], "dragend", function() {
			var position = this.getPosition();
			map_marker_positions = positionstrRemover(position, map_marker_positions);
			map_marker_positions += position.lat() + ',' + position.lng() + ';';
			addMarker(0);
		});
		google.maps.event.addListener(map_markers[i], "click", function() {
			var position = this.soo_position;
			removeMarker(position);
		});
	}

}

function positionstrRemover(obj_position, str_positions) {
	var remove_point = '';
	var arr_positions = str_positions.split(";");
	for(var i = 0; i < arr_positions.length; i++)
	{
		if(!arr_positions[i].trim()) continue;
		var position = arr_positions[i].split(",");
		var obj_base_position = new google.maps.LatLng(position[0],position[1]);
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
		arr_positons[i] = new google.maps.LatLng(position[0],position[1]);
	}
	return arr_positons;
}

function makeLocationStr(arr_position) {
	var str_positons = '';

	for(var i = 0; i < arr_position.length; i++)
	{
		str_positons += arr_position[i].lat() + ',' + arr_position[i].lng() + ';';
	}
	return str_positons;
}
function saveMap(obj) {
	var maps_srl = jQuery("#maps_srl").val(), map_title = jQuery("#map_title").val(), map_description = jQuery("#map_description").val();
	

	map_zoom = map.getZoom();
	map_lat = map.getCenter().lat();
	map_lng = map.getCenter().lng();
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