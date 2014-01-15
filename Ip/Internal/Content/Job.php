<?php


namespace Ip\Internal\Content;


class Job
{
    /**
     * Zone routing.
     *
     * @param $info
     * @return array|null
     * @throws \Ip\Exception
     */
    public static function ipRouteAction_70($info)
    {
        $zonesData = ipContent()->getZones();
        $urlParts = explode('/', $info['relativeUri'], 2);

        $result = array();

        if (!empty($urlParts[0])) {
            $potentialZoneUrl = urldecode($urlParts[0]);
            foreach ($zonesData as $zoneData) {
                if ($zoneData->getUrl() == $potentialZoneUrl) {
                    $result['zoneUrl'] = $potentialZoneUrl;
                    $result['zone'] = $zoneData->getName();
                    $result['relativeUri'] = isset($urlParts[1]) ? $urlParts[1] : '';
                    break;
                }
            }

            if (empty($result['zone'])) {
                $zoneWithNoUrl = null;
                foreach ($zonesData as $zoneData) {
                    if ($zoneData->getUrl() === '') {
                        $result['zoneUrl'] = '';
                        $result['zone'] = $zoneData->getName();
                        break;
                    }
                }
            }
        } else {
            if (empty($zonesData)) {
                throw new \Ip\Exception('Please insert at least one zone');
            } else {
                $firstZoneData = array_shift($zonesData);
                $result['zone'] = $firstZoneData->getName();
            }
        }

        if (empty($result['zone'])) {
            return NULL;
        }

        $result['plugin'] = 'Content';
        $result['controller'] = 'PublicController';
        $result['action'] = 'index';
        $result['urlParts'] = isset($urlParts[1]) ? explode('/', $urlParts[1]) : array();

        return $result;
    }
} 