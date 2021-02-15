<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if(!empty($arResult)):?>
	<div class="catalog_ingroup">
	<?foreach ($arResult["UNION_PRODUCT"] as $char => $elems):?>
		<div class="catalog_ingroup_item">
			<div class="catalog_ingroup_info">
				<div class="catalog_ingroup_char"><span><?=$char?></span></div>
				<i class="catalog_ingroup_icon"></i>
			</div>
			<div class="clearfix"></div>
			<div class="catalog_ingroup_list <?if(count($elems) == 1):?>single-item<?endif;?>">
				<ul>
					<?foreach ($elems as $element):?>
						<li><a href="<?=$element["URL"]?>"><?=$element["NAME"]?></a></li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
	<?endforeach;?>
	</div>
<?endif;?>