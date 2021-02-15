<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="union_products">
    <div class="union_products_list">
		<?if(!empty($arResult['ITEM'])):?>
		    <?foreach ($arResult['ITEM'] as $item):?>
		      <a class="union_products_item" href="<?=$item['URL']?>" data-id="<?=$item['ID']?>"><?=$item['NAME']?></a>
		    <?endforeach;?>
		<?endif;?>
    </div>
</div>
