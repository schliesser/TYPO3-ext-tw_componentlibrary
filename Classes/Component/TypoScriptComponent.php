<?php

/**
 * TypoScript component
 *
 * @category Tollwerk
 * @package Tollwerk\TwComponentlibrary
 * @subpackage Tollwerk\TwComponentlibrary\Component
 * @author Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 ***********************************************************************************/

namespace Tollwerk\TwComponentlibrary\Component;

use Tollwerk\TwComponentlibrary\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * TypoScript component
 *
 * @package Tollwerk\TwComponentlibrary
 * @subpackage Tollwerk\TwComponentlibrary\Component
 */
class TypoScriptComponent extends AbstractComponent
{
    /**
     * Template file extensions
     *
     * @var string
     */
    protected $extension = 't3s';
    /**
     * Component type
     *
     * @var string
     */
    protected $type = 'typoscript';

    /**
     * Set the TypoScript key
     *
     * @param string $key TypoScript key
     */
    protected function setTypoScriptKey($key)
    {
        $this->config = trim($key) ?: null;
    }

    /**
     * Return component specific properties
     *
     * Override this method in sub classes to export specific properties.
     *
     * @return array Component specific properties
     */
    protected function exportInternal()
    {

        // Read the linked TypoScript
        if ($this->config !== null) {
            $typoScript = TypoScriptUtility::extractTypoScriptKeyForPidAndType(
                $this->page,
                $this->typeNum,
                $this->config
            );
            $this->template = empty($typoScript) ?
                null : TypoScriptUtility::serialize(implode('.', explode('.', $this->config, -1)), $typoScript);
        }

        return parent::exportInternal();
    }

    /**
     * Render this component
     *
     * @return string Rendered component (HTML)
     */
    public function render()
    {
        // Set the request arguments as GET parameters
        $_GET = $this->getRequestArguments();

        // Instantiate a frontend controller
        $TSFE = TypoScriptUtility::getTSFE($this->page, $this->typeNum);
        $cObj = new ContentObjectRenderer($TSFE);
        $cObj->start($TSFE->page, 'pages');
        $typoScript = TypoScriptUtility::extractTypoScriptKeyForPidAndType(
            $this->page,
            $this->typeNum,
            $this->config
        );

        return call_user_func_array([$cObj, 'cObjGetSingle'], $typoScript);
    }
}
