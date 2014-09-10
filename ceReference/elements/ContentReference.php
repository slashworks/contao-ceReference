<?php
    /**
     *
     *          _           _                       _
     *         | |         | |                     | |
     *      ___| | __ _ ___| |____      _____  _ __| | _____
     *     / __| |/ _` / __| '_ \ \ /\ / / _ \| '__| |/ / __|
     *     \__ \ | (_| \__ \ | | \ V  V / (_) | |  |   <\__ \
     *     |___/_|\__,_|___/_| |_|\_/\_/ \___/|_|  |_|\_\___/
     *                                        web development
     *
     *     http://www.slash-works.de </> hallo@slash-works.de
     *
     *
     * @author      rwollenburg
     * @since       10.09.14 11:49
     * @package     slashworks
     *
     */


    namespace slashworks;


    /**
     * Class ContentReference
     *
     * @package slashworks
     */
    class ContentReference extends \ContentElement {

        /**
         * Template
         *
         * @var string
         */
        protected $strTemplate = 'ce_reference';


        /**
         * Generate the content element
         */
        protected function compile() {
            $aReferenceItems   = array();
            $aReferenceItemIds = deserialize($this->referenceItems);
            if (is_array($aReferenceItemIds)) {
                foreach ($aReferenceItemIds as $sReferenceItem) {
                    // generate and collect items
                    $aItem = $this->_generateUrl((string)$this->referenceType, (int)$sReferenceItem['referenceItemId']);
                    if ($aItem !== false) {
                        $aReferenceItems[] = $aItem;
                    }
                }
            }
            // assign items to template
            $this->Template->aReferenceItems   = $aReferenceItems;
            $this->Template->sEmptyListMessage = $GLOBALS['TL_LANG']['tl_ceReference']['EmpytListMessage'];

            return;
        }


        /**
         * Generate frontendurl by itemtype
         *
         * @param $sItemType
         * @param $iItemId
         *
         * @return array
         */
        private function _generateUrl($sItemType, $iItemId) {
            $sUrl   = "";
            $sTitle = "";
            switch ($sItemType) {
                case "tl_news":
                    $sUrl   = $this->replaceInsertTags("{{news_url::" . $iItemId . "}}");
                    $sTitle = $this->replaceInsertTags("{{news_title::" . $iItemId . "}}");
                    break;

                case "tl_faq":
                    $sUrl   = $this->replaceInsertTags("{{faq_url::" . $iItemId . "}}");
                    $sTitle = $this->replaceInsertTags("{{faq_title::" . $iItemId . "}}");
                    break;

                case "tl_calendar_events":
                    $sUrl   = $this->replaceInsertTags("{{event_url::" . $iItemId . "}}");
                    $sTitle = $this->replaceInsertTags("{{event_title::" . $iItemId . "}}");
                    break;
            }

            if (!empty($sUrl) && !empty($sTitle)) {
                return array("url" => $sUrl, "title" => $sTitle);
            } else {
                return false;
            }
        }
    }