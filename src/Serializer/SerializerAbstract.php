<?php
declare(strict_types = 1);

namespace Diaclone\Serializer;

use Diaclone\Pagination\CursorInterface;
use Diaclone\Pagination\PaginatorInterface;

abstract class SerializerAbstract
{
    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    abstract public function collection($resourceKey, $data);

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    abstract public function item($resourceKey, $data);

    /**
     * Serialize the meta.
     *
     * @param array $meta
     *
     * @return array
     */
    abstract public function meta(array $meta);

    /**
     * Serialize the paginator.
     *
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    abstract public function paginator(PaginatorInterface $paginator);

    /**
     * Serialize the cursor.
     *
     * @param CursorInterface $cursor
     *
     * @return array
     */
    abstract public function cursor(CursorInterface $cursor);

    public function mergeIncludes($transformedData, $includedData)
    {
        // If the serializer does not want the includes to be side-loaded then
        // the included data must be merged with the transformed data.
        if (! $this->sideloadIncludes()) {
            return array_merge($transformedData, $includedData);
        }

        return $transformedData;
    }

    /**
     * Indicates if includes should be side-loaded.
     *
     * @return bool
     */
    public function sideloadIncludes()
    {
        return false;
    }

    /**
     * Hook for the serializer to inject custom data based on the relationships of the resource.
     *
     * @param array $data
     * @param array $rawIncludedData
     *
     * @return array
     */
    public function injectData($data, $rawIncludedData)
    {
        return $data;
    }

    /**
     * Hook for the serializer to modify the final list of includes.
     *
     * @param array $includedData
     * @param array $data
     *
     * @return array
     */
    public function filterIncludes($includedData, $data)
    {
        return $includedData;
    }
}
