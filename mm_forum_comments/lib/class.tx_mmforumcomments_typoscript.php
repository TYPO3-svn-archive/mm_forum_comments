<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Hauke Hain <hhpreuss@googlemail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.*
 *
 *   52: class tx_mmforumcomments_typoscript
 *
 * TOTAL FUNCTIONS: 17
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 class tx_mmforumcomments_typoscript {

	public static function returnResultOfTSif(&$data, &$tsarr) {
    if (is_array($tsarr['if.'])) {
      $key = key($tsarr['if.']);
      $value = tx_mmforumcomments_typoscript::getTSvalueOfTEXT($data, $tsarr['if.'][$key]);

      switch ($key) {
        case 'isTrue.':
          $bool = $value ? true : false;
          break;
        //TODO cases
      }
    } else {
      $bool = true;
    }

    return $bool;
  }

	public static function getTSvalueOfTEXT(&$data, &$tsarr) {
    $ret = '';

    if (is_array($tsarr)) {
      foreach (array_keys($tsarr) as $key) {
        switch (strtolower($key)) {
          case 'field':
            $ret .= $data[$tsarr[$key]];
            break;
          case 'value':
            $ret .= $tsarr[$key];
            break;
          case 'stdwrap.':
            $wrap = $tsarr[$key]['wrap'];
            break;
          case 'wrap':
            $wrap = $tsarr[$key];
            break;
          case 'typolink.':
            $ret .= tx_mmforumcomments_typoscript::getTypolinkURL($data, $tsarr[$key]);
            break;
        }
      }
    }

    return tx_mmforumcomments_typoscript::wrap($ret, $wrap);
  }

	public static function getTypolinkURL(&$data, &$tsarr) {
    $url = '';
    $record = 'page';

    if (is_array($tsarr)) {
      foreach (array_keys($tsarr) as $key) {
        switch (strtolower($key)) {
          case 'parameter.':
            $parameter = tx_mmforumcomments_typoscript::getTSvalueOfTEXT($data, $tsarr[$key]);
            $linktitle = tx_mmforumcomments_typoscript::getPagesTitle($parameter);
            $url .= $parameter;
            break;
          case 'additionalparams.':
            if (is_array($tsarr[$key]['cObject.'])) {
              if (strtoupper($tsarr[$key]['cObject']) === 'COA') {
                $coa = $tsarr[$key]['cObject.'];
                $additionalParams = '';

                foreach (array_keys($coa) as $key2) {
                  if ((strstr($key2, '.') && is_array($coa[$key2]) && strtoupper($coa[str_replace('.','',$key2)]) === 'TEXT') && (tx_mmforumcomments_typoscript::returnResultOfTSif($data, $coa))) {
                    $additionalParams .= tx_mmforumcomments_typoscript::getTSvalueOfTEXT($data, $coa[$key2]);
                  }
                }

                preg_match('/\&\w+\[(\w+)\]\=(\d+)/', $additionalParams, $match);

                if ($match[1] && $match[2]) {
                  $record = $match[1];
                  $linktitle = tx_mmforumcomments_typoscript::getPagesTitle($match[2], $match[1]);
                  $url = $match[2];
                } else {
                  $url .= $additionalParams;
                }
              }
            } else {
              $url .= tx_mmforumcomments_typoscript::getTSvalueOfTEXT($data, $tsarr[$key]);
            }
            $ret .= $tsarr[$key];
            break;
        }
      }
    }

    return empty($linktitle) ? '[URL]record:' . $record . ':' . $url . '[/URL]' : '[URL="record:' . $record . ':' . $url . '"]' . $linktitle . '[/URL]';
  }
  
  public static function wrap($content, $wrap, $char='|') {
    if ($wrap) {
      $wrapArr = explode($char, $wrap);
      return trim($wrapArr[0]).$content.trim($wrapArr[1]);
    } else {
      return $content;
    }
  }
  
  public static function getPagesTitle($pid, $table='pages') {
    if (intval($pid) > 0) {
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title',
   					                                        $table,
                                                    'uid=' . intval($pid),
                                                    '', '', '1');
  
  		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) == 1) {
  		 $data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
  		 $GLOBALS['TYPO3_DB']->sql_free_result($res);
  
  		 return $data['title'];
  		}
    }

		return '';
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_typoscript.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mm_forum_comments/lib/class.tx_mmforumcomments_typoscript.php']);
}

?>