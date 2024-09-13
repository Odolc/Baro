<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function baro_install()
{
    jeedom::getApiKey('baro');

    $cron = cron::byClassAndFunction('baro', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }

    config::save('functionality::cron5::enable', 0, 'baro');
    config::save('functionality::cron10::enable', 0, 'baro');
    config::save('functionality::cron15::enable', 0, 'baro');
    config::save('functionality::cron30::enable', 1, 'baro');
    config::save('functionality::cronHourly::enable', 0, 'baro');

    //message::add('Plugin Tendance Baro', 'Merci pour l\'installation du plugin.');
}

function baro_update()
{
    jeedom::getApiKey('baro');

    $cron = cron::byClassAndFunction('baro', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }

    if (config::byKey('functionality::cron5::enable', 'baro', -1) == -1) {
        config::save('functionality::cron5::enable', 0, 'baro');
    }

    if (config::byKey('functionality::cron10::enable', 'baro', -1) == -1) {
        config::save('functionality::cron10::enable', 0, 'baro');
    }

    if (config::byKey('functionality::cron15::enable', 'baro', -1) == -1) {
        config::save('functionality::cron15::enable', 0, 'baro');
    }

    if (config::byKey('functionality::cron30::enable', 'baro', -1) == -1) {
        config::save('functionality::cron30::enable', 1, 'baro');
    }

    if (config::byKey('functionality::cronHourly::enable', 'baro', -1) == -1) {
        config::save('functionality::cronHourly::enable', 0, 'baro');
    }

    $plugin = plugin::byId('baro');
    $eqLogics = eqLogic::byType($plugin->getId());
    foreach ($eqLogics as $eqLogic) {
        //updateLogicalId($eqLogic, 'tendance', 'td');
        //updateLogicalId($eqLogic, 'tendance_num', 'td_num');
        updateLogicalId($eqLogic, 'pressure', null, 2, 'Pression Atmosphérique');
        updateLogicalId($eqLogic, 'dPdT', null, 2);
    }

    //resave eqs for new cmd:
    try {
        $eqs = eqLogic::byType('baro');
        foreach ($eqs as $eq) {
            $eq->save();
        }
    } catch (Exception $e) {
        $e = print_r($e, 1);
        log::add('baro', 'error', '[ALERT] baro_update ERROR: ' . $e);
    }

    //message::add('Plugin Tendance Baro', 'Merci pour la mise à jour de ce plugin, consultez le changelog.');
    foreach (eqLogic::byType('baro') as $baro) {
        $baro->getInformations();
    }
}

function updateLogicalId($eqLogic, $from, $to, $_historizeRound = null, $name = null)
{
    $command = $eqLogic->getCmd(null, $from);
    if (is_object($command)) {
        if ($to != null) {
            $command->setLogicalId($to);
        }
        if ($_historizeRound != null) {
            $command->setConfiguration('historizeRound', $_historizeRound);
        }
        if ($name != null) {
            $command->setName($name);
        }
        $command->save();
    }
}

function baro_remove()
{
    $cron = cron::byClassAndFunction('baro', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}
