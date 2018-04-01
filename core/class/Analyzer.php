<?php
namespace core;

class Analyzer
{
    const XML_REQUEST_PATH = 'https://www.ereality.ru/log';

    private function getFullPath($logId, $page)
    {
        return self::XML_REQUEST_PATH . $logId . '/page' . $page . '.xml';
    }

    public function getXMLData($logId, $page)
    {
        $path = $this->getFullPath($logId, $page);

        $curl = curl_init($path);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    public function parseXML($logId)
    {
        $result = [];
        $page = 1;

        while(!isset($result['result'])) {
            if (!$logId) {
                return false;
            }

            $xml = $this->getXMLData($logId, $page);
            $xmlData = new \SimpleXMLElement($xml);

            foreach (($xmlData->xpath('//log')) as $log) {
                $hero = explode(';', $log['p1'])[0];
                if ($hero == '-') {
                    $hero = 'Невидимка';
                }

                $enemy = explode(';', $log['p2'])[0];
                if ($enemy == '-') {
                    $enemy = 'Невидимка';
                }

                if (isset($log->spell)) {
                    foreach ($log->spell as $spell) {
                        if ((int)$spell['result'] == 2) {
                            $result[$hero][$enemy]['spells']['spellMissCount']++;
                            $result[$hero][$enemy]['spells']['spellAllCount']++;
                        }

                        if ((int)$spell['type'] == 0 && (int)$spell['result'] == 0) {
                            $result[$hero][$enemy]['spells']['spellFailureCount']++;
                            $result[$hero][$enemy]['spells']['spellAllCount']++;
                        }

                        if ((int)$spell['type'] == 0 && (int)$spell['result'] == 3) {
                            $result[$hero][$enemy]['spells']['spellHitCount']++;
                            $result[$hero][$enemy]['spells']['spellAllCount']++;
                        }
                    }
                }

                if (isset($log->attack)) {
                    foreach ($log->attack as $attack) {
                        $result[$hero][$enemy]['hits']['hitCount']++;

                        if ((int)$attack['weak'] == 1) {
                            $result[$hero][$enemy]['mods']['weakCount']++;
                        }

                        if ((int)$attack['anar'] == 1) {
                            $result[$hero][$enemy]['breaks']['anarCount']++;
                        }

                        if ((int)$attack['block'] == 1) {
                            $result[$hero][$enemy]['blockCount']++;
                            $result[$hero][$enemy]['breaks']['blockHitCount']++;
                        }

                        if ((int)$attack['krit'] == 1) {
                            $result[$hero][$enemy]['mods']['kritCount']++;
                        }
                    }
                }

                if (isset($log->block)) {
                    foreach ($log->block as $block) {
                        $result[$hero][$enemy]['hits']['hitCount']++;
                        $result[$hero][$enemy]['hits']['blockCount']++;
                    }
                }

                if (isset($log->miss)) {
                    foreach ($log->miss as $miss) {
                        $result[$hero][$enemy]['hits']['hitCount']++;
                        $result[$hero][$enemy]['hits']['missHitCount']++;
                    }
                }
            }

            $battleResult = $xmlData->xpath('//results');
            if ($battleResult) {
                $result['result'] = $battleResult;
            }

            $page++;
        }

        return $result;
    }

    public function debugLogs($logId)
    {
        $result = [];
        $page = 1;

        while(!isset($result['result'])) {
            $xml = $this->getXMLData($logId, $page);
            $xmlData = new \SimpleXMLElement($xml);

            foreach (($xmlData->xpath('//log')) as $log) {
                $result[] = $log;
            }

            $battleResult = $xmlData->xpath('//results');
            if ($battleResult) {
                $result['result'] = $battleResult;
            }

            $page++;
        }

        return $result;
    }

}