<?php

namespace App\Services;

class NavigationServices
{
    public static function navLinks($model, $intPosi, $modKeys, $action='')
    {
        $strLinkPref = $model.', ';
        if (strlen($action) > 0) {
            $action = '/'.$action;
        }
        $intCantRegi = count($modKeys);
        $intIdxxFirs = 0;
        if ($intPosi > 0) {
            $intIdxxPrev = $intPosi - 1;
        } else {
            $intIdxxPrev = 0;
        }
        if ($intPosi < $intCantRegi - 1) {
            $intIdxxNext = $intPosi + 1;
        } else {
            $intIdxxNext = $intCantRegi - 1;
        }
        $intIdxxLast = $intCantRegi - 1;
        //-----------------------------------------------------------------
        // Una vez determinadas las posiciones, se establecen los enlaces
        //-----------------------------------------------------------------
        $arrEnla      = array();
        $arrEnla['F'] = "$strLinkPref".$modKeys[$intIdxxFirs].$action;
        $arrEnla['P'] = "$strLinkPref".$modKeys[$intIdxxPrev].$action;
        $arrEnla['N'] = "$strLinkPref".$modKeys[$intIdxxNext].$action;
        $arrEnla['L'] = "$strLinkPref".$modKeys[$intIdxxLast].$action;
        return $arrEnla;
    }

    public static function getPostion($id, $modKeys)
    {
        return array_search($id, $modKeys) + 1;
    }

}
