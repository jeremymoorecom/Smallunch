<?php
class Smallunch_lib_View_Helper_StateBox extends Zend_View_Helper_FormSelect
{
  /**
   * Generate dropdown box for US States
   * 
   * @param string $name Name for form element
   * @param string $value String for selected element
   * @param $attribs  Attributes added to the 'select' tag.
   * @param array $options Array for select options
   */
  function stateBox($name, $value = null, $attribs = null, $options = array())
  {
    $states = array ('AK'=>'Alaska',
											'AL'=>'Alabama',
											'AR'=>'Arkansas',
											'AZ'=>'Arizona',
											'CA'=>'California',
											'CO'=>'Colorado',
											'CT'=>'Connecticut',
											'DC'=>'Washington D.C.',
											'DE'=>'Delaware',
											'FL'=>'Florida',
											'GA'=>'Georgia',
											'HI'=>'Hawaii',
											'IA'=>'Iowa',
											'ID'=>'Idaho',
											'IL'=>'Illinois',
											'IN'=>'Indiana',
											'KS'=>'Kansas',
											'KY'=>'Kentucky',
											'LA'=>'Louisiana',
											'MA'=>'Massachusetts',
											'MD'=>'Maryland',
											'ME'=>'Maine',
											'MI'=>'Michigan',
											'MN'=>'Minnesota',
											'MO'=>'Missourri',
											'MS'=>'Mississippi',
											'MT'=>'Montana',
											'NC'=>'North Carolina',
											'ND'=>'North Dakota',
											'NE'=>'Nebraska',
											'NH'=>'New Hampshire',
											'NJ'=>'New Jersey',
											'NM'=>'New Mexico',
											'NV'=>'Nevada',
											'NY'=>'New York',
											'OH'=>'Ohio',
											'OK'=>'Oklahoma',
											'OR'=>'Oregon',
											'PA'=>'Pennsylvania',
											'PR'=>'Puerto Rico',
											'RI'=>'Rhode Island',
											'SC'=>'South Carolina',
											'SD'=>'South Dakota',
											'TN'=>'Tennessee',
											'TX'=>'Texas',
											'UT'=>'Utah',
											'VA'=>'Virginia',
											'VT'=>'Vermont',
											'WA'=>'Washington',
											'WI'=>'Wisconsin',
											'WV'=>'West Virginia',
											'WY'=>'Wyoming');
    
    return $this->formSelect($name, $value, $attribs, array_merge($options, $states));
  }
}