<?php
use WPForms\Pro\Forms\Fields\Repeater\Helpers as RepeaterHelpers;
class WPForms_Views_Shortcode {
	public $view_id;
	public $entries_count;
	public $table_heading_added;
	public $form;
	public $form_data;
	private $seq_no = 1;
	function __construct() {
		add_shortcode( 'wpforms-views', array( $this, 'shortcode' ), 10 );
	}

	public function shortcode( $atts ) {
		$this->seq_no = 1;
		$atts         = shortcode_atts(
			array(
				'id' => '',
			),
			$atts
		);

		if ( empty( $atts['id'] ) ) {
			return;
		}
		$view_id                   = $atts['id'];
		$this->view_id             = $view_id;
		$this->table_heading_added = false;
		$view_settings_json        = get_post_meta( $view_id, 'view_settings', true );
		if ( empty( $view_settings_json ) ) {
			return;
		}

		$view_settings = json_decode( $view_settings_json );
		$view_type     = $view_settings->viewType;
		$method_name   = 'get_view';
		$view          = $this->$method_name( $view_settings );
		return $view;

	}

	function get_view( $view_settings ) {
		global $wpdb;
		$view_type        = $view_settings->viewType;
		$before_loop_rows = $view_settings->sections->beforeloop->rows;
		$loop_rows        = $view_settings->sections->loop->rows;
		$after_loop_rows  = $view_settings->sections->afterloop->rows;
		$per_page         = $view_settings->viewSettings->multipleentries->perPage;
		$sort_order       = isset($view_settings->viewSettings->sort)? $view_settings->viewSettings->sort : '';
		if ( ! function_exists( 'wpforms' ) ) {
			return 'Please install WPForms Pro to use Views';
		}

		// Get the form,
		$this->form = wpforms()->form->get( absint( $view_settings->formId ) );
		// If the form doesn't exists, abort.
		if ( empty( $this->form ) ) {
			return;
		}

		// Pull and format the form data out of the form object.
		$this->form_data = ! empty( $this->form->post_content ) ? wpforms_decode( $this->form->post_content ) : '';

		$args = array(
			'form_id'        => $view_settings->formId,
			'posts_per_page' => $per_page,
			'view_id'        => $this->view_id,
			'view_settings'  => $view_settings,
		);

		// OrderBy Params
		if ( ! empty( $sort_order ) ) {
			foreach ( $sort_order as $sortrrow ) {
				if ( isset( $sortrrow->field ) ) {
					$args['sort_order'][] = array(
						'field_id'  => $sortrrow->field,
						'direction' => $sortrrow->value,
					);
				}
			}
		}

		// pagination
		if ( ! empty( $_GET['pagenum'] ) && ! empty( $_GET['view_id'] ) && ( $this->view_id === $_GET['view_id'] ) ) {
			$page_no        = sanitize_text_field( $_GET['pagenum'] );
			$offset         = $per_page * ( $page_no - 1 );
			$args['offset'] = $offset;
			$this->seq_no   = $offset + 1;
		}

		// Get Submissions
		$entrys = wpforms_views_get_submissions( $args );
		if ( empty( $entrys['subs'] ) ) {
			return '<div class="views-no-records-cnt">' . __( 'No records found.', 'views-for-wpforms-lite' ) . '</div>';
		}

		$this->submissions_count = $entrys['total_count'];
		$entries                 = $entrys['subs'];
		$view_content            = '<div class="wpforms-view wpforms-view-type-' . $view_type . ' wpforms-view-' . $this->view_id . '">';
		if ( ! empty( $before_loop_rows ) ) {
			$view_content .= $this->get_sections_content( 'beforeloop', $view_settings, $entries );
		}

		if ( ! empty( $loop_rows ) ) {
			if ( $view_type == 'table' ) {
				$view_content .= $this->get_table_content( 'loop', $view_settings, $entries );
			} else {
				$view_content .= $this->get_sections_content( 'loop', $view_settings, $entries );
			}
		}

		if ( ! empty( $after_loop_rows ) ) {
			$view_content .= $this->get_sections_content( 'afterloop', $view_settings, $entries );
		}
		$view_content .= '</div>';
		return $view_content;

	}


	function get_sections_content( $section_type, $view_settings, $entries ) {
		$content      = '';
		$section_rows = $view_settings->sections->{$section_type}->rows;
		if ( $section_type == 'loop' ) {
			foreach ( $entries as $entry ) {
				foreach ( $section_rows as $row_id ) {
					// $content .= $this->get_table_content( $row_id, $view_settings, $entry );
					$content .= $this->get_grid_row_html( $row_id, $view_settings, $entry );
					$this->seq_no++;
				}
			}
		} else {
			foreach ( $section_rows as $row_id ) {
				$content .= $this->get_grid_row_html( $row_id, $view_settings );
			}
		}
		return $content;
	}



