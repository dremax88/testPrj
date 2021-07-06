<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\Date;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


class CIblocList extends CBitrixComponent
{
    protected $errors = array();

    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }

    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->getResult();
            $this->includeComponentTemplate();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

    protected function checkModules()
    {
        if (!Loader::includeModule('iblock'))
            throw new SystemException(Loc::getMessage('CPS_MODULE_NOT_INSTALLED', array('#NAME#' => 'iblock')));
    }

    protected function getResult()
    {
        if ($this->errors)
            throw new SystemException(current($this->errors));
        $arParams = $this->arParams;

        $arFilter = [
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        ];

        $rsElement = CIBlockElement::GetList([], $arFilter, false, false);

        while ($obElement = $rsElement->GetNextElement()) {
            $arItem = $obElement->GetFields();
            $arItem['PROPERTY']=$obElement->GetProperties();
            $arResult["ITEMS"][] = $arItem;
        }

        $arFilter = [
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        ];
        $properties = CIBlockProperty::GetList(["sort"=>"asc", "name"=>"asc"], $arFilter);
        while ($prop_fields = $properties->GetNext())
        {
            $arResult['HEAD'][]=$prop_fields;
        }

        $this->arResult = $arResult;
    }
    
}
