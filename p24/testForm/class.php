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
        $properties = CIBlockProperty::GetList(["sort"=>"asc", "name"=>"asc"], $arFilter);
        while ($prop_fields = $properties->GetNext())
        {
            switch ($prop_fields['PROPERTY_TYPE'])
            {
                case 'L':
                    $property_enums = CIBlockPropertyEnum::GetList([], ["IBLOCK_ID"=>$arParams["IBLOCK_ID"], "CODE"=>$prop_fields['CODE']]);
                    while($enum_fields = $property_enums->GetNext())
                    {
                        $list[$enum_fields["ID"]]=$enum_fields["VALUE"];
                    }
                    $prop_fields['list']=$list;
                    $arResult[]=$prop_fields;
                    break;
                default:
                    $arResult[]=$prop_fields;
                    break;
            }
        }
        $this->arResult = $arResult;
    }
    public static function addButtons()
    {
        $buttons='<div class="flex-cont">
                    <div  class="flex-box left_col"></div>
                    <div class="flex-box button-box right_col">
                        <a class="button_yello_brd add" style="margin-left: 0">Добавить</a>
                        <a class="button_yello_brd fs delete" style="margin-left: 0px">Удалить</a>
                    </div>
               </div><br>';
        return $buttons;
    }
}
