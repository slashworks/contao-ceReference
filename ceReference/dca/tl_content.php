<?php
/**
 * Created by PhpStorm.
 * User: jrgregory
 * Date: 26.08.14 | KW 35
 * Time: 19:38
 */

$GLOBALS['TL_DCA']['tl_content']['palettes']['ceReference'] = '{type_legend},type,headline;{type_referenceConfig},referenceType,referenceItems;';

//$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = array
//(
//    'DCA_Reference' => 'purgeRefItems'
//);

$fields = array
(

    'referenceType' => array
    (

        'label'                   => &$GLOBALS['TL_LANG']['tl_content']['referenceType'],
        'exclude'                 => true,
        'inputType'               => 'select',
        'options'                 => array('news', 'faq', 'calendar'),
        'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
        'eval'                    => array('submitOnChange'=>true, 'includeBlankOption'=>true),
        'sql'                     => "varchar(32) NOT NULL default ''"

    ),

    'referenceItems' => array
    (

        'label'                   => &$GLOBALS['TL_LANG']['tl_content']['referenceItems'],
        'exclude'                 => true,
        'inputType'               => 'select',
        'options_callback'        => array('DCA_Reference', 'getReferences'),
        'eval'                    => array('chosen'=>true, 'multiple' => true),
        'sql'                     => "blob NOT NULL default ''"

    ),



);


$GLOBALS['TL_DCA']['tl_content']['fields'] = array_merge($fields, $GLOBALS['TL_DCA']['tl_content']['fields']);

class DCA_Reference extends Backend

{

    public function getReferences(DataContainer $dc)

    {

        $_r = false;

        switch($dc->activeRecord->referenceType) {

            case 'news':
                $_r = $this->getReferenceItems(NewsModel, 'headline');
                break;

            case 'faq':
                $_r = $this->getReferenceItems(FaqModel, 'question');
                break;

            case 'calendar':
                $_r = $this->getReferenceItems(CalendarEventsModel, 'title');
                break;

        }

        return $_r;

    }

    private function getReferenceItems($model, $title)
    {

        $refObj = $model::findAll(array(

            published => 1

        ));

        $data = array();


        while($refObj->next())
        {

            $data[$refObj->id] = $refObj->$title;

        }

        return $data;

    }

    public function purgeRefItems()
    {

      /*  Database::getInstance()
            ->prepare('UPDATE tl_content SET referenceItems = NULL WHERE id=?')
            ->execute($dc->activeRecord->id);*/

    }

}