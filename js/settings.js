/**
 * Nextcloud - Nextpad
 *
 * This file is licensed under the Affero General Public License
 * version 3 or later. See the COPYING file.
 *
 * @author Olivier Tétard <olivier.tetard@miskin.fr>
 * @copyright Olivier Tétard <olivier.tetard@miskin.fr>, 2017
 */

(function (window, document, $) {
    'use strict';

    $(document).ready(function() {
        var savedMessage = $('#nextpad-saved-message');

        var saved = function () {
            if (savedMessage.is(':visible')) {
                savedMessage.hide();
            }

            savedMessage.fadeIn(function () {
                setTimeout(function () {
                    savedMessage.fadeOut();
                }, 5000);
            });
        };

        $('#nextpad_settings input').change(function() {
            var value = $(this).val();

            if($(this).attr('type') === 'checkbox') {
                if (this.checked) {
                    value = 'yes';
                } else {
                    value = 'no';
                }
            }

            OC.AppConfig.setValue('nextpad', $(this).attr('name'), value);
            saved();
        });

        $('#nextpad_etherpad_enable').change(function() {
            $("#nextpad_etherpad_settings").toggleClass('hidden', !this.checked);

            if(this.checked && $("#nextpad_etherpad_useapi").is(":checked")) {
                $("#nextpad_etherpad_useapi_settings").removeClass('hidden');
            }
            else {
                $("#nextpad_etherpad_useapi_settings").addClass('hidden');
            }

        });

        $('#nextpad_etherpad_useapi').change(function() {
            $("#nextpad_etherpad_useapi_settings").toggleClass('hidden', !this.checked);
        });

        $('#nextpad_ethercalc_enable').change(function() {
            $("#nextpad_ethercalc_settings").toggleClass('hidden', !this.checked);
        });
    });

}(window, document, jQuery));
