/**
 * Apison scripts
 */

(function ($) {

    // Handle the settings page
    class Settings {

        constructor() {
            this.wrapper = $('#apison')
            this.list = $('#the-list')

            // Manage form display
            this.wrapper.on('click', '.apison-add', this.addForm)
            this.wrapper.on('click', '.apison-edit', this.editForm)
            this.wrapper.on('click', '.apison-cancel', this.removeForm)

            // Manage for actions
            this.wrapper.on('submit', '.apison-form', this.save)
            this.wrapper.on('click', '.apison-delete', this.delete)
        }

        /**
         * Show the add new form
         *
         * @param {object} event
         */
        addForm = (event) => {
            const row = this.list.find('tr:first-child')

            if (! row.length) {
                this.list.html($(this.cloneForm()))
            }
            else if (row.is('.no-items')) {
                row.replaceWith($(this.cloneForm()))
            }
            else if (! row.is('.apison-form')) {
                this.removeForm()
                $(this.cloneForm()).insertBefore(row)
            }
        }

        /**
         * Show the edit form
         *
         * @param {object} event
         */
        editForm = (event) => {
            event.preventDefault()
            const row = $(event.target).parents('tr')

            this.removeForm()
            $(this.cloneForm()).insertAfter(row)
            this.populateForm(row)

            this.list.find('.apison-delete').removeClass('hidden')
        }

        /**
         * Remove the edit form
         */
        removeForm = () => {
            this.list.find('.apison-form').remove()
        }

        /**
         * Clone the edit form
         *
         * @return {object} clone
         */
        cloneForm = () => {
            return this.wrapper.find('.apison-form').clone(true)
        }

        /**
         * Populate the edit form
         *
         * @param {object} row
         */
        populateForm = (row) => {
            const form = this.list.find('.apison-form')

            row.find('input[type=hidden]').each(function() {
                const field = form.find(`.apison-input[name="${$(this).attr('name')}"]`)

                if (field.is('input:checkbox') && ! $(this).val()) {
                    field.removeAttr('checked')
                } else {
                    field.val($(this).val())
                }
            })

            form.find('input[name=id]').val(row.find('input[name=slug]').val())
        }

        /**
         * Save the form data
         *
         * @param {object} event
         */
        save = (event) => {
            event.preventDefault()
            const form = $(event.target)
            const spinner = form.find('.spinner')
            const button = form.find('.apison-save')
            const notice = form.find('.apison-notice')
            // Get form data and convert to a single object
            const formData = form.serializeArray().reduce((o, i) => ({ ...o, [i.name]: i.value }), {})

            spinner.addClass('is-active')
            button.attr('disabled', 'disabled')

            const request = $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: {
                    nonce: apison.nonce,
                    action: 'apison_save',
                    fields: formData
                }
            })

            request.done(({ status, data }) => {
                if (status === 'success') {
                    this.removeForm()
                    this.updateRow({ formData, data })
                } else {
                    notice.find('.error').html(data)
                    notice.removeClass('hidden').addClass('notice')
                }
            })
            request.fail((data) => {
                notice.removeClass('hidden').addClass('notice')
            })
            request.always((data) => {
                button.removeAttr('disabled')
                spinner.removeClass('is-active')
            })
        }

        /**
         * Delete the endpoint, this requires a second click for confirmation
         */
        delete = (event) => {
            const form = $(event.target).parents('.apison-form')
            const spinner = form.find('.spinner')
            const button = form.find('.apsion-delete')
            const notice = form.find('.apison-notice')
            const confirm = form.find('.apison-confirm')
            const id = form.find('input[name=id]').val()

            if (confirm.hasClass('hidden')) {
                confirm.removeClass('hidden')
            } else {
                spinner.addClass('is-active')
                button.attr('disabled', 'disabled')

                const request = $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        id,
                        nonce: apison.nonce,
                        action: 'apison_delete'
                    }
                })

                request.done(({ status, data }) => {
                    if (status === 'success') {
                        this.removeForm()
                        this.removeRow(id)
                    } else {
                        notice.find('.error').html(data)
                        notice.removeClass('hidden').addClass('notice')
                    }
                })
                request.fail((data) => {
                    notice.removeClass('hidden').addClass('notice')
                })
                request.always((data) => {
                    button.removeAttr('disabled')
                    spinner.removeClass('is-active')
                })
            }
        }

        /**
         * Update the edited row or add new row
         *
         * @param {object} params
         */
        updateRow = ({ formData: { id = null }, data }) => {
            if (! id) {
                this.list.prepend(data)
            } else {
                this.list.find(`input[name='slug'][value='${id}']`).parents('tr').replaceWith(data)
            }
        }

        /**
         * Remove the deleted row
         *
         * @param {int} id
         */
        removeRow = (id) => {
            this.list.find(`input[name='slug'][value='${id}']`).parents('tr').remove()
        }

    }

    new Settings()

})(jQuery)

