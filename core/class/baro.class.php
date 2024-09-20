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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../core/php/baro.inc.php';

class baro extends eqLogic
{
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
    public static function deadCmd()
    {
        $return = array();
        foreach (eqLogic::byType('baro') as $baro) {
            foreach ($baro->getCmd() as $cmd) {
                preg_match_all("/#([0-9]*)#/", $cmd->getConfiguration('infoName', ''), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))) {
                        $return[] = array('detail' => __('Baro', __FILE__) . ' ' . $baro->getHumanName() . ' ' . __('dans la commande', __FILE__) . ' ' . $cmd->getName(), 'help' => __('Nom Information', __FILE__), 'who' => '#' . $cmd_id . '#');
                    }
                }
                preg_match_all("/#([0-9]*)#/", $cmd->getConfiguration('calcul', ''), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))) {
                        $return[] = array('detail' => __('Baro', __FILE__) . ' ' . $baro->getHumanName() . ' ' . __('dans la commande', __FILE__) . ' ' . $cmd->getName(), 'help' => __('Calcul', __FILE__), 'who' => '#' . $cmd_id . '#');
                    }
                }
            }
        }
        return $return;
    }
    public static $_widgetPossibility = array('custom' => true);
    public static function cron5()
    {
        foreach (eqLogic::byType('baro') as $baro) {
            log::add(__CLASS__, 'debug', '========================== CRON 5 ==========================');
            $baro->getInformations();
        }
    }

    public static function cron10()
    {
        foreach (eqLogic::byType('baro') as $baro) {
            if ($baro->getIsEnable()) {
                log::add(__CLASS__, 'debug', '================= CRON 10 =================');
                $baro->getInformations();
            }
        }
    }

    public static function cron15()
    {
        foreach (eqLogic::byType('baro') as $baro) {
            if ($baro->getIsEnable()) {
                log::add(__CLASS__, 'debug', '================= CRON 15 =================');
                $baro->getInformations();
            }
        }
    }

    public static function cron30()
    {
        //no both cron15 and cron30 enabled:
        if (config::byKey('functionality::cron15::enable', 'baro', 0) == 1) {
            config::save('functionality::cron30::enable', 0, 'baro');
            return;
        }
        foreach (eqLogic::byType('baro') as $baro) {
            if ($baro->getIsEnable()) {
                log::add(__CLASS__, 'debug', '========================== CRON 30 ==========================');
                $baro->getInformations();
            }
        }
    }

    public static function cronHourly()
    {
        foreach (eqLogic::byType('baro') as $baro) {
            if ($baro->getIsEnable()) {
                log::add('baro', 'debug', '================= CRON HEURE =================');
                $baro->getInformations();
            }
        }
    }

    // Template
    public static function templateWidget()
    {
        return baro_Template::getTemplate();
    }
    public function AddCommand($Name, $_logicalId, $Type = 'info', $SubType = 'binary', $Template = null, $unite = null, $generic_type = null, $IsVisible = 1, $icon = 'default', $forceLineB = 'default', $valuemin = 'default', $valuemax = 'default', $_order = null, $IsHistorized = '0', $repeatevent = false, $_iconname = null, $_calculValueOffset = null, $_historizeRound = null, $_noiconname = null)
    {

        $Command = $this->getCmd(null, $_logicalId);
        if (!is_object($Command)) {
            log::add('baro', 'debug', '│ ' . __('Création Commande', __FILE__) . ' : ' . $Name . ' ── ' . __('Type / SubType', __FILE__) . ' : '  . $Type . '/' . $SubType . ' ── LogicalID : ' . $_logicalId . ' ── Template Widget / ' . __('Ligne', __FILE__)      . ' : ' . $Template . '/' . $forceLineB . ' ── ' . __('Type de générique', __FILE__) . ' : ' . $generic_type . ' ── ' . __('Icône', __FILE__)  . ' : ' . $icon . ' ── ' . __('Min/Max', __FILE__) . ' : ' . $valuemin . '/' . $valuemax . ' ── ' . __('Calcul', __FILE__) . ' / ' . __('Arrondi', __FILE__) . ' : ' . $_calculValueOffset . '/' . $_historizeRound . ' ── ' . __('Ordre', __FILE__) . $_order);
            $Command = new baroCmd();
            $Command->setId(null);
            $Command->setLogicalId($_logicalId);
            $Command->setEqLogic_id($this->getId());
            $Command->setName($Name);
            $Command->setType($Type);
            $Command->setSubType($SubType);
            if ($SubType == 'numeric') {
                if ($unite != null) {
                    $Command->setUnite($unite);
                }
                if ($valuemin != 'default') {
                    $Command->setconfiguration('minValue', $valuemin);
                }
                if ($valuemax != 'default') {
                    $Command->setconfiguration('maxValue', $valuemax);
                }
            }

            if ($Template != null) {
                $Command->setTemplate('dashboard', $Template);
                $Command->setTemplate('mobile', $Template);
            }

            $Command->setIsVisible($IsVisible);
            $Command->setIsHistorized($IsHistorized);

            if ($icon != 'default') {
                $Command->setdisplay('icon', '<i class="' . $icon . '"></i>');
            }
            if ($forceLineB != 'default') {
                $Command->setdisplay('forceReturnLineBefore', 1);
            }
            if ($_iconname != 'default') {
                $Command->setdisplay('showIconAndNamedashboard', 1);
            }
            if ($_noiconname != null) {
                $Command->setdisplay('showNameOndashboard', 0);
            }

            if ($_calculValueOffset != null) {
                $Command->setConfiguration('calculValueOffset', $_calculValueOffset);
            }

            if ($_historizeRound != null) {
                $Command->setConfiguration('historizeRound', $_historizeRound);
            }
            if ($generic_type != null) {
                $Command->setGeneric_type($generic_type);
            }

            if ($repeatevent == true && $Type == 'info') {
                $Command->setconfiguration('repeatEventManagement', 'never');
            }

            if ($_order != null) {
                $Command->setOrder($_order);
            }

            $Command->save();
        }

        $createRefreshCmd = true;
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = cmd::byEqLogicIdCmdName($this->getId(), __('Rafraichir', __FILE__));
            if (is_object($refresh)) {
                $createRefreshCmd = false;
            }
        }
        if ($createRefreshCmd) {
            if (!is_object($refresh)) {
                $refresh = new baroCmd();
                $refresh->setLogicalId('refresh');
                $refresh->setIsVisible(1);
                $refresh->setName(__('Rafraichir', __FILE__));
            }
            $refresh->setType('action');
            $refresh->setSubType('other');
            $refresh->setEqLogic_id($this->getId());
            $refresh->save();
        }
        return $Command;
    }

    /*     * *********************Methode d'instance************************* */
    public function refresh()
    {
        foreach ($this->getCmd() as $cmd) {
            $s = print_r($cmd, 1);
            log::add('baro', 'debug', 'refresh  cmd: ' . $s);
            $cmd->execute();
        }
    }
    public function preInsert() {}

    public function postInsert() {}

    public function preSave() {}

    public function preUpdate()
    {
        if (!$this->getIsEnable()) return;

        if ($this->getConfiguration('pression') == '') {
            log::add('baro', 'error', (__('La valeur :', __FILE__)) . ' ' . __('Pression Atmosphérique', __FILE__) . ' (' . $cmdvirt->getName() .  ')' . ' ' . __('pour l\'équipement', __FILE__) . ' [' . $this->getName() . '] ' . __('ne peut être vide', __FILE__));
            throw new Exception((__('La valeur :', __FILE__)) . ' ' . __('Pression Atmosphérique', __FILE__) . ' (' . $cmdvirt->getName() .  ')' . ' ' . __('pour l\'équipement', __FILE__) . ' [' . $this->getName() . '] ' . __('ne peut être vide', __FILE__));
        }
    }

    public function postSave()
    {
        //$_eqName = $this->getName();
        //log::add('baro', 'debug', 'Sauvegarde de l\'équipement [postSave()] : ' . $_eqName);
        $order = 1;
        $templatecore_V4  = 'core::';

        $td_num_max = 5;
        $td_num_visible = 1;
        $td_num = 1;
        $template_td = $templatecore_V4 . 'tile';
        $template_td_num = 'baro::tendance';
        $name_td = (__('Tendance', __FILE__));
        $name_td_num = (__('Tendance numérique', __FILE__));
        $_iconname_td = 1;
        $_iconname_td_num = 1;
        $dPdT_name = 'dPdT';
        $pressure_name =  (__('Pression Atmosphérique', __FILE__));

        $this->AddCommand($dPdT_name, 'dPdT', 'info', 'numeric', $templatecore_V4 . 'line', 'hPa/h', 'GENERIC_INFO', '0', 'default', 'default', 'default', 'default', $order, '0', true, 'default', null, 2, null);
        $this->AddCommand($pressure_name, 'pressure', 'info', 'numeric', $templatecore_V4 . 'line', 'hPa', 'WEATHER_PRESSURE', '0', 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        $this->AddCommand($name_td, 'td', 'info', 'string', $template_td, null, 'WEATHER_CONDITION', $td_num, 'default', 'default', 'default', 'default', $order++, '0', true, $_iconname_td, null, null, null);
        $this->AddCommand($name_td_num, 'td_num', 'info', 'numeric', $template_td_num, null, 'GENERIC_INFO', $td_num_visible, 'default', 'default', '0', $td_num_max, $order++, '0', true, $_iconname_td_num, null, null, null);

        if (!$this->getIsEnable()) return;
        $this->getInformations();
    }

    /*     * **********************Getteur Setteur*************************** */
    public function postUpdate() {}

    public function getInformations()
    {
        if (!$this->getIsEnable()) return;
        $_eqName = $this->getName();
        log::add('baro', 'debug', '┌── :fg-success:' . __('Mise à jour', __FILE__) . ' ::/fg: '  . $_eqName . ' ──');

        /*  ********************** Calcul *************************** */
        $calcul = 'tendance';

        /*  ********************** PRESSION *************************** => VALABLE AUSSI POUR LE PLUGIN BARO/ROSEE*/
        $pressure = $this->getConfiguration('pression');
        $pressureID = str_replace("#", "", $this->getConfiguration('pression'));
        $cmdvirt = cmd::byId($pressureID);
        if (is_object($cmdvirt)) {
            $pressure = $cmdvirt->execCmd();
            $pressureHISTO = $cmdvirt->getIsHistorized($pressureID);
            if ($pressure === '') {
                log::add('baro', 'error', (__('La valeur :', __FILE__)) . ' ' . (__('Pression Atmosphérique', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                throw new Exception((__('La valeur :', __FILE__)) . ' ' . (__('Pression Atmosphérique', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
            } else {
                $log_msg = (__('L\'historique de la commande', __FILE__)) . ' ';
                $log_msg .= $cmdvirt->getName();
                $log_msg .= ' ' . (__('doit être activé', __FILE__));
                if ($pressureHISTO != 1) {
                    log::add('baro', 'debug', '| ───▶︎ [ALERT] ' . $log_msg . ' : ' . $pressureHISTO);
                    message::add('Plugin Baro', $_eqName . ' : ' . $log_msg);
                } else {
                    log::add('baro', 'debug', '| ───▶︎ ' . __('Pression Atmosphérique', __FILE__) . ' (' . $cmdvirt->getName() . ') : ' . $pressure . ' hPa');
                    log::add('baro', 'debug', '|  └───▶︎ :fg-success:' . __('L\'historique de la commande', __FILE__) . ':/fg: ' . $cmdvirt->getName() . ':fg-success: ' . __('est bien activé', __FILE__) . ':/fg:');
                }
            }
        } else {
            log::add('baro', 'error', (__('Configuration :', __FILE__)) . ' ' . (__('Le champ PRESSION ATMOSPHÉRIQUE', __FILE__))  . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName() . ']');
            throw new Exception(__((__('Le champ PRESSION ATMOSPHÉRIQUE', __FILE__)) . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName(), __FILE__) . ']');
        }
        log::add('baro', 'debug', '└──');

        /*  ********************** Calcul de la tendance *************************** => VALABLE AUSSI POUR LE PLUGIN BARO/ROSEE*/
        if ($calcul == 'tendance') {
            log::add('baro', 'debug', '┌── :fg-warning:' . __('Calcul de la tendance', __FILE__) . ' ::/fg: '  . $_eqName . ' ──');
            $va_result_T = baro::getTendance($pressureID);
            $td_num = $va_result_T[0];
            $td = $va_result_T[1];
            $dPdT = $va_result_T[2];
            log::add('baro', 'debug', '└──');
        }

        /*  ********************** Mise à Jour des équipements *************************** */
        log::add('baro', 'debug', '┌── :fg-info:' . __('Mise à jour', __FILE__)  . ' ::/fg: '  . $_eqName . ' ──');

        $Equipement = eqlogic::byId($this->getId());
        if (is_object($Equipement) && $Equipement->getIsEnable()) {
            $list = 'dPdT,pressure,td,td_num';
            $Value_calcul = array('dPdT' => $dPdT, 'pressure' => $pressure, 'td' => $td, 'td_num' => $td_num);
            if ($td == 'Pression atmosphérique nulle (historique)') { // Non mise à jour des valeurs si problème dans l'historique
                $list = 'pressure';
                $Value_calcul = array('pressure' => $pressure);
                log::add('baro', 'debug', '| :fg-warning:───▶︎ ' . __('Problème avec l\'historique de la pression atmosphérique', __FILE__) . ' ::/fg: ' . __('Non mise à jour de la tendance et de la tendance numérique', __FILE__));
            }
            $fields = explode(',', $list);
            foreach ($this->getCmd() as $cmd) {
                foreach ($fields as $fieldname) {
                    if ($cmd->getLogicalId('data') == $fieldname) {
                        $this->checkAndUpdateCmd($fieldname, $Value_calcul[$fieldname]);
                        log::add('baro', 'debug', '| :fg-info:───▶︎ ' . $cmd->getName() . ' ::/fg: ' . $Value_calcul[$fieldname]);
                    }
                }
            }
        }
        log::add('baro', 'debug', '└──');
        log::add('baro', 'debug', '================ ' . __('FIN CRON OU SAUVEGARDE', __FILE__) . ' =================');
        return;
    }
    /*  ********************** Calcul de la tendance *************************** => VALABLE AUSSI POUR LE PLUGIN BARO/ROSEE*/
    public static function getTendance($pressureID)
    {
        $histo = new scenarioExpression();
        $endDate = $histo->collectDate($pressureID);

        // calcul du timestamp actuel
        $_date1 = new DateTime("$endDate");
        $_date2 = new DateTime("$endDate");
        $startDate = $_date1->modify('-15 minute');
        $startDate = $_date1->format('Y-m-d H:i:s');
        // Valeur nulle 
        $td_moy = 100;
        $dPdT = number_format($td_moy, 3, '.', '');
        $td_num = number_format(5);
        $td = 'Pression atmosphérique nulle (historique)';

        // dernière mesure barométrique
        $h1 = $histo->lastBetween($pressureID, $startDate, $endDate);
        if ($h1 != '') {
            $log_msg_h =  '-15min';
            log::add('baro', 'debug', '| ───▶︎ Timestamp ' . $log_msg_h  . ' : Start/End Date : ' . $startDate . '/' . $endDate . ' - ' . __('Pression Atmosphérique)', __FILE__)  . ' : ' . $h1 . ' hPa');

            // calcul du timestamp - 2h
            $endDate = $_date2->modify('-2 hour');
            $endDate = $_date2->format('Y-m-d H:i:s');
            $startDate = $_date1->modify('-2 hour');
            $startDate = $_date1->format('Y-m-d H:i:s');

            // mesure barométrique -2h
            $h2 = $histo->lastBetween($pressureID, $startDate, $endDate);

            // calculs de tendance 15min/2h
            if ($h2 != null) {
                $td2h = ($h1 - $h2) / 2;
                $log_msg_h =  ' -2h';
                $log_msg = __('Tendance', __FILE__) . $log_msg_h .  ' : ' . $td2h . ' hPa/h';
                log::add('baro', 'debug', '| ───▶︎ Timestamp ' . $log_msg_h  . ' : Start/End Date : ' . $startDate . '/' . $endDate . ' - ' . __('Pression Atmosphérique)', __FILE__)  . ' : ' . $h2 . ' hPa - ' . $log_msg);
                // calcul du timestamp - 4h
                $endDate = $_date2->modify('-2 hour');
                $endDate = $_date2->format('Y-m-d H:i:s');
                $startDate = $_date1->modify('-2 hour');
                $startDate = $_date1->format('Y-m-d H:i:s');

                // mesure barométrique -4h
                $h4 = $histo->lastBetween($pressureID, $startDate, $endDate);

                // calculs de tendance 2h/4h
                if ($h4 != null) {
                    $td4h = (($h1 - $h4) / 4);
                    $log_msg_h =  ' -4h';
                    $log_msg = __('Tendance', __FILE__) . ' -4h : ' . $td4h . ' hPa/h';
                    log::add('baro', 'debug', '| ───▶︎ Timestamp ' . $log_msg_h  . ' : Start/End Date : ' . $startDate . '/' . $endDate . ' - ' . __('Pression Atmosphérique)', __FILE__)  . ' : ' . $h4 . ' hPa - ' . $log_msg);

                    // Calcul de la tendance
                    // sources : http://www.freescale.com/files/sensors/doc/app_note/AN3914.pdf
                    // et : https://www.parallax.com/sites/default/files/downloads/29124-Altimeter-Application-Note-501.pdf
                    $td_moy = (0.5 * $td2h + 0.5 * $td4h);
                    $dPdT = number_format($td_moy, 3, '.', '');

                    log::add('baro', 'debug', '| ───▶︎ Tendance Moyenne (dPdT): ' . $dPdT . ' hPa/h');
                    if ($td_moy > 2.5) { // Quickly rising High Pressure System, not stable
                        $td = (__('Forte embellie, instable', __FILE__));
                        $td_num = number_format(5);
                    } elseif ($td_moy > 0.5 && $td_moy <= 2.5) { // Slowly rising High Pressure System, stable good weather
                        $td = (__('Amélioration, beau temps durable', __FILE__));
                        $td_num = number_format(4);
                    } elseif ($td_moy > 0.0 && $td_moy <= 0.5) { // Stable weather condition
                        $td = (__('Lente amélioration, temps stable', __FILE__));
                        $td_num = number_format(3);
                    } elseif ($td_moy > -0.5 && $td_moy <= 0) { // Stable weather condition
                        $td = (__('Lente dégradation, temps stable', __FILE__));
                        $td_num = number_format(2);
                    } elseif ($td_moy > -2.5 && $td_moy <= -0.5) { // Slowly falling Low Pressure System, stable rainy weather
                        $td = (__('Dégradation, mauvais temps durable', __FILE__));
                        $td_num = number_format(1);
                    } else { // Quickly falling Low Pressure, Thunderstorm, not stable
                        $td = (__('Forte dégradation, instable', __FILE__));
                        $td_num = 0;
                    };
                } else {
                    $td4h = 0;
                    log::add('baro', 'debug', '| ───▶︎ [ALERT] ' . __('Pression Atmosphérique', __FILE__) . ' -4h ' . __('nulle (historique)', __FILE__)  . '  : ' . $h4 . ' hPa');
                }
            } else {
                $td2h = 0;
                log::add('baro', 'debug', '| ───▶︎ [ALERT] ' . __('Pression Atmosphérique', __FILE__) . ' -2h ' . __('nulle (historique)', __FILE__)  . '  : ' . $h2 . ' hPa');
            }
        } else {
            log::add('baro', 'debug', '| ───▶︎ [ALERT] ' . __('Pression Atmosphérique', __FILE__) . ' 15min ' . __('nulle (historique)', __FILE__)  . '  : ' . $h1 . ' hPa');
        }
        return array($td_num, $td, $dPdT);
    }
}

class BaroCmd extends cmd
{
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
    public function dontRemoveCmd()
    {
        if ($this->getLogicalId() == 'refresh') {
            return true;
        }
        return false;
    }

    public function execute($_options = null)
    {
        if ($this->getLogicalId() == 'refresh') {
            log::add('baro', 'debug', ' ─────────▶︎ ' . (__('Début de l\'actualisation manuelle', __FILE__)));
            $this->getEqLogic()->getInformations();
            log::add('baro', 'debug', ' ─────────▶︎ ' . (__("Fin De l\'actualisation manuelle", __FILE__)));
            return;
        } else {
            log::add('baro', 'debug', '│  [WARNING] ' . __("Pas d'action pour la commande execute",  __FILE__) . ' : ' . $this->getLogicalId());
        }
    }
}
