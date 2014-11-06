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
	/**
	 * @param array $xe_langtype XE 언어 타입 모음(소문자)
	 */
	protected $xe_langtype = array(
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
			'tr'
		);
	/**
	 * @param array $google_langtype Google 언어 타입 모음
	 */
	protected $google_langtype = array(
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
			'tr'
		);

	/**
	 * @param array $microsoft_langtype Microsoft 언어 타입 모음 http://msdn.microsoft.com/en-us/library/gg427600.aspx
	 */
	protected $microsoft_langtype = array(
			'ko-KR',
			'en-US',
			'zh-TW',
			'zh-HK',
			'ja-JP',
			'es-ES',
			'fr-FR',
			'ru-RU',
			'en-US', // MS does not not support
			'en-US', // MS does not not support
			'en-US' // MS does not not support
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