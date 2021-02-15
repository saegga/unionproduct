<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class UnionProduct extends CBitrixComponent{

	public function onPrepareComponentParams($arParams)
	{
		return parent::onPrepareComponentParams($arParams);
	}

	public function executeComponent()
	{
		$arParams = &$this->arParams;

		$arUrlTemplates = [
			'index'            => $arParams['SEF_FOLDER'],
			'section'       => '#ELEMENT_CODE#/',
		];
		$arVariables = [];
		$page = \CComponentEngine::ParseComponentPath(
			$arParams['SEF_FOLDER'],
			$arUrlTemplates,
			$arVariables
		);
		if (!strlen($page)) {
			$page = 'index';
		}
		$arResult = [
			'FOLDER'        => $arParams['SEF_FOLDER'],
			'URL_TEMPLATES' => $arUrlTemplates,
			'VARIABLES'     => $arVariables,
		];
		$this->IncludeComponentTemplate($page);
	}

}
