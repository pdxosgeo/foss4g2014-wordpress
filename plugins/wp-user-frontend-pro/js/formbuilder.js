;(function($) {

    var $formEditor = $('ul#wpuf-form-editor');

    var Editor = {
        init: function() {

            // make it sortable
            this.makeSortable();

            this.tooltip();
            this.tabber();
            this.showHideHelp();

            // Form Settings
            $('#wpuf-metabox-settings').on('change', 'select[name="wpuf_settings[redirect_to]"]', this.settingsRedirect);
            $('#wpuf-metabox-settings-update').on('change', 'select[name="wpuf_settings[edit_redirect_to]"]', this.settingsRedirect);
            $('select[name="wpuf_settings[redirect_to]"]').change();
            $('select[name="wpuf_settings[edit_redirect_to]"]').change();

            // Form settings: Guest post
            $('#wpuf-metabox-settings').on('change', 'input[type=checkbox][name="wpuf_settings[guest_post]"]', this.settingsGuest);
            $('input[type=checkbox][name="wpuf_settings[guest_post]"]').trigger('change');

            // From settings: User details
            $('#wpuf-metabox-settings').on('change', 'input[type=checkbox][name="wpuf_settings[guest_details]"]', this.settingsGuestDetails);
            // $('input[type=checkbox][name="wpuf_settings[guest_details]"]').trigger('change');

            // collapse all
            $('button.wpuf-collapse').on('click', this.collpaseEditFields);

            // add field click
            $('.wpuf-form-buttons').on('click', 'button', this.addNewField);

            // remove form field
            $('#wpuf-form-editor').on('click', '.wpuf-remove', this.removeFormField);

            // on change event: meta key
            $('#wpuf-form-editor').on('change', 'li.custom-field input[data-type="label"]', this.setMetaKey);

            // on change event: checkbox|radio fields
            $('#wpuf-form-editor').on('change', '.wpuf-form-sub-fields input[type=text]', function() {
                $(this).prev('input[type=checkbox], input[type=radio]').val($(this).val());
            });

            // on change event: checkbox|radio fields
            $('#wpuf-form-editor').on('click', 'input[type=checkbox].multicolumn', function() {
                // $(this).prev('input[type=checkbox], input[type=radio]').val($(this).val());
                var $self = $(this),
                    $parent = $self.closest('.wpuf-form-rows');

                if ($self.is(':checked')) {
                    $parent.next().hide().next().hide();
                    $parent.siblings('.column-names').show();
                } else {
                    $parent.next().show().next().show();
                    $parent.siblings('.column-names').hide();
                }
            });

            // on change event: checkbox|radio fields
            $('#wpuf-form-editor').on('click', 'input[type=checkbox].retype-pass', function() {
                // $(this).prev('input[type=checkbox], input[type=radio]').val($(this).val());
                var $self = $(this),
                    $parent = $self.closest('.wpuf-form-rows');

                if ($self.is(':checked')) {
                    $parent.next().show().next().show();
                } else {
                    $parent.next().hide().next().hide();
                }
            });
            
            // woo attribute
            $('#wpuf-form-editor').on('click', 'input[type=checkbox].woo_attr', function() {
                var $self = $(this),
                    $parent = $self.closest('.wpuf-form-rows');

                if ($self.is(':checked')) {
                    $parent.next().show();
                } else {
                    $parent.next().hide();
                }
            });

            // toggle form field
            $('#wpuf-form-editor').on('click', '.wpuf-toggle', this.toggleFormField);

            // clone and remove repeated field
            $('#wpuf-form-editor').on('click', 'img.wpuf-clone-field', this.cloneField);
            $('#wpuf-form-editor').on('click', 'img.wpuf-remove-field', this.removeField);
        },

        showHideHelp: function() {
            var childs = $('ul#wpuf-form-editor').children('li');

            if ( !childs.length) {
                $('.wpuf-updated').show();
            } else {
                $('.wpuf-updated').hide();
            }
        },

        makeSortable: function() {
            $formEditor = $('ul#wpuf-form-editor');

            if ($formEditor) {
                $formEditor.sortable({
                    placeholder: "ui-state-highlight",
                    handle: '> .wpuf-legend',
                    distance: 5
                });
            }
        },

        addNewField: function(e) {
            e.preventDefault();

            var $self = $(this),
                $formEditor = $('ul#wpuf-form-editor'),
                name = $self.data('name'),
                type = $self.data('type'),
                data = {
                    name: name,
                    type: type,
                    order: $formEditor.find('li').length + 1,
                    action: 'wpuf_form_add_el'
                };

            // console.log($self, data);

            // check if these are already inserted
            var oneInstance = ['post_title', 'post_content', 'post_excerpt', 'featured_image',
                'user_login', 'first_name', 'last_name', 'nickname', 'user_email', 'user_url',
                'user_bio', 'password', 'user_avatar'];

            if ($.inArray(name, oneInstance) >= 0) {
                if( $formEditor.find('li.' + name).length ) {
                    alert('You already have this field in the form');
                    return false;
                }
            }

            $('.wpuf-loading').removeClass('hide');
            $.post(ajaxurl, data, function(res) {
                $formEditor.append(res);

                // re-call sortable
                Editor.makeSortable();

                // enable tooltip
                Editor.tooltip();

                $('.wpuf-loading').addClass('hide');
                Editor.showHideHelp();
            });
        },

        removeFormField: function(e) {
            e.preventDefault();

            if (confirm('are you sure?')) {

                $(this).closest('li').fadeOut(function() {
                    $(this).remove();

                    Editor.showHideHelp();
                });
            }
        },

        toggleFormField: function(e) {
            e.preventDefault();

            $(this).closest('li').find('.wpuf-form-holder').slideToggle('fast');
        },

        cloneField: function(e) {
            e.preventDefault();

            var $div = $(this).closest('div');
            var $clone = $div.clone();
            // console.log($clone);

            //clear the inputs
            $clone.find('input').val('');
            $clone.find(':checked').attr('checked', '');
            $div.after($clone);
        },

        removeField: function() {
            //check if it's the only item
            var $parent = $(this).closest('div');
            var items = $parent.siblings().andSelf().length;

            if( items > 1 ) {
                $parent.remove();
            }
        },

        setMetaKey: function() {
            var $self = $(this),
                val = $self.val().toLowerCase().split(' ').join('_').split('\'').join(''),
                $metaKey = $(this).closest('.wpuf-form-rows').next().find('input[type=text]');

            if ($metaKey.length) {
                $metaKey.val(val);
            }
        },

        tooltip: function() {
            $('.smallipopInput').smallipop({
                preferredPosition: 'right',
                theme: 'black',
                popupOffset: 0,
                triggerOnClick: true
            });
        },

        collpaseEditFields: function(e) {
            e.preventDefault();

            $('ul#wpuf-form-editor').children('li').find('.wpuf-form-holder').slideToggle();
        },

        settingsGuest: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-guest').show();
                table.find('tr.show-if-not-guest').hide();

                $('input[type=checkbox][name="wpuf_settings[guest_details]"]').trigger('change');

            } else {
                table.find('tr.show-if-guest').hide();
                table.find('tr.show-if-not-guest').show();
            }
        },

        settingsGuestDetails: function (e) {
            e.preventDefault();

            var table = $(this).closest('table');

            if ( $(this).is(':checked') ) {
                table.find('tr.show-if-details').show();
            } else {
                table.find('tr.show-if-details').hide();
            }
        },

        settingsRedirect: function(e) {
            e.preventDefault();

            var $self = $(this),
                $table = $self.closest('table'),
                value = $self.val();

            switch( value ) {
                case 'post':
                    $table.find('tr.wpuf-page-id, tr.wpuf-url, tr.wpuf-same-page').hide();
                    break;

                case 'page':
                    $table.find('tr.wpuf-page-id').show();
                    $table.find('tr.wpuf-same-page').hide();
                    $table.find('tr.wpuf-url').hide();
                    break;

                case 'url':
                    $table.find('tr.wpuf-page-id').hide();
                    $table.find('tr.wpuf-same-page').hide();
                    $table.find('tr.wpuf-url').show();
                    break;

                case 'same':
                    $table.find('tr.wpuf-page-id').hide();
                    $table.find('tr.wpuf-url').hide();
                    $table.find('tr.wpuf-same-page').show();
                    break;
            }
        },

        tabber: function() {
            // Switches option sections
            $('.group').hide();
            $('.group:first').fadeIn();

            $('.group .collapsed').each(function(){
                $(this).find('input:checked').parent().parent().parent().nextAll().each(
                function(){
                    if ($(this).hasClass('last')) {
                        $(this).removeClass('hidden');
                        return false;
                    }
                    $(this).filter('.hidden').removeClass('hidden');
                });
            });

            $('.nav-tab-wrapper a:first').addClass('nav-tab-active');

            $('.nav-tab-wrapper a').click(function(evt) {
                $('.nav-tab-wrapper a').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active').blur();
                var clicked_group = $(this).attr('href');
                $('.group').hide();
                $(clicked_group).fadeIn();
                evt.preventDefault();
            });
        }
    };

    // on DOM ready
    $(function() {
        Editor.init();
    });

})(jQuery);