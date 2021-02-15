<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Application;

$isMobile = (empty($arParams['flagAjax'])) ? CSite::InDir('/mobile_app/') : (boolean) \SP\Helper::getFromRequest('flagMobile');

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$value = $request["clear_cache"];

if($arParams["SECTION_ID"]){
	$cacheId = "union_labels-" . $arParams["SECTION_ID"] . "-" . intval($isMobile);
	$cacheDir = "/s1/sp-artgroup/union_labels/" . $cacheId;


	$obCache = new \CPHPCache;

	if($value == "Y"){
		$obCache->CleanDir($cacheDir);
	}
	if($obCache->InitCache(36000000, $cacheId, $cacheDir)){
		$vars = $obCache->GetVars();
		$arResult = $vars["arResult"];
	}else if($obCache->StartDataCache()){

		$res = CIBlockElement::GetList(["SORT"=>"ASC"], ["ACTIVE" => "Y", "IBLOCK_ID" => "68", "PROPERTY_UNION_PLACE_SECTION" => $arParams['SECTION_ID']], false, false, []);
		$sections = [];
		$ids = [];

		while($row = $res->fetch()) {

			$sections['ITEM'][$row['ID']]['ID'] = $row['ID'];
			$sections['ITEM'][$row['ID']]['NAME'] = $row['NAME'];

			if($isMobile){
				$sections['ITEM'][$row['ID']]['URL'] = "/mobile_app" . $arParams['SEF_FOLDER'] . $row['CODE'] . '/';
			}else{
				$sections['ITEM'][$row['ID']]['URL'] = $arParams['SEF_FOLDER'] . $row['CODE'] . '/';
			}
			$ids[] = $row['ID'];
		}

		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($cacheDir);
		$CACHE_MANAGER->RegisterTag('iblock_id_68');
		$CACHE_MANAGER->EndTagCache();
		$obCache->EndDataCache(["arResult" => $sections]);
		$arResult = $sections;
	}
	$this->includeComponentTemplate();
}