	function get_table_content( $section_type, $view_settings, $entries ) {
		$content      = '';
		$section_rows = $view_settings->sections->{$section_type}->rows;
		$content      = ' <div class="wpf-views-cont wpf-views-' . $this->view_id . '-cont"><table class="wpforms-views-table wpforms-view-' . $this->view_id . '-table pure-table pure-table-bordered">';
		$content     .= '<thead>';
		foreach ( $entries as $entry ) {
			$content .= '<tr>';
			foreach ( $section_rows as $row_id ) {

				$content .= $this->get_table_row_html( $row_id, $view_settings, $entry );
				$this->seq_no++;
			}
			$content .= '</tr>';

		}
		$content .= '</tbody></table></div>';

		return $content;
	}

	function get_table_row_html( $row_id, $view_settings, $entry = false ) {
		$row_content = '';
		$row_columns = $view_settings->rows->{$row_id}->cols;
		foreach ( $row_columns as $column_id ) {
			$row_content .= $this->get_table_column_html( $column_id, $view_settings, $entry );
		}
		// $row_content .= '</table>'; // row ends
		return $row_content;
	}

	function get_table_column_html( $column_id, $view_settings, $entry ) {
		$column_size   = $view_settings->columns->{$column_id}->size;
		$column_fields = $view_settings->columns->{$column_id}->fields;

		$column_content = '';

		if ( ! ( $this->table_heading_added ) ) {

			foreach ( $column_fields as $field_id ) {
				$column_content .= $this->get_table_headers( $field_id, $view_settings, $entry );
			}
			$this->table_heading_added = true;
			$column_content           .= '</tr></thead><tbody><tr>';
		}
		foreach ( $column_fields as $field_id ) {

			$column_content .= $this->get_field_html( $field_id, $view_settings, $entry );
		}

		return $column_content;
	}



	function get_grid_row_html( $row_id, $view_settings, $entry = false ) {
		$row_columns = $view_settings->rows->{$row_id}->cols;

		$row_content = '<div class="pure-g wpforms-view-row">';
		foreach ( $row_columns as $column_id ) {
			$row_content .= $this->get_grid_column_html( $column_id, $view_settings, $entry );
		}
		$row_content .= '</div>'; // row ends
		return $row_content;
	}

	function get_grid_column_html( $column_id, $view_settings, $entry ) {
		$column_size   = $view_settings->columns->{$column_id}->size;
		$column_fields = $view_settings->columns->{$column_id}->fields;

		$column_content = '<div class=" wpforms-view-col pure-u-1 pure-u-md-' . $column_size . '">';
		foreach ( $column_fields as $field_id ) {

			$column_content .= $this->get_field_html( $field_id, $view_settings, $entry );

		}
		$column_content .= '</div>'; // column ends
		return $column_content;
	}

