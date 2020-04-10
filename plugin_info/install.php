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

function baro_install() {
    jeedom::getApiKey('baro');
    config::save('functionality::cron15::enable', 1, 'baro');
    config::save('functionality::cron30::enable', 0, 'baro');
    $cron = cron::byClassAndFunction('baro', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}

function baro_update() {
    jeedom::getApiKey('baro');
    if (config::byKey('functionality::cron15::enable', 'baro', -1) == -1)
        config::save('functionality::cron15::enable', 1, 'baro');
    if (config::byKey('functionality::cron30::enable', 'baro', -1) == -1)
        config::save('functionality::cron30::enable', 0, 'baro');
    $cron = cron::byClassAndFunction('baro', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
    $plugin = plugin::byId('baro');
    $eqLogics = eqLogic::byType($plugin->getId());
    /* foreach ($eqLogics as $eqLogic) {

    }*/

    //resave eqs for new cmd:
        try
        {
            $eqs = eqLogic::byType('baro');
            foreach ($eqs as $eq){
                $eq->save();
            }
        }
        catch (Exception $e)
        {
            $e = print_r($e, 1);
            log::add('baro', 'error', 'baro_update ERROR: '.$e);
        }

    //message::add('baro', 'Merci pour la mise à jour de ce plugin,');
}

function updateLogicalId($eqLogic, $from, $to) {
    $barocmd = $eqLogic->getCmd(null, $from);
    if (is_object($baroCmd)) {
        $barocmd->setLogicalId($to);
        $barocmd->save();
    }
}

function baro_remove() {
    $cron = cron::byClassAndFunction('baro', 'pull');
    if (is_object($cron)) {
        $cron->remove();
    }
}
?>