<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\MiraklPhpConnector
 * Support: support@scaledev.fr
 */

namespace Scaledev\MiraklPhpConnector\Core\Collection;

use Scaledev\MiraklPhpConnector\Exception\BadCollectionElementException;

/**
 * Class AbstractCollection
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
abstract class AbstractCollection implements CollectionInterface
{
    const ELEMENT_NAME = null;

    /**
     * A list of objects.
     *
     * @var array
     */
    protected $list = array();

    /**
     * @inheritdoc
     */
    public function add($object)
    {
        if (get_class($object) != static::ELEMENT_NAME) {
            throw new BadCollectionElementException(
                static::class,
                static::ELEMENT_NAME,
                get_class($object)
            );
        }

        $this->list[] = $object;

        return $this;
    }

    /**
     * Get a list of objects.
     *
     * @return object[]
     */
    public function getList()
    {
        return $this->list;
    }
}
