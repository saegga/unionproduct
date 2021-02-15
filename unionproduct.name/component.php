<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Application, 
    Bitrix\Main\Context, 
    Bitrix\Main\Request, 
    Bitrix\Main\Server;

$isMobile = (empty($arParams['flagAjax'])) ? CSite::InDir('/mobile_app/') : (boolean) \SP\Helper::getFromRequest('flagMobile');

	$context = Application::getInstance()->getContext();
	$request = $context->getRequest();
	$value = $request["clear_cache"];

	$page = explode( "/", $APPLICATION->GetCurPage()); // символьный код для фильта

    if($isMobile){
        $code = $page[3];
    }else{
        $code = $page[2];
    }

    $cacheId = "unionproduct.name-" . $code;
    $cacheDir = "/unionproduct.name/" . $cacheId;

    $obCache = new \CPHPCache;
    
    $idsProduct = [];
    
    $breadcrumbName 	= "";
    $titlePage 			= "";
    $titlePageElement   = "";
    $descriptionPage 	= "";
    $keyWords 			= "";
    $topText = "";
    $bottomText = "";
    
    // для постройки цепочки навигации
    $arSections = [];

    if($value == "Y"){
		 $obCache->CleanDir($cacheDir);
	}
    
    if($obCache->InitCache(36000000, $cacheId, $cacheDir)){
        $vars = $obCache->GetVars();
        $idsProduct = $vars['IDS_PRODUCTS'];
        $arSections = $vars['SECTIONS_NAV'];

        $breadcrumbName = $vars['BREADCRUMB_NAME'];
        $titlePage = $vars['ELEMENT_META_TITLE'];
        $titlePageElement = $vars['ELEMENT_PAGE_TITLE'];
        $descriptionPage = $vars['ELEMENT_META_DESCRIPTION'];
        $keyWords = $vars['ELEMENT_META_KEYWORDS'];
        $topText = $vars['TOP_TEXT'];
        $bottomText = $vars['BOTTOM_TEXT'];

    }elseif($obCache->StartDataCache()){
        
        $res = CIBlockElement::GetList(["SORT"=>"ASC"], ["ACTIVE" => "Y", "IBLOCK_ID" => "68", "CODE" => $code], false, false, []);

        $idElement = "";
        $idSection = "";
        if($row = $res->GetNextElement()) {
        	$arFields = $row->GetFields();
        	$idElement = $arFields['ID'];
        	$breadcrumbName = $arFields['NAME'];

            $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues("68", $idElement);

            $titlePage = $ipropValues->getValues()['ELEMENT_META_TITLE'];
            $descriptionPage = $ipropValues->getValues()['ELEMENT_META_DESCRIPTION'];
            $keyWords = $ipropValues->getValues()['ELEMENT_META_KEYWORDS'];
            $titlePageElement = $ipropValues->getValues()['ELEMENT_PAGE_TITLE'];
        }

        $prop = CIBlockElement::GetProperty(68, $idElement, [], ["CODE" => ["UNION_PRODUCTS", "UNION_CRUMB_SECTION"]]);
        while($property = $prop->GetNext())
        {

            if($property['CODE'] == "UNION_PRODUCTS"){
                $idsProduct[] = $property['VALUE'];
            }
            if($property['CODE'] == "UNION_CRUMB_SECTION"){
                $idSection = $property['VALUE'];
                $nav = CIBlockSection::GetNavChain(false, $idSection); // вытаскиваем цепочку к разделу
                while($arSectionPath = $nav->GetNext()){
                	$arSections['ITEMS'][$arSectionPath['ID']] = $arSectionPath;
                }
            }
            if($property['CODE'] == 'UNION_PRODUCTS_TOP_TEXT' && $property['VALUE'] != ''){
                $topText = $property['~VALUE']['TEXT'];
            }
            if($property['CODE'] == 'UNION_PRODUCTS_BOTTOM_TEXT' && $property['VALUE'] != ''){
                $bottomText = $property['~VALUE']['TEXT'];
            }
        }

        global $CACHE_MANAGER;
        $CACHE_MANAGER->StartTagCache($cacheDir);
        $CACHE_MANAGER->RegisterTag('iblock_id_68');
        $CACHE_MANAGER->EndTagCache();
        $obCache->EndDataCache(array("IDS_PRODUCTS" => $idsProduct, "BREADCRUMB_NAME" => $breadcrumbName, "ELEMENT_META_TITLE" => $titlePage, "ELEMENT_META_KEYWORDS" => $keyWords, "ELEMENT_META_DESCRIPTION" => $descriptionPage, "ELEMENT_PAGE_TITLE" => $titlePageElement ,"SECTIONS_NAV" => $arSections, "TOP_TEXT" => $topText, "BOTTOM_TEXT" => $bottomText));
    }
    
    $APPLICATION->AddChainItem("Каталог", "/catalog/");
    $tmp = [];
    foreach($arSections['ITEMS'] as $sec){
    	// Строим ссылки на основе кодов раздела
    	array_push($tmp, $sec['CODE']);
    	if($tmp[0] == $sec['CODE']){ // 1 уровень
    		$APPLICATION->AddChainItem($sec['NAME'], "/catalog/" . $tmp[0] . "/");
    	}else{
    		$url = implode("/", $tmp);
    		$APPLICATION->AddChainItem($sec['NAME'], "/catalog/" . $url . "/");
    	}
    }
    unset($arSections);
    if($titlePageElement != ""){
        $APPLICATION->SetPageProperty("pagetitle", $titlePageElement);
    }else{
        $APPLICATION->SetPageProperty("pagetitle", $titlePage);
    }

    $APPLICATION->SetPageProperty("title", $titlePage);
    $APPLICATION->SetTitle($breadcrumbName);
    $APPLICATION->SetPageProperty("description", $descriptionPage);
    $APPLICATION->SetPageProperty("keywords", $keyWords);

    $APPLICATION->AddChainItem($breadcrumbName);
    $GLOBALS['filter_product'] = $idsProduct;
    $this->InitComponentTemplate();
    $this->__template->SetViewTarget('top_text');?>
       <div class="union_prod_text"><?=$topText;?></div>
   <?$this->__template->EndViewTarget();

    $this->__template->SetViewTarget('bottom_text');?>
       <div class="union_prod_text"><?=$bottomText;?></div>
   <?$this->__template->EndViewTarget();

//}