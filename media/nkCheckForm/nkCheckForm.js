
// Inspired of :
// http://stefangabos.ro/jquery/jquery-plugin-boilerplate-revisited/
// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

(function($) {

    function nkCheckFormSettings() {
        this.regional = [];
        this.regional[""] = {
            emptyField: 'This field is empty',
            emailInvalid: 'Email adress must be valid',
            onlyAlphaNumeric: 'Only alphanumeric character',
            onlyNumeric: 'Only numeric character',
            minlengthSize: ' minimum character',
            //maxlengthSize: ' maximum character',

            oldPasswordEmpty: 'Old password is empty',
            passwordEmpty : 'Password is empty',
            passwordConfirmEmpty : 'Password confirmation is empty',


            differentPassword: 'Passwords are not identical',
            badUsername : 'Bad Character',
            usernameTooLong: 'Username too long',
            usernameAlreadyUsed: 'Username already used',
            usernameBanned: 'Banned username',

            badDateFormat: 'Date incorrect',
 
 passwordWeak: 'Faible',
 passwordMedium: 'Moyen',
 passwordHigh: 'Elevé',
 passwordCheck: 'Niveau de sécurité du mot de passe',
 
            badRequiredField: "Forgot to complete or improperly completes a form required field !",
            badOptionalField: "Forgot to complete or improperly completes a form optional field !",
            fieldErased: "This field will be erased",
            noSimilarPassword: 'The two entered passwords are not identical',
            freeUsername: 'Free username',
            usernameUsed: 'Username already used',
            
        };
        this._defaults = {
            locale: 'en_GB',
            errorInputClass: 'error-field',
            warningInputClass: 'warning-field',
        };
        this.setDefaults = function(options) {
            $.extend(this._defaults, options);
        }
        $.extend(this._defaults, this.regional[""]);
        this.regional["en_GB"] = $.extend(true, {}, this.regional[""]);
    }

    $.nkCheckFormSettings = new nkCheckFormSettings(); // singleton instance

    // here we go!
    $.nkCheckForm = function(element, options) {
        // to avoid confusions, use "plugin" to reference the 
        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('nkCheckForm').settings.propertyName from outside the plugin, 
        // where "element" is the element the plugin is attached to;
        plugin.settings = {};

        //plugin.items = {};

        var $element = $(element),  // reference to the jQuery version of DOM element
        element = element;          // reference to the actual DOM element

        var submitButton = $element.find(':submit');

        // the "constructor" method that gets called when the object is created
        plugin.init = function() {
            // the plugin's final properties are the merged default and 
            // user-provided options (if any)
            plugin.settings = $.extend({}, $.nkCheckFormSettings._defaults, options);

            if (typeof plugin.settings.input != 'object') {
                console.log('Input settings must be a object or empty');
                return;
            }

            // Add class of checked input
            for (var id in plugin.settings.input) {
                if (! _setInputDefaultSettings(id))
                    return;

                // Add class to checked input
                $('#' + id).addClass('nkCheckform');

                if (plugin.settings.input[id].type == 'username')
                    _initUsernameInput(id);

                if (plugin.settings.input[id].type == 'password')
                    _initPasswordInput(id);

                // TODO POUR LES AJOUT DE PASSWORD???
                // Check input and disabled submit button for no valid form
                if (submitButton.attr('disabled') !== 'disabled' && ! plugin.settings.input[id].optional && ! _checkFormInput(id, true))
                    submitButton.attr('disabled', 'disabled');
            };

            // Check input when focus has lost
            $('.nkCheckform').blur(function() {
                //console.log(this.id);
                _checkInputById(this.id);
            });

            // Check input when a key is released
            $('.nkCheckform').keyup(function() {
                //console.log(this.id);
                _checkInputById(this.id);
            });

            // Check submited form
            $element.submit(function() {
                return _checkAllInput();
            });
        }

        var _setInputDefaultSettings = function(id) {
            // Check if input id exist
            if (! $('#' + id).length) {
                console.log('Input id ' + id + ' no found');
                return false;
            }

            // Check no empty settings values
            if (plugin.settings.input[id].noempty === undefined) {
                plugin.settings.input[id].noempty = false;

            } else if (typeof plugin.settings.input[id].noempty != 'boolean') {
                console.log('Input noempty settings must be a boolean');
                return false;
            }

            // Check optional settings values
            if (plugin.settings.input[id].optional === undefined) {
                plugin.settings.input[id].optional = false;

            } else if (typeof plugin.settings.input[id].optional != 'boolean') {
                console.log('Input optional settings must be a boolean');
                return false;
            }

            return true;
        }

        var _initUsernameInput = function(id) {
            // Save old username
            if (plugin.settings.input[id].oldUsername === true)
                plugin.settings.input[id].oldUsername = $('#' + id).val();
        }

        var _initPasswordInput = function(id) {
            var minlength;//, maxlength;

            minlength = (typeof plugin.settings.input[id].minlength == 'number') ?
                plugin.settings.input[id].minlength : undefined;

            //maxlength =(typeof plugin.settings.input[id].maxlength == 'number') ?
            //    plugin.settings.input[id].maxlength : undefined;

            if (typeof plugin.settings.input[id].passwordConfirmId == 'string'
                && $('#' + plugin.settings.input[id].passwordConfirmId).length) {
                plugin.settings.input[plugin.settings.input[id].passwordConfirmId] = {
                    type: 'passwordConfirm',
                    passwordConfirmId: id,
                    oldPasswordId: plugin.settings.input[id].oldPasswordId,
                    noempty: plugin.settings.input[id].noempty,
                    minlength: minlength,
                    //maxlength:maxlength,
                    optional: plugin.settings.input[id].optional
                };

                $('#' + plugin.settings.input[id].passwordConfirmId).addClass('nkCheckform');

            } else {
                plugin.settings.input[id].passwordConfirmId = undefined;
            }

            if (typeof plugin.settings.input[id].oldPasswordId == 'string'
                && $('#' + plugin.settings.input[id].oldPasswordId).length) {
                plugin.settings.input[plugin.settings.input[id].oldPasswordId] = {
                    type: 'oldPassword',
                    passwordId: id,
                    passwordConfirmId: plugin.settings.input[id].passwordConfirmId,
                    noempty: plugin.settings.input[id].noempty,
                    minlength: minlength,
                    //maxlength:maxlength,
                    optional: plugin.settings.input[id].optional
                };

                $('#' + plugin.settings.input[id].oldPasswordId).addClass('nkCheckform');

            } else {
                plugin.settings.input[id].oldPasswordId = undefined;
            }

            if (plugin.settings.input[id].passwordCheck === true) {
                _addCheckPassword(id);
                _checkSecurityPassword(id, $('#' + id).val());

                $('#' + id).blur(function() { _checkSecurityPassword(this.id, $('#' + this.id).val()) });
                $('#' + id).keyup(function() { _checkSecurityPassword(this.id, $('#' + this.id).val()) });
            }
        }

        //plugin.settings.passwordCheck _PASSCHECK
        //plugin.settings.passwordWeak _PASSWEAK
        //plugin.settings.passwordMedium _PASSMEDIUM
        //plugin.settings.passwordHigh _PASSHIGH

        var _addCheckPassword = function(id) {
            var labelWidth = $('#' + id + '_container>label').outerWidth();

            $('<div id="password_check_container">' + plugin.settings.passwordCheck + '<br />'
                + '<ul><li id="weak" class="nrm">' + plugin.settings.passwordWeak + '</li>'
                + '<li id="medium" class="nrm">' + plugin.settings.passwordMedium + '</li>'
                + '<li id="strong" class="nrm">' + plugin.settings.passwordHigh + '</li></ul></div>')
                    .insertAfter('#' + id + '_container');

            $('#password_check_container').css({"margin-left": labelWidth});
        }

        var _checkSecurityPassword = function(id, passwordValue) {
            var counter = 0;

            console.log(passwordValue);

            if (passwordValue.length >= 4) counter = counter + 1;
 
            if (passwordValue.match(/[A-Z]/)) counter = counter + 1;
            if (passwordValue.match(/[A-Z]{2}/)) counter = counter + 2;

            if (passwordValue.match(/[a-z]/)) counter = counter + 1;
            if (passwordValue.match(/[a-z]{2}/)) counter = counter + 2;

            if (passwordValue.match(/[0-9]/)) counter = counter + 1;
            if (passwordValue.match(/[0-9]{2}/)) counter = counter + 2;

            if (passwordValue.match(/[^A-Za-z0-9]/)) counter = counter + 1;
            if (passwordValue.match(/[^A-Za-z0-9]{2}/)) counter = counter + 2;

            $('#password_check_container>ul>li#weak').removeClass('yellow green red');
            $('#password_check_container>ul>li#medium').removeClass('yellow green red');
            $('#password_check_container>ul>li#strong').removeClass('yellow green red');

            if (counter <= 6) {
                $('#password_check_container>ul>li#weak').addClass('red');
            } else if (counter > 6 && counter < 12) {
                $('#password_check_container>ul>li#weak').addClass('yellow');
                $('#password_check_container>ul>li#medium').addClass('yellow');
            } else if (counter >= 12) {
                $('#password_check_container>ul>li#weak').addClass('green');
                $('#password_check_container>ul>li#medium').addClass('green');
                $('#password_check_container>ul>li#strong').addClass('green');
            }
        }

        /*
         * Check all input in settings list 
         * Enabled submit button if form is valid
         *
         * @param void
         * @return bool : Return true if all field is valid, false also
         */
        var _checkAllInput = function() {
            var submit = true;

            for (var id in plugin.settings.input) {
                if (! _checkFormInput(id, false) && ! plugin.settings.input[id].optional)
                    submit = submit && false;
            };

            if (submit)
                submitButton.removeAttr('disabled');
            else
                submitButton.attr('disabled', 'disabled');

            return submit;
        }

        var _checkInputById = function(id) {
            var check = _checkFormInput(id, false);
            
            if ($element.find('.error-field').length == 0)
                submitButton.removeAttr('disabled');
            else
                submitButton.attr('disabled', 'disabled');
        }

        /*
         * Add css class to invalid input
         *
         * @param string id : The input id
         * @param bool optional : If the field is optional or not
         * @return void
         */
        var _invalidInput = function(id) {
            if (plugin.settings.input[id].optional)
                $('#' + id).addClass(plugin.settings.warningInputClass);
            else
                $('#' + id).addClass(plugin.settings.errorInputClass);
        }

        /*
         * Remove css class to valid input
         *
         * @param string id : The input id
         * @param bool optional : If the field is optional or not
         * @return void
         */
        var _inputValid = function(id) {
            if (plugin.settings.input[id].optional)
                $('#' + id).removeClass(plugin.settings.warningInputClass);
            else
                $('#' + id).removeClass(plugin.settings.errorInputClass);
        }

        /*
         * Display input error message and hide it by fading
         * 
         * @param string id : The input id
         * @param string error : The error message
         * @return void
         */
        var _displayError = function(id, error) {
            var offset = $('#' + id).offset();

            $('<div id="formError-contenair-' + id + '" class="formError-contenair"><div>' + error + '</div></div>')
                .insertAfter('#' + id);

            $('#formError-contenair-' + id)
                .css({top: parseInt(offset.top), left: parseInt(offset.left) + $('#' + id).outerWidth() + 10});

            $('#formError-contenair-' + id + ">div").fadeOut(2000, function() {
                $('#formError-contenair-' + id).remove();
            });
        }

        /*
         * Check form input, main process
         * 
         * @param object inputSettings : The settings of input
         * @param bool checkOnly : If the input must be only check or display error message and add error class
         * @return bool : Return true if input is valid, false also
         */
        var _checkFormInput = function(id, checkOnly = false) {
            plugin.settings.input[id].value = $.trim($('#' + id).val())

            // Empty optional input is valid
            if (plugin.settings.input[id].optional && plugin.settings.input[id].value == '')
                return true;

            // Check input by type of value
            switch (plugin.settings.input[id].type) {
                case 'text':
                    break;

                case 'alpha':
                    plugin.settings.input[id].regex = /^[a-zA-Z]+$/;
                    break;

                case 'alphanumeric':
                    plugin.settings.input[id].regex = /^[a-zA-Z\d]+$/;
                    break;

                case 'numeric':
                    plugin.settings.input[id].regex = /^\d+$/;
                    break;

                case 'email':
                    plugin.settings.input[id].regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
                    break;

                case 'date':
                    return _checkInputDate(id, checkOnly);
                    break;

                case 'password':
                case 'passwordConfirm':
                    return _checkInputPassword(id, checkOnly);
                    break;

                case 'oldPassword':
                    return _checkInputOldPassword(id, checkOnly);
                    break;

                case 'username':
                    plugin.settings.input[id].regex = /[^\$\^\(\)'"?%#<>,;:]/
                    return _checkInputUsername(id, checkOnly);
                    break;

                default:
                    console.log('Unknow Input type ' + plugin.settings.input[id].type);
                    return false;
            }

            return _checkInputText(id, checkOnly);
        }


        var _getRegexError = function(type) {
            if (type == 'email')
                return plugin.settings.emailInvalid;

            else if (type == 'alpha')
                return plugin.settings.onlyAlpha;// TODO A FAIRE

            else if (type == 'alphanumeric')
                return plugin.settings.onlyAlphaNumeric;

            else if (type == 'numeric')
                return plugin.settings.onlyNumeric;
            else
                console.log('Unknow regex error of type ' + type);
        }

        /*
         * Check generic input value
         * 
         * @param object inputSettings : The settings of input
         * @param bool checkOnly : If the input must be only check or display error message and add error class
         * @return bool : Return true if input is valid, false also
         */
        var _checkInputText = function(id, checkOnly) {
            var check = false, error;

            // Check if not empty
            if (plugin.settings.input[id].noempty && plugin.settings.input[id].value.length == 0) {
                error = plugin.settings.emptyField;

            // Check by regex
            } else if (plugin.settings.input[id].regex !== undefined && ! plugin.settings.input[id].value.match(plugin.settings.input[id].regex)) {
                error = _getRegexError(plugin.settings.input[id].type);

            // Check minimum length
            } else if (typeof plugin.settings.input[id].minlength == 'number' && plugin.settings.input[id].value.length < plugin.settings.input[id].minlength) {
                error = plugin.settings.input[id].minlength + plugin.settings.minlengthSize;

            // Check maximum length
            //} else if (typeof plugin.settings.input[id].maxlength == 'number' && plugin.settings.input[id].value.length > plugin.settings.input[id].maxlength) {
            //    error = plugin.settings.input[id].maxlength + plugin.settings.maxlengthSize;

            } else {
                check = true;
            }

            // Add css class to invalid input and diplay error message if needed
            if (! checkOnly) {
                if (error !== undefined)
                    _displayError(id, error);

                if (check)
                    _inputValid(id);
                else
                    _invalidInput(id);
            }
            console.log(check);
            return check
        }

        var _checkDate = function(day, month, year) {
            var dt = new Date();
            dt.setFullYear(parseInt(year));
            dt.setMonth(parseInt(month) - 1);
            dt.setDate(parseInt(day))

            day = dt.getDate();
            month = dt.getMonth() + 1;
            year = dt.getFullYear();

            return new Array(day, month, year);
        }

        var _checkInputDate = function(id, checkOnly) {
            var day, month, year, validDate, check = false;

            if (typeof $.datepicker == 'object') {
                // fr-FR
                // en-GB
                if ($.datepicker._defaults.dateFormat == 'dd/mm/yy') {
                    var dateData = plugin.settings.input[id].value.split('/');

                    if (dateData.length == 3) {
                        console.log('dateData : ' + dateData);
                        dateData = _checkDate(dateData[0], dateData[1], dateData[2]);
                        console.log('_checkDate : ' + dateData);
                        validDate = ('0' + dateData[0]).slice(-2) + '/' + 
                        ('0' + dateData[1]).slice(-2) + 
                        '/' + dateData[2];
                    }

                // en-US
                } else if ($.datepicker._defaults.dateFormat == 'mm/dd/yy') {
                    var dateData = plugin.settings.input[id].value.split('/');

                    if (dateData.length == 3) {
                        dateData = _checkDate(dateData[1], dateData[0], dateData[2]);
                        validDate = ('0' + dateData[1]).slice(-2) + '/' + ('0' + dateData[0]).slice(-2) + '/' + dateData[2];
                    }
                }
            }

            console.log('value : ' + plugin.settings.input[id].value);
            console.log('validDate : ' + validDate);
            
            if (typeof validDate == 'string' && plugin.settings.input[id].value == validDate)
                check = true;

            // Add css class to invalid input and diplay error message if needed
            if (! checkOnly) {
                if (! check)
                    _displayError(id, plugin.settings.badDateFormat + ' ' + $.datepicker._defaults.dateFormat);

                if (check)
                    _inputValid(id);
                else
                    _invalidInput(id);
            }

            return check;
        }
        
        
        
        
        var _checkInputPassword = function(id, checkOnly) {
            if (plugin.settings.input[id].passwordConfirmId !== undefined)
                plugin.settings.input[id].confirmValue = $.trim($('#' + plugin.settings.input[id].passwordConfirmId).val());

            passwordAndComfirmDifferentAndNotEmpty = plugin.settings.input[id].value != plugin.settings.input[id].confirmValue &&
                (plugin.settings.input[id].value != '' || plugin.settings.input[id].confirmValue != '');

            // Check old password if defined
            if (plugin.settings.input[id].oldPasswordId !== undefined) {
                var oldPasswordValue = $.trim($('#' + plugin.settings.input[id].oldPasswordId).val());

                if (oldPasswordValue == '') {
                    // Password, confirmation password and old password are empty
                    if (plugin.settings.input[id].value == '' && plugin.settings.input[id].confirmValue == '') {
                        if (! checkOnly) {
                            _inputValid(id);
                            _inputValid(plugin.settings.input[id].passwordConfirmId);
                            _inputValid(plugin.settings.input[id].oldPasswordId);
                        }

                        return true;

                    // Passwords are not identical and old password is empty
                    } else if (passwordAndComfirmDifferentAndNotEmpty) {
                        if (! checkOnly) {
                            _invalidInput(id);
                            _invalidInput(plugin.settings.input[id].passwordConfirmId);
                            _invalidInput(plugin.settings.input[id].oldPasswordId);
                            _displayError(id, plugin.settings.differentPassword);
                        }

                        return false;

                    // Old password is empty
                    } else {
                        if (! checkOnly) {
                            _inputValid(id);
                            _inputValid(plugin.settings.input[id].passwordConfirmId);
                            _invalidInput(plugin.settings.input[id].oldPasswordId);
                            _displayError(plugin.settings.input[id].oldPasswordId, plugin.settings.oldPasswordEmpty);
                        }

                        return false;
                    }
                }

                // Passwords are not identical and old password is empty
                if (passwordAndComfirmDifferentAndNotEmpty) {
                    if (! checkOnly) {
                        _invalidInput(id);
                        _invalidInput(plugin.settings.input[id].passwordConfirmId);
                        _invalidInput(plugin.settings.input[id].oldPasswordId);
                        _displayError(id, plugin.settings.differentPassword);
                    }

                    return false;
                }
            }

            // Passwords are not identical and old password is empty
            if (passwordAndComfirmDifferentAndNotEmpty) {
                if (! checkOnly) {
                    _invalidInput(id);
                    _invalidInput(plugin.settings.input[id].passwordConfirmId);
                    _displayError(id, plugin.settings.differentPassword);
                }

                return false;
            }

            // Check password value with generic input check process
            var check = _checkInputText(id, checkOnly);

            if (! checkOnly) {
                if (check) {
                    _inputValid(plugin.settings.input[id].passwordConfirmId);
                    if (plugin.settings.input[id].oldPasswordId !== undefined)
                        _inputValid(plugin.settings.input[id].oldPasswordId);

                } else {
                    _invalidInput(plugin.settings.input[id].passwordConfirmId);
                    if (plugin.settings.input[id].oldPasswordId !== undefined)
                        _invalidInput(plugin.settings.input[id].oldPasswordId);
                }
            }

            return check;
        }

        var _checkInputOldPassword = function(id, checkOnly) {
            console.log('_checkInputOldPassword');
            
            if (plugin.settings.input[id].passwordConfirmId !== undefined)
                plugin.settings.input[id].confirmValue = $.trim($('#' + plugin.settings.input[id].passwordConfirmId).val());

            if (plugin.settings.input[id].passwordId !== undefined)
                plugin.settings.input[id].passwordValue = $.trim($('#' + plugin.settings.input[id].passwordId).val());

            passwordAndComfirmDifferentAndNotEmpty = plugin.settings.input[id].passwordValue != plugin.settings.input[id].confirmValue &&
                (plugin.settings.input[id].passwordValue != '' || plugin.settings.input[id].confirmValue != '');

            console.log('_checkInputOldPassword id :' + id);
            console.log('_checkInputOldPassword passwordConfirmId :' + plugin.settings.input[id].passwordConfirmId);
            console.log('_checkInputOldPassword id :' + id);

            if (plugin.settings.input[id].value == '') {
                // Password, confirmation password and old password are empty
                if (plugin.settings.input[id].value == '' && plugin.settings.input[id].confirmValue == '') {
                    if (! checkOnly) {
                        _inputValid(id);
                        _inputValid(plugin.settings.input[id].passwordId);
                        _inputValid(plugin.settings.input[id].passwordConfirmId);
                    }

                    return true;

                // Passwords are not identical when old password is empty
                } else if (passwordAndComfirmDifferentAndNotEmpty) {
                    if (! checkOnly) {
                        _invalidInput(id);
                        _invalidInput(plugin.settings.input[id].passwordId);
                        _invalidInput(plugin.settings.input[id].passwordConfirmId);
                        _displayError(plugin.settings.input[id].passwordId, plugin.settings.differentPassword);
                    }

                    return false;

                } else {
                    if (! checkOnly) {
                        _invalidInput(id);
                        _inputValid(plugin.settings.input[id].passwordId);
                        _inputValid(plugin.settings.input[id].passwordConfirmId);
                        _displayError(id, plugin.settings.oldPasswordEmpty);
                    }

                    return false;
                }

            } else {
                if (passwordAndComfirmDifferentAndNotEmpty) {
                    if (! checkOnly) {
                        _invalidInput(id);
                        _invalidInput(plugin.settings.input[id].passwordId);
                        _invalidInput(plugin.settings.input[id].passwordConfirmId);
                        _displayError(plugin.settings.input[id].passwordId, plugin.settings.differentPassword);
                    }

                    return false;

                } else if (plugin.settings.input[id].passwordValue == '') {
                    if (! checkOnly) {
                        _inputValid(id);
                        _invalidInput(plugin.settings.input[id].passwordId);
                        _invalidInput(plugin.settings.input[id].passwordConfirmId);
                        _displayError(plugin.settings.input[id].passwordId, plugin.settings.passwordEmpty);
                    }

                    return false;

                } else if (plugin.settings.input[id].confirmValue == '') {
                    if (! checkOnly) {
                        _inputValid(id);
                        _invalidInput(plugin.settings.input[id].passwordId);
                        _invalidInput(plugin.settings.input[id].passwordConfirmId);
                        _displayError(plugin.settings.input[id].passwordId, plugin.settings.passwordConfirmEmpty);
                    }

                    return false;
                }
            }

            var check = _checkInputText(id, checkOnly);

            if (check) {
                if (! checkOnly) {
                    _inputValid(id);
                    _inputValid(plugin.settings.input[id].passwordId);
                    _inputValid(plugin.settings.input[id].passwordConfirmId);
                }

                return true;
            }

            _invalidInput(id);

            return false;
        }

        /*
         * Check username value returning by ajax request
         * 
         * @param object inputSettings : The settings of input
         * @param string msg : The returned message by ajax request
         * @return void
         */
        var _checkUsername = function(id, msg) {
            switch (msg) {
                case 'good-username':
                    _inputValid(id);
                    return;
                    break;

                case 'bad-username':
                    _displayError(id, plugin.settings.badUsername + ': $^()\'"?%#<>,;:');
                    break;

                case 'username-too-long':
                    _displayError(id, plugin.settings.usernameTooLong);
                    break;

                case 'username-already-used':
                    _displayError(id, plugin.settings.usernameAlreadyUsed);
                    break;

                case 'username-banned':
                    _displayError(id, plugin.settings.usernameBanned);
                    break;

                default:
                    console.log('Unknow username message returned : ' + msg);
                    return;
            }

            _invalidInput(id);
        }

        /*
         * Check input value who contains user name
         * 
         * @param object inputSettings : The settings of input
         * @param bool checkOnly : If the input must be only check or display error message and add error class
         * @return bool : Return true if input is valid, false also
         */
        var _checkInputUsername = function(id, checkOnly) {
            if (!_checkInputText(id, checkOnly))
                return false;

            $.ajax({
                type: 'POST',
                async: false,
                url: 'index.php?file=User&page=checkUsername&withoutDesign=true',
                data: { username: plugin.settings.input[id].value, oldUsername: plugin.settings.input[id].oldUsername },
                success: function(msg) { _checkUsername(id, msg); }
            });

            if ($('#' + id).hasClass(plugin.settings.errorInputClass))
                return false;
            else
                return true;
        }

        // fire up the plugin!
        // call the "constructor" method
        plugin.init();
    }

    // add the plugin to the jQuery.fn object
    $.fn.nkCheckForm = function(options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {
            // if plugin has not already been attached to the element
            if (undefined == $(this).data('nkCheckForm')) {

                // create a new instance of the plugin
                // pass the DOM element and the user-provided options as arguments
                var plugin = new $.nkCheckForm(this, options);

                // in the jQuery version of the element
                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('nkCheckForm').publicMethod(arg1, arg2, ... argn) or
                // element.data('nkCheckForm').settings.propertyName
                $(this).data('nkCheckForm', plugin);
            }
        });
    }

})(jQuery);