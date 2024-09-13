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
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>

<script>
    setTimeout(function() {
        /* 
        hide configuration panel
        */
        let modal = jeeDialog.get('#div_confPlugin', 'dialog')
        let dom_container = null
        if (modal != null) {
            dom_container = modal.querySelector('#div_confPlugin')
        } else {
            dom_container = document.getElementById('div_pageContainer').querySelector('#div_confPlugin')
        }
        dom_container.querySelector('#div_plugin_configuration').closest('.panel').style.display = 'none'

        /*
        Allow only one cron
        */
        document.getElementById('div_plugin_functionality').querySelectorAll('input.configKey').forEach(
            _checkbox => {
                _checkbox.addEventListener('change', function(event) {
                    if (_checkbox.checked) {
                        document.querySelectorAll('#div_plugin_functionality input.configKey').forEach(_filter => {
                            if (_checkbox.getAttribute('data-l1key') != _filter.getAttribute('data-l1key')) _filter.checked = false
                        })
                    }
                })
            })
    }, 100)
</script>