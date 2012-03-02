<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Provide funtionalities to create and output HTML forms as well as hanle errors.
 *
 * @package html
 */
class html_form {
	var $location = '';
	var $name = '';
	var $action = '';
	var $method = '';
	var $has_error = false;
	var $elements = array();

	/**
	 * Create an HTML form.
	 * The object is stored in session when register() is called.
	 * The object can be retrieved with get_form() across the session.
	 *
	 * @param string $name Form name
	 * @param string $action URL of form action
	 * @param string $method "post" or "get"
	 * @return html_form
	 */
	function html_form ($name, $action='', $method = 'post') {
		if (isset($_SESSION['_FORM'][$name]) && $_SESSION['_FORM'][$name]->has_error) {
			$in_session = $_SESSION['_FORM'][$name];
			$this->location = $in_session->location;
			$this->name = $in_session->name;
			$this->action = $in_session->action;
			$this->method = $in_session->method;
			$this->has_error = false;
			$this->elements = $in_session->elements;
		} else {
			$this->location = $_SERVER['PHP_SELF'].(($query=http::build_query($_GET)) !== '' ? '?'.$query : '');
			$this->name = $name;
			$this->action = $action;
			$this->method = $method;
		}
	}

	/**
	 * Retrived html_form object stored in session
	 *
	 * @param string $name Form name
	 * @return html_form
	 */
	function get_form($name) {
		if (isset($_SESSION['_FORM'][$name])) {
			$form = $_SESSION['_FORM'][$name];
			$data = ($form->method == 'get') ? $_GET : $_POST;
			foreach ($form->elements as $name=>$element) {
				if (is_array($element)) {
					 if (isset($data[$name])) {
						if (is_array($data[$name])) {
							foreach (array_keys($element) as $value) {
								if (in_array($value, $data[$name])) {
									$element[$value]->checked = true;
								} else {
									$element[$value]->checked = false;
								}
							}
						} else {
							foreach (array_keys($element)as $value) {
								if ($data[$name] == $value) {
									$element[$value]->checked = true;
								} else {
									$element[$value]->checked = false;
								}
							}
						}
					 } else {
						 foreach ($element as $value=>$item) {
							 $element[$value]->checked = false;
						 }
					 }
				} elseif (is_a($element, 'html_form_file')) {
					$element->value = !empty($_FILES[$name]['name']) ? $_FILES[$name] : '';
					$delete_name = $name.'_delete';
					$element->delete = isset($data[$delete_name]) && $element->existing;
				} elseif (is_a($element, 'html_form_image_button')) {
					$var = $name.'_x';
					if (isset($data[$name])) {
						$element->value = $data[$name];
						$element->clicked = true;
					} elseif (isset($data[$var])) {
						$element->clicked = true;
					}
				} else {
					if (isset($data[$name])) {
						$element->value = $data[$name];
						if (isset($element->clicked)) {
							$element->clicked = true;
						}
					} elseif (isset($element->multiple) && $element->multiple && !isset($data[$name])) {
						$element->value = array();
					}
				}
				$form->elements[$name] = $element;
			}
			return $form;
		} else {
			return false;
		}
	}

