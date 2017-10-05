<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Dca;

use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;
use Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Model\MapModel;

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
                specialchars($this->translator->translate('editalias.1', 'tl_content', [$dataContainer->value])),
                specialchars(
                    str_replace(
                        "'",
                        "\\'",
                        sprintf($this->translator->translate('editalias.1', 'tl_content', [$dataContainer->value]))
                    )
                )
            ),
            \Image::getHtml(
                'alias.gif',
                $this->translator->translate('editalias.0', 'tl_content', [$dataContainer->value]),
                'style="vertical-align:top"'
            )
        );
    }
}
