<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Dca;

use Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class Module is the helper for the tl_module dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class FrontendIntegration
{
    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * FrontendIntegration constructor.
     *
     * @param Translator $translator Translator.
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Generate the callback definition.
     *
     * @param string $methodName Callback method name.
     *
     * @return callable
     */
    public static function callback($methodName)
    {
        return CallbackFactory::service('leaflet.dca.frontend-integration', $methodName);
    }

    /**
     * Get all leaflet maps.
     *
     * @return array
     */
    public function getMaps()
    {
        $collection = MapModel::findAll();

        return OptionsBuilder::fromCollection($collection, 'title')->getOptions();
    }

    /**
     * Get edit map link wizard.
     *
     * @param \DataContainer $dataContainer The dataContainer driver.
     *
     * @return string
     */
    public function getEditMapLink($dataContainer)
    {
        if ($dataContainer->value < 1) {
            return '';
        }

        $pattern  = 'title="%s" style="padding-left: 3px" onclick="Backend.openModalIframe(';
        $pattern .= '{\'width\':768,\'title\':\'%s\',\'url\':this.href});return false"';

        return sprintf(
            '<a href="%s%s&amp;popup=1&amp;rt=%s" %s>%s</a>',
            'contao/main.php?do=leaflet_map&amp;table=tl_leaflet_map&amp;act=edit&amp;id=',
            $dataContainer->value,
            \RequestToken::get(),
            sprintf(
                $pattern,
                specialchars($this->translator->trans('editalias.1', [$dataContainer->value], 'contao_tl_content')),
                specialchars(
                    str_replace(
                        "'",
                        "\\'",
                        sprintf($this->translator->trans('editalias.1', [$dataContainer->value], 'contao_tl_content'))
                    )
                )
            ),
            \Image::getHtml(
                'alias.gif',
                $this->translator->trans('editalias.0', [$dataContainer->value], 'contao_tl_content'),
                'style="vertical-align:top"'
            )
        );
    }
}
