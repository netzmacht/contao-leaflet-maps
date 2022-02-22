<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

use Contao\Image;
use Contao\RequestToken;
use Contao\StringUtil;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Class Module is the helper for the tl_module dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
final class FrontendIntegrationListener
{
    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * FrontendIntegration constructor.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param Translator        $translator        Translator.
     */
    public function __construct(RepositoryManager $repositoryManager, Translator $translator)
    {
        $this->translator        = $translator;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Get all leaflet maps.
     *
     * @return array
     */
    public function getMaps(): array
    {
        $repository = $this->repositoryManager->getRepository(MapModel::class);
        $collection = $repository->findAll(['order' => 'title']);

        return OptionsBuilder::fromCollection($collection, 'title')->getOptions();
    }

    /**
     * Get edit map link wizard.
     *
     * @param \DataContainer $dataContainer The dataContainer driver.
     *
     * @return string
     */
    public function getEditMapLink($dataContainer): string
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
            RequestToken::get(),
            sprintf(
                $pattern,
                StringUtil::specialchars(
                    $this->translator->trans('editalias.1', [$dataContainer->value], 'contao_tl_content')
                ),
                StringUtil::specialchars(
                    str_replace(
                        "'",
                        "\\'",
                        sprintf($this->translator->trans('editalias.1', [$dataContainer->value], 'contao_tl_content'))
                    )
                )
            ),
            Image::getHtml(
                'alias.gif',
                $this->translator->trans('editalias.0', [$dataContainer->value], 'contao_tl_content'),
                'style="vertical-align:top"'
            )
        );
    }
}
