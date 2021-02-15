<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>


<?$APPLICATION->IncludeComponent(
	"sp-artgroup:unionproduct.index",
	"",
	array(
		"SEF_FOLDER" => $arParams["SEF_FOLDER"],
		"COMPONENT_TYPE" => $arParams["COMPONENT_TYPE"] ? $arParams["COMPONENT_TYPE"] : "",
	),
	$component
);