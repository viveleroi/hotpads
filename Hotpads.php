<?php
/**
 * @package  Hotpads
 * @author   Michael Botsko, Trellis Development, LLC
 * @version  Git-Revision
 *
 * This PHP object allows you to generate the required xml documents for
 * a listing feed for the Hotpads.com web service. You primarily must
 * fill in the data accepted by the HotpadProperty class, and pass an array
 * of all property objects to the Hotpads object.
 *
 * You should cache the resulting xml file and provide this cached version
 * as the download option to Hotpads.
 */

require('Xml.php');

/**
 * @package Hotpads
 * @subpackage HotpadProperty
 */
class HotpadProperty {
	public $id;
	public $type;
	public $property_type;
	public $name;
	public $unit_no;
	public $address_1;
	public $street;
	public $city;
	public $state;
	public $zip;
	public $country= 'US'; // http://www.iso.org/iso/iso3166_en_code_lists.txt
	public $contact_name;
	public $contact_email;
	public $contact_phone;
	public $contact_fax;
	public $preview_message;
	public $description;
	public $terms;
	public $website_url;
	public $vr_url;
	public $photos = array();
	public $price;
	public $price_freq;
	public $hoa_maint;
	public $bedrooms;
	public $full_bath;
	public $half_bath;
	public $sqft;
}

/**
 * @package Hotpads
 */
class Hotpads {

	/**
	 * @var arrays Holds an array of all HotpadProperty objects
	 * @access private
	 */
	protected $properties = array();


	/**
	 * Adds a new property object to the properties array
	 * @param object $prop_obj
	 * @access public
	 */
	public function addProperty($prop_obj){
		$this->properties[] = $prop_obj;
	}


	/**
	 * Validates that all required property values have been provided.
	 * @param object $prop
	 * @return boolean
	 * @access private
	 * This is implented assuming all fields of prop object are required
	 */
	protected function validate($prop){
		
//		$errors = array();
//
//		foreach($prop as $var => $p){
//			if(is_array($p)){
//				if(empty($p)){
//					$errors[$var] = 'Field may not contain an empty array.';
//				}
//			} else {
//				if(is_null($p)){
//					$errors[$var] = 'Field must contain a value.';
//				}
//			}
//		}
//
//		return (empty($errors) ? true : $errors);

		return true;

	}


	/**
	 * Builds the complete xml document.
	 * @return string
	 * @access private
	 */
	protected function buildXML(){

		$hp = new Xml('1.0', 'utf-8');
		$hp->formatOutput = true;

		// begin hp items
		$hpitems = $hp->createElement('hotPadsItems');
		$hpitems->setAttribute("version", '2.1');

		$comm = $hp->createComment('Generated at ' . gmdate(DATE_RFC822));
		$hpitems->appendChild($comm);

		if(is_array($this->properties)){
			foreach($this->properties as $prop){

				$noerrors = $this->validate($prop);

				if($noerrors === true){

					// Build listing
					$listing = $hp->createElement('Listing');

						// Listing attributes
						$listing->setAttribute("id", $prop->id);
						$listing->setAttribute("type", $prop->type);
						$listing->setAttribute("propertyType", $prop->property_type);

						// Property details
						$listing->appendChild( $this->hpTextNode('name', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('unit', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('street', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('city', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('state', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('zip', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('country', false, $hp, $prop) );

						// Contact details
						$listing->appendChild( $this->hpTextNode('contactName', 'contact_name', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('contactEmail', 'contact_email', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('contactPhone', 'contact_phone', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('contactFax', 'contact_fax', $hp, $prop) );

						// Descriptions and urls
						$listing->appendChild( $this->hpTextNode('previewMessage', 'preview_message', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('description', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('terms', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('website', 'website_url', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('virtualTourUrl', 'vr_url', $hp, $prop) );

						// Photos
						if($prop->photos){
							foreach($prop->photos as $img){
								$photo = $hp->createElement('ListingPhoto');
								$photo->setAttribute("source", $img['source']);
								$listing->appendChild( $photo );
							}
						}

						// Additional data
						$listing->appendChild( $this->hpTextNode('price', false, $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('pricingFrequency', 'price_freq', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('HOA-Fee', 'hoa_maint', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('numBedrooms', 'bedrooms', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('numFullBaths', 'full_bath', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('numHalfBaths', 'half_bath', $hp, $prop) );
						$listing->appendChild( $this->hpTextNode('squareFeet', 'sqft', $hp, $prop) );

					$hpitems->appendChild($listing);

				}
			}

			$hp->appendChild($hpitems);

			return $hp->saveXML();

		}
	}


	/**
	 * Inserts a new text node using the property object field provided.
	 * @param string $element
	 * @param string $var
	 * @param object $hp
	 * @param object $prop
	 * @return object
	 * @access private
	 */
	protected function hpTextNode($element = 'node', $var = false, &$hp, $prop){
		$var = $var ? $var : $element;
		$node = $hp->createElement($element);
		$node->appendChild( $hp->createTextNode( $hp->encode_for_xml( $prop->{$var} ) ) );
		return $node;
	}


	/**
	 * Returns the completed xml document.
	 * @return string
	 * @access public
	 */
	public function xml(){
		return $this->buildXML();
	}
}
?>