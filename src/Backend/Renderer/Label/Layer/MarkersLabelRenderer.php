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

namespace Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer;

use Netzmacht\Contao\Leaflet\Model\MarkerModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class MarkersLabelRenderer.
 *
 * @package Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer
 */
final class MarkersLabelRenderer extends AbstractLabelRenderer
{
    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * FileLabelRenderer constructor.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getLayerType(): string
    {
        return 'markers';
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $row, string $label, Translator $translator): string
    {
        $repository = $this->repositoryManager->getRepository(MarkerModel::class);
        $count      = $repository->countBy(['pid=?'], [$row['pid']]);
        $label     .= sprintf(
            '<span class="tl_gray"> (%s %s)</span>',
            $count,
            $translator->trans('countEntries', [], 'contao_tl_leaflet_layer')
        );

        return $label;
    }
}