	function get_field_html( $field_id, $view_settings, $entry ) {
		$field         = $view_settings->fields->{$field_id};
		$form_field_id = $field->formFieldId;
		$fieldSettings = $field->fieldSettings;
		$label         = $fieldSettings->useCustomLabel ? $fieldSettings->label : $field->label;
		$class         = $fieldSettings->customClass;
		$view_type     = $view_settings->viewType;
		$field_html    = '';

		// Entry field values are in JSON, so we need to decode.
		$entry_fields = ! empty( $entry ) && is_object( $entry ) ? json_decode( $entry->fields, true ) : false;
		// echo '<pre>';
		// print_r( $this->form_data['fields'][ $form_field_id ] );
		// print_r( $entry_fields );
		// die;
		// check if it's a form field & & maybe deleted
		if ( is_numeric( $form_field_id ) && ( ! isset( $entry_fields[ $form_field_id ] ) && $this->form_data['fields'][ $form_field_id ]['type'] !== 'repeater' ) ) {
			return '<td></td>';
		}

		if ( $view_type == 'table' ) {
		$width       = ! empty( $field->fieldSettings->columnWidth ) ? $field->fieldSettings->columnWidth : 'auto';
			$field_html .= '<td  style="width:' . $width . '">';
		}

		$field_html .= '<div  class="wpforms-view-field-cont  field-' . $form_field_id . ' ' . $class . '">';

		// check if it's a form field
		if ( ! empty( $entry_fields ) && is_array( $entry_fields ) && is_numeric( $form_field_id ) ) {
			// if view type is table then don't send label
			if ( $view_type != 'table' ) {
				if ( ! empty( $label ) ) {
					$field_html .= '<div class="wpforms-view-field-label">' . $label . '</div>';
				}
			}

			$field_value_pre_processed = wp_strip_all_tags( isset( $entry_fields[ $form_field_id ]['value'] ) ? $entry_fields[ $form_field_id ]['value'] : '' );
			$field_value               = apply_filters( 'wpforms_html_field_value', wp_strip_all_tags( $field_value_pre_processed ), $this->form_data['fields'][ $form_field_id ], $this->form_data, 'entry-frontend-table' );

			// $form_field_type = $entry_fields[ $form_field_id ]['type'];
			$form_field_type = $this->form_data['fields'][ $form_field_id ]['type'];

			$field_html .= '<div class="wpforms-view-field-value wpforms-view-field-type-' . $form_field_type . '-value">';

			if ( is_array( $field_value ) ) {

				$field_value = implode( ', ', $field_value );

			} elseif ( $form_field_type == 'file-upload' ) {

				$file_parts       = pathinfo( $field_value_pre_processed );
				$image_extensions = array( 'jpg', 'png', 'gif' );

				if ( ( isset( $fieldSettings->displayFileType ) && $fieldSettings->displayFileType == 'Image' )  ) {
					$width    = ! empty( $fieldSettings->imageWidth ) ? $fieldSettings->imageWidth : '100%';
					$field_value_array = array();
					foreach ( $entry_fields[ $form_field_id ]['value_raw'] as $file ) {
						if ( empty( $file['value'] ) || empty( $file['file_original'] ) ) {
							return '';
						}
						$field_value_array[] = '<img style="width:' . $width . '" class="wpforms-view-img" src="' . wp_strip_all_tags( $file['value'] ) . '">';

					}
					if ( ! empty( $field_value_array ) ) {
						$field_value = implode( '<br>', $field_value_array );
					}
				} else {
					// Process modern uploader.
					if ( ! empty( $entry_fields[ $form_field_id ]['value_raw'] ) ) {
						$field_value = wpforms_chain( $entry_fields[ $form_field_id ]['value_raw'] )
						->map(
							static function ( $file ) {

								if ( empty( $file['value'] ) || empty( $file['file_original'] ) ) {
									return '';
								}

								return sprintf(
									'<a href="%s" rel="noopener noreferrer" target="_blank">%s</a>',
									esc_url( $file['value'] ),
									esc_html( $file['file_original'] )
								);
							}
						)
						->array_filter()
						->implode( '<br>' )
						->value();
					} else {

						// Process classic uploader.
						$field_value = sprintf(
							'<a href="%s" rel="noopener" target="_blank">%s</a>',
							esc_url( $entry_fields[ $form_field_id ]['value'] ),
							esc_html( $entry_fields[ $form_field_id ]['file_original'] )
						);
					}
				}
			} elseif ( $form_field_type == 'textarea' ) {
				$field_value = nl2br( $field_value );
			} elseif ( $form_field_type == 'repeater' ) {

				 $field_value = $this->get_repeater_field_value( $field_value, $this->form_data['fields'][ $form_field_id ], $entry );
			} elseif ( $form_field_type == 'select' || $form_field_type == 'checkbox' ) {
				$field_value_raw = $entry_fields[ $form_field_id ]['value'];
				$field_value_raw = preg_split( '/\r\n|\r|\n/', $field_value_raw );
				$field_value     = implode( ', ', $field_value_raw );
			}
			$field_value = apply_filters( 'wpforms-views-field-value', $field_value, $view_settings );
			$field_html .= $field_value;
			$field_html .= '</div>';
		} else {
			switch ( $field->formFieldId ) {
				case 'pagination':
					$field_html .= $this->get_pagination_links( $view_settings );
					break;
				case 'paginationInfo':
					$field_html .= $this->get_pagination_info( $view_settings );
					break;
				case 'html':
					$field_html .= do_shortcode( $fieldSettings->html );
					break;
				case 'entryId':
					$field_html .= '<div class="wpforms-view-field-value wpforms-view-field-type-entryId-value">';
					$field_html .= $entry->entry_id;
					$field_html .= '</div>';
					break;
				case 'sequenceNumber':
					$field_html .= '<div class="wpforms-view-field-value wpforms-view-field-type-sequenceNumber-value">';
					$field_html .= $this->seq_no;
					$field_html .= '</div>';
					break;
				case 'entryDate':
					$field_html .= '<div class="wpforms-view-field-value wpforms-view-field-type-entryDate-value">';
					$field_html .= $this->get_formatted_date( $entry->date, $fieldSettings, '', true );
					$field_html .= '</div>';
					break;
				case 'entryUpdateDate':
					$field_html .= '<div class="wpforms-view-field-value wpforms-view-field-type-entryUpdateDate-value">';
					$field_html .= $this->get_formatted_date( $entry->date_modified, $fieldSettings, '', true );
					$field_html .= '</div>';
					break;
			}
		}

		$field_html .= '</div>';
		if ( $view_type === 'table' ) {
			$field_html .= '</td>';
		}

		return $field_html;
	}