	/**
	 * Add an form element to this form
	 *
	 * @static
	 *
	 * @param html_form_element $element Element object
	 * @return boolean
	 */
	function add($element) {
		if (is_a($element, 'html_form_element')) {
			if (is_a($element, 'html_form_radio')) {
				if (!isset($this->elements[$element->name])) { // new form
					$this->elements[$element->name] = array();
				}
				if (!isset($this->elements[$element->name][$element->value])) {
					$this->elements[$element->name][$element->value] = $element;
				}
			} elseif(is_a($element, 'html_form_checkbox')) {
				if (!isset($this->elements[$element->name])) {
					$this->elements[$element->name] = array();
				}
				if (!isset($this->elements[$element->name][$element->value])) {
					$this->elements[$element->name][$element->value] = $element;
				}
			} else {
				if (!isset($this->elements[$element->name])) {
					$this->elements[$element->name] = $element;
				}
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the value of an element
	 *
	 * @param string $element_name Element name
	 * @return mixed
	 */
	function get($element_name) {
		if (isset($this->elements[$element_name])) {
			if (is_array($this->elements[$element_name])) {
				if (!empty($this->elements[$element_name])) {
					$keys = array_keys($this->elements[$element_name]);
					$type = get_class($this->elements[$element_name][$keys[0]]);
				} else {
					$type = 'html_form_radio';
				}

				$values = array();
				foreach ($this->elements[$element_name] as $element) {
					if ($element->checked) {
						$values[] = $element->value;
					}
				}

				if ($type == 'html_form_radio') {
					if (count($values) == 0) {
						return '';
					} elseif (count($values) == 1) {
						return $values[0];
					}
				} else {
					return $values;
				}
			} else {
				return $this->elements[$element_name]->value;
			}
		} else {
			return false;
		}
	}

	/**
	 * Detect whether a button or an image button is clicked
	 *
	 * @param string $element_name Element name
	 * @return boolean
	 */
	function clicked($element_name) {
		if (isset($this->elements[$element_name]) && isset($this->elements[$element_name]->clicked)) {
			return $this->elements[$element_name]->clicked;
		} else {
			return false;
		}
	}

	/**
	 * Detect whether a radio button or a checkbox is checked
	 *
	 * @param string $element_name Element name
	 * @param string $value Element value
	 * @return boolean
	 */
	function checked($element_name, $value) {
		if (isset($this->elements[$element_name]) && is_array($this->elements[$element_name])) {
			foreach ($this->elements[$element_name] as $index=>$element) {
				if ($index == $value) {
					return $element->checked;
				}
			}
			return false;
		} else {
			return false;
		}
	}

	/**
	 * Return whether an element is a required field
	 *
	 * @param string $element_name Element name
	 * @return boolean
	 */
	function required($element_name) {
		if (isset($this->elements[$element_name])) {
			return $this->elements[$element_name]->required;
		} else {
			return false;
		}
	}

	/**
	 * Return whether an file is to be deleted
	 *
	 * @param string $element_name Element name
	 * @return boolean
	 */
	function delete_file($element_name) {
		if (isset($this->elements[$element_name]) && is_a($this->elements[$element_name], 'html_form_file')) {
			return $this->elements[$element_name]->delete;
		} else {
			return false;
		}
	}

	/**
	 * Store the html_form object in session.
	 */
	function register() {
		if (!isset($_SESSION['_FORM'])) {
			$_SESSION['_FORM'] = array();
		}
		$_SESSION['_FORM'][$this->name] = $this;
	}

	/**
	 * Valiate all elements and detect any error
	 *
	 * @return boolean
	 */
	function validate() {
		$form_valid = true;
		foreach($this->elements as $name=>$element) {
			if (is_array($this->elements[$name])) {
				$elem_valid = true;
			} else {
				$elem_valid = $this->elements[$name]->validate();
			}
			$form_valid = $form_valid && $elem_valid;
		}
		return $form_valid;
	}

	/**
	 * Set the callback function to validate an element
	 *
	 * @param string $element_name Element name
	 * @param mixed $callback Name of callback function. Use array('class','function') to specify a static function in a class
	 * @param string $error The error message when validation fails
	 * @return boolean
	 */
	function set_validator($element_name, $callback, $error) {
		if (isset($this->elements[$element_name]) && !is_array($this->elements[$element_name])) {
			$this->elements[$element_name]->validator = $callback;
			$this->elements[$element_name]->validator_error = $error;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * There are error in the input. This function redirects back to the form page.
	 *
	 * @param string $error Custom error message
	 */
	function set_failure($error = '') {
		$this->has_error = true;
		foreach($this->elements as $name=>$element) {
			if (isset($element->clicked)) {
				$element->clicked = false;
				$this->elements[$name] = $element;
			}
		}
		$this->register();
		$error = ($error == '')? 'Sorry, some required fields are missing. See below for details.' : $error;
		html_message::add($error);
		http::redirect($this->location);
	}

	/**
	 *
	 */
	function set_reload() {
		$this->has_error = true;
		$this->register();
	}

	/**
	 * Return the HTML open tag of this form
	 *
	 * @param string $extra_fields Any extra attributes to output. E.g. target="_blank".
	 * @return string
	 */
	function output_open($extra_fields = '') {
		$multipart = false;
		foreach ($this->elements as $element) {
			if (is_a($element, 'html_form_file')) {
				$multipart = true;
			}
		}

		$html = '<form name="'.$this->name.'" id="'.$this->name.'" ';
		$html .= 'action="'.$this->action.'" method="'.$this->method.'"';
		if ($multipart) {
			$html .= ' enctype="multipart/form-data"';
		}
		$html .= ' '.$extra_fields.'>';
		return $html;
	}

	/**
	 * Return the HTML close tag of this form
	 *
	 * @return string
	 */
	function output_close() {
		return '</form>';
	}

	/**
	 * Output a star (*) for required fields
	 *
	 * @param string $element_name
	 * @return string
	 */
	function output_required($element_name) {
		global $_CONFIG;

		if ($this->required($element_name)) {
			return '<span'.(isset($_CONFIG['html_form']['required_class']) ? ' class="'.$_CONFIG['html_form']['required_class'].'"' : '').'>*</span>';
		} else {
			return '';
		}
	}

	/**
	 * Return the HTML codes for an element
	 *
	 * @param string $element_name Element name
	 * @param mixed $value If the element is a set of check boxes or radio buttons, specify which item to output
	 * @param string $extra_fields Any extra attributes to output.
	 * @return string
	 */
	function output($element_name, $value=NULL, $extra_fields = '') { // $value is needed only if element is radio or checkbox
		if (is_array($this->elements[$element_name])) {
			if ($value == NULL) {
				$html = '';
				foreach ($this->elements[$element_name] as $element) {
					$html .= $element->output($extra_fields);
				}
				return $html;
			} else {
				return $this->elements[$element_name][$value]->output($extra_fields);
			}
		} else {
			return $this->elements[$element_name]->output($extra_fields);
		}
	}
}

/**
 * Abstract class for all HTML form elements
 *
 * @package html
 */
class html_form_element {
	var $name = '';
	var $value = '';
	var $class = '';
	var $error = '';
	var $onclick = '';
	var $required = false;
	var $multiple = false;
	var $readonly = false;
	var $validator;
	var $validator_error;

	/**
	 * Return element error message
	 *
	 * @return string
	 */
	function show_error() {
		global $_CONFIG;

		$html = '';
		if (!empty($this->error)) {
			if (isset($_CONFIG['html_form']['error_class'])) {
				$html .= ' <span class="'.$_CONFIG['html_form']['error_class'].'">'.$this->error.'</span>';
			} else {
				$html .= ' '.$this->error;
			}
		}
		return $html;
	}

	/**
	 * Validate the user-entered information.
	 *
	 * @return boolean
	 */
	function validate() {
		global $_CONFIG;

		$empty = false;
		if ($this->multiple) {
			$empty = empty($this->value) || (count($this->value) == 1 && $this->value[0] == '');
		}

		$existing = false;
		if (isset($this->existing) && $this->existing) {
			$existing = true;
		}

		if ($this->required && ($this->value === '' || $empty) && !$existing) {
			if (isset($_CONFIG['html_form']['required_error_msg'])) {
				$this->error = $_CONFIG['html_form']['required_error_msg'];
			} else {
				$this->error = '* Required';
			}
			return false;
		} elseif ($this->value !== '' && !$empty && !empty($this->validator)) {
			if (!call_user_func($this->validator, $this->value)) {
				$this->error = $this->validator_error;
				return false;
			} else {
				$this->error = '';
				return true;
			}
		} else {
			$this->error = '';
			return true;
		}
	}
}

/**
 * Text box
 *
 * @package html
 */
class html_form_text extends html_form_element {
	var $maxlength = '';

	/**
	 * Create a text box.
	 *
	 * @param string $name Text box name
	 * @param boolean $required Whether this field is required
	 * @param string $value Pre-filled value if any
	 * @param string $class CSS class name
	 * @param boolean $readonly Whether this field is read-only
	 * @param int $size Size of text box
	 * @param int $maxlength Maximum length of text box
	 * @return html_form_text
	 */
	function html_form_text($name, $required=false, $value='', $class='', $readonly=false, $size='', $maxlength='') {
		$this->name = $name;
		$this->required = $required;
		$this->value = $value;
		$this->class = $class;
		$this->readonly = $readonly;
		$this->size = $size;
		$this->maxlength = $maxlength;
	}

	/**
	 * Return the HTML code for this text box.
	 *
	 * @param string $extra_fields Any extra attributes to output
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '<input type="text" name="'.$this->name.'" id="'.$this->name.'" ';
		if ($this->value != '') {
			$html .= 'value="'.html_text::escape($this->value).'" ';
		}
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if (!empty($this->size)) {
			$html .= 'size="'.$this->size.'" ';
		}
		if (!empty($this->maxlength)) {
			$html .= 'maxlength="'.$this->maxlength.'" ';
		}
		if ($this->readonly) {
			$html .= 'readonly ';
		}
		$html .= $extra_fields.'/>';
		$html .= $this->show_error();
		return $html;
	}
}

/**
 * Hidden field
 *
 * @package html
 */
class html_form_hidden extends html_form_element {

	/**
	 * Create a hidden field
	 *
	 * @param string $name Name of hidden field
	 * @param string $value Value of hidden field
	 * @return html_form_hidden
	 */
	function html_form_hidden($name, $value='', $required=false) {
		$this->name = $name;
		$this->value = $value;
		$this->required = $required;
	}

	/**
	 * Return the HTML codes for this hidden field.
	 *
	 * @param string $extra_fields Any extra field to output
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '<input type="hidden" name="'.$this->name.'" id="'.$this->name.'" ';
		$html .= 'value="'.html_text::escape($this->value).'" ';
		$html .= $extra_fields.'/>';
		$html .= $this->show_error();
		return $html;
	}
}

/**
 * Password field
 *
 * @package html
 */
class html_form_password extends html_form_element {
	var $maxlength = '';

	/**
	 * Create a password field.
	 *
	 * @param string $name Name of password field
	 * @param boolean $required Whether this field is required
	 * @param string $value Pre-filled value if any
	 * @param string $class Name of the CSS class
	 * @param boolean $readonly Whether this filed is read-only
	 * @param int $size Size of password field
	 * @param int $maxlength Maximum length of password field
	 * @return html_form_password
	 */
	function html_form_password($name, $required=false, $value='', $class='', $readonly=false, $size='', $maxlength='') {
		$this->name = $name;
		$this->required = $required;
		$this->value = $value;
		$this->class = $class;
		$this->readonly = $readonly;
		$this->maxlength = $maxlength;
		$this->size = $size;
	}

	/**
	 * Return the HTML code of this password field
	 *
	 * @param string $extra_fields Any extra attributes to output
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '<input type="password" name="'.$this->name.'" id="'.$this->name.'" ';
		$html .= 'value="'.html_text::escape($this->value).'" ';
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if (!empty($this->size)) {
			$html .= 'size="'.$this->size.'" ';
		}
		if (!empty($this->maxlength)) {
			$html .= 'maxlength="'.$this->maxlength.'" ';
		}
		if ($this->readonly) {
			$html .= 'readonly ';
		}
		$html .= $extra_fields.'/>';
		$html .= $this->show_error();
		return $html;
	}
}

/**
 * Text area
 *
 * @package html
 */
class html_form_textarea extends html_form_element {
	var $cols;
	var $rows;

	/**
	 * Create a text area.
	 *
	 * @param string $name Name of text area
	 * @param boolean $required Whether this field is required
	 * @param string $value Pre-filled value of text area if any
	 * @param string $class Name of CSS class
	 * @param int $cols Number of columns
	 * @param int $rows Number of rows
	 * @param boolean $readonly Whether this text area is read-only
	 * @return html_form_textarea
	 */
	function html_form_textarea($name, $required=false, $value='', $class='', $cols=57, $rows=4, $readonly=false) {
		$this->name = $name;
		$this->required = $required;
		$this->value = $value;
		$this->class = $class;
		$this->cols = $cols;
		$this->rows = $rows;
		$this->readonly = $readonly;
	}

	/**
	 * Return the HTML codes for text area
	 *
	 * @param string $extra_fields Any extra attributes to output
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '<textarea name="'.$this->name.'" id="'.$this->name.'" ';
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if ($this->readonly) {
			$html .= 'readonly ';
		}
		$html .= 'cols="'.$this->cols.'" ';
		$html .= 'rows="'.$this->rows.'" ';
		$html .= $extra_fields.'>';
		$html .= $this->value;
		$html .= '</textarea>';
		$html .= $this->show_error();
		return $html;
	}
}

/**
 * File field
 *
 * @package html
 */
class html_form_file extends html_form_element {
	var $maxlength = '';
	var $existing = false;
	var $delete = false;

	/**
	 * Create a file field.
	 *
	 * @param string $name Name of file field
	 * @param string $required Whether this field is required
	 * @param mixed $existing Whether there is an existing file. (boolean or string)
	 * @param string $class Name of file field
	 * @param boolean $readonly Whether this field is read-only
	 * @return html_form_file
	 */
	function html_form_file($name, $required=false, $existing=false, $class='', $readonly=false) {
		$this->name = $name;
		$this->required = $required;
		$this->existing = !empty($existing);
		$this->class = $class;
		$this->readonly = $readonly;
	}

	/**
	 * Return the HTML codes for file field.
	 *
	 * @param string $extra_fields Any extra attributes to output
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '';
		if ($this->existing) {
			$html = 'Replace: ';
		}

		$html .= '<input type="file" name="'.$this->name.'" id="'.$this->name.'" ';
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if ($this->readonly) {
			$html .= 'readonly ';
		}
		$html .= $extra_fields.'/>';
		$html .= $this->show_error();

		if ($this->existing && !$this->required) {
			$html .= '<br/>Delete? <input type="checkbox" name="'.$this->name.'_delete" value="true" />';
		}

		return $html;
	}
}

/**
 * Button
 *
 * @package html
 */
class html_form_button extends html_form_element {
	var $type;
	var $show_name;
	var $clicked = false;

	/**
	 * Creat a button.
	 *
	 * @param string $name Name of button
	 * @param string $value Button Label
	 * @param string $class Name of CSS class for this button
	 * @param string $type Button type: submit or button
	 * @param boolean $show_name Whether to output the "name" tag
	 * @param string $onclick OnClick value
	 * @return html_form_button
	 */
	function html_form_button($name, $value='', $class='', $type='submit', $show_name=false, $onclick='') {
		$this->name = $name;
		$this->value = $value;
		$this->type = $type;
		$this->show_name = $show_name;
		$this->class = $class;
		$this->onclick = $onclick;
	}

	/**
	 * Return the HTML code for the button.
	 *
	 * @param string $extra_fields Any extra attributes to output
	 * @return string
	 */
	/** phased out button setting
	 * function output($extra_fields = '') {
	 * 	$html = '<input type="'.$this->type.'" ';
	 * 	$html .= $this->show_name ? 'name="'.$this->name.'" ' : '';
	 * 	$html .= 'id="'.$this->name.'" ';
	 * 	$html .= 'value="'.html_text::escape($this->value).'" ';
	 * 	if (!empty($this->class)) {
	 * 		$html .= 'class="'.$this->class.'" ';
	 * 	}
	 * 	if (!empty($this->onclick)) {
	 * 		$html .= 'onclick="'.$this->onclick.'" ';
	 * 	}
	 * 	$html .= $extra_fields.'/>';
	 * 	return $html;
	}
	**/
	
	function output($extra_fields = '') {
		$html = '<button type="'.$this->type.'" ';
		$html .= $this->show_name ? 'name="'.$this->name.'" ' : '';
		$html .= 'id="'.$this->name.'" ';
		$html .= 'value="'.html_text::escape($this->value).'" ';
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if (!empty($this->onclick)) {
			$html .= 'onclick="'.$this->onclick.'" ';
		}
		$html .= $extra_fields.'>'.html_text::escape($this->value).'</button>';
		return $html;
	}
}

/**
 * Image button
 *
 * @package html
 */
class html_form_image_button extends html_form_element {
	var $path;
	var $show_name;
	var $clicked = false;

	/**
	 * Creat an image button
	 *
	 * @param string $name Name of image button
	 * @param string $path Image path
	 * @param string $value Value of image button
	 * @param string $class Name of CSS class
	 * @param boolean $show_name Whether to output the "name" tag
	 * @param string $onclick OnClick value
	 * @return html_form_image_button
	 */
	function html_form_image_button ($name, $path, $value='', $class='', $show_name=false, $onclick='') {
		$this->name = $name;
		$this->path = $path;
		$this->value = $value;
		$this->show_name = $show_name;
		$this->class = $class;
		$this->onclick = $onclick;
	}

	/**
	 * Return the HTML codes for image button
	 *
	 * @param string $extra_fields Any extra attributes to output
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '<input type="image" src="'.$this->path.'" ';
		$html .= $this->show_name ? 'name="'.$this->name.'" ' : '';
		$html .= 'id="'.$this->name.'" ';
		$html .= 'value="'.$this->value.'" ';
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if (!empty($this->onclick)) {
			$html .= 'onclick="'.$this->onclick.'" ';
		}
		$html .= $extra_fields.'/>';
		return $html;
	}
}

/**
 * List box
 *
 * @package html
 */
class html_form_select extends html_form_element {
	var $options;  // array( [value] => [label], ...)
	var $comment;
	var $onchange;

	/**
	 * Create a list box
	 *
	 * @param string $name Name of list box
	 * @param array $options Available options in list box in an associative array. Array('value'=>'label').
	 * @param string $comment Comment with the value -1. E.g. "--Select an option--"
	 * @param boolean $required Whether this field is required
	 * @param boolean $multiple Whether this is a multiple list box
	 * @param string $class Name of CSS class
	 * @param mixed $selected The pre-selected value as a string, or values as an array
	 * @param string $onchange OnChange value
	 * @param boolean $readonly Whether this field is read-only
	 * @return html_form_select
	 */
	function html_form_select ($name, $options, $comment='', $required=false, $multiple=false, $class='', $selected=NULL, $onchange='', $readonly=false) {
		$this->name = $name;
		$this->options = $options;
		if ($selected == NULL) {
			$this->value = $multiple ? array() : '';
		} else {
			$this->value = $selected;
		}
		$this->comment = $comment;
		$this->class = $class;
		$this->multiple = $multiple;
		$this->required = $required;
		$this->onchange = $onchange;
		$this->readonly = $readonly;
	}

	/**
	 * Return the HTML codes of list box
	 *
	 * @param string $extra_fields Any extra attributes of list box
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '<select name="'.$this->name.($this->multiple?'[]':'').'" id="'.$this->name.'" ';
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if ($this->multiple) {
			$html .= 'multiple ';
		}
		if ($this->onchange != '') {
			$html .= 'onchange="'.$this->onchange.'" ';
		}
		if ($this->readonly) {
			$html .= 'readonly ';
		}
		$html .= $extra_fields.'>';
		if (!empty($this->comment)) {
			$html .= '<option value="">'.$this->comment.'</option>';
		}
		foreach ($this->options as $value=>$label) {
			$html .= '<option value="'.html_text::escape($value).'" ';
			if ($this->multiple) {
				foreach ($this->value as $selected) {
					if ($selected == $value) {
						$html .= 'selected ';
					}
				}
			} else {
				if ($this->value == $value) {
					$html .= 'selected ';
				}
			}
			$html .= '>'.$label.'</option>';
		}
		$html .= '</select>';
		$html .= $this->show_error();
		return $html;
	}
}

/**
 * Radio button
 *
 * @package html
 */
class html_form_radio extends html_form_element {
	var $checked;
	
	/**
	 * Creat a radio buttons
	 *
	 * @param string $name Name of radio button
	 * @param string $value Value of radio button
	 * @param string $class Name of CSS class of radio button
	 * @param boolean $checked Whether radio button is checked
	 * @param string $onclick OnClick value
	 * @param boolean $readonly Whether radio button is read-only
	 * @return html_form_radio
	 */
	function html_form_radio ($name, $value, $class='', $checked=false, $onclick='', $readonly=false) {
		$this->name = $name;
		$this->value = $value;
		$this->class = $class;
		$this->checked = $checked;
		$this->onclick = $onclick;
		$this->readonly = $readonly;
	}

	/**
	 * Return the HTML codes for this radio button
	 *
	 * @param string $extra_fields Any extra attributes of radio button
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '<input type="radio" name="'.$this->name.'" id="'.$this->name.'_'.$this->value.'" ';
		$html .= 'value="'.$this->value.'" ';
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if (is_bool($this->checked) && $this->checked || $this->checked === $this->value) {
			$html .= 'checked ';
		}
		if (!empty($this->onclick)) {
			$html .= 'onclick="'.$this->onclick.'" ';
		}
		if ($this->readonly) {
			$html .= 'readonly ';
		}
		$html .= $extra_fields.'/>';
		return $html;
	}
}

/**
 * Check box
 *
 * @package html
 */
class html_form_checkbox extends html_form_element {
	var $checked;

	/**
	 * Creat a check box.
	 *
	 * @param string $name Name of check box
	 * @param string $value Value of check box
	 * @param string $class Name of CSS class for check box
	 * @param boolean $checked Whether check box is checked
	 * @param string $onclick OnClick value
	 * @param boolean $readonly Whether check box is read-only
	 * @return html_form_checkbox
	 */
	function html_form_checkbox ($name, $value, $class='', $checked=false, $onclick='', $readonly=false) {
		$this->name = $name;
		$this->value = $value;
		$this->class = $class;
		$this->checked = $checked;
		$this->onclick = $onclick;
		$this->readonly = $readonly;
	}

	/**
	 * Return the HTML codes for this checkbox
	 *
	 * @param string $extra_fields Any extra fields to output
	 * @return string
	 */
	function output($extra_fields = '') {
		$html = '<input type="checkbox" name="'.$this->name.'[]" id="'.$this->name.'_'.$this->value.'" ';
		$html .= 'value="'.$this->value.'" ';
		if (!empty($this->class)) {
			$html .= 'class="'.$this->class.'" ';
		}
		if (is_bool($this->checked) && $this->checked) {
			$html .= 'checked ';
		} elseif (is_array($this->checked) && array_search($this->value, $this->checked) !== false) {
			$html .= 'checked ';
		}
		if (!empty($this->onclick)) {
			$html .= 'onclick="'.$this->onclick.'" ';
		}
		if ($this->readonly) {
			$html .= 'readonly ';
		}
		$html .= $extra_fields.'/>';
		return $html;
	}
}

?>