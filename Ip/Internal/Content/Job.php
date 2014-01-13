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

        if (!empty($urlVars[0])) {
            $potentialZoneUrl = urldecode($urlVars[0]);
            foreach ($zonesData as $zoneData) {
                if ($zoneData['url'] == $potentialZoneUrl) {
                    $result['zoneUrl'] = $potentialZoneUrl;
                    $result['zone'] = $zoneData['name'];
                    $result['relativeUri'] = isset($urlParts[1]) ? $urlParts[1] : '';
                    break;
                }
            }

            if (empty($result['zone'])) {
                $zoneWithNoUrl = null;
                foreach ($zonesData as $zoneData) {
                    if ($zoneData['url'] === '') {
                        $result['zoneUrl'] = '';
                        $result['zone'] = $zoneData['name'];
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
        $result['urlVars'] = isset($urlParts[1]) ? explode('/', $urlParts[1]) : array();

        return $result;
    }
} 