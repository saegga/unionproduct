<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Application;

class UnionProductManufact extends CBitrixComponent{

	const IBLOCK_CATALOG = 1;
	private $isMobile = false;
	public function onPrepareComponentParams($arParams)
	{
		return parent::onPrepareComponentParams($arParams);
	}

	public function executeComponent()
	{
		global $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
//		$value = $request["clear_cache"];

		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$this->isMobile = (empty($arParams['flagAjax'])) ? CSite::InDir('/mobile_app/') : (boolean) \SP\Helper::getFromRequest('flagMobile');

		$page = explode( "/", $request->getRequestUri());

		if($this->isMobile){
			$idProp = explode("-", $page[3])[1]; // veles_ooo-64 -> 64
		}else{
			$idProp = explode("-", $page[2])[1];
		}
		$propEnum = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID" => self::IBLOCK_CATALOG, "ID"=> $idProp));

		if($field = $propEnum->GetNext())
		{
			$manufactName = $field["VALUE"];
		}

		$rsProd = CIBlockElement::GetList(["SORT" => "ASC"], ["ACTIVE" => "Y", "IBLOCK_ID" => self::IBLOCK_CATALOG, "=PROPERTY_MANUFACTURER_LIST" => $idProp], false, false, ["ID"]);
		while ($arProd = $rsProd->GetNext()){
			$arResult[] = $arProd["ID"];
		}

		$APPLICATION->SetPageProperty("pagetitle", $manufactName);
		$APPLICATION->SetTitle($manufactName);
		$GLOBALS['filter_product'] = $arResult;
		// $GLOBALS["APPLICATION"]->addChainItem("Товары по производителю", $arParams["SEF_FOLDER"]);
		$GLOBALS["APPLICATION"]->addChainItem($manufactName);

	}

}