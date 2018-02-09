<?php

return array(

	/**
	 * Exclude specific groups from Laravel Translation Manager.
	 * This is useful if, for example, you want to avoid editing the official Laravel language files.
	 *
	 * @type array
	 *
	 * 	array(
	 *		'pagination',
	 *		'reminders',
	 *		'validation',
	 *	)
	 */
	'exclude_groups' => array(),

    /**
     * Set this to true, if you do not want to translate vendor packages.
     */
	'exclude_vendor' => false,

	/**
	 * Export translations with keys output alphabetically.
	 */
	'sort_keys ' => false,

);
