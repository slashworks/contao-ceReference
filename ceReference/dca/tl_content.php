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
     * @package     Core
     *
     */


    // palettes
    $GLOBALS['TL_DCA']['tl_content']['palettes']['ceReference'] = '{type_legend},type,headline;{type_referenceConfig},referenceType,referenceItems;';


    // additional tl_content fields
    $fields = array
    (

        'referenceType'  => array
        (

            'label'     => &$GLOBALS['TL_LANG']['tl_content']['referenceType'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('tl_news' => 'news', 'tl_faq' => 'faq', 'tl_calendar_events' => 'calendar'),
            'reference' => &$GLOBALS['TL_LANG']['tl_content'],
            'eval'      => array('submitOnChange' => true, 'includeBlankOption' => true, 'chosen' => true),
            'save_callback' => array(
                array('DCA_Reference', 'purgeReferenceItems')
            ),
            'sql'       => "varchar(32) NOT NULL default ''"

        ),

        'referenceItems' => array
        (

            'label'     => &$GLOBALS['TL_LANG']['tl_content']['referenceItems'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => array(

                'columnFields' => array
                (
                    'referenceItemId' => array
                    (
                        'label'            => &$GLOBALS['TL_LANG']['tl_content']['referenceItems'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'options_callback' => array('DCA_Reference', 'getReferences'),
                        'eval'             => array('style' => 'width:250px', 'includeBlankOption' => true, 'chosen' => true)
                    )
                )
            ),
            'sql'       => "blob NULL"

        ),
    );


    $GLOBALS['TL_DCA']['tl_content']['fields'] = array_merge($fields, $GLOBALS['TL_DCA']['tl_content']['fields']);




    /**
     * Class DCA_Reference
     */
    class DCA_Reference extends Backend {
        /**
         * @param MultiColumnWizard $dc
         *
         * @return array|bool
         */
        public function getReferences(MultiColumnWizard $dc) {

            $oReferenceItem = false;

            switch ($dc->activeRecord->referenceType) {

                case 'tl_news':
                    $oReferenceItem = $this->getReferenceItems(NewsModel, 'headline');
                    break;

                case 'tl_faq':
                    $oReferenceItem = $this->getReferenceItems(FaqModel, 'question');
                    break;

                case 'tl_calendar_events':
                    $oReferenceItem = $this->getReferenceItems(CalendarEventsModel, 'title');
                    break;

            }

            return $oReferenceItem;

        }

        /**
         * Get referenced items by model
         *
         * @param $model
         * @param $title
         *
         * @return array
         */
        private function getReferenceItems($model, $title) {

            $refObj = $model::findAll(array(
                                          "published" => 1
                                      ));

            $data = array();
            while ($refObj->next()) {
                $data[$refObj->id] = $refObj->$title;

            }

            return $data;

        }


        /**
         * Clear blob after update types
         *
         * @param               $sValue
         * @param DataContainer $dc
         *
         * @return mixed
         */
        public function purgeReferenceItems($sValue, DataContainer $dc) {
            Database::getInstance()->prepare('UPDATE tl_content SET referenceItems = NULL WHERE id=?')->execute($dc->activeRecord->id);
            return $sValue;
        }

    }