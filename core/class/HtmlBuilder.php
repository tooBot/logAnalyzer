<?php
namespace core;

require 'Analyzer.php';

class HtmlBuilder
{

    const
        PLACEHOLDER_SPELLS = 'Всего использовано магических стрел (в том числе отражение с использованием приема \'Зачарованная броня\')',
        PLACEHOLDER_SPELLS_HITS = 'Всего попаданий магическими стрелами (в том числе с использованием приема \'Зачарованная броня\')',
        PLACEHOLDER_SPELLS_MISS = 'Всего промахов магическими стрелами / процентное соотношение промахов к общему количеству ударов магическими стрелами',
        PLACEHOLDER_SPELLS_FAILURE = 'Всего провалов магических стрел/ процентное соотношение промахов к общему количеству ударов магическими стрелами',

        PLACEHOLDER_HITS_ALL = 'Всего физических ударов (в том числе с использованием приема \'Активный блок\' и \'Активный удар\')',
        PLACEHOLDER_HITS = 'Всего попаданий физическими ударами в цель (в том числе удары в блок или отраженные удары) / процентное соотношение попаданий к общему количеству ударов',
        PLACEHOLDER_MISS = 'Всего промахов физическими ударами / процентное соотношение промахов к общему количеству ударов',

        PLACEHOLDER_BLOCKS = 'Всего ударов в блок физическими ударами / процентное соотношение ударов в блок к общему количеству ударов',
        PLACEHOLDER_BREAK_BLOCKS = 'Всего пробито блоков / процентное соотношение ударов, пробивших блок, к общему количеству ударов в блок',
        PLACEHOLDER_BREAK_ARMOR = 'Всего пробито брони / процентное соотношение ударов, пробивших броню, к общему количеству ударов',

        PLACEHOLDER_SIMP_HITS = 'Всего обычных ударов / процентное соотношение обычных ударов к общему количеству ударов',
        PLACEHOLDER_KRIT_HITS = 'Всего критических ударов / процентное соотношение критических ударов к общему количеству ударов',
        PLACEHOLDER_WEAK_HITS = 'Всего ослабленных ударов / процентное соотношение ослабленных ударов к общему количеству ударов';


