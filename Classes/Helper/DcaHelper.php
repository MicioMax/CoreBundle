<?php
/**
 * con4gis
 * @version   php 7
 * @package   con4gis
 * @author    con4gis authors (see "authors.txt")
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\CoreBundle\Classes\Helper;

use con4gis\MapsBundle\Resources\contao\classes\Utils;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use Contao\Database;
use Contao\Image;
use Contao\Input;
use Contao\System;

/**
 * Class DcaHelper
 * @package con4gis\CoreBundle\Classes\Helper
 */
class DcaHelper
{


    /**
     * save_callback: Erstelle eine Uuid.
     * @param $varValue
     * @param $dc
     * @return string
     */
    public static function generateUuid($varValue, $dc)
    {
        if (!$varValue) {
            return C4GBrickCommon::getGUID();
        }

        return $varValue;
    }


    /**
     * options_callback: Gibt eine Liste der Tabellen zurück.
     * @return array
     */
    public function cbGetTables()
    {
        $db     = Database::getInstance();
        $tables = $db->listTables();
        return $tables;
    }


    /**
     * options_callback: Gibt eine Liste der Felder einer Tabelle zurück.
     * @param $dc
     * @return array
     */
    public function cbGetFields($dc)
    {
        $data   = array();
        $table  = $dc->activeRecord->srctable;
        $db     = Database::getInstance();

        if ($table) {
            System::loadLanguageFile($table);
            $fields = $db->listFields($table);

            if (is_array($fields) && count($fields)) {
                foreach ($fields as $field) {
                    if ($field['name'] != 'PRIMARY') {
                        if (isset($GLOBALS['TL_LANG'][$table][$field['name']][0])) {
                            $label                  = $GLOBALS['TL_LANG'][$table][$field['name']][0];
                            $label                 .= ' [' . $field['name'] . ']';
                            $data[$field['name']]   = $label;
                        } else {
                            $data[$field['name']] = $field['name'];
                        }
                    }
                }
            }
        }

        return $data;
    }


    /**
     * @param $varValue
     * @param $dc
     * @return mixed
     * @throws \Exception
     */
    public function setLocLon($varValue, $dc) {
        if (!Utils::validateLon($varValue)) {
            throw new \Exception($GLOBALS['TL_LANG']['c4g_maps']['geox_invalid']);
        }
        return $varValue;
    }


    /**
     * @param $varValue
     * @param $dc
     * @return mixed
     * @throws \Exception
     */
    public function setLocLat($varValue, $dc) {
        if (!Utils::validateLat($varValue)) {
            throw new \Exception($GLOBALS['TL_LANG']['c4g_maps']['geoy_invalid']);
        }
        return $varValue;
    }

    /**
     * Gibt einen HTML-String zurück, der den Geopicker enthält.
     * @param \DataContainer $dc
     * @return string
     */
    public function getPickerLink(\DataContainer $dc)
    {
        if (!$GLOBALS['TL_JAVASCRIPT']['c4g-maps-backend'])
        {
            $GLOBALS['TL_JAVASCRIPT']['c4g-maps-backend'] = 'bundles/con4gismaps/js/c4g-maps-backend.js';
        }
        $input = Input::get('act');
        $strField = 'ctrl_' . $dc->field . (($input == 'editAll') ? '_' . $dc->id : '');
        if (substr($strField,-1,1)=='y') {
            $strFieldX = substr($strField,0,-1).'x';
            $strFieldY = $strField;
        } else {
            $strFieldX = $strField;
            $strFieldY = substr($strField,0,-1).'y';
        }

        return ' <a href="con4gis/api/geopickerService?rt=' . REQUEST_TOKEN .
            '" title="' . $GLOBALS['TL_LANG']['c4g_maps']['geopicker'] .
            '" style="padding-left:3px" onclick="c4g.maps.backend.showGeoPicker(this.href,' .
            $strFieldX . ',' . $strFieldY . ', {title:\'' . $GLOBALS['TL_LANG']['c4g_maps']['geopicker']. '\'});return false">' .
            Image::getHtml('bundles/con4gismaps/js/images/be-icons/geopicker.png',
                $GLOBALS['TL_LANG']['tl_content']['editalias'][0], 'style="vertical-align:top"') . '</a>';

    }
}
