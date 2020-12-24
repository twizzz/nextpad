/**
 * Nextcloud - Nextpad
 *
 * This file is licensed under the Affero General Public License
 * version 3 or later. See the COPYING file.
 *
 * @author Olivier Tétard <olivier.tetard@miskin.fr>
 * @copyright Olivier Tétard <olivier.tetard@miskin.fr>, 2017
 */

(function(OCA) {
    OCA.FilesNextpad = {
        attach: function(fileList) {
            if (OCA.Theming) {
                OC.MimeType._mimeTypeIcons['application/x-nextpad'] = OC.generateUrl('/apps/theming/img/nextpad/etherpad-lite.ico?v=' + OCA.Theming.cacheBuster);
            } else {
                OC.MimeType._mimeTypeIcons['application/x-nextpad'] = OC.imagePath('nextpad', 'etherpad-lite.ico');
            }
            this._extendFileActions(fileList.fileActions);
        },

        hide: function() {
            $('#nextpad').remove();
            FileList.setViewerMode(false);

            // replace the controls with our own
            $('#app-content #controls').removeClass('hidden');
        },

        show: function(fileName, dirName) {
            var self = this;
            var $iframe;

            var viewer = OC.generateUrl('/apps/nextpad/?file={file}&dir={dir}', {file: fileName, dir: dirName});

            $iframe = $('<iframe id="nextpad" style="width:100%;height:100%;display:block;position:absolute;top:0;z-index:999;" src="'+viewer+'"/>');

            FileList.setViewerMode(true);

            $('#app-content').append($iframe);
            $("#pageWidthOption").attr("selected","selected");
            $('#app-content #controls').addClass('hidden');

            $('#nextpad').load(function(){
                var iframe = $('#nextpad').contents();
                if ($('#fileList').length) {
                    iframe.find('#nextpad_close').click(function() {
                        self.hide();
                    });
                } else {
                    iframe.find("#nextpad_close").addClass('hidden');
                }
            });
        },

        _extendFileActions: function(fileActions) {
            var self = this;
            fileActions.registerAction({
                name: 'view',
                displayName: 'Nextpad',
                mime: 'application/x-nextpad',
                permissions: OC.PERMISSION_READ,
                actionHandler: function(fileName, context) {
                    self.show(fileName, context.dir);
                }
            });
            fileActions.setDefault('application/x-nextpad', 'view');
        }
    };
})(OCA);

OC.Plugins.register('OCA.Files.FileList', OCA.FilesNextpad);

(function(OCA) {

    var FilesNextpadMenu = function() {
        this.initialize();
    }

    FilesNextpadMenu.prototype = {

        _etherpadEnabled: false,
        _etherpadPublicEnabled: false,
        _etherpadAPIEnabled: false,
        _ethercalcEnabled: false,

        initialize: function() {
            var self = this;

            if(OC.getCurrentUser().uid !== null) {
                $.ajax({
                    url: OC.generateUrl('/apps/nextpad/ajax/v1.0/getconfig')
                }).done(function(result) {
                    self._etherpadEnabled = result.data.nextpad_etherpad_enable === "yes";
                    self._etherpadPublicEnabled = result.data.nextpad_etherpad_public_enable === "yes";
                    self._etherpadAPIEnabled = result.data.nextpad_etherpad_useapi === "yes";
                    self._ethercalcEnabled = result.data.nextpad_ethercalc_enable === "yes";
                    OC.Plugins.register('OCA.Files.NewFileMenu', self);
                });
            }
        },


        attach: function(newFileMenu) {
            var self = this;

            if(self._etherpadEnabled === true) {
                if (self._etherpadPublicEnabled === true || self._etherpadAPIEnabled === false) {
                    newFileMenu.addMenuEntry({
                        id: 'etherpad',
                        displayName: t('nextpad', 'Pad'),
                        templateName: t('nextpad', 'New pad.pad'),
                        iconClass: 'icon-filetype-etherpad',
                        fileType: 'etherpad',
                        actionHandler: function (filename) {
                            self._createPad("etherpad", filename);
                        }
                    });
                }

                if(self._etherpadAPIEnabled === true) {
                    var displayName = self._etherpadPublicEnabled === true ? 'Protected Pad' : 'Pad';
                    var templateName = self._etherpadPublicEnabled === true ? 'New protected pad.pad' : 'New pad.pad';
                    newFileMenu.addMenuEntry({
                        id: 'etherpad-api',
                        displayName: t('nextpad', displayName),
                        templateName: t('nextpad', templateName),
                        iconClass: 'icon-filetype-etherpad',
                        fileType: 'etherpad',
                        actionHandler: function(filename) {
                            self._createPad("etherpad", filename, true);
                        }
                    });
                }
            }

            if(self._ethercalcEnabled === true) {
                newFileMenu.addMenuEntry({
                    id: 'ethercalc',
                    displayName: t('nextpad', 'Calc'),
                    templateName: t('nextpad', 'New calc.calc'),
                    iconClass: 'icon-filetype-ethercalc',
                    fileType: 'ethercalc',
                    actionHandler: function(filename) {
                        self._createPad("ethercalc", filename);
                    }
                });
            }
        },

        _createPad: function(type, filename, is_protected) {
            // Default value for `is_protected`.
            var is_protected = typeof is_protected !== 'undefined' ? is_protected : false;

            var self = this;

            OCA.Files.Files.isFileNameValid(filename);
            filename = FileList.getUniqueName(filename);

            $.post(
                OC.generateUrl('/apps/nextpad/ajax/v1.0/newpad'), {
                    dir: $('#dir').val(),
                    padname: filename,
                    type: type,
                    protected: is_protected
                },
                function(result) {
                    if(result.status == 'success') {
                        FileList.add(result.data, {animate: true, scrollTo: true});
                    }
                    else {
                        OC.dialogs.alert(result.data.message, t('core', 'Could not create file'));
                    }
                }
            );
        }
    };

    // Only initialize the Nextpad menu when user is logged in and
    // using the “files” app.
    $(document).ready(function() {
        if($('#filesApp').val()) {
            OCA.FilesNextpadMenu = new FilesNextpadMenu();
        }
    });
})(OCA);