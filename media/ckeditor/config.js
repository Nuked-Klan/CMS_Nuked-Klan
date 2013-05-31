/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	// Default setting.
	config.toolbar_Full = [
	    { name: 'document', items: ['Source']},
	    { name: 'clipboard', items: ['Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', '-',  'PasteText', 'PasteFromWord']},
	    { name: 'editing', items: ['Find', 'Replace', 'Scayt']},
	    { name: 'others', items: ['Maximize', 'ShowBlocks']},
	    { name: 'about', items: ['About']},
	    '/',
	    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']},
	    { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},
	    { name: 'tools', items: ['Link', 'Unlink', 'Anchor', '-', 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Video', 'Syntaxhighlight']},
	    { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor']}
	];

	config.toolbar_Basic = [
	    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']},
	    { name: 'paragraph', items: ['Blockquote']},
	    { name: 'clipboard', items: ['Undo', 'Redo']},
	    { name: 'editing', items: ['Scayt']},
	    { name: 'tools', items: ['Link', 'Unlink', '-', 'Image', 'Smiley', 'SpecialChar', 'Syntaxhighlight']}
	];

};