	function get_table_headers( $field_id, $view_settings ) {
		$field         = $view_settings->fields->{$field_id};
		$fieldSettings = $field->fieldSettings;
		$label         = $fieldSettings->useCustomLabel ? $fieldSettings->label : $field->label;
		$width         = ! empty( $field->fieldSettings->columnWidth ) ? $field->fieldSettings->columnWidth : 'auto';
		$header        = '<th>';
		$header       .= '<div style="width:' . $width . '" class="wpf-views-table-header ">';
		$header       .= $label;
		$header       .= '</div>';
		$header       .= '</th>';
		return $header;
	}


	function get_pagination_links( $view_settings ) {
		global $wp;
		$entries_count = $this->submissions_count;
		$per_page      = $view_settings->viewSettings->multipleentries->perPage;
		$pages         = new WPForms_View_Paginator( $per_page, 'pagenum' );
		$pages->set_total( $entries_count ); // or a number of records
		$current_url = remove_query_arg( array( 'pagenum', 'view_id' ) );
		$current_url = add_query_arg( 'view_id', $this->view_id, $current_url );

		return $pages->page_links( $current_url . '&' );
	}

	function get_pagination_info( $view_settings ) {
		$page_no       = empty( $_GET['pagenum'] ) ? 1 : sanitize_text_field( $_GET['pagenum'] );
		$entries_count = $this->submissions_count;
		if ( $entries_count <= 0 ) {
			return;
		}
		$per_page = $view_settings->viewSettings->multipleentries->perPage;
		$from     = ( $page_no - 1 ) * $per_page;
		$of       = $per_page * $page_no;
		if ( $of > $entries_count ) {
			$of = $entries_count;
		}
		if ( $from == 0 ) {
			$from = 1;
		}

		return 'Displaying ' . $from . ' - ' . $of . ' of ' . $entries_count;
	}

	public function get_repeater_field_value( $field_value, $field, $entry ) {

		$html         = '';
		$entry_fields = wpforms_decode( $entry->fields );
		if ( ! class_exists( RepeaterHelpers::class ) ) {
			return '';
		}

		$repeater_clones = RepeaterHelpers::get_repeater_clones_from_fields( $field, $entry_fields );
		$original_fields = RepeaterHelpers::get_repeater_original_field_ids( $field );

		if ( ! empty( $original_fields ) ) {

			foreach ( $original_fields as $original_field_id ) {
				if ( isset( $entry_fields[ $original_field_id ] ) ) {
					$html .= '<strong>' . $entry_fields[ $original_field_id ]['name'] . '</strong>';
					$html .= '<div>' . $entry_fields[ $original_field_id ]['value'] . '</div>';
				}
			}
		}
		if ( ! empty( $repeater_clones ) ) {
			foreach ( $repeater_clones as $clone_num ) {
				$html .= '<hr/>';
				foreach ( $original_fields as $original_field_id ) {
					$cloned_field_id = $original_field_id . '_' . $clone_num;
					if ( isset( $entry_fields[ $cloned_field_id ] ) ) {
						$html .= '<strong>' . $entry_fields[ $cloned_field_id ]['name'] . '</strong>';
						$html .= '<div>' . $entry_fields[ $cloned_field_id ]['value'] . '</div>';
					}
				}
			}
		}

		return $html;

	}

	public function get_formatted_date( $date, $fieldSettings, $format = '', $gmt_offset = false ) {
		if ( ! empty( $fieldSettings->displayTime ) ) {
			$format = sprintf( '%s %s', get_option( 'date_format' ), get_option( 'time_format' ) );
		} else {
			$format = sprintf( '%s', get_option( 'date_format' ) );
		}

		if ( is_string( $date ) ) {
			$date = strtotime( $date );
		}

		if ( $gmt_offset ) {
			$date += (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		}

		return date_i18n( $format, $date );
	}

}
new WPForms_Views_Shortcode();
