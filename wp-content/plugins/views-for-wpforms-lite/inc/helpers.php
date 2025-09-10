<?php
function wpforms_views_get_form_fields( $form_id ) {
	if ( empty( $form_id ) ) {
		return '{}';
	}
	$form_fields_obj = new stdClass();
	$form            = wpforms()->form->get( absint( $form_id ), array( 'content_only' => true ) );
	foreach ( $form['fields'] as $field ) {

		if ( $field['type'] !== 'html' && $field['type'] !== 'layout' ) {
			$values = array();
			if ( ! empty( $field['choices'] ) ) {
				foreach ( $field['choices'] as $choice ) {
					// TODO: Check if values are different then label
					$values[ $choice['label'] ] = $choice['label'];
				}
			}

			$field['label']                  = isset( $field['label'] ) ? $field['label'] : '';
			$form_fields_obj->{$field['id']} = (object) array(
				'id'        => $field['id'],
				'label'     => $field['label'],
				'fieldType' => $field['type'],
				'values'    => $values,
			);
		}
	}
	return json_encode( $form_fields_obj );

}


/**
 * Get submissions based on specific critera.
 *
 * @since 2.7
 * @param array $args
 * @return array $sub_ids
 */
function wpforms_views_get_submissions( $args ) {
	global $wpdb;
	$form_id = $args['form_id'];

	$entries_args = array(
		'form_id' => absint( $args['form_id'] ),
	);

	// TODO --- Show single entry only
	if ( ! empty( $args['submission_id'] ) ) {
		$entries_args['entry_id'] = absint( $args['submission_id'] );
	}

	// Number of entries to show. If empty, defaults to 25.
	if ( ! empty( $args['posts_per_page'] ) ) {
		$entries_args['number'] = absint( $args['posts_per_page'] );
	}

	// Number of entries to show. If empty, defaults to 25.
	if ( ! empty( $args['offset'] ) ) {
		$entries_args['offset'] = absint( $args['offset'] );
	}

	// Add order by parameters
	if ( ! empty( $args['sort_order'] ) ) {
		foreach ( $args['sort_order'] as $sort ) {
			$field_id  = $sort['field_id'];
			$direction = $sort['direction'];
			if ( in_array( $field_id, array( 'submission_id', 'entryId', 'entryid' ) ) ) {
				$entries_args['order']   = $direction;
				$entries_args['orderby'] = 'entry_id';
			} elseif ( $field_id === 'entryDate' ) {
				$entries_args['order']   = $direction;
				$entries_args['orderby'] = 'date';
			} else {
				// For custom fields in the non-filter case, we need to use a custom query
				// This is a simple implementation for the lite version
				$entry_table        = WPForms_Views_Common::get_entry_table_name();
				$entry_fields_table = WPForms_Views_Common::get_entry_fields_table_name();

				$limit   = isset( $entries_args['number'] ) ? absint( $entries_args['number'] ) : 25;
				$offset  = isset( $entries_args['offset'] ) ? absint( $entries_args['offset'] ) : 0;
				$form_id = absint( $args['form_id'] );

				$sql_query = "SELECT e.* FROM {$entry_table} e
                              LEFT JOIN {$entry_fields_table} f ON e.entry_id = f.entry_id
                              WHERE e.form_id = {$form_id}
                              AND e.status != 'trash'
                              AND e.status != 'partial'";

				if ( ! empty( $entries_args['entry_id'] ) ) {
					$entry_id   = absint( $entries_args['entry_id'] );
					$sql_query .= " AND e.entry_id = {$entry_id}";
				}

				$sql_query .= " AND f.field_id = {$field_id}
                              GROUP BY e.entry_id
                              ORDER BY f.value {$direction}
                              LIMIT {$offset},{$limit}";

				$results = $wpdb->get_results( $sql_query );

				// Total entries count - simplified for lite version
				$count_query = "SELECT COUNT(DISTINCT e.entry_id) FROM {$entry_table} e
                               LEFT JOIN {$entry_fields_table} f ON e.entry_id = f.entry_id
                               WHERE e.form_id = {$form_id}
                               AND e.status != 'trash'
                               AND e.status != 'partial'";

				if ( ! empty( $entries_args['entry_id'] ) ) {
					$count_query .= " AND e.entry_id = {$entry_id}";
				}

				$count_query .= " AND f.field_id = {$field_id}";

				$total_entries_count = $wpdb->get_var( $count_query );

				$submissions['total_count'] = $total_entries_count;
				$submissions['subs']        = $results;

				return $submissions;
			}
		}
	}

	// Get all entries for the form, according to arguments defined.
	$entries = wpforms()->entry->get_entries( $entries_args );

	$total_entries_count = wpforms()->entry->get_entries( $entries_args, true );

	$submissions['total_count'] = $total_entries_count;
	$submissions['subs']        = $entries;

	return $submissions;
}
