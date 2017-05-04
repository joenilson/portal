/*
 * Copyright (C) 2016 Joe Nilson <joenilson at gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/*
window.addEventListener('load', function() {
    var editor;
    ContentTools.StylePalette.add([
        new ContentTools.Style('Author', 'author', ['p'])
    ]);
    editor = ContentTools.EditorApp.get();
    editor.init('*[data-editable]', 'data-name', fixtureTest=null, withIgnition=false);

    xhr = new XMLHttpRequest();
    xhr.open('GET', 'plugins/portal/view/js/ContentTools/translations/es.json', true);
    function onStateChange (ev) {
        var translations;
        if (ev.target.readyState === 4) {
            // Convert the JSON data to a native Object
            translations = JSON.parse(ev.target.responseText);

            // Add the translations for the French language
            ContentEdit.addTranslations('es', translations);

            // Set French as the editors current language
            ContentEdit.LANGUAGE = 'es';
        }
    }

    xhr.addEventListener('readystatechange', onStateChange);

    // Load the language
    xhr.send(null);

    editor.start();
    editor.addEventListener('saved', function (ev) {
        var name, payload, regions, xhr;

        // Check that something changed
        regions = ev.detail().regions;
        if (Object.keys(regions).length == 0) {
            return;
        }

        // Set the editor as busy while we save our changes
        this.busy(true);

        // Collect the contents of each region into a FormData instance
        payload = new FormData();
        for (name in regions) {
            if (regions.hasOwnProperty(name)) {
                payload.append(name, regions[name]);
            }
        }
        payload.append('page_title',document.form_basic.page_title.value);
        payload.append('page_name',document.form_basic.page_name.value);
        payload.append('type','update-info');

        // Send the update content to the server to be saved
        function onStateChange(ev) {
            // Check if the request is finished
            if (ev.target.readyState == 4) {
                editor.busy(false);
                if (ev.target.status == '200') {
                    // Save was successful, notify the user with a flash
                    //new ContentTools.FlashUI('ok');
                    alert('Información Guardada exitosamente');
                    window.location.assign(url_portal_admin);
                } else {
                    // Save failed, notify the user with a flash
                    //new ContentTools.FlashUI('no');
                    alert('Error al guardar la información');
                    window.location.assign(url_portal_admin);
                }
            }
        };

        xhr = new XMLHttpRequest();
        xhr.addEventListener('readystatechange', onStateChange);
        xhr.open('POST', url_portal_admin);
        xhr.send(payload);
    });

    $("#btn-cancel-form").on("click",function(){
        editor.stop();
        editor.start();
    });

    $("#btn-save-form").on("click",function(){
        editor.stop(true);
    });
});
*/