<?php
/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/**
 * @file	maps.class.php
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	the highest class of the maps module
 * @todo	상세 작업을 추가해서 마무리(?) 해야한다.
 */
class maps extends ModuleObject
{
	//language setting
	private $xe_langtype = array(
			'ko',
			'en',
			'zh-tw',
			'zh-cn',
			'jp',
			'es',
			'fr',
			'ru',
			'vi',
			'mn',
			'tr',
			'ja'
		);
	private $google_langtype = array(
			'ko',
			'en',
			'zh-Hant',
			'zh-Hans',
			'ja',
			'es',
			'fr',
			'ru',
			'vi',
			'en', // google does not not support
			'tr',
			'ja'
		);
		
	/**
	 * @param array $microsoft_langtype Microsoft 언어/지역 타입 https://msdn.microsoft.com/en-us/library/mt712553.aspx
	 */
	private $ISO_3166_1_alpha_2 = array(
			'KR',
			'US',
			'TW',
			'CN',
			'JP',
			'ES',
			'FR',
			'RU',
			'VN',
			'MN',
			'TR',
			'JP'
		);

	//default location setting (lat, lng)
	private $country_position = array(
			'ko' => array(37.57, 126.98),
			'en' => array(38, -97),
			'zh-tw' => array(25.03, 121.30),
			'zh-cn' => array(39.55, 116.23),
			'jp' => array(36, 138),
			'es' => array(40, -4),
			'fr' => array(46, 2),
			'ru' => array(60, 100),
			'vi' => array(16, 106),
			'mn' => array(47.55, 106.53),
			'tr' => array(39.1667, 35.6667),
			'ja' => array(36, 138)
		);

	/**
	 * Implement if additional tasks are necessary when installing
	 * @return Object
	 */
	public function moduleInstall()
	{
	}

	/**
	 * A method to check if successfully installed
	 * @return bool
	 */
	public function checkUpdate()
	{
	}

	/**
	 * Execute update
	 * @return Object
	 */
	public function moduleUpdate()
	{
	}

	/**
	 * Re-generate the cache file
	 * @return void
	 */
	public function recompileCache()
	{
	}
}

/* End of file maps.class.php */
/* Location: ./modules/maps/maps.class.php */