    public function buildHTML($logId)
    {
        $html =
            '<div align="center">' .
            '<link rel="stylesheet" href="css/tables.css">' .
            '<form method="post" action="index.php">' .
            '<input type="text" name="logId" placeholder="Номер лога боя..."><br />' .
            '<input type="submit" class="btn btn-default" value="Загрузить"">' .
            '</form>'.
            '<hr>' .
            '</div>';

        $analyzer = new Analyzer();
        $logs = $analyzer->parseXML($logId);

        if (is_array($logs)) {
            if (!isset($logs['error'])) {
                foreach ($logs as $hero => $data) {
                    if ($hero != 'result') {

                        $html .= '<h2 align="center">' . $hero . '</h2>';

                        foreach ($data as $enemy => $stats) {
                            $html .= '<div class="userBlock">';

                            if (isset($stats['spells'])) {
                                $html .= '<br />';
                                $html .= '<table>';
                                $html .=
                                    '<tr>' .
                                    '<th>МАГИЯ</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_SPELLS . '">Всего магических стрел</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_SPELLS_HITS . '">Всего попаданий</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_SPELLS_MISS . '">Всего промахов</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_SPELLS_FAILURE . '">Всего провалов</th>' .
                                    '</tr>' .

                                    '<tr>' .
                                    '<td>' . $enemy . '</td>' .
                                    '<td>' . $stats['spells']['spellAllCount'] . '</td>' .
                                    '<td>' . (isset($stats['spells']['spellHitCount']) ? $stats['spells']['spellHitCount'] : 0) . '/' . round($stats['spells']['spellHitCount'] / $stats['spells']['spellAllCount'] * 100) . '% </td>' .
                                    '<td>' . (isset($stats['spells']['spellMissCount']) ? $stats['spells']['spellMissCount'] : 0) . '/' . round($stats['spells']['spellMissCount'] / $stats['spells']['spellAllCount'] * 100) . '% </td>' .
                                    '<td>' . (isset($stats['spells']['spellFailureCount']) ? $stats['spells']['spellFailureCount'] : 0) . '/' . round($stats['spells']['spellFailureCount'] / $stats['spells']['spellAllCount'] * 100) . '% </td>' .
                                    '</tr>';

                                $html .= '</table>';
                            }

                            if (isset($stats['hits'])) {
                                $html .= '<br />';
                                $html .= '<table>';
                                $hits = $stats['hits']['hitCount'] - $stats['hits']['missHitCount'];
                                $html .=
                                    '<tr>' .
                                    '<th>УДАРЫ</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_HITS_ALL . '">Всего ударов</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_HITS . '">Всего попаданий</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_MISS . '">Всего промахов</th>' .
                                    '</tr>' .

                                    '<tr>' .
                                    '<td>' . $enemy . '</td>' .
                                    '<td>' . $stats['hits']['hitCount'] . '</td>' .
                                    '<td>' . $hits . '/' . round($hits / $stats['hits']['hitCount'] * 100) . '% </td>' .
                                    '<td>' . (isset($stats['hits']['missHitCount']) ? $stats['hits']['missHitCount'] : 0) . '/' . round((isset($stats['hits']['missHitCount']) ? $stats['hits']['missHitCount'] : 0) / $stats['hits']['hitCount'] * 100) . '% </td>' .
                                    '</tr>';

                                $html .= '</table>';

                                $html .= '<br />';
                                $html .= '<table>';
                                $simpleHits = $stats['hits']['hitCount'] - $stats['mods']['kritCount'] - $stats['mods']['weakCount'];
                                $html .=
                                    '<tr>' .
                                    '<th>МОДИФИКАТОРЫ</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_HITS_ALL . '">Всего ударов</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_SIMP_HITS . '">Обычные удары</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_KRIT_HITS . '">Критические удары</th>' .
                                    '<th class="tooltip" data-title="' . self::PLACEHOLDER_WEAK_HITS . '">Ослабленные удары</th>' .
                                    '</tr>' .

                                    '<tr>' .
                                    '<td>' . $enemy . '</td>' .
                                    '<td>' . $stats['hits']['hitCount'] . '</td>' .
                                    '<td>' . $simpleHits . '/' . round($simpleHits / $stats['hits']['hitCount'] * 100) . '% </td>' .
                                    '<td>' . (isset($stats['mods']['kritCount']) ? $stats['mods']['kritCount'] : 0) . '/' . round($stats['mods']['kritCount'] / $stats['hits']['hitCount'] * 100) . '% </td>' .
                                    '<td>' . (isset($stats['mods']['weakCount']) ? $stats['mods']['weakCount'] : 0) . '/' . round($stats['mods']['weakCount'] / $stats['hits']['hitCount'] * 100) . '% </td>' .
                                    '</tr>';

                                $html .= '</table>';

                                //Пробои. Всего ударов попало (в т.ч. пробой блока), пробито брони, всего ударов в блок, пробито блока
                                $html .= '<br />';
                                $html .= '<table>';

                                if ($hits > 0) {
                                    $html .=
                                        '<tr>' .
                                        '<th>ПРОБОИ</th>' .
                                        '<th class="tooltip" data-title="' . self::PLACEHOLDER_HITS_ALL . '">Всего ударов попало</th>' .
                                        '<th class="tooltip" data-title="' . self::PLACEHOLDER_BREAK_ARMOR . '">Пробито брони</th>' .
                                        '<th class="tooltip" data-title="' . self::PLACEHOLDER_BLOCKS . '">Всего ударов в блок</th>' .
                                        '<th class="tooltip" data-title="' . self::PLACEHOLDER_BREAK_BLOCKS . '">Пробито блока</th>' .
                                        '</tr>' .

                                        '<tr>' .
                                        '<td>' . $enemy . '</td>' .
                                        '<td>' . $hits . '</td>' .
                                        '<td>' . (isset($stats['breaks']['anarCount']) ? $stats['breaks']['anarCount'] : 0) . '/' . round((isset($stats['breaks']['anarCount']) ? $stats['breaks']['anarCount'] : 0) / $hits * 100) . '% </td>' .
                                        '<td>' . (isset($stats['hits']['blockCount']) ? $stats['hits']['blockCount'] : 0) . '/' . round((isset($stats['hits']['blockCount']) ? $stats['hits']['blockCount'] : 0) / $hits * 100) . '% </td>' .
                                        '<td>' . (isset($stats['breaks']['blockHitCount']) ? $stats['breaks']['blockHitCount'] : 0) . '/' . round((isset($stats['breaks']['blockHitCount']) ? $stats['breaks']['blockHitCount'] : 0) / (isset($stats['hits']['blockCount']) ? $stats['hits']['blockCount'] : 1) * 100) . '% </td>' .
                                        '</tr>';

                                    $html .= '</table>';
                                }

                            }

                            $html .= '</div>';
                        }
                    }
                }
            } else {
                $html .= '<div class="error">' . $logs['error'] . '</div>';
            }
        }

        return $html;
    }
}