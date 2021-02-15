<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Data\Cache;

class UnionProductIndex extends CBitrixComponent{

	const IBLOCK_UNION = 68;
	const IBLOCK_CATALOG = 1;
	const CACHE_TIME = 3600000;
	public $isMobile = false;

	public function onPrepareComponentParams($arParams)
	{
		return parent::onPrepareComponentParams($arParams);
	}

	public function executeComponent()
	{
		$arParams = &$this->arParams;
		$arResult = &$this->arResult;
		$this->isMobile = (empty($arParams['flagAjax'])) ? CSite::InDir('/mobile_app/') : (boolean) \SP\Helper::getFromRequest('flagMobile');

		switch ($arParams["COMPONENT_TYPE"]){
			case "union_name" :
				$arResult = $this->getArResultName();
				break;
			case "union_manufact" :
				$arResult = $this->getArResultManufact();
				break;
		}

		$this->includeComponentTemplate();
	}
	private function getArResultName(){
		global $CACHE_MANAGER;
		$cache = Cache::createInstance();

		$chacheId = "unionprod-" . self::IBLOCK_UNION ."-". intval($this->isMobile);
		$cacheDir = "/s1/sp-artgroup/union_name/";

		if($cache->initCache(self::CACHE_TIME, $chacheId, $cacheDir)){
			$arResult = $cache->getVars()['RESULT'];
		}else if($cache->startDataCache()){

			$CACHE_MANAGER->StartTagCache($cacheDir);
			$CACHE_MANAGER->RegisterTag("iblock_id_68");

			$rsProducts = \Bitrix\Iblock\ElementTable::getList(
				[
					"order"  =>  ["NAME" => "ASC"],
					"filter" => ["ACTIVE" => "Y", "IBLOCK_ID" => self::IBLOCK_UNION],
					"select" => ["ID", "NAME", "CODE"],
				]
			);
			while ($arProduct = $rsProducts->fetch()){

				$char = mb_strtoupper(mb_substr($arProduct["NAME"], 0, 1, "UTF-8"));
				$arResult["UNION_PRODUCT"][$char][$arProduct["ID"]]["NAME"] = $arProduct["NAME"];
					$arResult["UNION_PRODUCT"][$char][$arProduct["ID"]]["URL"] = $this->arParams["SEF_FOLDER"] . $arProduct['CODE'] . '/';
//					$arResult["UNION_PRODUCT"][$char][$arProduct["ID"]]["URL"] =  $this->arParams["SEF_FOLDER"] . $arProduct['CODE'] . '/';
				}

			$CACHE_MANAGER->EndTagCache();
			$cache->endDataCache(["RESULT" => $arResult]);
		}
		return $arResult;
	}
	private function getArResultManufact(){
		global $CACHE_MANAGER;
		$cache = Cache::createInstance();

		$chacheId = "unionprod-" . self::IBLOCK_CATALOG ."-". intval($this->isMobile);
		$cacheDir = "/s1/sp-artgroup/union_manufact/";

		if($cache->initCache(self::CACHE_TIME, $chacheId, $cacheDir)){
			$arResult = $cache->getVars()['RESULT'];
		}else if($cache->startDataCache()){

			$CACHE_MANAGER->StartTagCache($cacheDir);
			$CACHE_MANAGER->RegisterTag("iblock_id_1");

			$rsProducts  = CIBlockElement::GetList(["NAME" => "ASC"], ["ACTIVE" => "Y", "IBLOCK_ID" => self::IBLOCK_CATALOG], false, false, ["ID", "NAME", "CODE", "PROPERTY_MANUFACTURER_LIST"]);
			while ($arProduct = $rsProducts->GetNext()){

				if($arProduct["PROPERTY_MANUFACTURER_LIST_VALUE"] != null){
					$char = mb_strtoupper(mb_substr($arProduct["PROPERTY_MANUFACTURER_LIST_VALUE"], 0, 1, "UTF-8"));
					$arResult["UNION_PRODUCT"][$char][$arProduct["PROPERTY_MANUFACTURER_LIST_ENUM_ID"]]["NAME"] = $arProduct["PROPERTY_MANUFACTURER_LIST_VALUE"];
						$arResult["UNION_PRODUCT"][$char][$arProduct["PROPERTY_MANUFACTURER_LIST_ENUM_ID"]]["URL"] = $this->arParams["SEF_FOLDER"] . CUtil::translit($arProduct["PROPERTY_MANUFACTURER_LIST_VALUE"], "ru") . "-" . $arProduct["PROPERTY_MANUFACTURER_LIST_ENUM_ID"] .  '/';
					}
				}

			$CACHE_MANAGER->EndTagCache();
			$cache->endDataCache(["RESULT" => $arResult]);
		}
		ksort($arResult["UNION_PRODUCT"]);
		return $arResult;
	}
}