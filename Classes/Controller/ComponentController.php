<?php

/**
 * Component controller
 *
 * @category Tollwerk
 * @package Tollwerk\TwComponentlibrary
 * @subpackage Tollwerk\TwComponentlibrary\Controller
 * @author Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright Copyright © 2018 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  Copyright © 2018 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Tollwerk\TwComponentlibrary\Controller;

use Tollwerk\TwComponentlibrary\Component\ComponentInterface;
use Tollwerk\TwComponentlibrary\Component\Preview\FluidTemplate;
use Tollwerk\TwComponentlibrary\Service\GraphvizService;
use Tollwerk\TwComponentlibrary\Utility\Graph;
use Tollwerk\TwComponentlibrary\Utility\Scanner;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Component controller
 *
 * @package Tollwerk\TwComponentlibrary
 * @subpackage Tollwerk\TwComponentlibrary\Controller
 */
class ComponentController extends ActionController
{
    /**
     * Render a component
     *
     * @param string $component Component class
     * @return string Rendered component
     */
    public function renderAction($component)
    {
        // Register common stylesheets & scripts
        FluidTemplate::addCommonStylesheets($this->settings['stylesheets']);
        FluidTemplate::addCommonHeaderScripts($this->settings['headerScripts']);
        FluidTemplate::addCommonFooterScripts($this->settings['footerScripts']);

        $componentInstance = $this->objectManager->get($component, $this->controllerContext);
        if ($componentInstance instanceof ComponentInterface) {
            return trim($componentInstance->render());
        }

        return $this->view->render();
    }

    /**
     * Graph action
     *
     * @param string $component Component class
     * @return string SVG component graph
     * @todo Add a dummy graph telling that GraphViz isn't available
     */
    public function graphAction($component = null)
    {
        $graphvizService = GeneralUtility::makeInstanceService('graphviz', 'svg');
        if ($graphvizService instanceof GraphvizService) {
            $graph = new Graph(Scanner::discoverAll());
            return $graphvizService->createGraph($graph($component));
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="30" viewBox=".2 -6.2 200 30"><text x="100" y="7" text-anchor="middle" font-family="sans-serif" font-size="11">Component graph cannot be created</text><text x="100" y="17" text-anchor="middle" font-family="sans-serif" font-size="8">Please check your GraphViz installation</text><path fill="none" stroke="#000" stroke-miterlimit="10" d="M199.73 18.133c0 2.75-2.25 5-5 5h-189c-2.75 0-5-2.25-5-5v-19c0-2.75 2.25-5 5-5h189c2.75 0 5 2.25 5 5v19z"/></svg>';
    }
}
