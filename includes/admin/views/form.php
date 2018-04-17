<?php
/**
 * Edit form
 *
 * @package Apison
 */

use Lambry\Apison\Admin\Defaults; ?>

<table class="hidden">
    <tr class="apison-form inline-edit-row inline-edit-row-page quick-edit-row-page">
        <td colspan="5">
            <form method="get">
                <fieldset class="inline-edit-col-left">
                    <div class="inline-edit-col">
                        <label>
                            <span class="title"><?php _e('Title', 'apison'); ?><sup>*</sup></span>
                            <span class="input-text-wrap">
                                <input class="apison-input" name="title" type="text" required>
                            </span>
                        </label>

                        <label>
                            <span class="title"><?php _e('Slug', 'apison'); ?><sup>*</sup></span>
                            <span class="input-text-wrap">
                                <input class="apison-input" name="slug" type="text" required>
                            </span>
                        </label>

                        <label>
                            <span class="title"><?php _e('Url', 'apison'); ?><sup>*</sup></span>
                            <span class="input-text-wrap">
                                <input class="apison-input" name="url" type="url" required>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <fieldset class="inline-edit-col-right">
                    <div class="inline-edit-col">
                        <label>
                            <span class="title"><?php _e('Path', 'apison'); ?></span>
                            <span class="input-text-wrap">
                                <input class="apison-input" name="path" type="text">
                            </span>
                        </label>

                        <label>
                            <span class="title"><?php _e('Cache', 'apison'); ?></span>
                            <span class="input-text-wrap">
                                <select class="apison-input" name="cache">
                                    <?php foreach(Defaults::cache() as $key => $val) : ?>
                                        <option value="<?= $key ?>"><?= $val ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span>
                        </label>

                        <label>
                            <span class="title"><?php _e('Active', 'apison'); ?></span>
                            <span class="input-text-wrap">
                                <input class="apison-input" name="active" type="checkbox" checked>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <div class="apison-submit submit">
                    <input name="id" type="hidden">
                    <button type="button" class="apison-cancel button alignleft">
                        <?php _e('Cancel', 'apison'); ?>
                    </button>
                    <button type="button" class="apison-delete hidden">
                        <i class="dashicons dashicons-trash"></i>
                        <span class="apison-confirm hidden"><?php _e('Confirm Delete?', 'apison'); ?></span>
                    </button>
                    <button type="submit" class="apison-save button button-primary alignright">
                        <?php _e('Save', 'apison'); ?>
                    </button>
                    <span class="spinner"></span>
                    <br class="clear" />
                    <div class="apison-notice notice-error notice-alt hidden">
                        <p class="error"><?php _e('Sorry an error has occured, please try again.', 'apison'); ?></p>
                    </div>
                </div>
            </form>
        </td>
    </tr>
</table>
