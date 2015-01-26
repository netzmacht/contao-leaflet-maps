<?php

namespace Netzmacht\Contao\Leaflet\runonce;

/**
 * Migrate controller.
 *
 * @package Netzmacht\Contao\Leaflet\runonce
 */
class migrate
{
    /**
     * Execute the migration.
     *
     * @return void
     */
    public function execute()
    {
        $database = \Database::getInstance();

        if ($database->fieldExists('coordinates', 'tl_leaflet_marker')) {
            $this->createFields($database);
            $this->convertCoordinates($database);
        }
    }

    /**
     * @param $database
     *
     * @param \Database $database The database connection.
     *
     * @return array
     */
    protected function createFields(\Database $database)
    {
        foreach (array('latitude', 'longitude', 'altitude') as $field) {
            if (!$database->fieldExists('latitude', 'tl_leaflet_marker')) {
                $database->execute(sprintf('ALTER TABLE tl_leaflet_marker ADD %s float NULL;', $field));
            }
        }

    }

    /**
     * Convert coordinates to new splitted fields.
     *
     * @param \Database $database The database connection.
     *
     * @return void
     */
    private function convertCoordinates(\Database $database)
    {
        $query = <<<SQL
SELECT id,coordinates
FROM   tl_leaflet_marker
WHERE  coordinates <> '' AND latitude IS NULL aND longitude IS NULL AND altitude IS NULL
SQL;

        $result = $database->query($query);

        while ($result->next()) {
            list($latitude, $longitude, $altitude) = trimsplit(',', $result->coordinates);

            $database
                ->prepare('UPDATE tl_leaflet_marker %s WHERE id=?')
                ->set(
                    array(
                        'latitude'  => $latitude,
                        'longitude' => $longitude,
                        'altitude'  => $altitude
                    )
                )
                ->execute($result->id);
        }
    }
}

$controller = new migrate();
$controller->execute();
