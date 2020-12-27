/**
 * Nextcloud - Nextpad
 *
 * This file is licensed under the Affero General Public License
 * version 3 or later. See the COPYING file.
 *
 * @author Haliax <info@haliax.dev>
 * @copyright Haliax <info@haliax.dev>, 2020
 */

function npSpinnerShow(fileName)
{
    $('footer').after('<div class="oc-dialog icon-loading" style="position: fixed;"><div class="urledit push-bottom"><h3>'+ fileName +'</h3> </div> <div class="oc-dialog-buttonrow twobuttons"><a class="button">'+ t('nextpad', 'Button Cancel') +'</a></div></div>');
    $('footer').after('<div class="oc-dialog-dim"></div>');
    $('div.oc-dialog.icon-loading').click(function ()
    {
        $('div.oc-dialog.icon-loading').off('click').remove();
        $('div.oc-dialog-dim').remove();
    });
}

function npSpinnerDone(fileName, url)
{
    // Remove the old dialog
    $('div.oc-dialog.icon-loading').off('click').remove();

    $('footer').after('<div class="oc-dialog" style="position: fixed;"></div>');
    let dialog = $('.oc-dialog');

    dialog.append('<div class="urledit push-bottom"><h3>'+ t('nextpad', 'Text Header') +'</h3><p class="urldisplay">'+ t('nextpad', 'Text Redirect') + ' <em>' + fileName + '</em></p></div>');
    dialog.append('<div class="oc-dialog-buttonrow twobuttons"><a href="#" class="button">'+ t('nextpad', 'Button Cancel') +'</a><a href="'+ url +'" target="_blank" class="button primary">'+ t('nextpad', 'Button Redirect') +'</a></div>')

    $('div.oc-dialog').click(function ()
    {
        $('div.oc-dialog').off('click').remove();
        $('div.oc-dialog-dim').remove();
    });
}

function npSpinnerPermission(fileName)
{
    // Remove the old dialog
    $('div.oc-dialog.icon-loading').off('click').remove();

    $('footer').after('<div class="oc-dialog" style="position: fixed;"></div>');
    let dialog = $('.oc-dialog');

    dialog.append('<div class="urledit push-bottom"><h3>'+ t('nextpad', 'Text Header') +'</h3><p class="urldisplay">'+ t('nextpad', 'Text Permission') + '</p></div>');
    dialog.append('<div class="oc-dialog-buttonrow twobuttons"><a href="#" class="button">'+ t('nextpad', 'Button Cancel') +'</div>')

    $('div.oc-dialog').click(function ()
    {
        $('div.oc-dialog').off('click').remove();
        $('div.oc-dialog-dim').remove();
    });
}


(function(OCA) {

    const getFileExtension = function (fileName) {
        return fileName.substr(fileName.lastIndexOf(".") + 1).toLowerCase();
    }

    async function loadPublic({ downloadUrl } = {}) {
        const result = await window.fetch(downloadUrl, {
            method: "GET",
            headers: {
                requesttoken: window.OC.requestToken,
            },
        });
        if (result && result.ok) {
            return { filecontents: await result.text() };
        }
    }

    function parseUrl(fileContent) {

        const urlLines = fileContent.match("URL=.*");
        if (urlLines && Array.isArray(urlLines) && urlLines.length > 0) {
            let url = urlLines[0];
            return url.replace("URL=", "");
        }
        return '';
    }

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

        async show(fileName, downloadUrl) {
            var self = this;
            var $iframe;

            // Is the Pad protected?
            const parsed = parseUrl(downloadUrl);
            const isProtected = ~parsed.indexOf("/g.");

            if (!window.OC.currentUser && isProtected)
            {
                npSpinnerPermission(fileName);
                return;
            }

            // Prepare the Iframe.
            var viewer = OC.generateUrl('/apps/nextpad/ajax/v1.0/display?file={file}&content={content}', {file: fileName, content: downloadUrl});
            $iframe = $('<iframe id="nextpad" style="width:100%;height:100%;display:block;position:absolute;top:0;z-index:999;" src="'+viewer+'"/>');

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
                    let downloadUrl = await loadPublic({ downloadUrl: context.fileList.getDownloadUrl(fileName) });
                    await self.show(fileName, downloadUrl.filecontents);
                }
            });
            fileActions.setDefault('application/x-nextpad', 'view');
        }
    };

    document.addEventListener( 'DOMContentLoaded',  async function () {

        if ($("#isPublic").val() === "1" && !$("#filestable").length) {
            const directDownload = document.querySelectorAll(".directDownload");
            if (directDownload && directDownload.length > 0) {
                // Get the filename
                const fileName = (document.querySelector("input#filename") || {value: ""}).value;
                let dirName = (document.querySelector("input#dir") || {value: ""}).value;
                if (dirName === '') dirName = '/';

                // Get extension
                var extension = getFileExtension(fileName);
                // Public download page, single file
                if (extension === "pad") {

                    // Get the download URL
                    npSpinnerShow(fileName);
                    const downloadUrl = (document.querySelector("input#downloadURL") || {value: ""}).value;

                    // Hide Stuff.
                    $('a').attr('href', '#');
                    $('#files-public-content').hide();

                    // Fetch the url.
                    let url = await loadPublic({downloadUrl: downloadUrl});

                    // Match for URL line.
                    url = parseUrl(url.filecontents);
                    const isProtected = ~url.indexOf("/g.");

                    if (!window.OC.currentUser && isProtected)
                    {
                        npSpinnerPermission(fileName);
                        return;
                    }

                    if (url !== '') {
                        // Redirect.
                        window.location = url;

                        // Hide Stuff.
                        $('a').attr('href', url);
                        $('#files-public-content').show();
                        $('#downloadFile')[0].lastChild.nodeValue = " Open the Pad";
                        npSpinnerDone(fileName, url);
                    }
                }
            }
        }
    });

})(OCA);

OC.Plugins.register('OCA.Files.FileList', OCA.FilesNextpad);
