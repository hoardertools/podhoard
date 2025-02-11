/**
 * @license Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */

( e => {
const { [ 'it' ]: { dictionary, getPluralForm } } = {"it":{"dictionary":{"Open file manager":"Apri il gestore dei file","Cannot determine a category for the uploaded file.":"Impossibile determinare la categoria del file caricato.","Cannot access default workspace.":"Impossibile accedere all'area di lavoro predefinita.","Edit image":"Modifica immagine","Processing the edited image.":"Elaborazione dell'immagine modificata.","Server failed to process the image.":"Il server non è riuscito a elaborare l'immagine.","Failed to determine category of edited image.":"Impossibile determinare la categoria dell'immagine modificata."},getPluralForm(n){return n == 1 ? 0 : n != 0 && n % 1000000 == 0 ? 1 : 2;}}};
e[ 'it' ] ||= { dictionary: {}, getPluralForm: null };
e[ 'it' ].dictionary = Object.assign( e[ 'it' ].dictionary, dictionary );
e[ 'it' ].getPluralForm = getPluralForm;
} )( window.CKEDITOR_TRANSLATIONS ||= {} );
