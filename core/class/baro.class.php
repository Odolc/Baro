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

class baro extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    public static function cron15() {
        foreach (eqLogic::byType('baro') as $baro) {
            log::add('baro', 'debug', '========================== CRON 15 ==========================');
			$baro->getInformations();
		}

    }

    public static function cron30($_eqlogic_id = null) {
		//no both cron15 and cron30 enabled:
		if (config::byKey('functionality::cron15::enable', 'baro', 0) == 1)
		{
			config::save('functionality::cron30::enable', 0, 'baro');
			return;
		}
		foreach (eqLogic::byType('rosee') as $rosee) {
			if ($rosee->getIsEnable()) {
				log::add('rosee', 'debug', '========================== CRON 30 ==========================');
				$rosee->getInformations();
			}
		}
	}

    /*     * *********************Methode d'instance************************* */
    public function refresh() {
        foreach ($this->getCmd() as $cmd)
        {
            $s = print_r($cmd, 1);
            log::add('baro', 'debug', 'refresh  cmd: '.$s);
            $cmd->execute();
        }
    }

    public function preUpdate() {
    	if ($this->getConfiguration('pression') == '') {
    		throw new Exception(__('Le champ pression ne peut etre vide',__FILE__));
		}
    }

     public function postInsert() {
    	// Ajout d'une commande dans le tableau pour le dP/dT
            $BaroCmd = new BaroCmd();
            $BaroCmd->setName(__('dP/dT', __FILE__));
            $BaroCmd->setEqLogic_id($this->id);
            $BaroCmd->setLogicalId('dPdT');
            $BaroCmd->setConfiguration('data', 'dPdT');
            $BaroCmd->setType('info');
            $BaroCmd->setSubType('numeric');
            $BaroCmd->setUnite('hPa/h');
            $BaroCmd->setIsHistorized(0);
            $BaroCmd->setIsVisible(0);
            $BaroCmd->setDisplay('generic_type','GENERIC_INFO');
            $BaroCmd->save();

        // Ajout d'une commande dans le tableau pour la pression
            $BaroCmd = new BaroCmd();
            $BaroCmd->setName(__('Pression', __FILE__));
            $BaroCmd->setEqLogic_id($this->id);
            $BaroCmd->setLogicalId('pression');
            $BaroCmd->setConfiguration('data', 'pression');
            $BaroCmd->setType('info');
            $BaroCmd->setSubType('numeric');
            $BaroCmd->setUnite('hPa');
            $BaroCmd->setIsHistorized(0);
            $BaroCmd->setIsVisible(0);
            $BaroCmd->setDisplay('generic_type','WEATHER_PRESSURE');
            $BaroCmd->save();

		// Ajout d'une commande dans le tableau pour la tendance numérique
            $BaroCmd = new BaroCmd();
            $BaroCmd->setName(__('Tendance numerique', __FILE__));
            $BaroCmd->setEqLogic_id($this->id);
            $BaroCmd->setLogicalId('tendance_num');
            $BaroCmd->setConfiguration('data', 'tendance_num');
            $BaroCmd->setType('info');
            $BaroCmd->setSubType('numeric');
            $BaroCmd->setUnite('');
            $BaroCmd->setIsHistorized(0);
            $BaroCmd->setIsVisible(1);
            $BaroCmd->setDisplay('generic_type','GENERIC_INFO');
            $BaroCmd->save();

        // Ajout d'une commande dans le tableau pour la tendance
            $BaroCmd = new BaroCmd();
            $BaroCmd->setName(__('Tendance', __FILE__));
            $BaroCmd->setEqLogic_id($this->id);
            $BaroCmd->setLogicalId('tendance');
            $BaroCmd->setConfiguration('data', 'tendance');
            $BaroCmd->setType('info');
            $BaroCmd->setSubType('string');
            $BaroCmd->setUnite('');
            $BaroCmd->setIsHistorized(0);
            $BaroCmd->setIsVisible(1);
            $BaroCmd->setDisplay('generic_type','WEATHER_CONDITION');
            $BaroCmd->save();
        }

    public function postSave() {
        $refresh = $this->getCmd(null, 'refresh');
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

    /*     * **********************Getteur Setteur*************************** */
    public function postUpdate() {
        foreach (eqLogic::byType('baro') as $baro) {
            $baro->getInformations();
        }
    }

    public function getInformations() {
        if (!$this->getIsEnable()) return;
        $_eqName = $this->getName();
        log::add('baro', 'debug', '┌───────── CONFIGURATION EQUIPEMENT : '.$_eqName );
        /*  ********************** PRESSION *************************** */
            $idvirt = str_replace("#","",$this->getConfiguration('pression'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $pression = $cmdvirt->execCmd();
                log::add('baro', 'debug', '│ Pression Atmosphérique : ' . $pression.' hPa');
            } else {
                log::add('baro', 'error', 'Configuration : pression non existante : ' . $this->getConfiguration('pression'));
            }
        log::add('baro', 'debug', '└─────────');

        log::add('baro', 'debug', '┌───────── CALCUL Timestamp : '.$_eqName); // récupération du timestamp de la dernière mesure
        $histo = new scenarioExpression();
        $endDate = $histo -> collectDate($idvirt);
	// calcul du timestamp actuel
        log::add('baro', 'debug', '│ ┌─────── Timestamp -15min : ' .$_eqName);
        $_date1 = new DateTime("$endDate");
        $_date2 = new DateTime("$endDate");
        $startDate = $_date1 -> modify('-15 minute');
        $startDate = $_date1 -> format('Y-m-d H:i:s');
        log::add('baro', 'debug', '│ │ Start Date -15min : ' .$startDate );
        log::add('baro', 'debug', '│ │ End Date -15min : ' .$endDate );
	// dernière mesure barométrique
        $h1 = $histo->lastBetween($idvirt, $startDate, $endDate);
        log::add('baro', 'debug', '│ │ Pression Atmosphérique -15min : ' .$h1 . ' hPa' );
        log::add('baro', 'debug', '│ └───────');

	// calcul du timestamp - 2h
        log::add('baro', 'debug', '│ ┌─────── Timestamp -2h : ' .$_eqName);
        $endDate = $_date2 -> modify('-2 hour');
        $endDate = $_date2 -> format('Y-m-d H:i:s');
        $startDate = $_date1 -> modify('-2 hour');
        $startDate = $_date1 -> format('Y-m-d H:i:s');
        log::add('baro', 'debug', '│ │ Start Date -2h : ' .$startDate );
        log::add('baro', 'debug', '│ │ End Date -2h : ' .$endDate );
	// mesure barométrique -2h
        $h2 = $histo->lastBetween($idvirt, $startDate, $endDate);
        log::add('baro', 'debug', '│ │ Pression Atmosphérique -2h : ' .$h2 . ' hPa' );
    // calculs de tendance
        $tendance2h = ($h1 - $h2) / 2;
        log::add('baro', 'debug', '│ │ Tendance -2h : ' . $tendance2h . ' hPa/h' );
        log::add('baro', 'debug', '│ └───────');

	// calcul du timestamp - 4h
        log::add('baro', 'debug', '│ ┌─────── Timestamp -4h : ' .$_eqName);
        $endDate = $_date2 -> modify('-2 hour');
        $endDate = $_date2 -> format('Y-m-d H:i:s');
        $startDate = $_date1 -> modify('-2 hour');
        $startDate = $_date1 -> format('Y-m-d H:i:s');
        log::add('baro', 'debug', '│ │ Start Date -4h : ' .$startDate );
        log::add('baro', 'debug', '│ │ End Date -4h : ' .$endDate );
	// mesure barométrique -4h
        $h4 = $histo->lastBetween($idvirt, $startDate, $endDate);
        log::add('baro', 'debug', '│ │ Pression Atmosphérique -4h : ' .$h4 . ' hPa' );
    // calculs de tendance
        $tendance4h = ($h1 - $h4) / 4;
        log::add('baro', 'debug', '│ │ Tendance -4h : ' . $tendance4h . ' hPa/h' );
        log::add('baro', 'debug', '│ └───────');
        log::add('baro', 'debug', '└─────────');

	// calculs de tendance
        log::add('baro', 'debug', '┌───────── CALCUL TENDANCE : '.$_eqName);
	// sources : http://www.freescale.com/files/sensors/doc/app_note/AN3914.pdf
    // et : https://www.parallax.com/sites/default/files/downloads/29124-Altimeter-Application-Note-501.pdf

	// moyennation de la tendance à -2h (50%) et -4h (50%)
        $tendance = (0.5 * $tendance2h + 0.5 * $tendance4h);
        $tendance_format = number_format($tendance, 3, '.', '');
        log::add('baro', 'debug', '│ Tendance Moyenne : ' . $tendance . ' hPa/h' );

        if ($tendance > 2.5) { // Quickly rising High Pressure System, not stable
            $td = 'Forte embellie, instable';
            $td_num=5;
        } elseif ($tendance > 0.5) { // Slowly rising High Pressure System, stable good weather
            $td = 'Amélioration, beau temps durable';
            $td_num=4;
        } elseif ($tendance > 0.0) { // Stable weather condition
            $td = 'Lente amélioration, temps stable';
            $td_num=3;
        } elseif ($tendance > -0.5) { // Stable weather condition
            $td = 'Lente dégradation, temps stable';
            $td_num=2;
        } elseif ($tendance > -2.5) { // Slowly falling Low Pressure System, stable rainy weather
            $td = 'Dégradation, mauvais temps durable';
            $td_num=1;
        } else { // Quickly falling Low Pressure, Thunderstorm, not stable
            $td = 'Forte dégradation, instable';
            $td_num=0;
        }
        log::add('baro', 'debug', '│ Tendance : ' .  $td . '' );
        log::add('baro', 'debug', '│ Tendance numérique : ' .  $td_num . '' );
        log::add('baro', 'debug', '└─────────');

    /*  ********************** Mise à Jour des équipements *************************** */
        log::add('baro', 'debug', '┌───────── MISE A JOUR : '.$_eqName);

    $cmd = $this->getCmd('info', 'pression');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $pression);
			$cmd->save();
			$cmd->setCollectDate('');
            $cmd->event($pression);
                log::add('baro', 'debug', '│ Pression : ' . $pression. ' hPa');
		}

    $cmd = $this->getCmd('info', 'dPdT');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $tendance_format);
			$cmd->save();
			$cmd->setCollectDate('');
            $cmd->event($tendance_format);
                log::add('baro', 'debug', '│ dPdT : ' . $tendance_format. ' hPa/h');
		}

    $cmd = $this->getCmd('info', 'tendance');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $td);
			$cmd->save();
			$cmd->setCollectDate('');
            $cmd->event($td);
                log::add('baro', 'debug', '│ Tendance : ' . $td);
		}

    $cmd = $this->getCmd('info', 'tendance_num');
		if (is_object($cmd)) {
			$cmd->setConfiguration('value', $td_num);
			$cmd->save();
			$cmd->setCollectDate('');
            $cmd->event($td_num);
                log::add('baro', 'debug', '│ Tendance Numérique : ' . $td_num);
		}
        log::add('baro', 'debug', '└─────────');
        log::add('baro', 'debug', '================ FIN CRON =================');

        return ;
    }
}

class BaroCmd extends cmd {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
	public function dontRemoveCmd() {
        return true;
    }

	public function execute($_options = null) {
		if ($this->getLogicalId() == 'refresh') {
			$this->getEqLogic()->getInformations();
			return;
		}
	}
}