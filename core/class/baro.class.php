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

    public static function cron5($_eqlogic_id = null)
    {
        foreach (eqLogic::byType('baro') as $baro) {
            log::add(__CLASS__, 'debug', '========================== CRON 5 ==========================');
            $baro->getInformations();
        }
    }

    public static function cron10($_eqlogic_id = null)
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

    public static function cron30($_eqlogic_id = null)
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
    public function AddCommand($Name, $_logicalId, $Type = 'info', $SubType = 'binary', $Template = null, $unite = null, $generic_type = null, $IsVisible = 1, $icon, $forceLineB = '0', $valuemin = 'default', $valuemax = 'default', $_order = null, $IsHistorized = '0', $repeatevent = false, $_iconname = null, $_calculValueOffset = null, $_historizeRound = null, $_noiconname = null)
    {

        $Command = $this->getCmd(null, $_logicalId);
        if (!is_object($Command)) {
            log::add('rosee', 'debug', '│ Name : ' . $Name . ' -- Type : ' . $Type . ' -- LogicalID : ' . $_logicalId . ' -- Template Widget / Ligne : ' . $Template . '/' . $forceLineB . '-- Type de générique : ' . $generic_type . ' -- Icône : ' . $icon . ' -- Min/Max : ' . $valuemin . '/' . $valuemax . ' -- Calcul/Arrondi: ' . $_calculValueOffset . '/' . $_historizeRound);
            $Command = new roseeCmd();
            $Command->setId(null);
            $Command->setLogicalId($_logicalId);
            $Command->setEqLogic_id($this->getId());
            $Command->setName($Name);

            $Command->setType($Type);
            $Command->setSubType($SubType);

            if ($Template != null) {
                $Command->setTemplate('dashboard', $Template);
                $Command->setTemplate('mobile', $Template);
            }

            if ($unite != null && $SubType == 'numeric') {
                $Command->setUnite($unite);
            }

            $Command->setIsVisible($IsVisible);
            $Command->setIsHistorized($IsHistorized);

            if ($icon != null) {
                $Command->setdisplay('icon', '<i class="' . $icon . '"></i>');
            }
            if ($forceLineB != null) {
                $Command->setdisplay('forceReturnLineBefore', 1);
            }
            if ($_iconname != null) {
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
                log::add(__CLASS__, 'debug', '│ No Repeat pour l\'info avec le nom : ' . $Name);
            }
            if ($valuemin != 'default') {
                $Command->setconfiguration('minValue', $valuemin);
            }
            if ($valuemax != 'default') {
                $Command->setconfiguration('maxValue', $valuemax);
            }

            $Command->save();
        }

        if ($_order != null) {
            $Command->setOrder($_order);
        }

        $Command->save();

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
                $refresh = new roseeCmd();
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
            log::add(__CLASS__, 'debug', 'refresh  cmd: ' . $s);
            $cmd->execute();
        }
    }

    public function preUpdate()
    {
        if (!$this->getIsEnable()) return;

        if ($this->getConfiguration('pression') == '') {
            throw new Exception(__('Le champ pression ne peut etre vide', __FILE__));
        }
    }

    public function postInsert()
    {
    }

    public function postSave()
    {
        $_eqName = $this->getName();
        log::add(__CLASS__, 'debug', 'postSave() =>' . $_eqName);
        $order = 1;

        if (version_compare(jeedom::version(), "4", "<")) {
            $templatecore_V4 = null;
        } else {
            $templatecore_V4  = 'core::';
        };

        $Equipement = eqlogic::byId($this->getId());

        // Ajout d'une commande dans le tableau pour le dP/dT
        $Equipement->AddCommand('dPdT', 'dPdT', 'info', 'numeric', $templatecore_V4 . 'line', 'hPa/h', 'GENERIC_INFO', '0', 'null', 'default', 'default', 'default', $order, '0', true, null, null, 2, null);
        $order++;
        // Ajout d'une commande dans le tableau pour la pression
        $Equipement->AddCommand('Pression', 'pressure', 'info', 'numeric', $templatecore_V4 . 'line', 'hPa', 'WEATHER_PRESSURE', '0', 'null', 'default', 'default', 'default', $order, '0', true, null, null, 2, null);
        $order++;
        // Ajout d'une commande dans le tableau pour la tendance
        $Equipement->AddCommand('Tendance', 'td', 'info', 'string', $templatecore_V4 . 'multiline', null, 'WEATHER_CONDITION', '0', 'null', 'default', 'default', 'default', $order, '0', true, null, null, null, null);
        $order++;
        // Ajout d'une commande dans le tableau pour la tendance numérique
        $Equipement->AddCommand('Tendance numerique', 'td_num', 'info', 'numeric', 'baro::tendance', null, 'GENERIC_INFO', '0', 'null', 'default', '0', 5, $order, '0', true, null, null, null, null);
    }

    /*     * **********************Getteur Setteur*************************** */
    public function postUpdate()
    {
        $this->getInformations();
    }

    public function getInformations()
    {
        if (!$this->getIsEnable()) return;
        $_eqName = $this->getName();
        log::add(__CLASS__, 'debug', '┌───────── CONFIGURATION EQUIPEMENT : ' . $_eqName);
        /*  ********************** PRESSION *************************** */
        $idvirt = str_replace("#", "", $this->getConfiguration('pression'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $pressure = $cmdvirt->execCmd();
            log::add(__CLASS__, 'debug', '│ Pression Atmosphérique : ' . $pressure . ' hPa');
        } else {
            throw new Exception(__('Le champ "Pression Atmosphérique" ne peut être vide', __FILE__));
            log::add(__CLASS__, 'error', '│ Configuration : pression non existante : ' . $this->getConfiguration('pression'));
        }
        log::add(__CLASS__, 'debug', '└─────────');

        log::add(__CLASS__, 'debug', '┌───────── CALCUL Timestamp : ' . $_eqName); // récupération du timestamp de la dernière mesure
        $histo = new scenarioExpression();
        $endDate = $histo->collectDate($idvirt);

        // calcul du timestamp actuel
        log::add(__CLASS__, 'debug', '│ ┌─────── Timestamp -15min : ' . $_eqName);
        $_date1 = new DateTime("$endDate");
        $_date2 = new DateTime("$endDate");
        $startDate = $_date1->modify('-15 minute');
        $startDate = $_date1->format('Y-m-d H:i:s');
        log::add(__CLASS__, 'debug', '│ │ Start Date -15min : ' . $startDate);
        log::add(__CLASS__, 'debug', '│ │ End Date -15min : ' . $endDate);

        // dernière mesure barométrique
        $h1 = $histo->lastBetween($idvirt, $startDate, $endDate);
        log::add(__CLASS__, 'debug', '│ │ Pression Atmosphérique -15min : ' . $h1 . ' hPa');
        log::add(__CLASS__, 'debug', '│ └───────');

        // calcul du timestamp - 2h
        log::add(__CLASS__, 'debug', '│ ┌─────── Timestamp -2h : ' . $_eqName);
        $endDate = $_date2->modify('-2 hour');
        $endDate = $_date2->format('Y-m-d H:i:s');
        $startDate = $_date1->modify('-2 hour');
        $startDate = $_date1->format('Y-m-d H:i:s');
        log::add(__CLASS__, 'debug', '│ │ Start Date -2h : ' . $startDate);
        log::add(__CLASS__, 'debug', '│ │ End Date -2h : ' . $endDate);

        // mesure barométrique -2h
        $h2 = $histo->lastBetween($idvirt, $startDate, $endDate);
        log::add(__CLASS__, 'debug', '│ │ Pression Atmosphérique -2h : ' . $h2 . ' hPa');

        // calculs de tendance 15min/2h
        $td2h = ($h1 - $h2) / 2;
        log::add(__CLASS__, 'debug', '│ │ Tendance -2h : ' . $td2h . ' hPa/h');
        log::add(__CLASS__, 'debug', '│ └───────');

        // calcul du timestamp - 4h
        log::add(__CLASS__, 'debug', '│ ┌─────── Timestamp -4h : ' . $_eqName);
        $endDate = $_date2->modify('-2 hour');
        $endDate = $_date2->format('Y-m-d H:i:s');
        $startDate = $_date1->modify('-2 hour');
        $startDate = $_date1->format('Y-m-d H:i:s');
        log::add(__CLASS__, 'debug', '│ │ Start Date -4h : ' . $startDate);
        log::add(__CLASS__, 'debug', '│ │ End Date -4h : ' . $endDate);

        // mesure barométrique -4h
        $h4 = $histo->lastBetween($idvirt, $startDate, $endDate);
        log::add(__CLASS__, 'debug', '│ │ Pression Atmosphérique -4h : ' . $h4 . ' hPa');

        // calculs de tendance 2h/4h
        $td4h = ($h1 - $h4) / 4;
        log::add(__CLASS__, 'debug', '│ │ Tendance -4h : ' . $td4h . ' hPa/h');
        log::add(__CLASS__, 'debug', '│ └───────');
        log::add(__CLASS__, 'debug', '└─────────');

        // calculs de tendance
        log::add(__CLASS__, 'debug', '┌───────── CALCUL TENDANCE : ' . $_eqName);
        // sources : http://www.freescale.com/files/sensors/doc/app_note/AN3914.pdf
        // et : https://www.parallax.com/sites/default/files/downloads/29124-Altimeter-Application-Note-501.pdf

        // moyennation de la tendance à -2h (50%) et -4h (50%)
        $td_moy = (0.5 * $td2h + 0.5 * $td4h);
        $dPdT = number_format($td_moy, 3, '.', '');
        log::add(__CLASS__, 'debug', '│ Tendance Moyenne (dPdT) : ' . $dPdT . ' hPa/h');

        if ($td_moy > 2.5) { // Quickly rising High Pressure System, not stable
            $td = 'Forte embellie, instable';
            $td_num = 5;
        } elseif ($td_moy > 0.5 && $td_moy <= 2.5) { // Slowly rising High Pressure System, stable good weather
            $td = 'Amélioration, beau temps durable';
            $td_num = 4;
        } elseif ($td_moy > 0.0 && $td_moy <= 0.5) { // Stable weather condition
            $td = 'Lente amélioration, temps stable';
            $td_num = 3;
        } elseif ($td_moy > -0.5 && $td_moy <= 0) { // Stable weather condition
            $td = 'Lente dégradation, temps stable';
            $td_num = 2;
        } elseif ($td_moy > -2.5 && $td_moy <= -0.5) { // Slowly falling Low Pressure System, stable rainy weather
            $td = 'Dégradation, mauvais temps durable';
            $td_num = 1;
        } else { // Quickly falling Low Pressure, Thunderstorm, not stable
            $td = 'Forte dégradation, instable';
            $td_num = 0;
        }
        log::add(__CLASS__, 'debug', '│ Tendance : ' .  $td . '');
        log::add(__CLASS__, 'debug', '│ Tendance numérique : ' .  $td_num . '');
        log::add(__CLASS__, 'debug', '└─────────');

        /*  ********************** Mise à Jour des équipements *************************** */
        log::add(__CLASS__, 'debug', '┌───────── MISE A JOUR : ' . $_eqName);

        $Equipement = eqlogic::byId($this->getId());
        if (is_object($Equipement) && $Equipement->getIsEnable()) {

            foreach ($Equipement->getCmd('info') as $Command) {
                if (is_object($Command)) {
                    switch ($Command->getLogicalId()) {
                        case "dPdT":
                            log::add(__CLASS__, 'debug', '│ dPdT : ' . $dPdT . ' hPa/h');
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $dPdT);
                            break;
                        case "pressure":
                            log::add(__CLASS__, 'debug', '│ Pression : ' . $pressure . ' hPa');
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $pressure);
                            break;
                        case "td":
                            log::add(__CLASS__, 'debug', '│ Tendance : ' . $td);
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $td);
                            break;
                        case "td_num":
                            log::add(__CLASS__, 'debug', '│ Tendance Numérique : ' . $td_num);
                            $Equipement->checkAndUpdateCmd($Command->getLogicalId(), $td_num);
                            break;
                    }
                }
            }
        }
        log::add(__CLASS__, 'debug', '└─────────');
        log::add(__CLASS__, 'debug', '================ FIN CRON =================');
        return;
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
            log::add('baro', 'debug', ' ─────────> ACTUALISATION MANUELLE');
            $this->getEqLogic()->getInformations();
            log::add('baro', 'debug', ' ─────────> FIN ACTUALISATION MANUELLE');
            return;
        }
    }
}
