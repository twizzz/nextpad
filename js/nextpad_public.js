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

        async loadPublic({ downloadUrl } = {}) {
            const result = await window.fetch(downloadUrl, {
                method: "GET",
                headers: {
                    requesttoken: window.OC.requestToken,
                },
            });
            if (result && result.ok) {
                return { filecontents: await result.text() };
            }
        },

        async show(fileName, context) {
            var self = this;
            var $iframe;

            var content = await this.loadPublic({ downloadUrl: context.fileList.getDownloadUrl(fileName) });
            console.log(content);
            var viewer = OC.generateUrl('/apps/nextpad/ajax/v1.0/display?file={file}&content={content}', {file: fileName, content: content.filecontents});
            console.log(viewer);

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
                actionHandler: async (fileName, context) => {
                    await self.show(fileName, context);
                }
            });
            fileActions.setDefault('application/x-nextpad', 'view');
        }
    };
})(OCA);

OC.Plugins.register('OCA.Files.FileList', OCA.FilesNextpad);