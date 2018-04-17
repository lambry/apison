<ul>
    <li>
        <strong><?php _e('Title:', 'apison'); ?></strong>
        <?php _e('A memorable name for the API feed, used for visual reference only.', 'apison'); ?>
    </li>
    <li>
        <strong><?php _e(' Slug:', ' apison'); ?></strong>
        <?php _e('A short identifier for the API feed, this will be used to access the cached API data. ', ' apison '); ?>
    </li>
    <li>
        <strong><?php _e(' URL:', 'apison'); ?></strong>
        <?php _e('The full URL to access the desired API data, this will include an API token if necessary.', ' apison'); ?>
    </li>
    <li>
        <strong><?php _e('Path:', 'apison'); ?></strong>
        <?php _e('If the data you wish to access is not at the top level of the API response, you can set a path to point to the data within the API response that you actually wish to use. For example, you may want to loop over and display items that are beneath the "data" key of the response, if that is that case add "data" to the path field, if you wish to use "products" that are returned beneath said "data" add "data.products".', 'apison'); ?>
    </li>
    <li>
        <strong><?php _e('Cache:', 'apison'); ?></strong>
        <?php _e('The duration of time to cache data before refetching from the API.', 'apison'); ?>
    </li>
    <li>
        <strong><?php _e('Active:', 'apison'); ?></strong>
        <?php _e('Whether or not the current API endpoint is active.', 'apison'); ?>
    </li>
    <li>
        <strong><?php _e('Save:', 'apison'); ?></strong>
        <?php _e('Clicking the save button will save the new/updated endpoint and clear any currently cached data for that endpoint.', 'apison'); ?>
    </li>
</ul>
