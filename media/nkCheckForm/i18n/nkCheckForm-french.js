
/* French initialisation for the jQuery UI date picker plugin. */
/* Written by Keith Wood (kbwood{at}iinet.com.au),
			  Stéphane Nahmani (sholby@sholby.net)
			  Stéphane Raimbault <stephane.raimbault@gmail.com> */


(function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define([ "../nkCheckFormSettings" ], factory );
	} else {

		// Browser globals
		factory( jQuery.nkCheckFormSettings );
	}
}(function( nkCheckFormSettings ) {

	$.nkCheckFormSettings.regional['fr_FR'] = {
        emptyField: 'Ce champ est vide',
        emailInvalid: 'L\'adresse email n\'est pas valide',
        onlyAlphaNumeric: 'Uniquement des caracteres alphanumerique',
        onlyInteger: 'Uniquement des chiffres',
        minlengthSize: ' caracteres minimum',
        maxlengthSize: ' caracteres maximum',
        differentPassword: 'Les mots de passe ne sont pas identiques',
        badUsername : 'Mauvais caracteres',
        usernameTooLong: 'Pseudo trop long',
        usernameAlreadyUsed: 'Pseudo déjà utilisé',
        usernameBanned: 'Pseudo banni',
  
        passwordWeak: 'Weak',
        passwordMedium: 'Medium',
        passwordHigh: 'High',
        passwordCheck: 'Password\'s Security',
  
  
        badRequiredField: "Vous avez oublié de remplir ou mal remplit un champ requis du formulaire !",
        badOptionalField: "Vous avez oublié de remplir ou mal remplit un champ optionel du formulaire !",
        fieldErased: "Les champs vont etre effacées",
        noSimilarPassword: 'Les deux mots de passes saisis ne sont pas identiques',
		freeNick: 'Pseudo libre',
		nicknameUsed: 'Pseudo déjà utilisé',
		
	}

	$.nkCheckFormSettings.setDefaults($.nkCheckFormSettings.regional['fr_FR']);


	return $.nkCheckFormSettings.regional['fr_FR'];

}));
